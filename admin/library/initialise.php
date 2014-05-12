<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

define('MIWOEVENTS_PACK', 'calendar');
define('MPATH_MIWOEVENTS', MPATH_WP_PLG.'/miwoevents/site');
define('MPATH_MIWOEVENTS_ADMIN', MPATH_WP_PLG.'/miwoevents/admin');
define('MPATH_MIWOEVENTS_LIB', MPATH_MIWOEVENTS_ADMIN.'/library');

if (!class_exists('MiwoDatabase')) {
	MLoader::register('MiwoDatabase', MPATH_MIWOEVENTS_LIB.'/database.php');
}

if (MFactory::$application->isAdmin()) {
    $_side = MPATH_ADMINISTRATOR;
}
else {
    $_side = MPATH_SITE;
}

$_lang = MFactory::getLanguage();
$_lang->load('com_miwoevents', $_side, 'en-GB', true);
$_lang->load('com_miwoevents', $_side, $_lang->getDefault(), true);
$_lang->load('com_miwoevents', $_side, null, true);

MTable::addIncludePath(MPATH_MIWOEVENTS_ADMIN.'/tables');

MLoader::register('MiwoeventsController', MPATH_MIWOEVENTS_ADMIN.'/library/controller.php');
MLoader::register('MiwoeventsModel', MPATH_MIWOEVENTS_ADMIN.'/library/model.php');
MLoader::register('MiwoeventsView', MPATH_MIWOEVENTS_ADMIN.'/library/view.php');