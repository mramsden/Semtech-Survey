<?php
class Semtech_Form_Relation_Relation extends Semtech_Form_Form
{
	
	public function __construct($techid, $options = null)
	{
		$technology = Semtech_Model_Technology::getTechnology($techid);
		
		$srdt = new Semtech_Model_DbTable_ServiceRelationDefinitions();
		$relations = $srdt->fetchAll();
		
		$tt = new Semtech_Model_DbTable_Technologies();
		$select = $tt->select()->where("NOT id = ?", $technology->id);
		$technologies = $tt->fetchAll($select);
		
		parent::__construct($options);
		
		$this->setDecorators($this->_tableFormDecorator);
		
		$this->addElement($this->createHiddenElement("techid", $techid));
		
		$relationtype = new Zend_Form_Element_Select('relationtype');
		$relationtype->setLabel("Relation Type")
					 ->setDecorators($this->_tableFormRowDecorator);
					
		foreach ($relations as $relation)
		{
			$relationtype->addMultiOption($relation->name, $relation->text);
		}
		$this->addElement($relationtype);
		
		$relatedtechnology = new Zend_Form_Element_Select('relatedtechnology');
		$relatedtechnology->setLabel("Related Technology")
						  ->setDecorators($this->_tableFormRowDecorator);
		foreach ($technologies as $tech)
		{
			$relatedtechnology->addMultiOption($tech->id, $tech->name);
		}
		$this->addElement($relatedtechnology);
		
		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel("Description")
					->setDecorators($this->_tableFormRowDecorator);
		$this->addElement($description);
		
		$submit = $this->createSubmitButton("Update Tags");
		$submit->setDecorators($this->_tableFormSubmit);
		$this->addElement($submit);
	}
	
}