<?php
/********************
	Create a connection to the database.
	
	
	
********************/


function db(){
	require_once( 'config.php' );
	$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
	return $db;
}


	
?>
