<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsModelCategories extends MiwoeventsModel {

    public function __construct() {
		parent::__construct('categories');

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
        $this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'c.title');
        $this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'ASC');
        $this->filter_parent	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_parent', 	    'filter_parent', 	    '0');
        $this->filter_published	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_published', 	'filter_published', 	'');
        $this->filter_access	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_access', 	    'filter_access', 	    '');
        $this->filter_language	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_language', 	    'filter_language', 	    '');
        $this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'');
        $this->search 	 			= MString::strtolower($this->search);
    }

    public function _buildViewQuery() {
        $where = self::_buildViewWhere();

        $orderby = "";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY c.parent, {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = 'SELECT c.*, c.parent AS parent_id, c.title AS title, COUNT(ec.id) AS total_events '.
                        'FROM #__miwoevents_categories AS c '.
                        'LEFT JOIN #__miwoevents_event_categories AS ec '.
                        'ON c.id = ec.category_id '.
                        $where.' '.
                        'GROUP BY c.id '.
                        $orderby;
    }

    public function _buildViewWhere() {
        $where = array();

        if (!empty($this->search)) {
            $src = parent::secureQuery($this->search, true);
            $where[] = "(LOWER(c.title) LIKE {$src} OR LOWER(c.introtext) LIKE {$src} OR LOWER(c.fulltext) LIKE {$src})";
        }

        if (!empty($this->filter_parent)) {
            $where[] = "c.parent = {$this->filter_parent}";
        }

        if (is_numeric($this->filter_published)) {
            $where[] = 'c.published = '.(int) $this->filter_published;
        }

        if (is_numeric($this->filter_access)) {
            $where[] = 'c.access = '.(int) $this->filter_access;
        }

        if ($this->filter_language) {
            $where[] = 'c.language IN (' . $this->_db->Quote($this->filter_language) . ',' . $this->_db->Quote('*') . ')';
        }

        $where = (count( $where ) ? ' WHERE '. implode(' AND ', $where) : '');

        return $where;
    }

    public function getItems() {
		if (empty($this->_data)) {
			$rows = parent::getItems();

			$children = array();
			
			if (count($rows)) {
				foreach ($rows as $v) {
					$pt = $v->parent;
					$list = @$children[$pt] ? $children[$pt] : array();
					array_push($list, $v);
					$children[$pt] = $list;
				}	
			}

			$list = MHtml::_('menu.treerecurse', $this->filter_parent, '', array(), $children, 9999);

			$pagination = parent::getPagination();
			$list = array_slice($list, 0, $pagination->limit); //$pagination->limitstart

			$this->_data = $list;
		}

		return $this->_data;
	}

	public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDatabase::loadResult("SELECT COUNT(*) FROM #__miwoevents_{$this->_table} AS c".$this->_buildViewWhere());
		}
	
		return $this->_total;
	}

    public function getEditData($table = NULL) {
        if (empty($this->_data)) {
            $row = parent::getEditData();

            if (empty($this->_id) and !empty($this->filter_parent)) {
                $row->parent = $this->filter_parent;
            }

            $this->_data = $row;
        }

        return $this->_data;
    }
    
	public function copy($id) {
		$db = MFactory::getDBO();
		
		$rowOld = MiwoEvents::getTable('MiwoeventsCategories');
		$rowOld->load($id);

		$row = MiwoEvents::getTable('MiwoeventsCategories');
		
		$data = get_object_vars($rowOld);
		$data['title'] 			= $data['title']." Copy";
		$data['alias']			= $data['alias']."-copy";
		$data['description']	= "";		
		
		$data = MArrayHelper::fromObject($rowOld);
		$row->bind($data);

		$row->id = 0;
		$row->title = $row->title.' Copy';
		$row->alias = $row->alias.'-copy';
		$row->description = "";
		
		$row->store();
		return $row->id;
	}

    public function store(&$data) {
        $row = MiwoEvents::getTable('MiwoeventsCategories');

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