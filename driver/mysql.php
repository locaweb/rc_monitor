<?php

/**
Depends of the Roundcube Core
*/

class rc_monitor_driver_mysql extends rc_monitor_driver_base{

	function __construct($functional=""){
		parent::__construct();
		$this->name = "mysql";

		//are same for functional and sla tests
		$this->validate_connection();
	}

	function validate_connection(){

		if(!$this->dsn_list = $this->config['rc_monitor_mysql_dsn_list']){
			$this->dsn_list = array(
				'db_dsnw' 		,
				'db_dsnr' 		,
			);
		}

		$all_dsn = array();

		foreach($this->dsn_list as $dsn){
			$all_dsn[$dsn] = $this->config[$dsn];
		}

		foreach ($all_dsn as $key => $dsn) {
			if(empty($dsn))
				continue;

    	$par = explode('/', $dsn);
			$db = $this->get_db($dsn);

			$res = $db->query("SELECT 'ok' ");
	    $row = $db->fetch_array($res);
	    if($row[0]==ok){
	    	$this->set_status(ok);
	    	$this->add_functional("mysql $key - " . $par[3], ok);
	    }else{
	    	$this->set_status(error);
	    	$this->add_message("mysql $key - " . $par[3] . " -> " . $db->is_error());
	    	$this->add_functional("mysql $key - " . $par[3], error, "Error: " . $db->is_error());
	    }
		}
	}

  private function get_db($dsn)
  {
    return new rcube_mdb2($dsn);
  }
}