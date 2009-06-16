<?php
class Semtech_Model_DbTable_Tags extends Zend_Db_Table
{

	protected $_name = 'tags';
	protected $_rowClass = 'Semtech_Model_Tag';

    /**
     * This function performs a look up using the form id as the search method.
     * It will either return the corresponding tag object or false if it cannot
     * be found.
     *
     * @param <string> $formtag
     * @return <Tag|boolean>
     */
    public function getTagByFormName($formtag)
    {
        $tags = $this->fetchAll();
        foreach ($tags as $tag)
        {
            if ($tag->getFormId() == $formtag)
            {
                return $tag;
            }
        }

        return false;
    }

}
