<?php
namespace Models;

class DefaultadModel
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
	
	
	
	
	
	public function screens($supportID)
	{
		$stmt = $this->ddb->prepare("
			SELECT 
				screen_id as ID, 
				screen_name as name, 
				screen_width as width, 
				screen_height as height,
                (SELECT 
					media_name
                 FROM
                 	media_types
                 WHERE
                 	media_types.media_id = 1
                 ) as mediaTypeName
			FROM 
				support_screens 
			WHERE 
				support_id = :supportID
			ORDER BY 
				screen_name, 
				support_screens.screen_id");
		
		$stmt->execute([":supportID" => $supportID]);
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	
	
	
	
	public function screenSpecs($screenID)
	{
		$stmt = $this->ddb->prepare("
			SELECT 
				screen_id as ID, 
				screen_name as name, 
				screen_width as width, 
				screen_height as height,
                (SELECT 
					media_name
                 FROM
                 	media_types
                 WHERE
                 	media_types.media_id = 1
                 ) as mediaTypeName
			FROM 
				support_screens 
			WHERE 
				screen_id = :screenID
			ORDER BY 
				screen_name, 
				support_screens.screen_id");
		
		$stmt->execute([":screenID" => $screenID]);
		
		$screenInfos = $stmt->fetch(\PDO::FETCH_ASSOC);
		
		$mediaTypeModel = new MediaTypeModel();
		$mimeTypes = $mediaTypeModel->getMimes(1);
		
		$specs = [      "sizeLimit" => \Library\Params::get("DEFAULT_AD_MAX_SIZE"),
				  "displayDuration" => 0,
				   "supportedMimes" => $mimeTypes,
				        "mediaType" => 1,
				            "width" => $screenInfos['width'],
				           "height" => $screenInfos['height']];
		
		return $specs;
	}
	
	
	
	
	
	public function exist($screenID)
	{
		$stmt = $this->ddb->prepare("
		SELECT COUNT(*)	FROM broadcaster_default_ads WHERE broadcaster_id = :broadcasterID AND screen_id = :screenID ");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster,
						":screenID" => $screenID]);
		
		if($stmt->fetchColumn() == 0)
			return false;
		
		return true;
	}
	
	
	
	
	public function getCreative($screenID)
	{
		if(!$this->exist($screenID))
			return false;
		
		$stmt = $this->ddb->prepare("
		SELECT 
			default_ad_id as ID,
			broadcaster_id as broadcasterID,
			screen_id as screenID,
			creative_extension as extension,
			creative_upload_date as uploadDate,
			user_id as userID
		FROM
			broadcaster_default_ads
		WHERE
			broadcaster_id = :broadcasterID AND
			screen_id = :screenID
		");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster,
						":screenID" => $screenID]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	public function path($screenID, $rooted = false)
	{
		$creativeInfos = $this->getCreative($screenID);
		
		if($creativeInfos == false)
			return false;
		
		$path = "defaultAds/{$creativeInfos['broadcasterID']}/{$creativeInfos['ID']}.{$creativeInfos['extension']}";
		
		if($rooted)
			$path = "/$path";
		
		return $path;
	}
	
	
	
	
	
	public function create($screenID, $creativeName, $creativeExtension, $file)
	{
		if($this->exist($screenID))
			return false;
		
		$stmt = $this->ddb->prepare("
		INSERT INTO
			broadcaster_default_ads(
				broadcaster_id,
				screen_id,
				creative_extension,
				creative_upload_date,
				user_id)
		VALUES(
			:broadcasterID,
			:screenID,
			:extension,
			:uploadDate,
			:userID
		)");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster,
					    ":screenID" => $screenID,
					    ":extension" => $creativeExtension,
					    ":uploadDate" => time(),
					    ":userID" => \Library\User::id()]);
		
		$path = $this->path($screenID);
		
		move_uploaded_file($file, $path);
	}
	
	
	
	
	public function deleteAll()
	{
		$stmt = $this->ddb->prepare("SELECT screen_id FROM broadcaster_default_ads WHERE broadcaster_id = :broadcasterID");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster]);
		
		while($screenID = $stmt->fetchColumn())
		{
			$this->delete($screenID);
		}
	}
	
	
	
	
	public function delete($screenID)
	{
		//Remove file
		$path = $this->path($screenID, false);
		unlink($path);
		
		//Remove entry in ddb
		$stmt = $this->ddb->prepare("
		DELETE FROM 
			broadcaster_default_ads 
		WHERE 
			broadcaster_id = :broadcasterID AND
			screen_id = :screenID
		");
		$stmt->execute([":broadcasterID" => $this->currentBroadcaster,
					    ":screenID" => $screenID]);
	}	
}