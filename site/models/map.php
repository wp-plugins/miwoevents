<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsModelMap extends MiwoeventsModel {

	public function __construct() {
		parent::__construct('map', 'events');

        $this->_buildViewQuery();
	}

	public function getItems() {
		if (empty($this->_data)) {
            $this->_data = $this->_getList($this->_query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}
	

	public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDatabase::loadResult("SELECT COUNT(*) FROM #__miwoevents_events AS e {$this->_buildViewWhere()}");
		}

		return $this->_total;
	}

	public function _buildViewQuery() {
        $where = $this->_buildViewWhere();
        $orderBy = "ORDER BY e.event_date DESC";

		$this->_query =
			"SELECT e.*, DATEDIFF(e.early_bird_discount_date, NOW()) AS date_diff, l.title AS location_title, l.address, l.coordinates, IFNULL(SUM(r.number_attenders), 0) AS total_attenders
			FROM #__miwoevents_events AS e
			LEFT JOIN #__miwoevents_attenders AS r
				ON (e.id = r.event_id)
			LEFT JOIN #__miwoevents_locations AS l
			ON e.location_id = l.id
			{$where}
			GROUP BY e.id
			{$orderBy}";
	}
	
	public function _buildViewWhere() {
        $params = $this->_mainframe->getParams();

		$where = array() ;
		
		$where[] = 'e.published = 1';
		$where[] = 'e.access IN ('.implode(',', MFactory::getUser()->getAuthorisedViewLevels()).')';
		
		if ($this->_mainframe->getLanguageFilter()) {
			$where[] = 'e.language IN (' . $this->_db->Quote(MFactory::getLanguage()->getTag()) . ',' . $this->_db->Quote('*') . ')';
		}
		
		if ($this->MiwoeventsConfig->hide_past_events) {
			$where[] = 'DATE(e.event_date) >= CURDATE()';
		}	
		
        $categories = $params->get('categories', 0);
		if (!empty($categories)) {
            $categories = implode(',', $categories);

			$where[] = 'e.id IN (SELECT event_id FROM #__miwoevents_event_categories WHERE category_id IN ('.$categories.'))';
		}

        $locations = $params->get('locations', 0);
		if (!empty($locations)) {
            $locations = implode(',', $locations);

			$where[] = 'e.location_id IN ('.$locations.')';
		}	
		
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		return $where;
	}	
} 