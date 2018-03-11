<?php

namespace Models;

class AdModel
{
	private $ddb;
	private $currentAd = NULL;
	
	
	
	
	
	
	/**
	 * AdModel constructor.
	 * Init model and set current ad if needed
	 * @param int $adID
	 */
	public function __construct($adID = 0)
	{
		$this->ddb = \Library\DBA::get();
		
		$this->setAd($adID);
	}
	
	
	
	
	
	/**
	 * Set the given ad ID as current
	 * @param  integer $adID Ad ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setAd($adID)
	{
		if($adID == 0)
			return false;
		
		if(!$this->adExist($adID))
			return false;
		
		$this->currentAd = $adID;
		
		return true;
	}
	
	
	
	
	
	/**
	 * Tell if a broadcaster exist or not by its ID
	 * @param integer $adID ad ID
	 * @return boolean true if the broadcaster exist, false otherwise
	 */
	public function adExist($adID)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM ads WHERE ad_id = :adID");
		$stmt->execute([":adID" => $adID]);
		$nbrAds = $stmt->fetchColumn();
		
		if($nbrAds == 1)
			return true;
		else
			return false;
	}
	
	
	/**
	 * @param $campaignID
	 * @param $supportID
	 * @param $startTime
	 * @param $endTime
	 * @return int
	 */
	public function create($campaignID, $supportID, $startTime, $endTime)
	{
		$stmt = $this->ddb->prepare("INSERT INTO ads(campaign_id, support_id, user_id, ad_start_time, ad_end_time, ad_order, ad_uid) VALUES(:campaignID, :supportID, :userID, :startTime, :endTime, :order, UUID())");
		$stmt->execute([":campaignID" => $campaignID,
					    ":supportID" => $supportID,
						":userID" => \Library\User::id(),
					    ":startTime" => $startTime,
					    ":endTime" => $endTime,
					    ":order" => 999]);
		
		$adID = $this->ddb->lastInsertId();
		
		$this->setAd($adID);
			
		return $adID;
	}
	
	
	/**
	 * @return array
	 */
	public function getInfos()
	{
		$stmt = $this->ddb->prepare("
		SELECT 
			ad_id as ID, 
			campaigns.campaign_id as campaignID,
			supports.support_id as supportID,
			users.user_id as userID,
			ad_name as name, 
			ad_start_time as startTime, 
			ad_end_time as endTime, 
			ad_order as adOrder,
			ad_uid as UID
		FROM 
			ads 
			JOIN
				campaigns
				ON campaigns.campaign_id = ads.campaign_id
			JOIN
				users
				ON users.user_id = ads.user_id
			JOIN
				supports
				ON supports.support_id = ads.support_id
		WHERE 
			ad_id = :adID
		");
		$stmt->execute([":adID" => $this->currentAd]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	
	
	
	/**
	 * @return int[]
	 */
	public function getScreens()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				support_screens.screen_id
			FROM
				support_screens
				JOIN
					supports ON
					support_screens.support_id = supports.support_id
				JOIN
					campaigns ON
					supports.support_id = campaigns.support_id
				JOIN
					ads ON
					campaigns.campaign_id = ads.campaign_id
			WHERE
				ads.ad_id = :adID
		");
		$stmt->execute([":adID" => $this->currentAd]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	
	
	/**
	 * @return int
	 */
	public function getReviewStatus()
	{
		//First, check if there is a review
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM ad_reviews WHERE ad_id = :adID");
		$stmt->execute([":adID" => $this->currentAd]);
		
		if($stmt->fetchColumn() == 0)
		{
			return \Controllers\ReviewController::AD_INCOMPLETE;
		}
		
		$stmt = $this->ddb->prepare("
		SELECT
			review_status
		FROM
			ad_reviews
		WHERE
			ad_reviews.ad_id = :adID
		");
		$stmt->execute([":adID" => $this->currentAd]);
		
		return $stmt->fetchColumn();
	}
	
	
	/**
	 * @return int[]
	 */
	public function getCreatives()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creative_id
			FROM
				creatives
			WHERE
				creatives.ad_id = :adID
			ORDER BY
				creatives.screen_id
		");
		$stmt->execute([":adID" => $this->currentAd]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	
	/**
	 * @return int
	 */
	public function getPrintsTotal()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				COUNT(*)
			FROM
				campaign_print
			WHERE
				ad_id = :adID
		");
		$stmt->execute([":adID" => $this->currentAd]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	
	/**
	 * Update the current ad
	 * @param $startDate
	 * @param $endDate
	 */
	public function update($startDate, $endDate)
	{
		$stmt = $this->ddb->prepare("UPDATE ads SET ad_start_time = :startTime, ad_end_time = :endTime WHERE ad_id = :adID");
		$stmt->execute([":startTime" => $startDate,
			":endTime" => $endDate,
			":adID" => $this->currentAd]);
	}
	
	
	
	
	
	
	/**
	 * Remove the current ad
	 */
	public function delete()
	{
		$stmt = $this->ddb->prepare("DELETE FROM ads WHERE ad_id = :adID");
		$stmt->execute([":adID" => $this->currentAd]);
	}
}