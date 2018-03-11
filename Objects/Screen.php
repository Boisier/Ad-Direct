<?php

namespace Objects;

use \Models\ScreenModel;

class Screen
{
	private $screenModel;
	
	//Default properties
	/**
	 * @var null|int
	 */
	private $screenID = NULL;
	
	/**
	 * @var int
	 */
	private $supportID;
	
	/**
	 * @var string
	 */
	private $screenName;
	
	/**
	 * @var int
	 */
	private $width;
	
	/**
	 * @var int
	 */
	private $height;
	
	//Campaign supercharging
	/**
	 * @var null|Campaign
	 */
	private $campaign = null;
	
	/**
	 * @var int
	 */
	private $mediaType;
	
	/**
	 * @var int
	 */
	private $maxWeight;
	
	/**
	 * Try to instantiate a Screen Object
	 * @param  int $screenID Id of the screen
	 * @return Screen|bool a Screen object on success, false otherwise
	 */
	public static function getInstance($screenID)
	{
		//Sanitize the screen ID
		$screenID = \Library\Sanitize::int($screenID);
		
		//Do not instantiate if equal zero
		if($screenID == 0)
			return false;
		
		$screenModel = new ScreenModel();
		
		//Verify if ad exist
		if(!$screenModel->screenExist($screenID))
			return false;
		
		//Instantiate the ad
		return new Screen($screenID);
	}
	
	/**
	 * @private
	 * @param integer $screenID
	 */
	private function __construct($screenID)
	{
		$this->screenID = $screenID;
		$this->screenModel = new ScreenModel($screenID);
		
		$screenInfos = $this->screenModel->getInfos();
		
		$this->supportID = $screenInfos['supportID'];
		$this->screenName = $screenInfos['name'];
		$this->width = $screenInfos['width'];
		$this->height = $screenInfos['height'];
	}
	
	/**
	 * @param Campaign $campaign
	 */
	public function setCampaign($campaign)
	{
		$this->campaign = $campaign;
		
		$campaignScreenInfos = $this->screenModel->getCampaignScreenInfos($campaign->getID());
		
		$this->maxWeight = $campaignScreenInfos['maxWeight'];
		$this->mediaType = $campaignScreenInfos['mediaType'];
	}
	
	/**
	 * @return int|null
	 */
	public function getID()
	{
		return $this->screenID;
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
	 * @return string
	 */
	public function getName()//: string
	{
		return $this->screenName;
	}
	
	/**
	 * @return int
	 */
	public function getWidth()//: int
	{
		return $this->width;
	}
	
	/**
	 * @return int
	 */
	public function getHeight()//: int
	{
		return $this->height;
	}
	
	
	/**
	 * @return null|Campaign
	 */
	public function getCampaign()
	{
		if(!$this->campaign)
			return null;
		
		return $this->campaign;
	}
	
	/**
	 * @return int|null
	 */
	public function getCampaignID()
	{
		if(!$this->campaign)
			return null;
		
		return $this->campaign->getID();
	}
	
	
	/**
	 * Get the max weight of the screen for the current campaign
	 * @return int|null The max weight in MB
	 */
	public function getMaxWeight()
	{
		if(!$this->campaign)
			return null;
		
		return $this->maxWeight;
	}
	
	/**
	 * Get the media type of the screen for the current campaign
	 * @return int|null The ID of the media type
	 */
	public function getMediaType()
	{
		if(!$this->campaign)
			return null;
		
		return $this->mediaType;
	}
	
	/**
	 * @return array|null
	 */
	public function getSpecsForCampaign()
	{
		if(!$this->campaign)
			return null;
		
		$sizeLimit = $this->getMaxWeight($this->campaign->getID());
		$displayDuration = $this->campaign->getDisplayDuration();
		
		$mediaTypeID = $this->getMediaType($this->campaign->getID());
		$mediaTypeModel = new \Models\MediaTypeModel();
		$mimeTypes = $mediaTypeModel->getMimes($mediaTypeID);
		
		//Build the spec array
		return [      "sizeLimit" => $sizeLimit,
				"displayDuration" => $displayDuration,
				 "supportedMimes" => $mimeTypes,
				      "mediaType" => $mediaTypeID,
				          "width" => $this->getWidth(),
				         "height" => $this->getHeight()];
	}
	
	/**
	 * @param string $screenName
	 * @return Screen
	 */
	public function setName(string $screenName)//: Screen
	{
		$this->screenName = $screenName;
		return $this;
	}
	
	/**
	 * @param int $width
	 * @return Screen
	 */
	public function setWidth(int $width)//: Screen
	{
		$this->width = $width;
		return $this;
	}
	
	/**
	 * @param int $height
	 * @return Screen
	 */
	public function setHeight(int $height)//: Screen
	{
		$this->height = $height;
		return $this;
	}
	
	/**
	 * Save the screen
	 */
	public function save()
	{
		$this->screenModel->update($this->screenID,
								   $this->screenName,
								   $this->width,
								   $this->height);
	}
	
	/**
	 * Remove the screen
	 * Do not use save after calling delete
	 */
	public function delete()
	{
		$this->screenModel->delete();
	}
}