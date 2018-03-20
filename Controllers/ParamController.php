<?php

namespace Controllers;

use \Library\View,
	\Library\Composer;
use Objects\Record;

class ParamController
{
	public function display ($screen)
	{
		\Library\User::onlyAdmins();
		
		switch ($screen) {
			case "globals":
				
				\Library\User::restricted("EDIT_PARAMS");
				
				$view = new View("params/globals");
				
				$paramModel = new \Models\ParamModel();
				$view->params = $paramModel->getAll();
			
			break;
			case "admins":
			
			break;
		}
		
		echo $view->render();
	}
	
	
	public function form ($formName, $ID)
	{
		\Library\User::onlyAdmins();
		
		switch ($formName) {
			case "editglobal":
				
				\Library\User::restricted("EDIT_PARAMS");
				
				$form = new View("params/editGlobal");
				
				$paramName = \Library\Sanitize::string($ID);
				
				$paramModel = new \Models\ParamModel();
				$form->param = $paramModel->get($paramName);
				$form->paramValue = \Library\Params::get($paramName);
			
			break;
		}
		
		echo $form->render();
	}
	
	
	public function update ($type)
	{
		switch ($type) {
			case "global":
				$this->updateGlobal();
			break;
		}
	}
	
	
	private function updateGlobal ()
	{
		$_record = Record::createRecord(Record::GLOBAL_UPDATED);
		
		if (!\Library\User::hasPrivilege("EDIT_PARAMS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		if (empty($_POST['paramValue']) || empty($_POST['paramName'])) {
			$_record->setResult(Record::FATAL_ERROR)
				->setMessage("Missing field")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$paramName = $_POST['paramName'];
		
		$paramModel = new \Models\ParamModel();
		$paramType = $paramModel->type($paramName);
		
		if ($paramType == false) {
			$_record->setResult(Record::FATAL_ERROR)
				->setMessage("Bad global param ID")
				->save();
			
			http_response_code(400);
			echo "fatalError";
			return;
		}
		
		switch ($paramType) {
			case "int":
				
				$paramValue = \Library\Sanitize::int($_POST['paramValue']);
			
			break;
			case "list":
			case "duration":
				
				$paramValue = implode(",", $_POST['paramValue']);
			
			break;
			default; //case "string":
				
				$paramValue = $_POST['paramValue'];
		}
		
		$paramModel->updateGlobal($paramName, $paramValue);
		
		$_record->setResult(Record::OK)
			->save();
		
		$this->display("globals");
	}
}

