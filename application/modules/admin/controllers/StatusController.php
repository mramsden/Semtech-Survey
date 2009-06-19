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
	  $twitter = new Semtech_Twitter();
	  $this->view->remainingTwitterApiCalls = $twitter->getRemainingApiCalls();
	}
	
}