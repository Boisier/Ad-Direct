<?php

namespace Library\Interfaces;

interface ViewInterface
{
    public function __construct($template);
    public function getTemplate();
    
    public function __set($key, $value);
    public function bindValues(array $values);
    
    public function __get($key);
}
