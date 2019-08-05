<?php
	
class Outcomes{
	var $db;
	var $CourseID;
	var $AccessToken;
	
	function __construct($db, $CourseID, $AccessToken){
		$this->db = $db;
		$this->CourseID = $CourseID;
		$this->AccessToken = $AccessToken;
	}
	
	public function ManageOutcomes($Post){
		if( $Post['ManageOutcomes'] == 1 ){
			$OutcomesChecked = array();
			$sql = 'select * from CRC_OutcomeAssignments where CourseID='.$this->CourseID;
			$res = mysqli_query( $this->db, $sql );
			while( $row = mysqli_fetch_assoc( $res ) ){
				$OutcomesChecked[$row['AssignmentID'].":".$row['OutcomeID']] = 1;
			}
			
			
			$Form = '';

			require_once('./lib/canvas-php-curl-master/class.curl.php');
			$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );
			
			$Criterion = array();
			$AssignmentNames = array();
			
			//ENDPOINT: url:GET|/api/v1/courses/:course_id/assignments
			$AssignmentList = $Canvas->get("/courses/".$this->CourseID."/assignments");
			
/*
			echo "<pre>";
			print_r( $AssignmentList );
			echo "</pre>";
*/
			
			foreach( $AssignmentList as $Assignment ){
				$AssignmentNames[$Assignment->id] = $Assignment->name;
				if( isset( $Assignment->rubric)){
					foreach( $Assignment->rubric as $RubricRow ){
						if( !isset( $Criterion[$RubricRow->description])){
							$Criterion[$RubricRow->description] = array();
						}
						$Criterion[$RubricRow->description][$Assignment->id]=$RubricRow->id;
					}
				}
			}

			$Form .= '<form action="index.php" method="POST">';
			$Form .= '<input type="hidden" name="ManageOutcomes" value="2" />';

			$Form .= '<table border="1">';
			
			foreach( $Criterion as $Desc=>$AssignmentIDs){
				$Form .= '<tr><td>'.$Desc.'</td><td>';
				foreach( $AssignmentIDs as $ID=>$RubricItemID){
					$FormValue = $ID.":".$RubricItemID;
					if( isset( $OutcomesChecked[$FormValue])){
						$Checked = " checked";
					}else{
						$Checked = "";
					}
					
					$Form .= '<input type="checkbox" name="IncludeRubricItems[]" value="'.$FormValue.'"'.$Checked.'>'.$AssignmentNames[$ID].'</input><br />';
				}
				$Form .= '</td></tr>';
			}
			
			$Form .= '</table>';
			
			$Form .= '<input type="hidden" name="UpdateOutcomes" value="1" />';
			$Form .= '<input type="submit" value="Save">';
			$Form .= '</form>';
		
			return $Form;			

		}elseif( $Post['ManageOutcomes'] == 2 ){
			$Out = '';

			$sep = '';
			$UpdateOutcomesSql = 'insert into CRC_OutcomeAssignments (CourseID, AssignmentID, OutcomeID, TargetAssignmentID) values ';

			// ID Columns To Save
			$SetRubricItems = array();
			if( isset( $Post['IncludeRubricItems']) ){
				$sql = 'delete from CRC_OutcomeAssignments where CourseID='.$this->CourseID;
				mysqli_query( $this->db, $sql );
						
				foreach( $Post['IncludeRubricItems'] as $Index=>$Value ){
					$SetRubricItems[$Value] = 0;
				}
			}	
			
			// Get all the assignments again
			require_once('./lib/canvas-php-curl-master/class.curl.php');
			$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );
			
			$GradebookTargetNames = array();
			//ENDPOINT: url:GET|/api/v1/courses/:course_id/assignments
			$AssignmentList = $Canvas->get("/courses/".$this->CourseID."/assignments");
			
			// If this rubric item should be included
			foreach( $AssignmentList as $Assignment ){
				if( isset( $Assignment->rubric)){
					foreach( $Assignment->rubric as $RubricRow ){
						if( isset( $SetRubricItems[$Assignment->id.":".$RubricRow->id] )){
							$GradebookTargetNames[$RubricRow->description] = 0;
							$SetRubricItems[$Assignment->id.":".$RubricRow->id] = $RubricRow->description;
						}
					}
				}
			}
			
			// Find the gradebook column that matters
			foreach( $AssignmentList as $Assignment ){
				if( isset( $GradebookTargetNames[$Assignment->name] )){
					$GradebookTargetNames[$Assignment->name] = $Assignment->id;
//					$Out .= "COLUMN FOUND:".$Assignment->name." - ".$Assignment->id."<br>";
				}
			}
			// Gradebook column not found
			foreach( $GradebookTargetNames as $TargetName=>$TargetID){
				if( $TargetID == 0 ){
					// TODO - Automatically create the assignment.  For now, requesting instructor to create it manually is sufficient.
					
					$Out .= "CREATE ASSIGNMENT NAMED:".$TargetName."<br>";
				}
			}

			// Actually add the data to the db			
			foreach( $Post['IncludeRubricItems'] as $Index=>$Value ){
				$Parts = explode(":", $Value);
				if( count( $Parts ) != 2 ){
					die();
				}else{
					$UpdateOutcomesSql .= $sep.'('.$this->CourseID.','.mysqli_real_escape_string($this->db, $Parts[0]).",'".mysqli_real_escape_string($this->db, $Parts[1])."',".$GradebookTargetNames[$SetRubricItems[$Value]].")";
					$sep = ',';
				}
			}
			//echo $UpdateOutcomesSql."<br>";
			mysqli_query( $this->db, $UpdateOutcomesSql );
			
			
			return $Out;
		}else{
			return false;
		}
	}
	
	public function ManageOutcomesForm(){
		
		$Form = '<form action="index.php" class="adminForm" method="POST">';
		$Form .= '<input type="hidden" name="ManageOutcomes" value="1" />';
		$Form .= '<input type="submit" class="submitButton"  value="Manage Outcomes">';
		$Form .= '</form>';
		return $Form;		
	}
	public function UpdateOutcomesForm(){
		$Form = '<form action="index.php" class="adminForm" method="POST">';
		$Form .= '<input type="hidden" name="UpdateOutcomes" value="1" />';
		$Form .= '<input type="submit" class="submitButton"  value="Update Outcomes">';
		$Form .= '</form>';
		return $Form;
	}
	
	public function UpdateOutcomes(){
		
		$sql = "select * from CRC_OutcomeAssignments where CourseID=".$this->CourseID;
		$res = mysqli_query( $this->db, $sql );

		require_once('./lib/canvas-php-curl-master/class.curl.php');
		$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );

		$OutcomeScores = array();
		
		while( $row = mysqli_fetch_assoc($res)){

			//ENDPOINT: url:GET|/api/v1/courses/:course_id/assignments/:assignment_id/submissions
			$SubmissionList = $Canvas->get("/courses/".$this->CourseID."/assignments/".$row['AssignmentID']."/submissions", array("include[]"=>"rubric_assessment"));

			foreach( $SubmissionList as $Submission){
				if( isset( $Submission->rubric_assessment )){
					foreach( $Submission->rubric_assessment as $Assessment=>$Details ){						
						if( strcmp( $Assessment, $row['OutcomeID'] ) == 0 ){
							if( !isset( $OutcomeScores[$Submission->user_id])){
								$OutcomeScores[$Submission->user_id] = array();
							}
							if( !isset( $OutcomeScores[$Submission->user_id][$row['TargetAssignmentID']])){
								$OutcomeScores[$Submission->user_id][$row['TargetAssignmentID']] = 0;
							}
							$OutcomeScores[$Submission->user_id][$row['TargetAssignmentID']] += $Details->points;
						}
					}
				}
				
			}

		}
		
		$ScoreCount = 0;
		foreach( $OutcomeScores as $UserID=>$GradeDetails ){
			foreach( $GradeDetails as $AssignmentID => $Grade){
				$GradeData = array("submission"=>array("posted_grade"=>strval($Grade)));


				//ENDPOINT: url:PUT|/api/v1/courses/:course_id/assignments/:assignment_id/submissions/:user_id
				$URL = "/courses/".$this->CourseID."/assignments/".$AssignmentID."/submissions/".$UserID;
				$Response = $Canvas->put($URL, $GradeData );
				$ScoreCount += 1;
				
			}
		}
		 
		return "<br />".$ScoreCount." Outcome Scores Updated<br />";
	}

	
	
}
	
	
	
?>