<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

$db 		= MFactory::getDBO();
$user 		= MFactory::getUser();
$document 	= MFactory::getDocument();
$app 		= MFactory::getApplication();
$config 	= MiwoEvents::getConfig();

$task = MiwoEvents::getInput()->getCmd('task', '');

if ($task != 'changeMinicalendar') {
    $month = MiwoEvents::getInput()->getString('month', '');
	
    if (!$month) {
        $month = (int)$params->get('default_month', 0);
		
        if ($month > 0) {
            MiwoEvents::getInput()->setVar('month', $month);
		}
    }   
}

require_once(dirname(__FILE__).'/helper.php');
$helper = new modMiwoeventsMiniCalendarHelper();

list ($year, $month, $day) = $helper->_getYMD();
$data = $helper->_getCalendarData($year, $month, $day);

$listmonth = array(MText::_('COM_MIWOEVENTS_JAN'), MText::_('COM_MIWOEVENTS_FEB'), MText::_('COM_MIWOEVENTS_MARCH'), 
MText::_('COM_MIWOEVENTS_APR'), MText::_('COM_MIWOEVENTS_MAY'), MText::_('COM_MIWOEVENTS_JUNE'), MText::_('COM_MIWOEVENTS_JUL'), 
MText::_('COM_MIWOEVENTS_AUG'), MText::_('COM_MIWOEVENTS_SEP'), MText::_('COM_MIWOEVENTS_OCT'), MText::_('COM_MIWOEVENTS_NOV'), 
MText::_('COM_MIWOEVENTS_DEC'));

$document->addStyleSheet(MUri::base(true).'modules/mod_miwoevents_events/css/style.css');
$document->addStylesheet(MUri::base(true).'/components/com_miwoevents/assets/css/themes/'.$config->calendar_theme.'.css', 'text/css', null, null);

$module_title = $module->title;

require (MModuleHelper::getLayoutPath('mod_miwoevents_minicalendar', 'default'));