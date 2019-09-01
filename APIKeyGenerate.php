<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

$InternetAgentAPIUrl = "https://mydomain/api/app";

function StrToHex($string) {
  $hex='';
  for ($i=0; $i < strlen($string); $i++) {
	  $hex .= dechex(ord($string[$i]));
  }
  return $hex;
}

$Token = uniqid(random_int(11111111, 99999999), true);
$Token = str_replace(".", "", $Token);

$APIKeyPlainText = "{\"URL\":\"$InternetAgentAPIUrl\",\"Token\":\"$Token\"}";

$APIKey = StrToHex($APIKeyPlainText);
