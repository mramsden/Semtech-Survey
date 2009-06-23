<?php
/**
 * RevisionController.php
 *
 * @author Marcus Ramsden
 */

/**
 * This class is responsible for managing revisions.
 *
 * @package Admin_Controllers
 * @author Marcus Ramsden
 */
class Admin_RevisionController extends Semtech_Controller_Admin_Action
{
  
  /**
   * /admin/revision/delete
   *
   * @return void
   * @author Marcus Ramsden
   */
  public function deleteAction()
  {
    $revisionid = $this->getRequest()->getParam("revid");
    if (!is_null($revisionid))
    {
      $revision = Semtech_Model_Revision::getRevision($revisionid);
      if (!is_null($revision))
      {
        if ($this->getRequest()->isPost())
        {
          $techid = $revision->getTechnology()->id;
          if ($this->getRequest()->getParam("confirmation") == "Confirm Action")
          {
            $revision->delete();
            $this->_flashMessenger->addMessage("Revision deleted.");
          }
          else
          {
            $this->_flashMessenger->addMessage("Delete action cancelled.");
          }
          $this->getHelper("Redirector")->gotoSimple("view", "technology", "semtech", array("techid" => $techid));
        }
        else
        {
          $this->view->revision = $revision;
        }
      }
      else
      {
        Zend_Registry::get("log")->crit(__METHOD__.": Unable to find revision ".$revisionid.".");
        $this->_flashMessenger->addMessage("Unable to find requested revision.");
        $this->getHelper("Redirector")->gotoUrl($_SERVER['HTTP_REFERER']);
      }
    }
    else
    {
      $this->_flashMessenger->addMessage("No revision id was specified.");
      $this->getHelper("Redirector")->gotoUrl($_SERVER['HTTP_REFERER']);
    }
    
    $this->view->title = "Delete Revsion";
  }
  
}