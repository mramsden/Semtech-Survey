<?php
class Semtech_IndexController extends Semtech_Controller_Action
{
	
	public function indexAction()
	{
		$tagcloud = new Semtech_Tagcloud(250, 100);
		$technologytagstable = new Semtech_Model_DbTable_TechnologyTags();
		$technologytags = $technologytagstable->fetchAll();
		$tagsandweights = array();
		foreach ($technologytags as $technologytag)
		{
			$tag = Semtech_Model_Tag::getTag($technologytag->tag);
			if (isset($tagsandweights[$tag->id]))
			{
				$tagsandweights[$tag->id]['weight']++;
			}
			else
			{
				$tagsandweights[$tag->id]['name'] = $tag->tag;
				$tagsandweights[$tag->id]['weight'] = 1;	
			}
		}
		
		foreach ($tagsandweights as $tagid => $tag)
		{
			$tagcloud->addElement(new Semtech_Tagcloud_Element($tag['name'], $tag['weight'], "/search/tag/$tagid"));
		}
		
		//$twitter = new Semtech_Twitter();
		//$this->view->tweets = $twitter->getStatusMessages();
		$this->view->tagcloud = $tagcloud;
	}
	
	public function contactAction()
	{
		$this->view->title = "Contact Us";
	}
}
