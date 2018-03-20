<?php

namespace Controllers;

use \Library\View,
	\Library\Composer,
	\Library\Sanitize;
use Objects\Record;

class defaultadController
{
	/**
	 * Print the needed form to interact with the creative
	 * @param string  $formName The name of the form
	 * @param integer $adID     The Combo ID that
	 * @param integer $screenID identify the creative
	 */
	public function form ($formName, $broadcasterID, $screenID)
	{
		$broadcasterID = \Library\Sanitize::int($broadcasterID);
		$screenID = \Library\Sanitize::int($screenID);
		
		switch ($formName) {
			case "delete":
				
				$form = new View("defaultAds/deleteDefaultAd");
				
				$form->broadcasterID = $broadcasterID;
				$form->screenID = $screenID;
			
			break;
		}
		
		echo $form->render();
	}
	
	
	public function home ($broadcasterID)
	{
		$broadcasterID = Sanitize::int($broadcasterID);
		
		$view = new \Library\View("broadcasters/defaultAds");
		
		$supportModel = new \Models\SupportModel();
		$supports = $supportModel->supportList();
		
		$list = new Composer();
		
		foreach ($supports as $support) {
			$supportView = new View("supports/supportList");
			$supportView->supportName = $support['name'];
			$supportView->supportID = $support['ID'];
			$supportView->screenNbr = $support['screens'];
			$supportView->broadcasterID = $broadcasterID;
			
			$supportView->reason = "defaultAds";
			
			$list->attach($supportView);
		}
		
		$view->supportList = $list->render();
		
		return $view->render();
	}
	
	
	public function display ($broadcasterID, $supportID)
	{
		$broadcaster = \Objects\Broadcaster::getInstance($broadcasterID);
		
		//Authorisation
		\Library\User::canEditDefaultAds($broadcaster->getID());
		
		$page = new Composer();
		
		$support = \Objects\Support::getInstance($supportID);
		
		//The header
		if (\Library\User::isAdmin())
			$header = new View("defaultAds/headerAdmin");
		else
			$header = new View("defaultAds/headerClient");
		
		$header->broadcasterID = $broadcaster->getID();
		$header->broadcasterName = $broadcaster->getName();
		$header->supportName = $support->getName();
		
		$page->attach($header);
		
		//The block
		$block = $this->buildBlock($broadcaster->getID(), $support->getID());
		
		$page->attach($block);
		
		echo $page->render();
	}
	
	
	public function buildBlock ($broadcasterID, $supportID)
	{
		$support = \Objects\Support::getInstance($supportID);
		
		$block = new View("defaultAds/block");
		$block->broadcasterID = $broadcasterID;
		$block->displayName = \__("defaultAdSupport", ["supportName" => $support->getName()]);
		$block->screens = "";
		
		//Retrieve infos on the screen
		$defaultAdModel = new \Models\DefaultadModel($broadcasterID);
		$screens = $defaultAdModel->screens($supportID);
		
		$screensView = new Composer();
		
		$multipleScreens = count($screens) > 1 ? true : false;
		
		//Create the view for each screen
		foreach ($screens as $screen) {
			//Get the screen creative (will return false if there is none)
			$creativePath = $defaultAdModel->path($screen['ID'], true);
			
			$view = new View("ads/blockScreen");
			$view->hasCreative = false;
			
			//Attach infos to the screen block
			if ($creativePath != false) {
				$view->hasCreative = true;
				$view->creativePath = $creativePath;
			}
			
			$view->multipleScreens = $multipleScreens;
			$view->adID = $broadcasterID;
			$view->screenID = $screen['ID'];
			$view->screenName = $screen['name'];
			$view->screenWidth = $screen['width'];
			$view->screenHeight = $screen['height'];
			$view->mediaType = 1;
			$view->mediaTypeName = $screen['mediaTypeName'];
			$view->reason = "defaultAd";
			
			$screensView->attach($view);
		}
		
		//Attach all the screens block and return the ad block
		$block->screens = $screensView->render();
		
		return $block;
	}
	
	
	/**
	 * Create the modal to display the creative
	 * @param integer $adID Combo ID
	 * @param integer $screenID
	 */
	public function zoom ($broadcasterID, $screenID)
	{
		$broadcasterID = Sanitize::int($broadcasterID);
		$screenID = Sanitize::int($screenID);
		
		//Authorizations
		\Library\User::canEditDefaultAds($broadcasterID);
		
		$defaultAdModel = new \Models\DefaultadModel($broadcasterID);
		$path = $defaultAdModel->path($screenID, true);
		
		$view = new View("modals/displayCreative");
		$view->creativePath = $path;
		$view->mediaType = 1;
		
		echo $view->render();
	}
	
	
	public function add ($broadcasterID, $screenID)
	{
		$broadcasterID = Sanitize::int($broadcasterID);
		$screenID = Sanitize::int($screenID);
		
		$_record = Record::createRecord(Record::DEFAULT_AD_UPLOADED);
		$_record->setRef1($broadcasterID)
			->setRef2($screenID);
		
		//Authorizations
		if (!\Library\User::canEditDefaultAds($broadcasterID)) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
		}
		
		//Do we have the file?
		if (!array_key_exists("creative", $_FILES)) {
			$_record->setResult(Record::FATAL_ERROR)
				->setMessage("Missing POST var")
				->save();
			
			//display errors
			$errorModal = new View("modals/uploadErrors");
			$errorModal->errors = ["NO_FILE"];
			
			header('Content-Type: application/json');
			echo json_encode(["success" => false, "html" => $errorModal->render()]);
			
			return;
		}
		
		$defaultAdModel = new \Models\DefaultadModel($broadcasterID);
		
		//Is there already an ad here ?
		if ($defaultAdModel->exist($screenID)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Default Ad already present")
				->save();
			
			return; //Cannot add if there's already something here
		}
		
		$specs = $defaultAdModel->screenSpecs($screenID);
		
		$creativeController = new CreativeController();
		$errors = $creativeController->controlCreative($_FILES['creative'], $specs);
		
		if (count($errors) != 0) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Creative does not match specs")
				->save();
			
			//display errors
			$errorModal = new View("modals/uploadErrors");
			$errorModal->errors = $errors;
			$errorModal->specs = $specs;
			
			header('Content-Type: application/json');
			echo json_encode(["success" => false, "html" => $errorModal->render()]);
			
			return;
		}
		
		//The file is OK
		
		//Let's start by extracting it's name and extension
		$pathInfo = pathinfo($_FILES['creative']["name"]);
		$creativeName = $pathInfo['filename'];
		$creativeExtension = $pathInfo['extension'];
		
		//registering it in the DDB and store it
		$defaultAdModel->create($screenID, $creativeName, $creativeExtension, $_FILES['creative']['tmp_name']);
		
		$screen = \Objects\Screen::getInstance($screenID);
		$supportID = $screen->getSupportID();
		
		$_record->setResult(Record::OK)
			->save();
		
		//And return what to show in place on the ad block
		header('Content-Type: application/json');
		echo json_encode(["success" => true, "html" => $this->buildBlock($broadcasterID, $supportID)->render()]);
	}
	
	
	public function delete ($broadcasterID, $screenID)
	{
		$broadcasterID = Sanitize::int($broadcasterID);
		$screen = \Objects\Screen::getInstance($screenID);
		
		$_record = Record::createRecord(Record::DEFAULT_AD_REMOVED);
		$_record->setRef1($broadcasterID)
			->setRef2($screen->getID());
		
		//Authorizations
		if (!\Library\User::canEditDefaultAds($broadcasterID)) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
		}
		
		$defaultAdModel = new \Models\DefaultadModel($broadcasterID);
		$defaultAdModel->delete($screen->getID());
		
		$supportID = $screen->getSupportID();
		
		$_record->setResult(Record::OK)
			->save();
		
		$this->display($broadcasterID, $supportID);
	}
}
