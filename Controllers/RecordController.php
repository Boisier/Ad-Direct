<?php

namespace Controllers;

use \Library\View,
	\Library\Composer,
	\Library\Sanitize;
use Models\RecordModel;

class RecordController
{
	const DEFAULT_PAGE_SIZE = 100;
	
	
	
	
	
	/**
	 * Print the needed form to interact with the logs
	 * @param string  $formName            The name of the form
	 * @param integer [$broadcasterID      = 0] The broadcaster ID
	 */
	public function form($formName, $adID, $action = 0)
	{
		\Library\User::restricted("APPROVE_CREATIVES");
		
		switch($formName)
		{
			case "review":
			
				$form = View("");
				
			break;
		}
		
		echo $form->render();
	}
	
	
	
	
	
	/**
	 * Insert a new review, if possible, for the given ad
	 * @param integer $adID The ad to ork with
	 */
	public function userlogs($userID)
	{
		$user = \Objects\User::getInstance($userID);
		
		if(!$user)
			return;
		
		//Broadcaster infos
		$broadcaster = $user->getBroadcaster();
		
		$page = new Composer();
		
		if($user->isAdmin())
		{
			$header = new View("records/headerAdmin");
		}
		else
		{
			$header = new View("records/headerClient");
			$header->broadcasterID = $broadcaster->getID();
			$header->broadcasterName = $broadcaster->getName();
			$header->userID = $user->getID();
		}
		
		$header->userName = $user->getName();
		
		//Set up view
		$body = new View("records/home");
		
		//Retrieve logs
		$logsList = $this->buildLogPage($user, 0);
		
		$body->hasMore = $logsList->nbrViews() >= self::DEFAULT_PAGE_SIZE ? true : false;
		$body->logs = $logsList->render();
		$body->userID = $user->getID();
		
		$page->attach($header)
			 ->attach($body);
		
		echo $page->render();
	}
	
	
	
	public function getPage($userID, $page)
	{
		$user = \Objects\User::getInstance($userID);
		
		if(!$user)
		{
			echo json_encode(["success" => false]);
			return;
		}
		
		$logList = $this->buildLogPage($user, $page);
		$nbrLogs = $logList->nbrViews();
		
		header('Content-Type: application/json');
		echo json_encode(["success" => true,
						  "end" => $nbrLogs < self::DEFAULT_PAGE_SIZE,
						  "html" => $logList->render()]);
	}
	
	
	
	/**
	 * @param \Objects\User $user
	 * @param int $page
	 * @param int $length
	 * @return Composer
	 */
	private function buildLogPage(\Objects\User $user, int $page, int $length = self::DEFAULT_PAGE_SIZE)
	{
		$logs = $user->getLogPage($page, $length);
		$dateformat = \Library\Localization::dateFormat();
		
		$list = new Composer();
		
		foreach($logs as $log)
		{
			$logView = new View("records/recordList");
			$logView->date = $log->getDate();
			$logView->dateformat = $dateformat;
			$logView->action = $log->getAction();
			$logView->result = $log->getResult();
			$logView->resultText = $log->getResultText();
			$logView->message = $log->getMessage();
			
			$list->attach($logView);
		}
		
		return $list;
	}
	
	
	
	
	
}