<?php

namespace Controllers;

use Library\Localization;
use Library\Params;
use Library\Sanitize;
use Library\User;
use \Library\View,
	\Library\Composer,
	\Objects\Record;
use Models\CreativeModel;
use Models\MediaTypeModel;
use Models\ReviewModel;
use Objects\Ad;
use Objects\Campaign;

class AdController
{
	
	/**
	 * Print the form needed to interact with the ads
	 * @param string  $formName Name of the form
	 * @param integer $adID     ID of the related ad
	 */
	public function form ($formName, $adID)
	{
		$form = null;
		
		switch ($formName) {
			case "delete":
				
				//Get the view
				$form = new View("modals/deleteAd");
				
				//Retrieve needed infos
				$ad = Ad::getInstance($adID);
				
				//Attach infos to the view
				$form->adID = $ad->getID();
				$form->adName = $ad->getName();
			
			break;
		}
		
		if ($form == null)
			return;
		
		echo $form->render();
	}
	
	
	/**
	 * Create a new ad
	 * @param integer $campaignID ID of the campaign the new ad will belong to
	 */
	public function create ($campaignID)
	{
		$_record = Record::createRecord(Record::AD_ADDED);
		
		$campaignID = Sanitize::int($campaignID);
		
		//Authorizations
		User::canEditCampaign($campaignID);
		
		//Retrieve infos on the campaign
		$campaign = Campaign::getInstance($campaignID);
		
		//Nbr of ads in the campaigns
		$nbrAds = $campaign->getNbrAds();
		
		//Did we already reach the ad limit of the campaign
		if ($nbrAds >= $campaign->getAdLimit()) {
			$_record->setResult(Record::REFUSED)
				->save();
			
			echo json_encode(["html" => "", "hideAddBtn" => true]);
			return;
		}
		
		//Set the default broadcast date
		//Make it start at midnight
		$startTime = (new \DateTime('yesterday midnight'))->add(new \DateInterval('P1D'));
		
		if ($startTime->getTimestamp() < $campaign->getStartDate()) {
			$startTime->setTimestamp($campaign->getStartDate());
		}
		
		//Create the formated string
		$defaultDuration = Params::get("DEFAULT_AD_DURATION");
		$days = $defaultDuration["weeks"] * 7 + $defaultDuration["days"];
		$addString = "P{$defaultDuration["years"]}Y{$defaultDuration["months"]}M{$days}DT{$defaultDuration["hours"]}H{$defaultDuration["minutes"]}M{$defaultDuration["seconds"]}S";
		
		$endTime = clone $startTime;
		$endTime->add(new \DateInterval($addString));
		
		if ($endTime > $campaign->getEndDate()) {
			$endTime->setTimestamp($campaign->getEndDate());
		}
		
		//Create the ad
		$ad = Ad::create($campaignID, $campaign->getSupportID(), $startTime->getTimestamp(), $endTime->getTimestamp());
		
		$nbrAds++;
		
		//Create the needed directories
		//Ad directory
		$adUID = $ad->getUID();
		$campaignUID = $campaign->getCampaignUID();
		mkdir("campaigns/$campaignUID/$adUID/");
		
		//Screen.s directory.ies
		$screens = $campaign->getScreens();
		
		//Create a directory for each screen of the ad
		foreach ($screens as $screen) {
			mkdir("campaigns/$campaignUID/$adUID/{$screen->getID()}/");
		}
		
		//Build and send
		$adBlock = $this->buildBlock($ad->getID());
		
		//Shall we hide the "new Ad" button ?
		$hideBtn = false;
		if ($campaign->getAdLimit() == $nbrAds)
			$hideBtn = true;
		
		
		$_record->setResult(Record::OK)
			->setRef1($ad->getID())
			->save();
		
		//Send the new Ad
		echo json_encode(["html" => $adBlock->render(), "hideAddBtn" => $hideBtn]);
	}
	
	
	/**
	 * Create the ad block to be displayed
	 * @param  integer $adID The ID of the ad
	 * @return View    The View object of the builded ad
	 */
	public function buildBlock ($adID)
	{
		//Get the view
		$block = new View("ads/block");
		
		//Retrieve an ad object
		$ad = Ad::getInstance($adID);
		
		if ($ad == false)
			return;
		
		//Retrieve the campaign
		$campaign = $ad->getCampaign();
		
		//Attach infos to the block View
		$block->adID = $ad->getID();
		$block->startDate = $ad->getStartTime();
		$block->endDate = $ad->getEndTime();
		$block->campaignStartDate = $campaign->getStartDate();
		$block->campaignEndDate = $campaign->getEndDate();
		$block->adName = \__("ad");
		
		
		//Retrieve the ad Status
		$reviewModel = new ReviewModel();
		$block->review = $reviewModel->get($adID);
		
		//Can we display the review links ?
		$canReview = false;
		if (User::restricted("APPROVE_CREATIVES", true))
			$canReview = true;
		
		$block->canReview = $canReview;
		
		//Retrieve infos on the screens
		
		$screensView = new Composer();
		
		$creativeModel = new CreativeModel();
		$mediaTypeModel = new MediaTypeModel();
		
		//Retrieve all screens
		$screens = $ad->getScreens();
		
		//Does this support as multiple screens ?
		$multipleScreens = count($screens) > 1 ? true : false;
		
		//Create the view for each screen
		foreach ($screens as $screen) {
			//Get the screen creative (will return false if there is none)
			$creative = $creativeModel->getCreative($adID, $screen->getID());
			
			$view = new View("ads/blockScreen");
			$view->hasCreative = false;
			
			//Attach infos to the screen block
			if ($creative != false) {
				$view->hasCreative = true;
				$view->creativeMediaType = $creative->getMediaType();
				$view->creativePath = $creative->getThumbPath(true, true);
			}
			
			//Basic informations
			$view->multipleScreens = $multipleScreens;
			$view->adID = $adID;
			$view->screenID = $screen->getID();
			$view->screenName = $screen->getName();
			
			//Dimensions of the screen
			$view->screenWidth = $screen->getWidth();
			$view->screenHeight = $screen->getHeight();
			
			//Media type name if needed
			$screenMediaType = $screen->getMediaType($campaign->getID());
			if ($screenMediaType == 0) {
				$view->mediaTypeNameinfos = "";
			} else {
				$mediaTypeModel->setMediaType($screenMediaType);
				$view->mediaTypeName = $mediaTypeModel->name();
			}
			
			//Set contect for the block
			$view->reason = "campaign";
			
			$screensView->attach($view);
		}
		
		$block->details = "";
		
		if (User::isAdmin())
			$block->details = $this->adDetailsBlock($ad);
		
		//Attach all the screens block and return the ad block
		$block->screens = $screensView->render();
		return $block;
	}
	
	
	/**
	 * @param $ad Ad
	 * @return string
	 */
	private function adDetailsBlock ($ad)
	{
		$block = new View("ads/blockDetails");
		
		$creatives = $ad->getCreatives();
		$creativesStats = new Composer();
		
		//For each creat
		foreach ($creatives as $creative) {
			$creativeView = new View("ads/creativeStatsLine");
			
			$screen = $creative->getScreen();
			$uploader = $creative->getUploader();
			
			$creativeView->screenName = $screen->getName();
			$creativeView->creativeStatus = "creativeStatus-" . $creative->getStatus();
			$creativeView->conversionStatus = $creative->getConversionStatus();
			$creativeView->creativeUploadTime = date(Localization::dateFormat(), $creative->getUploadTime());
			$creativeView->creativeUploader = $uploader->getName();
			$creativeView->creativeSize = $creative->getSize();
			
			$creativesStats->attach($creativeView);
		}
		
		$block->creativesStates = $creativesStats->render();
		
		$adStats = $ad->getStats();
		$block->adPrintsTotal = $adStats->getPrintsTotal();
		
		return $block->render();
	}
	
	
	/**
	 * Permanently delete an ad and its creatives
	 * @param int  $adID -
	 * @param bool $silent
	 */
	public function delete ($adID, $silent = false)
	{
		$ad = Ad::getInstance($adID);
		
		if (!$ad)
			return; //Bad ID
		
		//Authorizations
		if (!User::canEditAd($ad->getID())) {
			$_record = Record::createRecord(Record::AD_REMOVED);
			$_record->setRef1($ad->getID())
				->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		$ad->delete();
		
		if ($silent)
			return;
		
		//Display the campaign
		$campaignController = new CampaignController();
		$campaignController->display($ad->getCampaignID());
	}
	
	
	/**
	 * Update the ad
	 * @param string  $field The information that need to be updated
	 * @param integer $adID  The Ad that gets updated
	 */
	public function update ($field, $adID)
	{
		$ad = Ad::getInstance($adID);
		
		$_record = Record::createRecord(Record::AD_UPDATED);
		$_record->setRef1($ad->getID());
		
		//Authorizations
		User::canEditAd($ad->getID());
		
		//Retirve informations on the ad and its campaign
		
		$campaign = $ad->getCampaign();
		
		//Update the correct informations
		switch ($field) {
			case "startdate":
			case "enddate":
				
				//Set the new start and end dates of the ad
				switch ($field) {
					case "startdate":
						$startDate = Sanitize::int($_POST['startDate']);
						$endDate = $ad->getEndTime();
					break;
					case "enddate":
						$startDate = $ad->getStartTime();
						$endDate = Sanitize::int($_POST['endDate']);
					break;
					default:
						return;
				}
				
				
				//Prevent impossible date range
				if ($startDate < $campaign->getStartDate())
					$startDate = $campaign->getStartDate();
				
				if ($startDate > $campaign->getEndDate())
					$startDate = $campaign->getEndDate();
				
				if ($startDate > $endDate)
					$endDate = $startDate;
				
				//Save the new dates
				$ad->setStartTime($startDate)
					->setEndTime($endDate)
					->save();
			
			break;
		}
		
		$_record->setResult(Record::OK)
			->save();
	}
}
