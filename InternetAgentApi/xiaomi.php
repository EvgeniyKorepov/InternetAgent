<?php

@ini_set("display_errors", "1"); error_reporting(E_ALL);
//@ini_set("display_errors", "0"); error_reporting(0);

const 	MIIO_PORT = '9898';


class miIO {
	
	public 	$ip = '';
	public 	$debug = '';
	public 	$send_timeout = 5;
	public 	$disc_timeout = 15;
  private $curl;

  public 	$msg_id = '1';
	public	$useAutoMsgID = false;

	public 	$data = '';
	public 	$sock = NULL;
	
	private $miPacket = NULL;
	
	private $AppKey = "zf**************atp";
	private $AES_KEY_IV_HEX = "17996d093d28ddb3ba695a2e6f58562e";

	
	public function __construct($ip = NULL, $bind_ip = NULL, $debug = false) {
		
		$this->debug = $debug;

		$this->InitCurl();
		
		if ($ip != NULL) $this->ip = $ip;
		
		if ($bind_ip != NULL) $this->bind_ip = $bind_ip;
		 else $this->bind_ip = '0.0.0.0';
		
		
		$this->sockCreate();
		
	}
	
	public function __destruct() {
		
		@socket_shutdown($this->sock, 2);
		@socket_close($this->sock);
		
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	private function Log($Title, $Value = "") {	
		if (is_array($Title))
			$Title = print_r($Title, true) . "\n";

		if ($Value <> "") {
			if (is_array($Value)) 
				$Value = "\n" . print_r($Value, true);
		}
		$Message = date("Y.m.d H:i:s ") . $Title . $Value . "\n";
		if ($this->debug)
			echo $Message;
//		file_put_contents($this->log_file, $Message, FILE_APPEND);	
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	private function InitCurl() {
	  $this->curl = curl_init();

	  curl_setopt($this->curl, CURLOPT_HEADER, true);
	  curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);   // Количество секунд ожидания при попытке соединения
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 10);   // Максимально позволенное количество секунд для выполнения cURL-функций.
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_BINARYTRANSFER, true);
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	private function RawHeadersParser($rawHeaders) {
		$headers = array();
		$key = '';

		foreach (explode("\n", $rawHeaders) as $headerRow) {
			if (trim($headerRow) === '') 
				break;
			$headerArray = explode(':', $headerRow, 2);

			if (isset($headerArray[1])) {
				if (!isset($headers[$headerArray[0]])) {
					$headers[trim($headerArray[0])] = trim($headerArray[1]);
				} elseif (is_array($headers[$headerArray[0]])) {
					$headers[trim($headerArray[0])] = array_merge($headers[trim($headerArray[0])], array(trim($headerArray[1])));
				} else 
					$headers[trim($headerArray[0])] = array_merge(array($headers[trim($headerArray[0])]), array(trim($headerArray[1])));
				$key = $headerArray[0];
			} else {
					if (substr($headerArray[0], 0, 1) === "\t") {
						$headers[$key] .= "\r\n\t" . trim($headerArray[0]);
					} elseif (!$key) {
						$headers[0] = trim($headerArray[0]);
					}
			}
		}

		return $headers;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	private function Post($URL, $HeadersArray = null, $DataJSON = "") {
		// $this->Log("Post JSON:", $DataJSON);

		if (isset($HeadersArray))
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $HeadersArray);

    curl_setopt($this->curl, CURLOPT_URL, $URL);
		curl_setopt($this->curl, CURLOPT_POST, true);
		if ($DataJSON != "")
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $DataJSON);

		// $this->Log("curl_exec: ", $URL);
		$response = curl_exec($this->curl);
		// $this->Log("Raw responce: ", $response);
		$curlErrno = curl_errno($this->curl);
		if ($curlErrno) {
			$curlError = curl_error($this->curl);
			$this->Message = "Error Get : curlErrno=$curlErrno, curlError=$curlError";
			$this->Log($this->Message);
			$this->Error = true;
	    return false;
		}
		$httpHeaderSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
		$httpHeaders    = $this->RawHeadersParser(substr($response, 0, $httpHeaderSize));
		$httpBody       = substr($response, $httpHeaderSize);
		$responseInfo   = curl_getinfo($this->curl);

		 $this->Log("responseInfo", $responseInfo);
		 $this->Log("httpHeaders", $httpHeaders);
		 $this->Log("httpBody", $httpBody);

		$ResponseJSON = $httpBody;
		return $ResponseJSON;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	private function Get($URL, $HeadersArray = null, $DataJSON = "") {
		// $this->Log("Post JSON:", $DataJSON);

		if (isset($HeadersArray))
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $HeadersArray);

    curl_setopt($this->curl, CURLOPT_URL, $URL);
		curl_setopt($this->curl, CURLOPT_POST, false);
		if ($DataJSON != "")
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $DataJSON);

		// $this->Log("curl_exec: ", $URL);
		$response = curl_exec($this->curl);
		// $this->Log("Raw responce: ", $response);
		$curlErrno = curl_errno($this->curl);
		if ($curlErrno) {
			$curlError = curl_error($this->curl);
			$this->Message = "Error Get : curlErrno=$curlErrno, curlError=$curlError";
			$this->Log($this->Message);
			$this->Error = true;
	    return false;
		}
		$httpHeaderSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
		$httpHeaders    = $this->RawHeadersParser(substr($response, 0, $httpHeaderSize));
		$httpBody       = substr($response, $httpHeaderSize);
		$responseInfo   = curl_getinfo($this->curl);

		 $this->Log("responseInfo", $responseInfo);
		 $this->Log("httpHeaders", $httpHeaders);
		 $this->Log("httpBody", $httpBody);

		$ResponseJSON = $httpBody;
		return $ResponseJSON;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	public function Authorize() {

		$URL = "https://mc.yandex.ru/watch/49488847?wmode=7&page-url=https%3A%2F%2Fquasar.yandex.ru%2Fskills%2Fiot%2Fdevice%2F68821a9e-c2dd-4ea8-83f5-1c1d07535410%3Fapp_id%3Dru.yandex.searchplugin%26dp%3D3.0%26lang%3Dru-RU%26app_platform%3Dandroid%26os_version%3D9%26app_version_name%3D9.00%26model%3DMi%2520A2%26size%3D1080%252C2000&charset=utf-8&ut=noindex&browser-info=ti%3A10%3Ans%3A1568752227919%3As%3A2560x1440x24%3Ask%3A1.5%3Afpr%3A67501995301%3Acn%3A1%3Aw%3A1496x1298%3Az%3A180%3Ai%3A20190917233029%3Aet%3A1568752230%3Aen%3Autf-8%3Ac%3A1%3Ala%3Aru%3Acpf%3A1%3Apv%3A1%3Als%3A1319972808377%3Arqn%3A14%3Arn%3A637423939%3Ahid%3A753704080%3Ads%3A0%2C0%2C130%2C110%2C1087%2C0%2C0%2C%2C%2C%2C%2C%2C%3Afp%3A1551%3Agdpr%3A13%3Av%3A1708%3Awv%3A2%3Arqnl%3A1%3Ast%3A1568752230%3Au%3A14799885511016242287%3At%3A%D0%A3%D1%81%D1%82%D1%80%D0%BE%D0%B9%D1%81%D1%82%D0%B2%D0%B0";

		$HeadersArray = array(
			'Content-Type: application/x-www-form-urlencoded',
			'Origin: https://quasar.yandex.ru',
			'Referer: https://quasar.yandex.ru/skills/iot/device/68821a9e-c2dd-4ea8-83f5-1c1d07535410?app_id=ru.yandex.searchplugin&dp=3.0&lang=ru-RU&app_platform=android&os_version=9&app_version_name=9.00&model=Mi%20A2&size=1080%2C2000',
			'Sec-Fetch-Mode: cors',
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
		);


		$ResponseJSON = $this->Post($URL, $HeadersArray);		
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	public function Authorize2() {

		$URL = "https://iot.quasar.yandex.ru/m/user/devices/68821a9e-c2dd-4ea8-83f5-1c1d07535410";

		$HeadersArray = array(
			'Origin: https://quasar.yandex.ru',
			'Referer: https://quasar.yandex.ru/skills/iot/device/68821a9e-c2dd-4ea8-83f5-1c1d07535410?app_id=ru.yandex.searchplugin&dp=3.0&lang=ru-RU&app_platform=android&os_version=9&app_version_name=9.00&model=Mi%20A2&size=1080%2C2000',
			'Sec-Fetch-Mode: cors',
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
		);


		$ResponseJSON = $this->Get($URL, $HeadersArray);		
	}


	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	public function SwitchPlug() {

//curl 'https://iot.quasar.yandex.ru/m/user/devices/68821a9e-c2dd-4ea8-83f5-1c1d07535410/actions' 
		$DataJSON = '{"actions":[{"type":"devices.capabilities.on_off","state":{"instance":"on","value":true}}]}';

		$URL = "https://iot.quasar.yandex.ru/m/user/devices/68821a9e-c2dd-4ea8-83f5-1c1d07535410/actions";

		$HeadersArray = array(
			'Sec-Fetch-Mode: cors',
			'Referer: https://quasar.yandex.ru/skills/iot/device/68821a9e-c2dd-4ea8-83f5-1c1d07535410?app_id=ru.yandex.searchplugin&dp=3.0&lang=ru-RU&app_platform=android&os_version=9&app_version_name=9.00&model=Mi%20A2&size=1080%2C2000' ,
			'Origin: https://quasar.yandex.ru' ,
			'x-csrf-token: 4c330fd65bedd56b93d05a793ca5ca617f9ffab9:1568625316' ,
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36' ,
			'Content-Type: text/plain;charset=UTF-8' ,
		);
		$ResponseJSON = $this->Post($URL, $HeadersArray, $DataJSON);		
	}

	/*
		Создание udp4 сокета.
	*/
	
	public function sockCreate() {
	
		if (!($this->sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP))) {
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
			if ($this->debug) echo "Ошибка создания сокета - [socket_create()] [$errorcode] $errormsg" . PHP_EOL;
			die("Ошибка создания сокета - [socket_create()] [$errorcode] $errormsg \n");
		} else { if ($this->debug) echo 'Сокет успешно создан' . PHP_EOL; }
	
	}
	
	/*
		Установка параметров сокета - таймаут.
	*/
	
	public function sockSetTimeout($timeout = 2) {
	
		if (!socket_set_option($this->sock, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $timeout, "usec" => 0))) {
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
			if ($this->debug) echo "Ошибка установки параметра SO_RCVTIMEO сокета - [socket_create()] [$errorcode] $errormsg" . PHP_EOL;
		} else { if ($this->debug) echo 'Параметр SO_RCVTIMEO сокета успешно задан' . PHP_EOL; }
	
	}
	
	/*
		Установка параметров сокета - броадкаст.
	*/
	
	public function sockSetBroadcast() {
	
		if (!socket_set_option($this->sock, SOL_SOCKET, SO_BROADCAST, 1)) {
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
			if ($this->debug) echo "Ошибка установки параметра SO_BROADCAST сокета - [socket_create()] [$errorcode] $errormsg" . PHP_EOL;
		} else { if ($this->debug) echo 'Параметр SO_BROADCAST сокета успешно задан' . PHP_EOL; }
	
	}
	

	public function socketWriteRead($msg) {
		
		$this->sockSetTimeout($this->send_timeout);
			
		if ($this->debug) echo " >>>>> Отправляем пакет на $this->ip с таймаутом $this->send_timeout" . PHP_EOL;

		$packet = $msg;
		
		if(!($bytes = socket_sendto($this->sock, $packet, strlen($packet), 0, $this->ip, MIIO_PORT))) {
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
			if ($this->debug) echo "Не удалось отправить данные в сокет [$errorcode] $errormsg" . PHP_EOL;
		} else { 
			if ($this->debug) echo " >>>>> Отправлено в сокет $bytes байт" . PHP_EOL; 
		}
			
    $buf = '';
		if (($bytes = @socket_recvfrom($this->sock, $buf, 4096, 0, $remote_ip, $remote_port)) !== false) {
			if ($buf != '') {
				if ($this->debug) {
					echo " <<<<< Получен ответ от IP $remote_ip с порта $remote_port \n $buf" . PHP_EOL;
					echo "Прочитано $bytes байта из сокета" . PHP_EOL;
				}	
				$ResultArray = json_decode($buf, true);

				if (isset($ResultArray)) {
					if (isset($ResultArray["data"])) 
						$ResultArray["data"] = json_decode($ResultArray["data"], true);
					return $ResultArray;
				}				
			}
		}
		return false;
	}
	
	public function GetIDList() {	
		$Message = "{\"cmd\" : \"get_id_list\"}";
		$ResultArray = $this->socketWriteRead($Message);
		return $ResultArray;
	}


	public function ReadSID($SID) {	
//		$Message = "{\"cmd\":\"read\",\"sid\":\"$SID\"}";
		$MessageArray = array(
			"cmd" => "read",
			"sid" => $SID,
		);
		$Message = json_encode($MessageArray);
		$ResultArray = $this->socketWriteRead($Message);
		return $ResultArray;
	}


	public function WriteSID($SID, $channel_0) {	
		$Message = '{"cmd":"write","model":"ctrl_neutral1","sid":"' . $SID . '","short_id":4343,"data":"{\"channel_0\":\"on\",\"key\":\"3EB43E37C20AFF4C5872CC0D04D81314\"}" }';
		$Message = '{"cmd":"write","model":"ctrl_neutral1","sid":"' . $SID . '","short_id":4343,"data":"{\"channel_0\":\"on\",\"key\":\"3EB43E37C20AFF4C5872CC0D04D81314\"}" }';
		$DataArray = array(
			"channel_0" => $channel_0,
			"key" => $this->GetKey(),
		);
		$MessageArray = array(
			"cmd" => "write",
			"sid" => $SID,
			"data" => json_encode($DataArray),
		);
		$Message = json_encode($MessageArray);
//echo "$Message\n";
		$ResultArray = $this->socketWriteRead($Message);
		return $ResultArray;
	}

	public function GetKey() {
		$SID_HUB = "4cf8c86c143";
		$HUBData = $this->GetIDList();
		$Token = $HUBData["token"];
		$AES_KEY_IV = hex2bin($this->AES_KEY_IV_HEX);

		$bin_data = base64_decode(openssl_encrypt($Token, 'AES-128-CBC', $this->AppKey, OPENSSL_ZERO_PADDING, $AES_KEY_IV));
		$key = bin2hex($bin_data);
		$key = strtoupper($key);
		return $key;
	}

	public function Decrypt($data) {
		$SID_HUB = "4cf8c86c143";
		$HUBData = $this->GetIDList();
		$Token = $HUBData["token"];
		$AES_KEY_IV = hex2bin($this->AES_KEY_IV_HEX);

		$data_decrypted = openssl_decrypt ($data , 'AES-128-CBC', $this->AppKey, OPENSSL_ZERO_PADDING, $AES_KEY_IV);
		return $data_decrypted;
	}

	public function PlugSwitch() {		
		$SID = "158d0002d4187c";

		$ResultArray = $this->ReadSID($SID);

		if ($ResultArray["data"]["status"] == "on")
			$channel_0 = "off";
		else
			$channel_0 = "on";

		$SID = "158d0002d4187c";

		$ResultArray = $this->WriteSID($SID, $channel_0);
	}		

	public function PlugStatus() {		
		$SID = "158d0002d4187c";

		$ResultArray = $this->ReadSID($SID);

		return $ResultArray["data"]["status"];
//		return $ResultArray["data"];
	}		



}









