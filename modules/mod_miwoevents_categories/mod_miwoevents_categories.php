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

$numberCategories = $params->get('number_categories', 5);

if ($app->getLanguageFilter()) {
	$extraWhere = ' AND language IN (' . $db->Quote(MFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
} else {
	$extraWhere = '' ;
}

$sql = 'SELECT id, title FROM #__miwoevents_categories WHERE parent = 0 AND published=1 '
	.' AND access IN ('.implode(',', $user->getAuthorisedViewLevels()).')'.$extraWhere.' ORDER BY ordering '.($numberCategories ? ' LIMIT '.$numberCategories : '');
   
$db->setQuery($sql) ;	
$rows = $db->loadObjectList() ;

require(MModuleHelper::getLayoutPath('mod_miwoevents_categories', 'default'));