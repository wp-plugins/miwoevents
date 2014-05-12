<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

class MiwoeventsViewHistory extends MiwoeventsView {

	public function display($tpl = null) {
        if (!MiwoEvents::get('acl')->canAccessHistory()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwoevents&view=category', MText::_('COM_MIWOEVENTS_ONLY_REGISTERED'));
        }

        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'history'), null, true);

		$filter_order		= $this->_mainframe->getUserStateFromRequest('com_miwoevents.history.filter_order',         'filter_order',             'r.register_date',  'cmd');
		$filter_order_Dir	= $this->_mainframe->getUserStateFromRequest('com_miwoevents.history.filter_order_Dir',     'filter_order_Dir',         'DESC',             'word');
        $filter_event		= $this->_mainframe->getUserStateFromRequest('com_miwoevents.history.filter_event',			'filter_event',			    0,				    'int');
		$search				= $this->_mainframe->getUserStateFromRequest('com_miwoevents.history.search',               'search',                   '',                 'string');
		$search				= MString::strtolower($search);

        $lists = array();
        $lists['search'] = $search;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$options = array();
		$options[] = MHtml::_('select.option', 0, MText::_('COM_MIWOEVENTS_SELECT_EVENT_2'), 'id', 'title');
		$options = array_merge($options, $this->get('AllEvents'));
		$lists['filter_event'] = MHtml::_('select.genericlist', $options, 'filter_event', ' class="inputbox" ', 'id', 'title', $filter_event);

		$this->lists        = $lists;
		$this->items        = $this->get('Items');
		$this->pagination   = $this->get('Pagination');
		$this->params       = $this->_mainframe->getParams();
		$this->Itemid       = $Itemid;

		parent::display($tpl);				
	}
}