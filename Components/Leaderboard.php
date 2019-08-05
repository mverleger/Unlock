<?php

class Leaderboard{
	var $db;
	var $CourseID;
	var $Users;
	var $Groups;
	var $AccessToken;
	var $AssignStatus;
	
	function __construct($db, $CourseID, $UserID, $AccessToken, $AssignStatus){
		$this->db = $db;
		$this->CourseID = $CourseID;
		$this->UserID = $UserID;
		$this->Users = array();	
		$this->Groups = array();
		$this->AccessToken = $AccessToken;
		$this->AssignStatus = $AssignStatus;
	}
	
	public function ManageLeaderboard($Post){
		if( $Post['ManageLeaderboard'] == 1 ){
			
			$CheckedAssignments = array();
			$sql = 'select * from CRC_Leaderboard_Columns where CourseID='.$this->CourseID;
			$res = mysqli_query( $this->db, $sql );
			while( $row = mysqli_fetch_assoc( $res )){
				$CheckedAssignments[$row['AssignmentID']] = 1;
			}

			
			require_once('./lib/canvas-php-curl-master/class.curl.php');
			$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );
	
			$Form = '<form action="index.php" method="POST">';
			$Form .= '<input type="hidden" name="ManageLeaderboard" value="2" />';

			
			//https://github.com/cesbrandt/canvas-php-curl
			//ENDPOINT: url:GET|/api/v1/courses/:course_id/assignments
			$AssignmentList = $Canvas->get("/courses/".$this->CourseID."/assignments");
			

/*
			$Form .= "<hr><pre>";
			$Form .= "Assignment List<br>";
			$Form .= print_r( $AssignmentList, true );
			$Form .= "</pre><hr>";
*/


			$Form .=  '<table border="1">';	
			$Form .=  "<tr>";
			$Form .=  "<th>Assignment</th>";
			$Form .=  "<th>Include in Leaderboard</th>";
			$Form .=  "</tr>";
			foreach( $AssignmentList as $Assignment ){
				$Form .=  "<tr>";
				$Form .=  "<td>".$Assignment->name."</td>";
				if( isset( $CheckedAssignments[$Assignment->id])){
					$Checked = ' checked';
				}else{
					$Checked = '';
				}
				$Form .=  '<td><input type="checkbox" name="IncludeColumns[]" value="'.$Assignment->id.'"'.$Checked.'></td>';
				$Form .=  "</tr>";
			}
			$Form .=  "</table>";
			$Form .= '<input type="hidden" name="UpdateLeaderboardCache" value="1" />';
			$Form .= '<input type="submit" value="Save">';
			$Form .= '</form>';
						
					
			return $Form;			
		}elseif( $Post['ManageLeaderboard'] == 2 ){
			$Out = '';
			
			
			$sql = 'delete from CRC_Leaderboard_Columns where CourseID='.$this->CourseID;
//			$Out .= $sql."<br>";
			mysqli_query($this->db, $sql );
			
			if( isset( $_POST['IncludeColumns'])){
				foreach ($_POST['IncludeColumns'] as $ColumnID){
					$sql = 'insert into CRC_Leaderboard_Columns (CourseID, AssignmentID) values ('.$this->CourseID.','.mysqli_real_escape_string($this->db, $ColumnID).')';
//					$Out .= $sql."<br>";
					mysqli_query($this->db, $sql );
				}
			}
			
			$Out .= "Saved";
			
			return $Out;
		}else{
			return false;
		}
	}
	public function ManageLeaderboardForm(){
		$Form = '<form action="index.php" class="adminForm" method="POST">';
		$Form .= '<input type="hidden" name="ManageLeaderboard" value="1" />';
		$Form .= '<input type="submit"  class="submitButton" value="Manage Leaderboard">';
		$Form .= '</form>';
		return $Form;		
	}
	public function UpdateLeaderboardCacheForm(){
		$Form = '<form action="index.php" class="adminForm" method="POST">';
		$Form .= '<input type="hidden" name="UpdateLeaderboardCache" value="1" />';
		$Form .= '<input type="submit"  class="submitButton" value="Update Leaderboard Cache">';
		$Form .= '</form>';
		return $Form;
	}
	
	public function UpdateUsersLeaderboardStatus(){
		$sql = 'select * from CRC_Leaderboard_Columns where CourseID='.$this->CourseID;
		$res = mysqli_query( $this->db, $sql );
		if( mysqli_num_rows( $res ) > 0 ){
			$CheckedAssignments = array();
			while( $row = mysqli_fetch_assoc( $res )){
				array_push($CheckedAssignments, $row['AssignmentID']);
			}

			$Score = $this->AssignStatus->TotalScore( $CheckedAssignments );

			$sql = 'delete from CRC_Leaderboard where CourseID='.$this->CourseID.' and UserID='.$this->UserID;
			mysqli_query( $this->db, $sql );
			
			$sql = "insert into CRC_Leaderboard (CourseID, UserID, Points) values (".$this->CourseID.",".$this->UserID.",".$Score.")";
			mysqli_query( $this->db, $sql );
			
		}
	}
	
	public function UpdateLeaderboardCache(){
		$sql = 'select * from CRC_Leaderboard_Columns where CourseID='.$this->CourseID;
		$res = mysqli_query( $this->db, $sql );
		if( mysqli_num_rows( $res ) > 0 ){
			while( $row = mysqli_fetch_assoc( $res )){
				$CheckedAssignments[$row['AssignmentID']] = 1;
			}
				
		
			require_once('./lib/canvas-php-curl-master/class.curl.php');
			
			$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );


			$Scores = array();	
			foreach( array_keys($CheckedAssignments) as $AssignmentID ){
				//https://github.com/cesbrandt/canvas-php-curl
				//ENDPOINT: url:GET|/api/v1/courses/:course_id/assignments/:assignment_id/submissions
				$Submissions = $Canvas->get("/courses/".$this->CourseID."/assignments/".$AssignmentID."/submissions", array( "include[]" => "user"));
				foreach( $Submissions as $Submission ){
/*
					echo "<hr><pre>";
					print_r( $Submission );
					echo "</pre>";
*/
					if( isset( $Submission->user_id )){
						$SubmissionUserID = $Submission->user_id;
					}elseif( isset( $Submission->user->id  )){
						$SubmissionUserID = $Submission->user->id;
					}
					
					if( !isset( $Scores[$SubmissionUserID] ) ){
						$Scores[$SubmissionUserID] = 0;
						$this->Users[$SubmissionUserID] = $Submission->user->name;
					}
					if( isset( $Submission->grade )){
						$Scores[$SubmissionUserID] += $Submission->grade;
					}
				}
			}
			
			$sql = 'delete from CRC_NameCache where CourseID='.$this->CourseID;
			mysqli_query( $this->db, $sql );

			$sql = "insert into CRC_NameCache (CourseID, UserID, Name) values ";
			$Sep = "";
			foreach( $this->Users as $UserID=>$Name ){
				$sql .= $Sep."(".$this->CourseID.",".$UserID.",'".mysqli_real_escape_string($this->db, $Name)."')";
				$Sep = ",";	
			}
			
			if( !isset( $this->Users[$_SESSION['user_id']])){
				$sql .= $Sep."(".$this->CourseID.",".$_SESSION['user_id'].",'".mysqli_real_escape_string($this->db, $_SESSION['fullname'])."')";
				$Sep = ",";	
			}
			mysqli_query( $this->db, $sql );			


			$sql = 'delete from CRC_Leaderboard where CourseID='.$this->CourseID;
			mysqli_query( $this->db, $sql );

			$sql = "insert into CRC_Leaderboard (CourseID, UserID, Points) values ";
			$Sep = "";
			foreach( $Scores as $UserID=>$Points ){
				$sql .= $Sep."(".$this->CourseID.",".$UserID.",'".$Points."')";
				$Sep = ",";	
			}
			mysqli_query( $this->db, $sql );			
			
			
			//ENDPOINT: url:GET|/api/v1/courses/:course_id/group_categories
			$GroupCategories = $Canvas->get("/courses/".$this->CourseID."/group_categories");
			
			
			$GroupCategoriesID = $GroupCategories[0]->id;
			
			$sql = "delete from CRC_GroupNames where CourseID=".$this->CourseID;
			$res = mysqli_query( $this->db, $sql );
			$sql = "delete from CRC_GroupCache where CourseID=".$this->CourseID;
			$res = mysqli_query( $this->db, $sql );
			
			//ENDPOINT: url:GET|/api/v1/group_categories/:group_category_id/groups
			$Groups = $Canvas->get("/group_categories/".$GroupCategoriesID."/groups");
			foreach( $Groups as $Group ){
				$sql = "insert into CRC_GroupNames (CourseID, ID, Name) values (".$this->CourseID.",".$Group->id.",'".mysqli_real_escape_string($this->db, $Group->name)."')";
				$res = mysqli_query( $this->db, $sql );
				
				//ENDPOINT: url:GET|/api/v1/groups/:group_id/memberships
				$Members = $Canvas->get("/groups/".$Group->id."/memberships");
				foreach( $Members as $Member ){
					$sql = "insert into CRC_GroupCache (CourseID, UserID, GroupID) values (".$this->CourseID.",".$Member->user_id.",".$Group->id.")";
					$res = mysqli_query( $this->db, $sql );
				}
			}

			
			return "Cache Updated";	
		}else{
			return "No Columns Selected";
		}
	}
	
	private function LoadUserNameCache(){
		$sql = 'select * from CRC_NameCache where CourseID='.$this->CourseID;
		$res = mysqli_query( $this->db, $sql );
		while( $row = mysqli_fetch_assoc($res )){
			$this->Users[$row['UserID']] = $row['Name'];
		}
	}
	private function LoadGroupNameCache(){
		$sql = 'select * from CRC_GroupNames where CourseID='.$this->CourseID;
		$res = mysqli_query( $this->db, $sql );
		while( $row = mysqli_fetch_assoc($res )){
			$this->Groups[$row['ID']] = $row['Name'];
		}
	}
	
	public function PrintIndividualBoard($Anonymous=false, $MaxCount = 5){
		
		$sql = 'select UserID,Points from CRC_Leaderboard where CourseID='.$this->CourseID." order by Points desc";
		$res = mysqli_query( $this->db, $sql);
		
		$Count = 0;
		
		$Ind = '<div class="LeaderboardHeader">Individual Leaderboard</div>';
		$Ind .= '<table border="1" class="LeaderboardTable">';
		$Ind .= '<tr><th class="LeaderboardNameColumn">Name</th><th class="LeaderboardScoreColumn">Score</th></tr>';

		while( $row = mysqli_fetch_assoc( $res )){
			$Count += 1;
			if( ( $Count <= $MaxCount ) || ($row['UserID'] == $this->UserID )){
				if( ($row['UserID'] == $this->UserID ) ){
					$CSS = ' class="LeaderboardCurrentPlayer"';
				}else{
					$CSS = '';
				}
				
				if( !isset( $this->Users[$row['UserID']] )){
					$this->LoadUserNameCache();
					if( !isset( $this->Users[$row['UserID']] )){
						$this->Users[$row['UserID']] = 'Unknown User';
					}
				}
				
				if( $Anonymous ){
					$matches = array();
					preg_match_all('/(?<=\s|^)[a-z]/i', $this->Users[$row['UserID']], $matches);
					$Name = strtoupper(implode('', $matches[0]));
				}else{
					$Name = $this->Users[$row['UserID']];
				}
				
				
				$Ind .= '<tr'.$CSS.'><td class="LeaderboardName">'.$Name.'</td><td class="LeaderboardScore">'.$row['Points'].'</td></tr>';
			}
		}
		
		$Ind .= '</table>';

		return $Ind;
	}
	
	public function LookupTeam( $UserID ){
		
		/*
		
		$sql = 'select GroupID from CRC_GroupCache where CourseID='.$this->CourseID." and UserID=".$UserID;
		$res = mysqli_query( $this->db, $sql  );
		if( mysqli_num_rows( $res ) != 1 ){
			return null;
		}else{
			$row = mysqli_fetch_assoc( $res );
			return $row['GroupID'];
		}
		
		*/
		
		
		require_once('./lib/canvas-php-curl-master/class.curl.php');
		$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );

		//ENDPOINT: url:GET|/api/v1/courses/:course_id/groups
		$GroupCategories = $Canvas->get("/courses/".$this->CourseID."/groups", array('only_own_groups'=>true));
		
		
		if( count($GroupCategories ) == 1 ){
			return $GroupCategories[0]->id;
		}else{
			return -1;
		}
	
	}
	
	
	public function PrintTeamBoard($Anonymous=false, $MaxCount = 5){
		$sql = 'select CRC_GroupCache.GroupID,avg(CRC_Leaderboard.Points) as AvgPoints from CRC_Leaderboard left join CRC_GroupCache on CRC_Leaderboard.CourseID=CRC_GroupCache.CourseID and CRC_Leaderboard.UserID=CRC_GroupCache.UserID where CRC_Leaderboard.CourseID='.$this->CourseID." group by CRC_GroupCache.GroupID order by AvgPoints desc";
		$res = mysqli_query( $this->db, $sql);
		
		$CurrentGroup = $this->LookupTeam( $this->UserID );
		
		$Team = '<div class="LeaderboardHeader">Team Leaderboard</div>';
		$Team .= '<table border="1" class="LeaderboardTable">';
		
		$Team .= '<tr><th class="LeaderboardNameColumn">Name</th><th class="LeaderboardScoreColumn">Score</th></tr>';

		$Count = 0;

		while( $row = mysqli_fetch_assoc( $res )){
			$Count += 1;
			if( isset( $row['GroupID'])){
				if( ( $Count <= $MaxCount ) || ($row['GroupID'] == $CurrentGroup )){
					if( ($row['GroupID'] == $CurrentGroup ) ){
						$CSS = ' class="LeaderboardCurrentPlayer"';
					}else{
						$CSS = '';
					}
					
					if( !isset( $this->Groups[$row['GroupID']] )){
						$this->LoadGroupNameCache();
					}
	
					$Team .= '<tr'.$CSS.'><td class="LeaderboardName">'.$this->Groups[$row['GroupID']].'</td><td class="LeaderboardScore">'.round($row['AvgPoints'],2).'</td></tr>';
				}
			}
		}
		
		$Team .= '</table>';

		return $Team;
		
	}
	public function PrintLastUpdateTime(){
		$sql = 'select UpdateTimestamp from CRC_Leaderboard where CourseID='.$this->CourseID.' order by UpdateTimestamp desc limit 1';
		$res = mysqli_query( $this->db, $sql );
		$row = mysqli_fetch_assoc( $res );
		$T = strtotime($row['UpdateTimestamp']);
		return date('M j, Y - h:i:s a',$T);
	}
	
}
	
	
?>