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

$numberEvents	= $params->get('number_events', 6);
$categoryIds 	= $params->get('category_ids', '');
$showCategory 	= $params->get('show_category', 1);
$showLocation 	= $params->get('show_location');
$showPrice 		= $config->show_price_in_mod_events;

$where = array() ;
$where[] = 'a.published = 1';
//$where[] = 'DATE(event_date) >= CURDATE()';
$where[] = '(event_date = "'.$db->getNullDate().'" OR DATE(event_date) >= CURDATE())';

if ($categoryIds != '') {
	$where[] = ' a.id IN (SELECT event_id FROM #__miwoevents_event_categories WHERE category_id IN ('.$categoryIds.'))' ;	
}

$where[] = ' a.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';
if ($app->getLanguageFilter()) {
	$where[] = 'a.language IN (' . $db->Quote(MFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
}

$sql = 'SELECT a.id, a.title, a.location_id, a.event_date, c.title AS location_title, a.individual_price AS price FROM #__miwoevents_events AS a '
	 . ' LEFT JOIN #__miwoevents_locations AS c '
	 . ' ON a.location_id = c.id '
	 . ' WHERE '.implode(' AND ', $where)
	 . ' ORDER BY a.event_date '
	 . ' LIMIT '.$numberEvents		
;	
$db->setQuery($sql) ;	
$rows = $db->loadObjectList();

for ($i = 0, $n = count($rows); $i < $n; $i++) {
	$row = $rows[$i];

	$sql = 'SELECT a.id, a.title FROM #__miwoevents_categories AS a INNER JOIN #__miwoevents_event_categories AS b ON a.id = b.category_id WHERE b.event_id='.$row->id;
	$db->setQuery($sql) ;
	$categories = $db->loadObjectList();

	if (count($categories)) {
		$itemCategories = array();

		foreach ($categories as  $category) {
            $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'category', 'category_id' => $category->id), null, true);

			$itemCategories[] = '<a href="'.MRoute::_('index.php?option=com_miwoevents&view=category&category_id='.$category->id . $Itemid).'"><strong>'.$category->title.'</strong></a>';
		}

		$row->categories = implode('&nbsp;|&nbsp;', $itemCategories) ;
	}		
}

$document->addStyleSheet(MUri::base(true).'/modules/mod_miwoevents_events/css/style.css');

require(MModuleHelper::getLayoutPath('mod_miwoevents_events', 'default'));