<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

function GetContentInfo($Token) {
	global $urfa_admin, $mysqli;
	InitMysqli($mysqli);

	include_once("InternetAgentApiContentInfoHelper.php");
	$UserID = GetUserByToken($Token);
	if ($UserID === false)
		return GetAnswerErrorHTTP("Неверный ключ доступа! Обновите ключ доступа в приложении.");
	MyLog("$Token\t$UserID");
	$_SESSION["UserData"]["UserID"] = $UserID;	

	return GetAllInfoHTML();

}

function GetPageInfo($Request) {
	if (!isset($Request["token"]))
		return false;
	$Token = $Request["token"];

	$Content = GetContentInfo($Token);
	
	return $Content;
}
