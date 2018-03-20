<?php

namespace Controllers;

use Library\Sanitize;
use \Library\View,
	\Library\Composer,
	\Objects\Record;
use Objects\Broadcaster;

class BroadcasterController
{
	/**
	 * Print the needed form to interact with the broadcaster
	 * @param string $formName The name of the form
	 * @param integer [$broadcasterID      = 0] The broadcaster ID
	 */
	public function form ($formName, $broadcasterID = 0)
	{
		\Library\User::restricted("MANAGE_CLIENTS");
		
		switch ($formName) {
			case "add":
				
				//Get the view
				$form = new \Library\View("broadcasters/addForm");
				
				$broadcasterGroupModel = new \Models\BroadcasterGroupModel();
				$form->broadcasterGroups = $broadcasterGroupModel->getAll();
			
			break;
			case "edit":
			case "delete":
				
				//Get the view
				$form = new \Library\View("broadcasters/" . $formName . "Form");
				
				//Retrieve needed infos
				$broadcaster = Broadcaster::getInstance($broadcasterID);
				
				//Attach the infos
				$form->broadcasterID = $broadcaster->getID();
				$form->broadcasterName = $broadcaster->getName();
				$form->groupID = $broadcaster->getGroupID();
				
				$broadcasterGroupModel = new \Models\BroadcasterGroupModel();
				$form->broadcasterGroups = $broadcasterGroupModel->getAll();
			
			break;
		}
		
		echo $form->render();
	}
	
	
	/**
	 * Display the broadcaster page
	 * @param integer $broadcasterID The ID of the broadcaster to display
	 * @param string  [$tab                = "CAMPAIGNS"] Wich tab should we land on
	 */
	public function display ($broadcasterID, $tab = "CAMPAIGNS")
	{
		\Library\User::onlyAdmins();
		
		//Retrieve informations on the braodcaster
		$broadcaster = Broadcaster::getInstance($broadcasterID);
		
		if (!$broadcaster)
			return;
		
		//Get the view
		$view = new \Library\View("broadcasters/display");
		
		//Attach broadcaster's infos
		$view->broadcasterID = $broadcaster->getID();
		$view->broadcasterName = $broadcaster->getName();
		
		//Attach the view for the three tabs
		//$view->broadcasterDefaultAds = $this->defaultAdsView($broadcasterID);
		$view->broadcasterCampaigns = $this->campaignsView($broadcaster);
		$view->broadcasterClients = $this->clientsView($broadcaster);
		
		$view->currentTab = strtolower($tab);
		
		echo $view->render();
	}
	
	
	private function defaultAdsView ($broadcasterID)
	{
		$defaultAdController = new DefaultadController();
		return $defaultAdController->home($broadcasterID);
	}
	
	/**
	 * @param  \Objects\Broadcaster $broadcaster
	 * @return string  The rendered tab
	 */
	private function campaignsView ($broadcaster)
	{
		$view = new View("broadcasters/campaigns");
		$view->broadcasterID = $broadcaster->getID();
		
		//Retrieve all campaigns of broadcaster
		$campaigns = $broadcaster->getCampaigns();
		
		//In case there is no campaign to show
		if (count($campaigns) == 0) {
			//Display "no campaigns"
			$noCampaigns = new View("broadcasters/noCampaign");
			$view->campaignList = $noCampaigns->render();
			return $view->render();
		}
		
		//There is campaign, let's display the list
		$list = new Composer();
		
		//For each campaign
		foreach ($campaigns as $campaign) {
			$campaignView = new View("broadcasters/campaignList");
			
			$support = $campaign->getSupport();
			
			$campaignView->campaignID = $campaign->getID();
			$campaignView->campaignName = $campaign->getName();
			$campaignView->startDate = $campaign->getStartDate();
			$campaignView->endDate = $campaign->getEndDate();
			$campaignView->supportName = $support->getName();
			
			$campaignView->pending = $campaign->getPendingNbrAds();
			
			$list->attach($campaignView);
		}
		
		$view->campaignList = $list->render();
		
		return $view->render();
	}
	
	/**
	 * @param  \Objects\Broadcaster $broadcaster
	 * @return string  The rendered tab
	 */
	private function clientsView ($broadcaster)
	{
		$view = new \Library\View("broadcasters/clients");
		$view->broadcasterID = $broadcaster->getID();
		
		$clients = $broadcaster->getClients();
		
		//In case there is no client to show
		if (count($clients) == 0) {
			$noClients = new View("broadcasters/noClient");
			$view->clientList = $noClients->render();
			return $view->render();
		}
		
		$list = new \Library\Composer();
		
		foreach ($clients as $client) {
			$clientView = new \Library\View("broadcasters/clientList");
			
			$clientView->clientID = $client->getID();
			$clientView->clientName = $client->getName();
			$clientView->clientEmail = $client->getEmail();
			$clientView->clientLive = $client->isLive();
			
			$list->attach($clientView);
		}
		
		$view->clientList = $list->render();
		
		return $view->render();
	}
	
	
	/**
	 *
	 */
	public function create ()
	{
		$_record = Record::createRecord(Record::BROADCASTER_CREATED);
		
		if (!\Library\User::hasPrivilege("MANAGE_CLIENTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		
		if (empty($_POST['name']) || !isset($_POST['groupID'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing Fields")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$broadcasterName = \Library\Sanitize::string($_POST['name']);
		$groupID = \Library\Sanitize::int($_POST['groupID']);
		
		$model = new \Models\BroadcasterModel();
		
		if ($model->broadcasterExistName($broadcasterName)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Broadcaster already exist")
				->save();
			
			http_response_code(400);
			echo "alreadyExist";
			return;
		}
		
		$broadcasterID = $model->create($broadcasterName, $groupID);
		
		//mkdir("defaultAds/$broadcasterID/");
		
		$_record->setResult(Record::OK)
			->setRef1($broadcasterID)
			->save();
		
		$this->display($broadcasterID);
	}
	
	
	/**
	 * @param $broadcasterID
	 */
	public function edit ($broadcasterID)
	{
		$broadcaster = Broadcaster::getInstance($broadcasterID);
		
		$_record = Record::createRecord(Record::BROADCASTER_UPDATED);
		$_record->setRef1($broadcaster->getID());
		
		if (!\Library\User::hasPrivilege("MANAGE_CLIENTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		/*Did we received everything ?*/
		if (empty($_POST['name']) || !isset($_POST['groupID'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing fields")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$broadcasterName = \Library\Sanitize::string($_POST['name']);
		$groupID = \Library\Sanitize::int($_POST['groupID']);
		
		$broadcasterModel = new \Models\broadcasterModel();
		
		/*Can we use this name ?*/
		if ($broadcasterName != $broadcaster->getName() && $broadcasterModel->broadcasterExistName($broadcasterName)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Broadcaster new name already exist")
				->save();
			
			http_response_code(400);
			echo "alreadyExist";
			return;
		}
		
		/*Update name*/
		$broadcaster->setName($broadcasterName)
			->setGroupID($groupID)
			->save();
		
		$_record->setResult(Record::OK)
			->save();
		
		$this->display($broadcaster->getID());
	}
	
	
	/**
	 * @param $broadcasterID
	 */
	public function delete ($broadcasterID)
	{
		$broadcaster = \Objects\Broadcaster::getInstance($broadcasterID);
		
		$_record = Record::createRecord(Record::BROADCASTER_REMOVED);
		$_record->setRef1($broadcaster->getID());
		
		if (!\Library\User::hasPrivilege("MANAGE_CLIENTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		$broadcasterModel = new \Models\BroadcasterModel($broadcasterID);
		$clients = $broadcaster->getClients();
		$campaigns = $broadcaster->getCampaigns();
		
		//Default ads
		//TODO: Removed default ads
		
		//Remove users
		foreach ($clients as $client) {
			$client->delete();
		}
		
		//Remove campaigns
		foreach ($campaigns as $campaign) {
			$campaign->delete();
		}
		
		//Remove default ads
		$defaultadModel = new \Models\DefaultadModel($broadcasterID);
		$defaultadModel->deleteAll();
		
		rmdir("defaultAds/$broadcasterID/");
		
		/*remove campaign*/
		$broadcasterModel->delete();
		
		$_record->setResult(Record::OK)
			->save();
		
		$home = new HomeController();
		$home->clients();
	}
}
