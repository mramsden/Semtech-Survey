<?php
/**
 * This class will create a user creation form for use with the Semtech survey.
 *
 * @package Form_User_Create
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 */
class Semtech_Form_User_Create extends Semtech_Form_Form
{
	
	/**
	 * This function is the constructor for this object.
	 *
	 * @param array $options 
	 * @return Form_User_Create
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->setAction("/user/create")
			 ->setMethod("post");
			
		$name = new Zend_Form_Element_Text("name");
		$name->setRequired(true)
		     ->setLabel("Your Name");
		$this->addElement($name);

		$email = new Zend_Form_Element_Text("email");
		$email->setRequired(true)
		      ->setLabel("Your Email");
		$this->addElement($email);

		$password = new Zend_Form_Element_Password("password");
		$password->setRequired(true)
			 	 ->setLabel("Your Password");
		$this->addElement($password);

		$institution = new Zend_Form_Element_Text("institution");
		$institution->setRequired(false)
					->setLabel("Institution");
		$this->addElement($institution);

		$role = new Zend_Form_Element_Text("role");
		$role->setRequired(false)
			 ->setLabel("Role");
		$this->addElement($role);
		
		$this->addElement($this->createSubmitButton("Create Account"));
		
		return $this;
	}
	
}