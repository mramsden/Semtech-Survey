<?php
class Semtech_Service_Report_Technology_Pdf
{
	
	/**
	 * @var Zend_Pdf
	 */
	private $_pdf;
	
	private $_pageSize;

	private $_metadata = array(
		'Title' => "Semtech Survey Technology Report",
		'Creator' => "Semtech Survey"
	);
	
	public function __construct($pageSize = Zend_Pdf_Page::SIZE_A4)
	{
		$this->_pdf = new Zend_Pdf();
		$this->_pageSize = $pageSize;
	}

	public function render()
	{
		$this->_pdf->render();
	}
	
	/**
	 * Creates a new page in the PDF and returns the newly created
	 * page.
	 * @return Zend_Pdf_Page
	 */
	private function _newPage()
	{
		$newPage = new Zend_Pdf_Page($this->_pageSize);
		$this->_pdf->pages[] = $newPage;
		return $newPage;
	}
	
}