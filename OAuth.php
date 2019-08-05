<?php

/**********************************************************************

Class for managing the OAuth workflow with Canvas

Based on the tutorial found at:
//http://tutorialspage.com/simple-oauth2-example-using-php-curl/

**********************************************************************/	

class API_OAuth{
	
	var $db;
	var $UserID;
	var $AccessToken = null;
	var $RefreshToken = null;
	
	function __construct( $UserID ){
		require_once('database.php');
		$this->db = db();
		$this->UserID = $UserID;
	}

	function Login(){
		
		if( isset( $_GET['code']) && isset( $_GET['state'] ) && strcmp( $_GET['state'],'CRC_Unverified' ) == 0 ){			
			$this->TradeCodeForKey( $_GET['code'] );
			return null;
		}elseif( !$this->LookupToken() ){
			return ACCESS_TOKEN_URL."?"
			   ."response_type=code"
			   ."&state=CRC_Unverified"
			   ."&client_id=". urlencode(CLIENT_ID)
			   ."&redirect_uri=". urlencode(CALLBACK_URL);
		}else{
			return null;
		}
	}
	
	function TradeCodeForKey($Code){
		$PostData = array(
			'grant_type' => 'authorization_code',
			'client_id'  => CLIENT_ID,
			'client_secret' => CLIENT_SECRET,
			'redirect_uri' => CALLBACK_URL,
			'code' => $Code
		);
		
					
		$ch = curl_init( AUTH_URL );
//			curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Authorization: Bearer ".$Token));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
//		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, HOST_SSL_LOCATION);
//		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, PEER_SSL_LOCATION);
//		curl_setopt($ch, CURLOPT_CERTINFO, 1);
//			curl_setopt($ch, CURLOPT_CAPATH, "/Users/matthewverleger/.localhost-ssl/");
//			curl_setopt($ch, CURLOPT_CAINFO, "/Users/matthewverleger/.localhost-ssl/cacert.pem");
//			curl_setopt($ch, CURLOPT_VERBOSE, 1);

//			curl_setopt($ch, CURLOPT_STDERR, fopen("/Users/matthewverleger/Sites/canvas/log/curl_debug.txt", "w+"));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData );
		
		$result = json_decode( curl_exec($ch) );
		curl_close($ch);

		$this->RefreshToken = $result->refresh_token;
		$this->Expires = time()+$result->expires_in;				
		$this->AccessToken = $result->access_token;
		$sql = "insert into LTI_Keys (UserID, AccessToken, RefreshToken, ExpirationDate) values ('".$this->UserID."','".$this->AccessToken."','".$this->RefreshToken."',".$this->Expires.")";
		mysqli_query( $this->db, $sql );
		return true;
	}

	
	function RefreshToken(){		
		$PostData = array(
			'grant_type' => 'refresh_token',
			'client_id'  => CLIENT_ID,
			'client_secret' => CLIENT_SECRET,
			'refresh_token' => $this->RefreshToken
		);
		
					
		$ch = curl_init( AUTH_URL );
//			curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Authorization: Bearer ".$Token));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, HOST_SSL_LOCATION);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, PEER_SSL_LOCATION);
//			curl_setopt($ch, CURLOPT_CAPATH, "/Users/matthewverleger/.localhost-ssl/");
//			curl_setopt($ch, CURLOPT_CAINFO, "/Users/matthewverleger/.localhost-ssl/cacert.pem");
//			curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_CERTINFO, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData );
		
		$result = json_decode( curl_exec($ch) );
/*
		
		if (curl_errno($ch)) {
			$error_msg = curl_error($ch);
			echo $error_msg;
		}		
		
*/
		curl_close($ch);

		$this->RefreshToken = $result->refresh_token;
		$this->Expires = time()+$result->expires_in;				
		$this->AccessToken = $result->access_token;
		$sql = "update LTI_Keys set AccessToken='".$this->AccessToken."', ExpirationDate=".$this->Expires." where UserID='".$this->UserID."'";
		//$sql = "insert into LTI_Keys (UserID, AccessToken, RefreshToken, ExpirationDate) values ('".$this->UserID."','".$this->AccessToken."','".$this->RefreshToken."',".$this->Expires.")";
		mysqli_query( $this->db, $sql );
		return $this->access_token;
	}
	
	function LookupToken(){
		$sql = "select * from LTI_Keys where UserID='".$this->UserID."'";
		$res = mysqli_query( $this->db, $sql );
		if( ( mysqli_num_rows( $res ) ) == 1 ) {
			$row = mysqli_fetch_assoc( $res );
			$this->AccessToken = $row['AccessToken'];
			$this->RefreshToken = $row['RefreshToken'];
			if( $row['Refreshable'] == 1 ){
				if( $row['ExpirationDate'] < time()){
					$this->RefreshToken();
				}
			}
			return true;
		}else{
			$this->AccessToken = null;
			$this->RefreshToken = null;		
			return false;
		}
	}
}
/*
if( isset( $_SESSION['user_id'])){
	echo "ERROR";
	echo "<hr><pre>";
	print_r( $_POST );
	echo "</pre>";
	echo "<hr><pre>";
	print_r( $_GET );
	echo "</pre>";
	print_r( $_SESSION );
	echo "</pre>";
}
*/




	
?>