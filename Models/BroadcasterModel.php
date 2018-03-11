<?php
namespace Models;

use Objects\Broadcaster;
use Objects\Campaign;

class broadcasterModel
{
	private $ddb;
	private $currentBroadcaster = NULL;
	
	
	/**
	 * Init model and set current broadcaster if needed
	 * @private
	 * @param integer [$broadcasterID      = 0] (optionnal) Broadcaster ID to set as current
	 */
	public function __construct($broadcasterID = 0)
	{
		$this->ddb = \Library\DBA::get();
		
		$this->setBroadcaster($broadcasterID);
	}
	
	
	/**
	 * Set the given broadcasrer ID as current
	 * @param  integer $broadcasterID Broadcaster ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setBroadcaster($broadcasterID)
	{
		if($broadcasterID == 0)
			return false;
		
		if(!$this->broadcasterExist($broadcasterID))
			return false;
		
		$this->currentBroadcaster = $broadcasterID;
		
		return true;
	}
	
	
	/**
	 * Tell if a broadcaster exist or not by its ID
	 * @param  integer $broadcasterID broadcaster ID
	 * @return boolean true if the broadcaster exist, false otherwise
	 */
	public function broadcasterExist($broadcasterID)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM broadcasters WHERE broadcaster_id = :broadcasterID");
		$stmt->execute([":broadcasterID" => $broadcasterID]);
		$nbrBroadcasters = $stmt->fetchColumn();
		
		if($nbrBroadcasters == 1)
			return true;
		else
			return false;
	}
	
	
	
	
	
	/**
	 * Tell if a broadcaster exist or not by its Name
	 * @param  integer $broadcasterName broadcaster Name
	 * @return boolean true if the broadcaster exist, false otherwise
	 */
	public function broadcasterExistName($broadcasterName)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM broadcasters WHERE broadcaster_name = :broadcasterName");
		$stmt->execute([":broadcasterName" => $broadcasterName]);
		$nbrBroadcasters = $stmt->fetchColumn();
		
		if($nbrBroadcasters == 1)
			return true;
		else
			return false;
	}
	
	
	/**
	 * @param string $broadcasterName
	 * @param int $groupID
	 * @return int
	 */
	public function create($broadcasterName, $groupID)
	{
		$stmt = $this->ddb->prepare("INSERT INTO broadcasters(broadcaster_name, broadcaster_create_time, broadcaster_creator, group_id) VALUES(:name, :time, :creator, :groupID)");
		$stmt->execute([":name" => $broadcasterName,
					    ":time" => time(),
					    ":creator" => \Library\User::id(),
					    ":groupID" => $groupID]);
		
		return $this->ddb->lastInsertId();
	}
	
	
	/**
	 * @return Broadcaster[]
	 */
	public function getAll()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				broadcaster_id
			FROM
				broadcasters
			ORDER BY
				broadcaster_name");
		$stmt->execute();
		
		$broadcasters = [];
		
		while($broadcasterID = $stmt->fetchColumn())
		{
			array_push($broadcasters, \Objects\Broadcaster::getInstance($broadcasterID));
		}
		
		return $broadcasters;
	}
	
	/**
	 * @return string[]
	 */
	public function getInfos()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				broadcaster_id as ID,
				broadcaster_name as name,
				broadcaster_create_time as createTime,
				broadcaster_creator as creatorID,
				group_id as groupID
			FROM
				broadcasters
			WHERE
				broadcaster_id = :broadcasterID
		");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * @return int
	 */
	public function getPendingNbrAds()
	{
		$stmt= $this->ddb->prepare("
			SELECT
				COUNT(*)
			FROM
				ad_reviews
				JOIN ads
					ON ad_reviews.ad_id = ads.ad_id
				JOIN campaigns
					ON ads.campaign_id = campaigns.campaign_id
			WHERE
				ad_reviews.review_status = 1 AND
				campaigns.broadcaster_id = :broadcasterID
			");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster]);
		
		return $stmt->fetchColumn();
	}
	
	
	/**
	 * Return clients ID of the broadcaster
	 * @return int[]
	 */
	public function getClients()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				user_id
			FROM
				users
			WHERE
				broadcaster_id = :broadcasterID
			ORDER BY
				user_name
		");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	/**
	 * @return int[]
	 */
	public function getCampaigns()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				campaign_id
			FROM
				campaigns
			WHERE
				broadcaster_id = :broadcasterID
			ORDER BY
				campaign_name
		");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	/**
	 * @param string $broadcasterName
	 * @param int $groupID
	 */
	public function update($broadcasterName, $groupID)
	{
		$stmt = $this->ddb->prepare("
		UPDATE 
			broadcasters 
		SET 
			broadcaster_name = :name,
			group_id = :groupID
		WHERE 
			broadcaster_id = :broadcasterID
		");
		$stmt->execute([":name" => $broadcasterName,
					    ":broadcasterID" => $this->currentBroadcaster,
					    ":groupID" => $groupID]);
	}
	
	/**
	 * Remove the broadcaster from the database
	 */
	public function delete()
	{
		$stmt = $this->ddb->prepare("DELETE FROM broadcasters WHERE broadcaster_id = :broadcasterID");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster]);
	}
}
