<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

mimport('framework.application.component.controller');

if (!class_exists('MiwisoftController')) {
	if (interface_exists('MController')) {
		abstract class MiwisoftController extends MControllerLegacy {}
	}
	else {
		class MiwisoftController extends MController {}
	}
}

class MiwoeventsController extends MiwisoftController {
	
	public $_mainframe;
	public $_option;
	public $_component;
	public $_context;
	public $_table;
	public $_model;
	
    public function __construct($context = '', $table = '') 	{
		parent::__construct();
		
		$view = MRequest::getCmd('view');
		
		if (empty($context) and !empty($view)) {
			$context = $view;
		}
		
		$this->_mainframe = MFactory::getApplication();
		if ($this->_mainframe->isAdmin()) {
			$this->_option = MiwoEvents::get('utility')->findOption();
		}
		else {
			$this->_option = MRequest::getCmd('option');
		}
		
		$this->_component = str_replace('com_', '', $this->_option);
		
		$this->_context = $context;
		
		$this->_table = $table;
		if ($this->_table == '') {
			$this->_table = $this->_context;
		}
		
		$this->_model = $this->getModel($context);
		$this->MiwoeventsConfig = MiwoEvents::getConfig();
		
		# Register tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('remove', 'delete');
	}
	
    public function display($cachable = false, $urlparams = false) {
        $layout = MRequest::getCmd('layout');
        $viewType = MFactory::getDocument()->getType();

        $function = 'display'.ucfirst($layout);

        $view = $this->getView(ucfirst($this->_context), $viewType);
        $view->setModel($this->_model, true);

        if (!empty($layout)) {
            $view->setLayout($layout);
        }

        $view->$function();
    }

	public function edit() {
		MRequest::setVar('hidemainmenu', 1);
		
		$view = $this->getView(ucfirst($this->_context), 'edit');
		$view->setModel($this->_model, true);
		$view->display('edit');
	}
	
	public function route($msg = "", $post = array()){
        if ($this->_mainframe->isSite()) {
            $this->routeFront($msg, $post);
            return;
        }

        $task = $this->getTask();

		if ($task == 'apply') {
			parent::setRedirect('index.php?option='.$this->_option.'&view='.$this->_context.'&task=edit&cid[]='.$post['id'], $msg);
		}
        else if ($task == 'save2new') {
			parent::setRedirect('index.php?option='.$this->_option.'&view='.$this->_context.'&task=add', $msg);
		}
        else {
			parent::setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
		}
	}

	public function routeFront($msg = "", $post = array()){
        $task = $this->getTask();

        switch ($this->_context) {
            case 'location':
                $id_var = 'location_id';
                break;
            case 'category':
                $id_var = 'category_id';
                break;
            case 'event':
            default:
                $id_var = 'event_id';
                break;
        }

        if ($task == 'apply') {
            parent::setRedirect('index.php?option='.$this->_option.'&view='.$this->_context.'&layout=submit&'.$id_var.'='.$post['id'].'&Itemid='.$post['Itemid'], $msg);
        }
        else if ($task == 'save2new') {
            parent::setRedirect('index.php?option='.$this->_option.'&view='.$this->_context.'&layout=submit&Itemid='.$post['Itemid'], $msg);
        }
        else {
            parent::setRedirect('index.php?option='.$this->_option.'&view=category', $msg);
        }
	}
	
	# Delete
	public function delete() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		# Action
		if (self::deleteRecord($this->_table, $this->_model) != true) {
			$msg = MText::_('COM_MIWOEVENTS_COMMON_RECORDS_DELETED_NOT');
		} else {
			$msg = MText::_('COM_MIWOEVENTS_COMMON_RECORDS_DELETED');
		}
		
		# Return
		self::route($msg);
	}
	
	public function deleteRecord($table, $model, $where = true) {
		if ($where === true) {
			$where = self::_getWhere($model);
		}
		
		if (MiwoDatabase::query("DELETE FROM #__{$this->_component}_{$table}{$where}")) {
			return true;
		} else  {
			return false;
		}
    }
	
	public function _getWhere($model, $prefix = "") {
        $where = '';
		
        $sel = MRequest::getVar('selection', 'selected', 'post');
        if ($sel == 'selected') {
            $where = self::_buildSelectedWhere($prefix);
        } elseif ($sel == 'filtered') {
            $where = $model->_buildViewWhere($prefix);
        }
        
        return $where;
    }
	
	# Get the id's of selected records
	public function _buildSelectedWhere($prefix = "") {
		$cid = MRequest::getVar('cid', array(), 'post', 'array');
		MArrayHelper::toInteger($cid);
		
		$where = '';
		if(count($cid) > 0){
			$where = " WHERE {$prefix}id IN (".implode(',',$cid).")";
		}

		return $where;
	}
	
	# Publish
	public function publish() {
		# Check token
		//MRequest::checkToken() or mexit('Invalid Token');
		
		# Action
		self::updateField($this->_table, 'published', 1, $this->_model);
		
		# Return
		self::route();
	}
	
	# Unpublish
	public function unpublish() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		# Action
		self::updateField($this->_table, 'published', 0, $this->_model);
		
		# Return
		self::route();
	}
	
   	# Save changed record
	public function saveRecord($post, $table, &$id = 0) {
		# Get row
		$row = MiwoEvents::getTable($table);
		
		# Bind the form fields to the table
		if (!$row->bind($post)) {
			return MError::raiseWarning(500, $row->getError());
		}
		
		# Make sure the record is valid
		if (!$row->check()) {
			return MError::raiseWarning(500, $row->getError());
		}
		
		# Save record
		if (!$row->store()) {
			return MError::raiseWarning(500, $row->getError());
		}
		
		if (empty($id)) {
			$id = $row->id;
		}
		
		return true;
	}
	
	# Save changes
	public function save() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		# Get post
		$post = MRequest::get('post', MREQUEST_ALLOWRAW);

        $cid = $post['cid'];
        $post['id'] = (int) $cid[0];
		
		# Save record
		$table = ucfirst($this->_component).ucfirst($this->_context);
		
		if (!self::saveRecord($post, $table, $post['id'])) {
			return MError::raiseWarning(500, MText::_('COM_MIWOEVENTS_COMMON_RECORD_SAVED_NOT'));
		}
        else {
            self::route(MText::_('COM_MIWOEVENTS_COMMON_RECORD_SAVED'), $post);
		}
	}
	
	# Update field
	public function updateField($table, $field, $value, $model, $where = true) {
		if ($where === true) {
			$where = self::_getWhere($model);
		}
		
		if (!MiwoDatabase::query("UPDATE #__{$this->_component}_{$table} SET {$field} = '{$value}' {$where}")) {
			return false;
		}

		return true;
	}
	
	# Update param
	public function updateParam($table, $table_m, $field, $param, $value, $model, $where = true) {
		if (!$ids = self::_getIDs($table, $model, $where)) {
			return;
		}
		
		$row = MiwoEvents::getTable($table_m);
		
		if (!empty($ids) && is_array($ids)) {
			foreach ($ids as $index => $id) {
				if (!$row->load($id)) {
					continue;
				}
				
				$params = new MRegistry($row->$field);
				$params->set($param, $value);
				
				$row->$field = $params->toString();
				
				if (!$row->check()) {
					continue;
				}
				
				if (!$row->store()) {
					continue;
				}
			}
		}
	}
	
	# Get IDs
	public function _getIDs($table, $model, $where = true) {
		if ($where === true) {
			$where = self::_getWhere($model);
		}
		
		if (!$ids = MiwoDatabase::loadResultArray("SELECT id FROM #__{$this->_component}_{$table} {$where}")) {
			return false;
		}
		
		return $ids;
	}

    public function saveOrder() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $ret = $this->_model->saveOrder(ucfirst($this->_component).ucfirst($this->_context));
        if ($ret) {
            $msg = MText::_('COM_MIWOEVENTS_ORDERING_SAVED');
        }
        else {
            $msg = MText::_('COM_MIWOEVENTS_ORDERING_SAVING_ERROR');
        }

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
    }

    public function orderUp() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->move(ucfirst($this->_component).ucfirst($this->_context), -1);

        $msg = MText::_('COM_MIWOEVENTS_ORDERING_UPDATED');

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
    }

    public function orderDown() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->move(ucfirst($this->_component).ucfirst($this->_context), 1);

        $msg = MText::_('COM_MIWOEVENTS_ORDERING_UPDATED');

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
    }

    public function copy() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->copy(ucfirst($this->_component).ucfirst($this->_context));

        $msg = MText::_('COM_MIWOEVENTS_RECORD_COPIED');

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
    }

    public function fix_daylight_saving_time() {
		$post = MRequest::get('post', MREQUEST_ALLOWRAW);
		$model = $this->getModel('daylightsaving');
		$model->process($post);
		$this->setRedirect('index.php?option=com_miwoevents&view=waitings', MText::_('Day Light saving time issue fixed'));
	}

    public function sync() {
		mimport('framework.filesystem.folder') ;
		MFolder::copy(MPATH_WP_PLG.'/miwoevents/site', 'D:/www/joomla30/components/com_miwoevents', '', true) ;
		MFolder::copy(MPATH_WP_PLG.'/miwoevents/admin', 'D:/www/joomla30/administrator/components/com_miwoevents', '', true) ;
	}
}