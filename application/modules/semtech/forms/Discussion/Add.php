<?php
class Semtech_Form_Discussion_Add extends Semtech_Form_Form
{
	
	public function __construct($techid, $userid, $options = null)
	{
		parent::__construct($options);
		
		$this->setAttrib("onsubmit", "javascript: addComment(); return false;");
		$this->setAttrib("id", "commentform");
		
		$this->addElement($this->createHiddenElement('techid', $techid));
		$this->addElement($this->createHiddenElement('userid', $userid));
		$this->addElement($this->createHiddenElement('replyto', 0));
		
		$comment = new Zend_Form_Element_Textarea('comment');
		$comment->setRequired(true)
				->setAttrib('rows', 4);
		$this->addElement($comment);
		
		$this->addElement($this->createSubmitButton("Add Comment"));
	}
	
}