<?php

namespace Library;

class DBA
{
	private static $dbType = NULL;
	private static $dbName = NULL;
	private static $host = NULL;
	private static $user = NULL;
	private static $password = NULL;
		
	public static function set($parameters)
	{
		self::$dbType = $parameters["dbType"];
		self::$dbName = $parameters["dbName"];
		self::$host = $parameters["host"];
		self::$user = $parameters["user"];
		self::$password = $parameters["password"];
	}
	
    /**
     * Establish a link to the database
     * @return PDO A PDO object
     */
    public static function get()
    {   
        try
        {
			$pdo = new \PDO(self::$dbType.":dbname=".self::$dbName.";host=".self::$host.";charset=utf8", self::$user, self::$password);
			return $pdo;
        }
        catch(Exception $e)
        {
            throw new \InvalidArgumentException("The database could not be reached.");
        }
    }
}