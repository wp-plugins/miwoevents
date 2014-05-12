<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

class MiwoEventsModelHistory extends MiwoeventsModel {

	public function __construct() {
		parent::__construct('history', 'attenders');

        $this->_getUserStates();
        $this->_buildViewQuery();
	}

    public function _getUserStates() {
        $this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'r.register_date',	'cmd');
        $this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'DESC',             'word');
        $this->filter_event	        = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_event', 	    'filter_event', 	    0,                  'int');
        $this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'',                 'string');
        $this->search 	 			= MString::strtolower($this->search);
    }

	public function _buildViewQuery() {
		$where = $this->_buildViewWhere();

        $orderby = "";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
        }

		$this->_query = 'SELECT r.*, e.title, e.event_date FROM #__miwoevents_attenders AS r INNER JOIN #__miwoevents_events AS e ON r.event_id = e.id '.$where.$orderby;
	}
	
	public function _buildViewWhere() {
		$user = MFactory::getUser();

		$where = array();

		$where[] = '(r.user_id ='.$user->get('id').')';

		if (!empty($this->filter_event)) {
			$where[] = 'r.event_id='.$this->filter_event;
        }

        if (!empty($this->search)) {
            $src = parent::secureQuery($this->search, true);
            $where[] = "(LOWER(r.fields) LIKE {$src})";
        }

		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');

		return $where;
	}

    public function getTotal(){
        if (empty($this->_total)) {
            $this->_total = MiwoDatabase::loadResult('SELECT COUNT(*) FROM #__miwoevents_attenders AS r '.$this->_buildViewWhere());
        }

        return $this->_total;
    }

    public function getAllEvents() {
        return MiwoDatabase::loadObjectList('SELECT id, title FROM #__miwoevents_events WHERE published = 1 ORDER BY title');
    }
    
    public function getTotalRegistrant() {
    	
    	# Get Events
    	$events = $this->getAllEvents();
    	
    	# Calculate Attenders
    	foreach ($events as $event){
    		$qty = MiwoDatabase::loadResult("SELECT COUNT(id) as total FROM #__miwoevents_attenders AS r WHERE event_id = {$event->id}");
    		$total['id'][]		= $event->id;
    		$total['title'][]	= $event->title;
    		$total['qty'][]		= $qty;
    	}
    	
    	return $total;
    }
    
    public function getCancel($id){
    	$this->_model->cancel($id);
    }
}