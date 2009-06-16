<?php
class Semtech_Validator_Unique extends Zend_Validate_Abstract
{
	const NOT_UNIQUE = 'notunique';
	
	protected $_messageTemplates = array(
		self::NOT_UNIQUE => "The value '%value%' already exists in the database."
	);
	
	private $tableClass;
	private $propertyName;
	
	public function __construct($tableClass, $propertyName)
	{
		if (!isset($tableClass))
			throw new Zend_Exception("The tableClass parameter must be passed.");
			
		if (!isset($propertyName))
			throw new Zend_Exception("The propertyName parameter must be passed.");
			
		$this->tableClass = $tableClass;
		$this->propertyName = $propertyName;
	}
	
	public function isValid($value)
	{
		$isValid = true;
		
		$tableClass = $this->tableClass;
		$propertyName = $this->propertyName;
		
		$table = new $tableClass();
		$resultrows = $table->fetchAll($table->select()->where("$propertyName = ?", $value));
		if ($resultrows->count())
		{
			$this->_error(self::NOT_UNIQUE);
			$isValid = false;
		}
			
		return $isValid;
	}
}
?>