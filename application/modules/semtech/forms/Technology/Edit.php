<?php
/**
 * This class is an extension of the technology creation form to produce an
 * edit technology form.
 *
 * @package Form_Technology
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 */
class Semtech_Form_Technology_Edit extends Semtech_Form_Technology_Create
{
	
	/**
	 * This function will produce the edit technology form object.
	 *
	 * @param string $techid 
	 * @param string $options 
	 * @return Form_Technology_Edit
	 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
	 */
	public function __construct($techid, $options = null)
	{
		$this->addElement($this->createHiddenElement("techid", $techid));
		parent::__construct($options);
		
		$this->url->removeValidator('Unique');
		
		$this->submit->setLabel("Update Data");
	}
}
?>