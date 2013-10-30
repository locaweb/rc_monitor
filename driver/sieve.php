<?php

/**
Depends of the Managesieve plugin
*/

require_once('../managesieve/lib/rcube_sieve.php');
//require_once('../managesieve/lib/rcube_sieve_script.php');
require_once('../managesieve/lib/Net/Sieve.php');

class rc_monitor_driver_sieve extends rc_monitor_driver_base{

	public $list_errors = array(
		1 => 'SIEVE_ERROR_CONNECTION',
		2 => 'SIEVE_ERROR_LOGIN',
		3 => 'SIEVE_ERROR_NOT_EXISTS',    // script not exists
		4 => 'SIEVE_ERROR_INSTALL',       // script installation
		5 => 'SIEVE_ERROR_ACTIVATE',      // script activation
		6 => 'SIEVE_ERROR_DELETE',        // script deletion
		7 => 'SIEVE_ERROR_INTERNAL',      // internal error
		8 => 'SIEVE_ERROR_DEACTIVATE',    // script activation
		255 => 'SIEVE_ERROR_OTHER',       // other/unknown error
	);

	function __construct($functional=""){
		parent::__construct();
		$this->name = "sieve";

		if(!$this->sieve_users = $this->config['rc_monitor_sieve_users']){
			$this->sieve_users = array(
				array(
						'user'=> 'user@domain.com', 
						'pass'=>'pass123', 
						'host'=>'imap.domain.com', // or host to connect sieve server
						'port' => 4190, //default
						'auth_type' => 'LOGIN',
					),
			);
		}

		if($functional){
			$this->validate_connection();
			$this->validate_operations();
		}else{
			$this->validate_connection();	
		}

	}

	function validate_connection(){
		foreach($this->sieve_users as $user){

     $this->sieve = new rcube_sieve(
            $user['user'],
            $user['pass'],
            ($user['host']) 			? $user['host'] 		: $this->config['managesieve_host'],
            ($user['port']) 		 	? $user['port'] 		: $this->config['managesieve_port'],
            ($user['auth_type']) 	? $user['auth_type']: $this->config['managesieve_auth_type'],
            ($user['usetls']) 		? $user['usetls'] 	: $this->config['managesieve_usetls'],
            ($user['disabled']) 	? $user['disabled'] : $this->config['managesieve_disabled'],
            ($user['debug']) 			? $user['debug'] 		: $this->config['managesieve_debug'],
            ($user['auth_cid']) 	? $user['auth_cid'] : $this->config['managesieve_auth_cid'],
            ($user['auth_pw']) 		? $user['auth_pw'] 	: $this->config['managesieve_auth_pw']
        );


			if($this->sieve->error()){
				$this->set_status(error);
				$this->add_message( $this->list_errors[$this->sieve->error()] . " with " . $user['user']);
				$this->add_functional("sieve connection to " . $user['user'], error, $this->list_errors[$this->sieve->error()] );
			}else{
				$this->set_status(ok);
				$this->add_functional("sieve connection to " . $user['user'], ok);
			}
		}  	
	}

	function validate_operations(){

		foreach($this->sieve_users as $user){

     $this->sieve = new rcube_sieve(
            $user['user'],
            $user['pass'],
            ($user['host']) 			? $user['host'] 		: $this->config['managesieve_host'],
            ($user['port']) 		 	? $user['port'] 		: $this->config['managesieve_port'],
            ($user['auth_type']) 	? $user['auth_type']: $this->config['managesieve_auth_type'],
            ($user['usetls']) 		? $user['usetls'] 	: $this->config['managesieve_usetls'],
            ($user['disabled']) 	? $user['disabled'] : $this->config['managesieve_disabled'],
            ($user['debug']) 			? $user['debug'] 		: $this->config['managesieve_debug'],
            ($user['auth_cid']) 	? $user['auth_cid'] : $this->config['managesieve_auth_cid'],
            ($user['auth_pw']) 		? $user['auth_pw'] 	: $this->config['managesieve_auth_pw']
        );

     	$scripts = $this->sieve->get_scripts();

			if( count($scripts) ){
				$this->add_functional("sieve listboxes of " . $user['user'] . "(". count($scripts) . ")", ok);
			}else{
				$this->add_functional(
					"sieve connection to " . $user['user'], 
					error, 
					"Problem to connect: " . $user['user'] . " - " . $this->list_errors[$this->sieve->error()] );
			}
		}  	 	
	}
}