<?php
require_once "Zend/Service/Twitter.php";
require_once "Zend/Exception.php";
require_once "Semtech/Bitly.php";

/**
 * This class provides some convienience wrapping around the Zend_Service_Twitter
 * class. It adds functions that are specific to the Semtech site.
 *
 * @package Semtech
 * @author Marcus Ramsden
 */
class Semtech_Twitter
{
	
	/**
	 * @var Zend_Service_Twitter
	 */
	private $_twitter;
	
	/**
	 * @var int
	 */
	private $_requestsLeft;

  /**
   * The username for the Twitter account.
   *
   * @var string
   */
  private $_username;
  
  /**
   * The password for the Twitter account.
   *
   * @var string
   */
  private $_password;
	
	/**
	 * The constructor takes the supplied username and password
	 * for the Twitter service.
	 *
	 * @param string $username 
	 * @param string $password 
	 * @author Marcus Ramsden
	 */
	public function __construct($username, $password)
	{
	  
	  $this->setUsername($username);
	  $this->setPassword($password);
		
	}
	
	public function __destruct()
	{
		$this->_logout();
	}
	
	/**
	 * Returns what the username for Twitter is currently set to.
	 *
	 * @return string
	 * @author Marcus Ramsden
	 */
	public function getUsername()
	{
	  return $this->_username;
	}
	
	/**
	 * Sets what the username for Twitter is. If the class had a validated
	 * session with some old credentials it is ended.
	 *
	 * @param string $username 
	 * @return void
	 * @author Marcus Ramsden
	 */
	public function setUsername($username)
	{
	  $this->_username = $username;
	  
	  if ($this->loggedIn())
	  {
	    $this->_logout();
	  }
	}
	
	/**
	 * Returns what the password for Twitter is currently set to.
	 *
	 * @return string
	 * @author Marcus Ramsden
	 */
	public function getPassword()
	{
	  return $this->_password;
	}
	
	/**
	 * Sets what the password for Twitter is. If the class had a validated
	 * session with some old credentials it is ended.
	 *
	 * @param string $password 
	 * @return void
	 * @author Marcus Ramsden
	 */
	public function setPassword($password)
	{
	  $this->_password = $password;
	  
	  if ($this->loggedIn())
	  {
	    $this->_logout();
	  }
	}
	
	/**
	 * Indicates whether the instance of the service is logged in or not.
	 *
	 * @return void
	 * @author Marcus Ramsden
	 */
	public function loggedIn()
	{
	  return !is_null($this->_twitter);
	}
	
	/**
	 * This function returns how many more API calls can be made in the next
	 * hour.
	 *
	 * @return int
	 * @author Marcus Ramsden
	 */
	public function getRemainingApiCalls()
	{
	  if (!$this->loggedIn())
	  {
	    $this->_login();
	  }
	  
	  return $this->_requestsLeft;
	}
	
	/**
	 * This function is responsible for shortening the url to the technology and
	 * then creating the message that is to be posted to Twitter.
	 *
	 * @param Semtech_Model_Technology $technology
	 * @author Marcus Ramsden
	 */
	public function announceTechnology(Semtech_Model_Technology $technology)
	{
		$bitly = new Semtech_Bitly("cuscus1986", "R_fe74ec667b4f29a59c1a7d014a40b433");
		$message = "A new technology ".(strlen($technology->name) > 50 ? substr_replace($technology->name, "...", 44, strlen($technology->name) - 3) : $technology->name)." has been added to the survey. (".$bitly->shortenUrl("http://semtech-survey.ecs.soton.ac.uk/technology/".$technology->id).")";
		try 
		{
			$this->_sendMessage($message);
			$technology->announced();
		} 
		catch (Zend_Exception $e){
			Zend_Registry::get("log")->error($e->getMessage());
		}
	}
	
	/**
	 * This will return the last 3 messages on the logged in user's account.
	 * You can get more than 3 messages by changing the value of the count
	 * parameter. By default the result will be cached for 1 hour to reduce
	 * the number of hits on the Twitter API. This can be changed by specifying
	 * the number of seconds the cache remains valid for. As soon as the cache
	 * expires a new request is made.
	 *
	 * @param int $count
	 * @param int $cache_time
	 * @return void
	 * @author Marcus Ramsden
	 */
	public function getStatusMessages($count = 5, $cache_time = 3600)
	{
	  // Initialize the cache for tweets
	  $cache = Zend_Cache::factory('Core', 'File', array('lifeTime' => $cache_time, 'automatic_serialization' => 'true'), array('cacheDir' => APPLICATION_PATH.'/../var/cache'));
	  
	  $tweets = $cache->load('twitter_tweets');
	  
	  if ($cache_time == 0 || !$tweets || sizeof($tweets) < $count)
	  {
	    Zend_Registry::get("log")->info("Cache Miss: Querying Twitter for tweets...");
  	  if (!$this->loggedIn())
  	  {
  	    $this->_login();
  	  }
  	  
  	  if ($this->loggedIn())
  	  {
  	    $twitter_response = $this->_twitter->statusUserTimeline(array('count' => $count));
    	  $tweets = array();
    	  if ($twitter_response->isSuccess())
    	  {
    	    foreach ($twitter_response->statuses as $status)
    	    {
    	      $tweets[] = array("user" => (string)$status->user->name, "message" => $this->_parseLinks((string)$status->text));
    	    }
    	  }
    	  $cache->save($tweets, 'twitter_tweets');
    	  $this->_requestsLeft = $this->_requestLeft - 1;
  	  }
  	  else
  	  {
  	    throw new Zend_Exception("Unable to get user timeline as login failed.");
  	  }
	  }
	  
	  return $tweets;
	}
	
	/**
	 * Flushes the status message cache. Returns true on success.
	 *
	 * @return boolean
	 * @author Marcus Ramsden
	 */
	public function flushStatusMessages()
	{
	  $cache = Zend_Cache::factory('Core', 'File', array('lifeTime' => $cache_time, 'automatic_serialization' => 'true'), array('cacheDir' => APPLICATION_PATH.'/../var/cache'));
	  
	  return $cache->remove('twitter_tweets'); 
	}
	
	/**
	 * This function is responsible for updating the account's Twitter status.
	 *
	 * @param string $msg
	 * @author Marcus Ramsden
	 */
	public function _sendMessage($msg)
	{
	  if (!$this->loggedIn())
	  {
	    $this->_login();
	  }
	  
		if ($this->_requestsLeft == 0)
		{
		  Zend_Log::get("log")->crit(__CLASS__.": No more API calls can be made this hour.");
      throw new Zend_Exception("Number of requests exceeded for this hour.");
		}
		
		if ($this->loggedIn())
		{
		  $this->_twitter->statusUpdate($msg);
  		$this->_requestsLeft = $this->_requestsLeft - 1;
		}	
		else
		{
		  throw new Zend_Exception("Unable to send message since login failed.");
		}
	}
	
	/**
	 * This function will look at the supplied text. If it sees any urls then it will
	 * wrap them with anchor tags.
	 *
	 * @param string $text 
	 * @return string
	 * @author Marcus Ramsden
	 */
	private function _parseLinks($text)
	{
    if (preg_match_all('/((ht|f)tps?:\/\/([\w\.]+\.)?[\w-]+(\.[a-zA-Z]{2,4})?[^\s\r\n\(\)"\'<>\,\!]+)/si', $text, $urls))
    {

      foreach (array_unique($urls[1]) AS $url)
      {
        $text = str_replace($url, '<a href="'. $url .'">'. $url .'</a>', $text);
      }
    
    }

    return $text;
	}
	
	/**
	 * Attempt to login in to Twitter with the currently set credentials.
	 *
	 * @return void
	 * @author Marcus Ramsden
	 */
	private function _login()
	{
	  if (is_null($this->_twitter))
	  {
	    if (is_null($this->_username) || is_null($this->_password))
  	  {
  	    throw new Zend_Exception("Both the username and password need to be specified.");
  	  }

  	  $this->_twitter = new Zend_Service_Twitter($this->_username, $this->_password);

  	  if ($this->_twitter->accountVerifyCredentials()->isSuccess())
  		{
  			$remaininghits = "remaining-hits";
  			$this->_requestsLeft = $this->_twitter->accountRateLimitStatus()->$remaininghits();
  			$this->_loggedIn = true;
  		}
  		else
  		{
  			throw new Zend_Exception("Twitter username and password incorrect.");
  		}
	  }
	}
	
	/**
	 * Logout of twitter and destroy any current session.
	 *
	 * @return void
	 * @author Marcus Ramsden
	 */
	private function _logout()
	{
	  if (!is_null($this->_twitter))
	  {
	    $this->_twitter->accountEndSession();
	  }
	  
	  $this->_twitter = null;
	}
}
?>
