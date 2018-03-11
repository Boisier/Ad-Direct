<?php

namespace Models;

use \Controllers\ReviewController;

class ReviewModel
{
	private $ddb;
	
	
	
	
	
	public function __construct()
	{
		$this->ddb = \Library\DBA::get();
	}
	
	
	
	
	
	public function create($adID, $reviewStatus, $reviewerID, $reviewedDate)
	{
		$stmt = $this->ddb->prepare("
		INSERT INTO
			ad_reviews(
				ad_id,
				review_create_date,
				review_status,
				reviewer_id,
				reviewed_date
			)
			VALUES(
				:adID,
				:createDate,
				:status,
				:reviewerID,
				:reviewedDate
			)
		");
		$stmt->execute([":adID" => $adID,
					    ":createDate" => time(),
					    ":status" => $reviewStatus,
					    ":reviewerID" => $reviewerID,
					    ":reviewedDate" =>$reviewedDate]);
	}
	
	
	
	
	
	public function get($adID)
	{
		//First, check if there is a review
		$stmt = $this->ddb->prepare("SELECT COUNT(*) FROM ad_reviews WHERE ad_id = :adID");
		$stmt->execute([":adID" => $adID]);
		
		if($stmt->fetchColumn() == 0)
		{
			return ["status" => ReviewController::AD_INCOMPLETE];
		}
	
		$stmt = $this->ddb->prepare("
		SELECT
			review_create_date as createDate,
			review_status as status,
			reviewer_id as reviewerID,
			review_comment as comment,
			reviewed_date as reviewedDate,
			ad_start_time as adStartTime,
			ad_end_time as adEndTime
		FROM
			ad_reviews
			JOIN
				ads
				ON ad_reviews.ad_id = ads.ad_id
		WHERE
			ad_reviews.ad_id = :adID
		");
		$stmt->execute([":adID" => $adID]);
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
	
	
	
	
	
	public function validate($adID, $newStatus, $comment)
	{
		if(strlen($comment) == 0)
			$comment = NULL;
		
		$stmt = $this->ddb->prepare("
		UPDATE
			ad_reviews
		SET
			review_status = :status,
			reviewer_id = :reviewerID,
			review_comment = :comment,
			reviewed_date = :reviewedDate
		WHERE
			ad_id = :adID
		");
		
		$stmt->execute([":status" => $newStatus,
					    ":reviewerID" => \Library\User::id(),
					    ":comment" => $comment,
					    ":reviewedDate" => time(),
					    ":adID" => $adID]);
	}
	
	
	
	
	
	public function remove($adID)
	{
		$stmt = $this->ddb->prepare("
		DELETE FROM
			ad_reviews
		WHERE
			ad_id = :adID
		");
		$stmt->execute([":adID" => $adID]);
	}
}