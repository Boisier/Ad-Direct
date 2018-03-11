<?php

namespace Models;

class ScreenModel
{
	private $ddb, $currentScreen;
	
	/**
	 * Init model and set current screen if needed
	 * @private
	 * @param integer [$screenID      = 0] (optionnal) Screen ID to set as current
	 */
	public function __construct($screenID = 0)
	{
		$this->ddb = \Library\DBA::get();
		
		$this->setScreen($screenID);
	}
	
	/**
	 * Set the given screen ID as current
	 * @param  integer $screenID Screen ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setScreen($screenID)
	{
		if($screenID == 0)
			return false;
		
		if(!$this->screenExist($screenID))
			return false;
		
		$this->currentScreen = $screenID;
		
		return true;
	}
	
	/**
	 * Tell if a screen exist or not by its ID
	 * @param  integer $screenID screen ID
	 * @return boolean true if the screen exist, false otherwise
	 */
	public function screenExist($screenID)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM support_screens WHERE screen_id = :screenID");
		$stmt->execute([":screenID" => $screenID]);
		$nbrScreens = $stmt->fetchColumn();
		
		if($nbrScreens == 1)
			return true;
		
		return false;
	}
	
	/**
	 * @param int $supportID
	 * @param string $screenName
	 * @param int $screenWidth
	 * @param int $screenHeight
	 * @return int
	 */
	public function add($supportID, $screenName, $screenWidth, $screenHeight)
	{
		$stmt = $this->ddb->prepare("INSERT INTO support_screens(support_id, screen_name, screen_width, screen_height) VALUES(:support, :name, :width, :height)");
		$stmt->execute([":support" => $supportID,
			":name" => $screenName,
			":width" => $screenWidth,
			":height" => $screenHeight]);
		
		return $this->ddb->lastInsertId();
	}
	
	/**
	 * @param $screenID
	 * @return mixed
	 */
	public function getInfos()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				screen_id as ID,
				support_id as supportID,
				screen_name as name,
				screen_width as width,
				screen_height as height
			FROM
				support_screens
			WHERE
				screen_id = :screenID
		");
		$stmt->execute([":screenID" => $this->currentScreen]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * @param int $campaignID
	 * @return array
	 */
	public function getCampaignScreenInfos($campaignID)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				campaign_id as campaignID,
				screen_id as screenID,
				media_type as mediaType,
				size_limit as maxWeight
			FROM
				campaign_screen_medias
			WHERE
				campaign_id = :campaignID AND
				screen_id = :screenID;
		");
		$stmt->execute([":campaignID" => $campaignID,
			":screenID" => $this->currentScreen]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * @param int $screenID
	 * @param int $screenName
	 * @param int $screenWidth
	 * @param int $screenHeight
	 */
	public function update($screenID, $screenName, $screenWidth, $screenHeight)
	{
		$stmt = $this->ddb->prepare("UPDATE support_screens SET screen_name = :name, screen_width = :width, screen_height = :height WHERE screen_id = :screenID");
		$stmt->execute([":screenID" => $screenID,
			":name" => $screenName,
			":width" => $screenWidth,
			":height" => $screenHeight]);
	}
	
	/**
	 * Remove the screen
	 */
	public function delete()
	{
		$stmt = $this->ddb->prepare("DELETE FROM support_screens WHERE screen_id = :screenID");
		$stmt->execute([":screenID" => $this->currentScreen]);
	}
}