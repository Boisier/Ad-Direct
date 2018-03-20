<?php

namespace Controllers;

use \Library\View,
	\Library\Composer;
use Objects\Ad;
use Objects\Record,
	Objects\Campaign;

class CampaignController
{
	public function form ($formName, $ID = 0, $ID2 = 0)
	{
		switch ($formName) {
			case "add":
				
				\Library\User::restricted("MANAGE_CLIENTS");
				
				$form = new View("campaigns/addCampaign");
				
				$broadcaster = \Objects\Broadcaster::getInstance($ID);
				
				$supportModel = new \Models\SupportModel();
				
				$form->broadcasterID = $broadcaster->getID();
				$form->broadcasterName = $broadcaster->getName();
				$form->supports = $supportModel->supportList();
				$form->defaultDurations = \Library\Params::get("DEFAULT_CAMPAIGN_DURATION");
			
			break;
			case "edit":
				
				\Library\User::restricted("MANAGE_CLIENTS");
				
				$form = new View("campaigns/editCampaign");
				
				$campaign = Campaign::getInstance($ID);
				
				$supportModel = new \Models\SupportModel();
				
				$form->campaignID = $campaign->getID();
				$form->campaignName = $campaign->getName();
				$form->campaignStartDate = $campaign->getStartDate();
				$form->campaignEndDate = $campaign->getEndDate();
				$form->campaignCreateDate = $campaign->getCreateDate();
				$form->supportID = $campaign->getSupportID();
				$form->adLimit = $campaign->getAdLimit();
				$form->displayDuration = $campaign->getDisplayDuration();
				
				$form->supports = $supportModel->supportList();
			
			break;
			case "editformats":
				
				\Library\User::restricted("MANAGE_CLIENTS");
				
				$form = new View("campaigns/editFormats");
				
				$campaign = Campaign::getInstance($ID);
				
				$mediaTypeModel = new \Models\MediaTypeModel();
				
				$form->campaignID = $campaign->getID();
				$form->campaignName = $campaign->getName();
				$form->screens = $campaign->getScreens();
				
				$form->mediaTypes = $mediaTypeModel->getAll();
			
			break;
			case "delete":
				
				\Library\User::restricted("MANAGE_CLIENTS");
				
				$form = new View("campaigns/deleteCampaign");
				
				$campaign = Campaign::getInstance($ID);
				
				$form->campaignID = $campaign->getID();
				$form->campaignName = $campaign->getName();
			
			break;
		}
		
		echo $form->render();
	}
	
	
	/**
	 * @param int $campaignID
	 */
	public function display ($campaignID)
	{
		//Authorizations
		\Library\User::canEditCampaign($campaignID);
		
		$campaign = \Objects\Campaign::getInstance($campaignID);
		
		if (!$campaign)
			return;
		
		$broadcaster = $campaign->getBroadcaster();
		$support = $campaign->getSupport();
		
		$page = new Composer();
		
		//Headers
		if (\Library\User::isAdmin())
			$header = new View("campaigns/headerAdmin");
		else
			$header = new View("campaigns/headerClient");
		
		$user = \Objects\User::getInstance(12);
		
		$header->campaignID = $campaign->getID();
		$header->broadcasterID = $campaign->getBroadcasterID();
		$header->broadcasterName = $broadcaster->getName();
		$header->campaignName = $campaign->getName();
		$header->displayFileURL = $campaign->getDisplayFileURL();
		$page->attach($header);
		
		$commonHeader = new View("campaigns/headerCommon");
		
		$commonHeader->campaignStartDate = $campaign->getStartDate();
		$commonHeader->campaignEndDate = $campaign->getEndDate();
		$commonHeader->supportName = $support->getName();
		$commonHeader->supportLimit = $campaign->getAdLimit();
		
		$page->attach($commonHeader);
		
		//The ads
		$ads = $campaign->getAds();
		
		$adController = new AdController();
		
		foreach ($ads as $ad) {
			$page->attach($adController->buildBlock($ad->getID()));
		}
		
		//Footer
		$footer = new View("campaigns/footer");
		$footer->campaignID = $campaign->getID();
		$footer->display = count($ads) < $campaign->getAdLimit() ? true : false;
		
		$page->attach($footer);
		
		echo $page->render();
	}
	
	
	public function create ()
	{
		$_record = Record::createRecord(Record::CAMPAIGN_CREATED);
		
		//Authorizations
		if (!\Library\User::hasPrivilege("MANAGE_CLIENTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		//Exclude any possible errors
		if (empty($_POST['broadcasterID']) || empty($_POST['campaignSupport'])) {
			$_record->setResult(Record::FATAL_ERROR)
				->setMessage("Missing POST var")
				->save();
			
			http_response_code(400);
			echo "fatalError";
			return;
		}
		
		if (empty($_POST['campaignName']) || empty($_POST['campaignStartDate']) || empty($_POST['campaignEndDate']) || empty($_POST['adLimit']) || empty($_POST['displayDuration'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing fields")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		//Retrieve all the informations and sanitize them
		$broadcasterID = \Library\Sanitize::int($_POST['broadcasterID']);
		$supportID = \Library\Sanitize::int($_POST['campaignSupport']);
		$campaignName = \Library\Sanitize::string($_POST['campaignName']);
		
		$dateFormat = \Library\Localization::dateFormat();
		
		//Parse dates
		$campaignStartDate = \DateTime::createFromFormat($dateFormat, $_POST['campaignStartDate']);
		$campaignEndDate = \DateTime::createFromFormat($dateFormat, $_POST['campaignEndDate']);
		
		//Verify dates formats
		if (!$campaignStartDate || !$campaignEndDate) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Incoherent dates")
				->save();
			
			http_response_code(400);
			echo "badDates";
			return;
		}
		
		//Get timestamps
		$campaignStartDate = $campaignStartDate->getTimestamp();
		$campaignEndDate = $campaignEndDate->getTimestamp();
		
		$adLimit = \Library\Sanitize::int($_POST['adLimit']);
		$displayDuration = \Library\Sanitize::int($_POST['displayDuration']);
		
		if ($campaignEndDate < $campaignStartDate) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Incoherent dates")
				->save();
			
			http_response_code(400);
			echo "badDates";
			return;
		}
		
		//Create the campaign
		$campaign = Campaign::create($broadcasterID,
			$supportID,
			$campaignName,
			$campaignStartDate,
			$campaignEndDate,
			$adLimit,
			$displayDuration);
		
		//Create the campaign Directory
		$campaignUID = $campaign->getCampaignUID();
		mkdir("campaigns/$campaignUID", 0777);
		
		$_record->setResult(Record::OK)
			->setRef1($campaign->getID())
			->save();
		
		$emailController = new EmailController();
		$emailController->create(EmailController::EMAIL_SCHEDULE_CAMPAIGN, $campaign->getID());
		
		//Display the formats form
		$this->form("editformats", $campaign->getID()); //Or directly add support
	}
	
	
	public function update ()
	{
		$campaign = Campaign::getInstance($_POST['campaignID']);
		
		if (!$campaign)
			return; //Bad ID
		
		$_record = Record::createRecord(Record::CAMPAIGN_UPDATED);
		$_record->setRef1($campaign->getID());
		
		//Authorizations
		if (!\Library\User::hasPrivilege("MANAGE_CLIENTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		//Exclude any possible error
		if (empty($_POST['campaignID']) || empty($_POST['campaignSupport'])) {
			$_record->setResult(Record::FATAL_ERROR)
				->setMessage("Missing POST var")
				->save();
			
			http_response_code(400);
			echo "fatalError";
			return;
		}
		
		if (empty($_POST['campaignName']) || empty($_POST['campaignStartDate']) || empty($_POST['campaignEndDate']) || empty($_POST['adLimit']) || empty($_POST['displayDuration'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing Fields")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		//Retrieve all the informations and sanitize them
		$supportID = \Library\Sanitize::int($_POST['campaignSupport']);
		$campaignName = \Library\Sanitize::string($_POST['campaignName']);
		
		$dateFormat = \Library\Localization::dateFormat();
		
		//Parse dates
		$campaignStartDate = \DateTime::createFromFormat($dateFormat, $_POST['campaignStartDate']);
		$campaignEndDate = \DateTime::createFromFormat($dateFormat, $_POST['campaignEndDate']);
		
		//Verify dates format
		if (!$campaignStartDate || !$campaignEndDate) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Incoherent dates")
				->save();
			
			http_response_code(400);
			echo "badDates";
			return;
		}
		
		//Get timestamps
		$campaignStartDate = $campaignStartDate->getTimestamp();
		$campaignEndDate = $campaignEndDate->getTimestamp();
		
		$adLimit = \Library\Sanitize::int($_POST['adLimit']);
		$displayDuration = \Library\Sanitize::int($_POST['displayDuration']);
		
		//Handle the dates
		if ($campaignEndDate < $campaignStartDate) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Incoherent dates")
				->save();
			
			http_response_code(400);
			echo "badDates";
			return;
		}
		
		//Confirm we can change the support
		$currentSupportID = $campaign->getSupportID();
		
		if ($currentSupportID != $supportID && $campaign->getNbrAds() != 0) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Cannot update support if there is ads in the campaign")
				->save();
			
			http_response_code(400);
			echo "cannotChangeSupportIfAdsPresents";
			return;
		}
		
		//Update the campaign
		
		$campaign->setSupportID($supportID)
			->setCampaignName($campaignName)
			->setStartDate($campaignStartDate)
			->setEndDate($campaignEndDate)
			->setAdLimit($adLimit)
			->setDisplayDuration($displayDuration)
			->save();
		
		$_record->setResult(Record::OK)
			->save();
		
		$emailController = new EmailController();
		$emailController->create(EmailController::EMAIL_UPDATE_CAMPAIGN_SCHEDULE, $campaign->getID());
		
		//Display the campaign
		$this->display($campaign->getID());
	}
	
	
	public function updateformats ()
	{
		$campaign = \Objects\Campaign::getInstance($_POST['campaignID']);
		
		if (!$campaign)
			return; //Bad ID
		
		$_record = Record::createRecord(Record::CAMPAIGN_FORMATS_UPDATED);
		$_record->setRef1($campaign->getID());
		
		//Authorizations
		if (!\Library\User::hasPrivilege("MANAGE_CLIENTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		if (empty($_POST['campaignID'])) {
			$_record->setResult(Record::FATAL_ERROR)
				->setMessage("Missing POST var")
				->save();
			
			http_response_code(400);
			echo "fatalError";
			return;
		}
		
		$screens = $campaign->getScreens();
		
		//Do we have everything ?
		foreach ($screens as $screen) {
			$screenFormatID = "screenFormat" . $screen->getID();
			$screenSizeID = "screenSize" . $screen->getID();
			
			if (!isset($_POST[$screenFormatID]) || empty($_POST[$screenSizeID])) {
				$_record->setResult(Record::FATAL_ERROR)
					->setMessage("Missing screen info POST vars")
					->save();
				
				http_response_code(400);
				echo "fatalError";
				return;
			}
		}
		
		//Can we update the mediaTypes, or only the sizeLimits ?
		$ignoreMediaTypes = false;
		
		if ($campaign->getNbrAds() != 0)
			$ignoreMediaTypes = true;
		
		$campaignModel = new \Models\CampaignModel($campaign->getID());
		
		foreach ($screens as $screen) {
			$screenFormatID = "screenFormat" . $screen->getID();
			$screenSizeID = "screenSize" . $screen->getID();
			
			$screenFormat = \Library\Sanitize::int($_POST[$screenFormatID]);
			$screenSizeLimit = \Library\Sanitize::int($_POST[$screenSizeID]);
			
			if ($ignoreMediaTypes)
				$screenFormat = $screen->getMediaType();
			
			$campaignModel->updateScreen($screen->getID(), $screenFormat, $screenSizeLimit);
		}
		
		$_record->setResult(Record::OK)
			->save();
		
		$this->display($campaign->getID());
	}
	
	
	public function delete ($campaignID, $silent = false)
	{
		\Library\User::restricted("MANAGE_CLIENTS");
		
		//Delete the campaign and its dependncies
		$campaign = \Objects\Campaign::getInstance($campaignID);
		$campaign->delete();
		
		if ($silent)
			return;
		
		$broadcasterController = new BroadcasterController();
		$broadcasterController->display($campaign->getBroadcasterID(), "CAMPAIGNS");
	}
}
