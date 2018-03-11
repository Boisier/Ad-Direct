<?php
/**
 * FrontController rredirect calls to the specified controller
 */

namespace Controllers;

class FrontController
{
    /**
     * Set default values
     */
    protected $controller  = "Controllers\\HomeController";
    protected $action      = "main";
    protected $params      = [];
	
	protected $dynamicMethods = [];
    
    /**
     * Set trigger path
     */
    protected $basePath    = "";
    
    /**
     * Init the FrontController
     *
     * @param $args array Must respect this format to work : ["controller" => "", "action" => "", "params" => []]
     */
    public function __construct(array $args = [])
    {
        if(empty($args)) 
        {
            $this->parseURI();
            return;
        }
        
        if(isset($args['controller']))
            $this->setController($args['controller']);
        
        if(isset($args['action']))
            $this->setAction($args['action']);
        
        if(isset($args['params']))
            $this->setParams($args['params']);
    }
    
    /**
     * Parse the URI and set $controller, $action, $params acordingly
     */
    protected function parseURI()
    {
        //Clean up URL
        $path = trim(parse_url(strtolower($_SERVER['REQUEST_URI']), PHP_URL_PATH), '/');
        
        //Remove basepath
		if(strlen($this->basePath) != 0)
		{
        	if(strpos($path, $this->basePath) === 0)
				$path = substr($path, strlen($this->basePath));
		}
		
        //Get all given variables
        @list($controller, $action, $params) = explode("/", $path, 3);
		
        //Assing new values
        if(!empty($controller))
            $this->setController($controller);
		
        if(!empty($action))
            $this->setAction($action);
        
        if(!empty($params))
            $this->setParams(explode("/", $params));
    }
    
    /**
     * Set which controller to call, and confirm it exists
     */
    protected function setController($controller)
    {
        $controller = "Controllers\\".ucfirst($controller)."Controller";
        
        if(!class_exists($controller))
        {
			if(constant("DEBUG"))
			{
				http_response_code(200);
				throw new \InvalidArgumentException("The controller ".$controller." could not be found.");
			}
			
			$this->controller = "Controllers\\ErrorController";
			$this->run();
			return;
        }
        
        $this->controller = $controller;
        return $this;
        
    }
    
    /**
     * Set which method to call, and confirm it exists
     */
    protected function setAction($action)
    {
		if(in_array($this->controller, $this->dynamicMethods))
      	{	
        	$this->action = $action;
        	return;
      	}

      	$reflector = new \reflectionclass($this->controller);

        if(!$reflector->hasMethod($action))
        {
        	if(constant("DEBUG"))
        	{
				http_response_code(200);
          		throw new \InvalidArgumentException("The action ".$action." is not a method of the ".$this->controller.".");
			}
			
		  	$this->controller = "Controllers\\ErrorController";
			$this->run();
			return;
      	}
		
	  	$this->action = $action;
	  	return $this;
    }

    
    /**
     * Add params to the list.
     */
    protected function setParams(array $params)
    {
        $this->params = $params;
        
        return $this;
    }
    
    /**
     * Execute the request
     */
    public function run()
    {
		http_response_code(200);
        call_user_func_array(array(new $this->controller, $this->action), $this->params);
    }
}
