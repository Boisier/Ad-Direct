<?php

namespace Objects;

use \Models\RecordModel;

class Record
{
	private $recordID = NULL;
	private $recordModel;
	
	private $newRecord;
	private $saved = false;
	
	private $userID = 0;
	private $action;
	private $result = self::UNKNOWN;
	private $ref1 = 0;
	private $ref2 = 0;
	private $message = null;
	
	private $recordDate;
	
	
	//Constants
	//Action
	const CLIENT_CREATED = 1;
	const CLIENT_UPDATED = 2;
	const CLIENT_PASSWORD_UPDATED = 3;
	const CLIENT_ACTIVATION_TOGGLED = 4;
	const CLIENT_REMOVED = 5;
	
	const USER_LOGGED_IN = 10;
	const USER_LOGGED_OUT = 11;
	const USER_SET_LANGUAGE = 12;
	
	const ADMIN_CREATED = 20;
	const ADMIN_UPDATED = 21;
	const ADMIN_REMOVED = 22;
	
	const BROADCASTER_CREATED = 30;
	const BROADCASTER_UPDATED = 31;
	const BROADCASTER_REMOVED = 32;
	
	const CAMPAIGN_CREATED = 33;
	const CAMPAIGN_UPDATED = 34;
	const CAMPAIGN_SCHEDULE_UPDATED = 35;
	const CAMPAIGN_FORMATS_UPDATED = 36;
	const CAMPAIGN_REMOVED = 37;
	
	const AD_ADDED = 40;
	const AD_UPDATED = 41;
	const AD_SCHEDULE_UPDATED = 42;
	const AD_REMOVED = 43;
	const AD_REVIEWED = 44;
	
	const CREATIVE_UPLOADED = 50;
	const CREATIVE_REMOVED = 51;
	const CREATIVE_TRANSCODED = 52;
	
	const DEFAULT_AD_UPLOADED = 60;
	const DEFAULT_AD_UPDATED = 61;
	const DEFAULT_AD_REMOVED = 62;
	
	const GLOBAL_UPDATED = 70;
	
	const SUPPORT_CREATED = 80;
	const SUPPORT_UPDATED = 81;
	const SUPPORT_REMOVED = 82;
	
	const SCREEN_CREATED = 90;
	const SCREEN_UPDATED = 91;
	const SCREEN_REMOVED = 92;
	
	const LEGAL_APPROVED = 100;
	
	const BROADCASTER_GROUP_CREATED = 110;
	const BROADCASTER_GROUP_UPDATED = 111;
	const BROADCASTER_GROUP_REMOVED = 112;
	
	//Result
	const OK = 1;
	const UNAUTHORIZED = 2;
	const REFUSED = 3;
	const UNKNOWN = 4;
	const FATAL_ERROR = 5;
	
	
	/**
	 * Return a Record Object for a specific record
	 * @param $recordID
	 * @return bool|Record
	 */
	public static function getInstance($recordID)
	{
		//Sanitize the record ID
		$recordID = \Library\Sanitize::int($recordID);
		
		//Do not instantiate if equal zero
		if($recordID == 0)
			return false;
		
		$recordModel = new RecordModel();
		
		//Verify if record exist
		if(!$recordModel->recordExist($recordID))
			return false;
		
		//Instantiate the record
		$record = new self($recordID);
		$record->setRecord($recordID);
		
		return $record;
	}
	
	
	/**
	 * Record a Record Object for a new record
	 * @param $recordType
	 * @param int $userID
	 * @return Record
	 */
	public static function createRecord($recordType, $userID = 0)
	{
		if($userID == 0 && \Library\User::loggedIn())
			$userID = \Library\User::id();
		
		$record = new self();
		$record->fillRecord($recordType, $userID);
		
		return $record;
	}
	
	
	/**
	 * Record constructor.
	 */
	protected function __construct()
	{
		$this->recordModel = new RecordModel();
	}
	
	
	/**
	 * @param $recordType
	 * @param $userID
	 */
	protected function fillRecord($recordType, $userID)
	{
		$this->newRecord = true;
		
		$this->userID = $userID;
		$this->action = $recordType;
		
		$this->recordModel = new RecordModel();
	}
	
	/**
	 * @param $recordID
	 */
	protected function setRecord($recordID)
	{
		$this->newRecord = false;
		$this->saved = true;
		
		$this->recordID = $recordID;
		$this->recordModel->setRecord($recordID);
		
		$recordInfos = $this->recordModel->getInfos();
		
		$this->recordDate = $recordInfos["date"];
		$this->action = $recordInfos["action"];
		$this->result = $recordInfos["result"];
		$this->userID = $recordInfos["userID"];
		$this->ref1 = $recordInfos["ref1"];
		$this->ref2 = $recordInfos["ref2"];
		$this->message = $recordInfos["message"];
	}
	
	
	/////////////
	//For new records
	/////////////
	
	/**
	 * @param $result
	 * @return $this|void
	 */
	public function setResult($result)
	{
		if(!$this->newRecord || $this->saved)
			return;
		
		$this->result = $result;
		
		return $this;
	}
	
	/**
	 * @param $userID
	 * @return $this|void
	 */
	public function setUserID($userID)
	{
		if(!$this->newRecord || $this->saved)
			return;
		
		$this->userID = $userID;
		
		return $this;
	}
	
	/**
	 * @param $ref
	 * @return $this|void
	 */
	public function setRef1(int $ref)
	{
		if(!$this->newRecord || $this->saved)
			return;
		
		$this->ref1 = $ref;
	
		return $this;
	}
	
	/**
	 * @param $ref
	 * @return $this|void
	 */
	public function setRef2(int $ref)
	{
		if(!$this->newRecord || $this->saved)
			return;
		
		$this->ref2 = $ref;
		
		return $this;
	}
	
	
	public function setMessage(string $message)
	{
		if(!$this->newRecord || $this->saved)
			return;
		
		$this->message = $message;
		
		return $this;
	}
	
	/**
	 * Save a new record in the database
	 */
	public function save()
	{
		//Save only new records
		if(!$this->newRecord || $this->saved)
			return;
		
		$this->recordModel->saveRecord($this->userID, $this->action, $this->result, $this->ref1, $this->ref2, $this->message);
		
		$this->saved = true;
	}
	
	/**
	 * Called when the class is destroyed saved any unsaved record with a FATAL_ERROR status
	 */
	public function __destructor()
	{
		//Try to save it
		//Save only new records
		if(!$this->newRecord || $this->saved)
			return;
		
		if($this->getResult() == self::UNKNOWN)
			$this->setResult(self::FATAL_ERROR);
		
		$this->save();
	}
	
	
	
	
	
	/////////////
	//For existing records
	/////////////
	
	public function getUserID()
	{
		return $this->userID;
	}
	
	public function getAction()
	{
		return $this->action;
	}
	
	public function getResult()
	{
		return $this->result;
	}
	
	public function getResultText()
	{
		switch($this->result)
		{
			case self::OK:
				return "Ok";
			break;
			case self::UNAUTHORIZED:
				return "Unauthorized";
			break;
			case self::REFUSED:
				return "Refused";
			break;
			case self::UNKNOWN:
				return "Unknown";
			break;
			case self::FATAL_ERROR:
				return "Fatal error";
			break;
		}
	}
	
	public function getDate()
	{
		return $this->recordDate;
	}
	
	public function getRef1()
	{
		return $this->ref1;
	}
	
	public function getRef2()
	{
		return $this->ref2;
	}
	
	public function getMessage()
	{
		return $this->message;
	}
	
	
}
