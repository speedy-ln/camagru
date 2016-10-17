<?php
	class REST {

        const DEVELOPMENT = true;
        public $_allow = array();
		public $_content_type = "application/json";
		public $_request = array();

		private $_code = 200;

        public static $C100 = 'Continue';
        public static $C101 = 'Switching Protocols';
        public static $C200 = 'OK';
        public static $C201 = 'Created';
        public static $C202 = 'Accepted';
        public static $C203 = 'Non-Authoritative Information';
        public static $C204 = 'No Content';
        public static $C205 = 'Reset Content';
        public static $C206 = 'Partial Content';
        public static $C300 = 'Multiple Choices';
        public static $C301 = 'Moved Permanently';
        public static $C302 = 'Found';
        public static $C303 = 'See Other';
        public static $C304 = 'Not Modified';
        public static $C305 = 'Use Proxy';
        public static $C306 = '(Unused)';
        public static $C307 = 'Temporary Redirect';
        public static $C400 = 'Bad Request';
        public static $C401 = 'Unauthorized';
        public static $C402 = 'Payment Required';
        public static $C403 = 'Forbidden';
        public static $C404 = 'Not Found';
        public static $C405 = 'Method Not Allowed';
        public static $C406 = 'Not Acceptable';
        public static $C407 = 'Proxy Authentication Required';
        public static $C408 = 'Request Timeout';
        public static $C409 = 'Conflict';
        public static $C410 = 'Gone';
        public static $C411 = 'Length Required';
        public static $C412 = 'Precondition Failed';
        public static $C413 = 'Request Entity Too Large';
        public static $C414 = 'Request-URI Too Long';
        public static $C415 = 'Unsupported Media Type';
        public static $C416 = 'Requested Range Not Satisfiable';
        public static $C417 = 'Expectation Failed';
        public static $C500 = 'Internal Server Error';
        public static $C501 = 'Not Implemented';
        public static $C502 = 'Bad Gateway';
        public static $C503 = 'Service Unavailable';
        public static $C504 = 'Gateway Timeout';
        public static $C505 = 'HTTP Version Not Supported';

		public function __construct($strip = true){
			$this->inputs($strip);
		}

		public function get_referer(){
			return $_SERVER['HTTP_REFERER'];
		}

		public function response($data,$status){
			$this->_code = ($status)?$status:200;
			$this->set_headers();
			echo $data;
			exit;
		}

		private function get_status_message(){
			$status = array(
						100 => 'Continue',
						101 => 'Switching Protocols',
						200 => 'OK',
						201 => 'Created',
						202 => 'Accepted',
						203 => 'Non-Authoritative Information',
						204 => 'No Content',
						205 => 'Reset Content',
						206 => 'Partial Content',
						300 => 'Multiple Choices',
						301 => 'Moved Permanently',
						302 => 'Found',
						303 => 'See Other',
						304 => 'Not Modified',
						305 => 'Use Proxy',
						306 => '(Unused)',
						307 => 'Temporary Redirect',
						400 => 'Bad Request',
						401 => 'Unauthorized',
						402 => 'Payment Required',
						403 => 'Forbidden',
						404 => 'Not Found',
						405 => 'Method Not Allowed',
						406 => 'Not Acceptable',
						407 => 'Proxy Authentication Required',
						408 => 'Request Timeout',
						409 => 'Conflict',
						410 => 'Gone',
						411 => 'Length Required',
						412 => 'Precondition Failed',
						413 => 'Request Entity Too Large',
						414 => 'Request-URI Too Long',
						415 => 'Unsupported Media Type',
						416 => 'Requested Range Not Satisfiable',
						417 => 'Expectation Failed',
						422 => 'Unprocessable Entity',
						500 => 'Internal Server Error',
						501 => 'Not Implemented',
						502 => 'Bad Gateway',
						503 => 'Service Unavailable',
						504 => 'Gateway Timeout',
						505 => 'HTTP Version Not Supported');
			return ($status[$this->_code])?$status[$this->_code]:$status[500];
		}

		public function get_request_method(){
			return $_SERVER['REQUEST_METHOD'];
		}

		private function inputs($strip = true){
			switch($this->get_request_method()){
				case "POST":
					$this->_request = $this->cleanInputs($_POST, $strip);
					break;
				case "GET":
				default:
					$this->response('',406);
					break;
			}
		}

		private function cleanInputs($data, $strip = true){
			$clean_input = array();
			if(is_array($data)){
				foreach($data as $k => $v){
					$clean_input[$k] = $this->cleanInputs($v, $strip);
				}
			}else{
				if(get_magic_quotes_gpc()){
					$data = trim(stripslashes($data));
				}
				if($strip){
					$data = strip_tags($data);
				}
				$clean_input = trim($data);
			}
			return $clean_input;
		}

		private function set_headers(){
			header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
			header("Content-Type:".$this->_content_type);
		}
	}
