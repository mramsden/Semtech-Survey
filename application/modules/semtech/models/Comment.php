<?php
/**
 * This class represents a  single comment associated with a particular technology.
 *
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @package models
 */
class Semtech_Model_Comment extends Zend_Db_Table_Row
{

	/**
	 * This function will create a new comment associated with the supplied
	 * techid. The message parameter is the contents of the comment. It can 
	 * also be marked as a reply if you supply a commentid.
	 *
	 * @param string $message
	 * @param int $postedby
	 * @param int $techid
	 * @return Comment
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public static function newComment($message, $postedby, $techid, $replyto = 0)
	{
		$tct = new Semtech_Model_DbTable_TechnologyComments();
		$newComment = $tct->createRow();
		$newComment->comment = $message;
		$newComment->postedby = $postedby;
		$newComment->technology = $techid;
		$newComment->replyto = $replyto;
		$newComment->save();
		
		return $newComment;
	}
	
	/**
	 * This function will return all comments in descending order of their
	 * posted date. If no parameters are specified then all the comments
	 * will be returned.
	 *
	 * techid - This will return only comments associated with this technology id.
	 * userid - This will return only comments associated with this user id.
	 *
	 * @param int $techid
	 * @param int $userid
	 * @return Zend_Db_Table_Rowset
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public static function fetchComments($techid = null, $userid = null)
	{
		$tct = new Semtech_Model_DbTable_TechnologyComments();
		$select = $tct->select();
		$select->order('postedon DESC');
		if ($techid)
			$select->where('technology = ?', $techid);
		if ($userid)
			$select->where('postedby = ?', $userid);
			
		return $tct->fetchAll($select);
	}

	/**
	 * This returns the user object associated with the user that originally
	 * posted the comment.
	 *
	 * @return User
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function getPoster() 
	{
		$users = new Semtech_Model_DbTable_Users();
		$user = $users->fetchRow($users->select()->where("id = ?", $this->postedby));
		return $user;
	}

}
