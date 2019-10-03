<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

function ProcessingUri() {

	$Result = false;
  if (!isset($_REQUEST["command"])) {
		return $Result;
	}
	switch ($_REQUEST["command"]) {
		case "LightKitchenSwitch" :
			include_once("/opt/InternetAgentApi/xiaomi.php");
			$dev = new miIO('10.0.0.25', '10.0.0.1');
			$dev->PlugSwitch();

/*
			if ($_REQUEST["Action"] == "on") {
				$ArrayExternalData = ExternalDataLoad();
				$ArrayExternalData["LightKitchenStatus"] = true;
				ExternalDataSave($ArrayExternalData);
			} else {
				$ArrayExternalData = ExternalDataLoad();
				$ArrayExternalData["LightKitchenStatus"] = false;
				ExternalDataSave($ArrayExternalData);
			}
*/
			$Result = true;
			break;		

		case "iRobotSwitch" :
			if ($_REQUEST["Action"] == "on") {
				$ArrayExternalData = ExternalDataLoad();
				$ArrayExternalData["iRobotStatus"] = true;
				ExternalDataSave($ArrayExternalData);
			} else {
				$ArrayExternalData = ExternalDataLoad();
				$ArrayExternalData["iRobotStatus"] = false;
				ExternalDataSave($ArrayExternalData);
			}
			$Result = true;
			break;		


	}

	if ($Result) 
		header("Location: ".$_SERVER["REQUEST_URI"]);
	return $Result;
}

function GetLightKitchenContent($isJS = false) {
	$ArrayExternalData = ExternalDataLoad();

/*
	include_once("/opt/InternetAgentApi/xiaomi.php");
	$dev = new miIO('10.0.0.25', '10.0.0.1');

	$PlugStatus = "unknown";
//	while ($PlugStatus == "unknown")
		$PlugStatus = $dev->PlugStatus();
*/
	$Disable = "";
	$StatusDiv = $ArrayExternalData["158d0002d4187c"]["data"]["status"];
	switch($ArrayExternalData["158d0002d4187c"]["data"]["status"]) {
		case "on": 
			$Action = "off";
			$Text = "Выключить";
			$Info = "Свет " . FormatMessage("Включен", "green", "");
			break;
		case "off": 
			$Action = "on";
			$Text = "Включить";
			$Info = "Свет " . FormatMessage("Выключен", "red", "");
			break;
		case "unknown": 
			$Action = "Ожидаем состояние...";
			$Text = "Включить";
			$Info = "Свет " . FormatMessage("Выключен", "red", "");
			$Disable = "disable";
			break;
	}

	$Content = "";
	$Content.= FormatAlert("Кухня", "info");
		
	$Content.= "
 		<div style=\"margin: 0 0 0 15px\">
			<div id=\"LightKitchenStatus\" style=\"display : none;\">$StatusDiv</div>
			<label class=\"form-check-label\">$Info</label>
			<form method=\"POST\" style=\"margin: 0 0 0 0;\">
				<input name=\"command\" type=\"hidden\" value=\"LightKitchenSwitch\">
				<input name=\"Action\" type=\"hidden\" value=\"$Action\">
				<div class=\"form-group\">
					<input type=\"submit\" class=\"btn btn-primary\" $Disable value=\"$Text\">\n
			  </div>
			</form>
		</div>
	";

	if (!$isJS)
		return $Content;

	$Content = array(
		"status" => $ArrayExternalData["158d0002d4187c"]["data"]["status"],
		"content" => "$Content",
	);
	$Content = json_encode($Content);
	return $Content;
}

function GetiRobotContent() {
	$ArrayExternalData = ExternalDataLoad();
	$iRobotStatus = $ArrayExternalData["iRobotStatus"];
	if ($iRobotStatus) {
		$Action = "off";
		$Text = "Остановить";
		$Info = "iRobot " . FormatMessage("Запущен", "green", "");
	} else {
		$Action = "on";
		$Text = "Запустить";
		$Info = "iRobot " . FormatMessage("Остановлен", "red", "");
	}
	$Content = "";
	$Content.= FormatAlert("iRobot Roomba 980", "info");
		
	$Content.= "
 		<div style=\"margin: 0 0 0 15px\">
			<label class=\"form-check-label\">$Info</label>
			<form method=\"POST\" style=\"margin: 0 0 0 0;\">
				<input name=\"command\" type=\"hidden\" value=\"iRobotSwitch\">
				<input name=\"Action\" type=\"hidden\" value=\"$Action\">
				<div class=\"form-group\">
					<input type=\"submit\" class=\"btn btn-primary\" disabled value=\"$Text\">\n
			  </div>
			</form>
		</div>
	";
	
	return $Content;
}


function GetIPCamEntranceContent() {
	$Content = "";
	$Content.= FormatAlert("IP камера подъезд лифт:", "info");
/*		
	$Content.= "
 		<div style=\"margin: 0 0 0 15px\">
		<image src=\"/api/apphome/image/ipcam002.PNG?nocache=12\" style=\"width: 100%\"/>
		</div>
	";
*/

	$Content.= "
 		<div style=\"margin: 0 0 0 0\">
			<image style=\"-webkit-user-select: none; width: 100% \" src=\"https://flintnet.ru:8109/video3.mjpg\"/>
		</div>
	";
/*
	$Content.= "
 		<div style=\"margin: 0 0 0 15px\">
			<image style=\"-webkit-user-select: none; margin: auto; width: 100% \" src=\"https://flintnet.ru:8109/image/jpeg.cgi\"/>
		</div>
	";
*/
	

	return $Content;
}

function GetIPCamStreetContent() {
	$Content = "";
	$Content.= FormatAlert("IP камера подъезд улица:", "info");
		
	$Content.= "
 		<div style=\"margin: 0 0 0 15px\">
		<img  src=\"/api/apphome/image/ipcam001.PNG?nocache=12\" style=\"width: 100%\">
		</div>
	";
	
	return $Content;
}

function GetServicesHTML($Token) {
	$Content = "";
	$Content.= "
		<!DOCTYPE html>
			<html lang=\"ru\">
		    <head>
					<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
					<meta charset=\"utf-8\" />
					<link href=\"/api/app/css/bootstrap.css\" rel=\"stylesheet\" type=\"text/css\" />
					<script src=\"/api/app/js/jquery-3.3.1.js\" type=\"text/javascript\"></script>
					<script src=\"/api/app/js/bootstrap.js\" type=\"text/javascript\"></script>
					<link href=\"/api/app/css/speedtest.css\" rel=\"stylesheet\" type=\"text/css\" />
					<script src=\"/api/app/js/speedtest.js?cache=10\"></script>
					<script type=\"text/javascript\">
						var conutnumber=0;						
						function showImage(){
							document.stillimage.src = \"http://10.0.0.109:80/cgi-bin/viewer/video.jpg?streamid=2&quality=5&date=\" + conutnumber;
							conutnumber++;
							setTimeout(\"showImage()\",1000);
						}

						var LightKitchenStatus = '';
	
						function httpGetAsync(theUrl, callback) {
				  	  var xmlHttp = new XMLHttpRequest();
							xmlHttp.responseType = \"json\";
			  	  	xmlHttp.onreadystatechange = function() { 
				  	 	  if (xmlHttp.readyState == 4 && xmlHttp.status == 200) 
									callback(xmlHttp.response);
					    }
			  		  xmlHttp.open(\"GET\", theUrl, true); // true for asynchronous 
			    		xmlHttp.setRequestHeader('Cache-Control', 'no-cache');
			    		xmlHttp.send(null);
						}

      			function FillButtonKitchenContent(Content) {
							if (LightKitchenStatus != Content.status) {
								idLightKitchenContent.innerHTML = Content.content;
								LightKitchenStatus = Content.status;
							}
						}

						var UrlButtonKitchen = '/api/apphome/?request={%22method%22:%22services%22,%22sub_method%22:%22button_kitchen%22,%22token%22:%22$Token%22}';
						var timerId = setInterval(
							function () {
								
								httpGetAsync(UrlButtonKitchen, FillButtonKitchenContent);
			        },
							1000
						);
						httpGetAsync(UrlButtonKitchen, FillButtonKitchenContent);
					</script>
				</head>
			<body>
	";

//	$Content.= GetLightKitchenContent();
	$Content.= "
		<div id=\"idLightKitchenContent\"></div>
	";
//	$Content.= GetiRobotContent();
	$Content.= GetIPCamEntranceContent();
//	$Content.= GetIPCamStreetContent();
	$Content.= "
			</body>
		</html>
	";
	return $Content;
}

