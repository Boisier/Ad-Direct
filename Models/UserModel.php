<?php

namespace Models;

use Objects\User;

class UserModel
{
	private $ddb;
	private $currentUser = NULL;
	
	/**
	 * Init model and set current user if needed
	 * @private
	 * @param integer [$userID      = 0] (optionnal) User ID to set as current
	 */
	public function __construct($userID = 0)
	{
		$this->ddb = \Library\DBA::get();
		
		$this->setUser($userID);
	}
	
	/**
	 * Set the given user ID as current
	 * @param  integer $userID User ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setUser($userID)
	{
		if($userID == 0)
			return false;
		
		if(!$this->userExist($userID))
			return false;
		
		$this->currentUser = $userID;
		
		return true;
	}
	
	/**
	 * Tell if a user exist or not by its ID
	 * @param  integer $userID user ID
	 * @return boolean true if the user exist, false otherwise
	 */
	public function userExist($userID)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				COUNT(*)
			FROM
				users
			WHERE
				user_id = :userID
		");
		$stmt->execute([":userID" => $userID]);
		$nbrAccounts = $stmt->fetchColumn();
		
		if($nbrAccounts == 1)
			return true;
		
		return false;
	}
	
	/**
	 * @param string|null $option
	 * @return bool|array
	 */
	public function privileges($option = null)
	{
		if($this->currentUser == NULL)
			return false;
		
		switch($option)
		{
			case "justNames":
				$get = "privilege_name";
			break;
			default;
				$get = "privilege_name, privilege_value";
		}
		
		$stmt = $this->ddb->prepare("SELECT ".$get." FROM privileges WHERE user_id = :userID");
		$stmt->execute([":userID" => $this->currentUser]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	/**
	 * @param string $privilegeName
	 * @param string|int|bool|null $privilegeValue
	 */
	public function addPrivilege($privilegeName, $privilegeValue)
	{
		$stmt = $this->ddb->prepare("
			INSERT INTO
				privileges(
					user_id,
					privilege_name,
					privilege_value
				)
			VALUES(
				:userID,
				:name,
				:value
			) ON DUPLICATE KEY UPDATE privilege_name = :name");
		
		$stmt->execute([":userID" => $this->currentUser,
					    ":name" => $privilegeName,
					    ":value" => $privilegeValue]);
	}
	
	/**
	 * Remove all privileges
	 */
	public function clearPrivileges()
	{
		$stmt = $this->ddb->prepare("
			DELETE FROM
				privileges
			WHERE
				user_id = :userID
		");
		$stmt->execute([":userID" => $this->currentUser]);
	}
	
	/**
	 * @param string $email
	 * @return bool
	 */
	public function isEmailUsed($email)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				COUNT(*)
			FROM
				users
			WHERE
				user_email = :email
		");
		$stmt->execute([":email" => $email]);
		$nbrEmails = $stmt->fetchColumn();
		
		if($nbrEmails == 1)
			return true;
		
		return false;	
	}
	
	/**
	 * @return array
	 */
	public function getInfos()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				user_id as ID,
				broadcaster_id as broadcasterID,
				user_admin as isAdmin,
				user_parent as parentID,
				user_name as name,
				user_email as email,
				user_password as password,
				user_creation_time as creationTime,
				user_last_activity as lastActivity,
				user_live as isLive,
				user_local as locale,
				legal_approved as legalApproved,
				time_zone as timezone
			FROM
				users
			WHERE
				user_id = :userID
		");
		$stmt->execute([":userID" => $this->currentUser]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * @return User[] list of admins
	 */
	public function admins()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				user_id as ID
			FROM
				users
			WHERE
				user_admin = 1
			ORDER BY
				user_name
		");
		$stmt->execute();
		
		$admins = [];
		
		while($adminID = $stmt->fetchColumn())
		{
			array_push($admins, User::getInstance($adminID));
		}
		
		return $admins;
	}
	
	/**
	 * @param string $name
	 * @param string $email
	 * @param string $password
	 * @param int $broadcasterID
	 * @param int $parentID
	 * @return int The new user ID
	 */
	public function addClient($name, $email, $password, $broadcasterID, $parentID, $timezone)
	{
		$stmt = $this->ddb->prepare("
			INSERT INTO
				users(
					broadcaster_id,
					user_admin,
					user_parent,
					user_name,
					user_email,
					user_password,
					user_creation_time,
					user_last_activity,
					user_live,
					time_zone
				)
			VALUES(
				:broadcasterID,
				:admin,
				:parent,
				:name,
				:email,
				:password,
				:creation,
				:lastActivity,
				:live,
				:timezone
			)
		");
		$stmt->execute([":broadcasterID" => $broadcasterID,
					   	":admin" => 0,
					   	":parent" => $parentID,
					   	":name" => $name,
					    ":email" => $email,
					    ":password" => password_hash($password, \PASSWORD_DEFAULT),
					   	":creation" => time(),
					   	":lastActivity" => time(),
					   	":live" => 1,
						":timezone" => $timezone]);
		
		return $this->ddb->lastInsertId();
	}
	
	/**
	 * @param string $name
	 * @param string $email
	 * @param string $password
	 * @param int $parentID
	 * @return int The new User ID
	 */
	public function addAdmin($name, $email, $password, $parentID, $timezone)
	{
		$stmt = $this->ddb->prepare("
			INSERT INTO
				users(
					user_admin,
					user_parent,
					user_name,
					user_email,
					user_password,
					user_creation_time,
					user_last_activity,
					user_live,
					time_zone
				)
			VALUES(
				:admin,
				:parent,
				:name,
				:email,
				:password,
				:creation,
				:lastActivity,
				:live,
				:timezone
			)
		");
		$stmt->execute([":admin" => 1,
					   	":parent" => $parentID,
					   	":name" => $name,
					    ":email" => $email,
					    ":password" => password_hash($password, \PASSWORD_DEFAULT),
					   	":creation" => time(),
					   	":lastActivity" => time(),
					   	":live" => 1,
						":timezone" => $timezone]);
		
		return $this->ddb->lastInsertId();
	}
	
	/**
	 * @param string $name
	 * @param string $email
	 * @param string $password
	 * @param int $legalApproved
	 * @param string $timezone
	 */
	public function update($name, $email, $password, $legalApproved, $timezone)
	{
		$stmt = $this->ddb->prepare("
			UPDATE
				users
			SET
				user_name = :name,
				user_email = :email,
				user_password = :password,
				legal_approved = :legal,
				time_zone = :timezone
			WHERE
				user_id = :clientID
		");
		$stmt->execute([":name" => $name,
					   	":email" => $email,
						":password" => $password,
					   	":legal" => $legalApproved,
					   	":timezone" => $timezone,
					   	":clientID" => $this->currentUser]);
	}
	
	/**
	 * Toggle client activation
	 */
	public function toggle()
	{
		$stmt = $this->ddb->prepare("
			UPDATE
				users
			SET
				user_live =
					CASE user_live WHEN 1
						THEN 0
						ELSE 1
					END
			WHERE
				user_id = :userID
		");
		$stmt->execute([":userID" => $this->currentUser]);
	}
	
	/**
	 * Remove user
	 */
	public function delete()
	{
		$stmt = $this->ddb->prepare("
			DELETE FROM
				users
			WHERE
				user_id = :userID
		");
		$stmt->execute([":userID" => $this->currentUser]);
	}
	
	/**
	 * Return campaigns of the user's broadcaster
	 * @return array
	 */
	public function getCampaigns()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				campaign_id
			FROM
				campaigns
				JOIN broadcasters
					ON broadcasters.broadcaster_id = campaigns.broadcaster_id
				JOIN users
					ON broadcasters.broadcaster_id = users.broadcaster_id
			WHERE user_id = :userID
		");
		$stmt->execute([":userID" => $this->currentUser]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	
	
	/**
	 * Get page of logs for the user
	 * @param int $start
	 * @param int $length
	 * @return array
	 */
	public function getLogPage($start, $length)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				record_id
			FROM
				user_tracking
			WHERE
				user_id = :userID
			ORDER BY record_date DESC
			LIMIT $length OFFSET $start");
		
		$stmt->execute([":userID" => $this->currentUser]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
}

