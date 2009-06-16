<?php
class Semtech_Service extends Zend_Json_Server
{
	
	private $_apiVersion = "0.1";
	
	public function __construct($class, $method, $args = null)
	{
		$class = (string) $class;
		$method = (string) $method;
		
		parent::__construct();
		
		$this->setId(time());
		$this->setVersion($this->_apiVersion);
		$this->setClass($class);
		
		$jsonrequest = new Zend_Json_Server_Request();
		$jsonresponse = new Zend_Json_Server_Response();
		
		$jsonrequest->setMethod($method);
		if (!is_null($args))
			$jsonrequest->addParams($args);
		
		$this->setRequest($jsonrequest);
		$this->setResponse($jsonresponse);
	}
	
}