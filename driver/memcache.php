<?php

/**   
Roundcube use a Memcache class and persistent connection
Depends of the Roundcube Core and Memcache class
*/
class rc_monitor_driver_memcache extends rc_monitor_driver_base{

	function __construct($functional=""){
		parent::__construct();
		$this->name = "memcache";

		$this->servers = implode(",", $this->config['memcache_hosts']);

		if($functional){
			$this->validate_connection();
			$this->validate_operations();
		}else{
			$this->validate_connection();	
		}
	}

	function validate_connection(){
		$this->memcache = $this->get_memcache();
		if($this->memcache->getVersion()){
			$this->set_status(ok);
		}else{
			$this->set_status(error);
	  	$this->add_message("Can't connect to memcache server: " . $this->servers);	
		}
	}

	function validate_operations(){
		if($this->memcache->set('test',ok)){
			if($this->memcache->get('test')==ok){
				$this->add_functional("memcache r/w", ok);
			}else{
				$this->add_functional("memcache r/w", error, "Can't get data from memcache: " . $this->servers);
			}
		}else{
			$this->add_functional("memcache r/w", error, "Can't set data on memcache: " . $this->servers);
		}
	}

  public function get_memcache()
  {
    if (!isset($this->memcache)) {
      // no memcache support in PHP
      if (!class_exists('Memcache')) {
        $this->memcache = false;
        return false;
      }

      $this->memcache = new Memcache;
      $this->mc_available = 0;

      // add all configured hosts to pool
      $pconnect = $this->config['memcache_pconnect'];
      foreach($this->config['memcache_hosts'] as $host) {
        if (substr($host, 0, 7) != 'unix://') {
          list($host, $port) = explode(':', $host);
          if (!$port) $port = 11211;
        }
        else {
          $port = 0;
        }
        $this->mc_available += intval($this->memcache->addServer($host, $port, $pconnect, 1, 1, 15, false, array($this, 'memcache_failure')));
      }

      // test connection and failover (will result in $this->mc_available == 0 on complete failure)
      $this->memcache->increment('__CONNECTIONTEST__', 1);  // NOP if key doesn't exist

      if (!$this->mc_available)
        $this->memcache = false;
    }

    return $this->memcache;
  }

  /**
   * Callback for memcache failure
   */
  public function memcache_failure($host, $port)
  {
		$this->add_functional("memcache connection", error, "Can't connect to memcache $host:$port ");
  }

}