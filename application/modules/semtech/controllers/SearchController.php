<?php
class Semtech_SearchController extends Semtech_Controller_Action
{
	public function init()
	{
		$this->request = $this->getRequest();
	}

	public function searchAction()
	{
	  $searchTerm = $this->getRequest()->getParam("searchTerm");
	  
		if ($searchTerm)
		{
			$registry = Zend_Registry::getInstance();

			$index = Zend_Search_Lucene::open($registry->searchindex);
			$query = Zend_Search_Lucene_Search_QueryParser::parse($searchTerm);
			foreach ($index->find($query) as $hit)
			{
				if (!isset($this->view->searchResults))
					$this->view->searchResults = array();
				$this->view->searchResults[] = Semtech_Model_Technology::getTechnology($hit->technologyid);
			}
			$this->view->searchTerm = $searchTerm;	
		}
		
		$this->view->title = "Search Results";
	}

	public function tagAction()
	{
	  $tag = $this->getRequest()->getParam("tagid");
	  
		if ($tag)
		{
			$this->view->tag = urldecode($tag);
			$tagtable = new Semtech_Model_DbTable_Tags();
			$tag = $tagtable->fetchRow($tagtable->select()->where("id = ?", $tag));
			$technologytagstable = new Semtech_Model_DbTable_TechnologyTags();
			$technologytags = $technologytagstable->fetchAll($technologytagstable->select()->where("tag = ?", $tag->id));
			$technologies = array();
			foreach ($technologytags as $technologytag)
			{
			  $revision = Semtech_Model_Revision::getRevision($technologytag->revision);
				if (!is_null($revision) && !$revision->isOriginal())
				{
					if (!isset($technologies[$technologytag->technology])) {
						$technologies[$technologytag->technology]['technology'] = Semtech_Model_Technology::getTechnology($technologytag->technology); 
						$technologies[$technologytag->technology]['count'] = 1;
					} else {
						$technologies[$technologytag->technology]['count']++;
					}
				}				
			} 				
			$this->view->technologies = $technologies;
		}

		$this->view->title = "Tag Search";
	}
}
