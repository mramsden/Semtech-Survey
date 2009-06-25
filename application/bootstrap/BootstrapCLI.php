<?php
/**
 * The application bootstrap used by Zend_Application
 * 
 * @category Bootstrap
 * @package Bootstrap
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @copyright Copyright (c) 2009 University of Southampton
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	/**
	 * @var Zend_Log
	 */
	protected $_logger;

	/**
	 * @var Zend_Loader_Autoloader
	 */
	protected $_autoLoader;
	
	/**
	 * @var Zend_Application_Module_Autoloader
	 */
	protected $_moduleLoader;
	
	/**
	 * @var Zend_Config_Ini
	 */
	private $_configuration;
	
	/**
	 * @var Zend_Controller_Front
	 */
	public $frontController;
	
	/**
	 * Setup the logging.
	 */
	protected function _initLogging()
	{
		$logger = new Zend_Log();
		
		$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../var/logs/cli.log');
		//$writer = new Zend_Log_Writer_Stream('php://stdout');
		$logger->addWriter($writer);
		
		if ('production' == $this->getEnvironment()) {
			$filter = new Zend_Log_Filter_Priority(Zend_Log::INFO);
			$logger->addFilter($filter);
		}
		
		$this->_logger = $logger;
		Zend_Registry::set('log', $logger);
	}
	
	protected function _initConfig()
	{
	  $config = new Zend_Config_Ini(APPLICATION_PATH."/config/application.ini", APPLICATION_ENV);
	  $this->_configuration = $config;
	}
	
	/**
	 * Configure the default modules autoloading, here we first create
	 * a new module autoloader sepcifying the base path and namespace
	 * for our default module. This will automatically add the default
	 * resource types for us.
	 */
	protected function _initAutoload()
	{	
		$this->_autoloader = Zend_Loader_Autoloader::getInstance();
		$this->_autoloader->registerNamespace('Semtech_');
	
		$semtechmoduleloader = new Zend_Loader_Autoloader_Resource(array(
			'namespace' => 'Semtech',
			'basePath' => APPLICATION_PATH.'/modules/semtech'
		));
		$semtechmoduleloader->addResourceType('forms', '/forms', 'Form');
		$semtechmoduleloader->addResourceType('model', '/models', 'Model');
		$semtechmoduleloader->addResourceType('service', '/services', 'Service');
		
		$this->_autoloader->pushAutoloader($semtechmoduleloader);
	}
	
	protected function _initSearchEngine()
	{
		Zend_Registry::getInstance()->searchindex = APPLICATION_PATH.'/../var/search';
	}
	
	public function getConfiguration()
	{
	  return $this->_configuration;
	}

}