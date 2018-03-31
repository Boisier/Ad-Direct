<?php

namespace Controllers;

use Library\Params;

class HomeController
{
	public function main ()
	{
		//Visitor index if not logged in
		if (!\Library\User::loggedIn()) {
			$this->visitorIndex();
			return;
		}

		$user = \Objects\User::getInstance(\Library\User::id());

		//Force user to review legals if needed
		if (!$user->isAdmin() && !$user->hasApprovedLegal()) {
			$page = $this->reviewLegal();
			return;
		}

		$page = new \Library\Composer();

		/*Head -- includes*/
		$head = new \Library\View("head");

		/*Header -- topBar*/
		$header = new \Library\View("common/header");

		$header->userName = $user->getName();


		/*MainPage*/
		if (\Library\User::isAdmin())
			$body = $this->adminHome();
		else
			$body = $this->clientHome();

		/*Modals and footer*/
		$footer = new \Library\View("common/footer");

		/*Build & print*/
		$page->attach($head)
			->attach($header)
			->attach($body)
			->attach($footer);

		echo $page->render();
	}

	private function visitorIndex ()
	{
		$page = new \Library\Composer();

		$head = new \Library\View("head");
		$body = new \Library\View("visitor/login");
		$footer = new \Library\View("visitor/footer");

		$page->attach($head)
			->attach($body)
			->attach($footer);

		echo $page->render();
	}

	private function reviewLegal ()
	{
		$page = new \Library\Composer();

		$head = new \Library\View("head");
		$body = new \Library\View("users/legal");
		$body->userID = \Library\User::id();
		$footer = new \Library\View("visitor/footer");

		$page->attach($head)
			->attach($body)
			->attach($footer);

		echo $page->render();
	}

	private function adminHome ()
	{
		$view = new \Library\View("admin/home");

		$userModel = new \Models\UserModel(\Library\User::id());
		$view->privileges = $userModel->privileges("justNames");

		return $view;

	}

	private function clientHome ()
	{
		$user = \Objects\User::getInstance(\Library\User::id());

		$view = new \Library\View("client/home");
		$view->campaigns = $user->getCampaigns(\Library\User::id());

		return $view;

	}

	public function legals ($legalPage)
	{
		$view = new \Library\View("client/legalText");
		$view->legalName = \Library\Sanitize::string($legalPage);

		echo $view->render();
	}


	public function overview ()
	{
		\Library\User::onlyLoggedIn();

		if (\Library\User::isAdmin())
			$page = new \Library\View("admin/overview");
		//$page = new \Library\View("common/badAction");
		else
			$page = $this->clientOverview();

		echo $page->render();
	}


	public function clientOverview ()
	{
		$page = new \Library\View("client/overview");

		return $page;
	}


	public function defaultads ()
	{
		$page = new \Library\View("client/defaultAdsOverview");

		$user = \Objects\User::getInstance(\Library\User::id());
		$broadcasterID = $user->getBroadcasterID();

		$defaultAdController = new DefaultadController();

		$page->defaultAds = $defaultAdController->home($broadcasterID);

		echo $page->render();
	}


	public function clients ()
	{
		\Library\User::onlyAdmins();

		$page = new \Library\View("broadcasters/home");

		$broadcasterModel = new \Models\BroadcasterModel();

		$broadcasterGroupModel = new \Models\BroadcasterGroupModel();
		$page->broadcasterGroups = $broadcasterGroupModel->getAll();

		$broadcasters = $broadcasterModel->getAll();

		if (count($broadcasters) == 0) {
			$view = new \Library\View("broadcasters/noBroadcaster");
			$page->broadcastersList = $view->render();

			echo $page->render();
			return;
		}

		$broadcasterList = new \Library\Composer();

		foreach ($broadcasters as $broadcaster) {
			$view = new \Library\View("broadcasters/broadcasterList");

			$view->broadcasterID = $broadcaster->getID();
			$view->broadcasterName = $broadcaster->getName();
			$view->nbrClients = $broadcaster->getNbrClients();
			$view->nbrCampaigns = $broadcaster->getNbrCampaigns();
			$view->groupID = $broadcaster->getGroupID();

			$view->status = $broadcaster->getStatus();

			$broadcasterList->attach($view);
		}

		$page->broadcastersList = $broadcasterList->render();
		echo $page->render();
	}


	public function params ()
	{
		\Library\User::onlyAdmins();

		$page = new \Library\View("params/home");

		$user = \Objects\User::getInstance(\Library\User::id());
		$page->privileges = $user->getPrivileges("justNames");

		echo $page->render();
	}
}
