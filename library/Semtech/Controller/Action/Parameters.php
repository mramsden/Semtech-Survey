<?php
class Semtech_Controller_Action_Parameters extends Zend_Controller_Action
{
	public function dispatch($action)
	{
		$this->_helper->notifyPreDispatch();
		$this->preDispatch();
		
		if ($this->getRequest()->isDispatched())
		{
			$params =  $this->_getAllParams();
			$method_params_array = $this->get_action_params($action);
			
			$data = array();
			
			foreach ($method_params_array as $param) {
				$name = $param->getName();
				if ($param->isOptional())
				{
					$data[$name] = !empty($params[$name])? $params[$name] : $param->getDefaultValue();	
				}
				else if (empty($params[$name]))
				{
					throw new Exception("Parameter '$name' cannot be empty");
				}
				else
				{
					$data[$name] = $params[$name];
				}
			}
			call_user_func_array(array($this, $action), $data);
		
			$this->postDispatch();	
		}
		
		$this->_helper->notifyPostDispatch();
		
	}
	
	private function get_action_params($action)
	{
		$classRef = new ReflectionObject($this);
		$className = $classRef->getName();
		$funcRef = new ReflectionMethod($className, $action);
		$paramsRef = $funcRef->getParameters();
		return $paramsRef;
	}
}
