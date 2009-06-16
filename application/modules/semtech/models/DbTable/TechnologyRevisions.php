<?php
class Semtech_Model_DbTable_TechnologyRevisions extends Zend_Db_Table
{
	
	const REVISION_TIMESTAMP = "createdon";
	
	protected $_name = 'technologyrevisions';
	protected $_rowClass = 'Semtech_Model_Revision';
	
	/**
	 * This function will return all of the original revisions. You may
	 * supply the ordering of the results based on a column and limit
	 * the number of rows returned.
	 *
	 * @param <string> $order
	 * @param <int> $limit
	 * @return <Zend_Db_Table_Rowset>
	 */
	public static function getOriginals($order = null, $limit = null)
	{
		$trt = new Semtech_Model_DbTable_TechnologyRevisions();
		
		$select = $trt->select()->where("technology = original");
		
		if ($order)
			$select->order($order);
			
		if ($limit)
			$select->limit($limit);
		
		$result = $trt->fetchAll($select);
		$techids = array();
		foreach ($result as $row)
		{
			array_push($techids, $row->technology);
		}
		
		$techtable = new TechnologyTable();
		$originals = array();
		foreach ($techids as $techid)
		{
			array_push($originals, $techtable->fetchRow($techtable->select()->where("id = ?", $techid)));
		}
		
		return $originals;
		
	}
	
	public static function getRevision($technologyid)
	{
		$trt = new Model_DbTable_TechnologyRevisions();
		
		$select = $trt->select()->where("technology = ?", $technologyid);
		
		return $trt->fetchRow($select);
	}
	
}
?>