<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsModelLocation extends MiwoeventsModel {

    public function __construct() {
		parent::__construct('location', 'locations');

        $this->location_id = MiwoEvents::getInput()->getInt('location_id', 0);

        $task = MiwoEvents::getInput()->getCmd('task', '');
        $layout = MiwoEvents::getInput()->getCmd('layout', '');
        $tasks = array('edit', 'apply', 'save', 'save2new');
        if (in_array($task, $tasks) or ($layout == 'submit')) {
            $this->setId((int)$this->location_id);

            require_once(MPATH_MIWOEVENTS_ADMIN.'/models/locations.php');
            $this->_admin_model = new MiwoEventsModelLocations();
        }
	}

    public function getItem() {
        $location_id = MRequest::getInt('location_id');

        $row = MiwoEvents::getTable('MiwoeventsLocations');
        $row->load($location_id);
        $row->fields = $this->getLocationFields($row->id, "yes");

        return $row;
    }

    public function getEvents() {
        $user = MFactory::getUser();
        $nullDate = $this->_db->getNullDate();

        $query = $this->_buildEventsQuery();
        $this->_db->setQuery($query);

        $rows = MiwoDatabase::loadObjectList($query, $this->getState('limitstart'), $this->getState('limit'));
        if ($user->get('id')) {
            $user_id = $user->get('id');

            $n = count($rows) ;
            for ($i = 0; $i < $n; $i++) {
                $row = $rows[$i];

                $row->user_registered = MiwoDatabase::loadResult('SELECT COUNT(id) FROM #__miwoevents_attenders WHERE user_id = '.$user_id.' AND event_id = '.$row->id);

                $row->total_attenders = MiwoEvents::get('attenders')->getTotalAttenders($row->id);

                //Canculate discount price
                if ($this->MiwoeventsConfig->show_discounted_price) {
                    $discount = 0 ;
                    if (($row->early_bird_discount_date != $nullDate) and ($row->date_diff >= 0)) {
                        if ($row->early_bird_discount_type == 1) {
                            $discount += $row->individual_price * $row->early_bird_discount_amount / 100;
							$n_discount += $row->individual_price * 15 / 740;
                        }
                        else {
                            $discount += $row->early_bird_discount_amount;
                        }
                    }

                    $row->discounted_price = $row->individual_price - $discount;
                }
            }
        }
        else {
            //Calculate discounted price
            if ($this->MiwoeventsConfig->show_discounted_price) {
                $n = count($rows) ;

                for ($i = 0; $i < $n; $i++) {
                    $row = $rows[$i] ;
                    if ($this->MiwoeventsConfig->show_discounted_price) {
                        $discount = 0;
                        if (($row->early_bird_discount_date != $nullDate) and ($row->date_diff >= 0)) {
                            if ($row->early_bird_discount_type == 1) {
                                $discount += $row->individual_price*$row->early_bird_discount_amount / 100;
                            }
                            else {
                                $discount += $row->early_bird_discount_amount;
                            }
                        }
                        $row->discounted_price = $row->individual_price - $discount;
                    }
                }
            }
        }

		return $rows;
	}

    public function getEventsPagination() {
        mimport('framework.html.pagination');
        $pagination = new MPagination($this->getEventsTotal(), $this->getState($this->_option.'.' . $this->_context . '.limitstart'), $this->getState($this->_option.'.' . $this->_context . '.limit'));

        return $pagination;
    }

    public function getEventsTotal() {
        $total = MiwoDatabase::loadResult('SELECT COUNT(*) FROM #__miwoevents_events AS e '.$this->_buildEventsWhere());

        return $total;
    }

    public function _buildEventsQuery() {
		$where = $this->_buildEventsWhere();

        if ($this->MiwoeventsConfig->order_events == 2) {
            $orderby = ' ORDER BY e.event_date ';
        }
        else {
            $orderby = ' ORDER BY e.ordering ';
        }

        $query = 'SELECT e.*, DATEDIFF(e.early_bird_discount_date, NOW()) AS date_diff, l.title AS location_name, IFNULL(SUM(r.number_attenders), 0) AS total_attenders'
                . ' FROM  #__miwoevents_events AS e '
			    . ' LEFT JOIN #__miwoevents_attenders AS r '
			    . ' ON (e.id = r.event_id = 1 )'
			    . ' LEFT JOIN #__miwoevents_locations AS l '
			    . ' ON e.location_id = l.id '
			    . $where
			    . ' GROUP BY e.id '
			    . $orderby
		        ;

		return $query;
	}

    public function _buildEventsWhere() {
		$db = MFactory::getDbo();
		$user = MFactory::getUser();
        $app = MFactory::getApplication();
        $location_id = MRequest::getInt('location_id', 0);

		$where = array();

		$where[] = 'e.published = 1';
		$where[] = 'e.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';

		if ($location_id) {
			$where[] = 'e.location_id = ' .$location_id;
		}

		if ($this->MiwoeventsConfig->hide_past_events) {
			$where[] = 'DATE(e.event_date) >= CURDATE()';
		}

		if ($app->getLanguageFilter()) {
			$where[] = 'e.language IN (' . $db->Quote(MFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
		}

		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
				
		return $where;
	}

    public function getEditData($table = NULL) {
        if (empty($this->_data)) {
            $row = MiwoEvents::getTable('MiwoEventsLocations');
            $row->load($this->_id);
            $this->_data = $row;
        }

        return $this->_data;
    }

    public function store(&$data) {
        return $this->_admin_model->store($data);
    }

    public function getLocationFields($locationId, $clear = NULL) {
        # General Settings
        $app	= MFactory::getApplication();
        $db		= MFactory::getDBO();
        $user	= MFactory::getUser();
        $userId = $user->get('id');

        $sql = "SELECT fields FROM #__miwoevents_locations WHERE id = $locationId";
        $db->setQuery($sql);
        $locationFields = $db->loadResult();

        if (empty($locationFields)) {
            return null;
        }

        $locationFields = json_decode($locationFields);

        if (!empty($locationFields)){
            foreach ($locationFields as $key => $eventField){
                $sql = "
	                SELECT
	                    f.ordering, f.name, f.title, f.description, f.field_type, f.values, f.default_values, f.rows, f.cols, f.size, f.css_class
	                FROM #__miwoevents_fields f
	                WHERE f.display_in = 2 AND f.name = '$key'
	                ORDER BY f.ordering
	                ";
                $db->setQuery($sql);
                $obj = $db->loadObject();
                $obj->field_value = $eventField;
                $rows[] = $obj;
            }

            # sorting Array From ordering
            asort($rows);
        }

        if ($clear == "yes") {
            if(empty($rows)){ return; } else { return $rows;}
        }
        else {
            if(empty($rows)){ return; }

            foreach ($rows as $row){
                $x[] =  MiwoEvents::get('fields')->getCustomField($row->name, $row->field_value, $row->values, $row->field_type, $row->title, $row->description);
            }

            return $x;
        }
    }
} 