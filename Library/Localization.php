<?php

namespace Library
{
	class Localization
	{
		public static $strings = NULL;
		public static $path;
		
		/**
		 * @var \Objects\User
		 */
		private static $user;

		public static function init($lang)
		{
			//Set lang & locale
			self::$path = "Library/Localization/";

			if(!file_exists(self::$path.$lang.".json"))
				$lang = "en_EN";

			self::$strings = json_decode(file_get_contents(self::$path.$lang.".json"), true);
			
			//set timezone
			if(!User::loggedIn())
				return;
			
			self::$user = \Objects\User::getInstance(User::id());
			date_default_timezone_set(self::$user->getTimezone());
		}

		public static function get($textID)
		{
			if(self::$strings == NULL || !array_key_exists($textID, self::$strings))
				return "[[ ".$textID." ]]";

			return self::$strings[$textID];
		}
		
		public static function dateFormat()
		{
			switch(\__("local"))
			{
				case "fr_FR":
					$format = "d/m/Y H:i";
				break;
				case "en_EN":
				case "en_US":
				case "en_UK":
					$format = "d/m/Y h:i a";
				break;
				case "en_CA":
					$format = "Y-m-d h:i a";
				break;
				case "fr_CA":
					$format = "Y-m-d H:i";
				break;
				default;
					$format = "d/m/Y H:i";
			}
			
			return $format;
		}
		
		public static function getTimezones()
		{
			return json_decode(file_get_contents(self::$path."timezones.json"), true);
		}
		
		public static function getCurrentTimezone()
		{
			return self::$user->getTimezone();
		}
		
		public static function timezoneExists($timezone)
		{
			return in_array($timezone, self::getTimezones());
		}
	}
}

namespace 
{
	function __($textID, array $labels = [])
	{
		$text = Library\Localization::get($textID);
		
		foreach($labels as $labelID => $label)
		{
			$text = str_replace("%(".$labelID.")", $label, $text);
		}
		
		return $text;
	}
}