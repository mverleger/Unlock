<?php

/******* Session Details *******/
define('SESSION_NAME'		, 'CHANGE_SESSION_NAME');
session_name(SESSION_NAME);
session_start();


/******** Database Login Information *******/
define('DB_USERNAME'		, 'database_username');
define('DB_PASSWORD'		, 'database_password');
define('DB_SERVER'			, 'localhost');
define('DB_DATABASE'		, 'database_name');
define('DB_TABLE_PREFIX'	, '');


/******* API Access Information *******/
define('API_KEY'			, 'This_should_be_your_API_master_access_Key');
define('API_DOMAIN'			, 'canvas.instructure.com' ); // CHANGE FOR YOUR DOMAIN
define('LOCAL_URL'			, 'https://TheLocalURLForThisCode/');

/******* OAuth 2.0 Vars *******/
define("CALLBACK_URL", LOCAL_URL."/index.php");
define("ACCESS_TOKEN_URL", "https://".API_DOMAIN."/login/oauth2/auth");
define("CLIENT_ID", "123456789012345"); // Given through Canvas' Setup
define("CLIENT_SECRET", "THISISALONGSTRING");// Given through Canvas' Setup
define("SCOPE", ""); // optional
define("AUTH_URL", "https://".API_DOMAIN."/login/oauth2/token");

?>
