<?php

$ClientConfig = array(
	"interface" => array(
		"app_title" => "Мой дом",
		"toolbar_title" => "Мой дом",
		"image_logo" => "https://flintnet.ru/api/apphome/image/house.jpg",
		"sections" => array(			
			"news" => array(
				"enable" => false,
				"name" => "Новости",
			),
			"info" => array(
				"enable" => true,
				"name" => "Информация",
			),
			"services" => array(
				"enable" => true,
				"name" => "Сервисы",
			),
			"support" => array(
				"enable" => false,
				"name" => "Чат",
				"read_only" => false,
			),
		),
	),
	"application" => array(
		"api_url" => "https://flintnet.ru/api/apphome/",
		"debug" => false,
		"versions" => array(
			"Windows" => array(
				"build" => 68,
				"update_url" => "https://flintnet.ru/soft/InternetAgentSetup.exe",
			),
			"Android" => array(
				"build" => 64,
				"update_url" => "https://play.google.com/store/apps/details?id=ru.flintnet.InternetAgent",
			),
			"iOS" => array(
				"build" => 0,
				"update_url" => "",
			),
			"macOS" => array(
				"build" => 0,
				"update_url" => "",
			),
		),
	),
	"timers" => array(
		"message" => 60,
		"message_dialog" => 5,
	),
	"internet_provider_token" => "03e3267fa9cc********************",
);

