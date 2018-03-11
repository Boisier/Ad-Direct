<?php

namespace Models;

class EmailModel
{
	private $ddb;
	
	
	public function __construct()
	{
		$this->ddb = \Library\DBA::get();
	}
	
	
	public function create($type, $emitter, $recipient, $refID1, $refID2)
	{
		$stmt = $this->ddb->prepare("
		INSERT INTO
			emails(
				email_uid,
				email_type,
				email_emitter,
				email_recipient,
				email_date,
				email_ref_id_1,
				email_ref_id_2
				)
		VALUES(
			UUID(),
			:type,
			:emitter,
			:recipient,
			:date,
			:refID1,
			:refID2
			)
		");
		
		$stmt->execute([":type" => $type,
					    ":emitter" => $emitter,
					    ":recipient" => $recipient,
					    ":date" => time(),
					    ":refID1" => $refID1,
					    ":refID2" => $refID2]);
		
		return $this->ddb->lastInsertId();
	}
	
	
	
	public function clientEmail($clientID)
	{
		$stmt = $this->ddb->prepare("SELECT user_id FROM users WHERE user_id = :ID");
		$stmt->execute([":ID" => $clientID]);
		
		return [$stmt->fetchColumn()];
	}
	
	
	
	public function reviewersEmails()
	{
		$stmt = $this->ddb->prepare("
		SELECT 
			user_id
		FROM
			privileges
		WHERE
			privileges.privilege_name = 'APPROVE_CREATIVES'");
		$stmt->execute();
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	
	
	public function broadcastHandlers()
	{
		$stmt = $this->ddb->prepare("
		SELECT
			user_id
		FROM
			privileges
		WHERE
			privileges.privilege_name = 'BROADCAST_ADMIN'");
		$stmt->execute();
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	
	
	public function campaignEmailsByAd($adID)
	{
		$stmt = $this->ddb->prepare("
		SELECT
			users.user_id
			FROM 
				users
				JOIN 
					campaigns
					ON
						campaigns.broadcaster_id = users.broadcaster_id
				JOIN
					ads
					ON
						ads.campaign_id = campaigns.campaign_id
			WHERE
				ads.ad_id = $adID
		");
		$stmt->execute([":adID" => $adID]);
		
		return $stmt->fetchAll(\PDO::FETCH_COLUMN);
	}
	
	
	
	
	
	
	public function infos($emailID)
	{
		$stmt = $this->ddb->prepare("
		SELECT 
			email_id as ID,
			email_uid as UID,
			email_type as type,
			email_emitter as emitter,
			email_recipient as recipient,
			email_date as date,
			email_ref_id_1 as refID1,
			email_ref_id_2 as refID2
		FROM
			emails
		WHERE
			email_id = :ID
		");
		$stmt->execute([":ID" => $emailID]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	public function getIDbyUID($emailUID)
	{
		$stmt = $this->ddb->prepare("
		SELECT 
			COUNT(*)
		FROM
			emails
		WHERE
			email_uid = :UID");
		$stmt->execute([":UID" => $emailUID]);
		
		if($stmt->fetchColumn() == 0)
			return 0;
		
		$stmt = $this->ddb->prepare("
		SELECT
			email_id
		FROM
			emails
		WHERE
			email_uid = :UID");
		$stmt->execute([":UID" => $emailUID]);
		
		return $stmt->fetchColumn();
	}
}
