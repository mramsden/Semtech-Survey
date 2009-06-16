<?php
class Admin_Model_Technology extends Zend_Db_Table_Row
{

	/**
	 * This method will return the technology associated with the
	 * supplied id.
	 * 
	 * @param int $id
	 * @return Technology
	 */
	public static function getTechnology($id)
	{
		$tt = new Admin_Model_DbTable_Technologies();
		return $tt->fetchRow($tt->select()->where("id = ?", $id));
	}
	
	/**
	 * This method will return all revisions associated with this technology.
	 * @return Zend_Db_Table_Rowset
	 */
	public function getRevisions()
	{
		$trt = new Admin_Model_DbTable_TechnologyRevisions();
		$select = $trt->select()->where("technology = ?", $this->id)->order("createdon");
		return $trt->fetchAll($select);
	}
	
	/**
	 * This function deletes any comments and references associated with the
	 * technology. It will also delete all revisions associated with the
	 * technology.
	 */
	protected function _delete()
	{
		$tct = new Semtech_Model_DbTable_TechnologyComments();
		$tct->delete($tct->getAdapter()->quoteInto("technology = ?", $this->id));
		
		$trt = new Semtech_Model_DbTable_TechnologyReferences();
		$trt->delete($trt->getAdapter()->quoteInto("technology = ?", $this->id));	
		
		foreach ($this->getRevisions() as $revision) 
		{
			$revision->delete();	
		}
		
		$servicerelations = new Semtech_Model_DbTable_ServiceRelations();
		$where = $servicerelations->getAdapter()->quoteInto("object = ? OR subject = ?", $this->id, 2);
		$servicerelations->delete($where);
	}
	
}