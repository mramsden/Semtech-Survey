<?php
class Semtech_Model_DbTable_TagCategories extends Zend_Db_Table
{

	protected $_name = "tagcategories";
	protected $_rowClass = "Semtech_Model_TagCategory";

    /**
     * This function will return all of the super categories.
     */
    public function getSuperCategories()
    {
        $select = $this->select();
        $select->columns("supercategory")
               ->group("supercategory");

        return $this->fetchAll($select);
    }

}
