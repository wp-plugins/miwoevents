<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.plugin.plugin');
require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

class plgSearchMiwoevents extends MPlugin {
	
	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		
		$this->loadLanguage();
	}

	public function onContentSearchAreas()	{
		static $areas = array('miwoevents' => 'COM_MIWOEVENTS_EVENTS');
		
		return $areas;
	}

	public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null) {
		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}
		
		$text = trim($text);
		if ($text == '') {
			return array();
		}
		
		$db	= MFactory::getDBO();
		$user = MFactory::getUser();
		$limit = $this->params->get('search_limit', 50);
		$Itemid = '&Itemid='.MiwoEvents::get('utility')->getItemid();
		
		$section = MText::_('COM_MIWOEVENTS_EVENTS');

        $wheres = array();
		
		switch ($phrase) {
			case 'exact':
				$text = $db->Quote('%'.$db->escape($text, true).'%', false);

                $wheres[] 	= 'a.title LIKE '.$text;
                $wheres[] 	= 'a.introtext LIKE '.$text;
                $wheres[] 	= 'a.fulltext LIKE '.$text;
				
				$where = '(' . implode(') OR (', $wheres) . ')';

				break;
			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);

				foreach ($words as $word) {
					$word = $db->Quote('%'.$db->escape($word, true).'%', false);
					
					$wheres2 	= array();
					$wheres2[] 	= 'a.title LIKE '.$word;
					$wheres2[] 	= 'a.introtext LIKE '.$word;
					$wheres2[] 	= 'a.fulltext LIKE '.$word;

					$wheres[] 	= implode(' OR ', $wheres2);
				}
				
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';

				break;
		}
	
		switch ($ordering) {
			case 'oldest':
				$order = 'a.event_date ASC';
				break;		
			case 'alpha':
				$order = 'a.title ASC';
				break;
			case 'newest':
				$order = 'a.event_date ASC';
			default:
				$order = 'a.ordering';
		}
		
		$query = 'SELECT a.id, a.category_id AS cat_id, a.title AS title, a.introtext AS text, event_date AS `created`, '.$db->Quote($section).' AS section, "0" AS browsernav '
				.'FROM #__miwoevents_events AS a '
				.'WHERE ('.$where.') AND (a.access = 0 OR a.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')) AND a.published = 1 '
				.'ORDER BY '.$order;
				
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();
		
		if (count($rows)) {
			foreach($rows as $key => $row) {
				$rows[$key]->href = MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$row->id.$Itemid);
			}
		}
		
		return $rows;
	}	
}
