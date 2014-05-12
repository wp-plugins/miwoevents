<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.form.formfield');

class MFormFieldMiwoeventsCountries extends MFormField {

	protected $type = 'MiwoeventsCountries';
	
	function getInput() {
        MFactory::getDocument()->addStyleSheet(MURL_MIWOEVENTS.'/admin/assets/css/config.css');

		$db = MFactory::getDBO();
		$db->setQuery("SELECT name AS value, name AS text FROM #__miwoevents_countries ORDER BY name");
		$rows = $db->loadObjectList();

		return MHtml::_('select.genericlist', $rows, $this->name, ' class="inputbox miwoevents_span2" ', 'value', 'text', $this->value);
	}
}
