<?php

namespace Controllers;

use \Library\View,
	\Library\Sanitize;
use Objects\Record;

class BroadcastergroupController
{
	/**
	 * Print the needed form to interact with the broadcaster
	 * @param string  $formName            The name of the form
	 * @param integer [$broadcasterID      = 0] The broadcaster ID
	 */
	public function form($formName, $broadcasterGroupID = 0) 
	{
		\Library\User::restricted("MANAGE_CLIENTS");
		
		switch($formName)
		{
			case "create":
				
				//Get the view
				$form = new View("broadcasterGroups/add");
				
			break;
			case "rename":
			case "delete":
				
				//Get the view
				$form = new View("broadcasterGroups/".$formName);
				
				//Retrieve needed infos
				$broadcasterGroupID = Sanitize::int($broadcasterGroupID);
		
				$model = new \Models\BroadcasterGroupModel();
				$broadcasterGroup = $model->getInfos($broadcasterGroupID);
				
				//Attach the infos
				$form->groupID = $broadcasterGroup['ID'];
				$form->groupName = $broadcasterGroup['name'];
				
			break;
		}
		
		echo $form->render();
	}
	
	
	
	
	
	
	public function home()
	{
		\Library\User::restricted("MANAGE_CLIENTS");
		
		$broadcasterGroupModel = new \Models\BroadcasterGroupModel();
		
		$view = new View("broadcasterGroups/home");
		$view->broadcasterGroups = $broadcasterGroupModel->getAll();
			
		echo $view->render();
	}
	
	
	/**
	 * Create a new Broadcaster Group
	 */
	public function create()
	{
		$_record = Record::createRecord(Record::BROADCASTER_GROUP_CREATED);
		
		if(!\Library\User::hasPrivilege("MANAGE_CLIENTS"))
		{
			$_record->setResult(Record::UNAUTHORIZED)
					->save();
		}
		
		if(empty($_POST['name']))
		{
			$_record->setResult(Record::REFUSED)
					->setMessage("Missing field")
					->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$broadcasterGroupName = Sanitize::string($_POST['name']);
		
		$model = new \Models\BroadcasterGroupModel();
		
		if($model->broadcasterGroupExistName($broadcasterGroupName))
		{
			$_record->setResult(Record::REFUSED)
					->setMessage("New broadcaster group name is already in use")
					->save();
			
			http_response_code(400);
			echo "alreadyExist";
			return;
		}
		
		$broadcasterGroupID = $model->create($broadcasterGroupName);
		
		$_record->setRef1($broadcasterGroupID)
				->setResult(Record::OK)
				->save();
		
		$this->home();
	}
	
	
	/**
	 * Update the broadcaster group
	 * @param $groupID
	 */
	public function update($groupID)
	{
		$groupID = Sanitize::int($groupID);
		
		$_record = Record::createRecord(Record::BROADCASTER_GROUP_UPDATED);
		$_record->setRef1($groupID);
		
		if(!\Library\User::hasPrivilege("MANAGE_CLIENTS"))
		{
			$_record->setResult(Record::UNAUTHORIZED)
					->save();
			
			return;
		}
		
		/*Did we received everything ?*/
		if(empty($_POST['name']))
		{
			$_record->setResult(Record::REFUSED)
					->setMessage("Missing Field")
					->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$groupName = Sanitize::string($_POST['name']);
		
		$model = new \Models\BroadcasterGroupModel();
		
		/*Can we use this name ?*/
		if($groupName != $model->getInfos($groupID)["name"] && $model->broadcasterGroupExistName($groupName))
		{
			$_record->setResult(Record::REFUSED)
					->setMessage("New name already in use")
					->save();
			
			http_response_code(400);
			echo "alreadyExist";
			return;
		}
		
		/*Update name*/
		$model->setName($groupID, $groupName);
		
		$_record->setResult(Record::OK)
				->save();
		
		$this->home();
	}
	
	
	public function delete($groupID)
	{
		$groupID = Sanitize::int($groupID);
		
		$_record = Record::createRecord(Record::BROADCASTER_GROUP_REMOVED);
		$_record->setRef1($groupID);
		
		if(\Library\User::hasPrivilege("MANAGE_CLIENTS"))
		{
			$_record->setResult(Record::UNAUTHORIZED)
					->save();
			
			return;
		}
		
		$model = new \Models\BroadcasterGroupModel();
		$model->delete($groupID);
		
		$_record->setResult(Record::OK);
		
		$this->home();
	}
}
