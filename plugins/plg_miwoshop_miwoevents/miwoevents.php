<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.user.helper');
mimport('framework.plugin.plugin');

class plgMiwoshopMiwoevents extends MPlugin {

	public function __construct(& $subject, $config) {
		parent::__construct($subject, $config);

		require_once(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php');
		require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');
	}
	
	public function __destruct() {
		$_SESSION['meregid'] = NULL;
	}
	
    public function onMiwoshopAfterOrderSave($data, $products, $vouchers, $totals, $order_id, $notify) {
        if (!isset($_SESSION['meregid'])) {
			return;
        }
        
		$this->_updateAttendersUserAndOrderId($order_id);
    }

    public function onMiwoshopBeforeOrderConfirm($data, &$order_id, &$order_status_id, &$notify) {
        $this->_updateAttendersStatus($data, $order_id, $order_status_id, $notify);
    }

    public function onMiwoshopBeforeOrderStatusUpdate($data, $order_id, $order_status_id, $notify) {
        $this->_updateAttendersStatus($data, $order_id, $order_status_id, $notify);
    }

    public function onMiwoshopAfterOrderDelete($order_id) {
        $this->_deleteAttenders($order_id);
    }

    private function _updateAttendersUserAndOrderId($order_id){
        $db = MFactory::getDbo();
        $user = MFactory::getUser();
        
		$db->setQuery("SELECT * FROM #__miwoshop_order_product WHERE order_id = {$order_id}");
        $order_products = $db->loadObjectList();

        if (empty($order_products)) {
            return;
        }

        $user_id = $user->get('id');

        foreach($order_products as $order_product) {
            $event_id = MiwoEvents::get('events')->checkIsEventFromPlugin($order_product->product_id);
            $event_id = json_decode($event_id);
            $event_id = array_map('intval', $event_id);
            
			if (empty($event_id)) {
				continue;
			}
			
			$regEvents		= array_keys($_SESSION['meregid']);
			$regEvents 		= array_map('intval', $regEvents);
			$eventIntersect = array_intersect($event_id,$regEvents);
			
			foreach ($eventIntersect as $eventXid){
				$attenders = MiwoEvents::get('utility')->getAttenderIdsFromSession($eventXid);
				if (empty($attenders)) {
					continue;
				}
				
				$db->setQuery("UPDATE #__miwoevents_attenders SET order_id = {$order_id}, user_id = {$user_id} WHERE id IN ({$attenders})");
				$db->query();
				
			}
        }

        $eventIntersect = NULL;
    }

	private function _updateAttendersStatus($data, $order_id, $order_status_id, $notify) {
        $db = MFactory::getDbo();
        $app = MFactory::getApplication();
        $email = MiwoEvents::get('email');
		
        $db->setQuery("SELECT * FROM #__miwoshop_order_product WHERE order_id = {$order_id}");
        $order_products = $db->loadObjectList();

        if (empty($order_products)) {
            return;
        }

        foreach($order_products as $order_product) {
        	$attenders = MiwoEvents::get('events')->getAttenders($order_product->product_id, $order_id);
        	if (empty($attenders)) {
				continue;
			}

            $first_status_id = null;
			
			foreach ($attenders as $attender) {
				$status_id = $this->_getAttenderStatusId($order_status_id, $attender->event_id);

                if (is_null($first_status_id)) {
                    $first_status_id = $status_id;
                }
				
				$db->setQuery("UPDATE #__miwoevents_attenders SET status = {$status_id} WHERE id = {$attender->id}");
				$db->query();
        	}

            if ($notify != 1) {
                //return;
            }

            if ($app->isSite()) {
                $email->sendNewRegistration($order_id, $first_status_id);
            }
            else {
                if ($first_status_id == 3){
                    $email->sendRegistrationApproved($order_id);
                }
                elseif ($first_status_id == 5){
                    $email->sendCancelRegistration($order_id);
                }
                elseif (MiwoEvents::getConfig()->waitinglist_enabled and (($first_status_id == 11) or ($first_status_id == 12))){
                    $email->sendWaitinglist($order_id);
                }
            }
        }
    }

    private function _deleteAttenders($order_id) {
        if (empty($order_id)) {
            return;
        }

        $db = MFactory::getDbo();
        $db->setQuery("DELETE FROM  #__miwoevents_attenders  WHERE order_id = {$order_id}");
        $db->query();
    }

    public function _getAttenderStatusId($order_status_id, $event_id) {
    	/*
    	 * MiwoEvents Status
    	 *   1 : Pending
    	 *   3 : Paid
    	 *   5 : Cancel
    	 *   6 : Cancel Pending
    	 *  11 : Waiting
    	 *  12 : Waiting Pending
    	 */
		if ($order_status_id == MiwoEvents::getConfig()->paid_order_status) {
            if ($this->_isEventFull($event_id) == true) {
                $status_id = 12;
            }
            else {
                $status_id = 3;
            }
        }
        elseif ($order_status_id == MiwoEvents::getConfig()->pending_order_status) {
        	if ($this->_isEventFull($event_id) == true) {
                $status_id = 11;
            }
            else {
                $status_id = 1;
            }
        }
        else {
            $status_id = 5;
        }

        return $status_id;
    }

    public function _isEventFull($event_id) {
        $result = MiwoDatabase::loadObject("SELECT e.event_capacity, COUNT(a.id) AS number_attenders FROM #__miwoevents_attenders AS a LEFT JOIN #__miwoevents_events AS e ON (e.id = a.event_id) WHERE e.id = {$event_id} AND a.status = 3");

        if ($result->event_capacity == 0) {
            return false;
        }

        if ($result->number_attenders >= $result->event_capacity) {
            return true;
        }

		return false;
    }
}