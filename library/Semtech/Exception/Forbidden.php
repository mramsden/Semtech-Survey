<?php
/**
 * This object is used to raise a means of identifying when an HTTP/1.0
 * 403 Forbidden response should be sent.
 *
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @package Semtech_Exception
 */
class Semtech_Exception_Forbidden extends Zend_Exception
{

	public function __construct($message)
	{
		parent::__construct($message);
	}
	
}
