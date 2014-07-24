<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwoeventsLocations extends MTable {

    public $id 	 		    = 0;
    public $user_id 	 	= 0;
    public $title 			= '';
    public $alias			= '';
    public $description		= '';
    public $address 		= '';
    public $coordinates		= '';
    public $geo_state		= '';
    public $geo_city		= '';
    public $geo_country		= '';
    public $language		= '*';
    public $meta_desc 		= '';
    public $meta_key		= '';
    public $meta_author		= '';
    public $published		= 1;
	public $fields			= null;

	public function __construct(&$db) {
		parent::__construct('#__miwoevents_locations', 'id', $db);
	}

    public function check() {
        # Set title
        $this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

        # Set alias
        $this->alias = MApplication::stringURLSafe($this->alias);
        if (empty($this->alias)) {
            $this->alias = MApplication::stringURLSafe($this->title);
        }
        
        return true;
    }
}