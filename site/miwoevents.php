<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

if (!MiwoEvents::get('utility')->checkRequirements('site')) {
    return;
}

$view = MRequest::getCmd('view');
if (empty($view)) {
    $view = 'category';
    MRequest::setVar('view', 'category');
}

if ($view) {
	$path = MPATH_MIWOEVENTS.'/controllers/'.$view.'.php';

	if (file_exists($path)) {
		require_once($path);
	}
    else {
		$view = '';
	}
}

$class_name = 'MiwoeventsController'.$view;

$controller = new $class_name();
$controller->execute(MRequest::getCmd('task'));
$controller->redirect();