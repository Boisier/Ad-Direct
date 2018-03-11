<?php

namespace Controllers;

class ErrorController
{
	public function main()
	{
		//echo "<p>An error occured.</p>";
		header("location: /");
	}
	
	public function __call($funcName, $args)
	{
		$this->main;
	}
}