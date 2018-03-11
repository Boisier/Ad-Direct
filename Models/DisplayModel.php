<?php

namespace Models;

class DisplayModel
{
	private $ddb;
	
	
	public function __construct()
	{
		$this->ddb = \Library\DBA::get();
	}
	
	
	
	
	
	
	public function registerPrint($campaignID, $printStatus, $adID, $printTime, $frameID)
	{
		$stmt = $this->ddb->prepare("
		INSERT INTO 
			campaign_print(
				print_time, 
				frame_id, 
				campaign_id, 
				ad_id, 
				print_status) 
		VALUES (
			:printTime,
			:frameID, 
			:campaignID,
			:adID,
			:status
		)
		");
		
		$stmt->execute([":printTime" => $printTime,
					    ":frameID" => $frameID,
					    ":campaignID" => $campaignID,
					    ":adID" => $adID,
					    ":status" => $printStatus]);
	}
}