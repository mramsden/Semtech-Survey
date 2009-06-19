<?php
class Semtech_ReportController extends Zend_Controller_Action
{

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