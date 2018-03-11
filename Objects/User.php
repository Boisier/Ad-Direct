<?php

namespace Objects;

use \Models\UserModel;

class User
{
	/**
	 * @var UserModel
	 */
	private $userModel;
	
	/**
	 * @var int|null
	 */
	private $userID = NULL;
	
	/**
	 * @var string
	 */
	private $userName;
	
	/**
	 * @var int
	 */
	private $broadcasterID;
	
	/**
	 * @var int
	 */
	private $isAdmin;
	
	/**
	 * @var int
	 */
	private $parentID;
	
	/**
	 * @var string
	 */
	private $email;
	
	/**
	 * @var string
	 */
	private $password;
	
	/**
	 * @var int
	 */
	private $creationTime;
	
	/**
	 * @var int
	 */
	private $lastActivity;
	
	/**
	 * @var int
	 */
	private $isLive;
	
	/**
	 * @var string
	 */
	private $locale;
	
	/**
	 * @var int
	 */
	private $legalApproved;
	
	/**
	 * @var string
	 */
	private $timezone;
	
	
	
	/**
	 * Try to instantiate an User Object
	 * @param  integer $userID Id of the user
	 * @return bool|User   a User object on success, false otherwise
	 */
	public static function getInstance($userID)
	{
		//Sanitize the user ID
		$userID = \Library\Sanitize::int($userID);
		
		//Do not instantiate if equal zero
		if($userID == 0)
			return false;
		
		$userModel = new UserModel();
		
		//Verify if user exist
		if(!$userModel->userExist($userID))
			return false;
		
		//Instantiate the user
		return new User($userID);
	}
	
	
	
	/**
	 * Set the user ID
	 * @private
	 * @param integer $userID the user ID
	 */
	private function __construct($userID)
	{
		$this->userID = $userID;
		$this->userModel = new UserModel($userID);
		
		$userInfos = $this->userModel->getInfos();
		
		$this->userName = $userInfos['name'];
		$this->broadcasterID = $userInfos['broadcasterID'];
		$this->isAdmin = $userInfos['isAdmin'];
		$this->parentID = $userInfos['parentID'];
		$this->email = $userInfos['email'];
		$this->password = $userInfos['password'];
		$this->creationTime = $userInfos['creationTime'];
		$this->lastActivity = $userInfos['lastActivity'];
		$this->isLive = $userInfos['isLive'];
		$this->locale = $userInfos['locale'];
		$this->legalApproved = $userInfos['legalApproved'];
		$this->timezone = $userInfos['timezone'];
	}
	
	
	/**
	 * @return int
	 */
	public function getID()
	{
		return $this->userID;
	}
	
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->userName;
	}
	
	/**
	 * @return Broadcaster
	 */
	public function getBroadcaster()
	{
		return Broadcaster::getInstance($this->broadcasterID);
	}
	
	/**
	 * @return int
	 */
	public function getBroadcasterID()
	{
		return $this->broadcasterID;
	}
	
	/**
	 * @return int|bool
	 */
	public function isAdmin()
	{
		return $this->isAdmin;
	}
	
	/**
	 * @return int
	 */
	public function getParentID()
	{
		return $this->parentID;
	}
	
	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}
	
	/**
	 * @return mixed
	 */
	public function getPassword()
	{
		return $this->password;
	}
	
	/**
	 * @return int
	 */
	public function getCreationTime()
	{
		return $this->creationTime;
	}
	
	/**
	 * @return int
	 */
	public function getLastActivity()
	{
		return $this->lastActivity;
	}
	
	/**
	 * @return int|bool
	 */
	public function isLive()
	{
		return $this->isLive;
	}
	
	/**
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale;
	}
	
	/**
	 * @return string
	 */
	public function getTimezone()//: string
	{
		return $this->timezone;
	}
	
	/**
	 * @return string
	 */
	public function hasApprovedLegal()
	{
		return $this->legalApproved;
	}
	
	
	/**
	 * @param int $page
	 * @param int $length
	 * @return Record[] array of records
	 */
	public function getLogPage($page = 0, $length = 30)
	{
		$start = $page * $length;
		
		$logsID = $this->userModel->getLogPage($start, $length);
		
		$logs = [];
		
		foreach($logsID as $logID)
		{
			array_push($logs, \Objects\Record::getInstance($logID));
		}
		
		return $logs;
	}
	
	/**
	 * @return Campaign[]
	 */
	public function getCampaigns()
	{
		$campaignsID = $this->userModel->getCampaigns();
		
		$campaigns = [];
		
		foreach($campaignsID as $campaignID)
		{
			array_push($campaigns, Campaign::getInstance($campaignID));
		}
		
		return $campaigns;
	}
	
	/**
	 * @return int[]
	 */
	public function getCampaignsID()
	{
		return $this->userModel->getCampaigns();
	}
	
	
	/**
	 * @param null|string $option
	 * @return bool
	 */
	public function getPrivileges($option = null)
	{
		return $this->userModel->privileges($option);
	}
	
	/**
	 * @param string $privilegeName
	 * @param string|int|bool|null $privilegeValue
	 */
	public function addPrivilege($privilegeName, $privilegeValue)
	{
		$this->userModel->addPrivilege($privilegeName, $privilegeValue);
	}
	
	/**
	 * remove all privileges of user
	 */
	public function clearPrivileges()
	{
		$this->userModel->clearPrivileges();
	}
	
	
	
	
	/**
	 * @param string $userName
	 * @return User
	 */
	public function setUserName($userName)
	{
		$this->userName = $userName;
		return $this;
	}
	
	/**
	 * @param string $email
	 * @return User
	 */
	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}
	
	/**
	 * @param mixed $password
	 * @return User
	 */
	public function setPassword($password)
	{
		$this->password = password_hash($password, \PASSWORD_DEFAULT);
		return $this;
	}
	
	/**
	 * @return User
	 */
	public function setLegalApproved()
	{
		$this->legalApproved = 1;
		return $this;
	}
	
	/**
	 * @param string $timezone
	 * @return User
	 */
	public function setTimezone(string $timezone)//: User
	{
		$this->timezone = $timezone;
		return $this;
	}
	
	
	
	
	
	
	/**
	 * Toggle activation status of this user
	 */
	public function toogleActivation()
	{
		if($this->isAdmin())
			return;
		
		$this->isLive = !$this->isLive;
		
		$this->userModel->toggle();
	}
	
	
	/**
	 * Save the User
	 */
	public function save()
	{
		$this->userModel->update($this->userName,
								 $this->email,
								 $this->password,
			                     $this->legalApproved,
								 $this->timezone);
	}
	
	
	/**
	 * Remove the user
	 * Save should not be called after delete has been called
	 */
	public function delete()
	{
		$this->userModel->delete();
	}
}
