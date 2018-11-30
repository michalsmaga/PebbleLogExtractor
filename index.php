<?php

require __DIR__ . '/vendor/autoload.php';


use \PebbleLogExtractor\Controller\LogExtractController;

ini_set('display_errors', 'On');

$LogExtractorController = new LogExtractController();
$LogExtractorController->run();
