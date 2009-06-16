<?php
class Semtech_Controller_Helper_Environment extends Zend_Controller_Action_Helper_Abstract
{
	const DEVELOPMENT_ENVIRONMENT = "development";
	const PRODUCTION_ENVIRONMENT = "production";
	
	private $_environment;
	
	/**
	 * __construct() - This function sets up the helper functions ready for use.
	 *
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk
	 */
	public function __construct()
	{
		$registry = Zend_Registry::getInstance();
		try
		{
			$this->_environment = Zend_Registry::getInstance()->get('environment');
		}
		catch (Zend_Exception $e)
		{
			// Catch the exception thrown due to the fact that the
			// registry entry for the environment does not exist.
			// Fall back to see if it has been defined in the environment.
			if (defined("APPLICATION_ENV"))
			{
				$this->_environment = APPLICATION_ENV;
			}
			else
			{
				require_once "Zend/Exception.php";
				throw new Zend_Exception("Unable to find an application environment variable. Please ensure that either the registry has an 'environment' entry or the 'APPLICATION_ENV' global is defined.");
			}
		}
	}
	
	public function getEnvironment()
	{
		return $this->_environment;
	}
	
	public function direct()
	{
		return $this->getEnvironment();
	}
}