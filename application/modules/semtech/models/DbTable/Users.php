<?php
class Semtech_Model_DbTable_Users extends Zend_Db_Table
{
	protected $_name = 'users';
	protected $_rowClass = 'Semtech_Model_User';
	
	/**
	 * Fetch all user rows from the database.
	 * 
	 * @return Zend_Db_Table_Rowset
	 */
	public function getAllUsers()
	{
		$users = $this->fetchAll();
		
		return $users;
	}
}
