<?php

namespace Models;

use Objects\Creative;

class CreativeModel
{
	private $ddb, $currentCreative;
	
	
	
	/**
	 * Init model and set current broadcaster if needed
	 * @private
	 * @param integer [$broadcasterID      = 0] (optionnal) Broadcaster ID to set as current
	 */
	public function __construct($creativeID = 0)
	{
		$this->ddb = \Library\DBA::get();
		
		$this->setCreative($creativeID);
	}
	
	
	
	
	
	/**
	 * Set the given broadcasrer ID as current
	 * @param  integer $broadcasterID Broadcaster ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setCreative($creativeID)
	{
		if($creativeID == 0)
			return false;
		
		if(!$this->exist($creativeID))
			return false;
		
		$this->currentCreative = $creativeID;
		
		return true;
	}
	
	
	
	
	
	/**
	 * Tell if a broadcaster exist or not by its ID
	 * @param  integer $broadcasterID broadcaster ID
	 * @return boolean true if the broadcaster exist, false otherwise
	 */
	public function exist($creativeID)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM creatives WHERE creative_id = :creativeID");
		$stmt->execute([":creativeID" => $creativeID]);
		$nbrCreatives = $stmt->fetchColumn();
		
		if($nbrCreatives == 1)
			return true;
		
		return false;
	}
	
	public function creativeExist($creativeID)
	{
		return $this->exist($creativeID);
	}
	
	
	
	
	
	
	
	
	
	/**
	 * Set the given broadcasrer ID as current
	 * @param  integer $broadcasterID Broadcaster ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setCreativeByComboID($adID, $screenID)
	{
		if($adID == 0 || $screenID == 0)
			return false;
		
		if(!$this->existComboID($adID, $screenID))
			return false;
		
		$stmt = $this->ddb->prepare("SELECT creative_id FROM creatives WHERE ad_id = :adID AND screen_id = :screenID");
		$stmt->execute([":adID" => $adID,
					    ":screenID" => $screenID]);
		
		$this->currentCreative = $stmt->fetchColumn();
		
		return true;
	}
	
	
	
	public function existComboID($adID, $screenID)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM creatives WHERE ad_id = :adID AND screen_id = :screenID");
		$stmt->execute([":adID" => $adID,
					    ":screenID" => $screenID]);
		
		$nbrCreatives = $stmt->fetchColumn();
		
		if($nbrCreatives == 0)
			return false;
		
		return true;
	}
	
	
	
	
	
	public function id()
	{
		return $this->currentCreative;
	}
	
	
	/**
	 * @param $adID
	 * @param $screenID
	 * @return bool|Creative
	 */
	public function getCreative($adID, $screenID)
	{
		if(!$this->existComboID($adID, $screenID))
			return false;
		
		$stmt = $this->ddb->prepare("
			SELECT
				creative_id as ID
			FROM
				creatives
			WHERE
				ad_id = :adID AND
				screen_id = :screenID
		");
		$stmt->execute([":adID" => $adID,
					    ":screenID" => $screenID]);
		
		return \Objects\Creative::getInstance($stmt->fetchColumn());
	}
	
	
	
	public function infos()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creative_id as ID,
				ad_id as adID,
				user_id as userID,
				screen_id as screenID,
				creative_name as name,
				creative_extension as extension,
				creative_upload_time as uploadTime
			FROM
				creatives
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	
	
	
	public function create($adID, $screenID, $creativeName, $creativeMediaType, $creativeExtension, $file)
	{
		//Prevent dupliocates
		if($this->existComboID($adID, $screenID))
			return false;
		
		//Insert the new creative in the database
		$stmt = $this->ddb->prepare("
			INSERT INTO
				creatives(
					ad_id,
					screen_id,
					media_type,
					user_id,
					creative_name,
					creative_extension,
					creative_upload_time,
					creative_status
				)
			VALUES(
				:adID,
				:screenID,
				:mediaType,
				:userID,
				:name,
				:extension,
				:uploadTime,
				:creativeStatus
			)
		");
		$stmt->execute([":adID" => $adID,
					    ":screenID" => $screenID,
					    ":mediaType" => $creativeMediaType,
					    ":userID" => \Library\User::id(),
					    ":name" => $creativeName,
					    ":extension" => $creativeExtension,
					    ":uploadTime" => time(),
					    ":creativeStatus" => \Controllers\CreativeController::CREATIVE_PROCESSING]);
		
		
		$this->setCreative($this->ddb->lastInsertId());
		
		$path = $this->path(false);
		
		//Check for folders
		mkdir(dirname($path), 0777, true);
		
		//Put the file in place
		move_uploaded_file($file, $path);
		
		chmod($path, 0777);
		
		return \Objects\Creative::getInstance($this->currentCreative);
	}
	
	
	
	
	
	
	
	public function path($rooted)
	{
		$stmt = $this->ddb->prepare("SELECT creatives.creative_extension as extension, creatives.screen_id as screenID, ads.ad_uid as adUID, campaigns.campaign_uid as campaignUID FROM creatives JOIN ads ON creatives.ad_id = ads.ad_id JOIN campaigns ON ads.campaign_id = campaigns.campaign_id WHERE creatives.creative_id = :creativeID");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		$pathInfos = $stmt->fetch(\PDO::FETCH_ASSOC);
		
		$path = "campaigns/{$pathInfos['campaignUID']}/{$pathInfos['adUID']}/{$pathInfos['screenID']}/{$this->currentCreative}.{$pathInfos['extension']}";
		
		if($rooted)
			$path = "/$path";
		
		return $path;
	}
	
	public function thumbPath($rooted)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creatives.creative_extension as extension
			FROM
				creatives
			WHERE
				creatives.creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		$creativeExtension = $stmt->fetchColumn();
		
		$path = "thumbs/{$this->currentCreative}.{$creativeExtension}";
		
		if($rooted)
			$path = "/$path";
		
		return $path;
	}
	
	

	
	
	public function delete()
	{
		//Remove entry in ddb
		$stmt = $this->ddb->prepare("DELETE FROM creatives WHERE creative_id = :creativeID");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		$this->currentCreative = NULL;
	}
	
	
	
	
	
	
	
	public function getMediaType()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				media_type
			FROM
				creatives
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	
	
	
	
	public function getScreenID()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				screen_id
			FROM
				creatives
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	
	
	
	
	public function getUploadTime()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creative_upload_time
			FROM
				creatives
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	
	
	
	
	public function getUploaderID()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				user_id
			FROM
				creatives
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	
	
	
	
	public function getAdID()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				ad_id
			FROM
				creatives
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	public function setStatus($status)
	{
		$stmt = $this->ddb->prepare("
			UPDATE
				creatives
			SET
				creative_status = :status
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":status" => $status,
					    ":creativeID" => $this->currentCreative]);
	}
	
	
	
	
	public function getStatus()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creative_status
			FROM
				creatives
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	public function getConversionStatus()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creative_conversion_status
			FROM
				creatives
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	
	
	
	public function getConvertedVideoPath($rooted)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creatives.screen_id as screenID,
				ads.ad_uid as adUID,
				campaigns.campaign_uid as campaignUID
			FROM
				creatives
				JOIN
					ads ON
					creatives.ad_id = ads.ad_id
				JOIN
					campaigns ON
					ads.campaign_id = campaigns.campaign_id
			WHERE
				creatives.creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		$pathInfos = $stmt->fetch(\PDO::FETCH_ASSOC);
		
		$path = "campaigns/{$pathInfos['campaignUID']}/{$pathInfos['adUID']}/{$pathInfos['screenID']}/{$this->currentCreative}.webm";
		
		if($rooted)
			$path = "/$path";
		
		return $path;
	}
	
	
	
	
	
	
	
	public function getOriginalVideoPath($rooted)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creatives.creative_original_extension as extension,
				creatives.screen_id as screenID,
				ads.ad_uid as adUID,
				campaigns.campaign_uid as campaignUID
			FROM
				creatives
				JOIN
					ads ON
					creatives.ad_id = ads.ad_id
				JOIN
					campaigns ON
					ads.campaign_id = campaigns.campaign_id
			WHERE
				creatives.creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		$pathInfos = $stmt->fetch(\PDO::FETCH_ASSOC);
		
		$path = "campaigns/{$pathInfos['campaignUID']}/{$pathInfos['adUID']}/{$pathInfos['screenID']}/{$this->currentCreative}.{$pathInfos['extension']}";
		
		if($rooted)
			$path = "/$path";
		
		return $path;
	}
	
	
	
	
	public function setNewExtension($newExtension)
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creative_extension
			FROM
				creatives
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":creativeID" => $this->currentCreative]);
		
		$currentExtension = $stmt->fetchColumn();
		
		$stmt = $this->ddb->prepare("
			UPDATE
				creatives
			SET
				creative_extension = :newExtension,
				creative_original_extension = :oldExtension
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":newExtension" => $newExtension,
					    ":oldExtension" => $currentExtension,
					    ":creativeID" => $this->currentCreative]);
	}
	
	
	public function setConversionStatus($percentage)
	{
		$stmt = $this->ddb->prepare("
			UPDATE
				creatives
			SET
				creative_conversion_status = :percentage
			WHERE
				creative_id = :creativeID
		");
		$stmt->execute([":percentage" => $percentage,
					    ":creativeID" => $this->currentCreative]);
	}
}
