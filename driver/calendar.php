<?php

/**
Depends of the Calendar plugin

http://www.sitepoint.com/parsing-xml-with-simplexml/
http://code.google.com/p/sabredav/wiki/WebDAVClient#Doing_a_PROPFIND_request
http://www.ietf.org/rfc/rfc4791.txt
*/

require_once('../calendar/program/backend/caldav/caldav-client.php');

class rc_monitor_driver_calendar extends rc_monitor_driver_base{

	function __construct($functional=""){
		parent::__construct();
		$this->name = "calendar";

		if(!$this->calendar_users = $this->config['rc_monitor_calendar_users']){
			$this->calendar_users = array();
		}

		if(!$this->calendar_url = $this->config['rc_monitor_calendar_url']){
			$this->calendar_url = 'http://local-owncloud/apps/calendar/caldav.php/calendars/#USER#/default%20calendar';
		}

		if($functional){
			$this->validate_connection();
			$this->validate_operations();
		}else{
			$this->validate_connection();	
		}

	}

	function validate_connection(){

		foreach($this->calendar_users as $user){
			$this->caldav = new CalDAVClient(
				str_replace("#USER#", $user['user'],$this->calendar_url), 
				trim($user['user']), 
				trim($user['pass']), 
				'basic', 
				false); //for debug = true
			
			try {
			  $resp = $this->caldav->DoXMLRequest("OPTIONS");
			  if(!$resp){
			  	throw new Exception('Problem on connect to calendar of '. $user['user']);
			  }
				$this->set_status(ok);
				$this->add_functional("calendar connection to " . $user['user'], ok);
			} catch(Exception $x) {
				$this->set_status(error);
				$this->add_message( "calendar connection: ".  $x->getMessage() );
				$this->add_functional("calendar connection to " . $user['user'], error, $x->getMessage() );
			}
			
		}
	}

	function validate_operations(){

		foreach($this->calendar_users as $user){
			$url = str_replace("#USER#", $user['user'],$this->calendar_url);
			$this->caldav = new CalDAVClient(
				$url, 
				trim($user['user']), 
				trim($user['pass']), 
				'basic', 
				false); //for debug = true

$xmlC = <<<PROPP
<?xml version="1.0" encoding="utf-8" ?>
 <D:propfind xmlns:D="DAV:" xmlns:C="http://calendarserver.org/ns/">
     <D:prop>
             <D:displayname />
             <C:getctag />
             <D:resourcetype />
     </D:prop>
 </D:propfind>
PROPP;
			try {
			  $this->caldav->SetDepth(1);
			 
				$xml_response = $this->caldav->DoXMLRequest("PROPFIND", $xmlC);
			  $xml_response = explode("\r\n\r\n", $xml_response)[1];

			  $xml = new SimpleXMLElement($xml_response);
			  $ns = $xml->getNamespaces(true);
			  $responses = $xml->children($ns["d"]);
				$this->set_status(ok);
				$this->add_functional("calendar list events of " . $user['user'] . "(" . count($responses) . ")", ok);
			} catch(Exception $x) {
				$this->set_status(error);
				$this->add_message( $x->getMessage() );
				$fullerror =  "url:" . $url . " " . $x->getMessage();
				error_log($fullerror);
				$this->add_functional("calendar list events of " . $user['user'], error, "Can't list events from $url" );
			}

		}	 	
	}
}