<?php

namespace Controllers;

use \Library\View,
	\Library\Composer;



class EmailController
{	
	const EMAIL_NEW_CLIENT = "WELCOME_EMAIL";
	const EMAIL_CLIENT_UPDATE = "CLIENT_UPDATE";
	const EMAIL_CLIENT_NEW_PASSWORD = "CLIENT_PASSWORD_UPDATE";
	const EMAIL_REVIEW_AD = "REVIEW_AD";
	const EMAIL_REVIEWED_AD = "REVIEWED_AD";
	const EMAIL_SCHEDULE_CAMPAIGN = "SCHEDULE_CAMPAIGN";
	const EMAIL_UPDATE_CAMPAIGN_SCHEDULE = "SCHEDULE_CAMPAIGN_UPDATE";
	const EMAIL_END_OF_DISPLAY = "END_OF_DISPLAY";
	
	
	/**
	 * @param string $type
	 * @param int refID1
	 * @param int $refID2
	 * @param array $data
	 */
	public function create($type, $refID1, $refID2 = 0, $data = [])
	{
		if(!\Library\User::loggedIn())
		{
			if(empty($data["AUTOMATED"]) || $data["AUTOMATED"] != true)
			{
				$page = new \Library\View("common/badAction");
				echo $page->render();
				exit;
			}
			
			//Set Emmiter to main admin account
			$emitter = 1; 
		}
		else
		{
			$emitter = \Library\User::id();
		}
		
		$type = strtoupper($type);
		
		if(!$this->typeIsValid($type))
		{
			$page = new \Library\View("common/badAction");
			echo $page->render();
			exit;
		}
		
		$recipients = $this->recipients($type, $refID1, $refID2);
		
		$emailModel = new \Models\EmailModel();
		
		foreach($recipients as $recipient)
		{
			//insert email in DB
			$emailID = $emailModel->create($type, $emitter, $recipient, $refID1, $refID2);

			//Send the email
			$this->send($emailID, $data);
		}
	}
	
	
	
	
	private function typeIsValid($type)
	{
		switch($type)
		{
			case EmailController::EMAIL_NEW_CLIENT:
			case EmailController::EMAIL_CLIENT_UPDATE:
			case EmailController::EMAIL_CLIENT_NEW_PASSWORD:
			case EmailController::EMAIL_REVIEW_AD:
			case EmailController::EMAIL_REVIEWED_AD:
			case EmailController::EMAIL_END_OF_DISPLAY:
			case EmailController::EMAIL_SCHEDULE_CAMPAIGN:
			case EmailController::EMAIL_UPDATE_CAMPAIGN_SCHEDULE:
				return true;
			break;
			default;
				return false;
		}
	}
	
	
	
	
	private function recipients($type, $refID1, $refID2)
	{	
		$emailModel = new \Models\EmailModel();
		
		switch($type)
		{
			case EmailController::EMAIL_NEW_CLIENT:
			case EmailController::EMAIL_CLIENT_UPDATE:
			case EmailController::EMAIL_CLIENT_NEW_PASSWORD:
				
				$recipients = $emailModel->clientEmail($refID1);
				
			break;
			case EmailController::EMAIL_REVIEW_AD:
				
				$recipients = $emailModel->reviewersEmails();		
					
			break;
			case EmailController::EMAIL_REVIEWED_AD:
			case EmailController::EMAIL_END_OF_DISPLAY:
				
				$recipients = $emailModel->campaignEmailsByAd($refID1);
					
			break;
			case EmailController::EMAIL_SCHEDULE_CAMPAIGN:
			case EmailController::EMAIL_UPDATE_CAMPAIGN_SCHEDULE:
				
				$recipients = $emailModel->broadcastHandlers();
					
			break;
			default;
				$recipients = [];
		}
		
		return $recipients;
	}
	
	
	
	
	private function generate($emailID, $data = [])
	{
		//Retrieve email informations
		$emailModel = new \Models\EmailModel();
		$emailInfos = $emailModel->infos($emailID);
		
		$user = \Objects\User::getInstance($emailInfos['recipient']);
		$userLocal = $user->getLocale();
		
		//Switch to recipient local
		$currentLocal = \__("local");
		\Library\Localization::init($userLocal);
		
		
		switch($emailInfos['type'])
		{
				
			case EmailController::EMAIL_NEW_CLIENT:
			case EmailController::EMAIL_CLIENT_UPDATE:
			case EmailController::EMAIL_CLIENT_NEW_PASSWORD:
				
				if($emailInfos['type'] == EmailController::EMAIL_NEW_CLIENT)
					$content = new View("emails/welcome");
				else if($emailInfos['type'] == EmailController::EMAIL_CLIENT_UPDATE)
					$content = new View("emails/clientUpdate");
				else if($emailInfos['type'] == EmailController::EMAIL_CLIENT_NEW_PASSWORD)
					$content = new View("emails/clientPasswordUpdate");
				
				$content->clientName = $user->getName();
				$content->clientEmail = $user->getEmail();
				
			break;
			case EmailController::EMAIL_REVIEW_AD:
				
				$ad = \Objects\Ad::getInstance($emailInfos['refID1']);
				
				//TODO: Ad Creative thumbnails
				//$creatives = $adModel->creatives();
				
				$content = new View("emails/reviewAd");
				$content->campaignName = $ad->getCampaign()->getName();
				$content->clientName = $ad->getUser()->getName();
				$content->adStartDate = $ad->getStartTime();
				$content->adEndDate = $ad->getEndTime();
				$content->supportName = $ad->getSupport()->getName();
				
			break;
			case EmailController::EMAIL_REVIEWED_AD:
			case EmailController::EMAIL_END_OF_DISPLAY:
				
				$ad = \Objects\Ad::getInstance($emailInfos['refID1']);
				
				if($emailInfos['type'] == EmailController::EMAIL_REVIEWED_AD)
				{
					$content = new View("emails/reviewedAd");
					
					$reviewModel = new \Models\ReviewModel();
					$review = $reviewModel->get($emailInfos['refID1']);
					$content->review = $review['status'];
					$content->reviewMessage = $review['comment'];
				}
				else if($emailInfos['type'] == EmailController::EMAIL_END_OF_DISPLAY)
					$content = new View("emails/endOfDisplay");
				
				$content->campaignName = $ad->getCampaign()->getName();
				$content->adStartDate = $ad->getStartTime();
				$content->adEndDate = $ad->getEndTime();
				
			break;
			case EmailController::EMAIL_SCHEDULE_CAMPAIGN:
			case EmailController::EMAIL_UPDATE_CAMPAIGN_SCHEDULE:
				
				$campaign = \Objects\Campaign::getInstance($emailInfos['refID1']);
			
				if($emailInfos['type'] == EmailController::EMAIL_SCHEDULE_CAMPAIGN)
					$content = new View("emails/newCampaignToSchedule");
				else if($emailInfos['type'] == EmailController::EMAIL_UPDATE_CAMPAIGN_SCHEDULE)
					$content = new View("emails/campaignUpdate");
				
				$content->broadcasterName = $campaign->getBroadcaster()->getName();
				$content->campaignName = $campaign->getName();
				$content->campaignStartDate = $campaign->getStartDate();
				$content->campaignEndDate = $campaign->getEndDate();
				$content->supportName = $campaign->getSupport()->getName();
				$content->displayFileURL = $campaign->getDisplayFileURL();
				
			break;
			default;
				$content = new View("emails/badEmail");
		}
		
		//Attach data;
		$content->data = $data;
		
		$body = new View("emails/body");
		$body->title = "Email de test";
		$body->content = $content->render();
		
		$renderedBody = $body->render();
		
		//Revery to user local
		\Library\Localization::init($currentLocal);
		
		return $renderedBody;
	}
	
	
	
	
	
	
	public function send($emailID, $data = [])
	{
		
		//gather data
		$emailModel = new \Models\EmailModel();
		
		$emailInfos = $emailModel->infos($emailID);
		
		$user = \Objects\User::getInstance($emailInfos['recipient']);
		$recipient = $user->getEmail();
		
		$emailBody = $this->generate($emailID, $data);
		
		//Construct the email
		$PHPMailer = new \PHPMailer\PHPMailer\PHPMailer();
		
		//SMTP CONFIG
		$params = json_decode(file_get_contents("Library/settings.json"), true)["smtp"];
		
		$PHPMailer->SMTPDebug = 0;                                 // Enable verbose debug output
		$PHPMailer->isSMTP();                                      // Set mailer to use SMTP
		$PHPMailer->Host = $params["host"];                        // Specify main and backup SMTP servers
		$PHPMailer->SMTPAuth = true;                               // Enable SMTP authentication
		$PHPMailer->Username = $params["username"];                // SMTP username
		$PHPMailer->Password = $params["password"];                // SMTP password
		$PHPMailer->SMTPSecure = $params["encryption"];            // Enable TLS encryption, `ssl` also accepted
		$PHPMailer->Port = $params["port"];
		
		//EMAIL CONFIG
        $PHPMailer->isHTML(true);
		$PHPMailer->setFrom('noreply@ad-direct.ca', 'Ad-Direct');
        $PHPMailer->Subject = \mb_encode_mimeheader(\__($emailInfos['type']));
        $PHPMailer->Body    = htmlspecialchars_decode(htmlentities($emailBody, ENT_NOQUOTES, "UTF-8"));
		$PHPMailer->addAddress($recipient);
		
		//TODO: Remove when online
		//echo $emailBody;
		
		//Send the emails
        $PHPMailer->send();	
	}
	
	public function display($emailUID)
	{
		$emailModel = new \Models\EmailModel();
		
		$emailID = $emailModel->getIDbyUID($emailUID);
		
		echo $this->generate($emailID);
	}
}