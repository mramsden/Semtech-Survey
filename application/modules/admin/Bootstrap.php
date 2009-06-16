<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
	
	/**
	 * @var Zend_Log
	 */
	private $_logger;
	
	/**
	 * Setup the logging.
	 */
	protected function _initLogging()
	{		
		$logger = new Zend_Log();
		
		$writer = 'production' == $this->getEnvironment() ? new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../var/logs/app.log') : new Zend_Log_Writer_Firebug();
		$logger->addWriter($writer);
		
		if ('production' == $this->getEnvironment()) {
			$filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT);
			$logger->addFilter($filter);
		}
		
		$this->_logger = $logger;
	}
	
	protected function _initAdminAutoloader()
	{
		$this->_logger->info("Bootstrap ".__METHOD__);
		
		$adminmoduleloader = new Zend_Loader_Autoloader_Resource(array(
			'namespace' => 'Admin',
			'basePath' => APPLICATION_PATH.'/modules/admin'
		));
		$adminmoduleloader->addResourceType("forms", "/forms", "Form");
		$adminmoduleloader->addResourceType("model", "/models", "Model");
		
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->pushAutoloader($adminmoduleloader);
	}
	
}