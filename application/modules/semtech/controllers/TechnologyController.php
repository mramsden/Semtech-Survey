<?php
class Semtech_TechnologyController extends Semtech_Controller_Action
{
	
	public function indexAction()
	{
		$this->view->title = "SemTech Catalogue";
		
		$technologytable = new Semtech_Model_DbTable_Technologies();
		
		// Create a paginator for the list.
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($technologytable->select()->order('name')));
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));
		$paginator->setPageRange(5);		
		
		$this->view->paginator = $paginator;
	}
	
	public function viewAction()
	{	
		$techid = $this->_getTechId();
		if ($techid == "") 
		{
			$this->_flashMessenger->addMessage("Unable to find technology.");
			$this->getHelper("Redirector")->gotoSimple("index");
		}
		
		$technology = Semtech_Model_Technology::getTechnology($techid);
		if (!$technology)
		{
			$this->_flashMessenger->addMessage("Unable to find technology.");
			$this->getHelper("Redirector")->gotoSimple("index");
		}
		
		if ($this->getRequest()->getParam('detail') != "")
		{
			$detailfield = $this->request->getParam("detail");
			$fields = $technology->toArray();
			$this->view->title = "Detail View: $technology";
			$this->view->data = $fields[$detailfield];
			$this->renderScript("technology/detail.phtml");
		}
		
		$revisions = Semtech_Model_Technology::getAllTagRevisions($techid);
		$this->view->revisions = array();
		foreach ($revisions as $revision)
		{	
			if ($revision->isOriginal())
				continue;
			
			$this->view->revisions[$revision->id] = "{$revision->getTechnology()->name} ({$revision->getAuthor()->name})";
		}
		
		$this->view->technology = $technology;
		$this->view->revision = $technology->getDefaultRevision();
		$revid = $this->getRequest()->getParam("revid", null);
		if ($revid) {
			$this->view->revision = Semtech_Model_Revision::getRevision($revid);
		}
		
		$this->view->tagsleft = array();
		$this->view->tagsright = array();
		
		$tct = new Semtech_Model_DbTable_TagCategories();
		foreach ($tct->fetchAll() as $tagcategory)
		{
			if ($tagcategory->supercategory == Semtech_Model_TagCategory::SUPERCATEGORY_EDUCATIONAL_CONTEXT)
				$this->view->tagsleft[] = $tagcategory;
			else if ($tagcategory->supercategory == Semtech_Model_TagCategory::SUPERCATEGORY_SEMANTIC_TECHNOLOGIES)
				$this->view->tagsright[] = $tagcategory;
		}
		
		if (count($this->view->tagsleft))
			$this->view->tagslefttitle = Semtech_Model_TagCategory::SUPERCATEGORY_EDUCATIONAL_CONTEXT;
		if (count($this->view->tagsright))
			$this->view->tagsrighttitle = Semtech_Model_TagCategory::SUPERCATEGORY_SEMANTIC_TECHNOLOGIES;
	}
	
	public function newAction()
	{
		if (is_null(Semtech_Model_User::getLoggedInUser()))
			throw new Semtech_Exception_Forbidden();
			
		$this->view->title = "Add Technology";
		
		$form = new Semtech_Form_Technology_Create();
		
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getPost()))
			{
				// Create the new technology and the original revision.
				$newTechnology = Semtech_Model_Technology::newTechnology($form->getValue('name'), $form->getValue('url'), $form->getValue('description'), $form->getValue('license'), $form->getValue('version'), $form->getValue('release_date'), $form->getValue('iprights'));
				
				$newRevision = Semtech_Model_Revision::newRevision($newTechnology, Semtech_Model_User::getLoggedInUser(), true);
				
				// Since the first revision we created is a locked version we need to make one the user can edit.
				// This one will be used as the default revision to display.
				$userRevision = Semtech_Model_Revision::newRevision($newTechnology, Semtech_Model_User::getLoggedInUser());
				
				// Announce the technology on twitter only if we are in the production environment.
				if (APPLICATION_ENV == "production") {
					$twitter = new Semtech_Twitter();
					$twitter->announceTechnology($technology);
				}
				
				// We don't need to upgrade the search indexer anymore since this is dealt with by a crontabbed
				// task.

				$this->_flashMessenger->addMessage("Successfully created new technology {$newTechnology->name}.");
				
				$this->getHelper("Redirector")->gotoRoute(array('techid' => $newTechnology->id), 'technology');
			}
		}
		
		$this->view->form = $form;
	}
	
	public function editAction()
	{
		if (is_null(Semtech_Model_User::getLoggedInUser()))
			throw new Semtech_Exception_Forbidden();
			
		$technologytable = new Semtech_Model_DbTable_Technologies();
		$technology = $technologytable->fetchRow($technologytable->select()->where("id = ?", $this->_getTechId()));
		
		$revision = $technology->getOriginalRevision();
		if (!$revision->isAuthor(Semtech_Model_User::getLoggedInUser()))
		{
			$this->_flashMessenger->addMessage("Only the original creator can edit a technology's details.");
			$this->getHelper("Redir")->gotoRoute(array('techid'=> $technology), 'technology');
		}
		
		$form = new Semtech_Form_Technology_Edit($this->_getTechId());
		
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getPost()))
			{
				$technology->name = $form->getValue('name');
				$technology->url = $form->getValue('url');
				$technology->description = $form->getValue('description');
				$technology->license = $form->getValue('license');
				$technology->version = $form->getValue('version');
				$technology->release_date = $form->getValue('release_date');
				$technology->iprights = $form->getValue('iprights');
				$technology->save();

				$this->_flashMessenger->addMessage("Successfully updated details for {$technology->name}.");
				$this->getHelper("Redirector")->gotoRoute(array('techid'=> $technology->id), 'technology');
			}
		}
		else
		{
			$form->setAction("/technology/edit");
			$form->submit->setLabel("Update");
			$form->populate($technology->toArray());
		}

		$this->view->title = "Edit Technology Details";
		$this->view->form = $form;
		$this->renderScript("technology/new.phtml");
	}
	
	public function tagsAction()
	{
		if (is_null(Semtech_Model_User::getLoggedInUser()))
			throw new Semtech_Exception_Forbidden();
		
		$techid = $this->_getTechId();
			
		if ($this->getRequest()->isPost())
		{
			$tagcatid = $this->getRequest()->getPost('tagcat');
			$form = new Semtech_Form_Technology_Tags($tagcatid, $techid);
			$form->populate($this->getRequest()->getPost());
			
			// First try and obtain a revision for this user and this technology. If there isn't one
			// then create a new one.
			$revision = Semtech_Model_Revision::getRevision(null, $techid, Semtech_Model_User::getLoggedInUser()->id);
			if (!$revision) 
			{
				$revision = Semtech_Model_Revision::newRevision(Semtech_Model_Technology::getTechnology($techid), Semtech_Model_User::getLoggedInUser());
				$this->_flashMessenger->addMessage("A new revision has been created for you. All tag edits you carry out will only apply to this revision.");
			}
					
			$revision->updateTags($form->processForm(), $tagcatid);
			
			if (isset($form->usage))
			{
				$revision->setTechnologyUsage($form->usage);
			}
			
			$this->_flashMessenger->addMessage("Successfully updated tags for your revision of ".Semtech_Model_Technology::getTechnology($techid).".");
			$this->getHelper("Redirector")->gotoRoute(array("techid" => $techid, "revid" => $revision->id), 'technologyrev');
		}
		else
		{
			$tagcatid = $this->getRequest()->getParam('tagcat');
			$form = new Semtech_Form_Technology_Tags($tagcatid, $techid);
		}
		
		$this->view->title = "Update Tags: ".Semtech_Model_Technology::getTechnology($techid);
		$this->view->form = $form;
	}
	
	public function activityAction()
	{
		if (is_null(Semtech_Model_User::getLoggedInUser()))
			throw new Semtech_Exception_Forbidden();
			
		$techid = $this->_getTechId();
		$revid = $this->_getRevId();
		
		$form = new Semtech_Form_Technology_Activity($techid, $revid);
		
		if ($this->getRequest()->isPost())
		{
			$form->populate($this->getRequest()->getPost());
			
			$revision = Semtech_Model_Revision::getRevision($revid, $techid, Semtech_Model_User::getLoggedInUser()->id);
			if (!$revision)
			{
				$revision = Semtech_Model_Revision::newRevision(Semtech_Model_Technology::getTechnology($techid), Semtech_Model_User::getLoggedInUser());
				$this->_flashMessenger->addMessage("A new revision has been created for you. All tag edits you carry out will only apply to this revision.");
			}
			
			$tat = new Semtech_Model_DbTable_TechnologyAnnotation();
			
			foreach ($form->processForm() as $resultarray)
			{
				$annotation = $tat->fetchRow($tat->select()->where("`revision` = ?", $revision->id)->where("`type` = ?", $resultarray['type'])->where("`group` = ?", $resultarray['group']));
				if (!$annotation)
					$annotation = $tat->createRow();
					
				$annotation->revision = $revision->id;
				$annotation->group = $resultarray['group'];
				$annotation->level = $resultarray['level'];
				$annotation->type = $resultarray['type'];
				$annotation->save();
			}
			
			$this->_flashMessenger->addMessage("Updated Annotation/Content Creation Activity");
			$this->getHelper("Redirector")->gotoRoute(array("techid" => $techid, "revid" => $revision->id), 'technologyrev');
		}
		
		$this->view->title = "Annotation/Content Creation Activity";
		$this->view->form = $form;
	}
	
	private function _getTechId()
	{
		if ($this->getRequest()->isPost())
		{
			return $this->getRequest()->getPost("techid");
		}
		
		return $this->getRequest()->getParam("techid");
	}
	
	private function _getRevId()
	{
		if ($this->getRequest()->isPost())
		{
			return $this->getRequest()->getPost("revid");
		}
		
		return $this->getRequest()->getParam("revid");
	}
	
}
