<?php

require('xiaomi.php');

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

include("/opt/InternetAgentApi/FCMNotification.php");

$FCMNotification = new FCMNotification();

function InitMysqli(&$mysqli) {
	global $UTM_DB_base, $News_DB_base;
	$UTM_DB_host = 'localhost';
	$UTM_DB_user = 'InternetAgent';
	$UTM_DB_password = '*****************';

	$Result = true;
	if (@!$mysqli) {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
		$mysqli = new mysqli($UTM_DB_host, $UTM_DB_user, $UTM_DB_password, $UTM_DB_base);
		if ($mysqli->connect_errno) {
			MyLog("Не удалось подключиться к MySQL: " . $mysqli->connect_error);
		}
	} 
//	$mysqli->set_charset("utf8");
	return $Result;
}

function WriteMessage($UserID, $message) {
	global $mysqli;
	$UserID = $mysqli->real_escape_string($UserID);
	$message = $mysqli->real_escape_string($message);
	$query = "
		INSERT INTO
			InternetAgentMessage
			(`user_id`, `date`, `read`, `direction`, `author`, `text`)
			VALUES
			(2,          NOW(),  0,      1,          'Система', '$message')
	";
//	MyLog("WriteMessage", $query);
	$mysqli->select_db("InternetAgent");
	$mysqli_result = $mysqli->query($query);
//	MyLog("WriteMessage ***************************");
	if ($mysqli->affected_rows == 1) {
//		MyLog("WriteMessage succseful mysqli->insert_id=" . $mysqli->insert_id);
		return $mysqli->insert_id;
	} else {
//		MyLog("WriteMessage error");
		return false;
	}
}

function SendMessage($UserID, $SenderID, $MessageID) {
	global $FCMNotification;
	$Message = "";
	switch($SenderID) {
		case "plug" : 
			$Message.= "Кухня розетка: ";
			break;
		case "magnet" : 
			$Message.= "Входная дверь: ";
			break;
	}

	switch($MessageID) {
		case "on" : 
			$Message.= "Включено";
			break;
		case "off" : 
			$Message.= "Выключено";
			break;
		case "open" : 
			$Message.= "Открыто";
			break;
		case "close" : 
			$Message.= "Закрыто";
			break;
	}

	$MessageID = WriteMessage($UserID, $Message);
	$Title = "Событие Мой дом";
	$MessageSection = "support";

	$FCMNotification->SetMessageSection($MessageSection);
	echo "FCMNotification: $UserID, $Title, $Message, $MessageID\n";
	$FCMNotificationResult = $FCMNotification->Send($UserID, $Title, $Message, $MessageID);
	echo "FCMNotificationResult:\n";
	print_r($FCMNotificationResult);
	echo "\n";

}

InitMysqli($mysqli);



$UserID = 2;

$ip = '10.0.0.25';
$bind_ip = '10.0.0.1';

$debug = false;

$dev = new miIO($ip, $bind_ip, $debug);
/*
$DevicesArray = array();

$ResultArray = $dev->GetIDList();
	print_r($ResultArray);
	echo "\n**************************************\n";

if (!isset($ResultArray["data"]))
	exit;
$DevicesSIDArray = $ResultArray["data"];
*/
$DevicesSIDArray = array(
	"04cf8c86c143",
	"158d0002d4187c",
	"158d0002d6c8d4",
	"158d0002c4a2a6",
	"158d0002d6a0b2",
);

function ExternalDataSave($DevicesArray) {
	$FileExternalDataPath = "/opt/InternetAgentApi/ExternalData.json";
	$JSON = json_encode($DevicesArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	file_put_contents($FileExternalDataPath, $JSON);
}




while (true) {

	foreach ($DevicesSIDArray as $DeviceSID) {
		$DeviceArray = $dev->ReadSID($DeviceSID);
		
		if (isset($DevicesArray[$DeviceSID]["data"]["status"]) and isset($DeviceArray["data"]["status"]))
			if ($DevicesArray[$DeviceSID]["data"]["status"] <> $DeviceArray["data"]["status"]) {
				echo "\n" . $DeviceArray["model"] . "\t" . $DeviceArray["data"]["status"] . "\n";
				SendMessage($UserID, $DeviceArray["model"], $DeviceArray["data"]["status"]);
			}				
		$DevicesArray[$DeviceSID] = $DeviceArray;
		usleep(50000);
	}

/*
	echo "DevicesArray:\n";
	print_r($DevicesArray);
	echo "\n-----------------------------------------------\n";
*/

//		usleep(50000);
	ExternalDataSave($DevicesArray);
	echo ".";

//break;
//	usleep(50000);
	usleep(50000);
}
	
