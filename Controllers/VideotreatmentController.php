<?php

namespace Controllers;

use Objects\Record;

class VideotreatmentController
{
	public function processVideos ()
	{
		echo "Starting video treatment<br>";
		
		$videoTreatmentModel = new \Models\VideoTreatmentModel();
		
		//FIrst, check if there is already a video beiing converted
		if ($videoTreatmentModel->alreadyConverting()) {
			echo "A video is already converting.<br>";
			echo "Check for error<br>";
			
			if (time() - \Library\Params::get("LAST_CONVERSION_START") < 1800) {
				echo "No errors. END<br>";
				return;
			}
			echo "error in convert, reset video status";
			
			$videoID = $videoTreatmentModel->getConvertingCreative();
			$convertingVideo = \Objects\Creative::getInstance($videoID);
			$convertingVideo->setStatus(\Controllers\CreativeController::CREATIVE_NEED_CONVERT_RETRY);
		}
		
		//No video currently converting, do we need to convert any video
		$videosID = $videoTreatmentModel->getCreativesAwaitingConversion();
		
		$paramModel = new \Models\ParamModel();
		$paramModel->updateGlobal("LAST_CONVERSION_START", time());
		
		//Is there any video to convert
		if (count($videosID) == 0) {
			echo "There is no video to convert. END<br>";
			return;
		}
		
		$_record = Record::createRecord(Record::CREATIVE_TRANSCODED);
		$_record->setRef1($videosID[0]);
		
		//Only treat the first video, the other will be treated after.
		$creative = \Objects\Creative::getInstance($videosID[0]);
		
		echo "Selecting video...<br>";
		
		//Mrk creative as being converted
		$creative->setStatus(\Controllers\CreativeController::CREATIVE_CONVERTING);
		echo "Locking conversion window.<br>";
		
		//Retrieve the conversion parameters
		$sourcePath = $creative->getPath();
		$destinationPath = $creative->getConvertedPath();
		$format = $this->getDestinationFormat($creative);
		
		//Use this to ensure thumbnail creation
		$creative->getThumbPath(false, true);
		
		@unlink($destinationPath);
		
		echo "Starting conversion.<br>";
		$startTime = time();
		
		try {
			//Load ffmpeg and the video
			$ffmpeg = $ffmpeg = \Library\FFMpeg::getFFMpegInstance();
			$video = $ffmpeg->open($sourcePath);
			
			//Convert the video
			$video->save($format, $destinationPath);
		} catch (\Exception $e) {
			//Conversion failed
			//Mark as retry
			
			echo "Conversion failed on process. END<br>";
			$creative->setStatus(\Controllers\CreativeController::CREATIVE_NEED_CONVERT_RETRY);
			return;
		}
		
		//Conversion is over
		$endTime = time();
		
		//Check conversion is OK
		//Check VIA file size
		if (filesize($destinationPath) == 0) {
			//File size is empty, the conversion needs to be done again
			echo "Conversion failed by file size. END<br>";
			$creative->setStatus(\Controllers\CreativeController::CREATIVE_NEED_CONVERT_RETRY);
			return;
		}
		
		echo "Conversion was successful.<br>";
		
		//Update the video extension and release the conversion slot
		$creative->updateExtension("webm");
		$creative->setStatus(\Controllers\CreativeController::CREATIVE_OK);
		
		echo "Unlocking conversion window.<br>";
		
		//Make sure this video has a thumbnail
		$creative->getThumbPath(false, true);
		
		chmod($destinationPath, 0777);
		
		$duration = $endTime - $startTime;
		
		$_record->setResult(Record::OK)
			->save();
		
		echo "Conversion time : $duration s";
	}
	
	
	private function getDestinationFormat ($creative)
	{
		$format = new \FFMpeg\Format\Video\WebM();
		
		//Add the progress monitor
		$format->on('progress', function ($video, $format, $percentage) use ($creative) {
			//echo "$percentage % transcoded<br>";
			$creative->setConversionStatus($percentage);
		});
		
		//We don't want any audio
		//TODO: Remove audio
		
		$format->setAdditionalParameters(array('-an', '-qmax', '15'));
		
		return $format;
	}
}
