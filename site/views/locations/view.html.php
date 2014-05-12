<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsViewLocations extends MiwoeventsView {

	public function display($tpl = null) {
        $Itemid = '&Itemid='.MiwoEvents::getInput()->getInt('Itemid', 0);
        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'locations'), null, true);

        $filter_order		= $this->_mainframe->getUserStateFromRequest('com_miwoevents.history.filter_order',         'filter_order',             'r.register_date',  'cmd');
        $filter_order_Dir	= $this->_mainframe->getUserStateFromRequest('com_miwoevents.history.filter_order_Dir',     'filter_order_Dir',         'DESC',             'word');
        $search				= $this->_mainframe->getUserStateFromRequest('com_miwoevents.history.search',               'search',                   '',                 'string');
        $search				= MString::strtolower($search);

        $lists = array();
        $lists['search'] = $search;
        $lists['order_Dir'] = $filter_order_Dir;
        $lists['order'] = $filter_order;

        $this->lists        = $lists;
		$this->items        = $this->get('Items');
		$this->pagination   = $this->get('Pagination');
        $this->params       = $this->_mainframe->getParams();
        $this->Itemid       = $Itemid;
						
		parent::display($tpl);				
	}	
}