<?php
/**
 * This class extends the Zend_Db_Table_Row.
 * It currently does not add any additional functionality
 * directly to the row object but it has some convenience
 * functions to help with user based operations.
 * 
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @copyright 2009 University of Southampton
 */
class Semtech_Model_User extends Zend_Db_Table_Row
{
	
	/**
	 * This method will get the user corresponding to the
	 * supplied id. Null is returned if no such user exists.
	 * 
	 * @param int $userid
	 * @return Semtech_Model_User
	 */
	public static function getUser($userid)
	{
		$ut = new Semtech_Model_DbTable_Users();
		return $ut->fetchRow($ut->select()->where("id = ?", $userid));
	}
	
	/**
	 * This method will return the currently logged in user
	 * or null if there is no user currently logged in to
	 * the session. As a security measure if the session exists
	 * with an invalid id the authentication is expired immediately
	 * since this should never be the case.
	 * 
	 * @return Semtech_Model_User
	 */
	public static function getLoggedInUser()
	{
		$user = null;
		
		if (Zend_Auth::getInstance()->hasIdentity())
		{
			$user = self::getUser(Zend_Auth::getInstance()->getIdentity());
			if (is_null($user))
			{
				Zend_Auth::getInstance()->clearIdentity();
			}
		}
		
		return $user;
	}
	
	public function getName()
	{
		return strlen($this->name) ? $this->name : $this->email;
	}
	
	/**
	 * Returns true if the user object represents an admin user. False if not.
	 *
	 * @return boolean
	 */
	public function isAdmin()
	{
		return $this->admin == 1;
	}
	
}
