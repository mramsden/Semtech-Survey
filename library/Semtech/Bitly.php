<?php
require_once "Zend/Http/Client.php";

/**
 * This class provides a connection to Bitly and allows developers
 * to access the API and shorten URLs.
 *
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @package Semtech_Bitly
 */
class Semtech_Bitly
{

	/**
	 * This is the URL of the bit.ly API.
	 */
	const BITLY_URL = "http://api.bit.ly";
	
	/**
	 * This is the version of the API to use. You shouldn't
	 * change this unless Bitly updates it's current API
	 * version.
	 */
	const API_VERSION = "2.0.1";
	
	/**
	 * Request that the response format be in JSON.
	 */
	const RESPONSE_FORMAT_JSON = "json";
	
	/**
	 * Request that the response format be in XML.
	 */
	const RESPONSE_FORMAT_XML = "xml";
	
	private $_username = "";
	private $_apikey = "";
	private $_responseformat = "xml";
	
	/**
	 * The constructor method takes two parameters the
	 * account username that you wish to use, and the
	 * apikey associated with that username. The default
	 * format is XML but you can use the appropriate
	 * method to change this.
	 *
	 * @param string $username
	 * @param string $apikey
	 */
	public function __construct($username, $apikey)
	{
		$this->_username = (string) $username;
		$this->_apikey = (string) $apikey;
		$this->_responseformat = self::RESPONSE_FORMAT_XML;
	}
	
	/**
	 * This method returns the current response format
	 * requested from the bit.ly api.
	 *
	 * @return string
	 */
	public function getResponseFormat()
	{
		return $this->_responseformat;
	}
	
	/**
	 * This method sets the desired response format
	 * requested from the bit.ly api. It must be set
	 * to either Semtech_Bitly::RESPONSE_FORMAT_JSON
	 * or Semtech_Bitly::RESPONSE_FORMAT_XML.
	 *
	 * @param string $format
	 */
	public function setResponseFormat($format)
	{
		if (!($format == self::RESPONSE_FORMAT_JSON && $format == self::RESPONSE_FORMAT_XML))
			throw new Zend_Exception("The format $format is not supported.");
			
		$this->_responseformat = $format;
	}
	
	/**
	 * This method will return the shortened bit.ly version
	 * of the supplied URL.
	 *
	 * @param string $query
	 * @return string
	 */
	public function shortenUrl($query)
	{
		
		$data = $this->_sendRequest($this->_createRequestUrl("shorten", array('longUrl' => $query)));
		
		$shortenedurl = false;
		$data = new SimpleXMLElement($data);
		if ($data->errorCode == 0)
		{
			foreach ($data->results->children() as $nodekeyval)
			{
				if ($shortenedurl)
				{
					if (!is_array($shortenedurl))
					{
						$temp = $shortenedurl;
						$shortenedurl = array();
						$shortenedurl[] = $temp;
					}
					$shortenedurl[] = $nodekeyval->shortUrl;
				}
				else
				{
					$shortenedurl = $nodekeyval->shortUrl;
				}
			}
		}
		else
		{
			throw new Zend_Exception("The request resulted in a bit.ly error: ".$data->errorMessage);
		}
		
		return $shortenedurl;
	}
	
	/**
	 * This method will execute the HTTP request to the
	 * specified URL and return the result as a string.
	 *
	 * @param string $uri
	 * @return string
	 */
	private function _sendRequest($uri)
	{
		$httpclient = new Zend_Http_Client();
		$httpclient->setUri($uri);
		$httpclient->encodeAuthHeader($this->_username, $this->_password);
		$httpresponse = $httpclient->request();
		if ($httpresponse->isSuccessful)
			throw new Zend_Exception("The request to bit.ly was unsuccessful, ".$httpresponse->getStatus()." ".$httpresponse->getMessage().".");
			
		return $httpresponse->getBody();
	}
	
	/**
	 * This method will create the URL for the request
	 * you want to make to the API.
	 *
	 * @param string $method
	 */
	private function _createRequestUrl($method, $args)
	{
		if (!is_array($args))
			throw new Zend_Exception("The args parameter must be an array.");
		
		$parameters = array(
			'login' => $this->_username,
			'apiKey' => $this->_apikey,
			'version' => self::API_VERSION,
			'format' => $this->_responseformat
			);
			
		$parameters = array_merge($parameters, $args);
		
		return self::BITLY_URL."/$method?".$this->_createQueryString($parameters);
	}
	
	/**
	 * This method creates the query string as part of the request
	 * URL. The query values should be an associative array.
	 *
	 * @param array $query_values
	 */
	private function _createQueryString($query_values)
	{
		if (!is_array($query_values))
			throw new Zend_Exception("Query values should be supplied as an array.");
			
		$temp = array();
		foreach ($query_values as $key => $value)
		{
			$temp[] = "$key=$value";
		}
		
		return join("&", $temp);
	}
	
}
?>