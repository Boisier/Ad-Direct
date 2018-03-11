<?php
/**
 * index.php
 * Main entry
 * All calls to an unknown path are rerouted there.
 */

//Ignite autoloader
require_once "Library/Autoloader.php";

\Library\Autoloader::register();
\Library\Autoloader::staticLoads();

//Ignite the application
\Library\Igniter::ignite();

//Finally init the frontController and use its run() method to call the requested controller.
$frontController = new Controllers\FrontController();
$frontController->run();