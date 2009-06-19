<?php
class Semtech_RelationController extends Semtech_Controller_Action
{
	
	public function viewAction()
	{
		$technology = Semtech_Model_Technology::getTechnology($this->getTechId());
		
		$this->view->title = "References and Relations: ".$technology->name;
		$this->view->technology = $technology;
	}
	
	public function relationAction()
	{
		if (!Zend_Auth::getInstance()->hasIdentity())
			throw new Semtech_Exception_Forbidden();
		
		$techid = $this->getTechId();
			
		if (is_null($techid)) 
		{
			$this->_flashMessenger->addMessage("Unable to create relation since the requested technology was not found.");
			$this->_redirect("/technology");
		}
		
		$technology = Semtech_Model_Technology::getTechnology($techid);	

		if (is_null($technology))
		{
			$this->_flashMessenger->addMessage("Unable to create relation since the requested technology was not found.");
			$this->_redirect("/technology");
		}
		
		$technology = Semtech_Model_Technology::getTechnology($techid);
		
		$form = new Semtech_Form_Relation_Relation($techid);
		
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getPost()))
			{
				Semtech_Model_Relation::newRelation($technology, Semtech_Model_Technology::getTechnology($form->getValue('relatedtechnology')), Semtech_Model_User::getLoggedInUser(), $form->getValue('relationtype'), $form->getValue('description'));
				$this->_redirect("/technology/relations/{$technology->id}");
			}
		}
		
		$this->view->form = $form;
		$this->view->title = "Add Relation: {$technology->name}";
	}
	
	public function referenceAction()
	{
		if (!Zend_Auth::getInstance()->hasIdentity())
			throw new Semtech_Exception_Forbidden();
		
		$techid = $this->getTechId();
		
		$technology = Semtech_Model_Technology::getTechnology($techid);
		
		$form = new Semtech_Form_Relation_Reference($techid);
		
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getPost()))
			{
				Semtech_Model_Reference::newReference($techid, Semtech_Model_User::getLoggedInUser(), $form->getValue('reference'), $form->getValue('url'));
				$this->_redirect("/technology/relations/{$technology->id}");
			}
		}
		
		$this->view->form = $form;
		$this->view->title = "Add Reference: {$technology->name}";
	}
	
	private function getTechId()
	{
		if ($this->getRequest()->isPost())
		{
			return $this->getRequest()->getPost("techid");
		}
		
		return $this->getRequest()->getParam("techid");
	}
	
}
?>