<?php
/**
 * This class represents a Revision of a technology. It extends the Zend_Db_Table_Row
 * object that is returned with a number of convinience functions that carry out more
 * complex database lookups.
 *
 * @package Semtech_Model
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 */
class Semtech_Model_Revision extends Zend_Db_Table_Row
{

  /**
   * This is the author of the revision.
   *
   * @var string|Semtech_Model_User
   */
  private $_author;

  /**
   * These are all of the tags associated with the revision.
   *
   * @var array
   */
  private $_tags;

  /**
   * This method will create a new revision.
   *
   * @param Semtech_Model_Technology $technology 
   * @param Semtech_Model_User $createdBy 
   * @param boolean $original 
   * @return Semtech_Model_Revision
   */
  public static function newRevision($technology, $createdBy, $original = false)
  {
    $trt = new Semtech_Model_DbTable_TechnologyRevisions();
    $newRevision = $trt->createRow();
    $newRevision->technology = $technology->id;
    $newRevision->createdby = $createdBy->id;
    $newRevision->original = $original;
    $newRevision->save();

    if (!$original)
    {
      $view = Zend_Layout::getMvcInstance()->getView();
      $newRevisionComment = "<i>{$createdBy->name} created a new revision of {$technology->name}. <a href=\"".$view->url(array('techid' => $technology->id, 'revid' => $newRevision->id), 'technologyrev')."\">Click here</a> to view it.</i>";
      Semtech_Model_Comment::newComment($newRevisionComment, $createdBy->id, $technology->id);
    }

    return $newRevision;
  }

  /**
   * This method will fetch the requested revision.
   * 
   * @param int $revid
   * @param int $techid
   * @param int $userid
   * @param boolean $original
   * @return Semtech_Model_Revision
   */
  public static function getRevision($revid = null, $techid = null, $userid = null, $original = false)
  {
    $trt = new Semtech_Model_DbTable_TechnologyRevisions();
    $select = $trt->select()->where("original = ?", $original);
    if ($revid)
      $select->where("id = ?", $revid);
    if ($techid)
      $select->where("technology = ?", $techid);
    if ($userid)
      $select->where("createdby = ?", $userid);
    return $trt->fetchRow($select);
  }

  /**
   * This function checks if the associated technology and original
   * technology ids match. If they do then this means that this
   * revision represents the original version.
   *
   * @return boolean
   */
  public function isOriginal()
  {
    return ($this->original ? true : false);
  }

  /**
   * This function checks if the supplied user is the original author
   * of this revision.
   *
   * @param Semtech_Model_User $author
   * @return boolean
   */
  public function isAuthor($author)
  {
    return ($this->createdby == $author->id);
  }

  /**
   * This function will return the user who created this revision.
   *
   * @return Semtech_Model_User|string
   */
  public function getAuthor()
  {
    if (is_null($this->_author))
    {
      $author = null;
      if (is_numeric($this->createdby))
      {
        $usertable = new Semtech_Model_DbTable_Users();
        $author = $usertable->fetchRow($usertable->select()->where("id = ?", $this->createdby));
      }
      else if (is_string($this->createdby))
      {
        $author = $this->createdby;
      }

      if (is_null($author))
      {
        $log = Zend_Registry::get("log");
        $log->alert("Unable to find an author for this revision, Revision ID: ".$this->id);
      }
      
      $this->_author = $author;
    }

    return $this->_author;
  }

  /**
    * This function will set the user who created the revision.
    *
    * @param User $author
    */
  public function setAuthor($author)
  {
    $this->createdby = $author->id;
  }

  /**
    * This function will return the technology object associated
    * with this revision.
    *
    * @return <Technology>
    */
  public function getTechnology()
  {
    $technologytable = new Semtech_Model_DbTable_Technologies();
    return $technologytable->fetchRow($technologytable->select()->where("id = ?", $this->technology));
  }

  /**
    * This function will return the createdon field as a UNIX timestamp.
    *
    * @return <int>
    */
  public function getCreatedOn()
  {
    return strtotime($this->createdon);
  }

  /**
    * This function takes an array of new tags which have the tag form ids set
    * as their keys. It then procedes to work out which tags need to be removed
    * and which need to be added.
    *
    * @param <array> $newTags
    */
  public function updateTags($newTags, $tagcatid)
  {
    // Flush the internally held tag cache before starting.
    $this->_tags = null;
    
    $tagcategorytable = new Semtech_Model_DbTable_TagCategories();
    $tagcategory = $tagcategorytable->fetchRow($tagcategorytable->select()->where("id = ?", $tagcatid));

    error_log("New Tags: ".$newTags);

    // First add all new tags.
    if (count($newTags))
    {
      $adddiff = array_diff($newTags, $this->getTags($tagcategory->name));
      foreach ($adddiff as $diff)
      {
        $this->addTag($diff);
      }
    }

    // Then remove any tags which are not present in the new tags.
    // Skip this step if there are no tags currently associate with the object.
    if (count($this->getTags($tagcategory->name)))
    {
      $deldiff = array_diff($this->getTags($tagcategory->name), $newTags);
      foreach ($deldiff as $diff)
      {
        $this->removeTag($diff);
      }
    }
  }

  public function hasTag($tag)
  {
    $hastag = false;
    $ttt = new Semtech_Model_DbTable_TechnologyTags();
    if ($ttt->fetchRow($ttt->select()->where("tag = ?", $tag->id)->where("revision = ?", $this->id)))
      $hastag = true;

    return $hastag;
  }

  public function getTags($category = null)
  {
    if (is_null($this->_tags) || (!isset($this->_tags[$category])))
    {
      $this->_tags = array();
      $ttt = new Semtech_Model_DbTable_TechnologyTags();
      $select = $ttt->select()->where("revision = ?", $this->id)->where("technology = ?", $this->technology);
      $alltags = $ttt->fetchAll($select);

      foreach ($alltags as $tag)
      {
        $tagtable = new Semtech_Model_DbTable_Tags();
        $select = $tagtable->select()->where("id = ?", $tag->tag);
        $tag = $tagtable->fetchRow($select);
        
        $this->_tags[$tag->category][] = $tag;
      }
    }
    
    if (!is_null($category))
    {
      $tags = isset($this->_tags[$category]) ? $this->_tags[$category] : array();
    }
    else
    {
      $tags = $this->_tags;
    }
    
    return $tags;
  }

  /**
    * This function will add a relation between the current technology and
    * the given tag.
    *
    * @param <Tag> $tag
    */
  private function addTag(Semtech_Model_Tag $tag)
  {
    // Flush the tag cache.
    $this->_tags = null;
    
    $technologytagstable = new Semtech_Model_DbTable_TechnologyTags();
    $technologytag = $technologytagstable->createRow();
    $technologytag->technology = $this->getTechnology()->id;
    $technologytag->tag = $tag->id;
    $technologytag->revision = $this->id;
    $technologytag->save();
  }

  /**
    * This function will remove a relation between the current technology and
    * the given tag.
    *
    * @param <Tag> $tag
    */
  private function removeTag(Semtech_Model_Tag $tag)
  {
    // Flush the tag cache.
    $this->_tags = null;
    
    $technologytagstable = new Semtech_Model_DbTable_TechnologyTags();
    $technologytagstable->delete("tag = {$tag->id} ".Zend_Db_Select::SQL_AND." revision = {$this->id}");
  }

  public function getAnnotationLevel()
  {
    $annotation = $this->_getAnnotation();
    if ($annotation)
      return $annotation->annotationlevel;

    return "Not Specified";
  }

  public function getAnnotationGroup()
  {
    $annotation = $this->_getAnnotation();
    if ($annotation)
      return $annotation->annotationgroup;

    return "Not Specified";
  }

  public function getCreationLevel()
  {
    $annotation = $this->_getAnnotation();
    if ($annotation)
      return $annotation->creationlevel;

    return "Not Specified";
  }

  public function getCreationGroup()
  {
    $annotation = $this->_getAnnotation();
    if ($annotation)
      return $annotation->creationgroup;

    return "Not Specified";
  }

  public function getActivityLevels()
  {
    $tat = new Semtech_Model_DbTable_TechnologyAnnotation();
    $activitylevels = $tat->fetchAll($tat->select()->where("revision = ?", $this->id));
    $result = array();
    foreach ($activitylevels as $level)
    {
      $result[$level->group][$level->type] = $level->level;
    }
    return $result;
  }

  public function setTechnologyUsage($usage)
  {
    $tut = new Semtech_Model_DbTable_TechnologyUsage();
    $technologyusage = $tut->fetchRow($tut->select()->where("revision = ?", $this->id));
    if (!$technologyusage)
      $technologyusage = $tut->createRow();

    $technologyusage->revision = $this->id;
    $technologyusage->usage = $usage->getValue();
    $technologyusage->save();
  }

  public function getTechnologyUsage()
  {
    $tut = new Semtech_Model_DbTable_TechnologyUsage();
    return $tut->fetchRow($tut->select()->where("revision = ?", $this->id));
  }

  private function _getAnnotation()
  {
    $tat = new Semtech_Model_DbTAble_TechnologyAnnotation();
    return $tat->fetchRow($tat->select()->where("revision = ?", $this->id));
  }

}
?>
