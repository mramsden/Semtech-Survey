<?php
class Admin_ProcessController extends Semtech_Controller_Admin_Action
{
  
  public function startAction()
  {
    $ps = $this->getRequest()->getParam("ps");
    if ($this->_ps_valid($ps))
    {
      // Attempt to start the process.
      exec("nice -n 5 ".APPLICATION_PATH."/../scripts/$psÅ &");
      
      // Wait 20 seconds to see if the process starts.
      $timeout = time() + 20;
      $started = false;
      
      while (!$started && (time() < $timeout))
      {
        if (file_exists(APPLICATION_PATH."/../var/processes/$ps"))
        {
          $started = true;
        }
      }
      
      if ($started)
      {
        $this->_flashMessenger->addMessage("'$ps' has been started.");
      }
      else
      {
        $this->_flashMessenger->addMessage("'$ps' failed to start.");
      }
    }
    else
    {
      $this->_flashMessenger->addMessage("'$ps' is not a valid process.");
    }
    
    $this->getHelper("Redirector")->gotoUrl($_SERVER['HTTP_REFERER']);
  }
  
  public function stopAction()
  {
    $ps = $this->getRequest()->getParam("ps");
    
    if ($this->_ps_valid($ps))
    {
      
      unlink(APPLICATION_PATH."/../var/processes/$ps");
      
      $timeout = time() + 20;
      $stopped = false;
      
      while (!$stopped && (time() < $timeout))
      {
        if (!file_exists(APPLICATION_PATH."/../var/processes/$ps"))
        {
          $stopped = true;
        }
      }
      
      if ($stopped)
      {
        $this->_flashMessenger->addMessage("'$ps' has been stopped.");
      }
      else
      {
        $this->_flashMessenger->addMessage("'$ps' failed to stop.");
      }
    }
    else
    {
      $this->_flashMessenger->addMessage("'$ps' is not a valid process.");
    }
    
    $this->getHelper("Redirector")->gotoUrl($_SERVER['HTTP_REFERER']);
  }
  
  public function logAction()
  {
    $ps = $this->getRequest()->getParam("ps");
    if ($this->_ps_valid($ps))
    {
      $this->view->lines = $this->getRequest()->getParam("lines", 20);
      $this->view->title = $ps;
      $this->view->log = $this->_tail(APPLICATION_PATH."/../var/logs/$ps.log", $this->view->lines);
    }
    else
    {
      $this->getHelper("Redirector")->gotoUrl($_SERVER['HTTP_REFERER']);
    }
  }
  
  private function _ps_valid($ps)
  {
    $valid = false;
    
    if (!is_null($ps))
    {
        $valid = is_file(APPLICATION_PATH."/../scripts/$ps");
    }
    
    return $valid;
  }
  
  private function _tail($file, $lines)
  {
    $handle = fopen($file, "r");
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    while ($linecounter > 0)
    {
      $t = "  ";
      while ($t != "\n")
      {
        if (fseek($handle, $pos, SEEK_END) == -1) 
        {
          $beginning = true;
          break;
        }
        $t = fgetc($handle);
        $pos--;
      }
      $linecounter--;
      if ($beginning)
      {
        rewind($handle);
      }
      $text[$lines - $linecounter-1] = fgets($handle);
      if ($beginning) break;
    }
    
    fclose($handle);
    return $text;
  }
  
}