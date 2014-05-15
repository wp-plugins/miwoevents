<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;


class MiwoEventsViewLocations extends MiwoeventsView {
	
	public function display($tpl = null){
        $this->addToolbar();
		
		$filter_order		= $this->_mainframe->getUserStateFromRequest($this->_option.'.locations.filter_order',			'filter_order',		'title','string');
		$filter_order_Dir	= $this->_mainframe->getUserStateFromRequest($this->_option.'.locations.filter_order_Dir',		'filter_order_Dir',	'ASC',	'word');
		$filter_language	= $this->_mainframe->getUserStateFromRequest($this->_option.'.locations.filter_language',		'filter_language',	'',		'string');
		$search				= $this->_mainframe->getUserStateFromRequest($this->_option.'.locations.search',				'search',			'',		'string');
		$search				= MString::strtolower($search);						
		
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;
		$lists['language'] 	= $filter_language;
		$lists['search'] 	= $search;

        $options = array();
		$options[] = MHtml::_('select.option', '', MText::_('Bulk Actions'));

		if (MiwoEvents::get('acl')->canEditState()) {
			$options[] = MHtml::_('select.option', 'publish', MText::_('MTOOLBAR_PUBLISH'));
			$options[] = MHtml::_('select.option', 'unpublish', MText::_('MTOOLBAR_UNPUBLISH'));
		}

		if (MiwoEvents::get('acl')->canDelete()) {
			$options[] = MHtml::_('select.option', 'delete', MText::_('MTOOLBAR_DELETE'));
		}

		$lists['bulk_actions'] = MHtml::_('select.genericlist', $options, 'bulk_actions', ' class="inputbox"', 'value', 'text', '');
			
		
		$this->lists 		= $lists;
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
        $this->langs        = MiwoEvents::get('utility')->getLanguages();
				
		parent::display($tpl);
	}

    protected function addToolbar() {
        $acl = MiwoEvents::get('acl');

        MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_LOCATIONS'), 'miwoevents');

        if ($acl->canCreate()) {
            MToolBarHelper::addNew();
        }

        


















        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/locations?tmpl=component', 650, 500);
    }
}