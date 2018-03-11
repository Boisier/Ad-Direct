<?php

namespace Objects;

use \Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use \Models\CreativeModel;

class Creative
{
	private $creativeID = NULL;
	private $creativeModel;
	
	
	/**
	 * Try to instantiate a Creative Object by its id or Combo ID
	 * @param  integer $creativeID Id of the creative
	 * @return mixed   a Screen object on success, false otherwise
	 */
	public static function getInstance($refID, $screenID = 0)
	{
		//Sanitize the creative ID
		$refID = \Library\Sanitize::int($refID);
		$screenID = \Library\Sanitize::int($screenID);
		
		//Do not instantiate if equal zero
		if($refID == 0)
			return false;
		
		$creativeModel = new CreativeModel();
		
		//Verify if creative exist
		if($screenID == 0)
		{
			//simple creative ID
			if(!$creativeModel->creativeExist($refID))
				return false;
		
			//Instantiate the creative
			return new Creative($refID);
		}
		
		//Combo ID
		if(!$creativeModel->setCreativeByComboID($refID, $screenID))
			return false;
		
		//Instantiate the creative
		return new Creative($creativeModel->id());
	}
	
	
	
	
	
	/**
	 * Set the creative ID
	 * @private
	 * @param integer $creativeID the creative ID
	 */
	private function __construct($creativeID)
	{
		$this->creativeID = $creativeID;
		$this->creativeModel = new CreativeModel($creativeID);
	}
	
	
	
	
	
	
	
	/**
	 * Give the ID of the screen
	 * @return integer The ID of the screen
	 */
	public function getID()
	{
		return $this->creativeID;
	}

	
	
	
	
	
	public function getAdID()
	{
		return $this->creativeModel->getAdID();
	}

	
	
	
	
	
	public function getAd()
	{
		return Ad::getInstance($this->creativeModel->getAdID());
	}
	
	
	
	
	
	
	public function getPath($rooted = false)
	{	
		return $this->creativeModel->path($rooted);
	}
	
	
	
	/**
	 * Return the path to the creative's thumbnail
	 *
	 * PNG checks are not here to stay. It is to ensure transition from png thumbs to jpg thumbs.
	 * TODO : Remove png support starting from mars/april 2018
	 *
	 * @param bool $rooted
	 * @param bool $checkFile
	 * @return string
	 */
	public function getThumbPath($rooted = false, $checkFile = true)
	{
		$pathPNG = "thumbs/{$this->creativeID}.png";
		$pathJPG = "thumbs/{$this->creativeID}.jpg";
		
		$existPNG = file_exists($pathPNG);
		$existJPG = file_exists($pathJPG);
		
		$path = $pathJPG;
		
		if($checkFile && $existPNG)
		{
			unlink($pathPNG);
			$existPNG = false;
		}
		
		if($checkFile && !$existPNG && !$existJPG)
		{
			$this->createThumbnail();
			
			if(!file_exists($existJPG))
				$path = "assets/images/thumbPlaceholder.png";
		}
		
		if($rooted)
			$path = "/$path";
		
		return $path;
	}
	
	
	
	
	
	public function getConvertedPath($rooted = false)
	{
		//There is no conversion if it is an image
		if($this->getMediaType() == 1)
			return false;
		
		return $this->creativeModel->getConvertedVideoPath(false);
	}
	
	
	
	
	
	public function getOriginalPath($rooted = false)
	{
		//There is no conversion if it is an image
		if($this->getMediaType() == 1)
			return false;
		
		return $this->creativeModel->getOriginalVideoPath(false);
	}

	
	
	
	
	
	public function getMediaType()
	{
		return $this->creativeModel->getMediaType();
	}

	
	
	
	
	
	public function getScreenID()
	{
		return $this->creativeModel->getScreenID();
	}

	
	
	
	
	
	public function getUploadTime()
	{
		return $this->creativeModel->getUploadTime();
	}

	
	
	
	
	
	public function getScreen()
	{
		return Screen::getInstance($this->creativeModel->getScreenID());
	}
	
	
	
	
	public function getStatus()
	{
		return $this->creativeModel->getStatus();
	}
	
	
	
	
	public function getConversionStatus()
	{
		return $this->creativeModel->getConversionStatus();
	}

	
	
	
	
	
	public function getUploader()
	{
		return User::getInstance($this->creativeModel->getUploaderID());
	}

	
	
	
	
	
	public function getSize()
	{
		return filesize($this->getPath(false));
	}
	
	
	
	
	
	public function setStatus($status)
	{
		$this->creativeModel->setStatus($status);
	}
	
	
	
	
	
	
	public function updateExtension($newExtension)	
	{
		$this->creativeModel->setNewExtension($newExtension);
	}
	
	
	
	
	public function setConversionStatus($percentage)
	{
		$this->creativeModel->setConversionStatus($percentage);
	}
	
	
	/**
	 * Create the thumbnail of the creative
	 */
	public function createThumbnail()
	{
		switch($this->getMediaType())
		{
			case "1":
				$this->createImageThumbnail();
			break;
			case "2":
				$this->createVideoThumbnail();
			break;
		}
	}
	
	
	/**
	 * Create the thumbnail of image creative
	 */
	private function createImageThumbnail()
	{
		$size = getimagesize($this->getPath());
		
		$ratio = min(1280/$size[0], 1280/$size[1]); // width/height
		
		$width = $size[0] * $ratio;
		$height = $size[1] * $ratio;
		
		$src = imagecreatefromstring(file_get_contents($this->getPath(false)));
		$thumb = imagecreatetruecolor($width,$height);
		
		imagecopyresampled($thumb, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
		imagedestroy($src);
		
		imagejpeg($thumb, $this->getThumbPath(false, false));
		imagedestroy($thumb);
	}
	
	
	/**
	 * Try to create the thumbnail of video creative
	 */
	private function createVideoThumbnail()
	{
		
		$ffmpeg = \Library\FFMpeg::getFFMpegInstance();
		
		//thumbnail
		try {
			$video = $ffmpeg->open($this->getPath());
			$frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(0));
			$frame->save($this->getThumbPath(false, false));
		}
		catch(\Exception $e) { unset($e); }
		//Couldn't create thumbnail, another conversion must be running.
	}
	
	
	/**
	 * Remove the creative
	 * Do not use save after
	 */
	public function delete()
	{	
		
		//Remove files
		$path = $this->getPath(false);
		if(file_exists($path))
			unlink($path);
		
		$thumbPath = $this->getThumbPath(false, false);
		if(file_exists($thumbPath))
			unlink($thumbPath);
		
		$originalPath = $this->getOriginalPath(false);
		if(file_exists($originalPath))
			unlink($originalPath);
		
		//Delete the creative
		$this->creativeModel->delete();
	}
}
