<?php

namespace Controllers;

use \Library\Session,
	\Library\Sanitize,
	\Objects\Record;

class AuthController
{
	private $model;
	
	/**
	 * AuthController constructor.
	 */
	public function __construct()
	{
		$this->model = new \Models\AuthModel();
	}
	
	
	
	
	
	/**
	 * Try to log in the user
	 */
	public function login()
	{
		$_record = Record::createRecord(Record::USER_LOGGED_IN);
		
		/**Not already logged in**/
		if(\Library\User::loggedIn())
		{
			$_record->setResult(Record::UNAUTHORIZED)
					->setMessage("User already logged in")
					->save();
			
			header("location:/");
			return;
		}
	
		/**All infos**/
		if(empty($_POST["login"]) || empty($_POST["password"]))
		{
			$_record->setResult(Record::REFUSED)
					->save();
			
			Session::write("loginEvent", "missingField");
			header("location:/");
			return;
		}
		
		/**Valid user**/
		
		//Collect and sanitize user inputs*/
		
		$login = Sanitize::string($_POST["login"], true);
		$password = Sanitize::string($_POST["password"], true);
		
		//DOes this user exist ?
		if(!$this->model->accountExist($login))
		{
			$_record->setResult(Record::REFUSED)
					->save();
			
			Session::write("loginEvent", "unknownAccount");
			header("location:/");
			return;
		}
		
		$userID = $this->model->getUserIDByEmail($login);
		$user = \Objects\User::getInstance($userID);
		
		$_record->setUserID($user->getID());
		
		//Is the password OK ?
		if(!password_verify($password, $user->getPassword()))
		{
			$_record->setResult(Record::REFUSED)
					->setMessage("Bad password")
					->save();
			
			Session::write("loginEvent", "loginFailed");
			header("location:/");
			return;	
		}
		   
		//Is this user live ? 
		if(!$user->isLive())
		{
			$_record->setResult(Record::REFUSED)
					->setMessage("Client is deativated")
					->save();
			
			Session::write("loginEvent", "inactiveAccount");
			header("location:/");
			return;
		}
		
		/** Valid user, Set session**/
		Session::write("userID", $user->getID());
		Session::write("userAdmin", $user->isAdmin());
		
		$_record->setResult(Record::OK)
				->save();
		
		header("location:/");
	}
	
	
	
	
	
	/**
	 * Log out the user
	 */
	public function logout()
	{
		\Library\User::onlyLoggedIn();
		
		$_record = Record::createRecord(Record::USER_LOGGED_OUT);
		
		//Empty the session
		Session::renewKey();
		Session::remove("userID");
		Session::remove("userAdmin");
		
		$_record->setResult(Record::OK)
				->save();
		
		header("location:/");
	}
}