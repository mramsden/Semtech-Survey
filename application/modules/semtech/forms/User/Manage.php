<?php
/**
 * This class extends the create user form so that a user can edit
 * their already exisiting data.
 *
 * @package Semtech_Form_User
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 */
class Semtech_Form_User_Manage extends Semtech_Form_Form
{
	
	/**
	 * This function is the default constructor for the manage profile
	 * form. It requires that you supply a userid so that the form
	 * knows which profile is being added to.
	 *
	 * @param array $options 
	 * @return Form_User_Manage
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$name = new Zend_Form_Element_Text("name");
		$name->setRequired(true)
		     ->setLabel("Your Name");
		$this->addElement($name);

		$email = new Zend_Form_Element_Text("email");
		$email->setRequired(true)
		      ->setLabel("Your Email");
		$this->addElement($email);

		$newpassword = new Zend_Form_Element_Password("newpassword");
		$newpassword->setRequired(false)
			 	 	->setLabel("New Password");
		$this->addElement($newpassword);

		$institution = new Zend_Form_Element_Text("institution");
		$institution->setRequired(false)
					->setLabel("Institution");
		$this->addElement($institution);

		$role = new Zend_Form_Element_Text("role");
		$role->setRequired(false)
			 ->setLabel("Role");
		$this->addElement($role);
		
		$this->addElement($this->createSubmitButton("Update Account"));
	}
	
}