<?php
/**
 * This class represents an element within a Semtech_Tagcloud.
 * 
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @package Semtech_Tagcloud
 */
class Semtech_Tagcloud_Element
{
	private $_text;
	private $_link;
	private $_weight;
	
	/**
	 * The constructor for a tagcloud element can take three
	 * parameters. The text parameter is the text of the element,
	 * the weight parameter is it's sizing factor and the link
	 * parameter can be used if you want the tag to be clickable.
	 * 
	 * @param $text
	 * @param $weight
	 * @param $link
	 * @return Semtech_Tagcloud_Element
	 */
	public function __construct($text = null, $weight = null, $link = null)
	{
		if ($text)
			$this->_text = $text;
			
		if ($link)
			$this->_link = $link;
			
		if ($weight)
			$this->_weight = $weight;
	}
	
	/**
	 * Gets the current text of the element.
	 * 
	 * @return string
	 */
	public function getText()
	{
		return $this->_text;
	}
	
	/**
	 * Sets the current text of the element.
	 * 
	 * @param $text string
	 */
	public function setText($text)
	{
		$this->_text = $text;
	}
	
	/**
	 * Gets the current link of the element.
	 * 
	 * @return string
	 */
	public function getLink()
	{
		return $this->_link;
	}
	
	/**
	 * Sets the current link of the element.
	 * 
	 * @param $link string
	 */
	public function setLink($link)
	{
		$this->_link = $link;	
	}
	
	/**
	 * Gets the current weight of the element.
	 * 
	 * @return int
	 */
	public function getWeight()
	{
		return $this->_weight;
	}
	
	/**
	 * Sets the current weight of the element.
	 * 
	 * @param $weight int
	 */
	public function setWeight($weight)
	{
		$this->_weight = $weight;
	}
	
	/**
	 * This is a convienience method so that you can access
	 * the properties within the element as public properties,
	 * bypassing the need to use the supplied getter methods.
	 * 
	 * @param $key
	 * @return mixed
	 */
	public function __get($key)
	{
		$key = "_$key";
		return $this->$key;
	}
	
	/**
	 * This is a convienience method so that you can assign
	 * the properties within the element as public properties,
	 * bypassing the need to use the supplied setter methods.
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value)
	{
		$key = "_$key";
		$this->$key = $value;
	}
}
?>