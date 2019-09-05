<?php                           

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

function GetArrayNews() {
	$ResultArray = array(
		array(
			"title" => "Оплата счетов ЖКХ",
			"text" => "Оплатить счета ЖКХ - электроэнергия, отопление, квартплата, домофон, вывоз мусора, вододоснабжение",
			"images" => "",
			"date" => "25.09.2019",
		),
		array(
			"title" => "Оплата Интернет",
			"text" => "Заплатить за интернет 499р.",
			"images" => "",
			"date" => "30.09.2019",
		),

	);
	return $ResultArray; 
}

function GetContentNews() {
	$ResultArray = GetArrayNews();
	$Index = 0;
	$Content = "
		<div class=\"blog\" itemscope=\"\" itemtype=\"https://schema.org/Blog\" style=\"margin: 10px 10px 10px 10px; max-width: 100%;\">
	";

	$Content.= "
	";
	foreach ($ResultArray as $Value) {
		$Title = $Value["title"];
		$Text = $Value["text"];
		$Date = $Value["date"];
		$Images = json_decode($Value["images"], true);
		if (isset($Images["image_intro"])) {
			$ImageURL = $Images["image_intro"];
			$Image = "
				<div style=\"float: left; max-width: 40%; margin : 0 1em 1vh 0\">
					<img src=\"$ImageURL\" alt=\"\">					
				</div>
			";			
		} else 
			$Image = "";

		$Content.= "
			<div class=\"items-row cols-1 row-$Index row-fluid clearfix\">
				<div class=\"span12\">
					<div class=\"item column-1\" itemprop=\"blogPost\" itemscope=\"\" itemtype=\"https://schema.org/BlogPosting\">

						<div class=\"alert alert-primary\" role=\"alert\">
							<h3 itemprop=\"name\">$Title</h3>
						</div>
							$Image
  						$Text
						<dl class=\"article-info muted\">
							<dt class=\"article-info-term\">
							</dt>
							<dd class=\"published\">
								<time itemprop=\"datePublished\">Дата: $Date</time>
							</dd>			
						</dl>
					</div>

				</div>
			</div>\n
		";
	  $Index++;
	}
	$Content.= "
		</div>
	";
	return $Content; 
}


function GetPageNews($Request) {
	$Content = "";
	$Content.= "
		<!DOCTYPE html>
			<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"ru-ru\" lang=\"ru-ru\" dir=\"ltr\">
		    <head>
    	    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
          <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
          <base href=\"https://flintnet.ru/\" />
					<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
					<style type=\"text/css\">
						body{font-family:Open Sans, sans-serif; font-size:16px; font-weight:normal; }
						h1{font-family:Open Sans, sans-serif; font-weight:normal; }
						h2{font-family:Open Sans, sans-serif; font-weight:600; }
						h3{font-family:Open Sans, sans-serif; font-weight:normal; }
						h4{font-family:Open Sans, sans-serif; font-weight:normal; }
						h5{font-family:Open Sans, sans-serif; font-weight:600; }
						h6{font-family:Open Sans, sans-serif; font-weight:600; }
						#sp-top-bar{ color:#999999; }
						#sp-bottom{ margin:10vh 0 0 0; }
						img {
					    max-width: 100%;
					    width: auto;
					    height: auto;
					    vertical-align: middle;
					    border: 0;
					    -ms-interpolation-mode: bicubic;
						}
					</style>
					<link href=\"/api/app/css/bootstrap.css\" rel=\"stylesheet\" type=\"text/css\" />
				</head>
			<body>
	";
	$Content.= GetContentNews();
	$Content.= "
			</body>
		</html>
	";
	return $Content;
}

