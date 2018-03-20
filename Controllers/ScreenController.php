<?php

namespace Controllers;

use \Library\View,
	\Library\Composer;
use Objects\Record;

class ScreenController
{
	public function form ($formName, $ID = 0)
	{
		\Library\User::restricted("EDIT_SUPPORTS");
		
		switch ($formName) {
			case "add":
				
				$form = new View("screens/add");
				
				$form->supportID = \Library\Sanitize::int($ID);
			
			break;
			case "edit":
			case "delete":
				
				$screen = \Objects\Screen::getInstance($ID);
				
				$form = new View("screens/$formName");
				$form->screenID = $screen->getID();
				$form->supportID = $screen->getSupportID();
				$form->screenName = $screen->getName();
				$form->screenWidth = $screen->getWidth();
				$form->screenHeight = $screen->getHeight();
			
			break;
		}
		
		echo $form->render();
	}
	
	
	public function add ($supportID)
	{
		$supportID = \Library\Sanitize::int($supportID);
		
		$_record = Record::createRecord(Record::SCREEN_CREATED);
		$_record->setRef2($supportID);
		
		if (!\Library\User::hasPrivilege("EDIT_SUPPORTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
		}
		
		if (empty($_POST['screenWidth']) || empty($_POST['screenHeight'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing fields")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$screenName = \Library\Sanitize::string($_POST['screenName']);
		$screenWidth = \Library\Sanitize::string($_POST['screenWidth']);
		$screenHeight = \Library\Sanitize::string($_POST['screenHeight']);
		
		$screenModel = new \Models\ScreenModel();
		$screenID = $screenModel->add($supportID, $screenName, $screenWidth, $screenHeight);
		
		$_record->setResult(Record::OK)
			->setRef1($screenID)
			->save();
		
		$supportController = new SupportController();
		$supportController->display($supportID);
	}
	
	
	/**
	 * @param $screenID
	 */
	public function edit ($screenID)
	{
		$screen = \Objects\Screen::getInstance($screenID);
		
		$_record = Record::createRecord(Record::SCREEN_UPDATED);
		$_record->setRef1($screen->getID());
		
		if (!\Library\User::hasPrivilege("EDIT_SUPPORTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
		}
		
		if (empty($_POST['screenWidth']) || empty($_POST['screenHeight'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing fields")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$screenName = \Library\Sanitize::string($_POST['screenName']);
		$screenWidth = \Library\Sanitize::string($_POST['screenWidth']);
		$screenHeight = \Library\Sanitize::string($_POST['screenHeight']);
		
		$screen->setName($screenName)
			->setWidth($screenWidth)
			->setHeight($screenHeight)
			->save();
		
		$_record->setResult(Record::OK)
			->save();
		
		$supportID = $screen->getSupportID();
		
		$supportController = new SupportController();
		$supportController->display($supportID);
	}
	
	
	public function delete ($screenID)
	{
		$screenID = \Library\Sanitize::int($screenID);
		
		$_record = Record::createRecord(Record::SCREEN_REMOVED);
		$_record->setRef1($screenID);
		
		if (!\Library\User::hasPrivilege("EDIT_SUPPORTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		$screen = \Objects\Screen::getInstance($screenID);
		$support = $screen->getSupport();
		
		if ($support->isUsed()) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Cannot remove screen if support is used")
				->save();
			
			$view = new View("screens/cannotDeleteScreen");
			$view->supportID = $support->getID();
			
			echo $view->render();
		}
		
		//tODO: delete client default Ad for this screen
		
		$screen->delete();
		
		$_record->setResult(Record::OK)
			->save();
		
		$supportController = new SupportController();
		$supportController->display($support->getID());
	}
}

