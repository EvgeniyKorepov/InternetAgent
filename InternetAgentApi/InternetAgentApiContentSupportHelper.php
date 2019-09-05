<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

function WriteMessage($UserID, $message) {
	global $mysqli;
	$UserID = $mysqli->real_escape_string($UserID);
	$message = $mysqli->real_escape_string($message);
	$query = "
		INSERT INTO
			InternetAgentMessage
			(`user_id`, `date`, `read`, `direction`, `author`, `text`)
			VALUES
			('$UserID', NOW(), 0, 0, '', '$message')
	";
	MyLog("WriteMessage", $query);
	$mysqli->select_db("InternetAgent");
	$mysqli_result = $mysqli->query($query);
	if ($mysqli->affected_rows() == 1)
		return true;
	else
		return false;
}

function GetContentSupportLastMessageIDJSON($Request) {
	$UserID = $_SESSION["UserData"]["UserID"];
	$LastMessageID = GetContentSupportLastMessageID($UserID);
	$ResultArray = array(
		"method"	=> $Request["method"],
		"sub_method"	=> $Request["sub_method"],
		"last_message_id" => $LastMessageID,
	  "error"		=> array(
			"error"		=> false,
			"message"	=> "",
		),
	);
	$Content = json_encode($ResultArray, JSON_PRETTY_PRINT);
	return $Content; 
} 

function GetContentSupportArray() {
	global $mysqli;
	$UserID = $_SESSION["UserData"]["UserID"];
	$query_update = "
		UPDATE
			InternetAgentMessage
		SET 
			`read` = 1
		WHERE
			date > DATE_ADD(NOW(), INTERVAL -6 MONTH) AND
			user_id = $UserID
	";

	$query = "
		SELECT
			UNIX_TIMESTAMP(date) as `datetime`,
			text as message,
			direction,
			author
		FROM
			InternetAgentMessage
		WHERE
			date > DATE_ADD(NOW(), INTERVAL -6 MONTH) AND
			(user_id = $UserID OR user_id = 0)
		ORDER BY
			date
	";

	$mysqli->select_db("InternetAgent");
	$mysqli->query($query_update);
	$mysqli_result = $mysqli->query($query);
	$num_rows = $mysqli_result->num_rows;
	$ResultArray = array();
	$Index = 0;
	while ($row = $mysqli_result->fetch_array(MYSQLI_ASSOC)) {
		$ResultArray[$Index] = $row;
	  $Index++;
	}
	return $ResultArray; 
}

function GetContentSupport($Request) {
	$DateTimeFormat = "d.m.Y H:i:s";

	$ResultArray = GetContentSupportArray();
//print_r($ResultArray);
//exit;
	$Content = "
		<ul>
	";
	$Index = 0;
	foreach ($ResultArray as $Value) {
		$Message = $Value["message"];
//		$url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
//		$Message = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $Message);

		$Direction = $Value["direction"];
		$DateTime = date($DateTimeFormat, $Value["datetime"]);
		$Author = $Value["author"];
		if ($Direction == 0) {
			$Content.= "
				<li>
					<div class=\"message-data\">
						<span class=\"message-data-name\"><!--<i class=\"fa fa-circle online\"></i>-->$Author</span>
						<span class=\"message-data-time\">$DateTime</span>
						</div>
						<div class=\"message my-message\">
							$Message
					</div>
				</li>
			";
		} else {
			$Content.= "
				<li class=\"clearfix\">
					<div class=\"message-data align-right\">
						<span class=\"message-data-time\" >$DateTime</span> &nbsp; &nbsp;
						<span class=\"message-data-name\" >$Author</span> <!--<i class=\"fa fa-circle me\"></i>-->
						</div>
						<div class=\"message other-message float-right\">
							$Message
					</div>
				</li>
			";
		}
	  $Index++;
	}
	$Content.= "
		</ul>
	";
	return $Content; 
}


function PostContentSupport($Request) {
	$ResultArray = array(
		"method" => $Request["method"],
		"sub_method" => $Request["sub_method"],
	  "error"	=> array(
			"error"		=> true,
			"message"	=> "",
		),
	);
	$UserID = $_SESSION["UserData"]["UserID"];
	MyLog("PostContentSupport", $Request);
	if (isset($Request["message"])) {
		$message = $Request["message"];
		if (WriteMessage($UserID, $message))
			$ResultArray["error"]["error"] = false;
	}
	$Content = json_encode($ResultArray);
	return $Content;
}

function GetPageSupportTemplate() {
	$ColorArray = array(
		"%ClientColor%" => "#E7EAFB",
		"%SupportColor%" => "#B8F0BE",
	);
	$Content = file_get_contents(__DIR__."/InternetAgentApiContentSupportTemplate.html");
	foreach ($ColorArray as $Mask => $Color)
		$Content = str_replace($Mask, $Color, $Content);

//	URLTemplate
	return $Content;
}

function GetContentSupportLastMessageID($UserID) {
	global $mysqli;
	$query = "
		SELECT
			id
		FROM
			InternetAgentMessage
		WHERE
			direction = 1 AND
			(user_id = $UserID OR user_id = 0)
		ORDER BY
			date DESC
		LIMIT 1
	";
	$mysqli->select_db("InternetAgent");
	$mysqli_result = $mysqli->query($query);
	$Result = 0;
	if ($mysqli_result->num_rows == 1) {
		$row = $mysqli_result->fetch_array(MYSQLI_ASSOC);
	  $Result = $row["id"];
	}
	return $Result;
} 


