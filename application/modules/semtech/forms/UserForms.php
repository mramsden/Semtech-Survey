<?php
class UserForms {

	public static function loginForm($options = null)
	{
		$form = new Zend_Form($options);
		$form->setAction("/user/login")
		     ->setMethod("post");
		
		$email = new Zend_Form_Element_Text('email');
		$email->setRequired(true)
		      ->setLabel("Email Address")
		      ->addFilter("StringToLower");
		$form->addElement($email);
		

		$password = new Zend_Form_Element_Password('password');
		$password->setRequired(true)
			 ->setLabel("Password");
		$form->addElement($password);

		/*$rememberme = new Zend_Form_Element_Checkbox('rememberme');
		$rememberme->setRequired(false)
			   ->setLabel("Remember me for 2 weeks");
		$form->addElement($rememberme);*/

		UserForms::addSubmitToForm($form, "Login");

		return $form;
	}

	public static function createForm($options = null) 
	{
		$form = new Zend_Form($options);
		$form->setAction("/user/create")
		     ->setMethod("post");

		$name = new Zend_Form_Element_Text("name");
		$name->setRequired(true)
		     ->setLabel("Your Name");
		$form->addElement($name);

		$email = new Zend_Form_Element_Text("email");
		$email->setRequired(true)
		      ->setLabel("Your Email");
		$form->addElement($email);

		$password = new Zend_Form_Element_Password("password");
		$password->setRequired(true)
			 ->setLabel("Your Password");
		$form->addElement($password);

		$institution = new Zend_Form_Element_Text("institution");
		$institution->setRequired(false)
					->setLabel("Institution");
		$form->addElement($institution);
		
		$role = new Zend_Form_Element_Text("role");
		$role->setRequired(false)
			 ->setLabel("Role");
		$form->addElement($role);

		UserForms::addSubmitToForm($form, "Create Account");

		return $form;
	}

	public static function profileForm($options = null)
	{
		$form = new Zend_Form($options);
		$form->setAction("/user/profile")
		     ->setMethod("post");

		$name = new Zend_Form_Element_Text("name");
		$name->setRequired(false)
		     ->setLabel("Your Name");
		$form->addElement($name);

		UserForms::addSubmitToForm($form, "Update Profile");

		return $form;
	}

	public static function addSubmitToForm(&$form, $label) {
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setLabel($label);
		$form->addElement($submit);
	}

}
?>
