<?php

namespace Library;

class Params
{
	public static function get($paramID)
	{
		User::onlyLoggedIn();
		
		$paramID = Sanitize::string($paramID);
		
		$paramModel = new \Models\ParamModel();
		
		if(!$paramModel->exist($paramID))
			return;
		
		$param = $paramModel->get($paramID);
		
		switch($param['type'])
		{
			case "list":
				$paramValue = explode(",", $param['value']);
			break;
			case "duration":
				$paramValue = explode(",", $param['value']);
				$paramValue["years"] = $paramValue[0];
				$paramValue["months"] = $paramValue[1];
				$paramValue["weeks"] = $paramValue[2];
				$paramValue["days"] = $paramValue[3];
				$paramValue["hours"] = $paramValue[4];
				$paramValue["minutes"] = $paramValue[5];
				$paramValue["seconds"] = $paramValue[6];
				
			break;
			default;
				$paramValue = $param['value'];
		}
		
		return $paramValue;
	}
}