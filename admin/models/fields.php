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
	
	public function _getUserStates(){}

    public function _buildViewWhere() {}

	public function getEditData($table =NULL) {}
	
    public function store(&$data) {}

    public function saveShopOption($data){}

    private function updateProductOption($optionID, $optionData){}


    public function getLanguageIDs() {}
    
    public function getFieldName($name) {}
    
	public function copy($id) {}
}