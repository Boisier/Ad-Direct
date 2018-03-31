<?php

namespace Objects;

use \Models\CampaignModel;

class Campaign
{
	private $campaignModel = NULL;

	private $campaignID = NULL;
	private $campaignUID;
	private $broadcasterID;
	private $supportID;
	private $creatorID;
	private $createDate;
	private $campaignName;
	private $startDate;
	private $endDate;
	private $displayDuration;
	private $adLimit;

	const CAMPAIGN_STATUS_EMPTY = 0;
	const CAMPAIGN_STATUS_NOT_PLAYING = 1;
	const CAMPAIGN_STATUS_PENDING = 2;
	const CAMPAIGN_STATUS_PLAYING = 3;
	const CAMPAIGN_STATUS_ENDED = 4;


	/**
	 * @param int $broadcasterID
	 * @param int $supportID
	 * @param int $mediaType
	 * @param $campaignName
	 * @param $campaignStartDate
	 * @param $campaignEndDate
	 * @param $adLimit
	 * @param $displayDuration
	 * @return bool|Campaign
	 */
	public static function create($broadcasterID,
	                              $supportID,
	                              $campaignName,
	                              $campaignStartDate,
	                              $campaignEndDate,
	                              $adLimit,
	                              $displayDuration)
	{
		$campaignModel = new CampaignModel();
		$campaignID = $campaignModel->create($broadcasterID,
											 $supportID,
											 $campaignName,
											 $campaignStartDate,
											 $campaignEndDate,
											 $adLimit,
											 $displayDuration);

		return self::getInstance($campaignID);
	}



	/**
	 * Try to instantiate a Campaign Object
	 * @param  integer $campaignID Id of the campaign
	 * @return bool|Campaign   a Campaign object on success, false otherwise
	 */
	public static function getInstance($campaignID)
	{
		//Sanitize the campaign ID
		$campaignID = \Library\Sanitize::int($campaignID);

		//Do not instantiate if equal zero
		if($campaignID == 0)
			return false;

		$campaignModel = new CampaignModel();

		//Verify if campaign exist
		if(!$campaignModel->campaignExist($campaignID))
			return false;

		//Instantiate the campaign
		return new Campaign($campaignID);
	}



	/**
	 * Set the campaign ID
	 * @private
	 * @param integer $campaignID the campaign ID
	 */
	private function __construct($campaignID)
	{
		$this->campaignID = $campaignID;
		$this->campaignModel = new CampaignModel($campaignID);


		//Fill in the object
		$campaignInfos = $this->campaignModel->getInfos();

		$this->campaignUID = $campaignInfos["UID"];
		$this->broadcasterID = $campaignInfos["broadcasterID"];
		$this->supportID = $campaignInfos["supportID"];
		$this->creatorID = $campaignInfos["creator"];
		$this->createDate = $campaignInfos["createDate"];
		$this->campaignName = $campaignInfos["name"];
		$this->startDate = $campaignInfos["startDate"];
		$this->endDate = $campaignInfos["endDate"];
		$this->displayDuration = $campaignInfos["displayDuration"];
		$this->adLimit = $campaignInfos["adLimit"];
	}




	/**
	 * @return int
	 */
	public function getID()
	{
		return $this->campaignID;
	}

	/**
	 * @return string
	 */
	public function getCampaignUID()
	{
		return $this->campaignUID;
	}

	/**
	 * @return Broadcaster
	 */
	public function getBroadcaster()
	{
		return Broadcaster::getInstance($this->broadcasterID);
	}

	/**
	 * @return int
	 */
	public function getBroadcasterID()
	{
		return $this->broadcasterID;
	}

	/**
	 * @return int
	 */
	public function getCreatorID()
	{
		return $this->creatorID;
	}

	/**
	 * @return int
	 */
	public function getCreateDate()
	{
		return $this->createDate;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->campaignName;
	}

	/**
	 * @return int
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @return int
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @return int
	 */
	public function getDisplayDuration()
	{
		return $this->displayDuration;
	}

	/**
	 * @return int
	 */
	public function getAdLimit()
	{
		return $this->adLimit;
	}

	/**
	 * @return int
	 */
	public function getSupportID()
	{
		return $this->supportID;
	}

	/**
	 * @return Support
	 */
	public function getSupport()
	{
		return \Objects\Support::getInstance($this->supportID);
	}

	public function getDisplayFileURL()
	{
		return "https://{$_SERVER['SERVER_NAME']}/display/generate/{$this->campaignID}/";
	}


	/**
	 *
	 */
	public function isDebugging()
	{
		return $this->campaignModel->isDebugging();
	}





	/**
	 * Return a Screen Object for each screen of the campaign
	 * @return Screen[] an array of screen objects
	 */
	public function getScreens()
	{
		//Get all the screens of the ad
		$screensID = $this->campaignModel->getScreens();

		$screens = [];

		//Build an array of Screen objects
		foreach($screensID as $screenID)
		{
			$screens[$screenID] = Screen::getInstance($screenID);
			$screens[$screenID]->setCampaign($this);
		}

		return $screens;
	}




	/**
	 * Return the number of ad in the campaign
	 * @return int The number of ads
	 */
	public function getNbrAds()
	{
		//Get all the ads of the campaign
		$adsID = $this->campaignModel->getAds();

		return count($adsID);
	}




	/**
	 * Return the number of ad pending in the campaign
	 * @return int The number of ads
	 */
	public function getPendingNbrAds()
	{
		return $this->campaignModel->getPendingNbrAds();
	}




	/**
	 * Return a Ad Object for each ad of the campaign
	 * @return Ad[] an array of Ad objects
	 */
	public function getAds()
	{
		//Get all the ads of the campaign
		$adsID = $this->campaignModel->getAds();

		$ads = [];

		//Build an array of Ad objects
		foreach($adsID as $adID)
		{
			$ads[$adID] = Ad::getInstance($adID);
		}

		return $ads;
	}


	public function getStatus()
	{
		// Check if going to play
		if(time() < $this->getStartDate())
			return Campaign::CAMPAIGN_STATUS_NOT_PLAYING;

		// Check if playing ended
		if(time() > $this->getEndDate())
			return Campaign::CAMPAIGN_STATUS_ENDED;

		// Check if empty
		if($this->getNbrAds() == 0)
			return Campaign::CAMPAIGN_STATUS_EMPTY;

		// Check ads status
		$ads = $this->getAds();
		$status = Campaign::CAMPAIGN_STATUS_NOT_PLAYING;

		foreach($ads as $ad)
		{
			$adStatus = $ad->getStatus();

			if($adStatus == Ad::AD_STATUS_PLAYING)
			{
				$status = Campaign::CAMPAIGN_STATUS_PLAYING;
				continue;
			}

			if($adStatus == Ad::AD_STATUS_PENDING)
			{
				$status = Campaign::CAMPAIGN_STATUS_PENDING;
				break;
			}
		}

		return $status;
	}







	/**
	 * @param mixed $supportID
	 * @return Campaign
	 */
	public function setSupportID($supportID)
	{
		$this->supportID = $supportID;
		return $this;
	}

	/**
	 * @param mixed $campaignName
	 * @return Campaign
	 */
	public function setCampaignName($campaignName)
	{
		$this->campaignName = $campaignName;
		return $this;
	}

	/**
	 * @param mixed $startDate
	 * @return Campaign
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
		return $this;
	}

	/**
	 * @param mixed $endDate
	 * @return Campaign
	 */
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
		return $this;
	}

	/**
	 * @param mixed $displayDuration
	 * @return Campaign
	 */
	public function setDisplayDuration($displayDuration)
	{
		$this->displayDuration = $displayDuration;
		return $this;
	}

	/**
	 * @param mixed $adLimit
	 * @return Campaign
	 */
	public function setAdLimit($adLimit)
	{
		$this->adLimit = $adLimit;
		return $this;
	}


	/**
	 * Save the campaign
	 */
	public function save()
	{
		$this->campaignModel->update(
			$this->supportID,
			$this->campaignName,
			$this->startDate,
			$this->endDate,
			$this->adLimit,
			$this->displayDuration
		);
	}


	/**
	 * Remove the campaign and its dependancies
	 */
	public function delete()
	{
		$_record = \Objects\Record::createRecord(\Objects\Record::CAMPAIGN_REMOVED);
		$_record->setRef1($this->getID());

		//Remove ads in the campaign
		$ads = $this->getAds();

		//Remove all ads of the campaign
		foreach($ads as $ad)
		{
			$ad->delete();
		}

		$this->campaignModel->delete();

		//Remove the campaign Directory
		rmdir("campaigns/{$this->getCampaignUID()}");
	}
}
