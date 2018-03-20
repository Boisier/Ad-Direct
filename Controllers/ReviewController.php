<?php

namespace Controllers;

use \Library\View,
	\Library\Composer,
	\Library\Sanitize;
use Models\EmailModel;

class ReviewController
{
	const AD_INCOMPLETE = 0;
	const AD_PENDING = 1;
	const AD_APPROVED = 2;
	const AD_REJECTED = 3;
	const AD_AUTO_APPROVED = 4;
	
	
	/**
	 * Print the needed form to interact with the broadcaster
	 * @param string $formName The name of the form
	 * @param integer [$broadcasterID      = 0] The broadcaster ID
	 */
	public function form ($formName, $adID, $action = 0)
	{
		\Library\User::restricted("APPROVE_CREATIVES");
		
		switch ($formName) {
			case "review":
				
				//Get the view
				$form = new \Library\View("reviews/review");
				
				//Retrieve needed infos
				$adID = \Library\Sanitize::int($adID);
				$action = \Library\Sanitize::string($action);
				
				//Attach the infos
				$form->adID = $adID;
				$form->action = $action;
			
			break;
		}
		
		echo $form->render();
	}
	
	
	/**
	 * Insert a new review, if possible, for the given ad
	 * @param integer $adID The ad to ork with
	 */
	public function createReview ($adID)
	{
		//First let's see if the ad is complete
		$ad = \Objects\Ad::getInstance($adID);
		
		$nbrScreens = $ad->getNbrScreens();
		$nbrCreatives = $ad->getNbrCreatives();
		
		if ($nbrCreatives != $nbrScreens)
			return; //Ads are missing, no review for now
		
		//The Ad is complete
		//Define the review status
		$reviewStatus = ReviewController::AD_PENDING;
		$reviewerID = NULL;
		$reviewedDate = 0;
		
		if (\Library\User::isAdmin()) {
			$reviewStatus = ReviewController::AD_APPROVED;
			$reviewerID = \Library\User::id();
			$reviewedDate = time();
		} else if (\Library\User::restricted("TRUSTED_CLIENT", true)) {
			$reviewStatus = ReviewController::AD_AUTO_APPROVED;
			$reviewerID = \Library\User::id();
			$reviewedDate = time();
		}
		
		$reviewModel = new \Models\ReviewModel();
		$reviewModel->create($adID, $reviewStatus, $reviewerID, $reviewedDate);
		
		if ($reviewerID == NULL) {
			$emailController = new EmailController();
			$emailController->create(EmailController::EMAIL_REVIEW_AD, $adID);
		}
	}
	
	
	public function validate ()
	{
		\Library\User::restricted("APPROVE_CREATIVES");
		
		//Exclude any possible errors
		if (empty($_POST['adID']) || empty($_POST['action'])) {
			http_response_code(400);
			echo "fatalError";
			return;
		}
		
		$adID = Sanitize::int($_POST['adID']);
		$action = Sanitize::string($_POST['action']);
		$comment = Sanitize::string($_POST['comment']);
		
		if ($action == "approve")
			$newStatus = self::AD_APPROVED;
		else if ($action == "reject")
			$newStatus = self::AD_REJECTED;
		else {
			http_response_code(400);
			echo "fatalError";
			return;
		}
		
		$reviewModel = new \Models\ReviewModel();
		$reviewModel->validate($adID, $newStatus, $comment);
		
		$ad = \Objects\Ad::getInstance($adID);
		
		if ($ad == false) {
			$homeController = new HomeController();
			$homeController->clients();
		}
		
		$campaignID = $ad->getCampaignID();
		
		$emailController = new EmailController();
		$emailController->create(EmailController::EMAIL_REVIEWED_AD, $adID);
		
		$campaignController = new CampaignController();
		$campaignController->display($campaignID);
	}
}
