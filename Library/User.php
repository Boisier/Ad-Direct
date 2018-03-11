<?php

namespace Library;

class User
{
	public static function loggedIn()
	{
		if(Session::exist("userID") && Session::exist("userAdmin"))
			return true;
		
		return false;
	}
	
	public static function isAdmin()
	{
		if(!self::loggedIn())
			return false;
		
		if(\Library\Session::read("userAdmin") == 0)
			return false;
		
		return true;
	}
	
	public static function id()
	{
		if(!self::loggedIn())
		{
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
		
		return Session::read("userID");
	}
	
	
	
	public static function onlyLoggedIn()
	{
		if(!self::loggedIn())
		{
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
	}
	
	public static function onlyAdmins()
	{
		if(!self::isAdmin())
		{
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
	}
	
	public static function restricted($privilege, $silence = false)
	{
		$user = \Objects\User::getInstance(self::id());
		$privileges = $user->getPrivileges("justNames");
		
		if(is_array($privileges) &&in_array($privilege, $privileges))
			return true;
		
		if($silence)
			return false;

		$page = new \Library\View("common/badAction");
		echo $page->render();
		exit;
		
		return false;
	}
	
	
	/**
	 * Tell if the current user has a specific privilege
	 * @param string $privilege Privilege
	 * @return bool
	 */
	public static function hasPrivilege($privilege)
	{
		$user = \Objects\User::getInstance(self::id());
		$privileges = $user->getPrivileges("justNames");
		
		if(is_array($privileges) &&in_array($privilege, $privileges))
			return true;
		
		return false;
	}
	
	
	public static function canEditCampaign($campaignID)
	{
		if(!self::loggedIn())
		{
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
		
		$user = \Objects\User::getInstance(self::id());
		$campaignsID = $user->getCampaignsID();
		
		if(!self::restricted("MANAGE_CLIENTS", true) && !in_array($campaignID, $campaignsID))
		{
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
	}
	
	public static function canEditDefaultAds($broadcasterID, $silence = true)
	{
		if(!self::loggedIn())
		{
			if($silence)
				return false;
			
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
		
		$user = \Objects\User::getInstance(self::id());
		$userBroadcaster = $user->getBroadcasterID();
		
		if(!self::restricted("MANAGE_CLIENTS", true) && $broadcasterID != $userBroadcaster)
		{
			
			if($silence)
				return false;
			
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
		
		return true;
	}
	
	public static function canEditAd($adID, $silence = true)
	{
		if(!self::loggedIn())
		{
			if($silence)
				return false;
			
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
		
		$user = \Objects\User::getInstance(self::id());
		$campaignsID = $user->getCampaignsID();
		
		$ad = \Objects\Ad::getInstance($adID);
		
		if(!self::restricted("MANAGE_CLIENTS", true) &&!in_array($ad->getCampaignID(), $campaignsID))
		{
			if($silence)
				return false;
			
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
		
		return true;
	}
}

