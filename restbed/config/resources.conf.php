<?php
use restbed\config\Config;

$resourceBaseDir = Config::ROOT_DIR.'/'.Config::RESOURCE_DIR;

//error_log('Resource Base Dir : '.$resourceBaseDir);

// At least one model and one controller.
// Sample resource
require_once($resourceBaseDir.'sample/SampleController.class.php');

$_RESOURCE['sample']['controller'] = 'SampleController';
?>
