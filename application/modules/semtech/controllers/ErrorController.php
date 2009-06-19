<?php
class Semtech_ErrorController extends Semtech_Controller_Action
{

	public function errorAction()
	{
		$errors = $this->_getParam("error_handler");
		
		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$this->getResponse()->setRawHeader("HTTP/1.1 404 Not Found");
				$this->view->title = "Page Not Found";
				$this->view->message = "The requested page was not found.";
				break;
			default:
				if ($errors->exception instanceof Semtech_Exception_Forbidden) {
					$this->getResponse()->setRawHeader("HTTP/1.1 403 Forbidden");
					$this->view->title = "Forbidden";
					if ($errors->exception->getMessage() != "")
						$this->view->message = "You do not have permission to view that page.";
					else
						$this->view->message = "You must <a href=\"/user/login\">login</a> to see this page.";	
				} else if ($errors->exception instanceof Semtech_Exception_Ajax) {
					$this->getResponse()->setRawHeader("HTTP/1.1 503 Service Unavilable");
					$this->view->title = "Not Supported";
					$this->view->message = "You cannot view this page.";
				} else {
					$this->view->title = "Internal Server Error";
					$this->view->message = "An internal server error has occured.";
				}
				break;
		}
		
		if (APPLICATION_ENV == "development")
		{
			$this->view->error = $errors;
		}
		
		Zend_Registry::get("log")->crit("A system error has occurred ({$this->_helper->environment()}): ".$errors->exception->getMessage());
	}

}
?>
