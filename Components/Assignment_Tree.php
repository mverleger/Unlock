<?php

class AssignmentTree{
	var $db;
	var $AssignmentCompletion;
	var $AssignmentStatus;
	var $Labels;
	
	function __construct($db, $CourseID, $AccessToken, $AssignmentStatus){
		$this->db = $db;
		$this->CourseID = $CourseID;
		$this->AccessToken = $AccessToken;		
		$this->AssignmentStatus = $AssignmentStatus;
	}
	
	public function ManageAssignmentTree($Post){
		$Columns = array(1 => "UpperLeft", 2 => "UpperRight",3 => "LowerLeft", 4 => "LowerRight", 5=>"Above", 6=>"Below");
		if( $Post['ManageAssignmentTree'] == 1 ){
			
			$Form = '';
			
			$Groups = array();
			$Levels = array();
			$Positions = array();
			
			$sql = 'select * from CRC_AssignmentTree where CourseID='.$this->CourseID;
			$res = mysqli_query( $this->db, $sql );
			while( $row = mysqli_fetch_assoc( $res )){
				$Groups[$row['AssignmentID']] = $row['Grouping'];
				$Levels[$row['AssignmentID']] = $row['DisplayLevel'];
				$Positions[$row['AssignmentID']] = $row['Position'];
			}
			
			
			require_once('./lib/canvas-php-curl-master/class.curl.php');
			$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );
			
			//ENDPOINT: url:GET|/api/v1/courses/:course_id/assignments
			$AssignmentList = $Canvas->get("/courses/".$this->CourseID."/assignments");


			$Form .= '<form action="index.php" method="POST">';
			$Form .= '<input type="hidden" name="ManageAssignmentTree" value="2" />';

			$Form .=  '<table border="1"><tr><th>Assignment Name</th><th>Group</th><th>Level</th><th>Position</th></tr>';
			foreach( $AssignmentList as $Assignment ){
				$Form .=  "<tr><td>";
				
				$Form .=  $Assignment->name;
				
				$Form .=  "</td><td>";
				$Form .=  '<select name="Group_'.$Assignment->id.'">';
				$Form .=  '<option value="0" '.( (isset( $Groups[$Assignment->id])&&($Groups[$Assignment->id] == 0)) ? 'selected':'' ).'>Do Not Display</option>';
				$Form .=  '<option value="1" '.( (isset( $Groups[$Assignment->id])&&($Groups[$Assignment->id] == 1)) ? 'selected':'' ).'>Hive Group 1</option>';
				$Form .=  '<option value="2" '.( (isset( $Groups[$Assignment->id])&&($Groups[$Assignment->id] == 2)) ? 'selected':'' ).'>Hive Group 2</option>';
				$Form .=  '<option value="3" '.( (isset( $Groups[$Assignment->id])&&($Groups[$Assignment->id] == 3)) ? 'selected':'' ).'>Hive Group 3</option>';
				$Form .=  '<option value="4" '.( (isset( $Groups[$Assignment->id])&&($Groups[$Assignment->id] == 4)) ? 'selected':'' ).'>Hive Group 4</option>';
				
				$Form .=  '</select>';

				$Form .=  "</td><td>";
				
				$Form .=  '<select name="Level_'.$Assignment->id.'">';
				$Form .=  '<option value="-1" '.( (isset( $Levels[$Assignment->id])&&($Levels[$Assignment->id] == -1)) ? 'selected':'' ).'>Do Not Display</option>';
				$Form .=  '<option value="0" '.( (isset( $Levels[$Assignment->id])&&($Levels[$Assignment->id] == 0)) ? 'selected':'' ).'>Purple Level</option>';
				$Form .=  '<option value="1" '.( (isset( $Levels[$Assignment->id])&&($Levels[$Assignment->id] == 1)) ? 'selected':'' ).'>White Level</option>';
				$Form .=  '<option value="2" '.( (isset( $Levels[$Assignment->id])&&($Levels[$Assignment->id] == 2)) ? 'selected':'' ).'>Blue Level</option>';
				$Form .=  '<option value="3" '.( (isset( $Levels[$Assignment->id])&&($Levels[$Assignment->id] == 3)) ? 'selected':'' ).'>Gold Level</option>';
				
				$Form .=  '</select>';
				
				
				$Form .=  "</td><td>";
				$Form .= '<input type="textbox" name="Posit_'.$Assignment->id.'" / value="'.(isset($Positions[$Assignment->id]) ? $Positions[$Assignment->id] : '').'">';				
				
				$Form .=  "</td></tr>";
			}
			$Form .=  "</table>";
			
			
			
			$Labels = array();
			$sql = 'select * from CRC_AssignmentTreeLabels where CourseID='.$this->CourseID;
			$res = mysqli_query( $this->db, $sql );
			while( $row = mysqli_fetch_assoc( $res )){
				if( !isset( $Labels[$row['Grouping']])){
					$Labels[$row['Grouping']] = array();
				}
				
				$Labels[$row['Grouping']][$row['Location']] = $row['Label'];
			}
			
			$Form .= '<table border="1"><tr><th>Group</th>';
			for( $J = 1; $J <= count( $Columns ); $J+=1 ){
				$Form .= '<th>'.$Columns[$J].'</th>';
			}
			$Form .= '</tr>';
			for( $Grouping = 1; $Grouping <= 4; $Grouping+=1){
				$Form .=  "<tr><td>".$Grouping."</td>";
				for( $J = 1; $J <= count( $Columns ); $J+=1 ){
					if( isset( $Labels[$Grouping][$Columns[$J]] ) ){
						$Value = $Labels[$Grouping][$Columns[$J]];
					}else{
						$Value = '';
					}
					
					$Form .=  '<td><input type="text" name="Label_'.$Grouping.'_'.$J.'" value="'.$Value.'" /></td>';
				}
				$Form .= "</tr>";
			}
			
			$Form .= '</table>';			
			
			
			$Form .= '<input type="hidden" name="UpdateAssignmentTree" value="1" />';
			$Form .= '<input type="submit" value="Save">';
			$Form .= '</form>';
			
			return $Form;
					
		}elseif( $Post['ManageAssignmentTree'] == 2 ){
			
			$sql = 'delete from CRC_AssignmentTree where CourseID='.$this->CourseID;
			$res = mysqli_query( $this->db, $sql );
			$sql = 'delete from CRC_AssignmentTreeLabels where CourseID='.$this->CourseID;
			$res = mysqli_query( $this->db, $sql );
			
			$AT_sql = 'insert into CRC_AssignmentTree (CourseID, AssignmentID, Grouping, DisplayLevel, Position) values ';
			$AT_sep = '';
			
			$ATL_sql = 'insert into CRC_AssignmentTreeLabels (CourseID, Grouping, Location, Label) values ';
			$ATL_sep = '';
			
			$Assignments = array();
			foreach( $Post as $Key=>$Value ){
				if( strcmp( substr($Key, 0,6 ), 'Level_' ) == 0 ){
					$AssignmentID = substr( $Key, 6 );
					if( !isset( $Assignments[$AssignmentID] )){
						$Assignments[$AssignmentID] = array();
					}
					$Assignments[$AssignmentID]['Level'] = $Value;
				}elseif( strcmp( substr($Key, 0,6 ), 'Group_' ) == 0 ){
					$AssignmentID = substr( $Key, 6 );
					if( !isset( $Assignments[$AssignmentID] )){
						$Assignments[$AssignmentID] = array();
					}
					$Assignments[$AssignmentID]['Group'] = $Value;
				}elseif( strcmp( substr($Key, 0,6 ), 'Posit_' ) == 0 ){
					$AssignmentID = substr( $Key, 6 );
					if( !isset( $Assignments[$AssignmentID] )){
						$Assignments[$AssignmentID] = array();
					}
					$Assignments[$AssignmentID]['Position'] = $Value;
				}elseif( strcmp( substr($Key, 0,6 ), 'Label_' ) == 0 ){
					$KeyExplode = explode("_", $Key);
					$Grouping = mysqli_real_escape_string( $this->db, $KeyExplode[1]);
					$Location = $Columns[mysqli_real_escape_string( $this->db, $KeyExplode[2])];
					$Label = mysqli_real_escape_string( $this->db, $Value );
					
					
					$ATL_sql .= $ATL_sep."(".$this->CourseID.",".$Grouping.",'".$Location."','".$Label."')";
					$ATL_sep = ',';
					
				}				
			}
			
			foreach( $Assignments as $AID=>$Values ){
				if( $Values['Level'] >= 0 && $Values['Group'] > 0 ){
					$AT_sql .= $AT_sep."(".$this->CourseID.",".$AID.",".$Values['Group'].",".$Values['Level'].",".$Values['Position'].")";
					$AT_sep = ",";
				}
			}
			mysqli_query( $this->db, $AT_sql );
			mysqli_query( $this->db, $ATL_sql );
			
		}else{
			
		}
	}
	
	public function ManageAssignmentTreeForm(){
		$Form = '<form action="index.php" class="adminForm" method="POST">';
		$Form .= '<input type="hidden" name="ManageAssignmentTree" value="1" />';
		$Form .= '<input type="submit"  class="submitButton" value="Manage Assignment Tree">';
		$Form .= '</form>';
		return $Form;		
	}

	public function LoadCompletionStatus(){
		require_once('./lib/canvas-php-curl-master/class.curl.php');
		$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );
		
		$Groups = array();
		$Levels = array();
		$sql = 'select * from CRC_AssignmentTree where CourseID='.$this->CourseID;
		$res = mysqli_query( $this->db, $sql );
		while( $row = mysqli_fetch_assoc( $res )){
			$Groups[$row['AssignmentID']] = $row['Grouping'];
			$Levels[$row['AssignmentID']] = $row['DisplayLevel'];
		}
		
		$Args = array( "assignment_ids[]"=>array_keys($Groups));
		
		
		//ENDPOINT: url:GET|/api/v1/courses/:course_id/students/submissions
		$AssignmentList = $Canvas->get("/courses/".$this->CourseID."/students/submissions");
		
						
		$this->AssignmentCompletion = array();
		foreach( $AssignmentList as $Assignment){
			if( isset( $Assignment->assignment_id ) ){
				if( isset( $Assignment->score) && ( $Assignment->score > 0 )){
					$this->AssignmentCompletion[$Assignment->assignment_id] = $Assignment->score;
				}else{
					$this->AssignmentCompletion[$Assignment->assignment_id] = 0;
				}
			}			
		}
	}
	
	public function LoadAssignmentNames(){
		
		
		
		require_once('./lib/canvas-php-curl-master/class.curl.php');
		$Canvas = new CanvasCurl( $this->AccessToken, API_DOMAIN );
				
		//ENDPOINT: url:GET|/api/v1/courses/:course_id/assignments
		$AssignmentList = $Canvas->get("/courses/".$this->CourseID."/assignments");
		
/*

		echo "<pre>";		
		print_r( $AssignmentList );
		echo "</pre>";
		
*/

		$this->AssignmentNames = array();
		foreach( $AssignmentList as $Assignment ){
			$this->AssignmentNames[$Assignment->id] = $Assignment->name;
		}
	}
	
	public function LoadLabels(){
		$sql = 'select * from CRC_AssignmentTreeLabels where CourseID='.$this->CourseID;
		$res = mysqli_query( $this->db, $sql );
		while( $row = mysqli_fetch_assoc($res )){
			if( !isset( $this->Labels )){
				$this->Labels = array();
			}
			if( !isset( $this->Labels[$row['Grouping']])){
				$this->Labels[$row['Grouping']] = array();
			}

			$this->Labels[$row['Grouping']][$row['Location']] = $row['Label'];
		}
	}
	
	
	public function GetLabel( $Grouping, $Location ){
		if( isset( $this->Labels[$Grouping][$Location] ) ){
			return $this->Labels[$Grouping][$Location];
		}else{
			return '';
		}
	}
	

	
	public function DisplayAssignmentTree(){
		$this->LoadCompletionStatus();
		$this->LoadAssignmentNames();
		$DisplayTable = array();
		
		$sql = 'select * from CRC_AssignmentTree where CourseID='.$this->CourseID." order by Grouping, Position asc, DisplayLevel";
		$res = mysqli_query( $this->db, $sql );
		
		$Index = 1;
		$PrevGrouping = 0;
		$Unlocked = array();
		$this->LoadLabels();
		$Unlocked = $this->AssignmentStatus->CalculateLockStatus();
		
		while( $row = mysqli_fetch_assoc( $res )){
			if( $row['Grouping'] != $PrevGrouping ){
				if( $PrevGrouping != 0 ){
					echo '</ul>';
					echo '<span class="LabelLeft">'.$this->GetLabel( $PrevGrouping, 'LowerLeft').'</span>';
					echo '<span class="LabelCenter">'.$this->GetLabel( $PrevGrouping, 'Below').'</span>';
					echo '<span class="LabelRight">'.$this->GetLabel( $PrevGrouping, 'LowerRight').'</span>';
					
					echo '</div>';
				}
				echo '<div class="HexGroup'.$row['Grouping'].'">';
				
				echo '<span class="LabelLeft">'.$this->GetLabel( $row['Grouping'], 'UpperLeft').'</span>';
				echo '<span class="LabelCenter">'.$this->GetLabel( $row['Grouping'], 'Above').'</span>';
				echo '<span class="LabelRight">'.$this->GetLabel( $row['Grouping'], 'UpperRight').'</span>';
				
				echo '<ul id="hexGrid">';
				$PrevGrouping = $row['Grouping'];
			}
			
			$AID = $row['AssignmentID'];

			if( isset( $this->AssignmentCompletion[$AID] ) && $this->AssignmentCompletion[$AID] > 0 ){
				$Complete = "Complete";
			}else{
				$Complete = "Incomplete";
			}
			echo '<li class="hex Level'.$row['DisplayLevel'].'"><div class="hexIn">';
			
			
			if( $Unlocked[$AID] == 1 ){
				echo '<a class="hexLink" href="https://'.API_DOMAIN.'/courses/'.$this->CourseID.'/assignments/'.$AID.'/" target="_parent">';
			}else{
				echo '<div class="hexLink">';
			}
			
			
			if( strcmp( $Complete, 'Complete' ) == 0 ){
				echo '<img src="'.LOCAL_URL.'image_assets/'.$Complete.'.png" alt="'.$this->AssignmentNames[$AID].' - '.$Complete.'" />';
			}elseif($Unlocked[$AID] != 1){
				echo '<img src="'.LOCAL_URL.'image_assets/Locked.png" alt="'.$this->AssignmentNames[$AID].' - Locked" />';
			}
			
			echo '<h1>';
			echo $this->AssignmentNames[$AID];
			echo '</h1>';
			
			echo '<p class="'.$Complete.'">'.$Complete.' <br /> '.$this->AssignmentStatus->OutOf($AID).'</p>';
			
			if( $Unlocked[$AID] == 1 ){
				echo '</a>';
			}else{
				echo '</div>';		
			}
			
			echo '</div></li>';



			$Index += 1;


		}
		echo '</ul>';
		
		echo '<span class="LabelLeft">'.$this->GetLabel( $row['Grouping'], 'LowerLeft').'</span>';
		echo '<span class="LabelCenter">'.$this->GetLabel( $row['Grouping'], 'Below').'</span>';
		echo '<span class="LabelRight">'.$this->GetLabel( $row['Grouping'], 'LowerRight').'</span>';

		echo '</div>';


	}
		
		
     		
		
		
		
}






?>