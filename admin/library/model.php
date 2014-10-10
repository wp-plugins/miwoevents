<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

mimport('framework.application.component.model');

if (!class_exists('MiwisoftModel')) {
	if (interface_exists('MModel')) {
		abstract class MiwisoftModel extends MModelLegacy {}
	}
	else {
		class MiwisoftModel extends MModel {}
	}
}

class MiwoeventsModel extends MiwisoftModel {
	
	public $_query;
	public $_id = null;
	public $_data = null;
	public $_total = null;
	public $_pagination = null;
	public $_context;
	public $_mainframe;
	public $_option;
	public $_table;
	
    public function __construct($context = '', $table = '') 	{
		parent::__construct();
		
		# Get config object
		$this->MiwoeventsConfig = MiwoEvents::getConfig();
		
		# Get global vars
		$this->_mainframe = MFactory::getApplication();
		if ($this->_mainframe->isAdmin()) {
			$this->_option = MiwoEvents::get('utility')->findOption();
		} else {
			$this->_option = MRequest::getCmd('option');
		}
		
		$this->_component = str_replace('com_', '', $this->_option);
		
		$this->_context = $context;
		$this->_modell = "7434YD56915";
		
		$this->_table = $table;
		if ($table == '' and $this->_context != '') {
			$this->_table = $this->_context;
		}
		
		# Pagination
		if ($this->_context != '') {
			# Get the pagination request variables
			$limit		= $this->_mainframe->getUserStateFromRequest($this->_option . '.' . $this->_context . '.limit', 'limit', $this->_mainframe->getCfg('list_limit'), 'int');
			$limitstart	= $this->_mainframe->getUserStateFromRequest($this->_option . '.' . $this->_context . '.limitstart', 'limitstart', 0, 'int');
			
			# Limit has been changed, adjust it
			$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
			
			$this->setState($this->_option . '.' . $this->_context . '.limit', $limit);
			$this->setState($this->_option . '.' . $this->_context . '.limitstart', $limitstart);
		}
	}

    public function setId($id) {
        $this->_id		= $id;
        $this->_data	= null;
    }
	
    public function _buildViewQuery() {
		$where = $this->_buildViewWhere();
		
		$orderby = "";
		if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
			$orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
		}
		
		$this->_query = "SELECT * FROM #__{$this->_component}_{$this->_table} {$where}{$orderby}";
	}
	
	public function _buildViewWhere() {
		return '';
	}
	
    public function getItems() {
		if (empty($this->_data)) {
			$this->_data = $this->_getList($this->_query, $this->getState($this->_option.'.' . $this->_context . '.limitstart'), $this->getState($this->_option.'.' . $this->_context . '.limit'));
		}
		
		return $this->_data;
	}
		
    public function getPagination() {
		if (empty($this->_pagination)) {
			mimport('framework.html.pagination');
			$this->_pagination = new MPagination($this->getTotal(), $this->getState($this->_option.'.' . $this->_context . '.limitstart'), $this->getState($this->_option.'.' . $this->_context . '.limit'));
		}
		
		return $this->_pagination;
	}
	
 	public function getTotal() {
		if (empty($this->_total)) {			
			$this->_total = MiwoDatabase::loadResult("SELECT COUNT(*) FROM #__{$this->_component}_{$this->_table}".$this->_buildViewWhere());	
		}
		
		return $this->_total;
	}
	
    public function getEditData($table = null) {
		# Get vars
		$cid = MRequest::getVar('cid', array(0), 'method', 'array');
		$id = $cid[0];

        if (empty($table)) {
            $table = ucfirst($this->_component).ucfirst($this->_context);
        }
		
		# Load the record
		if (is_numeric($id)) {
			$row = MiwoEvents::getTable($table); 
			$row->load($id);
		}
	
		return $row;
	}

    public function saveOrder($table) {
        $order = MRequest::getVar('order', array(), 'post');
        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($order);
        MArrayHelper::toInteger($cid);

        $row = MiwoEvents::getTable($table);
        $groupings = array();

        # update ordering values
        $n = count($cid);
        for ($i=0; $i < $n; $i++) {
            $row->load( (int) $cid[$i] );
            # track parents
            if ($this->_context == "categories"){
            	$groupings[] = $row->parent;
            }
            
            if ($row->ordering != $order[$i]) {
                $row->ordering = $order[$i];

                if (!$row->store()) {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
                }
            }
        }

        # execute updateOrder for each parent group
        $groupings = array_unique($groupings);
        foreach ($groupings as $group){
            $row->reorder('parent = '.(int) $group);
        }

        return true;
    }

    public function move($table, $direction) {
        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($cid);
        $id = $cid[0] ;

        $row = MiwoEvents::getTable($table);
        $row->load($id);
        
        $where = '';
        if ($this->_context == "categories"){
        	$where = ' parent = '.(int) $row->parent.' AND ';
        }

        if (!$row->move($direction, $where.' published >= 0 ')) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    public function copy($table) {
        $cid = MRequest::getVar('cid', array(), 'post');
        MArrayHelper::toInteger($cid);
        $id = $cid[0] ;

        $rowOld = MiwoEvents::getTable($table);
        $row = MiwoEvents::getTable($table);

        $rowOld->load($id) ;

        $data = MArrayHelper::fromObject($rowOld) ;
        $data['id'] = 0 ;
        $data['name'] = $data['name']. ' Copy';

        //Get next ordering
        if ($this->_context == "categories") {
        	$sql = "SELECT MAX(ordering + 1) FROM #__{$this->_component}_{$this->_table} WHERE parent = ".$rowOld->parent;
        } else {
        	$sql = "SELECT MAX(ordering + 1) FROM #__{$this->_component}_{$this->_table}";
        }
        
        $this->_db->setQuery($sql) ;
        $data['ordering'] = $this->_db->loadResult();

        $row->bind($data) ;
        $row->store();

        return true ;
    }
	
	public function secureQuery($text, $all = false) {
		static $db;
		
		if (!isset($db)) {
			$db = MFactory::getDBO();
		}
		
		$text = $db->escape($text, true);
		
		if ($all) {
			$text = $db->Quote("%".$text."%", false);
		} else {
			$text = $db->Quote($text, false);
		}
		
		return $text;
	}
	
	public function _getSecureUserState($long_name, $short_name, $default = null, $type = 'none') {
		$request = $this->_mainframe->getUserStateFromRequest($long_name, $short_name, $default, $type);
		
		if (is_string($request)) {
			$request = strip_tags(str_replace('"', '', $request));
		}
		
		return $request;
	}
}