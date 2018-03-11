<?php

namespace Controllers;

use \Library\View,
	\Library\Composer;

class mediaTypeController
{
	public function home()
	{
		\Library\User::restricted("EDIT_SUPPORTS");
		
		$mediaTypeModel = new \Models\MediaTypeModel();
		
		$mediaTypes = $mediaTypeModel->getAll();
		
		$list = new Composer();
		
		foreach($mediaTypes as $mediaType)
		{
			$mediaView = new View("mediaTypes/mediaList");
			$mediaView->mediaID = $mediaType['ID']; 	
			$mediaView->mediaName = $mediaType['name'];
			
			$mimes = $mediaTypeModel->getMimes($mediaType['ID']);
			$mediaView->mimesList = implode(", ", $mimes);
			
			$list->attach($mediaView);
		}
		
		$view = new View("mediaTypes/home");
		$view->mediaList = $list->render();
		
		echo $view->render();
	}
	
	
	public function form($formName, $mediaID = 0)
	{
		\Library\User::restricted("EDIT_SUPPORTS");
		
		switch($formName)
		{
			case "create":
				
				$form = new View("mediaTypes/add");
				
			break;
			case "edit":
				
				$mediaID = \Library\Sanitize::int($mediaID);
				$mediaTypeModel = new \Models\MediaTypeModel($mediaID);
				
				$form = new View("mediaTypes/edit");
				
				$form->mediaID = $mediaID;
				$form->mediaName = $mediaTypeModel->name();
				$form->mimes = $mediaTypeModel->getMimes($mediaID);
				
			break;
			case "delete":
				
				$mediaID = \Library\Sanitize::int($mediaID);
				$mediaTypeModel = new \Models\MediaTypeModel($mediaID);
				
				$form = new View("mediaTypes/delete");
				
				$form->mediaID = $mediaID;
				$form->mediaName = $mediaTypeModel->name();
				
			break;
		}
		
		echo $form->render();
	}
	
	
	public function create()
	{
		\Library\User::restricted("EDIT_SUPPORTS");
		
		if(empty($_POST['name']))
		{
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$mediaName = \Library\Sanitize::string($_POST['name']);
		
		$mediaTypeModel = new \Models\MediaTypeModel();
		
		if($mediaTypeModel->mediaTypeExistName($mediaName))
		{
			http_response_code(400);
			echo "alreadyExist";
			return;
		}
		
		$mediaID = $mediaTypeModel->create($mediaName);
		
		if(empty($_POST['mimeList']))
		{
			$this->home();
			return;
		}
		
		$mediaTypeModel->setMediaType($mediaID);
		
		foreach($_POST['mimeList'] as $mime)
		{
			$mime = \Library\Sanitize::mimeType($mime);
			
			if($mime == false)
				continue;
			
			$mediaTypeModel->addMime($mime);
		}
		
		$this->home();
	}
	
	public function update($mediaID)
	{
		\Library\User::restricted("EDIT_SUPPORTS");
		
		if(empty($_POST['name']))
		{
			http_response_code(400);
			echo "missingField";
			return;
		}
		
		$mediaID = \Library\Sanitize::int($mediaID);
		$mediaName = \Library\Sanitize::string($_POST['name']);
		
		$mediaTypeModel = new \Models\MediaTypeModel($mediaID);
		$currentName = $mediaTypeModel->name();
		
		if($mediaName != $currentName && $mediaTypeModel->mediaTypeExistName($mediaName))
		{
			http_response_code(400);
			echo "alreadyExist";
			return;
		}
		
		if($mediaName != $currentName)
			$mediaTypeModel->setName($mediaName);
		
		$mediaTypeModel->clearMimes();
		
		if(empty($_POST['mimeList']))
		{
			$this->home();
			return;
		}
		
		foreach($_POST['mimeList'] as $mime)
		{
			$mime = \Library\Sanitize::mimeType($mime);
			
			if($mime == false)
				continue;
			
			$mediaTypeModel->addMime($mime);
		}
		
		$this->home();
	}
	
	public function delete($mediaID)
	{
		\Library\User::restricted("EDIT_SUPPORTS");
		
		$mediaID = \library\Sanitize::int($mediaID);
		
		$mediaTypeModel = new \Models\MediaTypeModel($mediaID);
		
		//Check if media is currently in use
		if($mediaTypeModel->isUsed())
		{
			$view = new View("mediaTypes/cannotDeleteMedia");
			$view->mediaName = $mediaTypeModel->name();
			
			echo $view->render();
			
			return;
		}
		
		//Not used, let's go!
		$mediaTypeModel->clearMimes();
		$mediaTypeModel->delete();
		
		$this->home();
	}
}