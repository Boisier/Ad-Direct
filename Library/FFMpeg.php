<?php

namespace Library;

class FFMpeg
{
	public static function getFFMpegInstance()
	{
		$logger = new Logger("ffmpeg.log", "ffmpeg");
		
		return \FFMpeg\FFMpeg::create(self::getOptions(), $logger);
	}
	
	public static function getFFProbeInstance()
	{
		$logger = new Logger("ffmpeg.log", "ffprobe");
		
		return \FFMpeg\FFProbe::create(self::getOptions(), $logger);
	}
	
	
	private static function getOptions()
	{
		$params = json_decode(file_get_contents("Library/settings.json"), true)["ffmpeg"];
		
		$options = [];
		
		if(isset($params["ffmpeg"]) && !empty($params["ffmpeg"]))
			$options["ffmpeg.binaries"] = $params["ffmpeg"];
		
		if(isset($params["ffprobe"]) && !empty($params["ffprobe"]))
			$options["ffprobe.binaries"] = $params["ffprobe"];
		
		$options["timeout"] = 3600;
		
		return $options;
	}
	
	
	public static function fracToFloat($frac)
	{
		$numbers = explode("/", $frac);
		return round($numbers[0] / $numbers[1], 6);
	}
}
