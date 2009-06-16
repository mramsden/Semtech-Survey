<?php
/**
 * This class represents a single tag from the tags table.
 *
 * @author Marcus Ramsden
 */
class Semtech_Model_Tag extends Zend_Db_Table_Row
{

	public static function getTag($tagid)
	{
		$table = new Semtech_Model_DbTable_Tags();
		return $table->fetchRow($table->select()->where("id = ?", $tagid));
	}

	/**
	 * This function returns the name of the item formatted into
     * an acceptable form for use in HTML forms.
     *
     * @return <string>
	 */
	public function getFormId()
	{
		return strtolower($this->_removeNonAlphaCharacters($this->tag));
	}

    /**
     * This function returns the category name of the tag formatted into a
     * standard way for use in HTML forms.
     *
     * @return <string>
     */
	public function getFormCategory()
	{
		return strtolower($this->_removeNonAlphaCharacters($this->category));
	}

    /**
     * This function returns the string representation of a tag object. In this
     * case the string represenation of a tag object is the actual tag text.
     *
     * @return <string>
     */
    public function __toString()
    {
        return $this->tag;
    }

    /**
     * This function takes in a string and removes all non-alphanumeric
     * characters from the string.
     *
     * @param <string> $string
     * @return <string>
     */
	private function _removeNonAlphaCharacters($string)
	{
		return preg_replace("/(\W*)/i", "", $string);
	}

}
