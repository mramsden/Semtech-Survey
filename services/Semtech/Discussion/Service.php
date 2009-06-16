<?php
class Semtech_Discussion_Service extends Zend_Json
{
	
	public function addComment($techid, $userid, $comment, $replyto)
	{
		$comment = Comment::newComment($comment, $userid, $techid, $replyto);
		$user = User::getUser($userid);
		return array('id' => $comment->id, 'user' => $user->name, 'message' => $comment->comment, 'postedon' => $comment->postedon, 'replyto' => $comment->replyto);
	}
	
	public function getComments($techid, $lastcomment = 0)
	{
		$comments = Comment::fetchComments($techid);
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
			
			$user = User::getUser($comment->postedby);
			array_push($result, array('id' => $comment->id, 'user' => $user->name, 'message' => $comment->comment, 'postedon' => $comment->postedon, 'replyto' => $comment->replyto));
		}
		
		return array_reverse($result);
	}
	
}