<?php

namespace Controllers;

use FFMpeg\Exception\RuntimeException;
use \Library\View,
	\Library\Sanitize;
use Objects\Ad;
use Objects\Record;

class CreativeController
{
	const CREATIVE_KO = 0;
	const CREATIVE_OK = 1;
	const CREATIVE_PROCESSING = 2;
	const CREATIVE_NEED_CONVERT = 3;
	const CREATIVE_CONVERTING = 4;
	const CREATIVE_NEED_CONVERT_RETRY = 5;
	
	
	
	
	
	
	/**
	 * Print the needed form to interact with the creative
	 * @param string  $formName The name of the form
	 * @param integer $adID     The Combo ID that
	 * @param integer $screenID identify the creative
	 */
	public function form($formName, $adID, $screenID)
	{
		$adID = Sanitize::int($adID);
		$screenID = Sanitize::int($screenID);
		
		switch($formName)
		{
			case "delete":
				
				$form = new View("modals/deleteCreative");
				
				$creativeModel = new \Models\CreativeModel();
				$creativeModel->setCreativeByComboID($adID, $screenID);
				
				$form->creativeID = $creativeModel->id();
				
			break;
		}
		
		echo $form->render();
	}
	
	
	
	
	/**
	 * Add a new creative
	 * @param integer $adID     The Ad the creative belong to
	 * @param integer $screenID The screen the creative is printed on
	 */
	public function add($adID, $screenID)
	{
		$_record = Record::createRecord(Record::CREATIVE_UPLOADED);
		
		//Authorizations
		if(!\Library\User::canEditAd($adID))
		{
			$_record->setResult(Record::UNAUTHORIZED)
					->save();
		}
		
		//Do we have the file?
		if(!array_key_exists("creative", $_FILES))
		{
			$_record->setResult(Record::FATAL_ERROR)
					->setMessage("Missing creative")
					->save();
			
			//display errors
			$errorModal = new View("modals/uploadErrors");
			$errorModal->errors = ["NO_FILE"];
			
			header('Content-Type: application/json');
			echo json_encode(["success" => false, "html" => $errorModal->render()]);
			
			return;
		}
		
		
		//Sanitizing things
		$adID = Sanitize::int($adID);
		$screenID = Sanitize::int($screenID);
		
		$creativeModel = new \Models\CreativeModel();
		
		//Is there already an ad here ?
		if($creativeModel->existComboID($adID, $screenID))
		{
			$_record->setResult(Record::REFUSED)
					->setMessage("Creative already present")
					->save();
			
			return; //Cannot add if there's already something here
			//So we just stop the upload here, without doing anything.
		}
		
		
		$ad = \Objects\Ad::getInstance($adID);
		$specs = $ad->getScreenSpecs($screenID);
		
		//Parse the file, look for errors,
		//and make sure it fits the screen and campaigns specs.
		$errors = $this->controlCreative($_FILES['creative'], $specs);
		
		//Treat any errors
		if(count($errors) != 0)
		{
			$_record->setResult(Record::REFUSED)
					->setMessage("Creative did not match format")
					->save();
			
			//display errors
			$errorModal = new View("modals/uploadErrors");
			$errorModal->errors = $errors;
			$errorModal->specs = $specs;
			
			header('Content-Type: application/json');
			echo json_encode(["success" => false, "html" => $errorModal->render()]);
			
			return;
		}
		
		
		//The file is OK
		
		
		//Let's start by extracting it's name and extension
		$pathInfo = pathinfo($_FILES['creative']["name"]);
		$creativeName = $pathInfo['filename'];
		$creativeExtension = $pathInfo['extension'];
		$creativeMediaType = $this->getCreativeMediaType($_FILES['creative']['tmp_name']);
		
		//registering it in the DDB
		$creative = $creativeModel->create($adID, $screenID, $creativeName, $creativeMediaType, $creativeExtension, $_FILES['creative']['tmp_name']);
		
		//Create thumnails, check if it needs to be converted, reviewed, etc.
		$this->processCreative($creative, $creativeMediaType);

		//And return what to show in place on the ad block
		$adController = new \Controllers\AdController();
		
		$_record->setResult(Record::OK)
				->save();
		
		header('Content-Type: application/json');
		echo json_encode(["success" => true, "html" => $adController->buildBlock($ad->getID())->render()]);
	}
	
	
	
	
	
	/**
	 * Control a creative to make sure it fits the specs
	 * @param $file
	 * @param $specs
	 * @return array
	 */
	public function controlCreative($file, $specs)
	{
		$errors = [];
		
		//Error while uploading ?
		if($file['error'] != 0)
		{
			array_push($errors, "ERROR_UPLOAD");
			return $errors;
		}
		
		
		//Check file size
		$sizeInBytes = 1048576 * $specs['sizeLimit'];
		
		if($file['size'] > $sizeInBytes)
			array_push($errors, "TOO_HEAVY");
		
		//Validate media type
		$creativeMediaType = $this->getCreativeMediaType($_FILES['creative']['tmp_name']);
		
		//If creative media type is not supported by ad ad-direct, or is not supported by this screen
		if($creativeMediaType == 0 || ($creativeMediaType != $specs['mediaType'] && $specs['mediaType'] != 0))
		{
			array_push($errors, "WRONG_MIME");
			return $errors;
		}
		
		
		//MORE Treatments according to media type
		switch($creativeMediaType)
		{
			case 1:
				//This is a picture
				
				//verify it's dimentions
				$dimensions = getimagesize($file['tmp_name']);
				$ratio = $dimensions[0] / $dimensions[1];
				
				if($ratio != $specs['width'] / $specs['height'] || //Aspect ration
					$dimensions[0] < $specs['width'] || //Minimum dimensions
					$dimensions[1] < $specs['height'] ||
					$dimensions[0] > 2 * $specs['width'] || //Maximum dimensions
					$dimensions[1] > 2 * $specs['height'])
					array_push($errors, "BAD_DIMENSIONS");
				
			break;
			case 2:
				
				//This is a video
				$ffprobe = \Library\FFMpeg::getFFProbeInstance();
				$authorizedCodecs = \Library\Params::get("AUTHORIZED_CODECS");
				$framerates = \Library\Params::get("ACCEPTED_FRAMERATES");
				
				
				//Retrieve video infos
				$videoStream = $ffprobe->streams($file['tmp_name'])->videos()->first(); //Select the video
				$fileInformations = $ffprobe->format($file['tmp_name']);
				
				/*print_r(["codec_name" => $videoStream->get("codec_name"),
						 "width" => $videoStream->get("width"),
						 "height" => $videoStream->get("height"),
						 "frame_rate" => \Library\FFMpeg::fracToFloat($videoStream->get("r_frame_rate")),
						 "duration" => $fileInformations->get("duration")]);*/
				
				//Check video codec
				if(!in_array($videoStream->get("codec_name"), $authorizedCodecs))
					array_push($errors, "BAD_CODEC");
				
				
				//Check video dimensions
				if($videoStream->get("width") != $specs['width'] || $videoStream->get("height") != $specs['height'])
					array_push($errors, "BAD_DIMENSIONS");
				
				
				//Check framerate
				$framerate = \Library\FFMpeg::fracToFloat($videoStream->get("r_frame_rate"));
				if($framerate < 23.9 || $framerate > 30)
                	array_push($errors, 'BAD_FRAMERATE');
				
				
				//Check length
				$maxDuration = $specs["displayDuration"] + 1; //Add 1 second offset
				
				if($fileInformations->get("duration") > $maxDuration)
                	array_push($errors, 'TOO_LONG');
				
			break;
		}
		
		return $errors;
	}
	
	
	
	
	
	private function getFileMimeType($path)
	{
		$finfo = \finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $path);
		finfo_close($finfo);
		
		return $mime;
	}
	
	
	
	
	
	
	
	private function getCreativeMediaType($path)
	{
		$mimeType = $this->getFileMimeType($path);
		
		$mediaTypeModel = new \Models\MediaTypeModel();
		return $mediaTypeModel->getMimeMediaType($mimeType);
	}
	
	
	
	
	
	
	/**
	 * @param \Objects\Creative $creative
	 * @param int $creativeMediaType
	 */
	private function processCreative($creative, $creativeMediaType)
	{
		//Create thumbnail for the creative
		$creative->createThumbnail();
		
		//Set status depending on media type
		switch($creativeMediaType)
		{
			case 1: //Picture
				$creative->setStatus(self::CREATIVE_OK);
			break;
			case 2: //video
				$creative->setStatus(self::CREATIVE_NEED_CONVERT);
			break;
		}
		
		//Ad the review if needed
		$reviewController = new ReviewController();
		$reviewController->createReview($creative->getAdID());
	}
	
	
	
	
	
	/**
	 * Create the modal to display the creative
	 * @param integer $adID     Combo ID
	 * @param integer $screenID
	 */
	public function display($adID, $screenID)
	{
		//Authorizations
		if(!\Library\User::canEditAd($adID))
			return;
		
		$adID = Sanitize::int($adID);
		$screenID = Sanitize::int($screenID);
		
		$creative = \Objects\Creative::getInstance($adID, $screenID);
		
		$view = new View("modals/displayCreative");
		$view->creativePath = $creative->getPath(true);
		$view->creativeOriginalPath = $creative->getOriginalPath(true);
		$view->creativeMediaType = $creative->getMediaType();
		
		echo $view->render();
	}
	
	
	
	
	
	/**
	 * Delete the creative
	 * @param integer $creativeID The creative to delete
	 */
	public function delete($creativeID)
	{
		$creativeID = Sanitize::int($creativeID);
		
		$_record = Record::createRecord(Record::CREATIVE_REMOVED);
		$_record->setRef1($creativeID);
		
		$creative = \Objects\Creative::getInstance($creativeID);
		$ad = $creative->getAd();
		
		//Authorizations
		if(!\Library\User::canEditAd($ad->getID()))
		{
			$_record->setResult(Record::UNAUTHORIZED)
					->save();
			
			return;
		}
		
		//Remove any review for the creative's ad
		$reviewModel = new \Models\ReviewModel();
		$reviewModel->remove($ad->getID());
		
		//Make the creative remove itself
		$creative->delete();
		
		$_record->setResult(Record::OK)
				->save();
		
		$campaignController = new CampaignController();
		$campaignController->display($ad->getCampaign()->getID());
	}
}

