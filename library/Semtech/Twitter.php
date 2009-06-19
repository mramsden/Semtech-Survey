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

	const USERNAME = "semtech";
	const PASS = "semtpass";
	
	/**
	 * @var Zend_Service_Twitter
	 */
	private $_twitter;
	
	/**
	 * @var int
	 */
	private $_requestsLeft = 0;
	
	public function __construct($username = null, $pass = null)
	{
	  if (!is_null($username) && !is_null($pass))
	  {
	    $this->_twitter = new Zend_Service_Twitter($username, $pass);
	  }
	  else
	  {
      $this->_twitter = new Zend_Service_Twitter(self::USERNAME, self::PASS);
	  }
	  
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
	
	/**
	 * This function returns how many more API calls can be made in the next
	 * hour.
	 *
	 * @return int
	 * @author Marcus Ramsden
	 */
	public function getRemainingApiCalls()
	{
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
		if ($this->_requestsLeft == 0)
		{
		  Zend_Log::get("log")->crit(__CLASS__.": No more API calls can be made this hour.");
      throw new Zend_Exception("Number of requests exceeded for this hour.");
		}
			
		$this->_twitter->statusUpdate($msg);
		$this->_requestsLeft = $this->_requestsLeft - 1;
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
}
?>
