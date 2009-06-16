<?php
class Semtech_Model_Relation extends Zend_Db_Table_Row
{
	
	public static function newRelation($object, $subject, $user, $relation, $description = "")
	{	
		$srt = new Semtech_Model_DbTable_ServiceRelations();
		$newRelation = $srt->createRow();
		$newRelation->object = $object->id;
		$newRelation->subject = $subject->id;
		$newRelation->relation = $relation;
		$newRelation->description = $description;
		$newRelation->addedby = $user->id;
		$newRelation->save();
		
		$srtd = new Semtech_Model_DbTable_ServiceRelationDefinitions();
		$relationdefinition = $srtd->fetchRow($srtd->select()->where("name = ?", $relation));
		
		$inverseRelation = $srt->createRow();
		$inverseRelation->object = $subject->id;
		$inverseRelation->subject = $object->id;
		$inverseRelation->description = $description;
		$inverseRelation->addedby = $user->id;
		
		if ($relationdefinition->direction != "a")
		{
			$inversedefinition = $srtd->fetchRow($srtd->select()->where("type = ?", $relationdefinition->type)->where("NOT name = ?", $relationdefinition->name));
			$inverseRelation->relation = $inversedefinition->name;
		}
		else
		{
			$inverseRelation->relation = $relation;
		}
		
		$inverseRelation->save();
		
		
		$newRelationComment = "<i>{$user->name} created a new relation between {$object->name} and {$subject->name} ({$relationdefinition->text}).</i>";
		Semtech_Model_Comment::newComment($newRelationComment, $user->id, $object->id);
		
		return $newRelation;
	}
	
}