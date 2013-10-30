<?php

define("INSTALL_PATH" , "../../");
require_once '../../program/include/iniset.php';
header('Content-Type: text/json; charset=' .RCMAIL_CHARSET);

require_once('./driver/base.php');

class rc_monitor{

	private $drivers;

	function __construct($functional){
		$this->config = rc_monitor_driver_base::get_config();
		if(!$this->drivers = $this->config['rc_monitor_drivers_list']){
			$this->drivers 	= array("memcache","mysql","imap","sieve","calendar");
		}
		$this->functional = $functional;
	}

	function monitor(){
		$result = array();
		foreach ($this->drivers as $driver) {
			$_driver = 'rc_monitor_driver_' . $driver;
			require_once("./driver/$driver.php");

			$obj = new $_driver($this->functional);
			if($this->functional){
				$tests = $obj->functional_monitor();
				if(count($tests)){
					$result = array_merge($result,$tests);
				}
			}else{
				$result[] = $obj->monitor();
			}
		}
		return $result;
	}
}

$functional = @$_GET['functional'];
$monitor = new rc_monitor($functional);
print json_encode($monitor->monitor());

?>