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
   * Remembers the supplied url as a point to return to.
   *
   * @param string $target
   * @return void
   * @author Marcus Ramsden
   */
  public function setReturnToTarget($target)
  {
    $this->_returnToSessionNamespace->target = $target;
  }
  
  /**
   * Returns and clears the last set target.
   *
   * @return string
   * @author Marcus Ramsden
   */
  public function getReturnToTarget()
  {
    $returnToTarget = $this->_returnToSessionNamespace->target;
    $this->clearReturnToTarget();
    return $returnToTarget;
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
    return !is_null($this->_returnToSessionNamespace->target);
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