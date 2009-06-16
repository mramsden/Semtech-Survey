<?php
require_once "Zend/Service/Twitter.php";
require_once "Zend/Exception.php";
require_once "Semtech/Bitly.php";

class Semtech_Twitter
{

	const USERNAME = "semtech";
	const PASS = "semtpass";
	
	/**
	 * @var Zend_Service_Twitter
	 */
	private $_twitter;
	private $_requestsLeft = 0;
	
	public function __construct()
	{
		$this->_twitter = new Zend_Service_Twitter(self::USERNAME, self::PASS);
		if ($this->_twitter->accountVerifyCredentials()->isSuccess())
		{
			$remaininghits = "remaining-hits";
			$this->_requestsLeft = $this->_twitter->accountRateLimitStatus()->$remaininghits();
		}
		else
		{
			throw new Zend_Exception("Twitter username and password incorrect.");
		}
	}
	
	public function __destruct()
	{
		$this->_twitter->accountEndSession();
	}
	
	public function announceTechnology(Technology $technology)
	{
		$bitly = new Semtech_Bitly("cuscus1986", "R_fe74ec667b4f29a59c1a7d014a40b433");
		$message = "A new technology ".(strlen($technology->name) > 50 ? substr_replace($technology->name, "...", 44, strlen($technology->name) - 3) : $technology->name)." has been added to the survey. (".$bitly->shortenUrl("http://semtech-survey.ecs.soton.ac.uk/technology/".$technology->id).")";
		try 
		{
			$this->_sendMessage($message);
			$technology->announced();
		} 
		catch (Zend_Exception $e){
			print $e->getMessage();
		}
	}
	
	public function _sendMessage($msg)
	{
		if ($this->_requestsLeft == 0)
			throw new Zend_Exception("Number of requests exceeded for this hour.");
			
		$this->_twitter->statusUpdate($msg);
		$this->_requestsLeft = $this->_requestsLeft - 1;
	}
}
?>
