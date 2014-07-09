<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;
class MiwoeventsModelCategory extends MiwoeventsModel {

	public function __construct() {
		parent::__construct('category', 'events');

        $this->_buildViewQuery();

        $this->category_id = MiwoEvents::getInput()->getInt('category_id', 0);

        $task = MiwoEvents::getInput()->getCmd('task', '');
        $layout = MiwoEvents::getInput()->getCmd('layout', '');
        $tasks = array('edit', 'apply', 'save', 'save2new');
        if (in_array($task, $tasks) or ($layout == 'submit')) {
            $this->setId((int)$this->category_id);

            require_once(MPATH_MIWOEVENTS_ADMIN.'/models/categories.php');
            $this->_admin_model = new MiwoEventsModelCategories();
        }
	}

    public function getEvents() {
		if (empty($this->_data)) {
		    $user = MFactory::getUser();
			$nullDate = $this->_db->getNullDate();

			$rows = parent::getItems();
			
			if ($user->get('id')) {
				$user_id = $user->get('id');

                $n = count($rows);
				for ($i = 0; $i < $n; $i++) {
					$row = $rows[$i];

					$row->user_registered = MiwoDatabase::loadResult('SELECT COUNT(id) FROM #__miwoevents_attenders WHERE user_id = '.$user_id.' AND event_id = '.$row->id);

                    $row->total_attenders = MiwoEvents::get('attenders')->getTotalAttenders($row->id);
					
					# Canculate discount price
					if ($this->MiwoeventsConfig->show_discounted_price) {
					    $discount = 0;

					    if (($row->early_bird_discount_date != $nullDate) and ($row->date_diff >= 0)) {
            		        if ($row->early_bird_discount_type == 1) {            		                        		           
            					$discount += $row->individual_price * $row->early_bird_discount_amount / 100;
            				}
                            else {
            					$discount += $row->early_bird_discount_amount ;
            				}            				
					    }

            			$row->discounted_price = $row->individual_price - $discount;
					}					
				}
			}
            else {
			    # Calculate discounted price
                $n = count($rows);
			    for ($i = 0; $i < $n ; $i++) {
					$row = $rows[$i];

                    $row->total_attenders = MiwoEvents::get('attenders')->getTotalAttenders($row->id);

                    if ($this->MiwoeventsConfig->show_discounted_price) {
                        $discount = 0;

                        if (($row->early_bird_discount_date != $nullDate) and ($row->date_diff >=0)) {
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

	public function _buildViewQuery() {
		$where = $this->_buildViewWhere();

        if ($this->MiwoeventsConfig->order_events == 2) {
            $orderby = ' ORDER BY e.event_date ';
        }
        else {
            $orderby = ' ORDER BY e.ordering ';
        }
        
        $this->_query = 'SELECT e.*, DATEDIFF(e.early_bird_discount_date, NOW()) AS date_diff, l.title AS location_name, IFNULL(SUM(r.number_attenders), 0) AS total_attenders '
                        .' FROM #__miwoevents_events AS e '
                        .' LEFT JOIN #__miwoevents_attenders AS r '
                        .' ON (e.id = r.event_id) '
                        .' LEFT JOIN #__miwoevents_locations AS l '
                        .' ON e.location_id = l.id '
                        .$where
                        .' GROUP BY e.id '
                        .$orderby;
	}

    public function _buildViewWhere() {
        $category_id = MRequest::getInt('category_id');

		$where = array() ;

		$where[] = 'e.published = 1';
		$where[] = 'e.access IN ('.implode(',', MFactory::getUser()->getAuthorisedViewLevels()).')';

		if ($this->_mainframe->getLanguageFilter()) {
			$where[] = 'e.language IN (' . $this->_db->Quote(MFactory::getLanguage()->getTag()) . ',' . $this->_db->Quote('*') . ')';
		}

		if ($category_id) {
			$where[] = 'e.id IN (SELECT event_id FROM #__miwoevents_event_categories WHERE category_id='.$category_id.')';
		}

		if ($this->MiwoeventsConfig->hide_past_events) {
			$where[] = 'DATE(e.event_date) >= CURDATE()';
		}

		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
				
		return $where;
	}

    public function getTotal() {
        if (empty($this->_total)) {
            $this->_total = MiwoDatabase::loadResult("SELECT COUNT(*) FROM #__miwoevents_{$this->_table} AS e".$this->_buildViewWhere());
			// #__miwoevents_{$this->_table}
        }

        return $this->_total;
    }
    
    public function getCategories() {
		$rows = MiwoDatabase::loadObjectList($this->_buildCategoriesQuery());
		
		$n = count($rows);
		for ($i  = 0; $i < $n; $i++) {				
			$row = &$rows[$i];
			
			$row->total_categories = MiwoDatabase::loadResult('SELECT COUNT(*) FROM #__miwoevents_categories WHERE parent = '.$row->id.' AND published = 1');
			$row->total_events = MiwoEvents::get('events')->getTotalEventsByCategory($row->id);
		}
		
		return $rows;
	}
	
    public function _buildCategoriesQuery() {
		$where = $this->_buildCategoriesWhere();

		$query = 'SELECT * FROM #__miwoevents_categories '.$where.' ORDER BY ordering';
		
		return $query;
	}
	
    public function _buildCategoriesWhere() {
        $category_id = MRequest::getInt('category_id');

		$where = array() ;		

		$where[] = 'parent = '.$category_id;
		$where[] = 'access IN ('.implode(',', MFactory::getUser()->getAuthorisedViewLevels()).')';
		$where[] = 'published = 1';
		
		if ($this->_mainframe->getLanguageFilter()) {
			$where[] = 'language IN (' . $this->_db->Quote(MFactory::getLanguage()->getTag()) . ',' . $this->_db->Quote('*') . ')';
		}
		
		$where = (count( $where) ? ' WHERE '. implode(' AND ', $where ) : '');
				
		return $where;
	}

    public function getEditData($table = NULL) {
        if (empty($this->_data)) {
            $row = MiwoEvents::getTable('MiwoEventsCategories');
            $row->load($this->_id);

            $this->_data = $row;
        }

        return $this->_data;
    }

    public function store(&$data) {
        return $this->_admin_model->store($data);
    }
} 