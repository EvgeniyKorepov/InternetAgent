<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

include_once("InternetAgentApiConfig.php");
include_once("InternetAgentApiHelper.php");

$RemoteIP = $_SERVER["REMOTE_ADDR"];

MyLog($RemoteIP, $_REQUEST);

$Request = GetRequest();
if ($Request === false) {
	echo GetAnswerError($Request);
	return;
}

$_SESSION["Request"] = $Request;

$ErrorMessage = "";

switch ($Request["method"]) {

	case "config" :
		$Header = "Content-Type: application/json; charset=utf-8";
		$Content = GetAnswerConfig($Request);
		break;

	case "news" :
		$Header = "Content-Type: text/html; charset=utf-8";
		include_once("InternetAgentApiContentNews.php");
		$Content = GetPageNews($Request);
		break;

	case "info" :
		$Header = "Content-Type: text/html; charset=utf-8";
		include_once("InternetAgentApiContentInfo.php");
		$Content = GetPageInfo($Request);
		if ($Content === false)
			$Content = GetAnswerErrorHTTP("Неизвестная ошибка.");
		break;

	case "services" :
		$Header = "Content-Type: text/html; charset=utf-8";
		include_once("InternetAgentApiContentServices.php");
		$Content = GetPageServices($Request);
		if ($Content === false)
			$Content = GetAnswerErrorHTTP("Неизвестная ошибка.");
		break;

	case "support" :
		$Header = "Content-Type: text/html; charset=utf-8";
		include_once("InternetAgentApiContentSupport.php");
		$Content = GetPageSupport($Request);
		if ($Content === false)
			$ErrorMessage = "Error method support";
		break;

	default : 
		echo GetAnswerError($Request);
		exit;
}

header($Header);

if ($Content !== false) {
	echo $Content;
} else {
	MyLog("Ansfer", $Content);
	echo GetAnswerError($ErrorMessage);
}


