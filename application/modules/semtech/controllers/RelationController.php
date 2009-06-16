<?php
class Semtech_RelationController extends Semtech_Controller_Action_Parameters
{
	/**
	 * @var Zend_Action_Helper_Flash
	 */
	private $flashMessenger;
	
	public function init()
	{	
		$this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->messages = $this->flashMessenger->getMessages();
		$this->flashMessenger->clearMessages();
		
		$this->request = $this->getRequest();
		
		$this->techid = $this->_getTechId();
	}
	
	public function viewAction()
	{
		$technology = Semtech_Model_Technology::getTechnology($this->_getTechId());
		
		$this->view->title = "References and Relations: ".$technology->name;
		$this->view->technology = $technology;
	}
	
	public function relationAction($techid = null)
	{
		if (!Zend_Auth::getInstance()->hasIdentity())
			throw new Semtech_Exception_Forbidden();
			
		if (is_null($techid)) 
		{
			$this->flashMessenger->addMessage("Unable to create relation since the requested technology was not found.");
			$this->_redirect("/technology");
		}
		
		$technology = Semtech_Model_Technology::getTechnology($techid);	

		if (is_null($technology))
		{
			$this->flashMessenger->addMessage("Unable to create relation since the requested technology was not found.");
			$this->_redirect("/technology");
		}
		
		$technology = Semtech_Model_Technology::getTechnology($techid);
		
		$form = new Semtech_Form_Relation_Relation($techid);
		
		if ($this->request->isPost())
		{
			if ($form->isValid($this->request->getPost()))
			{
				Semtech_Model_Relation::newRelation($technology, Semtech_Model_Technology::getTechnology($form->getValue('relatedtechnology')), Semtech_Model_User::getLoggedInUser(), $form->getValue('relationtype'), $form->getValue('description'));
				$this->_redirect("/technology/relations/{$technology->id}");
			}
		}
		
		$this->view->form = $form;
		$this->view->title = "Add Relation: {$technology->name}";
	}
	
	public function referenceAction($techid = null)
	{
		if (!Zend_Auth::getInstance()->hasIdentity())
			throw new Semtech_Exception_Forbidden();
		
		$technology = Semtech_Model_Technology::getTechnology($techid);
		
		$form = new Semtech_Form_Relation_Reference($techid);
		
		if ($this->request->isPost())
		{
			if ($form->isValid($this->request->getPost()))
			{
				Semtech_Model_Reference::newReference($techid, Semtech_Model_User::getLoggedInUser(), $form->getValue('reference'), $form->getValue('url'));
				$this->_redirect("/technology/relations/{$technology->id}");
			}
		}
		
		$this->view->form = $form;
		$this->view->title = "Add Reference: {$technology->name}";
	}
	
	private function _getTechId()
	{
		if ($this->request->isPost())
		{
			return $this->request->getPost("techid");
		}
		
		return $this->request->getParam("techid");
	}
	
}
?>