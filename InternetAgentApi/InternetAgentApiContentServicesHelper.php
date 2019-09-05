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
			if ($_REQUEST["Action"] == "on")
				$LightKitchenStatus = true;
			else
				$LightKitchenStatus = false;
			$Result = true;
			break;		
	}

	if ($Result) 
		header("Location: ".$_SERVER["REQUEST_URI"]);
	return $Result;
}

function GetLightKitchenContent() {
	$LightKitchenStatus = true;
	if ($LightKitchenStatus) {
		$Action = "off";
		$Text = "Выключить";
		$Info = "Свет " . FormatMessage("Включен", "green", "");
	} else {
		$Action = "on";
		$Text = "Включить";
		$Info = "Свет " . FormatMessage("Выключен", "red", "");
	}
	$Content = "";
	$Content.= FormatAlert("Кухня:", "info");
		
	$Content.= "
 		<div style=\"margin: 0 0 0 15px\">
			<label class=\"form-check-label\">$Info</label>
			<form method=\"POST\" style=\"margin: 0 0 0 0;\">
				<input name=\"command\" type=\"hidden\" value=\"LightKitchenSwitch\">
				<input name=\"Action\" type=\"hidden\" value=\"$Action\">
				<div class=\"form-group\">
					<input type=\"submit\" class=\"btn btn-primary\" value=\"$Text\">\n
			  </div>
			</form>
		</div>
	";
	
	return $Content;
}

function GetIPCamEntranceContent() {
	$Content = "";
	$Content.= FormatAlert("IP камера подъезд лифт:", "info");
		
	$Content.= "
 		<div style=\"margin: 0 0 0 15px\">
		<image src=\"/api/apphome/image/ipcam002.PNG?nocache=12\" style=\"width: 100%\"/>
		</div>
	";
	
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

function GetServicesHTML() {
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
				</script>
				</head>
			<body>
	";

	$Content.= GetLightKitchenContent();
	$Content.= GetIPCamEntranceContent();
	$Content.= GetIPCamStreetContent();
	$Content.= "
			</body>
		</html>
	";
	return $Content;
}

