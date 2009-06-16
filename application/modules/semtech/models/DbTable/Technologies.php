<?php
/**
 * This is the software representation of the technologies table in the database.
 *
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @copyright 2009 University of Southampton
 */
class Semtech_Model_DbTable_Technologies extends Zend_Db_Table
{

    protected $_name = 'technologies';
    protected $_rowClass = 'Semtech_Model_Technology';

    /**
     * Fetch all technology rows from the database.
     * 
     * @param boolean $deleted If set to true then the result will include
     * any technologies that have been flagged as deleted. By default this
     * is false.
     * @return Zend_Db_Table_Rowset
     */
    public function getAllTechnologies($deleted = false)
    {
    	$select = $this->select();
    	$select->where("deleted = ?", 0);
    	if (true == $deleted)
    	{
    		$select->where("deleted = ?", 1);
    	}
    	$technologies = $this->fetchAll($select);
    	
    	return $technologies;
    }
    
    /**
     * This function will fetch all technologies that have the supplied tag
     * assigned.
     * 
     * @param string $tag
     * @return Zend_Db_Table_Rowset
     */
    public function fetchByTag($tag)
    {
        return null;
    }

}
