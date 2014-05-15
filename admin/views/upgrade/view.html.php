<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# View Class
class MiwoeventsViewUpgrade extends MiwoeventsView {
	
	public function display($tpl = null) {
        MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_UPGRADE'), 'miwoevents');
        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/upgrade?tmpl=component', 650, 500);

		parent::display($tpl);
	}
}
