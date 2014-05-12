<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsViewAttenders extends MiwoeventsView {
	
	public function display($tpl = null) {
        if (!MiwoEvents::get('acl')->canAccessAttenders()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwoevents', MText::_('JERROR_ALERTNOAUTHOR'));
        }
        $this->addToolbar();
		parent::display($tpl);				
	}

    protected function addToolbar() {
        $acl = MiwoEvents::get('acl');

        MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_ATTENDERS'), 'miwoevents');

        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/miwoevents/user-manual/attenders?tmpl=component', 650, 500);
    }
}