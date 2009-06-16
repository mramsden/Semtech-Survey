<?php
class Semtech_Form_Relation_Reference extends Semtech_Form_Form
{
	
	public function __construct($techid, $options = null)
	{
		parent::__construct($options);
		
		$this->addElement($this->createHiddenElement('techid', $techid));
		
		$reference = new Zend_Form_Element_Textarea('reference');
		$reference->setLabel("Reference")
				  ->setRequired(true);
		$this->addElement($reference);
		
		$url = new Zend_Form_Element_Text('url');
		$url->setLabel("URL");
		$this->addElement($url);
		
		$this->addElement($this->createSubmitButton("Add Reference"));
	}
	
}