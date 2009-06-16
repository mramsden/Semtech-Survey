<?php
/**
 * This class is used to create the technology creation form.
 *
 * @package Form_Technology
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 */
class Semtech_Form_Technology_Create extends Semtech_Form_Form
{

	/**
	 * This function creates the create technology form object.
	 *
	 * @param string $techid 
	 * @param string $options 
	 * @return Form_Technology_Create
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);
		$this->setAction("/technology/new")
			 ->setMethod("post");
			
		$name = new Zend_Form_Element_Text('name');
		$name->setRequired(true)
		     ->setLabel("Name of Service/Software");
		$this->addElement($name);

		$release = new Zend_Form_Element_Text('release_date');
                $release->setRequired(false)
                        ->setLabel("Release Date");
		$this->addElement($release);

		$version = new Zend_Form_Element_Text('version');
		$version->setRequired(false)
				->setLabel("Version Number");
		$this->addElement($version);

		$url = new Zend_Form_Element('url');
		$url->setRequired(true)
		    ->setLabel("URL")
				->addPrefixPath('Semtech_Validator', 'Semtech/Validator/', 'validate')
				->addValidator('Unique', false, array('Semtech_Model_DbTable_Technologies', 'url'));
		$this->addElement($url);

		$description = new Zend_Form_Element_Textarea('description');
		$description->setRequired(true)
			    	->setLabel("General Description")
						->setAttrib("rows", "4");
		$this->addElement($description);

		$iprights = new Zend_Form_Element_Textarea('iprights');
		$iprights->setRequired(false)
			 	 ->setLabel("Intellectual Property Rights")
				 ->setAttrib("rows", "4");
		$this->addElement($iprights);

		$license = new Zend_Form_Element_Radio('license');
		$license->setRequired(true)
				->setLabel("License");
		$licenses = new Semtech_Model_DbTable_Licenses();
		$licenses = $licenses->fetchAll();
		foreach ($licenses as $licenseData) {
			$license->addMultiOption($licenseData->name, $licenseData->name);
		}
		$this->addElement($license);
		
		$this->addElement($this->createSubmitButton("Add Technology"));
	}
	
}
?>