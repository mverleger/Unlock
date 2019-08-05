<?php
/**
 * Partially based on code from  
https://github.com/celtic-project/LTI-PHP/wiki/Installation

GetGUID based on:
https://github.com/IMSGlobal/LTI-Sample-Tool-Provider-PHP/blob/master/src/lib.php:673

 * Autoload a class file.
 *
 * @param string $class The fully-qualified class name.
 */
spl_autoload_register(function ($class) {

//	$class_original = $class;

  // base directory for the class files
  $base_dir = __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'ims' . DIRECTORY_SEPARATOR;

  if (strpos($class, 'IMSGlobal\\LTI\\') === 0) {
    $class = substr($class, 14);
  }

  // replace the namespace prefix with the base directory, replace namespace
  // separators with directory separators in the relative class name, append
  // with .php

  $file = $base_dir . preg_replace('/[\\\\\/]/', DIRECTORY_SEPARATOR, $class) . '.php';

  // if the file exists, require it
  if (file_exists($file)) {
    require($file);
  }

});

require_once('config.php');

use IMSGlobal\LTI\ToolProvider\DataConnector;
$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD);
mysqli_select_db($db, DB_DATABASE);
$db_connector = DataConnector\DataConnector::getDataConnector(DB_TABLE_PREFIX, $db);

function getAppPath()
{
    $root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    if (substr($root, -1) === '/') {  // remove any trailing / which should not be there
        $root = substr($root, 0, -1);
    }
    $dir = str_replace('\\', '/', dirname(__FILE__));

    $path = str_replace($root, '', $dir) . '/';

    return $path;
}

###
###  Get the application domain URL
###

function getHost()
{
    $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? 'http' : 'https';
    $url = $scheme . '://' . $_SERVER['HTTP_HOST'];

    return $url;
}

###
###  Get the URL to the application
###

function getAppUrl()
{
    $url = getHost() . getAppPath();

    return $url;
}


function validate_session(){
	$RequiredSessionVariables = array('fullname','admin','context_id','course_id','user_id');
	$RequiredSet = array_map( function($a){return isset($a);}, $RequiredSessionVariables);
		
	$ok = !in_array( FALSE, $RequiredSet );
	
//    $ok = isset($_SESSION['fullname']) && isset($_SESSION['admin']) && isset( $_SESSION['context_id'] ) && isset($_SESSION['course_id']);

    if (!$ok) {
        $_SESSION['error_message'] = 'Invalid Session.';
    }

    return $ok;
}


function getGuid()
{
    return sprintf('%04x%04x-%04x-%04x-%02x%02x-%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
        mt_rand(0, 65535), // 16 bits for "time_mid"
        mt_rand(0, 4096) + 16384, // 16 bits for "time_hi_and_version", with
        // the most significant 4 bits being 0100
        // to indicate randomly generated version
        mt_rand(0, 64) + 128, // 8 bits  for "clock_seq_hi", with
        // the most significant 2 bits being 10,
        // required by version 4 GUIDs.
        mt_rand(0, 256), // 8 bits  for "clock_seq_low"
        mt_rand(0, 65535), // 16 bits for "node 0" and "node 1"
        mt_rand(0, 65535), // 16 bits for "node 2" and "node 3"
        mt_rand(0, 65535)         // 16 bits for "node 4" and "node 5"
    );
}

?>