<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# View Class
class MiwoeventsViewSupport extends MiwoeventsView {

	public function display($tpl = null) {
		# Toolbar
		MToolBarHelper::title(MText::_('COM_MIWOEVENTS_SUPPORT'), 'miwoevents');
		MToolBarHelper::back(MText::_('Back'), 'index.php?option=com_miwoevents');
		
		if (MRequest::getCmd('task', '') == 'translators') {
			$this->document->setCharset('iso-8859-9');
		}
		
		parent::display($tpl);
	}
}