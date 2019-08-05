<?php
/*******************
	
	 Based on the tools/code at the following:
// https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP/wiki/Usage
// https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP
// http://www.imsglobal.org/sites/default/files/lti/tp-library-php/docs/index.html


*****************/

require_once('config.php');
require_once('load_libraries.php');
ini_set('session.cookie_path', getAppPath());

use IMSGlobal\LTI\ToolProvider;

class UnlockToolProvider extends ToolProvider\ToolProvider {
 
 	function onContentItem(){

        $_SESSION['consumer_pk'] = $this->consumer->getRecordId();
        $_SESSION['resource_id'] = getGuid();
        $_SESSION['resource_pk'] = NULL;
        $_SESSION['user_consumer_pk'] = $_SESSION['consumer_pk'];
        $_SESSION['user_pk'] = NULL;
        $_SESSION['isStudent'] = FALSE;
        $_SESSION['isContentItem'] = TRUE;
        $_SESSION['lti_version'] = $_POST['lti_version'];
        $_SESSION['return_url'] = $this->returnUrl;
        $_SESSION['data'] = $_POST['data'];
        $_SESSION['document_targets'] = $this->documentTargets;
	 	
        $placement = NULL;
        $documentTarget = 'iframe';
        $placement = new ToolProvider\ContentItemPlacement(1100,1000, $documentTarget, NULL);
        $item = new ToolProvider\ContentItem('LtiLinkItem', $placement);
        $item->setMediaType(ToolProvider\ContentItem::LTI_LINK_MEDIA_TYPE);
        $item->setTitle('Career Readiness Challenge');
        $item->setURL(LOCAL_URL.'launch.php');
        $item->icon = new ToolProvider\ContentItemImage(getAppUrl() . 'icon_small.png', 32, 32);
        $item->custom = array('content_item_id' => $_SESSION['resource_id']);
        $form_params['content_items'] = ToolProvider\ContentItem::toJson($item);
        if (!is_null($_SESSION['data'])) {
            $form_params['data'] = $_REQUEST['data'];
        }

        $data_connector = ToolProvider\DataConnector\DataConnector::getDataConnector(NULL, DB_TABLE_PREFIX);
        $consumer = ToolProvider\ToolConsumer::fromRecordId($_SESSION['consumer_pk'], $data_connector);
        $form_params = $consumer->signParameters($_SESSION['return_url'], 'ContentItemSelection', $_SESSION['lti_version'],
            $form_params);
        $page = ToolProvider\ToolProvider::sendForm($_SESSION['return_url'], $form_params);
              
		echo $page;
        exit;

 	}
 
 
  function onLaunch() {
	// Insert code here to handle incoming connections - use the user,
	// context and resourceLink properties of the class instance
	// to access the current user, context and resource link.
		
// Check the user has an appropriate role
    if ($this->user->isStaff()) {
// Initialise the user session
		$_SESSION['admin'] = TRUE;
	}else{
		$_SESSION['admin'] = FALSE;
	}
	$_SESSION['fullname'] = $this->user->fullname;
	$_SESSION['user_id'] =  $_POST['custom_canvas_user_id'];
	$_SESSION['context_id'] = $this->context->ltiContextId;
	$_SESSION['course_id'] = $_POST['custom_canvas_course_id'];
    $_SESSION['isContentItem'] = FALSE;

 	require_once('config.php');
 	require_once('database.php');
 	$db = db();
 	mysqli_query( $db, 'delete from CRC_NameCache where CourseID='.$_POST['custom_canvas_course_id'].' and UserID='.$_POST['custom_canvas_user_id']);
 	mysqli_query( $db, "insert into CRC_NameCache (CourseID, UserID, Name) values (".$_POST['custom_canvas_course_id'].",".$_POST['custom_canvas_user_id'].",'".$this->user->fullname."')");
 	
	require_once('OAuth.php');
	$OAuth = new API_OAuth($_SESSION['user_id']);
	$OAuthURL = $OAuth->Login();
	
	if( !is_null( $OAuthURL) ){
		$URL = $OAuthURL;
	}else{
		$URL = GetAppURL()."index.php";
	}

// Redirect the user to display the list of items for the resource link
    $this->redirectUrl = $URL;  

  }
}

$tool = new UnlockToolProvider($db_connector);
$tool->handleRequest();

?>