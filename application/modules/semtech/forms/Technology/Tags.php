<?php
/**
 * This class creates an object that will render a technology
 * tags form.
 *
 * @package Form_Technology
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 */
class Semtech_Form_Technology_Tags extends Semtech_Form_Form
{
	
	/**
	 * This function will create a technology tags form. The tagcat parameter
	 * will allow you to choose which category you want to work with. The
	 * techid parameter allows you to define in the form what technology the
	 * form should edit.
	 *
	 * @param string $tagcat 
	 * @param string $techid 
	 * @param array $options 
	 * @return Form_Technology_Tags
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function __construct($tagcat, $techid, $options = null)
	{
		parent::__construct($options);
		$this->setAction("/technology/tags")
			 ->setMethod("post")
			 ->setDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Form'));
			
		$this->addElement(parent::createHiddenElement('tagcat', $tagcat));
		$this->addElement(parent::createHiddenElement('techid', $techid));
		
		$tagcategory = Semtech_Model_TagCategory::getTagCategory($tagcat);
		$auth = Zend_Auth::getInstance();
		$revision = Semtech_Model_Revision::getRevision(null, $techid, $auth->getStorage()->read());
		
		$tt = new Semtech_Model_DbTable_Tags();
		foreach ($tt->fetchAll($tt->select()->where('category = ?', $tagcategory->name)) as $tag)
		{
			$tagCheck = new Zend_Form_Element_Checkbox($tag->getFormId());
			$tagCheck->setLabel($tag->tag)
					 ->setDecorators($this->_tableFormRowDecorator);
			if ($revision)
				if ($revision->hasTag($tag))
				{
					$tagCheck->setChecked(true);
				}
			$this->addElement($tagCheck);
		}
		
		$other = new Zend_Form_Element_Text('other');
		$other->setLabel("Other")
		      ->setDecorators($this->_tableFormRowDecorator);
		$this->addElement($other);
		
		if ($tagcategory->name == "Aimed At")
		{
			$usage = new Zend_Form_Element_Select('usage');
			$usage->setLabel("Scale of Use")
				  ->setDecorators($this->_tableFormRowDecorator);
			if ($revision && $revision->getTechnologyUsage())
				$usage->setValue($revision->getTechnologyUsage()->usage);
			$usage->addMultiOptions(array('Unknown' => 'Unknown', 'Experiment (Under Trial)' => 'Experiment (Under Trial)', 'Module' => 'Module', 'Course' => 'Course', 'Department' => 'Department', 'Institution' => 'Institution', 'National' => 'National', 'Global' => 'Global'));
			$this->addElement($usage);
		}
		
		$submit = $this->createSubmitButton("Update Tags");
		$submit->setDecorators($this->_tableFormSubmit);
		$this->addElement($submit);
	}
	
	/**
	 * This function will convert the tags stored in this form into an
	 * array of tags which can be processed when updating the tags for
	 * a technology.
	 *
	 * @return array
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function processForm()
	{
		$formData = $this->getValues();
		$tagCategoryId = $formData['tagcat'];

        // Start by removing all values with keys which are on the ignore list.
        $ignoreList = array('tagcat', 'techid', 'submit', 'usage');
        foreach ($ignoreList as $ignoreItem)
        {
            unset($formData[$ignoreItem]);
        }

        // Grab the other tags then unset them from the array. This step is
        // skipped in the event of the other tags field being empty.
        $otherTags = array();
        if ($formData['other'] != "")
        {
            $otherTags = split(",", $formData['other']);
            unset($formData['other']);
        }

        // Now remove all tags that have 0 as their value since we can ignore
        // these tags.
        foreach (array_keys($formData) as $formDataTags)
        {
            if ($formData[$formDataTags] == 0)
            {
                unset($formData[$formDataTags]);
            }
        }

        // Get the form friendly versions of the other tags.
        $formTags = array();
        foreach ($otherTags as $otherTag)
        {
            // First attempt to fetch the other tag if it already exists.
            $tagtable = new Semtech_Model_DbTable_Tags();
            $tag = $tagtable->fetchRow($tagtable->select()->where("tag = ?", $otherTag));

            // If the tag was not found then we need to create a new one which
            // we will save to the database.
            if (!$tag)
            {
                $tagcategorytable = new Semtech_Model_DbTable_TagCategories();
                $tagcategory = $tagcategorytable->fetchRow($tagcategorytable->select()->where("id = ?", $tagCategoryId));

                $tag = $tagtable->createRow();
                $tag->tag = $otherTag;
                $tag->category = $tagcategory->name;
                $tag->save();
            }
            $formTags[$tag->getFormId()] = $tag;
        }

        // Finally populate the return array with the preset tags.
        foreach (array_keys($formData) as $formTag)
        {
            $tagtable = new Semtech_Model_DbTable_Tags();
            $tag = $tagtable->getTagByFormName($formTag);
            $formTags[$tag->getFormId()] = $tag;
        }

        return $formTags;
	}
	
}
?>