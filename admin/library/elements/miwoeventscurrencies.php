<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.form.formfield');

require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

class MFormFieldMiwoeventsCurrencies extends MFormField {

	protected $type = 'MiwoeventsCurrencies';
	
	public function getInput() {
		if (!isset(MiwoEvents::getConfig()->currency_symbol)) {
			$symbol = "$";
		} else {
			$symbol = MiwoEvents::getConfig()->currency_symbol;
		}
		
		return MiwoEvents::get('utility')->getCurrencySelectbox($symbol, "config");
	}
}
