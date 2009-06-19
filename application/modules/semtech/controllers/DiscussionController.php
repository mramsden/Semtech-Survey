<?php
class Semtech_DiscussionController extends Semtech_Controller_Action
{
	
	public function viewAction()
	{	
		$this->view->technology = Semtech_Model_Technology::getTechnology($this->getTechId());
		$this->view->comments = Semtech_Model_Comment::fetchComments($this->getTechId());
		$this->view->title = "Technology Discussion: {$this->view->technology->name}";
		if (Zend_Auth::getInstance()->hasIdentity())
			$this->view->form = new Semtech_Form_Discussion_Add($this->techid, Zend_Auth::getInstance()->getStorage()->read());
	}
	
	public function listAction()
	{
		if ($this->request->isXmlHttpRequest())
		{
			$lastcomment = $this->request->getParam('lastcomment', null);
			$serviceargs = array('techid' => $this->request->getParam('techid'));
			if (!is_null($lastcomment) && $lastcomment > 0)
				array_push($serviceargs, array('lastcomment' => $lastcomment));
			
			$discussionservice = new Semtech_Service('Semtech_Service_Discussion_Service', 'getComments', $serviceargs);
			$discussionservice->handle();
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
		}
	}
	
	public function addAction()
	{
		if ($this->request->isPost() && $this->request->isXmlHttpRequest())
		{
			$args = array(
				'techid' => $this->request->getPost("techid"),
				'userid' => $this->request->getPost("userid"),
				'comment' => $this->request->getPost("comment"),
				'replyto' => $this->request->getPost("replyto")
			);
			$discussionservice = new Semtech_Service('Semtech_Service_Discussion_Service', 'addComment', $args);
			$discussionservice->handle();
			$this->_helper->viewRenderer->setNoRender(true);
			$this->_helper->layout->disableLayout();
		}
		else
		{
			throw new Semtech_Exception_NotAjax();
		}
	}
	
	private function getTechId()
	{
		if ($this->request->isPost())
		{
			return $this->getRequest()->getPost('techid');
		}
		else
		{
			return $this->getRequest()->getParam('techid');
		}
	}
	
}
