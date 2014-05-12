<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class MiwoeventsAttenders {

    public function __construct() {
		$this->MiwoeventsConfig = MiwoEvents::getConfig();
	}

    public function getAttender($id) {
        static $cache = array();

        if (!isset($cache[$id])) {
            $cache[$id] = MiwoDatabase::loadObject('SELECT * FROM #__miwoevents_attenders WHERE id = '.$id);
        }

        return $cache[$id];
    }

    public function getTotalAttenders($event_id = 0, $status = 3) {
        static $cache = array();

        if (!isset($cache[$event_id][$status])) {
			$where = array();
		
            if ($event_id != 0) {
                $where[] = 'event_id = '.$event_id;
            }
		
            if ($status != 0) {
                $where[] = 'status = '.$status;
            }

			$where = count($where) ? ' WHERE '. implode(' AND ', $where) : '';

            $cache[$event_id][$status] = (int)MiwoDatabase::loadResult('SELECT COUNT(*) AS total_attenders FROM #__miwoevents_attenders '.$where);
        }

        return $cache[$event_id][$status];
    }
	
	public function approveNextWaitingAttender() {
		$next_status = 3;
		$next_waiting = $this->getNextAttenderByStatus(12);
		
		if (empty($next_waiting)) {
			$next_status = 1;
			$next_waiting = $this->getNextAttenderByStatus(11);
		}
		
		if (empty($next_waiting)) {
			return false;
		}
		
		$row = MiwoEvents::getTable('MiwoeventsAttenders');
		$row->load($next_waiting->id);
		$row->set('status', $next_status);
		$row->store();
		
		MiwoEvents::get('email')->sendRegistrationApproved($row->order_id);
		
		return $row;
	}

    public function getNextAttenderByStatus($status = 11) {
        return MiwoDatabase::loadObject("SELECT * FROM #__miwoevents_attenders WHERE status = {$status} ORDER BY id ASC LIMIT 1");
    }

    public function cancelRegistration($id) {
        $row = MiwoEvents::getTable('MiwoeventsAttenders');
        $row->load($id);

        if (!is_object($row)) {
            return false;
        }

        $event = MiwoDatabase::loadObject("SELECT enable_cancel_registration FROM #__miwoevents_events WHERE (DATEDIFF(cancel_before_date, NOW()) >= 0) and id = {$row->event_id} LIMIT 1");

        if (!is_object($event)) {
            return false;
        }

        if (($event->enable_cancel_registration == 0) or !in_array($row->status, array(1, 3, 11, 12))) {
            return false;
        }

        $row->status = 5;

        if ($row->check()) {
            $row->store();
        }

        MiwoEvents::get('email')->sendCancelRegistration($row);

        MiwoEvents::get('utility')->trigger('onRegistrationCancel', array(&$row));

        return true;
    }

    public function getFieldsObject($row) {
        return json_decode($row->fields, true);
    }

    public function getFieldValue($row, $field_name) {
        $ret = '';

        $fields = $this->getFieldsObject($row);

        if (empty($fields)) {
            return $ret;
        }

        if (isset($fields->$field_name)) {
            $ret = $fields->$field_name;
        }

        return $ret;
    }

    public function getFirstName($row) {
        return $this->getFieldValue($row, $this->MiwoeventsConfig->firstname_field);
    }

    public function getLastName($row) {
        return $this->getFieldValue($row, $this->MiwoeventsConfig->lastname_field);
    }

    public function getEmail($row) {
        return $this->getFieldValue($row, $this->MiwoeventsConfig->email_field);
    }
}