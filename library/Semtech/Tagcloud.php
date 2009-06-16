<?php
/**
 * This class is designed to represent a tag cloud. It is able to
 * render itself into HTML.
 * This class has a number of default behaviours which you can
 * modify to fit your needs.
 * By default the tagcloud will;
 * 	- Randomize the order of the elements.
 *  - Render elements which have the link property set with hyperlinks.
 *  - Assume the desired font size is a percentage and falls in the range
 *    of 100% to 250%.
 *  - Render the weight of the element after it's name.
 *  - Only render elements with a weight greater than the mean of the weights.
 * 
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @package Semtech_Tagcloud
 */
require_once("Semtech/Tagcloud/Element.php");
require_once("Semtech/Tagcloud/Stylesheet.php");

class Semtech_Tagcloud
{
	
	const FONT_SIZE_PX = 'px';
	const FONT_SIZE_PERCENTAGE = '%';
	const CULL_MODE_NONE = 'cullnone';
	const CULL_MODE_MEAN = 'cullmean';
	const CULL_MODE_MEDIAN = 'cullmedian';
	
	private $_elements;
	private $_randomize;
	private $_renderlinks;
	private $_renderweights;
	private $_cullmode;
	private $_fontsizeunit;
	private $_minfontsize;
	private $_maxfontsize;
	private $_stylesheet;
	
	/**
	 * Initialises the tagcloud ready to recieve tagcloud elements.
	 * By default the tagcloud will randomize the order elements go
	 * into the cloud. The maxfontsize parameter specifies the
	 * maximum font size. The minfontsize parameter specifies the
	 * minimum font size. The fontsizeunit parameter specifies the
	 * unit of the font sizes. You must use one of the font size
	 * constantants.
	 * 
	 * @param integer $maxfontsize
	 * @param integer $minfontsize
	 * @param string $fontsizeunit
	 * @return Semtech_Tagcloud
	 */
	public function __construct($maxfontsize = 250, $minfontsize = 100, $fontsizeunit = Semtech_Tagcloud::FONT_SIZE_PERCENTAGE)
	{
		if (!($fontsizeunit == Semtech_Tagcloud::FONT_SIZE_PERCENTAGE || $fontsizeunit == Semtech_Tagcloud::FONT_SIZE_PX))
			throw new Zend_Exception("You must use one of the specified FONT_SIZE_* constants.");
		
		$this->_elements = array();
		$this->_randomize = true;
		$this->_renderlinks = true;
		$this->_renderweights = true;
		$this->_cullmode = Semtech_Tagcloud::CULL_MODE_MEAN;
		$this->_fontsizeunit = $fontsizeunit;
		$this->_minfontsize = $minfontsize;
		$this->_maxfontsize = $maxfontsize;
		$this->_stylesheet = new Semtech_Tagcloud_Stylesheet();
	}
	
	/**
	 * Adds the supplied tagcloud element to the tagcloud.
	 * 
	 * @param Semtech_Tagcloud_Element $element
	 */
	public function addElement(Semtech_Tagcloud_Element $element)
	{
		array_push($this->_elements, $element);
	}
	
	/**
	 * This method will return the current elements array.
	 * 
	 * @return array
	 */
	public function getElements()
	{
		return $this->_elements;
	}
	
	/**
	 * This method will set the elements in the tagcloud
	 * to those contained in the supplied array.
	 * 
	 * @param array $elements
	 */
	public function setElements($elements)
	{
		if (!is_array($elements))
			throw new Zend_Exception("setElements expects an array as a parameter.");
		
		$this->_elements = $elements;
	}
	
	/**
	 * This method returns what the current randomize
	 * setting is.
	 * 
	 * @return boolean
	 */
	public function getRandomize()
	{
		return $this->_randomize;
	}
	
	/**
	 * This method will set the randomize setting to the specified
	 * value.
	 * 
	 * @param boolean $randomize
	 */
	public function setRandomize($randomize)
	{
		$this->_randomize = $randomize;
	}
	
	/**
	 * This method will return the current renderlinks setting.
	 *
	 * @return boolean
	 */
	public function getRenderlinks()
	{
		return $this->_renderlinks;
	}
	
	/**
	 * This method will set the renderlinks setting to the supplied
	 * value.
	 * 
	 * @param boolean $renderlinks
	 */
	public function setRenderlinks($renderlinks)
	{
		$this->_renderlinks = $renderlinks;
	}
	
	/**
	 * This method will return the current renderweights setting.
	 * 
	 * @return boolean
	 */
	public function getRenderweights()
	{
		return $this->_renderweights;
	}
	
	/**
	 * This method will set the renderweights setting to the supplied
	 * value.
	 * 
	 * @param boolean $renderweights
	 */
	public function setRenderweights($renderweights)
	{
		$this->_renderweights = $renderweights;
	}
	
	/**
	 * This method will return the current tag seperator.
	 * 
	 * @return string
	 */
	public function getSeparator()
	{
		return $this->_separator;
	}
	
	/**
	 * This method will set the cullmode to use.
	 * 
	 * @param string $cullmode
	 */
	public function setCullmode($cullmode)
	{
		if ($cullmode != Semtech_Tagcloud::CULL_MODE_MEAN && $cullmode != Semtech_Tagcloud::CULL_MODE_MEDIAN && $cullmode != Semtech_Tagcloud::CULL_MODE_NONE)
			throw new Zend_Exception("The supplied cullmode is not valid.");
			
		$this->_cullmode = $cullmode;
	}
	
	/**
	 * This method returns the stylesheet associated with
	 * this tag cloud.
	 * 
	 * @return Semtech_Tagcloud_Stylesheet
	 */
	public function getStylesheet()
	{
		return $this->_stylesheet;
	}
	
	/**
	 * This method will cause the tagcloud to render itself
	 * and then return the result.
	 * 
	 * @return string
	 */
	public function render()
	{
		return $this->_render();
	}
	
	/**
	 * This method is a convienience method to have the object
	 * print without the need for calling the render method.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->_render();
	}
	
	/**
	 * This method does the actual rendering.
	 * 
	 * @return string
	 */
	private function _render()
	{
		$this->_cullElements();
		
		if ($this->_randomize)
			shuffle($this->_elements);	
			
		$tagcloudstring = "";
		foreach ($this->_elements as $element)
		{
			$tagstring = $element->text;
			if ($this->_renderweights)
				$tagstring .= " <span class=\"weight\">({$element->weight})</span>";
				
			if ($this->_renderlinks && $element->link != null)
				$tagstring = "<a href=\"{$element->link}\">$tagstring</a>";

			//$this->_stylesheet->addRule("ul.tagcloud li.weight-{$element->weight}", "font-size", $this->_calculateFontSize($element));	
			$tagstring = " <li style=\"font-size: {$this->_calculateFontSize($element)}\">$tagstring</li>";
				
			$tagcloudstring .= $tagstring;
		}
		
		return "<ul class=\"tagcloud\">".$tagcloudstring."</ul>";
	}
	
	private function _cullElements()
	{
		if (count($this->_elements) && $this->_cullmode != Semtech_Tagcloud::CULL_MODE_NONE)
		{
			$cullweight = 10;
			switch ($this->_cullmode)
			{
				case Semtech_Tagcloud::CULL_MODE_MEAN:
					$totalweight = 0;
					foreach($this->_elements as $element)
					{
						$totalweight = $totalweight + $element->weight;
					}
					$cullweight = $totalweight/count($this->_elements);
					break;
				case Semtech_Tagcloud::CULL_MODE_MEDIAN:
					$weightoccurences = array();
					foreach($this->_elements as $element)
					{
						if (!isset($weightoccurences[$element->weight]))
						{
							$weightoccurences[$element->weight] = 1;
						}
						else
						{
							$weightoccurences[$element->weight]++;
						}
					}
					
					rsort($weightoccurences);
					$cullweight = array_shift($weightoccurences);
					break;
				default:
					throw new Zend_Exception("No valid cull mode has been set."); 
			}
			
			$culledcloud = array();
			foreach ($this->_elements as $element)
			{
				if ($element->weight >= $cullweight)
				{
					array_push($culledcloud, $element);
				}
			}
			
			$this->_elements = $culledcloud;
		}
	}
	
	/**
	 * This method calculates the fontsize that the tag needs
	 * to be. It returns a css property string with the
	 * requested unit.
	 * @return string
	 */
	private function _calculateFontSize(Semtech_Tagcloud_Element $element)
	{
		$spread = $this->_getSpread();
		$step = ($this->_maxfontsize - $this->_minfontsize)/$spread;
		$size = ceil($this->_minfontsize + (($element->weight - $this->_getMinWeight()) * $step));
		
		return $size.$this->_fontsizeunit;
	}
	
	/**
	 * This method will return the spread of weights in the
	 * tag cloud. null is returned if the cloud has no
	 * elements.
	 * 
	 * @return integer
	 */
	private function _getSpread()
	{
		if (count($this->_elements))
		{
			$spread = $this->_getMaxWeight() - $this->_getMinWeight();
			if ($spread == 0)
				$spread = 1;
		}
		else
		{
			$spread = null;
		}
		
		return $spread;
	}
	
	/**
	 * This method will return the maximum weight in the
	 * cloud. null is returned if there are no elements
	 * in the cloud.
	 * 
	 * @return integer
	 */
	private function _getMaxWeight()
	{
		$max_weight = null;
		
		foreach ($this->_elements as $element)
		{
			if ($max_weight == null)
				$max_weight = $element->weight;
			
			if ($element->weight > $max_weight)
				$max_weight = $element->weight;
		}
		
		return $max_weight;
	}
	
	/**
	 * This method will return the minimum weight in the
	 * cloud. null is returned if there are no elements
	 * in the cloud.
	 * 
	 * @return integer
	 */
	private function _getMinWeight()
	{
		$min_weight = null;
		
		foreach ($this->_elements as $element)
		{
			if ($min_weight == null)
				$min_weight = $element->weight;
			
			if ($element->weight < $min_weight)
				$min_weight = $element->weight;	
		}
		
		return $min_weight;
	}
}
?>