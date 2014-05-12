<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsModelLocations extends MiwoeventsModel {
	
	public function __construct() {
		parent::__construct('locations');

        $task = MRequest::getCmd('task');
        $tasks = array('edit', 'apply', 'save', 'save2new');

        if (in_array($task, $tasks)) {
            $cid = MRequest::getVar('cid', array(0), '', 'array');
			$this->setId((int)$cid[0]);
		}
		else {
			$this->_getUserStates();
			$this->_buildViewQuery();
		}
	}
	
	public function _getUserStates() {
		$this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'title');
		$this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'ASC');
		$this->filter_language		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_language', 		'filter_language', 		'');
		$this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'');
		$this->search 	 			= MString::strtolower($this->search);
	}
	
	public function _buildViewWhere() {
		$where = array();

		if ($this->search) {
            $src = parent::secureQuery($this->search, true);
			$where[] = "(LOWER(title) LIKE {$src} OR LOWER(address) LIKE {$src})";
		}
		
		if ($this->filter_language) {
			$where[] = 'language IN (' . $this->_db->Quote($this->filter_language) . ',' . $this->_db->Quote('*') . ')';
		}
				
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where ) : '');
			
		return $where;
	}

	public function getEditData($table = NULL) {
		if (empty($this->_data)) {
                $row = parent::getEditData();
				$this->_data = $row;
		}

		return $this->_data;
	}

	public function store(&$data) {
        $row = MiwoEvents::getTable('MiwoeventsLocations');
        
        if (empty($data['user_id'])) {
			$data['user_id'] = MFactory::getUser()->get('id');
        }

		if ($data['id']) {
			$row->load($data['id']);
        }

        if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

        if (!$row->check($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

        $data['id'] = $row->id;

		return true;
	}
}