<?php
class Semtech_FeedsController extends Semtech_Controller_Action
{

	public function init()
	{
		$this->_helper->layout->disableLayout();
	}

	public function recentAction()
	{
		$technologies = new Technologies();
		$select = $technologies->select()->limit(20)
						 ->order('date_added '.Zend_Db_Select::SQL_DESC);
		$technologies = $technologies->fetchAll($select);

		$feeddata = array(
			'title' => 'Recently Added Technologies',
			'link' => 'http://semtech.ecs.soton.ac.uk',
			'lastUpdate' => date("Y-m-d H:i:s", time()),
			'published' => date("Y-m-d H:i:s", time()),
			'charset' => 'UTF-8',
			'description' => 'These are the 20 most recently added technologies in the SemTech directory.',
		);

		foreach ($technologies as $technology) {
			$feeddata['entries'][] = array(
				'title' => $technology->name,
				'link' => 'http://quango.ecs.soton.ac.uk:8080/technology/view/id/'.$technology->id,
				'description' => $technology->description,
				'content' => '<p>Description:<br/>'.$technology->description.'</p><p>Intellectual Property Rights:<br/>'.$technology->iprights.'</p>'
			);
		}

		switch ($this->getRequest()->getParam('feed')) {
			case 'rss':
				$this->rssFeed($feeddata);
				break;
			case 'atom':
				$this->atomFeed($feeddata);
				break;
		}

		$this->getResponse()->setHeader("Content-Type", "xml");
		$this->view->technologies = $technologies;
	}

	private function rssFeed($data)
	{
		Zend_Feed::importArray($data, 'rss')->send();
	}

	private function atomFeed($data)
	{
		Zend_Feed::importArray($data)->send();
	}
}
