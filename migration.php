<?php
/**
 * Created by PhpStorm.
 * User: val
 * Date: 26/11/2017
 * Time: 18:24
 */
//Ignite autoloader
require_once "Library/Autoloader.php";

\Library\Autoloader::register();
\Library\Autoloader::staticLoads();

//Ignite the application
\Library\Igniter::ignite();

require_once "neotraf_addirect.php";

/** @var \PDO $ddb */
$ddb = \Library\DBA::get();






function genPassword()//: string
{
	$length = 8;
    $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!?$&#-_";
    $retVal = "";
	
	for ($i = 0, $n = strlen($charset); $i < $length; $i++)
	{
		$retVal += substr($charset, rand(0, $n), 1);
	}
	
	return $retVal;
}






$companyBroadcaster = [];
$userRemmaping = [1 => 1];

//Prepare SQL statements
$addBroadcasterStmt = $ddb->prepare("
	INSERT INTO broadcasters(
		broadcaster_name,
		broadcaster_create_time,
		broadcaster_creator
	)
	VALUES (
		:name,
		:createTime,
		1
	)
");

$addUserStmt = $ddb->prepare("
	INSERT INTO users(
		broadcaster_id,
		user_admin,
		user_parent,
		user_name,
		user_email,
		user_password,
		user_creation_time,
		user_last_activity,
		user_live,
		user_local,
		legal_approved,
		time_zone
	)
	VALUES(
		:broadcasterID,
		:isAdmin,
		:parent,
		:name,
		:email,
		:password,
		:creationTime,
		:lastActivity,
		:live,
		'fr_FR',
		0,
		'America/Montreal'
	)
");








//Add all the broadcasters
foreach($companies as $company)
{
	$addBroadcasterStmt->execute([
		":name" => $company["company_name"],
		":createTime" => $company["company_create_date"]
	]);
	
	$companyBroadcaster[$company["company_id"]] = $ddb->lastInsertId();
}

echo "<pre>";
//Add all users
foreach ($users as $user)
{
	$newPassword = genPassword();
	
	if($user["user_admin"] == 1)
		$broadcasterID = NULL;
	else
		$broadcasterID = $companyBroadcaster[$user["company_id"]];
	
	$addUserStmt->execute([
		":broadcasterID" => $broadcasterID,
		":isAdmin" => $user["user_admin"],
		":parent" => $userRemmaping[$user["user_parent"]],
		":name" => $user["user_name"],
		":email" => $user["user_email"],
		":password" => $newPassword,
		":creationTime" => $user["user_creation_date"],
		":lastActivity" => $user["user_last_activity"],
		":live" => $user["user_live"]
	]);
	
	$userID = $ddb->lastInsertId();
	
	$userRemmaping[$user["user_id"]] = $userID;
	
	//send email
}

echo "</pre>";