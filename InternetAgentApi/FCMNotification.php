<?php

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);
class FCMNotificationConfig {
	protected $ServerToken = "03e3267************4679ab1bc155d"; 

	protected $DB_host = "127.0.0.1";
	protected $DB_user = "InternetAgent";
	protected $DB_password = "***************";
	protected $DB_base = "InternetAgent";
	protected $DB_table = "InternetAgentToken";
}

class FCMNotification extends FCMNotificationConfig {
	private $curl;
	private $mysqli;

	private $APIURL = "http://internetagent.flintnet.ru/api/";

	private $MessageSection = "support";

	public $AnsferJSON = false;
	public $Balance = false;

	public $Error = false;
	public $ErrorMessage = "";

	function __construct(){

		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
		$this->mysqli = new mysqli($this->DB_host, $this->DB_user, $this->DB_password, $this->DB_base);
		if ($this->mysqli->connect_errno) {
			$this->Error = true;
			$this->ErrorMessage = "Не удалось подключиться к MySQL: " . $this->mysqli->connect_error;
		}

		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_URL, $this->APIURL);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
/*
		$headers = array( 
			'Content-Type: application/json'
		);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
*/
		// Avoids problem with https certificate
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
	}

	function __destruct() {
		curl_close($this->curl);
	}

	private function GetUserTokenByUserID($UserID) {
		$DB_table = $this->DB_table;
		$query = "
			SELECT
				token
			FROM
				$DB_table	
			WHERE
				id = $UserID
		";
		$mysqli_result = $this->mysqli->query($query);
		if ($mysqli_result->num_rows != 1) 
			return false; 
		$row = $mysqli_result->fetch_array(MYSQLI_ASSOC);
		return $row["token"];
	}

	private function GetAllTokens() {
		$DB_table = $this->DB_table;
		$query = "
			SELECT
				id,
				token
			FROM
				$DB_table	
		";
		$mysqli_result = $this->mysqli->query($query);
		$ResultArray = array();
		while ($row = $mysqli_result->fetch_array(MYSQLI_ASSOC)) 
			$ResultArray[$row["User_id"]] = $row["token"];
		return $ResultArray;
	}

	private function GetUserActiveRegistrationCount($UserToken) {
		$URL = $this->APIURL . "?method=GetUser&ServerToken=" . $this->ServerToken . "&UserToken=" . $UserToken;
		$ContentJSON = file_get_contents($URL);
		if ($ContentJSON === false) {
			return false;		
		}
		$ContentArray = json_decode($ContentJSON, true);
		if (!isset($ContentArray["error"]["error"]) or $ContentArray["error"]["error"]) {
			return false;	
		}	
		$ActiveRegistrationCount = 0;
		foreach ($ContentArray["data"] as $Value) {
			if (isset($Value["Active"]) and $Value["Active"] == 1)
				$ActiveRegistrationCount++;
		}
		return $ActiveRegistrationCount;
	}

	private function HTTPPost($Data) {
		$POST_fields = array(
			"method" => "send",
			"data" => $Data,
		);
print_r($POST_fields);
echo "";
//		curl_setopt($this->curl, CURLOPT_HTTPGET, false);
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($POST_fields));

		// Execute post
		$response = curl_exec($this->curl);

//print_r($response);
//echo "\n";

	  $error = curl_errno($this->curl);
	  $error_message = curl_error($this->curl);

//echo "error=$error\n";
//echo "error_message=$error_message\n";
	  if ($error != 0) {
			$this->Error = true;
			$this->ErrorMessage = $error_message;
			return false;
		}
		return $response;
	}

	private function SendFCM($UserToken, $Title, $Message, $MessageID) {
		$MessagesPacketArray = array(
			"provider_token" => $this->ServerToken,
			"messages" => array(
				array(
					"user_token" => $UserToken, 
					"message_id" => $MessageID,
			    "message_title" => $Title,
					"message_body" => $Message,
					"message_section" => $this->MessageSection,
				),
			),
		);
print_r($MessagesPacketArray);
echo "\n";

		$ResultJSON = $this->HTTPPost($MessagesPacketArray);
//print_r($ResultJSON);
//echo "\n";
		if ($ResultJSON === false)	
			return false;
		$this->AnsferJSON = $ResultJSON;
		$ResultArray = json_decode($ResultJSON, true);
		if (!isset($ResultArray)) {
			$this->Error = true;
			$this->ErrorMessage = "Error decode JSON";
			return false;
		}
		if ($ResultArray["error"]["error"]) {
			$this->Error = true;
			$this->ErrorMessage = $ResultArray["error"]["message"];
			return false;
		}

//print_r($ResultArray);
//echo "\n";
/*
		$Result = false;
		foreach ($ResultArray["data"] as $Responce) {
			if (!isset($Responce["balance"])) 
				$this->Balance = $Responce["balance"];
			if (isset($Responce["message_id"]) and $Responce["message_id"] == $MessageID) {
				if (!isset($Responce["count_success"])) {
					$this->Error = true;
					if (!isset($Responce["error"])) {
						$this->ErrorMessage = $Responce["error"];
						return false;
					}
				} else 
					if ($Responce["count_success"] > 0) 
						$Result = array("device_count" => $Responce["count_success"]);
			}
		}
		return $Result;
*/

		return $ResultArray["data"];
	}

	public function IsExistUser($UserID) {
		$UserToken = $this->GetUserTokenByUserID($UserID);
		if ($UserToken === false)
			return false;
		$UserActiveRegistrationCount = $this->GetUserActiveRegistrationCount($UserToken);
		if ($UserActiveRegistrationCount === false)
			return false;
		if ($UserActiveRegistrationCount > 0)
			return true;
		else
			return false;
	}

	public function SetMessageSection($MessageSection) {
		$this->MessageSection = $MessageSection;
	}

	public function Send($UserID, $Title, $Message, $MessageID = false) {
echo "Send($UserID, $Title, $Message, $MessageID)\n";
		if ($MessageID === false)
			$MessageID = -1;
		$UserToken = $this->GetUserTokenByUserID($UserID);
		if ($UserToken === false)		
			return false;
echo "$UserToken=UserToken\n";
		$Result = $this->SendFCM($UserToken, $Title, $Message, $MessageID);
		return $Result;
	}

	public function SendToAll($Title, $Message, $MessageID = false) {
		if ($MessageID === false)
			$MessageID = -1;

		$UserTokensArray = $this->GetAllTokens();

		$ResultArray = array();

		foreach ($UserTokensArray as $UserID => $UserToken) {
			$Result = $this->SendFCM($UserToken, $Title, $Message, $MessageID);
			$ResultArray[$UserID] = $Result;
		}
		return $ResultArray;
	}

	
}

