<?php
class Semtech_Tagcloud_Stylesheet
{
	
	private $_classes;
	
	public function __construct()
	{
		$this->_classes = array();
		
		$this->_classes['ul.tagcloud']['list-style-type'] = "none";
		$this->_classes['ul.tagcloud']['padding'] = 0;
		$this->_classes['ul.tagcloud']['line-height'] = '2em';
		
		$this->_classes['ul.tagcloud li']['display'] = 'inline';
		$this->_classes['ul.tagcloud li']['line-height'] = '3em';
		$this->_classes['ul.tagcloud li']['white-space'] = 'nowrap';
		
		$this->_classes['ul.tagcloud li:after']['content'] = '","';
		
		$this->_classes['ul.tagcloud li:last-child:after']['content'] = '""';
		
		$this->_classes['ul.tagcloud .count']['font-size'] = '0.875em';
		$this->_classes['ul.tagcloud .count']['line-height'] = '1.714em';
		$this->_classes['ul.tagcloud .count']['color'] = '#888';
	}
	
	/**
	 * This method adds a rule to the stylesheet.
	 * 
	 * @param $class string
	 * @param $property string
	 * @param $value string
	 */
	public function addRule($class, $property, $value)
	{
		$this->_classes[$class][$property] = $value;	
	}
	
	/**
	 * This method gets the current tag separator.
	 * 
	 * @return string
	 */
	public function getSeparator()
	{
		return str_replace("\"", "", $_classes['ul.tagcloud li:after']['content']);
	}
	
	/**
	 * This function sets the tag separator.
	 * 
	 * @param $separator string
	 */
	public function setSeparator($separator)
	{
		$this->_classes['ul.tagcloud li:after']['content'] = "\"$separator\"";
	}
	
	/**
	 * This method returns the stylesheet object as a string.
	 * 
	 * @return string
	 */
	public function render()
	{
		return $this->_render();
	}
	
	/**
	 * This convienience method bypasses calling render().
	 * 
	 * @return string
	 */
	public function _toString()
	{
		return $this->_render();
	}
	
	/**
	 * This method actually creates the stylesheet string.
	 * 
	 * @return string
	 */
	private function _render()
	{
		$stylesheet = "";
		
		foreach ($this->_classes as $class => $properties)
		{
			$rule = "$class {\n";

			foreach ($properties as $property => $value)
			{
				$rule .= "\t$property: $value;\n";
			}
			
			$rule .= "}\n\n";
			
			$stylesheet .= $rule;
		}
		
		return $stylesheet;
	}
}
?>