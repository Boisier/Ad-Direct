<?php

namespace Models;

class SearchModel
{
	private $ddb;
	
	
	public function __construct()
	{
		$this->ddb = \Library\DBA::get();
	}
	
	
	
	
	
	
	public function search($query)
	{
		$formatedQuery = "%$query%";
		
		$stmt = $this->ddb->prepare("
			SELECT
				'broadcaster' as type,
				broadcaster_id as ID,
				broadcaster_name as ref
			FROM
				broadcasters
			WHERE
				broadcaster_name LIKE :query
		UNION
			SELECT
				'campaign' as type,
				campaign_id as ID,
				campaign_name as ref
			FROM
				campaigns
			WHERE
				campaign_name LIKE :query
		UNION
			SELECT
				'client' as type,
				user_id as ID,
				user_name as ref
			FROM
				users
			WHERE
				(user_name LIKE :query OR
				 user_email LIKE :query) AND
				user_admin = 0
		ORDER BY
			ref
		");
		
		$stmt->execute([":query" => $formatedQuery]);
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}