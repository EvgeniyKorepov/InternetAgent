<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

include_once("InternetAgentApiContentServicesHelper.php");

function GetContentServices($Token) {
	global $urfa_admin, $mysqli;

	InitMysqli($mysqli);
	$UserID = GetUserByToken($Token);
	if ($UserID === false)
		return GetAnswerErrorHTTP("Неверный ключ доступа! Обновите ключ доступа в приложении.");

	MyLog("$Token\t$UserID");
	$_SESSION["UserData"]["UserID"] = $UserID;	

	if ($UserID === false)
		return false;

	if (ProcessingUri())
		return;

	return GetServicesHTML($Token);
}

function GetPageServices($Request) {
	if (!isset($Request["token"]))
		return false;
	$Token = $Request["token"];

	if (isset($Request["sub_method"])) {
		switch ($Request["sub_method"]) {
			case "button_kitchen" :
				$Content = GetLightKitchenContent(true);
				break;
		}
	} else 
		$Content = GetContentServices($Token);
	
	return $Content;
}
