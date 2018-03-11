<?php

namespace Models;

class SupportModel
{
	private $ddb;
	private $currentSupport = NULL;
	
	
	/**
	 * Init model and set current support if needed
	 * @param int $supportID
	 */
	public function __construct($supportID = 0)
	{
		$this->ddb = \Library\DBA::get();
		
		$this->setSupport($supportID);
	}
	
	
	
	
	
	/**
	 * Set the given support ID as current
	 * @param  int $supportID Support ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setSupport($supportID)
	{
		if($supportID == 0)
			return false;
		
		if(!$this->supportExist($supportID))
			return false;
		
		$this->currentSupport = $supportID;
		
		return true;
	}
	
	
	
	
	
	/**
	 * Tell if a support exist or not by its ID
	 * @param  integer $supportID support ID
	 * @return boolean true if the support exist, false otherwise
	 */
	public function supportExist($supportID)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM supports WHERE support_id = :supportID");
		$stmt->execute([":supportID" => $supportID]);
		$nbrSupports = $stmt->fetchColumn();
		
		if($nbrSupports == 1)
			return true;
		
		return false;
	}
	
	
	
	
	
	/**
	 * Tell if a support exist or not by its Name
	 * @param  integer $supportName Support Name
	 * @return boolean true if the support exist, false otherwise
	 */
	public function supportExistName($supportName)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM supports WHERE support_name = :supportName");
		$stmt->execute([":supportName" => $supportName]);
		$nbrSupports = $stmt->fetchColumn();
		
		if($nbrSupports == 1)
			return true;
		
		return false;
	}
	
	
	/**
	 * Return a list of all the supports
	 * @return mixed
	 */
	public function supportList()
	{
		$stmt = $this->ddb->prepare("SELECT support_id as ID, support_name as name, (SELECT COUNT(*) FROM support_screens WHERE support_screens.support_id = supports.support_id) as screens FROM supports ORDER BY support_name");
		$stmt->execute();
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * @return mixed
	 */
	public function getInfos()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				support_id as ID,
				support_name as name,
				(
					SELECT COUNT(*)
					FROM support_screens
					WHERE support_screens.support_id = supports.support_id
				) as screens
			FROM
				supports
			WHERE
				support_id = :supportID
		");
		$stmt->execute([":supportID" => $this->currentSupport]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * @param $supportName
	 * @return mixed
	 */
	public function create($supportName)
	{
		$stmt = $this->ddb->prepare("
			INSERT INTO
				supports(support_name)
			VALUES(:supportName)
		");
		$stmt->execute([":supportName" => $supportName]);
		
		return $this->ddb->lastInsertId();
	}
	
	
	/**
	 * @return mixed
	 */
	public function screens()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				screen_id as ID,
				screen_name as name,
				screen_width as width,
				screen_height as height
			FROM
				support_screens
			WHERE
				support_id = :supportID
			ORDER BY
				screen_name,
				screen_id
		");
		$stmt->execute([":supportID" => $this->currentSupport]);
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * @param $supportName
	 */
	public function update($supportName)
	{
		$stmt = $this->ddb->prepare("
			UPDATE
				supports
			SET
				support_name = :name
			WHERE
				support_id = :supportID
		");
		$stmt->execute([":name" => $supportName,
					    ":supportID" => $this->currentSupport]);
	}
	
	
	/**
	 * @return bool
	 */
	public function isUsed()
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM campaigns WHERE support_id = :supportID");
		$stmt->execute([":supportID" => $this->currentSupport]);
		
		if($stmt->fetchColumn() == 0)
			return false;
		
		return true;
	}
	
	
	/**
	 * Remove the current support
	 */
	public function delete()
	{
		$stmt = $this->ddb->prepare("DELETE FROM supports WHERE support_id = :supportID");
		$stmt->execute([":supportID" => $this->currentSupport]);
	}
	
	
	/**
	 * Return informations on the support of the given campaign
	 * @param $campaignID
	 * @return mixed
	 */
	public function campaignSupport($campaignID)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				supports.support_id as ID,
				supports.support_name as name,
				campaign_supports.media_type as mediaType,
				campaign_supports.ad_limit as adLimit,
				campaign_supports.size_limit as sizeLimit
			FROM
				supports
			JOIN
				campaign_supports
				ON campaign_supports.support_id = supports.support_id
			WHERE
				campaign_supports.campaign_id = :campaignID
		");
		$stmt->execute([":campaignID" => $campaignID]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
}

