<?php
class Admin_TwitterController extends Semtech_Controller_Admin_Action
{
  
  public function clearStatusAction()
  {
    $twitter = new Semtech_Twitter();
    if ($twitter->flushStatusMessages())
    {
      $this->_flashMessenger->addMessage("Twitter Module message cache cleared.");
    }
    else
    {
      Zend_Registry::get("log")->crit(__CLASS__.": Failed to clear Twitter message cache.");
      $this->_flashMessenger->addMessage("Failed to clear Twitter Module message cache.");
    }
    
    $this->getHelper("Redirector")->gotoUrl($_SERVER['HTTP_REFERER']);
  }
  
}