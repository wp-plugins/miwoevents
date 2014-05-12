<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwoeventsAttenders extends MTable {

    public $id 	 		    	= 0;
    public $event_id 	 		= 0;
    public $user_id 	 		= 0;
    public $group_id 	 		= 0;
    public $fields	 			= NULL;
    public $number_attenders 	= 0;
    public $register_date		= null;
    public $payment_date		= null;
    public $reminder_sent		= 0;
    public $status				= 0;
    public $language			= '*';

	public function __construct($db) {
		parent::__construct('#__miwoevents_attenders', 'id', $db);
	}
}