<?php
class Semtech_Model_RelationDefinition extends Zend_Db_Table_Row
{
	
	public static function getRelationDefinition($name)
	{
		$srdt = new Semtech_Model_DbTable_ServiceRelationDefinitions();
		return $srdt->fetchRow($srdt->select()->where("name = ?", $name));
	}
	
}