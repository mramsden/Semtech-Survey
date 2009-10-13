<?php
class Semtech_Process
{
  
  private $_name;
  private $_execString;
  private $_scriptHomePath;
  
  public function __construct($name, $scriptHomePath)
  {
    $this->_name = $name;
    $this->_scriptHomePath = $scriptHomePath;
    $this->_execString = $this->_scriptHomePath."/".$this->_name;
  }
  
  public function getName()
  {
    return $this->_name;
  }
  
  public function start()
  {
    if (!$this->isRunning())
    {
      shell_exec($this->_execString);
    }
  }
  
  public function stop()
  {
    if ($this->isRunning())
    {
      shell_exec("kill ".$this->getPid());
    }
  }
  
  public function getPid()
  {
    $pid = null;
    
    $pid = file_get_contents(APPLICATION_PATH."/../var/processes/{$this->_name}/{$this->_name}.pid");
    
    return $pid;
  }
  
  public function isRunning()
  {
    return file_exists(APPLICATION_PATH."/../var/processes/{$this->_name}/{$this->_name}.pid");
  }
  
}