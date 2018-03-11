<?php

namespace Models;

class VideoTreatmentModel
{
	private $ddb;
	
	public function __construct()
	{
		$this->ddb = \Library\DBA::get();
	}
	
	
	
	
	
	public function alreadyConverting()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				COUNT(*)
			FROM
				creatives
			WHERE
				creative_status = :status
		");
		$stmt->execute([":status" => \Controllers\CreativeController::CREATIVE_CONVERTING]);
		
		if($stmt->fetchColumn()  == 0)
			return false;
		
		return true;
	}
	
	
	
	
	
	public function getConvertingCreative()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creative_id
			FROM
				creatives
			WHERE
				creative_status = :status
		");
		$stmt->execute([":status" => \Controllers\CreativeController::CREATIVE_CONVERTINg]);
		
		return $stmt->fetchColumn();
	}
	
	
	
	
	
	public function getCreativesAwaitingConversion()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				creative_id
			FROM
				creatives
			WHERE
				creative_status = :status OR
				creative_status = :status2
			ORDER BY
				creative_status, creative_upload_time
		");
		$stmt->execute([":status" => \Controllers\CreativeController::CREATIVE_NEED_CONVERT,
						":status2" => \Controllers\CreativeController::CREATIVE_NEED_CONVERT_RETRY]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
}