<?php
class Admin_StatusController extends Semtech_Controller_Admin_Action
{
  
  /**
   * /admin/status/index OR /admin/status
   *
   * @return void
   * @author Marcus Ramsden
   */
	public function indexAction()
	{
	  $this->view->title = "System Status";
	  $processmanager = new Semtech_Process_Manager();
	  $this->view->processes = $processmanager->getAvailableProcesses();
	  $twitter = Zend_Registry::get("twitter");
	  $this->view->remainingTwitterApiCalls = $twitter->getRemainingApiCalls();
	}
	
}