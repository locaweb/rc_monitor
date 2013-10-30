<?php


/**

 Class for verify imap connection using rcube_imap_generic

*/
class rc_monitor_driver_imap extends rc_monitor_driver_base{

	function __construct($functional=""){
		parent::__construct();
		$this->name = "imap";
		$this->conn = new rcube_imap_generic();
		# to enable imap debug
		#$this->conn->setDebug(true);

		$this->imap_users = $this->config['rc_monitor_imap_users'];

		if($functional){
			$this->validate_connection();
			$this->validate_operations();
		}else{
			$this->validate_connection();	
		}

	}

	function validate_connection(){
		foreach($this->imap_users as $user){
			$this->conn->connect($user['host'],$user['user'], $user['pass']);
			if(!$this->conn->connected()){
				$this->set_status(error);
				$this->add_message("Problem on connect to imap with " . $user['user']);
				$this->add_functional("imap connection with " . $user['user'], error, "Problem to connect: " . $user['user']);
			}else{
				$this->set_status(ok);
				$this->add_functional("imap connection with " . $user['user'], ok);
			}
		}  	
	}

	function validate_operations(){
		foreach($this->imap_users as $user){
			$this->conn->connect($user['host'],$user['user'], $user['pass']);
			$boxes = $this->conn->listMailboxes('','*');
			if( count($boxes) ){
				$this->add_functional("imap listboxes of " . $user['user'] . "(". count($boxes) . ")", ok);
			}else{
				$this->add_functional("imap connection with " . $user['user'], error, "Problem on connect to imap with: " . $user['user']);
			}
		}  	
	}
}