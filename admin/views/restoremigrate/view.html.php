<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class MiwoeventsViewRestoreMigrate extends MiwoeventsView {

	public function display($tpl = null) {
        if (!MiwoEvents::get('acl')->canAdmin()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwoevents', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_RESTORE'), 'miwoevents');
		$this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/restore-migrate?tmpl=component', 650, 500);
		
		parent::display($tpl);
	}
}