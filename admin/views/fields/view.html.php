<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsViewFields extends MiwoeventsView {

	public function display($tpl = null){
        $this->addToolbar();
		
		$filter_order		= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_order',			'filter_order',		'ordering',	'cmd');
		$filter_order_Dir	= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_order_Dir',		'filter_order_Dir',	'',		'word');
        $filter_display	    = $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_display',		'filter_display',	'');
        $filter_type	    = $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_type',		    'filter_type',	    '',		'word');
		$filter_published	= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_published',		'filter_published',	'');
		$filter_language	= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.filter_language',		'filter_language',	'',		'string');
        $search				= $this->_mainframe->getUserStateFromRequest($this->_option.'.fields.search',				'search',			'',		'string');
		$search				= MString::strtolower($search);

        $options = array();
        $options[] = MHtml::_('select.option', '', MText::_('COM_MIWOEVENTS_SEL_DISPLAY_IN'));
        $options[] = MHtml::_('select.option', 1 , MText::_('COM_MIWOEVENTS_FIELDS_REGISTRATION'));
        $options[] = MHtml::_('select.option', 2 , MText::_('COM_MIWOEVENTS_FIELDS_EVENT'));
        $lists['filter_display'] = MHtml::_('select.genericlist', $options, 'filter_display', 'class="inputbox" ', 'value', 'text', $filter_display);

        $options = array();
        $options[] = MHtml::_('select.option', '', 						MText::_('COM_MIWOEVENTS_SEL_FIELD_TYPE'));
        $options[] = MHtml::_('select.option', 'text', 					MText::_('COM_MIWOEVENTS_FIELDS_TEXT'));
        $options[] = MHtml::_('select.option', 'textarea', 				MText::_('COM_MIWOEVENTS_FIELDS_TEXTAREA'));
        $options[] = MHtml::_('select.option', 'radio', 				MText::_('COM_MIWOEVENTS_FIELDS_RADIO'));
        $options[] = MHtml::_('select.option', 'list', 					MText::_('COM_MIWOEVENTS_FIELDS_LIST'));
        $options[] = MHtml::_('select.option', 'multilist', 			MText::_('COM_MIWOEVENTS_FIELDS_MULTILIST'));
        $options[] = MHtml::_('select.option', 'checkbox', 				MText::_('COM_MIWOEVENTS_FIELDS_CHECKBOX'));
        $options[] = MHtml::_('select.option', 'calendar', 				MText::_('COM_MIWOEVENTS_FIELDS_CALENDAR'));
        $options[] = MHtml::_('select.option', 'miwoeventscountries',	MText::_('COM_MIWOEVENTS_FIELDS_MIWOEVENTSCOUNTRIES'));
        $options[] = MHtml::_('select.option', 'email', 				MText::_('COM_MIWOEVENTS_FIELDS_EMAIL'));
        $options[] = MHtml::_('select.option', 'color', 				MText::_('COM_MIWOEVENTS_FIELDS_COLOR'));
        $options[] = MHtml::_('select.option', 'language', 				MText::_('COM_MIWOEVENTS_FIELDS_LANGUAGE'));
        $options[] = MHtml::_('select.option', 'timezone', 				MText::_('COM_MIWOEVENTS_FIELDS_TIMEZONE'));
        $lists['filter_type'] = MHtml::_('select.genericlist', $options, 'filter_type', 'class="inputbox" ', 'value', 'text', $filter_type);

        $options = array();
        $options[] = MHtml::_('select.option', '',	MText::_('MOPTION_SELECT_PUBLISHED'));
        $options[] = MHtml::_('select.option', 1, 	MText::_('COM_MIWOEVENTS_PUBLISHED'));
        $options[] = MHtml::_('select.option', 0, 	MText::_('COM_MIWOEVENTS_UNPUBLISHED'));
        $lists['filter_published'] = MHtml::_('select.genericlist', $options, 'filter_published', 'class="inputbox"  ', 'value', 'text', $filter_published);

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
			
		
		$lists['order_Dir']			= $filter_order_Dir;
		$lists['order'] 			= $filter_order;
		$lists['filter_language'] 	= $filter_language;
		$lists['search'] 			= $search;

		$this->lists				= $lists;
		$this->items				= $this->get('Items');
		$this->pagination			= $this->get('Pagination');
        $this->langs 				= MiwoEvents::get('utility')->getLanguages();
		
		parent::display($tpl);
	}

    protected function addToolbar() {
        $acl = MiwoEvents::get('acl');

        MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_FIELDS'), 'miwoevents');

        if ($acl->canCreate()) {
            MToolBarHelper::addNew();
        }

        

























        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/fields?tmpl=component', 650, 500);
    }
}