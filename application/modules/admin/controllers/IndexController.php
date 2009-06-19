<?php
class Admin_IndexController extends Semtech_Controller_Admin_Action
{
	
	public function preDispatch()
	{
	  parent::preDispatch();
	  
		$this->view->headTitle("Admin Area");
	}
	
	/**
	 * /admin/index/index OR /admin
	 *
	 * @return void
	 * @author Marcus Ramsden
	 */
	public function indexAction()
	{
		$this->view->title = "Admin Area";
	}
	
}