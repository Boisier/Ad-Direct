<?php

namespace Objects;

use \Models\AdModel;

class AdStats
{
	private $adID = NULL;
	private $adModel;
	
	
	/**
	 * Try to instantiate an Ad Object
	 * @param  integer $adID Id of the ad
	 * @return bool|AdStats   a Ad object on success, false otherwise
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
		return new AdStats($adID);
	}
	
	
	
	/**
	 * Set the ad ID
	 * @private
	 * @param integer $adID the ad ID
	 */
	private function __construct($adID)
	{
		$this->adID = $adID;
		$this->adModel = new AdModel($adID);
	}
	
	
	
	
	
	public function getAd()
	{
		return Ad::getInstance($this->adID);
	}
	
	
	
	
	
	public function getPrintsTotal()
	{
		return $this->adModel->getPrintsTotal();
	}
}