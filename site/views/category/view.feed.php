<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('MIWI') or die( 'Restricted access' );
mimport( 'joomla.application.component.view');

class MiwoeventsViewCategory extends MViewLegacy
{
	//Creates the Event Feed
	public function display() {
		$doc		= MFactory::getDocument();
		$jinput		= MFactory::getApplication()->input;
		$config		= MFactory::getConfig();
		
		$jinput->set('limit', $config->get('feed_limit'));
        $events 			= $this->get('Events');
		
		foreach ($events as $event) {
			// Strip html from feed item title
			$title = $this->escape($event->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');		

			// Url link to event
			$link = MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$event->id,$event->title);
			

			if (!empty($event->description)) $description = $event->introtext;
			
			@$created =  ($event->created == MFactory::getDbo()->getNullDate()) ? date( 'r', strtotime($event->start)) : date( 'r', strtotime($event->created));

			// load individual item creator class
            $item = new JFeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $description;
			$item->date			= $event->event_date;//@$created;
			// loads item info into rss array
			$doc->addItem( $item );
		}
	}
}