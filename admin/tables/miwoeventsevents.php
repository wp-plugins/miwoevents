<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwoeventsEvents extends MTable {
	
    public $id									= 0;
    public $parent_id							= null;
    public $category_id							= 0;
    public $location_id							= 0;
    public $product_id							= 0;
    public $title								= null;
    public $alias								= '';
    public $event_type							= null;
    public $event_date							= null;
    public $event_end_date						= null;
    public $introtext							= null;
    public $fulltext							= null;
    public $article_id							= 0;
    public $access								= 1;
    public $registration_access					= 1;
    public $individual_price					= null;
    public $tax_class       					= '-1';
    public $event_capacity						= null;
    public $created_by							= null;
    public $cut_off_date						= null;
    public $registration_type					= 0;
    public $max_group_number					= '';
    public $early_bird_discount_type			= null;
    public $early_bird_discount_date			= null;
    public $early_bird_discount_amount			= null;
    public $group_rates							= null;
    public $enable_cancel_registration			= null;
    public $cancel_before_date					= null;
    public $enable_auto_reminder				= null;
    public $remind_before_x_days				= 3;
    public $recurring_type						= 0;
    public $recurring_frequency					= 0;
    public $weekdays							= null;
    public $monthdays							= null;
    public $recurring_end_date					= null;
    public $recurring_occurrencies				= null;
    public $attachment							= null;
    public $params								= null;
    public $ordering							= 0;
    public $published							= 1;
    public $fields								= null;
    public $currency_symbol 					= null;
    public $thumb								= null;
    public $notification_emails					= null;
    public $registrant_email_body				= null;
    public $thanks_message						= null;
    public $registration_approved_email_body	= null;
    public $language							= '*';
    public $meta_desc 							= '';
    public $meta_key							= '';
    public $meta_author							= '';

	public function __construct(&$db) {
		parent::__construct('#__miwoevents_events', 'id', $db);
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