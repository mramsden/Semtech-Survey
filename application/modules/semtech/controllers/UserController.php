<?php
class Semtech_UserController extends Zend_Controller_Action
{
	protected $_request, $_forms;

	public function init()
	{
		$this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->messages = $this->flashMessenger->getMessages();
		$this->flashMessenger->clearMessages();
	}

	public function indexAction()
	{
		$this->view->title = "User Account Area";	
	}

	public function loginAction()
	{
		$this->view->title = "Login";
		$form = new Semtech_Form_User_Login();

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->getRequest()->getPost())) {
				$users = new Semtech_Model_DbTable_Users();
				$authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter());
				$authAdapter->setTableName('users')
					    	->setIdentityColumn('email')
					    	->setCredentialColumn('password');
				$authAdapter->setIdentity($form->getValue('email'))
					    	->setCredential(md5($form->getValue('password')));
				if ($authAdapter->authenticate()->isValid()) {
					$auth = Zend_Auth::getInstance();
					$user = $authAdapter->getResultRowObject(null, 'password');
					$auth->getStorage()->write($user->id);
					
					if ($form->getValue('rememberme'))
					{
						Zend_Session::rememberMe(1209600);
					}
					
					$this->flashMessenger->addMessage("You are now logged in as ".($user->name != '' ? $user->name : '('.$user->email.')').".");
		
					$this->_redirect("/user");
				} else {
					$this->flashMessenger->addMessage("Credentials not recognised.");
					$this->_redirect("/user");
				}
			}
		}

		$this->view->form = $form;
	}

	public function logoutAction()
	{
		$this->flashMessenger->addMessage("You are now logged out.");
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::forgetMe();
		$this->_redirect('/');
	}
	
	public function createAction()
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$this->flashMessenger->addMessage("You are already logged in and do not need to create an additional account.");
			$this->getHelper('Redirector')->gotoSimple("index");
		}
		
		$this->view->title = "Create Account";
		$form = new Semtech_Form_User_Create();

		
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->getRequest()->getPost())) {
				$users = new Semtech_Model_DbTable_Users();
				$newUser = $users->createRow();
				$newUser->name = $form->getValue('name');
				$newUser->password = md5($form->getValue('password'));
				$newUser->email = $form->getValue('email');
				$newUser->created_at = date("Y-m-d H:i:s", time());
				$newUser->save();
				$this->flashMessenger->addMessage("Successfully created account for ".($newUser->name != '' ? $newUser->name : $newUser->email)."!");
				$this->_redirect("/user");
			}
		}
		
		$this->view->form = $form;
	}

	public function manageAction()
	{
		$this->view->title = "Manage Account";

		if (Zend_Auth::getInstance()->hasIdentity()) {
			$identity = Zend_Auth::getInstance()->getIdentity();
			$form = new Semtech_Form_User_Manage();
			$user = Semtech_Model_User::getUser($identity);
			if ($this->getRequest()->isPost()) {
				if ($form->isValid($this->getRequest()->getPost())) {
					$user->name = $form->getValue('name');
					$user->email = $form->getValue('email');
					if ($form->getValue('newpassword') != "")
						$user->password = md5($form->getValue('newpassword'));
					if ($form->getValue('institution') != "")
						$user->institution = $form->getValue('institution');
					if ($form->getValue('role') != "")
						$user->role = $form->getValue('role');
					$user->save();
					$this->_redirect("/user/logout");
				}
			}
			
			$form->populate($user->toArray());
			$this->view->form = $form;

		} else {
			throw new Semtech_Exception_Forbidden();	
		}
	}
}
?>
