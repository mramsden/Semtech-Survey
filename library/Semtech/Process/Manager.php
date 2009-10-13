<?php
class Semtech_Process_Manager
{
  
  private $_scriptHomePath;
  
  public function __construct()
  {
    $this->_scriptHomePath = APPLICATION_PATH."/../scripts";
  }
  
  /**
   * This function is very important. It protects our application from people
   * running arbitrary unix commands on the server. It will only return a
   * Semtech_Process object if the process script exists in the
   * $this->_scriptHomePath directory. If not then null is returned.
   *
   * @param string $ps 
   * @return Semtech_Process
   * @author Marcus Ramsden
   */
  public function getProcess($ps)
  {
    $process = null;
    
    $processDir = dir($this->_scriptHomePath);
    while (false !== ($entry = $processDir->read()))
    {
      if (substr($entry, 0, 1) != "." && $entry == $ps && is_executable($this->_scriptHomePath."/".$entry))
      {
        $process = new Semtech_Process($entry, $this->_scriptHomePath);
        break;
      }
    }
    
    $processDir->close();
    
    return $process;
  }
  
  public function getAvailableProcesses()
  {
    $availableProcesses = array();
    
    $processDir = dir($this->_scriptHomePath);
    while (false !== ($entry = $processDir->read()))
    {
      if (substr($entry, 0, 1) != "." && is_executable($this->_scriptHomePath."/".$entry))
      {
        $availableProcesses[] = new Semtech_Process($entry, $this->_scriptHomePath);
      }
    }
    
    $processDir->close();
    
    return $availableProcesses;
  }
  
}