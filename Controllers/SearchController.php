<?php

namespace Controllers;

use \Library\View,
	\Library\Composer,
	\Library\Sanitize;

class SearchController
{
	/**
	 * Search through Broadcaster, campaigns and users
	 */
	public function search()
	{
		\Library\User::onlyAdmins();
		
		$query = Sanitize::string($_POST['search']);
		
		$searchModel = new \Models\SearchModel();
		$results = $searchModel->search($query);
		
		//No results
		if(count($results) == 0)
		{
			$view = new View("search/noResults");
			
			echo $view->render();
			
			return;
		}
		
		//Results, let's treat them one by one
		$response = new Composer();
		
		foreach($results as $result)
		{
			switch($result['type'])
			{
				case "broadcaster":
					
					$resultView = $this->broadcasterResult($result['ID']);
					
				break;
				case "campaign":
					
					$resultView = $this->campaignResult($result['ID']);
					
				break;
				case "client":
					
					$resultView = $this->clientResult($result['ID']);
				
				break;
			}
			
			$response->attach($resultView);
		}
		
		echo $response->render();
	}
	
	/**
	 * @param int $broadcasterID
	 * @return View
	 */
	private function broadcasterResult($broadcasterID)
	{
		$broadcaster = \Objects\Broadcaster::getInstance($broadcasterID);
		
		$view = new View("broadcasters/broadcasterList");
		$view->broadcasterID = $broadcaster->getID();
		$view->broadcasterName = $broadcaster->getName();
		$view->nbrClients = $broadcaster->getNbrClients();
		$view->nbrCampaigns = $broadcaster->getNbrCampaigns();
		$view->groupID = $broadcaster->getGroupID();
		
		return $view;
	}
	
	/**
	 * @param int $campaignID
	 * @return View
	 */
	private function campaignResult($campaignID)
	{
		$campaign = \Objects\Campaign::getInstance($campaignID);
		$support = $campaign->getSupport();
		
		$view = new View("broadcasters/campaignList");
		$view->campaignID = $campaign->getID();
		$view->campaignName = $campaign->getName();
		$view->startDate = $campaign->getStartDate();
		$view->endDate = $campaign->getEndDate();
		$view->supportName = $support->getName();

		return $view;
	}
	
	/**
	 * @param int $userID
	 * @return View
	 */
	private function clientResult($userID)
	{
		$user = \Objects\User::getInstance($userID);
		
		$view = new View("search/clientList");
		$view->clientName = $user->getName();
		$view->clientEmail = $user->getEmail();
		$view->broadcasterID = $user->getBroadcasterID();

		return $view;
	}
}