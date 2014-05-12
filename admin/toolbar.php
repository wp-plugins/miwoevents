<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

$view = MRequest::getCmd('view');

MHtml::_('behavior.switcher');

// Load submenus
$views = array( ''							    => MText::_('COM_MIWOEVENTS_COMMON_PANEL'),
				'&view=fields'				    => MText::_('COM_MIWOEVENTS_CPANEL_FIELDS'),
				'&view=categories'			    => MText::_('COM_MIWOEVENTS_CPANEL_CATEGORIES'),
                '&view=locations'			    => MText::_('COM_MIWOEVENTS_CPANEL_LOCATIONS'),
				'&view=events'				    => MText::_('COM_MIWOEVENTS_CPANEL_EVENTS'),
				'&view=attenders'			    => MText::_('COM_MIWOEVENTS_CPANEL_ATTENDERS'),
				'&view=upgrade'				    => MText::_('COM_MIWOEVENTS_CPANEL_UPGRADE'),
				'&view=support&task=support'	=> MText::_('COM_MIWOEVENTS_CPANEL_SUPPORT'),
				);

if (!class_exists('JSubMenuHelper')) {
    return;
}

foreach($views as $key => $val) {
	if ($key == '') {
		$active	= ($view == $key);
		
		$img = 'icon-16-miwoevents.png';
	}
	else {
	    $a = explode('&', $key);
	  	$c = explode('=', $a[1]);
	
		$active	= ($view == $c[1]);
	
		$img = 'icon-16-miwoevents-'.$c[1].'.png';
	}
	
	JSubMenuHelper::addEntry('<img src="<?php echo MURL_MIWOEVENTS; ?>/site/assets/images/'.$img.'" style="margin-right: 2px;" align="absmiddle" />&nbsp;'.$val, 'index.php?option=com_miwoevents'.$key, $active);
}