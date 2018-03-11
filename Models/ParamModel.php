<?php

namespace Models;

class ParamModel
{
	private $ddb;
	
	
	public function __construct()
	{
		$this->ddb = \Library\DBA::get();
	}
	
	
	public function getAll()
	{
		$stmt = $this->ddb->prepare("
			SELECT
				param_name as name,
				param_value as value,
				param_type as type,
				param_last_update
			FROM
				params
			ORDER BY
				param_name
		");
		$stmt->execute();
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	
	
	public function get($paramName)
	{
		$stmt = $this->ddb->prepare("SELECT param_name as name, param_value as value, param_type as type, param_last_update FROM params WHERE param_name = :name");
		$stmt->execute([":name" => $paramName]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	public function exist($paramName)
	{
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM params WHERE param_name = :name");
		$stmt->execute([':name' => $paramName]);
		$nbrParam = $stmt->fetchColumn();
		
		if($nbrParam == 1)
			return true;
		
		return false;
	}
	
	
	public function type($paramName)
	{
		if(!$this->exist($paramName))
			return false;
		
		$stmt = $this->ddb->prepare("SELECT param_type FROM params WHERE param_name = :name");
		$stmt->execute([':name' => $paramName]);
		return $stmt->fetchColumn();
	}
	
	public function updateGlobal($paramName, $paramValue)
	{
		$stmt = $this->ddb->prepare("UPDATE params SET param_value = :value, param_last_update = :update WHERE param_name = :name");
		$stmt->execute([":value" => $paramValue,
					    ":update" => time(),
					    ":name" => $paramName]);
	}
}