<?php
/**
 * This class represents an individual technology stored in the database.
 *
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @package Semtech_Model
 */
class Semtech_Model_Technology extends Zend_Db_Table_Row {

  /**
   * The original revision of the technology.
   *
   * @var Semtech_Model_Revision
   */
  private $_originalRevision;
  
  /**
   * The revisions associated with this technology.
   *
   * @var Zend_Db_Table_Rowset
   */
  private $_revisions;

  /**
   * The references associated with this technology.
   *
   * @var Zend_Db_Table_Rowset
   */
  private $_references;

	/**
	 * This function will create and save a new Technology row object.
	 *
	 * @return Semtech_Model_Technology
	 */
	public static function newTechnology($name, $url, $description, $license, $version, $release_date, $iprights)
	{
		$technologytable = new Semtech_Model_DbTable_Technologies();
		$newTechnology = $technologytable->createRow();
		$newTechnology->name = $name;
		$newTechnology->url = $url;
		$newTechnology->description = $description;
		$newTechnology->license = $license;
		$newTechnology->version = $version;
		$newTechnology->release_date = $release_date;
		$newTechnology->iprights = $iprights;
		$newTechnology->save();

		return $newTechnology;
	}

	/**
	 * This method will return the technology associated with the
	 * supplied id.
	 * 
	 * @param int $id
	 * @return Semtech_Model_Technology
	 */
	public static function getTechnology($id)
	{
		$tt = new Semtech_Model_DbTable_Technologies();
		return $tt->fetchRow($tt->select()->where("id = ?", $id));
	}

  /**
   * This method will return the revisions associated with the technology.
   *
   * @param int $id 
   * @return Zend_Db_Table_Rowset
   */
	public static function getAllTagRevisions($id)
	{
		$trt = new Semtech_Model_DbTable_TechnologyRevisions();
		$select = $trt->select()->where("technology = ?", $id)->order("createdon");
		return $trt->fetchAll($select);
	}

	/**
	 * This method returns true if the technology has been announced
	 * on Twitter. False if not.
	 *
	 * @return int
	 */
	public function isAnnounced()
	{
		return (int)$this->announced;
	}

	/**
	 * This method marks that the technology has been announced.
	 */
	public function announced()
	{
		$this->announced = 1;
		$this->save();
	}

	/**
	 * This function will check if the technology has the given tag associated
	 * with it. If the tag does exist then true is returned, if not then false
	 * is returned.
	 *
	 * @param Tag $tag
	 * @return boolean
	 */
	public function hasTag(Tag $tag)
	{
		return in_array($tag, $this->getTags());
	}

	/**
	 * This function returns all of the relations attached to this technology.
	 *
	 * @return <array>
	 */
	public function getRelations()
	{
		$srt = new Semtech_Model_DbTable_ServiceRelations();
		return $srt->fetchAll($srt->select()->where("object = ?", $this->id));
	}

	/**
	 * This method will return all revisions associated with this technology.
	 * @return Zend_Db_Table_Rowset
	 */
	public function getRevisions()
	{
	  if (is_null($this->_revisions))
	  {
	    $trt = new Semtech_Model_DbTable_TechnologyRevisions();
  		$select = $trt->select()->where("technology = ?", $this->id)->order("createdon");
  		$this->_revisions = $trt->fetchAll($select);
	  }
	  
		return $this->_revisions;
	}
	
	/**
	 * This function counts the number of relations this technology has.
	 *
	 * @return <int>
	 */
	public function countRelations()
	{
		$total = 0;

		foreach ($this->getRelations() as $relationset)
		{
			unset($relationset['text']);
			$total += count($relationset);
		}

		return $total;
	}

	/**
	 * This function returns a string representation of the object which in this case is the name of the technology.
	 *
	 * @return <string>
	 */
	public function __toString()
	{
		return $this->name;
	}

	/**
	 * This function returns all of the comments associated with this object.
	 *
	 * @return <Zend_Db_Table_Rowset_Abstract>
	 */
	public function getComments()
	{
		$technologycommentstable = new Semtech_Model_DbTable_TechnologyComments();
		return $technologycommentstable->fetchAll($technologycommentstable->select()->where("technology = ?", $this->id));
	}

	/**
	 * This function will get the original revision of the technology. The original
	 * revision belongs to the original creator of the technology.
	 * 
	 * @return Semtech_Model_Revision
	 */
	public function getOriginalRevision()
	{
	  if (is_null($this->_originalRevision))
	  {
	    $trt = new Semtech_Model_DbTable_TechnologyRevisions();
  		$this->_originalRevision = $trt->fetchRow($trt->select()->where("technology = ?", $this->id)->where("original = 1"));
	  }
	  
		return $this->_originalRevision;
	}

	public function getRevision($user)
	{
	  if (is_null($this->_revisions))
	  {
	    $trt = new Semtech_Model_DbTable_TechnologyRevisions();
  		$revision = $trt->fetchRow($trt->select()->where("createdby = ?", $user->id)->where("technology = ?", $this->id)->where("original = 0"));
	  }
	  else
	  {
	    foreach ($this->_revisions as $r)
	    {
	      if ($r->createdby == $user->id)
	      {
	        $revision = $r;
	      }
	    }
	  }
		
		return $revision;
	}

	public function getDefaultRevision()
	{
		$originalrevision = $this->getOriginalRevision();
		return $originalrevision;
	}

	public function getReferences()
	{
	  if (is_null($this->_references))
    {
      $referencetable = new Semtech_Model_DbTable_TechnologyReferences();
  		$this->_references = $referencetable->fetchAll($referencetable->select()->where("technology = ?", $this->id));
    }
		
		return $this->_references;
	}

	public function __set($key, $val)
	{
		switch ($key)
		{
			case "release_date":
				$val = strtotime($val);
				break;
		}

		parent::__set($key, $val);
	}

	public function __get($key)
	{
		$ret = parent::__get($key);

		switch ($key)
		{
			case "release_date":
				if ($ret == 0)
				{
				  $ret = "";
				}
				break;
			case "url":
				if (!preg_match("/^.*:\/\/.*$/i", $ret))
				{
				  $ret = "http://".$ret;
				}
				break;
		}

		return $ret;
	}
	
}
?>
