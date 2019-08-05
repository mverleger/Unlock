/* After the database is created, this adds your LTI account to the database */


<?php
	
require_once('config.php');
	
use IMSGlobal\LTI\ToolProvider;

$consumer = new ToolProvider\ToolConsumer(API_DOMAIN, $db_connector);
$consumer->name = 'NAME OF LTI TOOL';
$consumer->secret = 'SHARED_SECRET';
$consumer->enabled = TRUE;
$consumer->save();

echo "Account Created";

?>
