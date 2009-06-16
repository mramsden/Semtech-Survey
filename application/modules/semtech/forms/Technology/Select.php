<?php
class Semtech_Form_Technology_Select extends Semtech_Form_Form
{
	public function __construct($technologies, $technologyid = null, $options = array())
	{
		parent::__construct($options);
		
		$this->setAction("/revision/list");
		
		$technology = new Zend_Form_Element_Select('technology');
		$technology->addMultiOption("", "");
		foreach ($technologies as $tech)
		{
			$technology->addMultiOption($tech->id, $tech->name);
		}
		if (!is_null($technologyid))
		{
			$technology->setValue($technologyid);
		}
		$this->addElement($technology);
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel("Select Technology");
		$this->addElement($submit);
		
		return $this;
	}
}
