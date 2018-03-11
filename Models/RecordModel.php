<?php

namespace Models;

class RecordModel
{
	private $ddb, $currentRecord;
	
	
	
	/**
	 * Init model and set current broadcaster if needed
	 * @private
	 * @param integer [$broadcasterID      = 0] (optional) Broadcaster ID to set as current
	 */
	public function __construct($recordID = 0)
	{
		$this->ddb = \Library\DBA::get();
		
		$this->setRecord($recordID);
	}
	
	
	
	
	
	/**
	 * Set the given broadcasrer ID as current
	 * @param  integer $broadcasterID Broadcaster ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setRecord($recordID)
	{
		if($recordID == 0)
			return false;
		
		if(!$this->recordExist($recordID))
			return false;
		
		$this->currentRecord = $recordID;
		
		return true;
	}
	
	
	
	
	
	/**
	 * Tell if a broadcaster exist or not by its ID
	 * @param  integer $broadcasterID broadcaster ID
	 * @return boolean true if the broadcaster exist, false otherwise
	 */
	public function recordExist($recordID)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM user_tracking WHERE record_id = :recordID");
		$stmt->execute([":recordID" => $recordID]);
		$nbrRecords = $stmt->fetchColumn();
		
		if($nbrRecords == 1)
			return true;
		else
			return false;
	}

	
	
	//Getters
	public function getInfos()
	{
		$stmt = $this->ddb->prepare("
			SELECT 
				record_id as ID,
				record_date as date,
				record_action as action,
				record_result as result,
				user_id as userID,
				record_ref_1 as ref1,
				record_ref_2 as ref2,
				record_message as message
			FROM 
				user_tracking 
			WHERE 
				record_id = :recordID
		");
		$stmt->execute([":recordID" => $this->currentRecord]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	
	
	//Setters
	public function saveRecord($userID, $action, $result, $ref1, $ref2, $message)
	{
		$stmt = $this->ddb->prepare("
			INSERT INTO
				user_tracking(
					record_date,
					record_action,
					record_result,
					user_id,
					record_ref_1,
					record_ref_2,
					record_message
				)
			VALUES(
				:date,
				:action,
				:result,
				:userID,
				:ref1,
				:ref2,
				:message
			)
		");
		$stmt->execute([":date" => time(),
					   	":action" => $action,
					    ":result" => $result,
						":userID" => $userID,
					    ":ref1" => $ref1,
					    ":ref2" => $ref2,
						":message" => $message]);
	}
	
}