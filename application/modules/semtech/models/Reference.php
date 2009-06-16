<?php
class Semtech_Model_Reference extends Zend_Db_Table_Row
{
	
	public static function newReference($techid, $user, $reference, $url = "")
	{
		$rt = new Semtech_Model_DbTable_TechnologyReferences();
		$newReference = $rt->createRow();
		$newReference->technology = $techid;
		$newReference->user = $user->id;
		$newReference->reference = $reference;
		$newReference->url = $url;
		$newReference->save();
		
		$newReferenceComment = "<i>{$user->name} added a new reference to this technology. <a href=\"/relation/view/$techid\">Click here</a> to view it.</i>";
		Semtech_Model_Comment::newComment($newReferenceComment, $user->id, $techid);
		
		return $newReference;
	}
	
	public function __get($key)
	{
		$ret = parent::__get($key);
		
		switch($key)
		{
			case "url":
				if (!preg_match("/^.*:\/\/.*$/i", $ret))
					$ret = "http://".$ret;
		}
		
		return $ret;
	}
	
}
?>