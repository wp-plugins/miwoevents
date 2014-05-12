<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsModelLocations extends MiwoeventsModel {

    public function __construct() {
		parent::__construct('locations');

        $this->_getUserStates();
        $this->_buildViewQuery();
	}

    public function _getUserStates() {
        $this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'title',	'cmd');
        $this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'DESC',     'word');
        $this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'',         'string');
        $this->search 	 			= MString::strtolower($this->search);
    }

    public function _buildViewWhere() {
        $where = array();

        $where[] = 'published=1';

        if (!empty($this->search)) {
            $src = parent::secureQuery($this->search, true);
            $where[] = "(LOWER(title) LIKE {$src} OR LOWER(description) LIKE {$src})";
        }

        $where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');

        return $where;
    }
}