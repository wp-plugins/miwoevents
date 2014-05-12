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

$numberLocations = $params->get('number_locations', 5);
$showNumberEvents = $params->get('show_number_events', 1);
if ($app->getLanguageFilter()) {
	$extraWhere = ' AND a.language IN (' . $db->Quote(MFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
} else {
	$extraWhere = '' ;
}

$sql = 'SELECT a.id, a.title, COUNT(b.id) AS total_events FROM #__miwoevents_locations AS a LEFT JOIN #__miwoevents_events AS b ON (a.id = b.location_id AND (b.access = 0 OR b.access IN ('.implode(',', $user->getAuthorisedViewLevels()).'))) '
	.'WHERE a.published=1 '.$extraWhere.' GROUP BY a.id HAVING total_events > 0 ORDER BY a.title '.( $numberLocations ? ' LIMIT '.$numberLocations : '');
$db->setQuery($sql) ;
$rows = $db->loadObjectList() ;

$document->addStylesheet(MUri::base(true).'/components/com_miwoevents/assets/css/style.css', 'text/css', null, null);	

require(MModuleHelper::getLayoutPath('mod_miwoevents_locations', 'default'));