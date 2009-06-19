<?php
class Admin_StatusController extends Zend_Controller_Action
{
  
  /**
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	private $_flashMessenger;
	
	public function init()
	{
		$this->_flashMessenger = $this->getHelper('FlashMessenger');
		
		$user = Semtech_Model_User::getUser(Zend_Auth::getInstance()->getIdentity());
		if ($user == null || !$user->isAdmin())
			throw new Semtech_Exception_Forbidden();
		
		if ($this->_flashMessenger->hasMessages()) {
			$this->view->messages = $this->_flashMessenger->getMessages();
			$this->_flashMessenger->clearMessages();
		}
	}
	
	public function preDispatch()
	{
		$this->view->headTitle("Admin Area");
	}
	
	public function postDispatch()
	{
		$this->view->headTitle($this->view->title);
	}
	
	public function indexAction()
	{
	  $this->view->title = "System Status";
	  $twitter = new Semtech_Twitter();
	  $this->view->remainingTwitterApiCalls = $twitter->getRemainingApiCalls();
	}
	
}