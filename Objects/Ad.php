<?php

namespace Objects;

use \Models\AdModel;

class Ad
{
	private $adModel;
	
	/**
	 * @var int|null
	 */
	private $adID = NULL;
	
	/**
	 * @var int
	 */
	private $campaignID;
	
	/**
	 * @var int
	 */
	private $supportID;
	
	/**
	 * @var int
	 */
	private $userID;
	
	/**
	 * @var string
	 */
	private $adName;
	
	/**
	 * @var int
	 */
	private $startTime;
	
	/**
	 * @var int
	 */
	private $endTime;
	
	/**
	 * @var int
	 */
	private $order;
	
	/**
	 * @var string
	 */
	private $adUID;
	
	
	/**
	 * @param int $campaignID
	 * @param int $supportID
	 * @param int $startTime
	 * @param int $endTime
	 * @return bool|Ad
	 */
	public static function create($campaignID,
	                              $supportID,
	                              $startTime,
	                              $endTime)
	{
		$adModel = new AdModel();
		$adID = $adModel->create($campaignID,
								 $supportID,
								 $startTime,
								 $endTime);
		
		return self::getInstance($adID);
	}
	
	
	/**
	 * Try to instantiate an Ad Object
	 * @param  integer $adID Id of the ad
	 * @return bool|Ad   a Ad object on success, false otherwise
	 */
	public static function getInstance($adID)
	{
		//Sanitize the ad ID
		$adID = \Library\Sanitize::int($adID);
		
		//Do not instantiate if equal zero
		if($adID == 0)
			return false;
		
		$adModel = new AdModel();
		
		//Verify if ad exist
		if(!$adModel->adExist($adID))
			return false;
		
		//Instantiate the ad
		return new Self($adID);
	}
	
	
	/**
	 * Ad constructor.
	 * @param $adID
	 */
	private function __construct($adID)
	{
		$this->adID = $adID;
		$this->adModel = new AdModel($adID);
		
		$adInfos = $this->adModel->getInfos();
		
		$this->campaignID = $adInfos['campaignID'];
		$this->supportID = $adInfos['supportID'];
		$this->userID = $adInfos['userID'];
		$this->adName = $adInfos['name'];
		$this->startTime = $adInfos['startTime'];
		$this->endTime = $adInfos['endTime'];
		$this->order = $adInfos['adOrder'];
		$this->adUID = $adInfos['UID'];
	}
	
	
	/**
	 * @return int|null
	 */
	public function getID()
	{
		return $this->adID;
	}
	
	/**
	 * @return int
	 */
	public function getCampaignID()//: int
	{
		return $this->campaignID;
	}
	
	/**
	 * Return the Campaign Object
	 * @return Campaign
	 */
	public function getCampaign()//: Campaign
	{
		return Campaign::getInstance($this->campaignID);
	}
	
	/**
	 * @return int
	 */
	public function getSupportID()//: int
	{
		return $this->supportID;
	}
	
	/**
	 * @return Support
	 */
	public function getSupport()//: Support
	{
		return Support::getInstance($this->supportID);
	}
	
	/**
	 * @return int
	 */
	public function getUserID()//: int
	{
		return $this->userID;
	}
	
	/**
	 * @return User
	 */
	public function getUser()//: User
	{
		return User::getInstance($this->userID);
	}
	
	/**
	 * @return string
	 */
	public function getName()//: string
	{
		return $this->adName;
	}
	
	/**
	 * @return int
	 */
	public function getStartTime()//: int
	{
		return $this->startTime;
	}
	
	/**
	 * @return int
	 */
	public function getEndTime()//: int
	{
		return $this->endTime;
	}
	
	/**
	 * @return int
	 */
	public function getOrder()//: int
	{
		return $this->order;
	}
	
	/**
	 * @return string
	 */
	public function getUID()//: string
	{
		return $this->adUID;
	}
	
	/**
	 * @param bool $rooted
	 * @return string
	 */
	public function getPath($rooted)
	{
		$campaignUID = $this->getCampaign()->getCampaignUID();
		$adUID = $this->getUID();
		
		$path = "campaigns/$campaignUID/$adUID/";
		
		if($rooted)
			$path = "/$path";
		
		return $path;
	}
	
	/**
	 * Return an array with the specs of the screen
	 * @param  integer $screenID the id of the screen
	 * @return bool|array an array on success, false otherwise
	 */
	public function getScreenSpecs($screenID)
	{
		//First, verify this screen is part of this ad
		$screens = $this->getScreens();
		
		if(!array_key_exists($screenID, $screens))
			return false;
		
		//Get the screen specs
		return $screens[$screenID]->getSpecsForCampaign();
	}
	
	/**
	 * @return int
	 */
	public function getNbrScreens()
	{
		return count($this->adModel->getScreens());
	}
	
	/**
	 * Return a Screen Object for each screen of the ad
	 * @return Screen[] an array of screen objects
	 */
	public function getScreens()
	{
		//Get all the screens of the ad
		$screensID = $this->adModel->getScreens();
		$campaign = $this->getCampaign();
		
		$screens = [];
		
		//Build an array of Screen objects
		foreach($screensID as $screenID)
		{
			$screens[$screenID] = Screen::getInstance($screenID);
			$screens[$screenID]->setCampaign($campaign);
		}
		
		return $screens;
	}
	
	/**
	 * @return int
	 */
	public function getNbrCreatives()
	{
		return count($this->adModel->getCreatives());
	}
	
	/**
	 * Return an array of Creative objects, one for each creative of the ad
	 * @return Creative[]
	 */
	public function getCreatives()
	{
		//Get all the screens of the ad
		$creativesID = $this->adModel->getCreatives();
		
		$creatives = [];
		
		//Build an array of Screen objects
		foreach($creativesID as $creativeID)
		{
			$creatives[$creativeID] = Creative::getInstance($creativeID);
		}
		
		return $creatives;
	}
	
	/**
	 * @return bool|AdStats
	 */
	public function getStats()
	{
		return AdStats::getInstance($this->adID);
	}
	
	
	
	
	
	
	/**
	 * Tell if the ad can be displayed.
	 * @return bool
	 */
	public function canBeDisplayed()
	{
		//Two things to check, the current review of the ad and its creatives' status.
		$review = $this->adModel->getReviewStatus();
		
		//Cannot be displayed if status is not approved
		if($review != \Controllers\ReviewController::AD_APPROVED && 
		   $review != \Controllers\ReviewController::AD_AUTO_APPROVED)
			return false;
		
		$creatives = $this->getCreatives();
		
		foreach($creatives as $creative)
		{
			if($creative->getStatus() != \Controllers\CreativeController::CREATIVE_OK)
				return false; //No need to go further
		}
		
		return true;
	}
	
	
	/**
	 * @param int $startTime
	 * @return Ad
	 */
	public function setStartTime(int $startTime)//: Ad
	{
		$this->startTime = $startTime;
		return $this;
	}
	
	/**
	 * @param int $endTime
	 * @return Ad
	 */
	public function setEndTime(int $endTime)//: Ad
	{
		$this->endTime = $endTime;
		return $this;
	}
	
	
	
	
	public function save()
	{
		$this->adModel->update($this->startTime, $this->endTime);
	}
	
	
	
	
	/**
	 * Remove the Ad
	 * Do not use save after
	 */
	public function delete()
	{
		$_record = Record::createRecord(Record::AD_REMOVED);
		$_record->setRef1($this->getID());
		
		//Retrieve informations on the ads and its creatives
		$screens = $this->getScreens();
		$adPath = $this->getPath(false);
		
		//Delete every creatives the ad may contain
		foreach($screens as $screen)
		{
			//Delete creatives if exists
			$creative = Creative::getInstance($this->getID(), $screen->getID());
			
			if($creative != false)
				$creative->delete();
			
			//Delete the screen folder
			rmdir($adPath.$screen->getID()."/");
		}
		
		$reviewModel = new \Models\ReviewModel();
		$reviewModel->remove($this->getID());
		
		//Delete the ad
		//Remove files
		rmdir($this->getPath(false));
		
		//Delete the entry
		$this->adModel->delete();
		
		$_record->setResult(Record::OK)
				->save();
	}
}