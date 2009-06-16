<?php
class Semtech_RevisionController extends Zend_Controller_Action
{
	
	/**
	 * FlashMessenger
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	private $_flashMessenger = null;
	
	/**
	 * Redirector
	 * @var Zend_Controller_Action_Helper_Redirector
	 */
	private $_redirector = null;
	
	public function init()
	{
		if (is_null(User::getLoggedInUser()))
			throw new Semtech_Exception_Forbidden("No user is logged in.");
			
		$user = User::getLoggedInUser();
		
		if (!$user->admin)
			throw new Semtech_Exception_Forbidden("The user does not have admin privileges.");
		
		$this->_flashMessenger = $this->_helper->getHelper("FlashMessenger");
		$this->_redirector = $this->_helper->getHelper("Redirector");
		
		if ($this->_flashMessenger->hasMessages())
		{
			$this->view->messages = $this->_flashMessenger->getMessages();
			$this->_flashMessenger->getMessages();
		}
	}
	
	public function listAction()
	{
		$technology = $this->_request->getParam("technology", null);
		
		if (!is_null($technology))
		{
			$this->view->technology = Technology::getTechnology($technology);
			$this->view->revisions = $this->view->technology->getRevisions();
		}
		
		$technologiestable = new TechnologyTable();
		$this->view->technologies = $technologiestable->fetchAll($technologiestable->select()->order("name"));
		$this->view->title = "Manage Revisions";
	}
	
	public function deleteAction()
	{
		$revid = $this->_request->getParam("id", null);
		
		if (!is_null($revid))
		{
			$revision = Revision::getRevision($revid);
			if ($revision->original != 1)
			{
				$revision->delete();
				$this->_flashMessenger->addMessage("Successfully deleted revision by {$revision->getAuthor()->name} for {$revision->getTechnology()->name}.");
			}
			else
			{
				$this->_flashMessenger->addMessage("Unable to delete revision by {$revision->getAuthor()->name} for {$revision->getTechnology()->name} because it is the original revision.");
			}
		}
		else
		{
			$this->_flashMessenger->addMessage("Unable to delete revision because no revision specified.");
		}
		
		$this->_redirector->gotoSimple("list");
	}
}
?>