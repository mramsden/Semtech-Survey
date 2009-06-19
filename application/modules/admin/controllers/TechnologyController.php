<?php
class Admin_TechnologyController extends Semtech_Controller_Admin_Action
{
	
	public function indexAction()
	{
		$this->view->title = "Manage Technologies";
		
		$technologiestable = new Semtech_Model_DbTable_Technologies();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($technologiestable->select()->order('name')));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$paginator->setPageRange(5);
		
		$this->view->paginator = $paginator;
	}
	
	public function deleteAction()
	{
		
		if ($this->getRequest()->isPost())
		{
			
			if ($this->getRequest()->getParam("confirmation", "Cancel Action") == "Confirm Action")
			{
				$technology = Admin_Model_Technology::getTechnology($this->getRequest()->getParam("techid", null));
				
				if (!is_null($technology))
				{
					$technology->delete();
					$this->_flashMessenger->addMessage("Technology has been deleted.");
				}
				else
				{
					$this->_flashMessenger->addMessage("Unable to find technology for deletion.");
				}
				
			}
			else
			{
				$this->_flashMessenger->addMessage("Delete operation aborted.");
			}
			
			$this->getHelper("Redirector")->gotoSimple("index");
			
		}	
		else
		{
			
			$technology = Semtech_Model_Technology::getTechnology($this->getRequest()->getParam("techid", null));
			if (is_null($technology))
			{
				$this->_flashMessenger->addMessage("Unable to find the specified technology.");
				$this->getHelper("Redirector")->gotoSimple("index");
			}
			$this->view->technology = $technology;
			$this->view->title = "Delete ".$technology->name;
		}	
		
	}
	
}