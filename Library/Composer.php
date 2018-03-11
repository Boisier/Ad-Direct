<?php

namespace Library;

class Composer implements Interfaces\ComposerInterface, Interfaces\RenderInterface
{
    private $views = array();
    
    public function attach(Interfaces\RenderInterface $view)
    {
        if(!in_array($view, $this->views, true))
            $this->views[] = $view;
        
        return $this;
    }
    
    public function detach(Interfaces\RenderInterface $view)
    {
        $this->views = array_filter($this->views, function ($value) use ($view) {
            return $value !== $view;
        });
        return $this;
    }
    
    public function render()//: string
    {
        $output = "";
        
        foreach($this->views as $view)
        {
            $output .= $view->render();
        }
        
        return $output;
    }
    
    public function nbrViews()//: int
    {
    	return count($this->views);
    }
}