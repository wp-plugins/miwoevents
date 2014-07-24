<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Access check
if (!MFactory::getUser()->authorise('core.manage', 'com_miwoevents')) {
	return MError::raiseWarning(404, MText::_('JERROR_ALERTNOAUTHOR'));
}

MHtml::_('behavior.framework');

require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

if (!MiwoEvents::get('utility')->checkRequirements('admin')) {
    return;
}

$task = MRequest::getCmd('task', '');

if (!(($task == 'add' or $task == 'edit') and MiwoEvents::is30())) {
    require_once(MPATH_MIWOEVENTS_ADMIN.'/toolbar.php');
}

if ($view = MRequest::getCmd('view', '')){
	$path = MPATH_MIWOEVENTS_ADMIN.'/controllers/'.$view.'.php';

	if (file_exists($path)) {
		require_once($path);
	} else {
		$view = '';
	}
}

$class_name = 'MiwoeventsController'.$view;

$controller = new $class_name();
$controller->execute(MRequest::getCmd('task', ''));
$controller->redirect();