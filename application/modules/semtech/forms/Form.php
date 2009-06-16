<?php
/**
 * This class is used to help in the creation of forms for use in Semtech.
 * It contains helper functions and decorator definitions that can be used
 * across the Semtech form classes.
 *
 * @package Semtech_Form
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 */
class Semtech_Form_Form extends Zend_Form
{
	/**
	 * This defines decorators for a form which needs to be rendered as a table.
	 *
	 * @var array
	 */
	protected $_tableFormDecorator = array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form');
	
	/**
	 * This defines decorators for a row to be used in a form being rendered as a
	 * table.
	 *
	 * @var array
	 */
	protected $_tableFormRowDecorator = array(
			'ViewHelper',
			'Errors',
			array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
			array('Label', array('tag' => 'td')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
		);
	
	/**
	 * This defines decorators for a submit button to be used as part of a form
	 * rendered as a table.
	 *
	 * @var array
	 */
	protected $_tableFormSubmit = array(
			'ViewHelper',
			'Errors',
			array(array('data' => 'HtmlTag'), array('tag' => 'td', 'colspan' => '2', 'style' => 'text-align: right;')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
		);
	
	/**
	 * This defines decorators for a hidden form element so that it will not be
	 * rendered in the form at all and only appear as a hidden input.
	 *
	 * @var array
	 */
	private $_hiddenElementDecorator = array(
			'ViewHelper'
		);
	
	/**
	 * This function is the constructor for the Semtech_Form object.
	 * In all cases it is designed to be a passthrough to the Zend_Form class.
	 *
	 * @param array $options 
	 * @return Semtech_Form
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);
	}
	
	/**
	 * This function creates a hidden element on the form with the given id and value.
	 *
	 * @param string $id 
	 * @param string $value 
	 * @return Zend_Form_Element_Hidden
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function createHiddenElement($id, $value)
	{
		$hiddenelem = new Zend_Form_Element_Hidden($id);
		$hiddenelem->setValue($value)
			   	   ->setDecorators($this->_hiddenElementDecorator);
		return $hiddenelem;
	}
	
	/**
	 * This function creates a submit button for the form object. It takes label as the
	 * button label.
	 *
	 * @param string $label 
	 * @return Zend_Form_Element_Submit
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function createSubmitButton($label)
	{
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel($label);
		return $submit;
	}
}