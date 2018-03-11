<?php

namespace Controllers;

use \Library\Sanitize;

//Allow cross origin requests (DisplayFiles are played on a localhost origin)
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header("Access-Control-Allow-Headers: cache-control, pragma");

class DisplayController
{
	
	function generate($campaignID)
	{
		$campaignID = Sanitize::int($campaignID);
		
		$campaign = \Objects\Campaign::getInstance($campaignID);
		
		$file = new \Library\View("displayFiles/displayFile");
		$file->campaignID = $campaign->getID();
		$file->campaignDebug = $campaign->isDebugging();
		
		$refresh = 300;
		$weekSec = 3600 * 24 * 7;
		
		header('Content-Type: text/html');
		header("Cache-Control: public, max-age=$refresh, max-stale=$weekSec");
		header('Pragma: ');
		header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (3600 * 24 * 7)));
		
		echo $file->render();
	}
	
	
	
	
	
	
	
	public function data($campaignID)
	{
		header('Content-Type: application/json');
		
		$campaignID = Sanitize::int($campaignID);
		
		//Retreive the campaign
		$campaign = \Objects\Campaign::getInstance($campaignID);
		
		if(!$campaign)
		{
 			echo json_encode(["status" => "KO",
				    "time" => time()]);
			
			return;
		}
		
		$campaignScreens = $campaign->getScreens();
		$defaultAdModel = new \Models\DefaultadModel($campaign->getBroadcasterID());
		
		$screens = [];
		
		//Build the screens part
		foreach($campaignScreens as $screen)
		{
			$screens[$screen->getID()] = [
				"screenID" => $screen->getID(),
				"screenWidth" => $screen->getWidth(),
				"screenHeight" => $screen->getHeight(),
				"defaultCreative" => $defaultAdModel->path($screen->getID(), true)
			];
		}
		
		//Gather ads and creative datas
		$campaignAds = $campaign->getAds();
		
		$ads = [];
		
		foreach($campaignAds as $ad)
		{
			if(!$ad->canBeDisplayed())
				continue; //This ad connot be displayed
			
			$adStartTime = $ad->getStartTime();
			$adEndTime = $ad->getEndTime();
			
			//if(time() < $adStartTime || time() > $adEndTime)
			//	continue; //This ad is not to be displayed yet
			
			$adData = [
				"adID" => $ad->getID(),
				"start" => $adStartTime,
				"end" => $adEndTime,
				"creatives" => []
			];
			
			$adCreatives = $ad->getCreatives();
			
			foreach($adCreatives as $creative)
			{
				array_push($adData["creatives"], [
					"ID" => $creative->getID(),
					"path" => $creative->getPath(true),
					"mediaType" => $creative->getMediaType(),
					"screenID" => $creative->getScreenID()
				]);
			}
			
			array_push($ads, $adData);
		}
	
		
		//Build the data block
		$data = 
		[
			"status" => "OK",
			"time" => time(),
			"start" => $campaign->getStartDate(),
			"end" => $campaign->getEndDate(),
			"screens" => $screens,
			"ads" => $ads
		];
		
		echo json_encode($data);
	}
	
	
	
	
	
	
	public function register($campaignID)
	{
		$campaignID = Sanitize::int($campaignID);
		
		$displayModel = new \Models\DisplayModel();
		
		$frameID = Sanitize::string($_POST["frameID"]);
		
		foreach($_POST["history"] as $print)
		{
			//This is an ad print
			$adID = Sanitize::int($print[0]);
			$printStatus = Sanitize::string($print[1]);
			$printTime = Sanitize::int($print[2]);
			
			$displayModel->registerPrint($campaignID, $printStatus, $adID, $printTime, $frameID);
		}
	}
}

