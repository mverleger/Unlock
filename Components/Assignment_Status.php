<?php

class AssignmentStatus{
	var $db;
	var $AssignmentGroup;
	var $AssignmentLevel;
	var $AssignmentName;
	var $AssignmentGrade;
	var $PossiblePoints;
	var $DataLoaded;
	var $Status;
	
	function __construct($db, $CourseID, $AccessToken){
		$this->db = $db;
		$this->CourseID = $CourseID;
		$this->AccessToken = $AccessToken;
		
		$this->AssignmentGroup = array();
		$this->AssignmentLevel = array();
		$this->AssignmentName = array();
		$this->AssignmentGrade = array();
		$this->PossiblePoints = array();
		$this->DataLoaded = 0;
		
	}
	
	
	function CalculateLockStatus(){
		$Unlocked = array();

		$this->LoadData();
		
		require_once( 'Components/Progress_Bar.php' );
		$PBar = new Progress_Bar( $this->db, $_SESSION['course_id'], $this );
		$CurrentLevel = $PBar->GetCurrentLevel();
		
		foreach( $this->AssignmentLevel as $AID=>$Level ){
			if( $Level > $CurrentLevel ){
				$Unlocked[$AID] = 0;
			}else{
				$Unlocked[$AID] = 1;
			}
		}
				
		return $Unlocked;		
		
	}
	
	function CurrentStatus( $AID ){
		if( !isset( $this->Status[$AID] )){
			$this->CalculateStatus();
		}
		
		return $this->Status[$AID];
		
	}
	
	function PrintGradebook(){
		$Echo = '';
		
		$this->LoadData();
		
		$Echo .= '<table border="1"><tr><th>Title</th><th>Points</th><th>Possible</th></tr>';
		foreach( $this->AssignmentName as $AID=>$Name){
			$Echo .= '<tr><td>';
			$Echo .= $Name;
			$Echo .= '</td><td>';
			$Echo .= $this->Grade($AID);
			$Echo .= '</td><td>';
			$Echo .= $this->OutOf($AID);
			$Echo .= '</td></tr>';
		}
		$Echo .= '</table>';
		return $Echo;
		
	}
	
	function TotalScore($AIDList = null){
		$Score = 0;
		if( is_null( $AIDList ) ){
			if( count( array_keys( $this->AssignmentGrade )) == 0 ){
				$this->LoadData();	
			}
			$AIDList = array_keys( $this->AssignmentGrade );
		}
		foreach( $AIDList as $Index=>$AID ){
			$Score += $this->Grade($AID);
		}
		
		return $Score;
	}
	
	function Grade( $AID ){
		if( isset( $this->AssignmentGrade[$AID] )){
			return $this->AssignmentGrade[$AID];
		}else{
			$this->LoadData();
			if( isset( $this->AssignmentGrade[$AID] )){
				return $this->AssignmentGrade[$AID];
			}else{
				return 0;
			}
		}
	}
	function OutOf( $AID ){
		if( isset( $this->PossiblePoints[$AID] )){
			return $this->PossiblePoints[$AID];
		}else{
			$this->LoadData();
			if( isset( $this->PossiblePoints[$AID] )){
				return $this->PossiblePoints[$AID];
			}else{
				return 0;
			}
		}
	}
	
	function LoadData(){
		if( $this->DataLoaded != 1 ){
			$this->LoadDatabaseInfo();
			$this->LoadCanvasInfo();
			$this->DataLoaded = 1;
		}
	}
	
	function LoadDatabaseInfo(){
		$sql = 'select AssignmentID, DisplayLevel, Grouping from CRC_AssignmentTree where CourseID='.$this->CourseID;
		$res = mysqli_query( $this->db, $sql );
		
		while( $row = mysqli_fetch_assoc( $res )){
			$this->AssignmentLevel[$row['AssignmentID']] = $row['DisplayLevel'];
			$this->AssignmentGroup[$row['AssignmentID']] = $row['Grouping'];
		}
	}
		
	function LoadCanvasInfo(){
		require_once('./lib/canvas-php-curl-master/class.curl.php');
		$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );
		
		//ENDPOINT: url:GET|/api/v1/courses/:course_id/assignments
		$AssignmentList = $Canvas->get("/courses/".$this->CourseID."/assignments");
		
		foreach( $AssignmentList as $Assignment ){
			$this->AssignmentName[$Assignment->id] = $Assignment->name;
			$this->PossiblePoints[$Assignment->id] = $Assignment->points_possible;
		}
		
		//ENDPOINT: url:GET|/api/v1/courses/:course_id/students/submissions
		$SubmissionList = $Canvas->get("/courses/".$this->CourseID."/students/submissions");
		foreach( $SubmissionList as $Submission ){
			if ( isset( $Submission->assignment_id ) ){
				if( isset( $Submission->score )){
					$this->AssignmentGrade[$Submission->assignment_id] = $Submission->score;
				}else{
					$this->AssignmentGrade[$Submission->assignment_id] = 0;
				}
			}
		}
		
	}
}

?>