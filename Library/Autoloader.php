<?php
/**
 * Class Autoloader
 */

namespace Library;

class Autoloader
{
    /**
     * Register Autoloader handler
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Include the required class file
     * @param $class string Name of the class to load
     */
    static function autoload($class)
    {   
		//echo $class."<br>";
        
		$class = str_replace('\\', '/', ucfirst($class));
        
        if(file_exists($class.".php"))
        {
            require_once $class.".php";
            return true;
        }
        
        return false;
    }

    /**
     * Include all others files that may be needed
     */
    static function staticLoads()
    {
		//register Composer autoloader
		require_once "assets/vendor/autoload.php";
    }
}
