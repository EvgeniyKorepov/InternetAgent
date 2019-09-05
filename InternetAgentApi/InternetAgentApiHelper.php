<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);


function JSONEncode($Array) {
	return json_encode($Array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function GetUserByToken($Token) {
	global $mysqli;
	$Token = $mysqli->real_escape_string($Token);
	$query = "
		SELECT
			id
		FROM
			InternetAgentToken
		WHERE
			token = '$Token'
	";
	$mysqli->select_db("InternetAgent");
	$mysqli_result = $mysqli->query($query);
	$num_rows = $mysqli_result->num_rows;
	if ($num_rows == 0)
		return false;
	$row = $mysqli_result->fetch_array(MYSQLI_ASSOC);
	return $row["id"];
}

function FormatMessage($Message, $Color = '', $HTag = 'h5') {
	if ($HTag != "") {
		$HTagOpen = "<$HTag>";
		$HTagClose = "</$HTag>";
	} else {
		$HTagOpen = "";
		$HTagClose = "";
	}
	if ($Color=='')	{
		$MessageHeader = "<span>$HTagOpen";
		
		$MessageHeaderFooter = "$HTagClose</span>";
	} else {
		$MessageHeader = "<span>$HTagOpen<font color=\"$Color\">";
		$MessageHeaderFooter = "</font>$HTagClose</span>";
	}
	$Message = $MessageHeader.$Message.$MessageHeaderFooter;
	return $Message;
}


function FormatAlert($Message, $AlertClass = "primary") {
//	https://getbootstrap.com/docs/4.1/components/alerts/
//		<div class="alert alert-primary" role="alert">
//		<div class="alert alert-secondary" role="alert">
//		<div class="alert alert-success" role="alert">
//		<div class="alert alert-danger" role="alert">
//		<div class="alert alert-warning" role="alert">
//		<div class="alert alert-info" role="alert">
//		<div class="alert alert-light" role="alert">
//		<div class="alert alert-dark" role="alert"> 
	return "<div class=\"alert alert-$AlertClass\" role=\"alert\">$Message</div>";
}           

function GetRequest() {
	$Result = false;
	if (!isset($_REQUEST["request"]))
		return $Result;
	$Request = $_REQUEST["request"];
	$Request = urldecode($Request);
	$RequestArray = json_decode($Request, true);
	if (!isset($RequestArray))
		return $RequestArray;
	if (!isset($RequestArray["method"]))
		return $Result;
	return $RequestArray;
}

function GetAnswerErrorHTTP($Message, $AlertClass = "danger") {
	$Content = "";
	$Content.= "
		<!DOCTYPE html>
			<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"ru-ru\" lang=\"ru-ru\" dir=\"ltr\">
		    <head>
    	    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
          <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
					<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
					<link href=\"/api/app/css/bootstrap.css\" rel=\"stylesheet\" type=\"text/css\" />
				</head>
			<body>
	";
	$Content.= FormatAlert($Message, $AlertClass);
	$Content.= "
			</body>
		</html>
	";
	return $Content;
}

function GetAnswerError($Request) {
	$TemplateAnswerError = array(
		"method"	=> NULL,
	  "error"		=> array(
			"error"		=> true,
			"message"	=> "",
		),
	);
	if ($Request === false) {
		$TemplateAnswerError["error"]["message"] = "request is null";
		return JSONEncode($TemplateAnswerError);
	} else {
		$TemplateAnswerError["error"]["message"] = $Request;
		return JSONEncode($TemplateAnswerError);
	}
}


function GetAnswerConfig($Request) {
	global $mysqli;

	if (!isset($Request["token"]))
		return false;
	$Token = $Request["token"];

	InitMysqli($mysqli);

	$UserID = GetUserByToken($Token);
	if ($UserID === false)
		return GetAnswerErrorHTTP("Неверный ключ доступа! Обновите ключ доступа в приложении.");

	MyLog("$Token\t$UserID");
	$_SESSION["UTM5"]["AccountData"]["AccointID"] = $UserID;	

	include("InternetAgentApiConfigClient.php");
	return JSONEncode($ClientConfig);
}


function AddSpanNoWrap($Value) {
	return "<span style=\"white-space:nowrap;\">$Value<span/>";
}

function ArrayToHTML($Array) {
	$Content = "
		<table class=\"table table-sm table-hover\">
	";
	foreach($Array as $SectionKey => $SectionValue)	{
		$Content.= "
		  <thead>
		    <tr>
		      <th scope=\"col\" style=\"text-align: center;\">$SectionKey</th>
		    </tr>
		  </thead>
			<tbody>
		";
		foreach($SectionValue as $Value) {
			if (!is_array($Value)) {
				$Content.= "
			    <tr>
			      <td>$Value</td>
    			</tr>
				";
			} else {
				$Title = $Value["Title"];
				$Text = $Value["Text"];
				$Value = "<span style=\"float:left;\">$Title</span>" . "<span style=\"float:right;\">$Text</span>";
				$Content.= "
			    <tr>
			      <td>$Value</td>
    			</tr>
				";
			}
		}
		$Content.= "
			</tbody>
		";
	}
	$Content.= "
		</table>
	";
	return $Content;
}






