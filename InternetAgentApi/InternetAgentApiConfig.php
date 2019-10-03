<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

date_default_timezone_set("Europe/Moscow");

global $mysqli;

function InitMysqli(&$mysqli) {
	global $UTM_DB_base, $News_DB_base;
	$UTM_DB_host = 'localhost';
	$UTM_DB_user = 'InternetAgent';
	$UTM_DB_password = '******************';

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


function MyLog($Title, $Value = "", $Debug = false) {	
	$LogFile = "/var/log/InternetAgentHome/InternetAgent_".date("Y.m.d").".log";
	if ($Title == "EraseLogFile") {
		if (file_exists($LogFile))
			unlink(($LogFile));
		return;
	}
	if (is_array($Title))
		$Title = "\n".print_r($Title, true)."\n";

	if (is_array($Value))
		$Value = "\n".print_r($Value, true)."\n";

	$Message = date("Y.m.d H:i:s")." ".$Title.$Value."\r\n";
	if ($Debug)
		echo $Message;
	file_put_contents($LogFile, $Message, FILE_APPEND);	
}








