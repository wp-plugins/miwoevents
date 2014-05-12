<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwoeventsFields extends MTable {
    
    public $id 					= 0;
    public $name 				= '';
    public $title			 	= '';
    public $description 		= '';
    public $field_type 			= 1;
    public $values 				= '';
    public $default_values		= '';
    public $prices		        = '';
    public $display_in			= '';
    public $rows				= '';
    public $cols				= '';
    public $size				= 25;
    public $css_class			= "inputbox";
    public $field_mapping		= '';
    public $ordering			= 0;
    public $language			= '*';
    public $published 			= 1;

	public function __construct(&$db) {
		parent::__construct('#__miwoevents_fields', 'id', $db);
	}
}