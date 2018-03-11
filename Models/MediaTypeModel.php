<?php

namespace Models;

class mediaTypeModel
{
	private $ddb;
	private $currentMediaType = NULL;
	
	
	
	
	
	/**
	 * Init model and set current broadcaster if needed
	 * @private
	 * @param integer [$broadcasterID      = 0] (optionnal) Broadcaster ID to set as current
	 */
	public function __construct($mediaTypeID = 0)
	{
		$this->ddb = \Library\DBA::get();
		
		$this->setMediaType($mediaTypeID);
	}
	
	
	
	
	
	/**
	 * Set the given broadcasrer ID as current
	 * @param  integer $broadcasterID Broadcaster ID to set
	 * @return boolean true on success, false otherwise
	 */
	public function setMediaType($mediaTypeID)
	{
		if($mediaTypeID == 0)
			return false;
		
		if(!$this->mediaTypeExist($mediaTypeID))
			return false;
		
		$this->currentMediaType = $mediaTypeID;
		
		return true;
	}
	
	
	
	
	
	/**
	 * Tell if a broadcaster exist or not by its ID
	 * @param  integer $broadcasterID broadcaster ID
	 * @return boolean true if the broadcaster exist, false otherwise
	 */
	public function mediaTypeExist($mediaTypeID)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM media_types WHERE media_id = :mediaID");
		$stmt->execute([":mediaID" => $mediaTypeID]);
		$nbrMediaTypes = $stmt->fetchColumn();
		
		if($nbrMediaTypes == 1)
			return true;
		
		return false;
	}
	
	
	
	
	
	/**
	 * Tell if a broadcaster exist or not by its Name
	 * @param  integer $broadcasterName broadcaster Name
	 * @return boolean true if the broadcaster exist, false otherwise
	 */
	public function mediaTypeExistName($mediaTypeName)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM media_types WHERE media_name = :mediaName");
		$stmt->execute([":mediaName" => $mediaTypeName]);
		$nbrMediaTypes = $stmt->fetchColumn();
		
		if($nbrMediaTypes == 1)
			return true;
		
		return false;
	}
	
	
	public function getAll()
	{
		$stmt = $this->ddb->prepare("SELECT media_id as ID, media_name as name FROM media_types ORDER BY media_name");
		$stmt->execute();
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	
	public function getMimes($mediaID)
	{
		//mediaID = 0 means all
		if($mediaID == 0)
		{
			$stmt = $this->ddb->prepare("SELECT mime_type FROM mime_types");
			$stmt->execute();
		
			return $stmt->fetchAll(\PDO::FETCH_COLUMN);
		}
		
		$stmt = $this->ddb->prepare("SELECT mime_type FROM mime_types WHERE media_id = :mediaID");
		$stmt->execute([":mediaID" => $mediaID]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	
	public function name()
	{
		$stmt = $this->ddb->prepare("SELECT media_name FROM media_types WHERE media_id = :mediaID");
		$stmt->execute([":mediaID" => $this->currentMediaType]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	public function create($mediaName)
	{
		$stmt = $this->ddb->prepare("INSERT INTO media_types(media_name) VALUES(:name)");
		$stmt->execute([":name" => $mediaName]);
		
		return $this->ddb->lastInsertId();
	}
	
	public function addMime($mime)
	{
		$stmt = $this->ddb->prepare("INSERT INTO mime_types(mime_type, media_id) VALUES(:mime, :name) ON DUPLICATE KEY UPDATE mime_type = mime_type");
		$stmt->execute([":mime" => $mime,
					    ":name" => $this->currentMediaType]);
	}
	
	
	
	public function setName($mediaName)
	{
		$stmt = $this->ddb->prepare("UPDATE media_types SET media_name = :name WHERE media_id = :mediaID");
		$stmt->execute([":name" => $mediaName,
					    ":mediaID" => $this->currentMediaType]);
	}
	
	
	
	public function clearMimes()
	{
		$stmt = $this->ddb->prepare("DELETE FROM mime_types WHERE media_id = :mediaID");
		$stmt->execute([":mediaID" => $this->currentMediaType]);
	}
	
	
	
	public function isUsed()
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM campaign_screen_medias WHERE media_id = :mediaID");
		$stmt->execute([":mediaID" => $this->currentMediaType]);
		$nbrScreens = $stmt->fetchColumn();
		
		if($nbrScreens == 0)
			return false;
		
		return true;
	}
	
	
	
	public function delete()
	{
		$stmt = $this->ddb->prepare("DELETE FROM media_types WHERE media_id = :mediaID");
		$stmt->execute([":mediaID" => $this->currentMediaType]);
	}
	
	
	
	
	
	
	
	public function getMimeMediaType($mimeType)
	{
		//First check if we the mime/type exist.
		$stmt = $this->ddb->prepare("
			SELECT
				COUNT(*)
			FROM
				mime_types
			WHERE
				mime_type = :mimeType
		");
		$stmt->execute([":mimeType" => $mimeType]);
		
		//No match
		if($stmt->fetchColumn() == 0)
			return 0;
		
		$stmt = $this->ddb->prepare("
			SELECT
				media_id
			FROM
				mime_types
			WHERE
				mime_type = :mimeType
		");
		$stmt->execute([":mimeType" => $mimeType]);
		
		return $stmt->fetchColumn();
	}
}