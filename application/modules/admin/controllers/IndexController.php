<?php
class Admin_IndexController extends Zend_Controller_Action
{
	
	public function preDispatch()
	{
		$this->view->headTitle("Admin Area");
	}
	
	public function indexAction()
	{
		$this->view->title = "Admin Area";
	}
	
}