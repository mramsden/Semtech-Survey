<?php
/**
 * This class will create a Semtech login form.
 *
 * @package Form_User
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 */
class Semtech_Form_User_Login extends Semtech_Form_Form
{

	/**
	 * This function is the constructor for the login form.
	 *
	 * @param array $options 
	 * @return Form_User_Login
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->setAction("/user/login")
		     ->setMethod("post");
		
		$email = new Zend_Form_Element_Text('email');
		$email->setRequired(true)
		      ->setLabel("Email Address")
		      ->addFilter("StringToLower");
		$this->addElement($email);
		

		$password = new Zend_Form_Element_Password('password');
		$password->setRequired(true)
			 	 ->setLabel("Password");
		$this->addElement($password);
		
		$rememberme = new Zend_Form_Element_Checkbox('rememberme');
		$rememberme->setLabel("Remember Me (for 2 weeks)");
		$this->addElement($rememberme);
		
		$this->addElement($this->createSubmitButton("Login"));
	}
	
}