<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

function GetAccountAllDataArray() {
	$ResultArray["Кухня"] = array(
		array(
			"Title" => "Газ",
			"Text" => "Выкл.",
		),
		array(
			"Title" => "Газоанализатор",
			"Text" => "Норма",
		),
		array(
			"Title" => "Вода",
			"Text" => "Выкл.",
		),
		array(
			"Title" => "Свет",
			"Text" => "Вкл.",
		),

		array(
			"Title" => "Окно",
			"Text" => "Закрыто",
		),

	);

	$ResultArray["Ванная комната"] = array(
		array(
			"Title" => "Вода",
			"Text" => "Выкл.",
		),
		array(
			"Title" => "Протечка воды",
			"Text" => "Нет",
		),
	);

	$ResultArray["Туалетная комната"] = array(
		array(
			"Title" => "Протечка воды",
			"Text" => "Нет",
		),
	);



	return $ResultArray;
}


function GetProviderDataArray() {
	$ResultArray["Позвонить семье"] = array(
		array(
			"Title" => "Евгений",
			"Text" => "<a href=\"tel:+79042734555\">+7 (904) 555-55-55</a>",
		),
		array(
			"Title" => "Лена",
			"Text" => "<a href=\"tel:+79042734555\">+7 (904) 555-55-56</a>",
		),
		array(
			"Title" => "Настюша",
			"Text" => "<a href=\"tel:+79042734555\">+7 (904) 555-55-57</a>",
		),
		array(
			"Title" => "Тёма",
			"Text" => "у Тёмы пока еще нет телефона",
		),
	);

	$ResultArray["Позвонить в службы"] = array(
		array(
			"Title" => "Полиция",
			"Text" => "<a href=\"tel:020\">020</a>",
		),
		array(
			"Title" => "Газовая служба",
			"Text" => "<a href=\"tel:+78216734859\">+7 (821) 673-48-59</a>",
		),
		array(
			"Title" => "Водоканал Аварийная",
			"Text" => "<a href=\"tel:+78216734859\">+7 (821) 676-32-70</a>",
		),
	);

	return $ResultArray;
}


function GetAllDataArray() {
	$AccountAllDataArray = GetAccountAllDataArray();
	$ProviderDataArray = GetProviderDataArray();
	$AllDataArray = array_merge($AccountAllDataArray, $ProviderDataArray);
	return $AllDataArray;
}

function GetAllInfoHTML() {
	$Content = "";
	$Content.= "
		<!DOCTYPE html>
			<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"ru-ru\" lang=\"ru-ru\" dir=\"ltr\">
		    <head>
					<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
					<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
					<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
					<link href=\"/api/app/css/bootstrap.css\" rel=\"stylesheet\" type=\"text/css\" />
					<script src=\"/api/app/js/jquery-3.3.1.js\" type=\"text/javascript\"></script>
					<script src=\"/api/app/js/bootstrap.js\" type=\"text/javascript\"></script>
				</head>
			<body>
	";
	$AllDataArray = GetAllDataArray();
	$Content.= ArrayToHTML($AllDataArray);
	$Content.= "
			</body>
		</html>
	";

	return trim($Content);
}









