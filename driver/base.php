<?php

class rc_monitor_driver_base {

	const ok = 'ok';
	const error = 'error';

	protected $status;
	protected $name;
	protected $message;

	protected $functionals = [];

	static $config;

	function __construct(){
		self::get_config();
		$this->config = &self::get_config();
	}

	# Singleton for get config of rc
	static function get_config(){
		if(!self::$config){
			// load main config file
	    require_once(RCMAIL_CONFIG_DIR . '/main.inc.php');
			self::$config = $rcmail_config;

	    // load database config
	    require_once(RCMAIL_CONFIG_DIR . '/db.inc.php');
	    include_once('config.inc.php');

		  self::$config = array_merge(self::$config, $rcmail_config);
		  ksort(self::$config);
		  return self::$config;
		}else{
			return self::$config;
		}
	}

	function monitor(){
		$resp = array($this->name => array("status" => $this->status));
		if($this->message){
			$resp[$this->name]["message"] = $this->message;
		}
		return $resp;
	}

	function functional_monitor(){
		return $this->functionals;
	}

	function add_functional($name, $status="ok", $msg=""){
		$item = array("status" => $status);
		if($msg){
			$item["message"] = array($msg);
		}
		$this->functionals[][$name] = $item;
	}

	# Function for set status or return driver
	# @param [String] status -> ok or error
	function set_status($status){
		if(empty($this->status) && $status == "ok"){
			$this->status = "ok";
		}
		if($status == "error"){
			$this->status = "error";
		}
	}

	# Function for add message errors
	# @param [String] msg -> message of error
	function add_message($msg){
		$this->message[] = $msg;
	}

}