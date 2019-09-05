<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

include_once("InternetAgentApiContentSupportHelper.php");


function GetPageSupport($Request) {
	global $mysqli;
	if (!isset($Request["sub_method"]))
		return false;

	InitMysqli($mysqli);

	if (!isset($Request["token"]))
		return false;
	$UserID = GetUserByToken($Request["token"]);
	if ($UserID === false)
		return GetAnswerErrorHTTP("Неверный ключ доступа! Обновите ключ доступа в приложении.");
	$_SESSION["UserData"]["UserID"] = $UserID;	

	$sub_method = $Request["sub_method"];
	switch ($sub_method) {
		case "page" :
			$Content = GetPageSupportTemplate();
			break;
		case "content" :
			$Content = GetContentSupport($Request);
			break;
		case "post" :
			$Content = PostContentSupport($Request); 
			break;
		case "get_last_message_id" :
			$Content = GetContentSupportLastMessageIDJSON($Request); 
			break;

	}
	return $Content;
}
