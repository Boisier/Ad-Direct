<?php

namespace Objects;

use \Models\SupportModel;

class Support
{
	private $supportModel;
	
	private $supportID;
	private $supportName;
	private $nbrScreens;
	
	/**
	 * Try to instantiate a Support Object
	 * @param  integer $supportID Id of the support
	 * @return bool|Support   a Support object on success, false otherwise
	 */
	public static function getInstance($supportID)
	{
		//Sanitize the support ID
		$supportID = \Library\Sanitize::int($supportID);
		
		//Do not instantiate if equal zero
		if($supportID == 0)
			return false;
		
		$supportModel = new SupportModel();
		
		//Verify if campaign exist
		if(!$supportModel->supportExist($supportID))
			return false;
		
		//Instantiate the support
		return new self($supportID);
	}
	
	
	/**
	 * Set the support
	 * @private
	 * @param $supportID int
	 */
	private function __construct($supportID)
	{
		$this->supportID = $supportID;
		$this->supportModel = new SupportModel($supportID);
		
		
		//Fill in the object
		$supportInfos = $this->supportModel->getInfos();
		
		$this->supportName = $supportInfos["name"];
		$this->supportScreens = $supportInfos["screens"];
	}
	
	/**
	 * @return int
	 */
	public function getID()
	{
		return $this->supportID;
	}
	
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->supportName;
	}
	
	/**
	 * @return int
	 */
	public function getNbrScreens()
	{
		return $this->nbrScreens;
	}
	
	public function getScreensID()
	{
		return $this->supportModel->screens();
	}
	
	
	
	
	
	
	/**
	 * @param mixed $supportName
	 * @return Support
	 */
	public function setSupportName($supportName)
	{
		$this->supportName = $supportName;
		return $this;
	}
	
	
	
	
	
	public function save()
	{
		$this->supportModel->update($this->supportName);
	}
	
	
	/**
	 * @return bool
	 */
	public function isUsed()
	{
		return $this->supportModel->isUsed();
	}
	
	/**
	 * Remove the support from the database
	 */
	public function delete()
	{
		$this->supportModel->delete();
	}
}