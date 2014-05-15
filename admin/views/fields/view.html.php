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
		parent::display($tpl);
	}

    protected function addToolbar() {
        $acl = MiwoEvents::get('acl');
        MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_FIELDS'), 'miwoevents');
        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/fields?tmpl=component', 650, 500);
    }
}