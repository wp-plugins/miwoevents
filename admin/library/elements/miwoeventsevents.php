<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.form.formfield');

class MFormFieldMiwoeventsEvents extends MFormField {

	protected $type = 'MiwoeventsEvents';
	
	function getInput() {
		$db = MFactory::getDBO();
		$db->setQuery("SELECT id, title FROM #__miwoevents_events WHERE published = 1 ORDER BY title");
		$rows = $db->loadObjectList();
		
		$options = array();
		$options[] = MHtml::_('select.option', '0', MText::_('Select Event'), 'id', 'title');
		$options = array_merge($options, $rows);
		
		return MHtml::_('select.genericlist', $options, $this->name, ' class="inputbox" ', 'id', 'title', $this->value);
	}
}
