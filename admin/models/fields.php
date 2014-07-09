<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsModelFields extends MiwoeventsModel {

    public function __construct() {
		parent::__construct('fields');

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
	
	public function _getUserStates(){
		$this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'ordering');
		$this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'ASC');
        $this->filter_display	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_display', 	    'filter_display', 	    '');
		$this->filter_type	        = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_type', 	        'filter_type', 	        '');
		$this->filter_published	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_published', 	'filter_published', 	'');
		$this->filter_language	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_language', 	    'filter_language', 	    '');
		$this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'');
		$this->search 	 			= MString::strtolower($this->search);
	}

    public function _buildViewWhere() {
		$where = array();			
		
		if ($this->search) {
            $src = parent::secureQuery($this->search, true);
			$where[] = "LOWER(title) LIKE {$src}";
		}

        if (is_numeric($this->filter_display)) {
            $where[] = 'display_in = '.(int) $this->filter_display;
        }

		if (!empty($this->filter_type)) {
			$where[] = 'field_type = "'.$this->filter_type.'"';
		}

		if (is_numeric($this->filter_published)) {
			$where[] = 'published = '.(int) $this->filter_published;
		}
		
		if ($this->filter_language) {
			$where[] = 'language IN (' . $this->_db->Quote($this->filter_language) . ',' . $this->_db->Quote('*') . ')';
		}
						
		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		return $where;
	}

	public function getEditData($table =NULL) {
		
		if (empty($this->_data)) {
			if (!empty($this->_id)) {
				$this->_data = parent::getEditData();
			} else {
				$row = parent::getEditData();
                $row->datatype_validation = 0;
	
				$this->_data = $row;
			}
		}
		return $this->_data;
	}
	
    public function store(&$data) {
		$row = MiwoEvents::getTable('MiwoeventsFields');
		
		# Display in : for Registration
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if (!$row->id) {
            $row->ordering = MiwoDatabase::loadResult('SELECT MAX(ordering) + 1 AS ordering FROM #__miwoevents_fields');

            if ($row->ordering == 0) {
                $row->ordering = 1;
            }
		} else {
			$row->ordering = MiwoDatabase::loadResult('SELECT ordering FROM #__miwoevents_fields WHERE id ='.$data['id']);
		}
		
		
			if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				$config = MiwoEvents::getConfig();
				$config->individual_fields->$data['name'] = 0;
				$config->group_fields->$data['name'] = 0;
				
				MiwoEvents::get('utility')->storeConfig($config);
				
				return true;
			}
			





















    












































































































	public function copy($id) {
		$db = MFactory::getDBO();
		
		$rowOld = MiwoEvents::getTable('MiwoeventsFields');
		$rowOld->load($id);

		$row = MiwoEvents::getTable('MiwoeventsFields');
		
		$data = get_object_vars($rowOld);
		$data['title'] 			= $data['title']." Copy";
		$data['name']			= $data['name']."_copy";
		
		
		$data = MArrayHelper::fromObject($rowOld);
		$row->bind($data);

		$row->id = 0;
		$row->title = $row->title.' Copy';
		$row->name = $row->name.'_copy';
		
		$row->store();
		return $row->id;
	}
}