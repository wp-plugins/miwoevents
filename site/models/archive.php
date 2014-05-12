<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsModelArchive extends MiwoeventsModel {

	public function __construct() {
		parent::__construct('archive', 'events');

        $this->_buildViewQuery();
	}

	public function getItem() {
		if (empty($this->_data)) {
		    $nullDate = $this->_db->getNullDate();
            $user_id = MFactory::getUser()->get('id');
			
			$rows = $this->_getList($this->_query, $this->getState('limitstart'), $this->getState('limit'));
														
			if ($user_id) {
                $n = count($rows);

				for ($i = 0; $i < $n; $i++) {
					$row = $rows[$i];					
					$row->user_registered = MiwoDatabase::loadResult("SELECT COUNT(id) FROM #__miwoevents_attenders WHERE user_id = {$user_id} AND event_id = {$row->id}");

                    $row->total_attenders = MiwoEvents::get('attenders')->getTotalAttenders($row->id);

					if ($this->MiwoeventsConfig->show_discounted_price) {
					    $discount = 0;

					    if (($row->early_bird_discount_date != $nullDate) and ($row->date_diff >= 0)) {
            		        if ($row->early_bird_discount_type == 0) {            		                        		           
            					$discount += $row->individual_price * $row->early_bird_discount_amount / 100;
            				}
                            else {
            					$discount += $row->early_bird_discount_amount;
            				}
					    }
					    
					    $row->discounted_price = $row->individual_price - $discount;
						if ($row->discounted_price <= 0){ $row->discounted_price = 0; }
					}
				}
			}
            else {
                $n = count($rows);
                for ($i = 0; $i < $n; $i++) {
                    $row = $rows[$i];

                    $row->total_attenders = MiwoEvents::get('attenders')->getTotalAttenders($row->id);

                    if ($this->MiwoeventsConfig->show_discounted_price) {
                        $discount = 0;

                        if (($row->early_bird_discount_date != $nullDate) and ($row->date_diff >= 0)) {
                            if ($row->early_bird_discount_type == 0) {
                                $discount += $row->individual_price * $row->early_bird_discount_amount / 100;
                            }
                            else {
                                $discount += $row->early_bird_discount_amount;
                            }
                        }
                        $row->discounted_price = $row->individual_price - $discount;
                    }
                }
			}
            $this->_data = $rows;
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
			"SELECT e.*, DATEDIFF(e.early_bird_discount_date, NOW()) AS date_diff, l.title AS location_name, IFNULL(SUM(r.number_attenders), 0) AS total_attenders
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
        $category_id = MRequest::getInt('category_id', 0);
		
		$where = array() ;
		$where[] = 'e.published = 1';
		$where[] = 'e.access IN ('.implode(',', MFactory::getUser()->getAuthorisedViewLevels()).')';
		
		if ($category_id) {
			$where[] = 'e.id IN (SELECT event_id FROM #__miwoevents_event_categories WHERE category_id ='.$category_id.')';
		}
		
		$where[] = 'DATE(e.event_end_date) < CURDATE()';
		
		if (MFactory::getApplication()->getLanguageFilter()) {
			$where[] = 'e.language IN (' . $this->_db->Quote(MFactory::getLanguage()->getTag()) . ',' . $this->_db->Quote('*') . ')';
		}		
		
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		return $where;
	}	
} 