<?php
class Semtech_Controller_Action extends Zend_Controller_Action
{
  
  /**
   * This is an array containing all of the callbacks that
   * need to be run during dispatch.
   *
   * @var array
   */
  private $_callbacks;
  
  /**
   * @var Zend_Controller_Action_Helper_FlashMessenger
   */
  protected $_flashMessenger;
  
  public function preDispatch()
  {
    parent::preDispatch();
    
    $this->_flashMessenger = $this->getHelper("FlashMessenger");
    
    // Register any callback functions.
    $this->registerCallback("getFlashMessages");
  }
  
  public function dispatch($action)
  {
    $this->_helper->notifyPreDispatch();
    $this->preDispatch();
    
    if ($this->getRequest()->isDispatched())
    {
      // Execute the callbacks to run for this action.
      $this->_runCallbacks();
      
      // Actually run the associated action function.
      call_user_func(array($this, $action));
      
      $this->postDispatch();
    }
    
    $this->_helper->notifyPostDispatch();
  }
  
  /**
   * This function will get any messages from the FlashMessenger
   * helper and put them into the Zend_View object as an array
   * assigned to the messages property.
   *
   * @return void
   * @author Marcus Ramsden
   */
  public function getFlashMessages()
  {
    if ($this->_flashMessenger->hasMessages()) {
			$this->view->messages = $this->_flashMessenger->getMessages();
			$this->_flashMessenger->clearMessages();
		}
  }
  
  /**
   * This function will run all callbacks registered in the
   * callback queue.
   *
   * @return void
   * @author Marcus Ramsden
   */
  private function _runCallbacks()
  {
    foreach ($this->_callbacks as $function)
    {
      call_user_func(array($this, $function));
    }
  }
  
  /**
   * Add a function to the callback queue.
   *
   * @param string $func
   * @return void
   * @author Marcus Ramsden
   */
  protected function registerCallback($func)
  {
    $this->_callbacks[] = $func;
  }
  
}