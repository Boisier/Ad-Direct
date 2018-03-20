<?php

namespace Controllers;

use Library\{
	Sanitize, View, Composer
};

use Objects\{
	Record, Support
};

class SupportController
{
	/**
	 * @param     $formName
	 * @param int $supportID
	 */
	public function form ($formName, $supportID = 0)
	{
		switch ($formName) {
			case "create":
				
				$form = new View("supports/create");
			
			break;
			case "rename":
			case "delete":
				
				$support = Support::getInstance($supportID);
				
				$form = new View("supports/$formName");
				$form->supportID = $support->getID();
				$form->supportName = $support->getName();
			
			break;
		}
		
		echo $form->render();
	}
	
	
	/**
	 * Create a new support
	 */
	public function create ()
	{
		$_record = Record::createRecord(Record::SUPPORT_CREATED);
		
		if (!\Library\User::hasPrivilege("EDIT_SUPPORTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		if (empty($_POST['name'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing Field")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$supportName = \Library\Sanitize::string($_POST['name']);
		
		$model = new \Models\SupportModel();
		
		if ($model->supportExistName($supportName)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("A support with the same name already exist")
				->save();
			
			http_response_code(400);
			echo "alreadyExist";
			return;
		}
		
		$supportID = $model->create($supportName);
		
		$_record->setResult(Record::OK)
			->setRef1($supportID)
			->save();
		
		$this->display($supportID);
	}
	
	
	/**
	 *
	 */
	public function home ()
	{
		\Library\User::onlyLoggedIn();
		
		$view = new View("supports/home");
		
		$supportModel = new \Models\SupportModel();
		$supports = $supportModel->supportList();
		
		$list = new Composer();
		
		foreach ($supports as $support) {
			$supportView = new View("supports/supportList");
			$supportView->supportName = $support['name'];
			$supportView->supportID = $support['ID'];
			$supportView->screenNbr = $support['screens'];
			
			$supportView->reason = "supports";
			
			$list->attach($supportView);
		}
		
		$view->supportList = $list->render();
		
		echo $view->render();
	}
	
	
	/**
	 * @param $supportID
	 */
	public function display ($supportID)
	{
		\Library\User::restricted("EDIT_SUPPORTS");
		
		$support = Support::getInstance($supportID);
		
		$view = new View("supports/display");
		
		$view->supportID = $supportID;
		$view->supportName = $support->getName();
		
		$screens = $support->getScreensID();
		
		if (count($screens) == 0) {
			$noScreen = new View("supports/noScreen");
			$view->screens = $noScreen->render();
			
			echo $view->render();
			return;
		}
		
		$list = new Composer();
		
		$nbrScreens = count($screens);
		
		foreach ($screens as $screen) {
			$screenView = new View("supports/screenList");
			$screenView->screenID = $screen['ID'];
			
			if ($nbrScreens == 1 && strlen($screen['name']) == 0)
				$screenView->screenName = \__("mainScreen");
			else if ($nbrScreens != 1 && strlen($screen['name']) == 0)
				$screenView->screenName = \__("unNamedScreen", ["screenID" => $screen['ID']]);
			else
				$screenView->screenName = $screen['name'];
			
			$screenView->screenWidth = $screen['width'];
			$screenView->screenHeight = $screen['height'];
			
			$list->attach($screenView);
		}
		
		$view->screens = $list->render();
		echo $view->render();
	}
	
	
	/**
	 * @param $supportID
	 */
	public function edit ($supportID)
	{
		$supportID = Sanitize::int($supportID);
		$support = Support::getInstance($supportID);
		
		$_record = Record::createRecord(Record::SUPPORT_UPDATED);
		$_record->setRef1($supportID);
		
		if (!\Library\User::hasPrivilege("EDIT_SUPPORTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		/*Did we received everything ?*/
		if (empty($_POST['name'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing field")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$supportName = \Library\Sanitize::string($_POST['name']);
		
		$model = new \Models\SupportModel();
		
		/*Can we use this name ?*/
		if ($supportName != $support->getName() && $model->supportExistName($supportName)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("A support with the same name already exist")
				->save();
			
			http_response_code(400);
			echo "alreadyExist";
			return;
		}
		
		/*Update name*/
		$support->setName($supportName)
			->save();
		
		
		$_record->setResult(Record::OK)
			->save();
		
		$this->display($supportID);
	}
	
	
	/**
	 * @param $supportID
	 */
	public function delete ($supportID)
	{
		$supportID = \Library\Sanitize::int($supportID);
		
		$_record = Record::createRecord(Record::SUPPORT_REMOVED);
		$_record->setRef1($supportID);
		
		if (!\Library\User::hasPrivilege("EDIT_SUPPORTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		$support = Support::getInstance($supportID);
		
		if ($support->isUsed()) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Cannot remove support if it is in being used")
				->save();
			
			$view = new View("supports/cannotDeleteSupport");
			$view->supportID = $supportID;
			
			echo $view->render();
			return;
		}
		
		//Delete screens
		$screens = $support->getScreensID();
		
		$screenModel = new \Models\ScreenModel();
		
		foreach ($screens as $screen) {
			$screenModel->delete($screen['ID']);
		}
		
		$support->delete();
		
		$_record->setResult(Record::OK)
			->save();
		
		$this->home();
	}
}

