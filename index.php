<?php
	
/********************
index.php

This is the main content page for the application.  It is NOT the actual launch page - see launch.php for that.

The workflow is that launch.php authenticates and processes the POST data and then redirects to this index page.
	
********************/
	
	require_once('config.php');
	require_once('load_libraries.php');
	require_once('OAuth.php');
	
	$OAuth = new API_OAuth($_SESSION['user_id']);
	$OAuthURL = $OAuth->Login();
	if( $OAuthURL != null ){
		header('Location: '.$OAuthURL);
	}
	
	echo "<html><head>";
	echo '<link rel = "stylesheet"  type = "text/css" href = "style.css" />';
	echo "</head><body>";
	
	require_once('Components/Assignment_Status.php');
	$AssignStatus = new AssignmentStatus($db, $_SESSION['course_id'], $OAuth->AccessToken);

	require_once('Components/Assignment_Tree.php');
	$AssignmentTree = new AssignmentTree( $db, $_SESSION['course_id'], $OAuth->AccessToken, $AssignStatus );
		
	if( validate_session() ){

		require_once('Components/Leaderboard.php');
		
		$Leaderboard = new Leaderboard($db, $_SESSION['course_id'],$_SESSION['user_id'], $OAuth->AccessToken, $AssignStatus);

		if( $_SESSION['admin'] == TRUE ){

			echo '<div class="AdminPanel">';
			echo "Administrator Panel<br>";

			require_once('Components/Outcomes.php');
			$Outcomes = new Outcomes($db, $_SESSION['course_id'], $OAuth->AccessToken);
			
			if( isset( $_POST['ManageOutcomes']) && $_POST['ManageOutcomes'] == 1 ){
				echo $Outcomes->ManageOutcomes($_POST);
			}elseif( isset( $_POST['ManageOutcomes']) && $_POST['ManageOutcomes'] == 2 ){
				echo $Outcomes->ManageOutcomes($_POST);
				echo $Outcomes->ManageOutcomesForm();	
			}else{
				echo $Outcomes->ManageOutcomesForm();				
			}
			
			
			if( isset( $_POST['UpdateOutcomes']) && $_POST['UpdateOutcomes'] == 1 ){
				echo $Outcomes->UpdateOutcomes();
			}
			echo $Outcomes->UpdateOutcomesForm();
			

			if( isset( $_POST['ManageLeaderboard']) && $_POST['ManageLeaderboard'] == 1 ){
				echo $Leaderboard->ManageLeaderboard($_POST);
			}elseif( isset( $_POST['ManageLeaderboard']) && $_POST['ManageLeaderboard'] == 2 ){
				echo $Leaderboard->ManageLeaderboard($_POST);
				echo $Leaderboard->ManageLeaderboardForm();	
			}else{
				echo $Leaderboard->ManageLeaderboardForm();				
			}
			
			if( isset( $_POST['UpdateLeaderboardCache']) && $_POST['UpdateLeaderboardCache'] == 1 ){
				echo $Leaderboard->UpdateLeaderboardCache();
			}
			echo $Leaderboard->UpdateLeaderboardCacheForm();

			if( isset( $_POST['ManageAssignmentTree']) && $_POST['ManageAssignmentTree'] == 1 ){
				echo $AssignmentTree->ManageAssignmentTree($_POST);
			}elseif( isset( $_POST['ManageAssignmentTree'] ) && $_POST['ManageAssignmentTree'] == 2){
				echo $AssignmentTree->ManageAssignmentTree($_POST);
				echo $AssignmentTree->ManageAssignmentTreeForm();	
			}else{
				echo $AssignmentTree->ManageAssignmentTreeForm();
			}

			echo '</div>';
			
		}
		
		require_once( 'Components/Progress_Bar.php' );
		$PBar = new Progress_Bar( $db, $_SESSION['course_id'], $AssignStatus );
		echo $PBar->DisplayProgressBar();
		

		$Leaderboard->UpdateUsersLeaderboardStatus();
		echo '<div class="LeaderboardBlock"><div class="IndividualLeaderboardBlock">';
		if( $_SESSION['admin'] == TRUE ){
			echo $Leaderboard->PrintIndividualBoard(false, 10);
		}else{
			echo $Leaderboard->PrintIndividualBoard(true);
		}
		echo '</div><div class="TeamLeaderboardBlock">';
		echo $Leaderboard->PrintTeamBoard();
//		echo '<div class="UpdateTime">Last Updated:'.$Leaderboard->PrintLastUpdateTime().'</div>';
		echo '</div></div>';		
				
		echo '<div class="StudentBlock">';
		$AssignmentTree->DisplayAssignmentTree();
		echo '</div>';
		
		
	}else{
		echo $_SESSION['error_message'];
		die();
	}
	
	echo "</body></html>";
		


?>