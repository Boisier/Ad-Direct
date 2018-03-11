<?php

namespace Models;

class AuthModel
{
	private $ddb;
	
	
	/**
	 * AuthModel constructor.
	 */
	public function __construct()
	{
		$this->ddb = \Library\DBA::get();
	}
	
	
	/**
	 * Tell if an account with the given email exist
	 * @param $login
	 * @return bool
	 */
	public function accountExist($login)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM users WHERE user_email = :login");
		$stmt->execute([":login" => $login]);
		$nbrAccounts = $stmt->fetchColumn();
		
		if($nbrAccounts == 1)
			return true;
		
		return false;
	}
	
	
	/**
	 * Return a user ID for the given email
	 * @param $email
	 * @return int
	 */
	public function getUserIDByEmail($email)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				user_id
			FROM
				users
			WHERE
				user_email = :email
		");
		$stmt->execute([":email" => $email]);
		
		return $stmt->fetchColumn();
	}
}

