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
	protected $_configuration;
	
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
		
		$writer = 'production' == $this->getEnvironment() ? new Zend_Log_Writer_Stream(APPLICATION_PATH.'/../var/logs/app.log') : new Zend_Log_Writer_Firebug();
		$logger->addWriter($writer);
		
		if ('production' == $this->getEnvironment()) {
			$filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT);
			$logger->addFilter($filter);
		}
		
		$this->_logger = $logger;
		Zend_Registry::set('log', $logger);
	}
	
	/**
	 * Setup request and response so we can use Firebug for logging
	 * also make the dispatcher prefix the default module
	 */
	protected function _initFrontControllerSettings()
	{
		$this->_logger->info('Bootstrap '.__METHOD__);
		
		$this->bootstrap('frontController');
		$this->bootstrap('modules');
		$this->frontController->setResponse(new Zend_Controller_Response_Http());
		$this->frontController->setRequest(new Zend_Controller_Request_Http());
		$this->frontController->addControllerDirectory(APPLICATION_PATH.'/modules/admin/controllers', 'admin');
		//$this->frontController->addModuleDirectory(APPLICATION_PATH.'/modules/admin');		
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
		$this->_logger->info('Bootstrap '.__METHOD__);
		
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
	
	/**
	 * Setup locale
	 */
	protected function _initLocale()
	{
		$this->_logger->info('Bootstrap '.__METHOD__);
		
		$locale = new Zend_Locale('en_GB');
		Zend_Registry::set('Zend_Locale', $locale);
	}
	
	protected function _initSearchEngine()
	{
		$this->_logger->info('Bootstrap '.__METHOD__);
		
		Zend_Registry::getInstance()->searchindex = APPLICATION_PATH.'/../var/search';
	}
	
	/**
	 * Setup the database profiling
	 */
	protected function _initDbProfiler()
	{
		$this->_logger->info('Bootstrap '.__METHOD__);
		
		if ('production' !== $this->getEnvironment()) {
			$this->bootstrap('db');
			$profiler = new Zend_Db_Profiler_Firebug("All DB Queries");
			$profiler->setEnabled(true);
			$this->getPluginResource('db')->getDbAdapter()->setProfiler($profiler);
		}
	}
	
	/**
	 * Setup the view
	 */
	protected function _initView()
	{
		$this->_logger->info('Bootstrap '.__METHOD__);
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->init();
		
		$this->_view = $viewRenderer->view;
		
		$this->_view->setEncoding('UTF-8');
		$this->_view->doctype('XHTML1_STRICT');
		
		$this->_view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
		$this->_view->headMeta()->appendHttpEquiv('Content-Language', 'en-US');
		
		$this->_view->headLink()->appendStylesheet('/stylesheets/main.css');
		
		$this->_view->headScript()->appendFile('/js/lib/jquery-1.3.2.min.js');
		$this->_view->headScript()->appendFile('/js/lib/jquery.qtip-1.0.0-rc3.min.js');
		$this->_view->headScript()->appendFile('/js/lib/tooltips.js');
		
		$this->_view->headTitle('SemTech Survey');
		$this->_view->headTitle()->setSeparator(' :: ');
		
		Zend_Layout::startMvc(array(
			'layout' => 'main',
			'layoutPath' => APPLICATION_PATH.'/layouts/scripts'
		));
	}
	
	/**
	 * Setup the routes that are to be used.
	 */
	protected function _initRoutes()
	{
		$this->_logger->info('Bootstrap '.__METHOD__);
		
		$this->bootstrap('frontController');
		
		$router = $this->frontController->getRouter();
		
		$route = new Zend_Controller_Router_Route(
			'technology/:techid/:detail',
			array(
				'action' => 'view',
				'controller' => 'technology'
			),
			array(
				'techid' => '[0-9]+',
				'detail' => '[a-z]+'
			)
		);
		$router->addRoute('technologydetail', $route);
		
		$route = new Zend_Controller_Router_Route(
			'technology/:techid',
			array(
				'action' => 'view',
				'controller' => 'technology'
			),
			array(
				'techid' => '[0-9]+'
			)
		);
		$router->addRoute('technology', $route);
		
		$route = new Zend_Controller_Router_Route(
			'technology/:techid/:revid',
			array(
				'action' => 'view',
				'controller' => 'technology'
			),
			array(
				'techid' => '[0-9]+',
				'revid' => '[0-9]+'
			)
		);
		$router->addRoute('technologyrev', $route);
		
		$route = new Zend_Controller_Router_Route(
			'technology/edit/:techid',
			array(
				'action' => 'edit',
				'controller' => 'technology'
			),
			array(
				'techid' => '[0-9]+'
			)
		);
		$router->addRoute('technologyedit', $route);
		
		$route = new Zend_Controller_Router_Route(
			'technology/discussion/:techid',
			array(
				'action' => 'view',
				'controller' => 'discussion'
			),
			array(
				'techid' => '[0-9]+'
			)
		);
		$router->addRoute('technologydiscussion', $route);
		
		$route = new Zend_Controller_Router_Route(
			'technology/relations/:techid',
			array(
				'action' => 'view',
				'controller' => 'relation'
			),
			array(
				'techid' => '[0-9]+'
			)
		);
		$router->addRoute('technologyrelations', $route);
		
		$route = new Zend_Controller_Router_Route(
			'technology/tags/:techid/:tagcat',
			array(
				'action' => 'tags',
				'controller' => 'technology'
			),
			array(
				'techid' => '[0-9]+',
				'tagcat' => '[0-9]+'
			)
		);
		$router->addRoute('technologytags', $route);
		
		$route = new Zend_Controller_Router_Route(
			'technology/activity/:techid/:revid',
			array(
				'action' => 'activity',
				'controller' => 'technology'
			),
			array(
				'techid' => '[0-9]+',
				'tagcat' => '[0-9]+'
			)
		);
		$router->addRoute('technologyactivity', $route);
	
	  $route = new Zend_Controller_Router_Route(
	    'search/tag/:tagid',
	    array(
	      'action' => 'tag',
	      'controller' => 'search'
	    ),
	    array(
	      'tagid' => '[0-9]+'
	    )
	  );
	  $router->addRoute('tagsearch', $route);
	}
	
	protected function _initActionHelpers()
	{
		$this->_logger->info('Bootstrap '.__METHOD__);
		Zend_Controller_Action_HelperBroker::addHelper(new Semtech_Controller_Helper_Environment());
		Zend_Controller_Action_HelperBroker::addHelper(new Semtech_Controller_Helper_ReturnToTarget());
	}
	
	protected function _initTwitterService()
	{
	  $this->_logger->info("Bootstrap ".__METHOD__);
	  $twitter = new Semtech_Twitter($this->_configuration->service->twitter->username, $this->_configuration->service->twitter->password);
	  Zend_Registry::set("twitter", $twitter);
	}

}