<?php
class Semtech_Controller_Helper_ReturnToTarget extends Zend_Controller_Action_Helper_Abstract
{
  
  /**
   * @var Zend_Session_Namespace
   */
  private $_returnToSessionNamespace;
  
  public function __construct()
  {
    if (!Zend_Session::isStarted())
    {
      Zend_Session::start();
    }
    
    $this->_returnToSessionNamespace = new Zend_Session_Namespace("returnToTarget");
    
    // We keep the namespace locked as it should only be
    // edited within this class.
    if ($this->_returnToSessionNamespace->isLocked())
    {
      $this->_returnToSessionNamespace->unlock();
    }
  }
  
  public function __destruct()
  {
    if (!$this->_returnToSessionNamespace->isLocked())
    {
      $this->_returnToSessionNamespace->lock();
    }
  }
  
  /**
   * If a return to target exists then this function returns true.
   * False is returned if nothing is set.
   *
   * @return boolean
   * @author Marcus Ramsden
   */
  public function returnToTargetExists()
  {
    return !is_null($this->_returnToSessionNamespace->module) ||
      !is_null($this->_returnToSessionNamespace->controller) ||
      !is_null($this->_returnToSessionNamespace->action);
  }
  
  /**
   * This function sets the url that you want this helper to return you to.
   *
   * @param string $action 
   * @param string $controller 
   * @param string $module 
   * @return void
   * @author Marcus Ramsden
   */
  public function setReturnToTarget($action, $controller = null, $module = null)
  {
    $this->_returnToSessionNamespace->action = $action;
    $this->_returnToSessionNamespace->controller = $controller;
    $this->_returnToSessionNamespace->module = $module;
  }
  
  /**
   * This function will return a url string that you want to be returned to
   * in the form /:module/:controller/:action. If you have only set the action
   * then only :action is returned. The return to target is then reset.
   *
   * @return void
   * @author Marcus Ramsden
   */
  public function getReturnToTargetUrl()
  {
    $url = "";
    
    if (!is_null($this->_returnToSessionNamespace->module))
    {
      if (strlen($url) == 0)
      {
        $url .= "/";
      }
      $url .= $this->_returnToSessionNamespace->module;
    }
    if (!is_null($this->_returnToSessionNamespace->controller))
    {
      if (strlen($url) == 0)
      {
        $url .= "/";
      }
      $url .= $this->_returnToSessionNamespace->controller."/";
    }
    if (!is_null($this->_returnToSessionNamespace->action))
    {
      $url .= $this->_returnToSessionNamespace->action;
    }
    $this->clearReturnToTarget();
    
    return $url;
  }
  
  /**
   * This function will return the return to target
   * to as an associative array of the form;
   *
   *  array(
   *    'module'      => :module,
   *    'controller'  => :controller,
   *    'action'      => :action
   *  )
   *
   * The return to target is then reset.
   *
   * @return array
   * @author Marcus Ramsden
   */ 
  public function getReturnToTargetArray()
  {
    $returnToArray = array(
      'module' => $this->_returnToSessionNamespace->module,
      'controller' => $this->_returnToSessionNamespace->controller,
      'action' => $this->_returnToSessionNamespace->action
    );
    $this->clearReturnToTarget();
    
    return $returnToArray;
  }
  
  /**
   * This function will wipe the namespace of any return to target.
   *
   * @return void
   * @author Marcus Ramsden
   */
  public function clearReturnToTarget()
  {
    $this->_returnToSessionNamespace->unsetAll();
  }
  
}