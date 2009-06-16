<?php
class Admin_UserController extends Zend_Controller_Action
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
		$this->view->title = "Manage Users";
	
		$userstable = new Semtech_Model_DbTable_Users();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($userstable->select()->order('email')->where("NOT id = ?", Semtech_Model_User::getLoggedInUser()->id)));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$paginator->setPageRange(5);
		
		$this->view->paginator = $paginator;
	}
	
	public function adminAction()
	{
		$userid = $this->getRequest()->getParam("userid", null);
		if (is_null($userid)) 
		{
			$this->_flashMessenger->addMessage("Unable to modify admin privileges as no user was specified.");
			$this->getHelper("Redirector")->gotoSimple('index');
		}
		
		$user = Semtech_Model_User::getUser($userid);
		if (is_null($user))
		{
			$this->_flashMessenger->addMessage("Unable to modify admin privileges as the requested user was not found.");
			$this->getHelper("Redirector")->gotoSimple("index");
		}
		
		if ($user->isAdmin())
		{
			if ($userid == Zend_Auth::getInstance()->getIdentity())
			{
				$this->_flashMessenger->addMessage("You can't revoke your own administration privileges.");
				$this->getHelper("Redirector")->gotoSimple("index");	
			}
			$user->admin = 0;
			$this->_flashMessenger->addMessage("{$user->email} no longer has administration privileges.");
		}
		else
		{
			$user->admin = 1;
			$this->_flashMessenger->addMessage("{$user->email} now has administration privileges.");
		}
		
		$user->save();
		
		$this->getHelper("Redirector")->gotoSimple("index");
	}
	
	public function deleteAction()
	{
		
		$this->view->title = "Delete User";
		
		if ($this->getRequest()->getParam("userid") == Zend_Auth::getInstance()->getIdentity())
		{
			$this->_flashMessenger->addMessage("You can't delete yourself.");
			$this->getHelper("Redirector")->gotoSimple("index");
		}
		
		if ($this->getRequest()->isPost())
		{
			// If this request was posted then this is the second stage,
			// which means we should carry out the delete.
			$userstable = new Admin_Model_DbTable_Users();
			$user = $userstable->fetchRow($userstable->select()->where("id = ?", $this->getRequest()->getParam("userid")));
			
			if ($this->getRequest()->getParam("confirmation", "Cancel Action") == "Confirm Action" && !is_null($user))
			{				
				$technologiestable = new Semtech_Model_DbTable_Technologies();
				$technologies = $technologiestable->fetchAll();
				
				foreach ($technologies as $technology)
				{
					
					$revision = $technology->getOriginalRevision();
					
					if (!is_null($revision) && $revision->isAuthor($user) && !in_array($technology->id, $this->getRequest()->getParam("technologies", array())))
					{
						// If the user was the creator then we need to set
						// the created by field to be their name. Assuming
						// the technology is not being deleted.
						$revision->createdby = $user->getName();
						$revision->save();
					}
					
				}
				
				// Next delete all of the technologies that were selected for deletion.
				foreach ($this->getRequest()->getParam("technologies", array()) as $deletedTechId)
				{
					
					$technology = Admin_Model_Technology::getTechnology($deletedTechId);
					if (!is_null($technology))
					{
						$technology->delete();
					}
					
				}
				
				// Finally delete the user.
				$user->delete();
				
			}
			else
			{
				$this->_flashMessenger->addMessage("Delete operation cancelled. No data was modified.");
			}
			
			$this->getHelper("Redirector")->gotoSimple("index");
			
		}
		else
		{
			$user = $this->_getUser();
			
			$technologiestable = new Semtech_Model_DbTable_Technologies();
			$technologies = $technologiestable->fetchAll();
			$this->view->technologies = array();
			
			foreach ($technologies as $technology)
			{
				$revision = $technology->getOriginalRevision();
				if (!is_null($revision) && $revision->isAuthor($user))
				{
					array_push($this->view->technologies, $technology);
				}
			}
			
			$this->view->user = $user;
		}

	}
	
	/**
	 * Gets the user based on the userid parameter supplied in the request.
	 * 
	 * @param boolean $redirect If this is set to true then the method
	 * will redirect the request of the userid parameter not being set
	 * or the corresponding user not being found. By default it is true.
	 * @return Semtech_Model_User
	 */
	private function _getUser($redirect = true)
	{
		$userid = $this->getRequest()->getParam("userid", null);
		if (is_null($userid) && $redirect)
		{
			$this->_flashMessenger->addMessage("Unable to carry out operation as no user was specified.");
			// Redirect to indexAction
			$this->getHelper("Redirector")->gotoSimple("index");
		}
		
		$user = Semtech_Model_User::getUser($userid);
		if (is_null($user) && $redirect)
		{
			$this->_flashMessenger->addMessage("Unable to find the requested user.");
			// Redirect ot indexAction
			$this->getHelper("Redirector")->gotoSimple("index");
		}
		
		return $user;
	}
	
}