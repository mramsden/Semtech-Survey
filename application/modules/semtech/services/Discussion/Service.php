<?php
class Semtech_Service_Discussion_Service extends Zend_Json
{
	
	/**
	 * @var Zend_Log
	 */
	private $_logger;
	
	public function __construct()
	{
		$this->_logger = Zend_Registry::get('log');
		$this->_logger->info("Recieved request to Semtech Discussion Service.");
	}
	
	public function addComment($techid, $userid, $comment, $replyto)
	{
		$this->_logger->info(__CLASS__." executing: ".__METHOD__);
		$comment = Semtech_Model_Comment::newComment($comment, $userid, $techid, $replyto);
		$user = Semtech_Model_User::getUser($userid);
		return array('id' => $comment->id, 'user' => $user->name, 'message' => $comment->comment, 'postedon' => $comment->postedon, 'replyto' => $comment->replyto);
	}
	
	public function getComments($techid, $lastcomment = 0)
	{
		$this->_logger->info(__CLASS__." executing: ".__METHOD__);
		$comments = Semtech_Model_Comment::fetchComments($techid);
		$result = array();
		foreach ($comments as $comment)
		{
			if ($lastcomment > 0)
			{
				if ($comment->id <= $lastcomment) 
					continue;
			}
			
			if ($comment->technology != $techid)
				continue;
			
			$user = Semtech_Model_User::getUser($comment->postedby);
			array_push($result, array('id' => $comment->id, 'user' => $user->name, 'message' => $comment->comment, 'postedon' => $comment->postedon, 'replyto' => $comment->replyto));
		}
		
		return array_reverse($result);
	}
	
}