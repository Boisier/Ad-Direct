<?php

namespace Objects;

use Models\BroadcasterModel;

class Broadcaster
{
	private $broadcasterModel;
	
	/**
	 * @var int|null
	 */
	private $broadcasterID = null;
	
	/**
	 * @var string
	 */
	private $broadcasterName;
	
	/**
	 * @var int
	 */
	private $createTime;
	
	/**
	 * @var int
	 */
	private $creatorID;
	
	/**
	 * @var int
	 */
	private $groupID;
	
	
	/**
	 * Create a new broadcaster
	 * @param $broadcasterName
	 * @param $broadcasterGroupID
	 * @return bool|Broadcaster
	 */
	public static function create($broadcasterName,
								  $broadcasterGroupID)
	{
		$broadcasterModel = new BroadcasterModel();
		
		$broadcasterID = $broadcasterModel->create($broadcasterName,
								                   $broadcasterGroupID);
		
		return self::getInstance($broadcasterID);
	}
	
	
	/**
	 * Try to instantiate a Broadcaster object
	 * @param $broadcasterID
	 * @return bool|Broadcaster
	 */
	public static function getInstance($broadcasterID)
	{
		//Sanitize the broadcaster ID
		$broadcasterID = \Library\Sanitize::int($broadcasterID);
		
		//Do not instantiate if equal zero
		if($broadcasterID == 0)
			return false;
		
		$broadcasterModel = new BroadcasterModel();
		
		//Verify if broadcaster exist
		if(!$broadcasterModel->broadcasterExist($broadcasterID))
			return false;
		
		//Instantiate the broadcaster
		return new self($broadcasterID);
	}
	
	
	/**
	 * Broadcaster constructor.
	 * @param $broadcasterID
	 */
	private function __construct($broadcasterID)
	{
		$this->broadcasterID = $broadcasterID;
		$this->broadcasterModel = new BroadcasterModel($broadcasterID);
		
		$broadcasterInfos = $this->broadcasterModel->getInfos();
		
		$this->broadcasterName = $broadcasterInfos['name'];
		$this->createTime = $broadcasterInfos['createTime'];
		$this->creatorID = $broadcasterInfos['creatorID'];
		$this->groupID = $broadcasterInfos['groupID'];
	}
	
	/**
	 * @return int|null
	 */
	public function getID()
	{
		return $this->broadcasterID;
	}
	
	/**
	 * @return string
	 */
	public function getName()//: string
	{
		return $this->broadcasterName;
	}
	
	/**
	 * @return int
	 */
	public function getCreateTime()//: int
	{
		return $this->createTime;
	}
	
	/**
	 * @return int
	 */
	public function getCreatorID()//: int
	{
		return $this->creatorID;
	}
	
	/**
	 * @return int
	 */
	public function getGroupID()//: int
	{
		$groupID = $this->groupID;
		return $groupID == null ? 0 : $groupID;
	}
	
	
	/**
	 * Get all campaigns of the broadcaster
	 * @return Campaign[]
	 */
	public function getCampaigns()
	{
		$campaignsID = $this->broadcasterModel->getCampaigns();
		
		$campaigns = [];
		
		foreach ($campaignsID as $campaignID)
		{
			array_push($campaigns, Campaign::getInstance($campaignID));
		}
		
		return $campaigns;
	}
	
	/**
	 * Get nbr of campaigns in the broadcaster
	 * @return int
	 */
	public function getNbrCampaigns()
	{
		return count($this->broadcasterModel->getCampaigns());
	}
	
	/**
	 * Get all clients of the broadcaster
	 * @return User[]
	 */
	public function getClients()
	{
		$clientsID = $this->broadcasterModel->getClients();
		
		$clients = [];
		
		foreach ($clientsID as $clientID)
		{
			array_push($clients, User::getInstance($clientID));
		}
		
		return $clients;
	}
	
	/**
	 * Get nbr of clients in the broadcaster
	 * @return int
	 */
	public function getNbrClients()
	{
		return count($this->broadcasterModel->getClients());
	}
	
	
	
	
	/**
	 * Return the number of ad pending in the broadcaster's campaigns
	 * @return int The number of ads
	 */
	public function getPendingNbrAds()
	{
		return $this->broadcasterModel->getPendingNbrAds();
	}
	
	
	
	
	
	/**
	 * @param string $broadcasterName
	 * @return Broadcaster
	 */
	public function setName(string $broadcasterName)
	{
		$this->broadcasterName = $broadcasterName;
		return $this;
	}
	
	/**
	 * @param int $groupID
	 * @return Broadcaster
	 */
	public function setGroupID(int $groupID)//: Broadcaster
	{
		$this->groupID = $groupID;
		return $this;
	}
	
	/**
	 * Save the broadcaster
	 */
	public function save()
	{
		$this->broadcasterModel->update($this->broadcasterName,
										$this->groupID);
	}
	
	/**
	 * Remove the broadcaster
	 */
	public function delete()
	{
		$this->broadcasterModel->delete();
	}
	
}