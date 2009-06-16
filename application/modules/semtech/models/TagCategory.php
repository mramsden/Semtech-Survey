<?php
class Semtech_Model_TagCategory extends Zend_Db_Table_Row
{
    const SUPERCATEGORY_EDUCATIONAL_CONTEXT = "Educational Context";
    const SUPERCATEGORY_SEMANTIC_TECHNOLOGIES = "Semantic/Web 2.0 Technologies";

	public static function getTagCategory($id)
	{
		$tct = new Semtech_Model_DbTable_TagCategories();
		return $tct->fetchRow($tct->select()->where("id = ?", $id));
	}

    /**
     * This function will format the name of the tag into a form friendly
     * version.
     * @return <string>
     */
	public function getFormId()
	{
		return strtolower($this->_removeNonAlphaCharacters($this->name));
	}

    /**
     * This function will format the supercategory of a tag into a form friendly
     * version.
     * @return <string>
     */
	public function getFormSuperCategory()
	{
		return strtolower($this->_removeNonAlphaCharacters($this->supercategory));
	}
	
	private function _removeNonAlphaCharacters($string)
	{
		return preg_replace("/(\W*)/i", "", $string);
	}
}
?>
