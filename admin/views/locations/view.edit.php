<?php

/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsViewLocations extends MiwoeventsView {
	
	public function display($tpl = null) {
        if (!MiwoEvents::get('acl')->canEdit()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwoevents', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $task = MRequest::getCmd('task');
		$text = ($task == 'edit') ? MText::_('COM_MIWOEVENTS_EDIT') : MText::_('COM_MIWOEVENTS_NEW');

        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_LOCATIONS').': <small><small>[ '.$text.' ]</small></small>', 'miwoevents');
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::save2new();
            MToolBarHelper::cancel();
            MToolBarHelper::divider();
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/miwoevents/user-manual/locations?tmpl=component', 650, 500);
        }

		$item = $this->get('EditData');
		
		$lists['published'] = MHtml::_('select.booleanlist', 'published', ' class="inputbox" ', $item->published) ;
		$lists['language'] = MHtml::_('select.genericlist', MHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox" ', 'value', 'text', $item->language);

        MHtml::_('behavior.tooltip');

		$this->item = $item;
		$this->lists = $lists;

		parent::display($tpl);				
	}
}