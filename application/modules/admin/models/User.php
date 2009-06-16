<?php
class Admin_Model_User extends Zend_Db_Table_Row
{
	
	public function getName()
	{
		return strlen($this->name) ? $this->name : $this->email;
	}
	
	/**
	 * This function will delete all of the comments and
	 * references created by a user. It will also delete
	 * all non-original references created by the user.
	 */
	protected function _delete()
	{
		$tct = new Semtech_Model_DbTable_TechnologyComments();
		$tct->delete($tct->getAdapter()->quoteInto("technology = ?", $this->id));
		
		$trt = new Semtech_Model_DbTable_TechnologyReferences();
		$trt->delete($trt->getAdapter()->quoteInto("technology = ?", $this->id));
		
		$technologyrevisions = new Admin_Model_DbTable_TechnologyRevisions();
		$where[] = $technologyrevisions->getAdapter()->quoteInto("createdby = ?", $this->id);
		$where[] = $technologyrevisions->getAdapter()->quoteInto("original != ?", 1);
		$technologyrevisions->delete($where);
	}
	
}