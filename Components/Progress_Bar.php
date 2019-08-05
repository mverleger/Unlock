<?php

class Progress_Bar{
	
	var $db;
	var $CourseID;
	var $AssignmentStatus;
	var $Settings;
	
	function __construct($db, $CourseID, $AssignmentStatus){
		$this->db = $db;
		$this->CourseID = $CourseID;
		$this->AssignmentStatus = $AssignmentStatus;
	}	
	
	
	public function ManageProgressBar($Post){
		if( $Post['ManageProgressBar'] == 1 ){
			
			$Form = '';

			

/*			
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
				$Form .=  '<option value="0" '.( (isset( $Levels[$Assignment->id])&&($Levels[$Assignment->id] == 0)) ? 'selected':'' ).'>Do Not Display</option>';
				$Form .=  '<option value="4" '.( (isset( $Levels[$Assignment->id])&&($Levels[$Assignment->id] == 4)) ? 'selected':'' ).'>Purple Level</option>';
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
*/
			return $Form;
					
		}elseif( $Post['ManageProgressBar'] == 2 ){
/*			
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
				if( $Values['Level'] > 0 && $Values['Group'] > 0 ){
					$AT_sql .= $AT_sep."(".$this->CourseID.",".$AID.",".$Values['Group'].",".$Values['Level'].",".$Values['Position'].")";
					$AT_sep = ",";
				}
			}
			mysqli_query( $this->db, $AT_sql );
			mysqli_query( $this->db, $ATL_sql );
*/
		}else{
			
		}
	}
	
	public function ManageProgressBarForm(){
		$Form = '<form action="index.php" class="adminForm" method="POST">';
		$Form .= '<input type="hidden" name="ManageProgressBar" value="1" />';
		$Form .= '<input type="submit"  class="submitButton" value="Manage Progress Bar">';
		$Form .= '</form>';
		return $Form;		
	}
	
	
	public function LoadSettings(){
		if( !isset( $this->Settings )){
			$sql = 'select * from CRC_ProgressBarSettings where CourseID='.$this->CourseID;
			$res = mysqli_query( $this->db, $sql );
			
			if( mysqli_num_rows($res ) != 1 ){
				echo "Progress Bar Settings Error<br>";
				exit();
			}else{
				$this->Settings = mysqli_fetch_assoc( $res );
			}
			
		}
	}
	
	public function DisplayProgressBar(){
		$this->LoadSettings();
		
		$Bar = '';
		$Bar .= '<div class="CurrentProgressBlock"><div class="HeaderText">Current Progress</div>';
		
		$Bar .= $this->SubheaderText();
		
		$Bar .= $this->DrawBar();
		$Bar .= $this->DrawProgress();
		$Bar .= '</div>';
		return $Bar;
	}
	
	public function SubheaderText(){
		$this->LoadSettings();
		$CurrentScore = $this->AssignmentStatus->TotalScore();
		
		if( $CurrentScore < $this->Settings['PurplePoints'] ){	
			$LevelUpText = 'Level up in '.($this->Settings['PurplePoints']-($CurrentScore))." points";
		}elseif( $CurrentScore < $this->Settings['WhitePoints'] ){
			$LevelUpText = 'Level up in '.($this->Settings['WhitePoints']-($CurrentScore))." points";
		}elseif( $CurrentScore < $this->Settings['BluePoints'] ){
			$LevelUpText = 'Level up in '.($this->Settings['BluePoints']-($CurrentScore))." points";
		}elseif( $CurrentScore < $this->Settings['GoldPoints'] ){
			$LevelUpText = 'No more levels, but there is more you can do!';
		}else{
			$LevelUpText = 'You finished everything!';
		}
				
		return '<div class="SubHeaderText">Current Score:'.$this->AssignmentStatus->TotalScore().'<br>'.$LevelUpText.'</div>';
	}
	
	public function GetCurrentLevel(){
		$this->LoadSettings();
		$CurrentScore = $this->AssignmentStatus->TotalScore();

		if( $CurrentScore < $this->Settings['PurplePoints'] ){	
			$CurrentLevel = 0;
		}elseif( $CurrentScore < $this->Settings['WhitePoints'] ){
			$CurrentLevel = 1;
		}elseif( $CurrentScore < $this->Settings['BluePoints'] ){
			$CurrentLevel = 2;
		}elseif( $CurrentScore < $this->Settings['GoldPoints'] ){
			$CurrentLevel = 3;
		}else{
			$CurrentLevel = 0;
		}
		return $CurrentLevel;		
	}
	
	public function DrawBar(){
		$this->LoadSettings();
		$Bar = '';
		$Bar .=  '<div class="ProgressBarBlock">';
		$Bar .=  '<table class="ProgressBarTable"><tr class="ProgressBarRow">';
		$Bar .=  '<td style="width:'.$this->Settings['PurpleSize'].'%;  color:white; background-color:rgb(144, 97, 162)">'.$this->Settings['PurpleLabel'].'</td>';
		$Bar .=  '<td style="width:'.$this->Settings['WhiteSize'].'%; color:black; background-color:rgb(220, 220, 220)">'.$this->Settings['WhiteLabel'].'</td>';
		$Bar .=  '<td style="width:'.$this->Settings['BlueSize'].'%; color:white; background-color:rgb(0,84,159)">'.$this->Settings['BlueLabel'].'</td>';
		$Bar .=  '<td style="width:'.$this->Settings['GoldSize'].'%; color:white; background-color:rgb(162, 144, 97)">'.$this->Settings['GoldLabel'].'</td>';
		$Bar .=  '</tr></table>';
		$Bar .=  '</div>';
		return $Bar;
		
	}
	
	public function DrawProgress(){
		$this->LoadSettings();
		$CurrentScore = $this->AssignmentStatus->TotalScore();
		
		$Offset = 20;
		$TotalWidth=725-$Offset;
		$Position = $Offset;
		
				
		if( $CurrentScore <= $this->Settings['PurplePoints'] ){			
			$Ratio = ($CurrentScore/$this->Settings['PurplePoints']);
			$Position += $Ratio*$this->Settings['PurpleSize']/100*$TotalWidth;
			
			
			
		}elseif( $CurrentScore <= $this->Settings['WhitePoints'] ){
			$Position += ($TotalWidth*$this->Settings['PurpleSize']/100);
			

			$Ratio = ($CurrentScore/$this->Settings['WhitePoints']);
			$Position += $Ratio*$this->Settings['WhiteSize']/100*$TotalWidth;
		}elseif( $CurrentScore <= $this->Settings['BluePoints'] ){
			$Position += ($TotalWidth*$this->Settings['PurpleSize']/100);
			$Position += ($TotalWidth*$this->Settings['WhiteSize']/100);
			
			$Ratio = ($CurrentScore/$this->Settings['BluePoints']);
			$Position += $Ratio*$this->Settings['BlueSize']/100*$TotalWidth;
		}elseif( $CurrentScore <= $this->Settings['GoldPoints'] ){
			$Position += ($TotalWidth*$this->Settings['PurpleSize']/100);
			$Position += ($TotalWidth*$this->Settings['WhiteSize']/100);
			$Position += ($TotalWidth*$this->Settings['BlueSize']/100);
			
			$Ratio = ($CurrentScore/$this->Settings['GoldPoints']);
			$Position += $Ratio*$this->Settings['GoldSize']/100*$TotalWidth;
		}else{
			$Position += $TotalWidth;
		}
		
		return '<div><img src="image_assets/ProgressTriangle.png" style="width:20px;margin-left:'.$Position.'px;" /></div>';
		
	}
	
	
	
}



?>