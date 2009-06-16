<?php
class Semtech_Logger
{
	
	/**
	 * The singleton instance of the class.
	 * @var Semtech_Logger
	 */
	private static $_instance = null;
	
	/**
	 * The current output path.
	 * @var string
	 */
	private $_output = "php://stderr";
	
	/**
	 * This is the instance of Zend_Logger we wish to use.
	 * 
	 * @var Zend_Logger
	 */
	private $_logger = null;
	
	/**
	 * This is the format that the log entry will take. The
	 * initial format is;
	 * 	[<timestamp>] %message% PHP_EOL
	 * @var string
	 */
	private $_logFormat = "";
	
	public static function getInstance()
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new Semtech_Logger();
		}
		
		return self::$_instance;
	}
	
	/**
	 * This method returns whatever the current output path
	 * is set to.
	 * 
	 * @return string
	 */
	public function getOutputPath()
	{
		return $this->_output;
	}
	
	/**
	 * This method will set the output path for the logger.
	 * 
	 * @param string $outputPath
	 */
	public function setOutputPath($outputPath)
	{
		$this->_output = (string) $outputPath;
		$this->_initLogger();
	}
	
	/**
	 * This method returns the current log format string.
	 * 
	 * @return string
	 */
	public function getLogFormat()
	{
		return $this->_logFormat;	
	}
	
	/**
	 * This method will set a new log format.
	 * 
	 * @param string $format
	 */
	public function setLogFormat($format)
	{
		$this->_logFormat = (string) $format;
		$this->_initLogger();
	}
	
	/**
	 * This method posts a message to the log with a default
	 * level of Zend_Log::INFO.
	 * 
	 * @param string $message
	 * @param int $priority
	 */
	public function log($message, $priority = Zend_Log::INFO)
	{
		if (is_null($this->_logger))
		{
			error_log("Logger has not been initialised yet. Initialising with current settings.");
			$this->_initLogger();
		}
			
		$this->_logger->log($message, $priority);
	}
	
	/**
	 * This method sets up the logger with the default settings.
	 * Should be called in the event of the output path or log format
	 * changing.
	 */
	private function _initLogger()
	{
		$writer = new Zend_Log_Writer_Stream($this->_output);
		$formatter = new Zend_Log_Formatter_Simple($this->_logFormat);
		$writer->setFormatter($formatter);
		
		$logger = new Zend_Log($writer);
		
		$this->_logger = $logger;
	}
}