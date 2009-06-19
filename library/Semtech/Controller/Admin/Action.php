<?php
class Semtech_Controller_Admin_Action extends Semtech_Controller_Action
{
  
  public function preDispatch()
	{
	  parent::preDispatch();
	  $this->registerCallback("checkAdminUserLoggedIn");
		$this->view->headTitle("Admin Area");
	}
	
	public function postDispatch()
	{
		$this->view->headTitle($this->view->title);
	}
  
  /**
   * Checks if an admin user is logged in. This should be run for 
   * every admin action. 
   *
   * @return void
   * @author Marcus Ramsden
   */
  public function checkAdminUserLoggedIn()
  {
    $user = Semtech_Model_User::getLoggedInUser();
		if ($user == null)
		{
		  $this->getHelper("ReturnToTarget")->setReturnToTarget($this->getRequest()->getRequestUri());
		  $this->getHelper("Redirector")->gotoSimple("login", "user", "semtech");
		}
		if (!$user->isAdmin())
		{
		  throw new Semtech_Exception_Forbidden("You must be a site administrator to access this area.");
		}
  }
}