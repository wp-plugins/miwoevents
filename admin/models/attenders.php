<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsModelAttenders extends MiwoeventsModel {

    public function __construct() {
        parent::__construct('attenders');

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

    public function _buildViewQuery() {}

    public function _buildViewWhere() {}

    public function getTotal() {}

    public function getEvents() {}

    public function getCountries() {}

    public function getEditData($table = NULL) {}

    public function store(&$data) {}

    public function getFields() {}

    public function getColumns() {}
    
    public function exportCSV() {}
}