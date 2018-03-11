<?php
/**
 * This file handle CRON JOBS making ad-direct works.
*/

//Preloading similar to index

//Ignite autoloader
require_once "Library/Autoloader.php";

\Library\Autoloader::register();
\Library\Autoloader::staticLoads();

//Ignite the application
\Library\Igniter::ignite();





//Video treatments
$videoTreatmentController = new \Controllers\VideotreatmentController();
$videoTreatmentController->processVideos();