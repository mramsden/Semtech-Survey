<?php
class Admin_Model_Revision extends Zend_Db_Table_Row
{
	
	/**
	 * This function ensures that the databases referential integrity is
	 * maintained. When the revision is deleted the associated tags should
	 * also be deleted. This stops orphaned tag references from appearing.
	 * 
	 * (non-PHPdoc)
	 * @see library/Zend/Db/Table/Row/Zend_Db_Table_Row_Abstract#_delete()
	 */
	protected function _delete()
	{
		$ttt = new Semtech_Model_DbTable_TechnologyTags();
		$ttt->delete($ttt->getAdapter()->quoteInto("revision = ?", $this->id));
		
		$tat = new Semtech_Model_DbTable_TechnologyAnnotation();
		$tat->delete($tat->getAdapter()->quoteInto("revision = ?", $this->id));
		
		$tut = new Semtech_Model_DbTable_TechnologyUsage();
		$tut->delete($tut->getAdapter()->quoteInto("revision = ?", $this->id));
	}
	
}