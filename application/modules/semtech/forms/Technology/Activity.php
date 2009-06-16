<?php
/**
 * This class represents an activity levels form.
 *
 * @package Form_Technology
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 */
class Semtech_Form_Technology_Activity extends Semtech_Form_Form
{
	
	/**
	 * This function will create the form object for the activity levels form.
	 * The supplied techid will be used to work out which technology these
	 * changes are associated with.
	 *
	 * @param string $techid 
	 * @param string $options 
	 * @return Form_Technology_Activity
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function __construct($techid, $revid, $options = null)
	{
		parent::__construct($options);
		$this->setAction("/technology/activity")
			 ->setMethod("post");
			
		$this->addElement($this->createHiddenElement('techid', $techid));
		$this->addElement($this->createHiddenElement('revid', $revid));
		
		$tt = new Semtech_Model_DbTable_Tags();
		$tags = $tt->fetchAll($tt->select()->where("category = ?", "Actors"));
		$validtagids = array();
		foreach ($tags as $tag)
			$validtagids[] = $tag->id;
		
		$alt = new Semtech_Model_DbTable_ActivityLevels();
		$activitylevels = $alt->fetchAll();
		
		$ttt = new Semtech_Model_DbTable_TechnologyTags();
		$technologytags = $ttt->fetchAll($ttt->select()->where("technology = ?", $techid)->where("revision = ?", $revid));
		foreach ($technologytags as $techtag)
		{
			if (in_array($techtag->tag, $validtagids))
			{
				$tag = Semtech_Model_Tag::getTag($techtag->tag);
				
				$annotationlevel = new Zend_Form_Element_Select("annotation_".$tag->getFormId());
				$creationlevel = new Zend_Form_Element_Select("creation_".$tag->getFormId());
				
				$annotationlevel->setLabel("Annotation Level");
				$creationlevel->setLabel("Content Creation Level");
				
				$annotationlevel->addMultiOption('');
				$creationlevel->addMultiOption('');
				
				foreach ($activitylevels as $activitylevel)
				{
					$annotationlevel->addMultiOption($activitylevel->level, $activitylevel->level);
					$creationlevel->addMultiOption($activitylevel->level, $activitylevel->level);
				}
				
				$annotationlevel->setDecorators($this->_tableFormRowDecorator);
				$creationlevel->setDecorators($this->_tableFormRowDecorator);
				
				$tat = new Semtech_Model_DbTable_TechnologyAnnotation();
				$annotation = $tat->fetchRow($tat->select()->where("revision = ?", $revid)->where("type = ?", "annotation"));
				$creation = $tat->fetchRow($tat->select()->where("revision = ?", $revid)->where("type = ?", "creation"));
				
				if ($annotation)
					$annotationlevel->setValue($annotation->level);
				if ($creation)
					$creationlevel->setValue($creation->level);
				
				$this->addElement($annotationlevel);
				$this->addElement($creationlevel);
				
				$this->addDisplayGroup(array("annotation_".$tag->getFormId(), "creation_".$tag->getFormId()), $tag->getFormId());
				$this->getDisplayGroup($tag->getFormId())->setLegend($tag->tag);
				$this->setDisplayGroupDecorators(array('FormElements', array(array('data' => 'HtmlTag'), array('tag' => 'table')), 'Fieldset'));
			}
		}

		$this->addElement($this->createSubmitButton("Update Activity Levels"));
	}
	
	public function processForm()
	{
		$ignorelist = array('techid', 'revid', 'submit');
		
		$result = array();
		
		foreach ($this->getValues() as $key => $value)
		{
			if (in_array($key, $ignorelist))
				continue;
			
			if (preg_match("/creation_/", $key))
			{
				$type = 'creation';
				$formid = str_replace("creation_", "", $key);
			}
			else if (preg_match("/annotation/", $key))
			{
				$type = 'annotation';
				$formid = str_replace("annotation_", "", $key);
			}
				
			$tt = new Semtech_Model_DbTable_Tags();
			$tags = $tt->fetchAll($tt->select()->where("category = ?", "Actors"));
			$group = "";
			foreach ($tags as $tag)
			{
				if ($tag->getFormId() == $formid)
					$group = $tag->tag;
			}	
			
			$result[] = array('level' => $value, 'type' => $type, 'group' => $group);
		}
		
		return $result;
	}
	
}
?>