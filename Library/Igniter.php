<?php

/** Ignite the app with defaults settings **/

namespace Library;

class Igniter
{
	public static function ignite()
	{
		self::ddbConnection();
		self::sessions();
		self::params();
		self::lang();
		self::debug();
		self::headers();
	}

	
	
	public static function sessions()
	{
		Session::open();
	}
	
	
	/**
	 * Set language
	 */
	public static function lang()
	{
		if(isset($_GET['lang']))
		{
			$lang = $_GET['lang'];
		}
		else if(Cookie::read("lang") == false)
		{
			$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);
			$lang = str_replace("-", "_", $lang);
		}
		else
		{
			$lang = Cookie::read("lang");
		}
		
		Cookie::set("lang", $lang, time()+3600*24*365);
		
		//Set encoding as UTF8 to prevent invalid characters
		setlocale(LC_ALL, $lang.".utf8");
		Localization::init($lang);
		
		//Save user current langage if logged in
		if(User::loggedIn())
		{
			$ddb = DBA::get();
			$stmt = $ddb->prepare("
				UPDATE 
					users 
				SET 
					user_local = :local
				WHERE
					user_id = :id
			");
			
			$stmt->execute([":local" => \__("local"), 
						    ":id" => User::id()]);
		}
	}
	
	
	
	
	public static function params()
	{
		//PARAMS::init();
	}
	
		
	
	/**
	 * Set up the connection to the database
	 */
	public static function ddbConnection()
	{
		DBA::set(json_decode(file_get_contents("Library/settings.json"), true)['database']);
	}
	
	
	
	
	public static function debug()
	{
		$debug = json_decode(file_get_contents("Library/settings.json"), true)['debug'];
		
		if($debug)
		{
			error_reporting(E_ALL);
			ini_set('display_errors', 'On');
		}
		else
		{
			error_reporting(0);
			ini_set('display_errors', 'Off');
		}
		
		define("DEBUG", $debug);
	}
	
	public static function headers()
	{
		header('Content-Type: text/html; charset=UTF-8');
		http_response_code(200);
	}
}