<?php
class Semtech_ReportController extends Zend_Controller_Action
{

	/**
	 * @var Zend_Controller_Action_Helper_Redirector
	 */
	private $_redirector = null;

	/**
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	private $_flashMessenger = null;

	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#init()
	 */
	public function init()
	{
		$this->_redirector = $this->_helper->getHelper("Redirector");
		$this->_flashMessenger = $this->_helper->getHelper("FlashMessenger");
		if ($this->_flashMessenger->hasMessages())
		{
			$this->view->messages = $this->_flashMessenger->getMessages();
			$this->_flashMessenger->clearMessages();
		}
	}

	/**
	 * Handles /report/index or /report.
	 */
	public function indexAction()
	{
		$this->view->title = "Survey Reports";
	}

	public function technologiesPdfAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		
		$this->getResponse()->setHeader("Content-type", "application/pdf");
	}
	
	public function technologiesHtmlAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout->disableLayout();
		
		$this->getResponse()->setHeader("Content-type", "text/html");
		$report = new Semtech_Service_Report_Technology_Html();
		echo $report->render();
	}

}