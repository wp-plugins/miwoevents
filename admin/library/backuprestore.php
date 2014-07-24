<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

mimport('framework.filesystem.file');
# Backup/Restore class
class MiwoeventsBackupRestore {
	
	protected $_dbprefix;
	protected $_table;
	protected $_where;
	protected $_jstatus;

    public function __construct($options = "") {
		if (is_array($options)) {
			if (isset($options['_table'])) {
				$this->_table = $options['_table'];
			}
			
			if (isset($options['_where'])) {
				$this->_where = $options['_where'];
			}
		}
		
		$this->_jstatus = MiwoEvents::is30();
		
		$this->_dbprefix = MFactory::getConfig()->get('dbprefix');
	}

    public function backupCategories() {
        $filename = "miwoevents_categories.sql";
        $fields = array('id', 'parent', 'title', 'alias', 'description', 'introtext', 'fulltext', 'ordering', 'access', 'color_code', 'language', 'meta_desc', 'meta_key', 'meta_author', 'published');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwoevents_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `parent`, `title`, `alias`, `description`, `introtext`, `fulltext`, `ordering`, `access`, `color_code`, `language`, `meta_desc`, `meta_key`, `meta_author`, `published` FROM {$this->_dbprefix}miwoevents_{$this->_table}{$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupLocations() {
        $filename = "miwoevents_locations.sql";
        $fields = array('id', 'user_id', 'title', 'alias', 'description', 'address', 'geo_city', 'geo_state', 'geo_country', 'coordinates', 'language', 'meta_desc', 'meta_key', 'meta_author', 'published');
        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwoevents_{$this->_table} (".implode(', ', $fields).")";
        $query = "SELECT `id`, `user_id`, `title`, `alias`, `description`, `address`, `geo_city`, `geo_state`, `geo_country`, `coordinates`, `language`, `meta_desc`, `meta_key`, `meta_author`, `published` FROM {$this->_dbprefix}miwoevents_{$this->_table}{$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupEvents() {
        $filename = "miwoevents_events.sql";
        $fields = array('id', 'parent_id', 'category_id', 'location_id', 'product_id', 'title', 'alias', 'event_type', 'event_date', 'event_end_date', 'introtext', 'fulltext', 'article_id', 'access',
            'registration_access', 'individual_price', 'tax_class', 'event_capacity', 'created_by', 'cut_off_date', 'registration_type', 'max_group_number', 'early_bird_discount_type', 'early_bird_discount_date',
            'early_bird_discount_amount', 'group_rates', 'enable_cancel_registration', 'cancel_before_date', 'enable_auto_reminder', 'remind_before_x_days', 'recurring_type', 'recurring_frequency', 'weekdays', 'monthdays',
            'recurring_end_date', 'recurring_occurrencies', 'attachment', 'notification_emails', 'registrant_email_body', 'thanks_message',
            'params', 'ordering', 'published', 'meta_desc', 'meta_key', 'meta_author', 'fields', 'currency_symbol', 'thumb', 'registration_approved_email_body',
            'language');

        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwoevents_events (".implode(', ', $fields).")\n";

        $query = "SELECT `id`, `parent_id`, `category_id`, `location_id`, `product_id`, `title`, `alias`, `event_type`, `event_date`, `event_end_date`, `introtext`, `fulltext`, `article_id`, `access`,".
            " `registration_access`, `individual_price`, `tax_class`, `event_capacity`, `created_by`, `cut_off_date`, `registration_type`, `max_group_number`, `early_bird_discount_type`, `early_bird_discount_date`,".
            " `early_bird_discount_amount`, `group_rates`, `enable_cancel_registration`, `cancel_before_date`, `enable_auto_reminder`, `remind_before_x_days`, `recurring_type`, `recurring_frequency`, `weekdays`, `monthdays`,".
            " `recurring_end_date`, `recurring_occurrencies`, `attachment`, `notification_emails`, `registrant_email_body`, `thanks_message`, ".
            " `params`, `ordering`, `published`, `meta_desc`, `meta_key`, `meta_author`, `fields`, `currency_symbol`, `thumb`, `registration_approved_email_body`,".
            " `language` FROM {$this->_dbprefix}miwoevents_events {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupEventcategories() {
        $filename = "miwoevents_event_categories.sql";
        $fields = array('id', 'event_id', 'category_id');

        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwoevents_event_categories (".implode(', ', $fields).")";

        $query = "SELECT `id`, `event_id`, `category_id` FROM {$this->_dbprefix}miwoevents_event_categories {$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function backupAttenders() {
        $filename = "miwoevents_attenders.sql";
        $fields = array('id', 'event_id', 'user_id', 'group_id', 'order_id', 'fields',
            'number_attenders', 'register_date', 'payment_date', 'reminder_sent', 'language', 'status');

        $line = "INSERT IGNORE INTO {$this->_dbprefix}miwoevents_{$this->_table} (".implode(', ', $fields).")";

        $query = "SELECT `id`, `event_id`, `user_id`, `group_id`, `order_id`, `fields`,".
            " `number_attenders`, `register_date`, `payment_date`, `reminder_sent`, `language`, `status` FROM {$this->_dbprefix}miwoevents_{$this->_table}{$this->_where}";

        return array($query, $filename, $fields, $line);
    }

    public function restoreCategories($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwoevents_categories`?/';

        return array($preg, $line);
    }

    public function restoreLocations($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwoevents_locations`?/';

        return array($preg, $line);
    }

    public function restoreEventcategories($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwoevents_event_categories`?/';

        return array($preg, $line);
    }

    public function restoreEvents($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwoevents_events`?/';

        return array($preg, $line);
    }

    public function restoreAttenders($line) {
        $preg = '/^INSERT IGNORE INTO `?(\w)+miwoevents_attenders`?/';

        return array($preg, $line);
    }

    # The Events Calendar
    public function migrateEventscalendarCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT t.term_id AS category_id, t.name AS category_name, tt.parent, tt.description AS category_desc FROM #__terms AS t INNER JOIN #__term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'tribe_events_cat' ORDER BY t.term_id";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
			$cat_name = ($this->_jstatus) ? $db->escape($cat['category_name']) : $db->getEscaped($cat['category_name']);
			$cat_desc = ($this->_jstatus) ? $db->escape($cat['category_desc']) : $db->getEscaped($cat['category_desc']);

            $q = "INSERT IGNORE INTO `#__miwoevents_categories` (`id`, `parent`, `title`, `alias`, `introtext`, `published`, `ordering`, `access`, `language`) ".
                "VALUES ('".$cat['category_id']."', '".$cat['parent']."', '".$cat_name."', '".$cat['alias']."', '".$cat_desc."', '1', '0', '1', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateEventscalendarEvents(){
        $db = MFactory::getDBO();

		$evn = "SELECT ID AS ev_id, post_title, post_content, post_name AS product_sku, post_date, post_modified, post_status AS published, post_author FROM #__posts WHERE post_status != 'auto-draft' AND post_status != 'trash' AND post_type = 'tribe_events' AND post_parent = '0' ORDER BY ID";
        $db->setQuery($evn);
        $evns = $db->loadAssocList();

        if (empty($evns)) {
            return false;
        }

        foreach($evns as $evn) {
			$ev_duration = $ev_price = $ev_organizer = $venue_id = $dt_start = $dt_end = $ev_image = $ev_symbol = '';
			$evn_publish = '1';
			
			$q = "SELECT meta_key, meta_value FROM `#__postmeta` WHERE post_id = ".$evn['ev_id'];
			$db->setQuery($q);
			$metas = $db->loadAssocList();
			
			foreach($metas as $meta){
				switch($meta['meta_key']){
					case '_EventStartDate':
						$dt_start = ($this->_jstatus) ? MFactory::getDate($meta['meta_value'])->toSql() : MFactory::getDate($meta['meta_value'])->toMySQL();
						break;
					case '_EventEndDate':
						$dt_end = ($this->_jstatus) ? MFactory::getDate($meta['meta_value'])->toSql() : MFactory::getDate($meta['meta_value'])->toMySQL();
						break;
					case '_EventVenueID':
						$venue_id = $meta['meta_value'];
						break;
					case '_EventCost':
						$ev_price = $meta['meta_value'];
						break;
					case '_EventOrganizerID':
						$ev_organizer = $meta['meta_value'];
						break;
					case '_EventDuration':
						$ev_duration = $meta['meta_value'];
						break;
					case '_EventCurrencySymbol':
						$ev_symbol = $meta['meta_value'];
						break;
				}
			}
			
			if(!empty($evn['published'])){
				if($evn['published'] == 'publish'){
					$evn_publish = '1';
				}
				if($evn['published'] == 'pending'){
					$evn_publish = '0';
				}
			}

			$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND post_parent=".$evn['ev_id'];
			$db->setQuery($q);
			$result_img = $db->loadResult();
			
			if(!empty($result_img)){
				self::copyEvnImages($result_img);

				$images = explode('/' , $result_img);
				$last1 = array_pop($images);
				
				$ev_image = empty($last1) ? '' : $last1;
			}
			
            $q = "SELECT r.term_taxonomy_id FROM `#__posts` AS p INNER JOIN `#__term_relationships` AS r ON p.ID = r.object_id WHERE p.post_type = 'tribe_events' AND p.post_parent = '0' AND ID=".$evn['ev_id'];
            $db->setQuery($q);
            $ev_catid = $db->loadResult();
			
            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($evn['post_title'], ENT_QUOTES));
			$evn_name = ($this->_jstatus) ? $db->escape($evn['post_title']) : $db->getEscaped($evn['post_title']);
			$evn_desc = ($this->_jstatus) ? $db->escape($evn['post_content']) : $db->getEscaped($evn['post_content']);

            $q = "INSERT IGNORE INTO `#__miwoevents_events` (`id`, `category_id`, `location_id`, `product_id`, `title`, `alias`, `introtext`, `published`, `ordering`, `event_date`, `event_end_date`, `created_by`,".
				"`recurring_frequency`, `recurring_type`, `access`, `registration_access`, `event_type`, `language`, `thumb`, `currency_symbol`, `individual_price`) ".
                "VALUES ('".$evn['ev_id']."', '".$ev_catid."', '".$venue_id."', '0', '".$evn_name."', '".$alias."', '".$evn_desc."', '".$evn_publish."', '1', '".$dt_start."', '".$dt_end."', '".$evn['post_author'].
				"', '0', '0', '1', '1', '0', '*', '".$ev_image."', '".$ev_symbol."', '".$ev_price."')";
            $db->setQuery($q);
            $db->query();
	  
			$q = "SELECT r.object_id, r.term_taxonomy_id FROM `#__posts` AS p INNER JOIN `#__term_relationships` AS r ON p.ID = r.object_id WHERE p.post_type = 'tribe_events' AND p.post_parent = '0' AND ID=".$evn['ev_id'];
			$db->setQuery($q);
			$results = $db->loadAssocList();
			
			if (!empty($results)) {
				foreach($results as $ptcs) {
					$ptjc = "INSERT IGNORE INTO `#__miwoevents_event_categories` (`event_id`, `category_id`) VALUES ('".$evn['ev_id']."', '".$ptcs['term_taxonomy_id']."')";
					$db->setQuery($ptjc);
					$db->query();
				}
			}
        }

        return true;
    }
	
    public function migrateEventscalendarLocations(){
        $db = MFactory::getDBO();
		$loc = "SELECT ID AS loc_id, post_title, post_content, post_status AS published, post_author FROM #__posts WHERE post_status != 'auto-draft' AND post_status != 'trash' AND post_type = 'tribe_venue' AND post_parent = '0' ORDER BY ID";

        $db->setQuery($loc);
        $locs = $db->loadAssocList();

        if (empty($locs)) {
            return false;
        }

        foreach($locs as $loc) {
			$loc_publish = 1;
			$loc_city = $loc_country = $loc_state = $loc_address = '';
			
			if(!empty($loc['published'])){
				if($loc['published'] == 'publish'){
					$loc_publish = '1';
				}
				if($loc['published'] == 'pending'){
					$loc_publish = '0';
				}
			}
			
			$q = "SELECT meta_key, meta_value FROM `#__postmeta` WHERE post_id = ".$loc['loc_id'];
			$db->setQuery($q);
			$metas = $db->loadAssocList();
			
			foreach($metas as $meta){
				switch($meta['meta_key']){
					case '_VenueCity':
						$loc_city = $meta['meta_value'];
						break;
					case '_VenueCountry':
						$loc_country = $meta['meta_value'];
						break;
					case '_VenueState':
						$loc_state = $meta['meta_value'];
						break;
					case '_VenueAddress':
						$loc_address = $meta['meta_value'];
						break;
				}
			}
			
            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($loc['post_title'], ENT_QUOTES));
			$loc_name = ($this->_jstatus) ? $db->escape($loc['post_title']) : $db->getEscaped($loc['post_title']);
			$loc_desc = ($this->_jstatus) ? $db->escape($loc['post_content']) : $db->getEscaped($loc['post_content']);

            $q = "INSERT IGNORE INTO `#__miwoevents_locations` (`id`, `user_id`, `title`, `alias`, `description`, `geo_city`, `geo_state`, `geo_country`, `address`, `language`, `published`) ".
                "VALUES ('".$loc['loc_id']."', '".$loc['post_author']."', '".$loc_name."', '".$alias."', '".$loc_desc."', '".$loc_city."', '".$loc_state."', '".$loc_country."', '".$loc_address."', '*', '".$loc_publish."')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    # Spider Event Calendar
    public function migrateSpiderEventCalCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT * FROM #__spidercalendar_event_category ORDER BY `id`";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
            $colors = strtoupper(str_replace('#', '', $cat['color']));
            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($cat['title'], ENT_QUOTES));
			$cat_name = ($this->_jstatus) ? $db->escape($cat['title']) : $db->getEscaped($cat['title']);
			$cat_desc = ($this->_jstatus) ? $db->escape($cat['description']) : $db->getEscaped($cat['description']);

            $q = "INSERT IGNORE INTO `#__miwoevents_categories` (`id`, `parent`, `title`, `alias`, `introtext`, `published`, `access`, `color_code`, `ordering`, `language`) ".
                "VALUES ('".$cat['id']."', '0', '".$cat_name."', '".$alias."', '".$cat_desc."', '".$cat['published']."', '1', '".$colors."', '0', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateSpiderEventCalEvents(){
        $db = MFactory::getDBO();

        $evn = "SELECT * FROM #__spidercalendar_event ORDER BY id";
        $db->setQuery($evn);
        $evns = $db->loadAssocList();

        if (empty($evns)) {
            return false;
        }

        foreach($evns as $evn) {
            if($evn['repeat_method'] == 'no_repeat'){
                $rec_type = $rec_freq = '0';
                $weekdays = NULL;
                $monthdays = NULL;
            }
			else if($evn['repeat_method'] == 'daily'){
                $rec_type = '1';
                $rec_freq = $evn['repeat'];
                $weekdays = NULL;
                $monthdays = $evn['repeat_interval'];
            }
			else if($evn['repeat_method'] == 'weekly'){
                $rec_type = '2';
                $rec_freq = $evn['repeat'];
                $day_no = '';
				if(!empty($evn['week'])){
					$weeksss = explode(',', $evn['week']);
					foreach($weeksss AS $weekss){
						if($weekss == 'Mon') $day_no .= '0,';
						if($weekss == 'Tue') $day_no .= '1,';
						if($weekss == 'Wed') $day_no .= '2,';
						if($weekss == 'Thu') $day_no .= '3,';
						if($weekss == 'Fri') $day_no .= '4,';
						if($weekss == 'Sat') $day_no .= '5,';
						if($weekss == 'Sun') $day_no .= '6,';
					}
					$day_no = rtrim($day_no, ',');
				}
                $weekdays = $day_no;
                $monthdays = NULL;
            }
			else if($evn['repeat_method'] == 'monthly'){
                $rec_type = '3';
                $rec_freq = $evn['repeat'];
                $weekdays = NULL;
                $monthdays = $evn['month'];
            } else {
                $rec_type = $rec_freq = '0';
                $weekdays = NULL;
                $monthdays = NULL;
            }
			
			$evn_type = ($rec_type == '0') ? '0' : '1';
			
			$dt_start = ($this->_jstatus) ? MFactory::getDate($evn['date'])->toSql() : MFactory::getDate($evn['date'])->toMySQL();
			$dt_end = ($this->_jstatus) ? MFactory::getDate($evn['date_end'])->toSql() : MFactory::getDate($evn['date_end'])->toMySQL();

            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($evn['title'], ENT_QUOTES));
			$evn_name = ($this->_jstatus) ? $db->escape($evn['title']) : $db->getEscaped($evn['title']);
			$evn_desc = ($this->_jstatus) ? $db->escape($evn['text_for_date']) : $db->getEscaped($evn['text_for_date']);

            $q = "INSERT IGNORE INTO `#__miwoevents_events` (`id`, `parent_id`, `category_id`, `location_id`, `product_id`, `title`, `alias`, `event_type`, `event_date`, `event_end_date`, `cut_off_date`, `introtext`, `access`,".
                " `registration_access`, `created_by`, `registration_type`, `ordering`, `published`, `language`, `recurring_type`, `recurring_frequency`, `weekdays`, `monthdays`, `recurring_end_date`) ".
                "VALUES ('".$evn['id']."', '0', '".$evn['category']."', '0', '0', '".$evn_name."', '".$alias."', '".$evn_type."', '".$dt_start."', '".$dt_end."', '".$dt_end."', '".$evn_desc."', '1', '".
				"1', '".$evn['userID']."', '0', '1', '".$evn['published']."', '*', '".$rec_type."', '".$rec_freq."', '".$weekdays."', '".$monthdays."', '".$dt_end."')";
            $db->setQuery($q);
            $db->query();

            $q = "INSERT IGNORE INTO `#__miwoevents_event_categories` (`event_id`, `category_id`) VALUES ('".$evn['id']."', '".$evn['category']."')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    # Events Manager
    public function migrateEventmanagerCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT t.term_id AS category_id, t.name AS category_name, tt.parent, tt.description AS category_desc FROM #__terms AS t INNER JOIN #__term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'event-categories' ORDER BY t.term_id";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
			$ev_image = $color = '';
			
			$q = "SELECT meta_key, meta_value FROM `#__em_meta` WHERE object_id = ".$cat['category_id'];
			$db->setQuery($q);
			$metas = $db->loadAssocList();
			
			foreach($metas as $meta){
				switch($meta['meta_key']){
					case 'category-bgcolor':
						$color = str_replace("#", "", $meta['meta_value']);
						break;
					case 'category-image-id':
						$evn_image_id = $meta['meta_value'];
						break;
				}
			}
			
			if(!empty($evn_image_id)){
				$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$evn_image_id;
				$db->setQuery($q);
				$result_img = $db->loadResult();
				
				if(!empty($result_img)){
                    self::copyEvnImages($result_img);

					$images = explode('/' , $result_img);
					$last1 = array_pop($images);
					
					$ev_image = empty($last1) ? '' : $last1;
				}
			}
			
            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($cat['category_name'], ENT_QUOTES));
			$cat_name = ($this->_jstatus) ? $db->escape($cat['category_name']) : $db->getEscaped($cat['category_name']);
			$cat_desc = ($this->_jstatus) ? $db->escape($cat['category_desc']) : $db->getEscaped($cat['category_desc']);

            $q = "INSERT IGNORE INTO `#__miwoevents_categories` (`id`, `parent`, `title`, `alias`, `introtext`, `published`, `ordering`, `access`, `color_code`, `language`) ".
                "VALUES ('".$cat['category_id']."', '".$cat['parent']."', '".$cat_name."', '".$alias."', '".$cat_desc."', '1', '0', '1', '".$color."', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateEventmanagerEvents(){
        $db = MFactory::getDBO();

        $evn = "SELECT * FROM #__em_events ORDER BY event_id";
        $db->setQuery($evn);
        $evns = $db->loadAssocList();

        if (empty($evns)) {
            return false;
        }

        foreach($evns as $evn) {
			$ev_image = $evn_price = '';
			$recurrence_id = $rec_type = $rec_freq = $ev_catid = $evn_type = '0';
			$weekdays = $monthdays = NULL;
			
			/*$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$evn['post_id'];
			$db->setQuery($q);
			$result_img = $db->loadResult();
			
			if(!empty($result_img)){
				self::copyEvnImages($result_img);

				$images = explode('/' , $result_img);
				$last1 = array_pop($images);
				
				$ev_image = empty($last1) ? '' : $last1;
			}*/
			
			$q = "SELECT ticket_price, ticket_spaces FROM `#__em_tickets` WHERE event_id = ".$evn['event_id']." LIMIT 1";
			$db->setQuery($q);
			$evn_price = $db->loadResult();
			
            $q = "SELECT r.term_taxonomy_id FROM `#__posts` AS p INNER JOIN `#__term_relationships` AS r ON p.ID = r.object_id WHERE p.post_type = 'tribe_events' AND p.post_parent = '0' AND ID=".$evn['post_id'];
            $db->setQuery($q);
            $ev_catid = $db->loadResult();
			
			$q = "SELECT  meta_value FROM `#__postmeta` WHERE meta_key = '_thumbnail_id' AND object_id = ".$evn['post_id'];
			$db->setQuery($q);
			$evn_image_id = $db->loadResult();
			
			if(!empty($evn_image_id)){
				$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$evn_image_id;
				$db->setQuery($q);
				$result_img = $db->loadResult();
				
				if(!empty($result_img)){
                    self::copyEvnImages($result_img);

					$images = explode('/' , $result_img);
					$last1 = array_pop($images);
					
					$ev_image = empty($last1) ? '' : $last1;
				}
			}

			$st_date = $evn['event_start_date'].' '.$evn['event_start_time'];
			$end_date = $evn['event_end_date'].' '.$evn['event_end_time'];
			$cut_off_date = $evn['event_rsvp_date'].' '.$evn['event_rsvp_time'];
			
			if($evn['recurrence_id']){
				$recurrence_id = $evn['recurrence_id'];
			}
			
			if($recurrence_id){
				if($evn['recurrence_freq'] == NULL){
					$rec_type = $rec_freq = '0';
					$weekdays = NULL;
					$monthdays = NULL;
				}
				else if($evn['recurrence_freq'] == 'daily'){
					$rec_type = '1';
					$rec_freq = $evn['recurrence_days'];
					$weekdays = NULL;
					$monthdays = $evn['repeat_interval'];
				}
				else if($evn['recurrence_freq'] == 'weekly'){
					$rec_type = '2';
					$rec_freq = $evn['recurrence_days'];
					$weekdays = $evn['recurrence_byday'];
					$monthdays = NULL;
				}
				else if($evn['recurrence_freq'] == 'monthly'){
					$rec_type = '3';
					$rec_freq = $evn['recurrence_days'];
					$weekdays = NULL;
					$monthdays = $evn['recurrence_byweekno'];
				} else {
					$rec_type = $rec_freq = '0';
					$weekdays = NULL;
					$monthdays = NULL;
				}
			}
			
			$evn_type = ($rec_type == '0') ? '0' : '1';
			
			$evn_name = ($this->_jstatus) ? $db->escape($evn['event_name']) : $db->getEscaped($evn['event_name']);
			$evn_desc = ($this->_jstatus) ? $db->escape($evn['post_content']) : $db->getEscaped($evn['post_content']);
			$evn_desc_short = ($this->_jstatus) ? $db->escape($evn['short_description']) : $db->getEscaped($evn['short_description']);

            $q = "INSERT IGNORE INTO `#__miwoevents_events` (`id`, `parent_id`, `category_id`, `location_id`, `product_id`, `title`, `alias`, `event_type`, `event_date`, `event_end_date`, `introtext`, `fulltext`, `article_id`, `access`,".
                " `registration_access`, `individual_price`, `event_capacity`, `created_by`, `registration_type`,".
                " `recurring_type`, `recurring_frequency`, `weekdays`, `monthdays`, `recurring_end_date`,".
                " `cut_off_date`, `ordering`, `published`, `thumb`, `language`) ".
                "VALUES ('".$evn['event_id']."', '".$recurrence_id."', '".$ev_catid."', '".$evn['location_id']."', '0', '".$evn_name."', '".$evn['event_slug']."', '".$evn_type."', '".$st_date
                ."', '".$end_date."', '".$evn_desc."', '".$evn_desc_short."', '0', '1', '".$evn['event_rsvp']."', '".$evn_price
                ."', '".$evn['event_spaces']."', '".$evn['event_owner']."', '0', '".$rec_type."', '".$rec_freq."', '".$weekdays."', '".$monthdays."', '".$end_date
                ."', '".$cut_off_date."', '0', '".$evn['event_status']."', '".$ev_image."', '*')";
            $db->setQuery($q);
            $db->query();

			$cat = "SELECT `term_taxonomy_id` FROM `#__term_relationships` WHERE object_id = '".$evn['event_id']."' ORDER BY `object_id`";
			$db->setQuery($cat);
			$cats = $db->loadAssocList();

			foreach($cats as $cat) {
				$q = "INSERT IGNORE INTO `#__miwoevents_event_categories` (`event_id`, `category_id`) VALUES ('".$evn['event_id']."', '".$cat['term_taxonomy_id']."')";
				$db->setQuery($q);
				$db->query();
			}
        }

        return true;
    }

    public function migrateEventmanagerLocations(){
        $db = MFactory::getDBO();

        $loc = "SELECT * FROM #__em_locations ORDER BY location_id";
        $db->setQuery($loc);
        $locs = $db->loadAssocList();

        if (empty($locs)) {
            return false;
        }

        foreach($locs as $loc) {
            $coord = '';
            if(!empty($loc['location_latitude']) && !empty($loc['location_longitude'])){
                $coord = $loc['location_latitude'].','.$loc['location_longitude'];
            }
			
			$loc_name = ($this->_jstatus) ? $db->escape($loc['location_name']) : $db->getEscaped($loc['location_name']);
			$loc_desc = ($this->_jstatus) ? $db->escape($loc['post_content']) : $db->getEscaped($loc['post_content']);

            $q = "INSERT IGNORE INTO `#__miwoevents_locations` (`id`, `user_id`, `title`, `alias`, `description`, `address`, `geo_city`, `geo_state`, `geo_country`, `language`, `published`, coordinates) ".
                "VALUES ('".$loc['location_id']."', '".$loc['location_owner']."', '".$loc_name."', '".$loc['location_slug']."', '".$loc_desc."', '".$loc['location_address']."', '".$loc['location_town']."', '".$loc['location_state']."', '".$loc['location_country']."', '*', '".$loc['location_status']."', '".$coord."')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    # Event Espresso
    public function migrateEventespressoCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT * FROM #__events_category_detail ORDER BY id";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
			$cat_name = ($this->_jstatus) ? $db->escape($cat['category_name']) : $db->getEscaped($cat['category_name']);
			$cat_desc = ($this->_jstatus) ? $db->escape($cat['category_desc']) : $db->getEscaped($cat['category_desc']);

            $q = "INSERT IGNORE INTO `#__miwoevents_categories` (`id`, `parent`, `title`, `alias`, `introtext`, `published`, `ordering`, `access`, `language`) ".
                "VALUES ('".$cat['id']."', '0', '".$cat_name."', '".$cat['category_identifier']."', '".$cat_desc."', '1', '1', '1', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateEventespressoEvents(){
        $db = MFactory::getDBO();

        $evn = "SELECT * FROM #__events_detail ORDER BY id";
        $db->setQuery($evn);
        $evns = $db->loadAssocList();

        if (empty($evns)) {
            return false;
        }

        foreach($evns as $evn) {
			$end_time = $start_time = '00:00:00';
			
            $alias = MApplication::stringURLSafe(htmlspecialchars_decode($evn['event_name'], ENT_QUOTES));
			
			$multi_cat = explode(',', $evn['category']);
			$evn['category'] = (!empty($multi_cat)) ? $multi_cat[0] : $evn['category'];
			
			$reg_form = ($evn['display_reg_form'] == 'Y') ? '0' : '3';
			if($evn['allow_multiple'] == 'N' && $reg_form = '0') $reg_form = '1';
			
			$q = "SELECT start_time, end_time FROM `#__events_start_end` WHERE event_id = ".$evn['id'];
			$db->setQuery($q);
			$st_end_times = $db->loadObject();
			
			if(!empty($st_end_times)){
				$start_time = $st_end_times->start_time;
				$end_time = $st_end_times->end_time;
			}
			
			$st_date = $evn['start_date'].' '.$start_time;
			$end_date = $evn['end_date'].' '.$end_time;
			$reg_str = $evn['registration_start'].' '.$evn['registration_startT'];
			$reg_end = $evn['registration_end'].' '.$evn['registration_endT'];

			$evn_name = ($this->_jstatus) ? $db->escape($evn['event_name']) : $db->getEscaped($evn['event_name']);
			$evn_desc = ($this->_jstatus) ? $db->escape($evn['event_desc']) : $db->getEscaped($evn['event_desc']);

            $q = "INSERT IGNORE INTO `#__miwoevents_events` (`id`, `parent_id`, `category_id`, `location_id`, `product_id`, `title`, `alias`, `event_type`, `introtext`, `article_id`, `access`, `created_by`, `cut_off_date`, ".
                " `event_date`, `event_end_date`, `event_capacity`, `max_group_number`, `registration_type`, `published`, `language`) ".
                " VALUES ('".$evn['id']."', '0', '".$evn['category']."', '0', '0', '".$evn_name."', '".$evn['event_identifier']."', '0', '".$evn_desc."', '0', '1', '".$evn['wp_user']."', '".$reg_end."', ".
                " '".$st_date."', '".$end_date."', '".$evn['reg_limit']."', '".$evn['additional_limit']."', '".$reg_form."', '1', '*')";
            $db->setQuery($q);
            $db->query();
        }

        $cat = "SELECT * FROM #__events_category_rel ORDER BY id";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        foreach($cats as $cat) {
            $q = "INSERT IGNORE INTO `#__miwoevents_event_categories` (`id`, `event_id`, `category_id`) VALUES ('".$cat['id']."', '".$cat['event_id']."', '".$cat['cat_id']."')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    # Event Organiser
    public function migrateEventorganiserCats(){
        $db = MFactory::getDBO();

        $cat = "SELECT t.term_id AS category_id, t.slug AS alias, t.name AS category_name, tt.parent, tt.description AS category_desc FROM #__terms AS t INNER JOIN #__term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'event-category' ORDER BY t.term_id";
        $db->setQuery($cat);
        $cats = $db->loadAssocList();

        if (empty($cats)) {
            return false;
        }

        foreach($cats as $cat) {
			$color = get_option( "eo-event-category_".$cat['category_id']);
            $color['colour'] = strtoupper(str_replace('#', '', $color['colour']));

            $cat_name = ($this->_jstatus) ? $db->escape($cat['category_name']) : $db->getEscaped($cat['category_name']);
            $cat_desc = ($this->_jstatus) ? $db->escape($cat['category_desc']) : $db->getEscaped($cat['category_desc']);
			
            $q = "INSERT IGNORE INTO `#__miwoevents_categories` (`id`, `parent`, `title`, `alias`, `introtext`, `published`, `ordering`, `access`, `color_code`, `language`) ".
                "VALUES ('".$cat['category_id']."', '".$cat['parent']."', '".$cat_name."', '".$cat['alias']."', '".$cat_desc."', '1', '0', '1', '".$color['colour']."', '*')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

    public function migrateEventorganiserEvents(){
        $db = MFactory::getDBO();

        $evn = "SELECT ID AS ev_id, post_title, post_content, post_name, post_date, post_modified, post_status AS published, post_author FROM #__posts WHERE post_status != 'auto-draft' AND post_status != 'trash' AND post_type = 'event' AND post_parent = '0' ORDER BY ID";
        $db->setQuery($evn);
        $evns = $db->loadAssocList();

        if (empty($evns)) {
            return false;
        }

        foreach($evns as $evn) {
			$ev_duration = $ev_price = $ev_organizer = $venue_id = $dt_start = $dt_end = $ev_image = '';
			$evn_publish = '1';
			
			$q = "SELECT * FROM `#__eo_events` WHERE event_occurrence = '0' AND post_id = ".$evn['ev_id'];
			$db->setQuery($q);
			$metas = $db->loadObject();
			
			$st_date = $metas->StartDate.' '.$metas->StartTime;
			$end_date = $metas->EndDate.' '.$metas->FinishTime;
			
			if(!empty($evn['published'])){
				if($evn['published'] == 'publish'){
					$evn_publish = '1';
				}
				if($evn['published'] == 'pending'){
					$evn_publish = '0';
				}
			}
			
			$evn_img = MFactory::getWOption( "_thumbnail_id", $evn['ev_id']);
			
			$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$evn_img;
			$db->setQuery($q);
			$result_img = $db->loadResult();
			
			if(!empty($result_img)){
				self::copyEvnImages($result_img);

				$images = explode('/' , $result_img);
				$last1 = array_pop($images);
				
				$ev_image = empty($last1) ? '' : $last1;
			}
			
            $q = "SELECT r.term_taxonomy_id FROM `#__term_relationships` AS r INNER JOIN `#__term_taxonomy` AS t ON r.term_taxonomy_id = t.term_id WHERE t.taxonomy = 'event-category' AND r.object_id=".$evn['ev_id'];
            $db->setQuery($q);
            $ev_catid = $db->loadResult();
			
            $q = "SELECT r.term_taxonomy_id FROM `#__term_relationships` AS r INNER JOIN `#__term_taxonomy` AS t ON r.term_taxonomy_id = t.term_id WHERE t.taxonomy = 'event-venue' AND r.object_id=".$evn['ev_id'];
            $db->setQuery($q);
            $ev_locid = $db->loadResult();

            $recurr = "SELECT * FROM #__eo_events WHERE event_occurrence = '1' AND post_id ='{$evn['post_id']}'";
            $db->setQuery($recurr);
            $recurr = $db->loadAssocList();

            if($recurr){
                $event_type = '1';
            } else {
                $event_type = '0';
            }
			
			$evn_name = ($this->_jstatus) ? $db->escape($evn['post_title']) : $db->getEscaped($evn['post_title']);
			$evn_desc = ($this->_jstatus) ? $db->escape($evn['datdescription']) : $db->getEscaped($evn['datdescription']);

            $q = "INSERT IGNORE INTO `#__miwoevents_events` (`id`, `parent_id`, `category_id`, `location_id`, `product_id`, `title`, `alias`, `event_type`, `introtext`, `access`,".
                " `registration_access`, `event_capacity`, `created_by`, `registration_type`, `max_group_number`,".
                " `ordering`, `published`, `thumb`, `language`, `event_date`, `event_end_date`) ".
                "VALUES ('".$evn['ev_id']."', '0', '".$ev_catid."', '".$ev_locid."', '0', '".$evn_name."', '".$evn['post_name']."', '".$event_type."',".
                " '".$evn_desc."', '1', '1', '".$evn['max_multi_signup']."', '".$evn['created_by']."', '0', '".$evn['max_multi_signup']."',".
                " '0', '".$evn_publish."', '".$ev_image."', '*', '".$st_date."', '".$end_date."')";
            $db->setQuery($q);
            $db->query();

			$cat = "SELECT r.term_taxonomy_id FROM `#__term_relationships` AS r INNER JOIN `#__term_taxonomy` AS t ON r.term_taxonomy_id = t.term_id WHERE t.taxonomy = 'event-category' AND r.object_id = '".$evn['ev_id']."' ORDER BY `object_id`";
			$db->setQuery($cat);
			$cats = $db->loadAssocList();

			foreach($cats as $cat) {
				$q = "INSERT IGNORE INTO `#__miwoevents_event_categories` (`event_id`, `category_id`) VALUES ('".$evn['ev_id']."', '".$cat['term_taxonomy_id']."')";
				$db->setQuery($q);
				$db->query();
			}
			
			/*if($event_type = '1'){
				$evn = "SELECT ID AS ev_id, post_title, post_content, post_name, post_date, post_modified, post_status AS published, post_author FROM #__posts WHERE post_status != 'auto-draft' AND post_status != 'trash' AND post_type = 'event' AND post_parent = '0' ORDER BY ID";
				$db->setQuery($evn);
				$evns = $db->loadAssocList();

				if (empty($evns)) {
					return false;
				}
				
				foreach($evns as $evn) {
					$ev_duration = $ev_price = $ev_organizer = $venue_id = $dt_start = $dt_end = $ev_image = '';
					$evn_publish = '1';
					
					$q = "SELECT * FROM `#__eo_events` WHERE event_occurrence != '0' AND post_id = ".$evn['ev_id'];
					$db->setQuery($q);
					$metas = $db->loadObject();
					
					$st_date = $metas->StartDate.' '.$metas->StartTime;
					$end_date = $metas->EndDate.' '.$metas->FinishTime;
					
					if(!empty($evn['published'])){
						if($evn['published'] == 'publish'){
							$evn_publish = '1';
						}
						if($evn['published'] == 'pending'){
							$evn_publish = '0';
						}
					}
					
					$evn_img = MFactory::getWOption( "_thumbnail_id", $evn['ev_id']);
					
					$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$evn_img;
					$db->setQuery($q);
					$result_img = $db->loadResult();
					
					if(!empty($result_img)){
						self::copyEvnImages($result_img);

						$images = explode('/' , $result_img);
						$last1 = array_pop($images);
						
						$ev_image = empty($last1) ? '' : $last1;
					}
					
					$q = "SELECT r.term_taxonomy_id FROM `#__term_relationships` AS r INNER JOIN `#__term_taxonomy` AS t ON t.term_taxonomy_id = t.term_id WHERE t.taxonomy = 'event-category' AND r.object_id=".$evn['ev_id'];
					$db->setQuery($q);
					$ev_catid = $db->loadResult();
					
					$q = "SELECT r.term_taxonomy_id FROM `#__term_relationships` AS r INNER JOIN `#__term_taxonomy` AS t ON t.term_taxonomy_id = t.term_id WHERE t.taxonomy = 'event-venue' AND r.object_id=".$evn['ev_id'];
					$db->setQuery($q);
					$ev_locid = $db->loadResult();

					$recurr = "SELECT * FROM #__eo_events WHERE event_occurrence = '1' AND post_id ='{$evn['post_id']}'";
					$db->setQuery($recurr);
					$recurr = $db->loadAssocList();

					if($recurr){
						$event_type = '1';
					} else {
						$event_type = '0';
					}
					
					$evn_name = ($this->_jstatus) ? $db->escape($evn['title']) : $db->getEscaped($evn['title']);
					$evn_desc = ($this->_jstatus) ? $db->escape($evn['datdescription']) : $db->getEscaped($evn['datdescription']);

					$q = "INSERT IGNORE INTO `#__miwoevents_events` (`id`, `parent_id`, `category_id`, `location_id`, `product_id`, `title`, `alias`, `event_type`, `introtext`, `access`,".
						" `registration_access`, `event_capacity`, `created_by`, `registration_type`, `max_group_number`".
						" `ordering`, `published`, `thumb`, `language`, `event_date`, `event_end_date`) ".
						"VALUES ('".$evn['ev_id']."', '0', '".$ev_catid."', '".$ev_locid."', '0', '".$evn_name."', '".$evn['post_name']."', '".$event_type."',".
						" '".$evn_desc."', '1', '1', '".$evn['max_multi_signup']."', '".$evn['created_by']."', '0', '".$evn['max_multi_signup']."',".
						" '0', '".$evn_publish."', '".$ev_image."', '*', '".$st_date."', '".$end_date."')";
					$db->setQuery($q);
					$db->query();

					$cat = "SELECT term_taxonomy_id FROM `#__term_relationships` AS r INNER JOIN `#__term_taxonomy` AS t ON t.term_taxonomy_id = t.term_id WHERE t.taxonomy = 'event-category' AND r.object_id = '".$evn['ev_id']."' ORDER BY `object_id`";
					$db->setQuery($cat);
					$cats = $db->loadAssocList();

					foreach($cats as $cat) {
						$q = "INSERT IGNORE INTO `#__miwoevents_event_categories` (`event_id`, `category_id`) VALUES ('".$evn['ev_id']."', '".$cat['term_taxonomy_id']."')";
						$db->setQuery($q);
						$db->query();
					}
				}
			}*/
        }

        return true;
    }

    public function migrateEventorganiserLocations(){
        $db = MFactory::getDBO();

        $loc = "SELECT t.term_id AS loc_id, t.slug AS alias, t.name AS venue FROM #__terms AS t INNER JOIN #__term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'event-venue' ORDER BY t.term_id";
        $db->setQuery($loc);
        $locs = $db->loadAssocList();

        if (empty($locs)) {
            return false;
        }

        foreach($locs as $loc) {
			$address = $city = $state = $country = $loc_lat = $loc_lng = $loc_desc = $coord = $loc_postcode = '';
			
			$q = "SELECT meta_key, meta_value FROM `#__eo_venuemeta` WHERE eo_venue_id = ".$loc['loc_id'];
			$db->setQuery($q);
			$metas = $db->loadAssocList();
			
			foreach($metas as $meta){
				switch($meta['meta_key']){
					case '_address':
						$address = $meta['meta_value'];
						break;
					case '_city':
						$city = $meta['meta_value'];
						break;
					case '_state':
						$state = $meta['meta_value'];
						break;
                    case '_postcode':
                        $loc_postcode = $meta['meta_value'];
                        break;
					case '_country':
						$country = $meta['meta_value'];
						break;
					case '_lat':
						$loc_lat = $meta['meta_value'];
						break;
					case '_lng':
						$loc_lng = $meta['meta_value'];
						break;
					case '_description':
						$loc_desc = $meta['meta_value'];
						break;
				}
			}

            if(!empty($loc_lat) && !empty($loc_lng) && $loc_lat != '0.000000' && $loc_lng != '0.000000'){
                $coord = $loc_lat.','.$loc_lng;
            } else {
                $address = $loc_postcode.', '.$address;
            }
			
			$loc_name = ($this->_jstatus) ? $db->escape($loc['venue']) : $db->getEscaped($loc['venue']);

            $q = "INSERT IGNORE INTO `#__miwoevents_locations` (`id`, `title`, `alias`, `description`, `address`, `geo_city`, `geo_state`, `geo_country`, `coordinates`, `language`, `published`) ".
                "VALUES ('".$loc['loc_id']."', '".$loc_name."', '".$loc['alias']."', '".$loc_desc."', '".$address."', '".$city."', '".$state."', '".$country."', '".$coord."', '*', '1')";
            $db->setQuery($q);
            $db->query();
        }

        return true;
    }

	# Copy Images
    public function copyEvnImages($results) {
        /*$db = MFactory::getDBO();
		$q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$id;
		$db->setQuery($q);
		$results = $db->loadResult();*/

        $images = explode('/' , $results);
        $last1 = array_pop($images);
        $last2 = array_pop($images);
        $last3 = array_pop($images);

        $pro_images = MPATH_MEDIA.'/'.$last3.'/'.$last2.'/';

        self::_copyImages($pro_images, $last1);
        return;
    }

    public function copyEvnImagesId($id) {
        $db = MFactory::getDBO();
        $q = "SELECT guid FROM `#__posts` WHERE post_type LIKE 'attachment' AND ID=".$id;
        $db->setQuery($q);
        $results = $db->loadResult();

        $images = explode('/' , $results);
        $last1 = array_pop($images);
        $last2 = array_pop($images);
        $last3 = array_pop($images);

        $pro_images = MPATH_MEDIA.'/'.$last3.'/'.$last2.'/';

        self::_copyImages($pro_images, $last1);
        return;
    }

    public function _copyImages($dir, $image_name) {
        foreach (glob($dir . "*") as $filename) {
            if (MFolder::exists($filename)) {
                continue;
            }
			
			$media_path = MPATH_WP_CNT.'/miwi/media/miwoevents/images/';

            if (file_exists('/'.$media_path . $image_name)) {
                continue;
            }

            if($image_name == basename($filename)){
                if (!MFile::copy($filename, ABSPATH . $media_path . basename($filename))){
                    echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
                }
                if (!MFile::copy($filename, ABSPATH . $media_path . 'thumbs/' . basename($filename))){
                    echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
                }
            }
        }
    }

    public function _copyFiles($dir) {
        foreach (glob($dir . "*") as $filename) {
            if (MFolder::exists($filename)) {
                continue;
            }

            if (!MFile::copy($filename, MPATH_WP_CNT.'/miwi/media/miwoevents/images/' . basename($filename))){
                echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
            }

            if (!MFile::copy($filename, MPATH_WP_CNT.'/miwi/media/miwoevents/images/thumbs/' . basename($filename))){
                echo 'Failed to copy <i>' . $filename . '</i> to image directory.<br />';
            }
        }
    }
}