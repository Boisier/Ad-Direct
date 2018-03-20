<?php

namespace Controllers;

use \Library\View,
	\Library\Composer;
use Objects\Record,
	Objects\User;

class UserController
{
	public function form ($formName, $ID = 0)
	{
		switch ($formName) {
			case "createclient":
				
				\Library\User::restricted("MANAGE_CLIENTS");
				
				$form = new View("users/createClient");
				$form->broadcasterID = $ID;
				$form->timezones = \Library\Localization::getTimezones();
			
			break;
			case "editclient":
			case "deleteclient":
				
				\Library\User::restricted("MANAGE_CLIENTS");
				
				$client = User::getInstance($ID);
				
				$form = new View("users/$formName");
				$form->clientID = $client->getID();
				$form->clientName = $client->getName();
				$form->clientEmail = $client->getEmail();
				$form->broadcasterID = $client->getBroadcasterID();
				$form->privileges = $client->getPrivileges("justNames");
				
				$form->timezone = $client->getTimezone();
				$form->timezones = \Library\Localization::getTimezones();
			
			break;
			case "createadmin":
				
				\Library\User::restricted("EDIT_ADMINS");
				
				$form = new View("admin/add");
				$form->timezones = \Library\Localization::getTimezones();
			
			break;
			case "editadmin":
			case "deleteadmin":
				
				\Library\User::restricted("EDIT_ADMINS");
				
				$admin = User::getInstance($ID);
				
				$form = new View("admin/$formName");
				$form->adminID = $admin->getID();
				$form->adminName = $admin->getName();
				$form->adminEmail = $admin->getEmail();
				$form->privileges = $admin->getPrivileges("justNames");
				
				$form->timezone = $admin->getTimezone();
				$form->timezones = \Library\Localization::getTimezones();
			
			break;
		}
		
		echo $form->render();
	}
	
	/**
	 * Display admin list
	 */
	public function admins ()
	{
		\Library\User::restricted("EDIT_ADMINS");
		
		$userModel = new \Models\UserModel();
		$admins = $userModel->admins();
		
		$list = new Composer();
		
		foreach ($admins as $admin) {
			$adminView = new View("admin/adminList");
			$adminView->adminID = $admin->getID();
			$adminView->adminName = $admin->getName();
			$adminView->adminEmail = $admin->getEmail();
			$adminView->privileges = $admin->getPrivileges();
			
			$list->attach($adminView);
		}
		
		$view = new View("admin/param");
		$view->adminList = $list->render();
		
		echo $view->render();
	}
	
	/**
	 * User creation dispatcher
	 * @param $type
	 */
	public function create ($type)
	{
		if ($type == "client") {
			$this->createClient();
			return;
		}
		
		if ($type == "admin") {
			$this->createAdmin();
			return;
		}
	}
	
	/**
	 * create a new client
	 */
	private function createClient ()
	{
		$_record = Record::createRecord(Record::CLIENT_CREATED);
		
		if (!\Library\User::hasPrivilege("MANAGE_CLIENTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		if (empty($_POST['clientName']) || empty($_POST['clientEmail']) || empty($_POST['clientPassword']) || empty($_POST['broadcasterID'] || empty($_POST['clientTimezone']))) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing field")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$name = \Library\Sanitize::string($_POST['clientName']);
		$email = \Library\Sanitize::email($_POST['clientEmail']);
		$password = \Library\Sanitize::string($_POST['clientPassword']);
		$broadcasterID = \Library\Sanitize::int($_POST['broadcasterID']);
		$timezone = \Library\Sanitize::string($_POST['clientTimezone']);
		
		if (!$email) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Bad email")
				->save();
			
			http_response_code(400);
			echo "badEmail";
			return;
		}
		
		$broadcasterModel = new \Models\BroadcasterModel();
		
		if (!$broadcasterModel->broadcasterExist($broadcasterID)) {
			$_record->setResult(Record::FATAL_ERROR)
				->setMessage("Given broadcaster does not exist")
				->save();
			
			http_response_code(400);
			echo "fatalError";
			return;
		}
		
		$userModel = new \Models\userModel();
		
		if ($userModel->isEmailUsed($email)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Email already used by another account")
				->save();
			
			http_response_code(400);
			echo "emailAlreadyUsed";
			return;
		}
		
		if (!\Library\Localization::timezoneExists($timezone)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Specified timezone is not currently supported (" . $timezone . ")")
				->save();
			
			http_response_code(400);
			echo "fatal_error";
			return;
		}
		
		$userID = $userModel->addClient($name, $email, $password, $broadcasterID, \Library\User::id(), $timezone);
		$broadcasterController = new BroadcasterController();
		
		$_record->setResult(Record::OK)
			->save();
		
		$emailController = new EmailController();
		$emailController->create(EmailController::EMAIL_NEW_CLIENT, $userID, 0, ["password" => $password]);
		
		if (empty($_POST['privileges'])) {
			$broadcasterController->display($broadcasterID, "CLIENTS");
			return;
		}
		
		$user = User::getInstance($userID);
		
		foreach ($_POST['privileges'] as $privilege) {
			$privilege = \Library\Sanitize::string($privilege);
			$user->addPrivilege($privilege, null);
		}
		
		$broadcasterController->display($broadcasterID, "CLIENTS");
	}
	
	/**
	 * Create a new admin
	 */
	private function createAdmin ()
	{
		$_record = Record::createRecord(Record::ADMIN_CREATED);
		
		if (!\Library\User::hasPrivilege("EDIT_ADMINS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		if (empty($_POST['adminName']) || empty($_POST['adminEmail']) || empty($_POST['adminPassword'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing fields")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$name = \Library\Sanitize::string($_POST['adminName']);
		$email = \Library\Sanitize::email($_POST['adminEmail']);
		$password = \Library\Sanitize::string($_POST['adminPassword']);
		$timezone = \Library\Sanitize::string($_POST['adminTimezone']);
		
		if (!$email) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Bad email")
				->save();
			
			http_response_code(400);
			echo "badEmail";
			return;
		}
		
		$userModel = new \Models\userModel();
		
		if ($userModel->isEmailUsed($email)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Email already use by another account")
				->save();
			
			http_response_code(400);
			echo "emailAlreadyUsed";
			return;
		}
		
		if (!\Library\Localization::timezoneExists($timezone)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Specified timezone is not currently supported (" . $timezone . ")")
				->save();
			
			http_response_code(400);
			echo "fatal_error";
			return;
		}
		
		$adminID = $userModel->addAdmin($name, $email, $password, \Library\User::id(), $timezone);
		
		$_record->setResult(Record::OK)
			->save();
		if (empty($_POST['privileges'])) {
			$this->admins();
			return;
		}
		
		$user = User::getInstance($adminID);
		
		foreach ($_POST['privileges'] as $privilege) {
			$privilege = \Library\Sanitize::string($privilege);
			$user->addPrivilege($privilege, null);
		}
		
		//TODO: EMAIL
		
		$this->admins();
	}
	
	
	/**
	 * Update a user account
	 * @param $type
	 */
	public function edit ()
	{
		if (!isset($_POST['isAdmin'])) {
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		//Base init based on account type
		$recordType = Record::CLIENT_UPDATED;
		$privilegeRequired = "MANAGE_CLIENTS";
		
		if ($_POST['isAdmin']) {
			$recordType = Record::ADMIN_UPDATED;
			$privilegeRequired = "EDIT_ADMINS";
		}
		
		$_record = Record::createRecord($recordType);
		
		//Authorizations
		if (!\Library\User::hasPrivilege($privilegeRequired)) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		//Missing fields ?
		if (empty($_POST['userName']) || empty($_POST['userEmail']) || empty($_POST['userID'])) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Missing field")
				->save();
			
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		//Sanitize everything
		$userID = \Library\Sanitize::int($_POST['userID']);
		$name = \Library\Sanitize::string($_POST['userName']);
		
		if ($userID != 1)
			$email = \Library\Sanitize::email($_POST['userEmail']);
		else
			$email = $_POST['userEmail'];
		
		$password = \Library\Sanitize::string($_POST['userPassword']);
		$timezone = \Library\Sanitize::string($_POST['userTimezone']);
		
		$user = User::getInstance($userID);
		$userModel = new \Models\UserModel();
		
		//Verify user ID
		if ($user == false) {
			$_record->setResult(Record::FATAL_ERROR)
				->setMessage("Bad user ID")
				->save();
			
			http_response_code(400);
			echo "fatalError";
			return;
		}
		
		$_record->setRef1($userID);
		
		//Verify email format
		if (!$email) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Bad email")
				->save();
			
			http_response_code(400);
			echo "badEmail";
			return;
		}
		
		
		if ($email != $user->getEmail() && $userModel->isEmailUsed($email)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Email already used by another account")
				->save();
			
			http_response_code(400);
			echo "emailAlreadyUsed";
			return;
		}
		
		if (!\Library\Localization::timezoneExists($timezone)) {
			$_record->setResult(Record::REFUSED)
				->setMessage("Specified timezone is not currently supported (" . $timezone . ")")
				->save();
			
			http_response_code(400);
			echo "fatal_error";
			return;
		}
		
		$oldEmail = $user->getEmail();
		
		$user->setUserName($name)
			->setEmail($email)
			->setTimezone($timezone);
		
		$emailController = new EmailController();
		
		if (strlen($password) > 0) {
			$_record2 = Record::createRecord(Record::CLIENT_PASSWORD_UPDATED);
			$_record2->setRef1($userID);
			
			$user->setPassword($password);
			
			$_record2->setResult(Record::OK)
				->save();
			
		}
		
		$user->save();
		
		$_record->setResult(Record::OK)
			->save();
		
		if (!$user->isAdmin()) {
			if ($oldEmail != $email)
				$emailController->create(EmailController::EMAIL_CLIENT_UPDATE, $userID);
			
			if (strlen($password) > 0)
				$emailController->create(EmailController::EMAIL_CLIENT_NEW_PASSWORD, $userID, 0, ["password" => $password]);
		}
		
		if (!$user->isAdmin())
			$broadcasterController = new BroadcasterController();
		
		//Update privileges
		$user->clearPrivileges();
		
		if (empty($_POST['privileges'])) {
			if ($user->isAdmin())
				return $this->admins();
			
			return $broadcasterController->display($user->getBroadcasterID(), "CLIENTS");
		}
		
		foreach ($_POST['privileges'] as $privilege) {
			$privilege = \Library\Sanitize::string($privilege);
			$user->addPrivilege($privilege, null);
		}
		
		if ($user->isAdmin())
			return $this->admins();
		
		$broadcasterController->display($user->getBroadcasterID(), "CLIENTS");
	}
	
	/**
	 * switch client activation
	 * @param $clientID
	 */
	public function toggle ($clientID)
	{
		$client = User::getInstance($clientID);
		
		$_record = Record::createRecord(Record::CLIENT_ACTIVATION_TOGGLED);
		$_record->setRef1($clientID);
		
		if (!\Library\User::hasPrivilege("MANAGE_CLIENTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		//Toggle activation
		$client->toggle();
		
		$_record->setResult(Record::OK)
			->save();
		
		$broadcasterController = new BroadcasterController();
		$broadcasterController->display($client->getBroadcasterID(), "CLIENTS");
	}
	
	/**
	 * User removal dispatcher
	 * @param string $type
	 * @param int    $userID
	 */
	public function delete ($type, $userID)
	{
		if ($type == "client") {
			$this->deleteClient($userID);
			return;
		}
		
		if ($type == "admin") {
			$this->deleteAdmin($userID);
			return;
		}
		
		//TODO: Error;
	}
	
	/**
	 * Remove a client
	 * @param $userID
	 */
	private function deleteClient ($userID)
	{
		$userID = \Library\Sanitize::int($userID);
		
		$_record = Record::createRecord(Record::CLIENT_REMOVED);
		$_record->setRef1($userID);
		
		if (!\Library\User::hasPrivilege("MANAGE_CLIENTS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		$user = User::getInstance($userID);
		
		$broadcasterID = $user->getBroadcasterID();
		
		$user->delete();
		
		$_record->setResult(Record::OK)
			->save();
		
		$broadcasterController = new BroadcasterController();
		$broadcasterController->display($broadcasterID, "CLIENTS");
	}
	
	/**
	 * Remove an admin
	 * @param $userID
	 */
	private function deleteAdmin ($userID)
	{
		$user = User::getInstance($userID);
		
		$_record = Record::createRecord(Record::ADMIN_REMOVED);
		$_record->setRef1($user->getID());
		
		if (!\Library\User::hasPrivilege("EDIT_ADMINS")) {
			$_record->setResult(Record::UNAUTHORIZED)
				->save();
			
			return;
		}
		
		$user->delete();
		
		$_record->setResult(Record::OK)
			->save();
		
		if ($user->getID() == \Library\User::id()) {
			$authController = new AuthController();
			$authController->logout();
			return;
		}
		
		$this->admins();
	}
	
	/**
	 * Mark client as having approved legals
	 * @param $userID
	 */
	public function approvelegals ($userID)
	{
		$user = User::getInstance($userID);
		
		$_record = Record::createRecord(Record::LEGAL_APPROVED);
		
		if (!$user)
			return;
		
		$user->setLegalApproved()
			->save();
		
		$_record->setResult(Record::OK)
			->save();
		
		header("location:/");
	}
}

