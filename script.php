<?php
/**
* @package		MiwoEvents
* @copyright	2009-2014 Miwosoft LLC, miwosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
# No Permission
defined('MIWI') or die('Restricted access');

// Import Libraries
mimport('framework.application.helper');
mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');
mimport('framework.installer.installer');
class com_MiwoeventsInstallerScript {
	private $_current_version = null;
	private $_is_new_installation = true;

	public function preflight($type, $parent) {
		$db = MFactory::getDBO();
		$db->setQuery('SELECT option_value FROM #__options WHERE option_name = "miwoevents"');
		$config = $db->loadResult();
		
		if (! empty($config)) {
			$this->_is_new_installation = false;
			
			$miwoevents_xml = ABSPATH . 'wp-content/plugins/miwoevents/admin/miwoevents.xml';
			
			if (MFile::exists($miwoevents_xml)) {
				$xml = simplexml_load_file($miwoevents_xml, 'SimpleXMLElement');
				$this->_current_version = (string) $xml->version;
			}
		}
	}

	public function postflight($type, $parent) {
		$db = MFactory::getDBO();
		$src = __FILE__;
		
		#Remove htaccess file to support image feature
		if (MFile::exists(MPATH_ROOT . '/media/miwoevents/.htaccess')) {
			MFile::delete(MPATH_ROOT . '/media/miwoevents/.htaccess');
		}
		
		require_once (ABSPATH . 'wp-content/plugins/miwoevents/admin/library/miwoevents.php');
		
		if (MFolder::copy(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/languages'), MPath::clean(MPATH_MIWI . '/languages'), null, true)) {
			MFolder::delete(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/languages'));
		}
		if (MFolder::copy(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/languages'), MPath::clean(MPATH_MIWI . '/languages'), null, true)) {
			MFolder::delete(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/languages'));
		}
		if (MFolder::copy(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/media'), MPath::clean(ABSPATH.'wp-content/uploads/miwoevents'), null, true)) {
			MFolder::delete(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/media'));
		}
		if (! MFolder::exists(MPATH_MIWI . '/media/miwoevents/events')) {
			MFolder::create(MPATH_ROOT . '/media/miwoevents/events');
		}
		if (MFolder::copy(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/modules'), MPath::clean(MPATH_MIWI . '/modules'), null, true)) {
			MFolder::delete(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/modules'));
		}
		if (MFolder::copy(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/plugins'), MPath::clean(MPATH_MIWI . '/plugins'), null, true)) {
			MFolder::delete(MPath::clean(ABSPATH . 'wp-content/plugins/miwoevents/plugins'));
		}		
		
		//@TODO Delete this code next version(Current Version 1.0.1)
		if ($type == 'upgrade') {
			return;
		}
		########
		
		if ($this->_is_new_installation == true) {
			$this->_installMiwoevents();
		}
		else {
			$this->_updateMiwoevents();
		}
	}

	protected function _installMiwoevents() {
		$db = MFactory::getDbo();
		
		$config = new stdClass();
		$config->pid = '';
		$config->version_checker = '1';
		$config->show_db_errors = '0';
		$config->cb_integration = '0';
		$config->waitinglist_enabled = '0';
		$config->prevent_duplicate_registration = '0';
		$config->include_group_member_in_csv_export = '1';
		$config->include_group_billing_in_attenders = '1';
		$config->add_events_user_or_group_ids = '25';
		$config->registrant_access_user_ids = '62';
		$config->activate_recurring_event = '0';
		$config->hide_past_events = '0';
		$config->fix_next_button = '0';
		$config->fix_breadcrumbs = '0';
		$config->accept_term = '0';
		$config->article_id = '0';
		$config->attachment_file_types = 'bmp|gif|jpg|png|swf|zip|doc|pdf|xls';
		$config->date_format = 'd-m-Y';
		$config->event_date_format = 'd-m-Y g:i a';
		$config->event_time_format = 'g:i a';
		$config->currency_symbol = '$';
		$config->decimals = '2';
		$config->dec_point = '.';
		$config->thousands_sep = ',';
		$config->currency_position = '0';
		$config->default_country = 'United States';
		$config->from_name = '';
		$config->from_email = '';
		$config->notification_emails = '';
		$config->admin_email_subject = '';
		$config->admin_email_body = '';
		$config->registrant_email_subject = '';
		$config->registrant_email_body = '';
		$config->registration_form_message = '';
		$config->registration_form_message_group = '';
		$config->number_members_form_message = '';
		$config->member_information_form_message = '';
		$config->thanks_message = '';
		$config->cancel_message = '';
		$config->registration_cancel_message_free = '';		
		$config->registration_cancel_message_paid = '';
		$config->invitation_form_message = '';
		$config->invitation_email_subject = '';
		$config->invitation_email_body = '';
		$config->invitation_complete = '';
		$config->reminder_email_subject = '';
		$config->reminder_email_body = '';
		$config->registration_cancel_email_subject = '';
		$config->registration_cancel_email_body = '';
		$config->watinglist_confirmation_subject = '';
		$config->watinglist_confirmation_body = '';
		$config->watinglist_notification_subject = '';
		$config->watinglist_notification_body = '';
		$config->calendar_theme = 'default';
		$config->show_multiple_days_event_in_calendar = '1';
		$config->show_event_time = '0';
		$config->show_empty_cat = '1';
		$config->show_number_events = '1';
		$config->number_categories = '7';
		$config->number_events = '5';
		$config->order_events = '2';
		$config->show_capacity = '1';
		$config->show_cut_off_date = '1';
		$config->show_individual_price = '1';
		$config->show_registered = '1';
		$config->show_available_place = '1';
		$config->show_image_in_table_layout = '1';
		$config->show_list_of_attenders = '1';
		$config->show_location_in_category_view = '0';
		$config->show_event_location_in_email = '0';
		$config->load_plugins = '0';
		$config->show_cat_decription_in_calendar_layout = '1';
		$config->display_message_for_full_event = '1';
		$config->show_price_for_free_event = '1';
		$config->show_discounted_price = '0';
		$config->show_event_date = '0';
		$config->show_fb_like_button = '1';
		$config->show_social_bookmark = '1';
		$config->show_invite_friend = '1';
		$config->activate_weekly_calendar_view = '1';
		$config->activate_daily_calendar_view = '0';
		$config->calendar_start_date = '1';
		$config->comments = '0';
		$config->firstname_field = 'mijo_firstname';
		$config->lastname_field = 'mijo_lastname';
		$config->email_field = 'mijo_email';
		$config->button_class = MiwoEvents::is30() ? 'btn btn-primary' : 'miwoevents_button';
		
		$config->show_fields_in_category = '0';
		$config->paid_order_status = '5';
		$config->cancelled_order_status = '7';
		$config->pending_order_status = '1';
		$config->csv_delimiter = ',';
		$config->show_map_info = '0';
		$config->show_price_in_mod_events = '1';
		$config->cart_or_checkout = '1';
		
		# Individual Fields
		$config->individual_fields = new stdClass();
		
		# Group Fields
		$config->group_fields = new stdClass();
		
		# Search / List Fields
		$config->search_list_fields = new stdClass();
		$config->search_list_fields->search = new stdClass();
		
		$reg = new MRegistry($config);
		$config = $reg->toString();
		
		$db->setQuery('INSERT INTO `#__options` (option_name, option_value) VALUES ("miwoevents", '.$db->Quote($config).')');
		$db->query();
		
		
		# sample data
		# Category
		$db->setQuery("INSERT IGNORE INTO `#__miwoevents_categories` (`id`, `parent`, `title`, `alias`, `introtext`, `fulltext`, `ordering`, `access`, `color_code`, `language`, `meta_desc`, `meta_key`, `meta_author`, `published`) VALUES 
        		(1, 0, 'Joomla Days', 'joomla-days', '<p>JoomlaDay™ events are officially recognized, but not organized, by the Joomla! Project and Open Source Matters, Inc (OSM). Each event is managed independently by a local community.</p>', '', 1, 1, 'FFF3D6', '*', '', '', '', 1)");
		$db->query();
		
		# Location
		$db->setQuery("INSERT IGNORE INTO `#__miwoevents_locations` (`id`, `user_id`, `title`, `alias`, `description`, `address`, `geo_city`, `geo_state`, `geo_country`, `coordinates`, `language`, `meta_desc`, `meta_key`, `meta_author`, `published`) VALUES 
        		(1, 988, 'Noordwijkerhout (NL)', 'noordwijkerhout-nl', '<p>Surrounded by woodlands and wild dunes on the edge of Noordwijkerhout.</p>', 'NH Conference Centre Leeuwenhorst 3 Langelaan 2211 XT Noordwijkerhout, Netherlands', '', '', '', '52.2514562,4.4739822', '*', '', '', '', 1), 
        		(2, 988, 'San Jose (USA)', 'san-jose-usa', '<p>The eBay Town Hall.</p>', '2161 N 1st St. (btn Charcot & Karina), San Jose, CA 95131, USA', '', '', '', '37.3902956,-121.8961047', '*', '', '', '', 1)");
		$db->query();
		
		# Fields
		$db->setQuery("INSERT IGNORE INTO `#__miwoevents_fields` (`id`, `name`, `title`, `description`, `field_type`, `values`, `default_values`, `display_in`, `rows`, `cols`, `size`, `css_class`, `field_mapping`, `ordering`, `access`, `language`, `published`) VALUES 
        		(13, 'mijo_organiser', 'Organised by', '', 'text', '', '', 2, 0, 0, 25, 'inputbox', '', 13, 1, '*', 1),
        		(14, 'mijo_website', 'Website', '', 'text', '', '', 2, 0, 0, 25, 'inputbox', '', 14, 1, '*', 1)");
		$db->query();
		
		# Events
		$db->setQuery("INSERT IGNORE INTO `#__miwoevents_events` (`id`, `parent_id`, `category_id`, `location_id`, `product_id`, `title`, `alias`, `event_type`, `event_date`, `event_end_date`, `introtext`, `fulltext`, `article_id`, `access`, `registration_access`, `individual_price`, `event_capacity`, `created_by`, `cut_off_date`, `registration_type`, `max_group_number`, `early_bird_discount_type`, `early_bird_discount_date`, `early_bird_discount_amount`, `group_rates`, `enable_cancel_registration`, `cancel_before_date`, `enable_auto_reminder`, `remind_before_x_days`, `recurring_type`, `recurring_frequency`, `weekdays`, `monthdays`, `recurring_end_date`, `recurring_occurrencies`, `attachment`, `notification_emails`, `registrant_email_body`, `thanks_message`, `params`, `ordering`, `published`, `meta_desc`, `meta_key`, `meta_author`, `fields`, `currency_symbol`, `thumb`, `registration_approved_email_body`, `language`) VALUES
        		(1, 0, 0, 1, 0, 'J and Beyond 2013', 'j-and-beyond-2013', 0, '2013-05-31 02:00:00', '2013-06-02 02:00:00', '<p>Need a hot Joomla! tip? Make an investment in yourself and go to JandBeyond 2013.</p>\r\n', '\r\n<p>At JandBeyond all kinds of Joomla! experts share their knowledge, tricks, and experience in a spirit of open source friendlness and fun, and do Joomlers know how to have fun! Last years event was amazing and I wouldn''t miss this event.</p>', 0, 1, 1, '279.00', 500, 0, '2013-05-29 00:00:00', 0, '', 1, '2013-04-30 00:00:00', '200.00', '', 0, '2013-05-28 00:00:00', 0, 3, 0, 0, NULL, '', '0000-00-00 00:00:00', 0, '', '', '', '', '{\"if_mijo_address\":1,\"if_mijo_city\":0,\"if_mijo_comment\":1,\"if_mijo_country\":1,\"if_mijo_email\":2,\"if_mijo_fax\":0,\"if_mijo_firstname\":2,\"if_mijo_lastname\":2,\"if_mijo_organization\":0,\"if_mijo_phone\":1,\"if_mijo_state\":0,\"if_mijo_zip\":0,\"gf_mijo_address\":1,\"gf_mijo_city\":0,\"gf_mijo_comment\":1,\"gf_mijo_country\":1,\"gf_mijo_email\":2,\"gf_mijo_fax\":0,\"gf_mijo_firstname\":2,\"gf_mijo_lastname\":2,\"gf_mijo_organization\":1,\"gf_mijo_phone\":0,\"gf_mijo_state\":0,\"gf_mijo_zip\":0}', 17, 1, '', '', '', '{\"mijo_organiser\":\"Robert Deutz\",\"mijo_website\":\"http:\\/\\/jandbeyond.org\"}', '$', 'jab13.jpg', '', '*'),
        		(2, 0, 0, 2, 0, 'Joomla Conference 2014', 'joomla-conference-2014', 0, '2014-05-23 02:00:00', '2014-05-25 02:00:00', '<p>This conference is a community meeting, and a great starting point for getting involved in the Joomla! community.</p>\r\n', '\r\n<p>You''ll get information about almost every aspect of the Joomla! CMS, the Joomla! Web Application Platform, and about the Joomla! Community.</p>', 0, 1, 1, '100.00', 0, 0, '2014-04-30 00:00:00', 0, '', 0, '2013-05-22 00:00:00', '0.00', '[{\"number\":\"10\",\"price\":\"90\"},{\"number\":\"20\",\"price\":\"80\"},{\"number\":\"30\",\"price\":\"60\"}]', 1, '2014-04-20 00:00:00', 0, 3, 0, 0, NULL, '', '0000-00-00 00:00:00', 0, '', '', '', '', '{\"if_mijo_address\":1,\"if_mijo_city\":0,\"if_mijo_comment\":1,\"if_mijo_country\":1,\"if_mijo_email\":2,\"if_mijo_fax\":0,\"if_mijo_firstname\":2,\"if_mijo_lastname\":2,\"if_mijo_organization\":0,\"if_mijo_phone\":1,\"if_mijo_state\":0,\"if_mijo_zip\":0,\"gf_mijo_address\":1,\"gf_mijo_city\":0,\"gf_mijo_comment\":1,\"gf_mijo_country\":1,\"gf_mijo_email\":2,\"gf_mijo_fax\":0,\"gf_mijo_firstname\":2,\"gf_mijo_lastname\":2,\"gf_mijo_organization\":1,\"gf_mijo_phone\":0,\"gf_mijo_state\":0,\"gf_mijo_zip\":0}', 19, 1, '', '', '', '{\"mijo_organiser\":\"OSM\"}', '$', 'jwc14.jpg', '', '*')");
		$db->query();
		
		$db->setQuery("INSERT IGNORE INTO `#__miwoevents_event_categories` (`id`, `event_id`, `category_id`) VALUES 
        		(17, 1, 1),
        		(18, 2, 1)");
		$db->query();		
	
		$this->addPage();
	}

	protected function _updateMiwoevents() {
		if (empty($this->_current_version)) {
			return;
		}
		
		if ($this->_current_version == '1.0.0') {
			return;
		}
	}

	public function uninstall($parent) {
		$db = MFactory::getDBO();
		$src = __FILE__;
	}
	
	public function addPage(){
        $page_content="<!-- MiwoEvents Shortcode. Please do not remove to event plugin work properly. -->[miwoevents]<!-- MiwoEvents Shortcode End. -->";
        add_option("miwoevents_page_id",'','','yes');

        $miwoevents_post  = array();
        $_tmp_page      = null;

        $id = get_option("miwoevents_page_id");

        if (!empty($id) && $id > 0) {
            $_tmp_page = get_post($id);
        }

        if ($_tmp_page != null){
            $miwoevents_post['ID']            = $id;
            $miwoevents_post['post_status']   = 'publish';

            wp_update_post($miwoevents_post);
        }
        else{
            $miwoevents_post['post_title']    = 'Events';
            $miwoevents_post['post_content']  = $page_content;
            $miwoevents_post['post_status']   = 'publish';
            $miwoevents_post['post_author']   = 1;
            $miwoevents_post['post_type']     = 'page';
            $miwoevents_post['comment_status']= 'closed';

            $id = wp_insert_post($miwoevents_post);
            update_option('miwoevents_page_id',$id);
        }
    }
}