<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.plugin.plugin');

class plgMiwoEventsUnpublishEvents extends MPlugin {

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);		
	}
	
	public function onAfterStoreAttender($row) {
		$db = MFactory::getDbo();
		
		$db->setQuery('SELECT event_capacity FROM #__miwoevents_events WHERE id = '.$row->event_id);		
		$capacity = (int) $db->loadResult();
		
		if ($capacity > 0) {
			$sql = 'SELECT COUNT(*) FROM #__miwoevents_attenders WHERE event_id = '.$row->event_id.' AND status = 3';
			$db->setQuery($sql);
			$totalAttenders = (int) $db->loadResult();
			
			if ($totalAttenders >= $capacity) {
				$db->setQuery('UPDATE #__miwoevents_events SET published = 0 WHERE id='.$row->event_id);
				$db->query();
			}
		}
	}
}