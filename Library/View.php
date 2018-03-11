<?php

namespace Library;

class View implements Interfaces\ViewInterface, Interfaces\RenderInterface
{
    private $template = null;
    private $fields = array();
    
    public function __construct($template)
    {
        $path = "views/".$template."View.php";
        
        if(!file_exists($path))
            return false;
        
        $this->template = $path;
    }
    
    public function getTemplate()
    {
        return $this->template;
    }
    
    public function __set($key, $value)
    {
        $this->fields[$key] = $value;
        return $this;
    }
    
    public function bindValues(array $values)
    {
        foreach($values as $key => $value)
        {
            $this->fields[$key] = $value;
        }
        
        return $this;
    }
    
    public function __get($key)
    {
        if(!isset($this->fields[$key]))
		{
			//TODO: Make this better
			/*echo "<pre>";
            throw new \InvalidArgumentException(
                "Unable to get the field '$key'.");
			echo "</pre>";*/
			return;
        }
       
        $field = $this->fields[$key];
        
        return $field instanceof Closure ? $field($this) : $field;
    }
    
    public function render()
    {
        if($this->template == null)
            return "";
    
        ob_start();
            include $this->template;    
        return ob_get_clean();
    }
}
