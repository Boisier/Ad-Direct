<?php

namespace Models;

class BroadcastergroupModel
{
	private $ddb;
	
	
	/**
	 * BroadcastergroupModel constructor.
	 */
	public function __construct()
	{
		$this->ddb = \Library\DBA::get();
	}
	
	
	
	
	
	/**
	 * Tell if a broadcaster group exist or not by its Name
	 * @param  integer $broadcasterGroupName
	 * @return boolean true if the broadcaster exist, false otherwise
	 */
	public function broadcasterGroupExistName($broadcasterGroupName)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				COUNT(*)
			FROM
				broadcaster_groups
			WHERE
				group_name = :groupName
		");
		$stmt->execute([":groupName" => $broadcasterGroupName]);
		$nbrGroups = $stmt->fetchColumn();
		
		if($nbrGroups == 1)
			return true;
		else
			return false;
	}
	
	
	/**
	 * Create a new broadcaster group
	 * @param $groupName
	 * @return mixed
	 */
	public function create($groupName)
	{
		$stmt = $this->ddb->prepare("
		INSERT INTO
			broadcaster_groups(
				group_name
			)
		VALUES(
			:groupName
		)");
		$stmt->execute([":groupName" => $groupName]);
		
		return $this->ddb->lastInsertId();
	}
	
	
	/**
	 * Retrieve all the broadcaster groups
	 * @return mixed
	 */
	public function getAll()
	{
		$stmt = $this->ddb->prepare("
		SELECT
			group_id as ID,
			group_name as name
		FROM 
			broadcaster_groups
		ORDER BY
			group_name
		");
		
		$stmt->execute();
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Retrieve information for a broadcaster group
	 * @param $groupID
	 * @return mixed
	 */
	public function getInfos($groupID)
	{
		$stmt = $this->ddb->prepare("
		SELECT
			group_id as ID,
			group_name as name
		FROM 
			broadcaster_groups
		WHERE
			group_id = :groupID
		");
		
		$stmt->execute([":groupID" => $groupID]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Update the name of the broadcaster group
	 * @param $groupID
	 * @param $groupName
	 */
	public function setName($groupID, $groupName)
	{
		$stmt = $this->ddb->prepare("
		UPDATE
			broadcaster_groups
		SET
			group_name = :name
		WHERE
			group_id = :groupID");
		$stmt->execute([":name" => $groupName,
			":groupID" => $groupID]);
	}
	
	
	/**
	 * Remove the given broadcaster group
	 * @param $groupID
	 */
	public function delete($groupID)
	{
		$stmt = $this->ddb->prepare("
			DELETE FROM
				broadcaster_groups
			WHERE
				group_id = :groupID
		");
		$stmt->execute([":groupID" => $groupID]);
	}
}
