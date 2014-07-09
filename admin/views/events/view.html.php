<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;


class MiwoEventsViewEvents extends MiwoeventsView {
	
	function display($tpl = null) {
        $this->addToolbar();
		
		$filter_order		= $this->_mainframe->getUserStateFromRequest($this->_option.'.events.event_filter_order',	'filter_order',		'title',	'cmd');
		$filter_order_Dir	= $this->_mainframe->getUserStateFromRequest($this->_option.'.events.filter_order_Dir',	    'filter_order_Dir',	'',		    'word');
        $filter_past 		= $this->_mainframe->getUserStateFromRequest($this->_option.'.events.filter_past',			'filter_past',		-1,		    'string');
		$filter_category 	= $this->_mainframe->getUserStateFromRequest($this->_option.'.events.filter_category',		'filter_category',	0,		    'int');
		$filter_location	= $this->_mainframe->getUserStateFromRequest($this->_option.'.events.filter_location',		'filter_location',	0,		    'int');
		$filter_published	= $this->_mainframe->getUserStateFromRequest($this->_option.'.events.filter_published',	    'filter_published',				'');
        $filter_access      = $this->_mainframe->getUserStateFromRequest($this->_option.'.events.filter_access',	    'filter_access',	        	'');
        $filter_language	= $this->_mainframe->getUserStateFromRequest($this->_option.'.events.filter_language',		'filter_language',	'',		    'string');
		$search				= $this->_mainframe->getUserStateFromRequest($this->_option.'.events.search',				'search',			'',		    'string');
		$search				= MString::strtolower($search);

		$lists['filter_category']	= MiwoEvents::get('utility')->buildCategoryDropdown($filter_category, 'filter_category', true);
		$lists['search']		 	= $search ;
		$lists['order_Dir']			= $filter_order_Dir;
		$lists['order'] 		 	= $filter_order;
		
		$items		= $this->get('Items');
		$pagination = $this->get('Pagination');
		$locations  = $this->get('Locations');
		
		$options = array() ;
		$options[] = MHtml::_('select.option', 0, MText::_('COM_MIWOEVENTS_SELECT_LOCATION'));
		if (!empty($locations)) {
			foreach ($locations as $location) {
				$options[] = MHtml::_('select.option', $location->id, $location->title);
			}
		}
		$lists['filter_location'] = MHtml::_('select.genericlist', $options, 'filter_location', ' class="inputbox" style="width: 150px;"  ', 'value', 'text', $filter_location);
        				
		$options = array() ;
		$options[] = MHtml::_('select.option', -1, MText::_('COM_MIWOEVENTS_PAST_EVENTS'));
		$options[] = MHtml::_('select.option', 0, MText::_('COM_MIWOEVENTS_HIDE'));
		$options[] = MHtml::_('select.option', 1, MText::_('COM_MIWOEVENTS_SHOW'));
		$lists['filter_past'] = MHtml::_('select.genericlist', $options, 'filter_past', ' class="inputbox" style="width: 140px;"  ', 'value', 'text', $filter_past);
		
		$options = array();
		$options[] = MHtml::_('select.option', '', MText::_('COM_MIWOEVENTS_SELECT_STATUS'));
		$options[] = MHtml::_('select.option',  1, MText::_('COM_MIWOEVENTS_PUBLISHED'));
		$options[] = MHtml::_('select.option',  0, MText::_('COM_MIWOEVENTS_UNPUBLISHED'));
		$lists['filter_published'] = MHtml::_('select.genericlist', $options, 'filter_published', ' class="inputbox" style="width: 140px;"  ', 'value', 'text', $filter_published);

		$options = array();
		$options[] = MHtml::_('select.option', '', MText::_('Bulk Actions'));

		if (MiwoEvents::get('acl')->canEditState()) {
			$options[] = MHtml::_('select.option', 'publish', MText::_('MTOOLBAR_PUBLISH'));
			$options[] = MHtml::_('select.option', 'unpublish', MText::_('MTOOLBAR_UNPUBLISH'));
		}

		if (MiwoEvents::get('acl')->canCreate()) {
			$options[] = MHtml::_('select.option', 'copy', MText::_('Copy'));
		}
			

		if (MiwoEvents::get('acl')->canDelete()) {
			$options[] = MHtml::_('select.option', 'delete', MText::_('MTOOLBAR_DELETE'));
		}

		$lists['bulk_actions'] = MHtml::_('select.genericlist', $options, 'bulk_actions', ' class="inputbox"', 'value', 'text', '');
			

        MHtml::_('behavior.tooltip');
		
		$this->lists 			= $lists;
		$this->items 			= $items;
		$this->pagination 		= $pagination;
        
        $this->langs            = MiwoEvents::get('utility')->getLanguages();
		$this->filter_language 	= $filter_language;
		$this->filter_access 	= $filter_access;

		parent::display($tpl);				
	}

    protected function addToolbar() {
        $acl = MiwoEvents::get('acl');

        MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_EVENTS'), 'miwoevents');

        if ($acl->canCreate()) {
            MToolBarHelper::addNew();
        }

        

























        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/fields?tmpl=component', 650, 500);
    }
}