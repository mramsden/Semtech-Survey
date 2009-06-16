<?php
class TechnologyForms
{

	public static function createForm($options = null)
	{
		$form = new Zend_Form($options);
		$form->setAction("/technology/new")
		     ->setMethod("post");

        $id = new Zend_Form_Element_Hidden('id');
        $form->addElement($id);

		$name = new Zend_Form_Element_Text('name');
		$name->setRequired(true)
		     ->setLabel("Name of Service/Software");
		$form->addElement($name);

		$release = new Zend_Form_Element_Text('release_date');
                $release->setRequired(false)
                        ->setLabel("Release Date");
		$form->addElement($release);

		$version = new Zend_Form_Element_Text('version');
		$version->setRequired(false)
			->setLabel("Version Number");
		$form->addElement($version);

		$url = new Zend_Form_Element('url');
		$url->setRequired(true)
		    ->setLabel("URL");
		$form->addElement($url);

		$description = new Zend_Form_Element_Textarea('description');
		$description->setRequired(true)
			    ->setLabel("General Description");
		$form->addElement($description);

		$iprights = new Zend_Form_Element_Textarea('iprights');
		$iprights->setRequired(false)
			 ->setLabel("Intellectual Property Rights");
		$form->addElement($iprights);

		$license = new Zend_Form_Element_Radio('license');
		$license->setRequired(true)
			->setLabel("License");
		$licenses = new Licenses();
		$licenses = $licenses->fetchAll();
		foreach ($licenses as $licenseData) {
			$license->addMultiOption($licenseData->name, $licenseData->name);
		}
		$form->addElement($license);

		TechnologyForms::addSubmitToForm($form, "Create");

		return $form;
	}

	public static function tagForm($categoryid, $technologyid, $options = null)
	{

		$form = new Zend_Form($options);
		$form->setAction("/technology/tags")
		     ->setMethod("post");
		$form->setDecorators(array(
            'FormElements',
            array(array('data' => 'HtmlTag'), array('tag' => 'table')),
            'Form'
        ));

		$id = new Zend_Form_Element_Hidden('id');
        $id->setValue($categoryid);
        $id->setDecorators(array(
            'ViewHelper'
        ));
		$form->addElement($id);

        $tech = new Zend_Form_Element_Hidden('tech');
        $tech->setValue($technologyid);
        $tech->setDecorators(array(
            'ViewHelper'
        ));
        $form->addElement($tech);

		$tagcategorytable = new TagCategoryTable();
        $select = $tagcategorytable->select()->where("id = ?", $categoryid);
		$tagcategory = $tagcategorytable->fetchRow($select);

        $technologytable = new TechnologyTable();
        $technology = $technologytable->fetchRow($technologytable->select()->where("id = ?", $technologyid));

        $tagtable = new TagTable();
		$tags = $tagtable->fetchAll($tagtable->select()->where('category = ?', $tagcategory->name));
		foreach ($tags as $tag) {
			$tagcheck = new Zend_Form_Element_Checkbox($tag->getFormId());
			$tagcheck->setLabel($tag->tag);
            if ($technology->hasTag($tag))
            {
                $tagcheck->setChecked(true);
            }

			$tagcheck->setDecorators(array(
				'ViewHelper',
				'Errors',
				array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
				array('Label', array('tag' => 'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
			));	
			$form->addElement($tagcheck);
		}

		$other = new Zend_Form_Element_Text("other");
		$other->setLabel("Other");
		$other->setDecorators(array(
				'ViewHelper',
				'Errors',
				array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
				array('Label', array('tag' => 'td')),
				array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
		));
		$form->addElement($other);				

        TechnologyForms::addSubmitToForm($form, "Update Tags");
        $submit = $form->getElement('submit');
        $submit->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'colspan' => '2', 'style' => 'text-align: right;')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));
		
		return $form;
	}

    public static function relatedForm($options = null)
    {
        if (!isset($options['techid']))
            throw new Zend_Exception("'techid' must be supplied in the options array.");

        $techid = $options['techid'];
        unset($options['techid']);

        $form = new Zend_Form($options);
        $form->setAction("/technology/relations")
             ->setMethod('post');

        $techidelement = new Zend_Form_Element_Hidden('techid');
        $techidelement->setValue($techid);
        $form->addElement($techidelement);

        $technologies = TechnologyRevisionsTable::getOriginals();

        $service = new Zend_Form_Element_Select('service');
        $service->setLabel("Service Name")
                ->setRequired(true);
        foreach ($technologies as $technology)
        {
            if ($technology->id == $techid)
                continue;

            $service->addMultiOption($technology->id, $technology->name);
        }
        $form->addElement($service);

        $servicereldeftable = new ServiceRelationDefinitionsTable();
        $servicereldefs = $servicereldeftable->fetchAll();

        $servicerelation = new Zend_Form_Element_Select('servicerelation');
        $servicerelation->setLabel("Relation");
        foreach ($servicereldefs as $servicereldef)
        {
            $servicerelation->addMultiOption($servicereldef->name, $servicereldef->text);
        }
        $form->addElement($servicerelation);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel("Relation Description");
        $form->addElement($description);

        TechnologyForms::addSubmitToForm($form, "Add Relation");

        return $form;
    }

    public static function commentsForm($options = null)
    {
        if (!isset($options['techid']))
            throw new Zend_Exception("'techid' must be supplied in the options array.");

        $techid = $options['techid'];
        unset($options['techid']);

        $form = new Zend_Form($options);
        $form->setAction("/technology/comments")
             ->setMethod("post");

        $techidelement = new Zend_Form_Element_Hidden('techid');
        $techidelement->setValue($techid);
        $form->addElement($techidelement);

        $comment = new Zend_Form_Element_Textarea('comment');
        $comment->setLabel("Comment")
                ->setRequired(true)
                ->setAttrib('rows', 4);
        $form->addElement($comment);

        $captcha = new Zend_Form_Element_Captcha('foo', array(
            'label' => "Please enter the following text (All letters are capitals)",
            'captcha' => array(
                'captcha' => 'Image',
                'font' => '../other/acme.ttf',
                'wordLen' => 6,
                'timeout' => 300,
            ),
        ));
        $form->addElement($captcha);

        TechnologyForms::addSubmitToForm($form, "Add Comment");

        return $form;
    }

    public static function activityForm($options = null)
    {
        if (!isset($options['techid']))
            throw new Zend_Exception("'techid' must be set in the options array when creating an activity form.");

        $techid = $options['techid'];
        unset($options['techid']);

        $form = new Zend_Form($options);
        $form->setAction("/technology/activity")
             ->setMethod("post");

        $tech_id = new Zend_Form_Element_Hidden('techid');
        $tech_id->setValue($techid);
        $form->addElement($tech_id);

        $annotationlevel = new Zend_Form_Element_Select('annotationlevel');
        $annotationlevel->setLabel("Level of Annotation");
        $form->addElement($annotationlevel);

        $annotationgroup = new Zend_Form_Element_Select('annotationgroup');
        $annotationgroup->setLabel("Annotation Carried Out By");
        $annotationgroup->addMultiOption("All", "All");
        $form->addElement($annotationgroup);

        $contentlevel = new Zend_Form_Element_Select('creationlevel');
        $contentlevel->setLabel("Level of Content Creation");
        $form->addElement($contentlevel);

        $contentgroup = new Zend_Form_Element_Select('creationgroup');
        $contentgroup->setLabel("Content Created By");
        $contentgroup->addMultiOption("All", "All");
        $form->addElement($contentgroup);

        $activitylevelstable = new ActivityLevelsTable();

        foreach ($activitylevelstable->fetchAll($activitylevelstable->select()->order("id")) as $activitylevel)
        {
            $annotationlevel->addMultiOption($activitylevel->level, $activitylevel->level);
            $contentlevel->addMultiOption($activitylevel->level, $activitylevel->level);
        }

        $technologytable = new TechnologyTable();
        $technology = $technologytable->fetchRow($technologytable->select()->where("id = ?", $techid));
        $tags = $technology->getTags("Actors");

        foreach ($tags as $tag)
        {
            $annotationgroup->addMultiOption($tag->tag, $tag->tag);
            $contentgroup->addMultiOption($tag->tag, $tag->tag);
        }

        TechnologyForms::addSubmitToForm($form, "Update");
       
	return $form; 
    }

	public static function referenceForm($options = null)
	{
		if (!isset($options['techid']))
            throw new Zend_Exception("'techid' must be set in the options array when creating an activity form.");

        $techid = $options['techid'];
        unset($options['techid']);

        $form = new Zend_Form($options);
        $form->setAction("/technology/reference")
             ->setMethod("post");

		$tech_id = new Zend_Form_Element_Hidden('techid');
        $tech_id->setValue($techid);
        $form->addElement($tech_id);

		$reference = new Zend_Form_Element_Textarea('reference');
		$reference->setLabel("Reference")
				  ->setRequired(true);
		$form->addElement($reference);
		
		TechnologyForms::addSubmitToForm($form, "Add Reference");
		
		return $form;
	}

	public static function addSubmitToForm(&$form, $label)
	{
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setLabel($label);
		$form->addElement($submit);
	}

}
