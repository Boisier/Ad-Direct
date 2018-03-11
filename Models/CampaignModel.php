<?php

namespace Models;

class CampaignModel
{
	private $ddb, $currentCampaign;
	
	/**
	 * Init model and set current broadcaster if needed
	 * @private
	 * @param integer [$broadcasterID      = 0] (optional) Broadcaster ID to set as current
	 */
	public function __construct($campaignID = 0)
	{
		$this->ddb = \Library\DBA::get();
		
		$this->setCampaign($campaignID);
	}
	
	/**
	 * Set the given broadcasrer ID as current
	 * @param  integer $broadcasterID Broadcaster ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setCampaign($campaignID)
	{
		if($campaignID == 0)
			return false;
		
		if(!$this->campaignExist($campaignID))
			return false;
		
		$this->currentCampaign = $campaignID;
		
		return true;
	}
	
	/**
	 * Tell if a broadcaster exist or not by its ID
	 * @param  integer $broadcasterID broadcaster ID
	 * @return boolean true if the broadcaster exist, false otherwise
	 */
	public function campaignExist($campaignID)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM campaigns WHERE campaign_id = :campaignID");
		$stmt->execute([":campaignID" => $campaignID]);
		$nbrCampaigns = $stmt->fetchColumn();
		
		if($nbrCampaigns == 1)
			return true;
		else
			return false;
	}
	
	/**
	 * @return array
	 */
	public function getInfos()
	{
		$stmt = $this->ddb->prepare("
		SELECT 
			broadcaster_id as broadcasterID, 
			support_id as supportID, 
			campaign_id as ID, 
			campaign_creator as creator, 
			campaign_name as name, 
			campaign_start_date as startDate, 
			campaign_end_date as endDate, 
			campaign_create_date as createDate, 
			display_duration as displayDuration,
			ad_limit as adLimit,
			campaign_uid as UID
		FROM 
			campaigns 
		WHERE 
			campaign_id = :campaignID
		");
		$stmt->execute([":campaignID" => $this->currentCampaign]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * @param int $broadcasterID
	 * @param int $supportID
	 * @param int $mediaType
	 * @param string $campaignName
	 * @param int $campaignStartDate
	 * @param int $campaignEndDate
	 * @param int $adLimit
	 * @param int $displayDuration
	 * @return int The new campaignID
	 */
	public function create($broadcasterID, $supportID, $campaignName, $campaignStartDate, $campaignEndDate, $adLimit, $displayDuration)
	{
		//Insert the new campaigns
		$stmt = $this->ddb->prepare("
		INSERT INTO 
			campaigns(
				broadcaster_id, 
				campaign_creator, 
				support_id, 
				campaign_name, 
				campaign_start_date, 
				campaign_end_date, 
				campaign_create_date, 
				display_duration, 
				ad_limit, 
				campaign_uid)
		VALUES(
			:broadcasterID, 
			:creator, 
			:supportID, 
			:name, 
			:startDate, 
			:endDate, 
			:createDate, 
			:displayDuration,
			:adLimit, 
			UUID())
		");
		
		$stmt->execute([":broadcasterID" => $broadcasterID,
					    ":creator" => \Library\User::id(),
					    ":supportID" => $supportID, 
					    ":name" => $campaignName,
					    ":startDate" => $campaignStartDate,
					    ":endDate" => $campaignEndDate,
					    ":createDate" => time(), 
					    ":displayDuration" => $displayDuration, 
					    ":adLimit" => $adLimit]);
		
		$campaignID = $this->ddb->lastInsertId();
		
		//Set the new campaign as current
		$this->setCampaign($campaignID);
		
		//Insert the campaign screens
		$this->createScreens($supportID);
		
		//Return the campaign ID
		return $campaignID;
	}
	
	/**
	 * @param int $supportID
	 * @param string $campaignName
	 * @param int $campaignStartDate
	 * @param int $campaignEndDate
	 * @param int $adLimit
	 * @param int $displayDuration
	 */
	public function update($supportID, $campaignName, $campaignStartDate, $campaignEndDate, $adLimit, $displayDuration)
	{
		$campaignInfos = $this->getInfos();
		
		//Update the campaign
		$stmt = $this->ddb->prepare("
		UPDATE 
			campaigns 
		SET 
			support_id = :supportID,
			campaign_name = :name, 
			campaign_start_date = :startDate, 
			campaign_end_date = :endDate,
			display_duration = :displayDuration,
			ad_limit = :adLimit
		WHERE 
			campaign_id = :campaignID");
		
		$stmt->execute([":supportID" => $supportID,
						":name" => $campaignName,
					    ":startDate" => $campaignStartDate,
					    ":endDate" => $campaignEndDate,
					    ":displayDuration" => $displayDuration,
					    ":adLimit" => $adLimit,
					    ":campaignID" => $this->currentCampaign]);
		
		//Update the campaign screens if needed
		if($campaignInfos['supportID'] == $supportID)
		{
			return;
		}
		
		$this->createScreens($supportID);
	}
	
	/**
	 * @param int $supportID
	 */
	private function createScreens($supportID)
	{
		//Remove old screens, then insert the new ones
		$stmt = $this->ddb->prepare("
			DELETE FROM 
				campaign_screen_medias 
			WHERE 
				campaign_id = :campaignID
		");
		$stmt->execute([":campaignID" => $this->currentCampaign]);
		
		$screensQuery = $this->ddb->prepare("
			SELECT 
				screen_id 
			FROM 
				support_screens 
			WHERE 
				support_id = :supportID
		");
		$screensQuery->execute([":supportID" => $supportID]);
		
		$insertScreenMedia = $this->ddb->prepare("
			INSERT INTO 
				campaign_screen_medias(
					campaign_id, 
					screen_id, 
					media_type, 
					size_limit
				) 
				VALUES(
					:campaignID, 
					:screenID, 
					:mediaID, 
					:sizeLimit
				)");
		
		while($screenID = $screensQuery->fetchColumn())
		{
			$insertScreenMedia->execute([":campaignID" => $this->currentCampaign,
										 ":screenID" => $screenID,
										 ":mediaID" => 0,
										 ":sizeLimit" => \Library\Params::get("FILE_SIZE_LIMIT")]);
		}
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
			WHERE
				campaigns.campaign_id = :campaignID
		");
		$stmt->execute([":campaignID" => $this->currentCampaign]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	/**
	 * Update a campaign screen
	 * @param int $screenID
	 * @param int $mediaType
	 * @param int $sizeLimit
	 */
	public function updateScreen($screenID, $mediaType, $sizeLimit)
	{	
		$stmt = $this->ddb->prepare("
		UPDATE
			campaign_screen_medias
		SET
			media_type = :mediaType,
			size_limit = :sizeLimit
		WHERE
			campaign_id = :campaignID AND
			screen_id = :screenID
		");
		
		$stmt->execute([":mediaType" => $mediaType,
					    ":sizeLimit" => $sizeLimit,
					    ":screenID" => $screenID,
					    ":campaignID" => $this->currentCampaign]);
	}
	
	/**
	 * @return int[]
	 */
	public function getAds()
	{
		$stmt = $this->ddb->prepare("
			SELECT 
				ad_id 
			FROM 
				ads 
			WHERE 
				campaign_id = :campaignID
		");
		$stmt->execute([":campaignID" => $this->currentCampaign]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}



	public function isDebugging()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				campaign_debug
			FROM
				campaigns
			WHERE
				campaign_id = :campaignID
		");
		$stmt->execute([":campaignID" => $this->currentCampaign]);
		
		return $stmt->fetchColumn();
	}
	
	/**
	 * @return int
	 */
	public function getPendingNbrAds()
	{
		$stmt = $this->ddb->prepare("
			SELECT COUNT(*)
			FROM
				ad_reviews
				JOIN
					ads
					ON ad_reviews.ad_id = ads.ad_id
			WHERE
				ad_reviews.review_status = 1 AND
				ads.campaign_id = :campaignID
		");
		$stmt->execute([":campaignID" => $this->currentCampaign]);
		
		return $stmt->fetchColumn();
	}
	
	/**
	 * Remove the campaign from the database
	 */
	public function delete()
	{
		$stmt = $this->ddb->prepare("DELETE FROM campaign_screen_medias WHERE campaign_id = :campaignID");
		$stmt->execute([":campaignID" => $this->currentCampaign]);
		
		$stmt = $this->ddb->prepare("DELETE FROM campaigns WHERE campaign_id = :campaignID");
		$stmt->execute([":campaignID" => $this->currentCampaign]);
	}
}