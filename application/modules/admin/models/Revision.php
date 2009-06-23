<?php
class Admin_Model_Revision extends Zend_Db_Table_Row
{
	
	/**
   * This method will fetch the requested revision.
   * 
   * @param int $revid
   * @param int $techid
   * @param int $userid
   * @param boolean $original
   * @return Semtech_Model_Revision
   */
  public static function getRevision($revid = null, $techid = null, $userid = null, $original = false)
  {
    $trt = new Semtech_Model_DbTable_TechnologyRevisions();
    $select = $trt->select()->where("original = ?", $original);
    if ($revid)
      $select->where("id = ?", $revid);
    if ($techid)
      $select->where("technology = ?", $techid);
    if ($userid)
      $select->where("createdby = ?", $userid);
    return $trt->fetchRow($select);
  }
	
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