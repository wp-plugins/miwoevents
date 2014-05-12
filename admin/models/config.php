<?php
/**
 * @package        MiwoEvents
 * @copyright      2009-2014 Miwisoft LLC, miwisoft.com
 * @license        GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('_MEXEC') or die;

class MiwoeventsModelConfig extends MiwoeventsModel {

	public function __construct() {
		parent::__construct('config');
	}

	// Save configuration
	function save() {
		$config                                         = new stdClass();
		$config->pid                                    = MRequest::getString('pid', '');
		$config->version_checker                        = MRequest::getInt('version_checker', '1');
		$config->show_db_errors                         = MRequest::getInt('show_db_errors', '0');
		$config->cb_integration                         = MRequest::getInt('cb_integration', '0');
		$config->waitinglist_enabled                    = MRequest::getInt('waitinglist_enabled', '0');
		$config->prevent_duplicate_registration         = MRequest::getInt('prevent_duplicate_registration', '0');
//		$config->include_group_member_in_csv_export     = MRequest::getInt('include_group_member_in_csv_export', '1');
//		$config->include_group_billing_in_attenders     = MRequest::getInt('include_group_billing_in_attenders', '1');
//		$config->add_events_user_or_group_ids           = '25';
//		$config->registrant_access_user_ids             = '62';
		$config->activate_recurring_event               = MRequest::getInt('activate_recurring_event', '0');
		$config->hide_past_events                       = MRequest::getInt('hide_past_events', '0');
//		$config->fix_next_button                        = MRequest::getInt('prevent_duplicate_registration', '0');
//		$config->fix_breadcrumbs                        = MRequest::getInt('prevent_duplicate_registration', '0');
//		$config->accept_term                            = MRequest::getInt('prevent_duplicate_registration', '0');
//		$config->article_id                             = MRequest::getInt('prevent_duplicate_registration', '0');
		$config->attachment_file_types                  = MRequest::getString('attachment_file_types', 'bmp|gif|jpg|png|swf|zip|doc|pdf|xls');
		$config->date_format                            = MRequest::getString('date_format', 'd-m-Y');
		$config->event_date_format                      = MRequest::getString('event_date_format', 'd-m-Y g:i a');
		$config->event_time_format                      = MRequest::getString('event_time_format', 'g:i a');
//		$config->decimals                               = '2';
//		$config->dec_point                              = '.';
//		$config->thousands_sep                          = ',';
		$config->currency_position                      = MRequest::getInt('currency_position', '0');
//		$config->default_country                        = 'United States';
		$config->from_name                              = MRequest::getWord('from_name', '');
		$config->from_email                             = MRequest::getWord('from_email', '');
		$config->notification_emails                    = MRequest::getString('notification_emails', '');;
		$config->admin_email_subject                    = MRequest::getString('admin_email_subject', 'New Registration For Event : [EVENT_TITLE]');
		$config->admin_email_body                       = MRequest::getVar('admin_email_body', '<p>Dear administrator</p><p>User [FIRST_NAME] [LAST_NAME] has just registered for event <strong>[EVENT_TITLE]</strong>. The registration detail is as follow :</p><p>[REGISTRATION_DETAIL]</p><p>Regards,</p><p>Events management team</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->registrant_email_subject               = MRequest::getWord('registrant_email_subject', 'Event registration confirmation');
		$config->registrant_email_body                  = MRequest::getVar('registrant_email_body', '<p>Dear <strong>[FIRST_NAME] [LAST_NAME]</strong></p><p>You have just registered for event <strong>[EVENT_TITLE]</strong>. The registration detail is as follow :</p><p>[REGISTRATION_DETAIL]</p><p>Regards,</p><p>Events management Team</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->registration_form_message              = MRequest::getVar('registration_form_message', '<p>Please enter information in the form below to process registration for event <strong>[EVENT_TITLE]</strong>.</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->registration_form_message_group        = MRequest::getVar('registration_form_message_group', '<p>Please enter information in the form below to complete group registration for event <strong>[EVENT_TITLE]</strong>.</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->number_members_form_message            = MRequest::getVar('number_members_form_message', '<p>Please enter number of members for your group registration. Number of members need to be greater than or equal 2. You can enter detail information of these members in the next step.</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->member_information_form_message        = MRequest::getVar('member_information_form_message', '<p>Please enter the information of the group members in the following forms. Fields marked with (<span class="required">*</span>) are required.</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->thanks_message                         = MRequest::getVar('thanks_message', '<p>Thanks for registering for event <strong>[EVENT_TITLE]</strong>. Your registration detail is as follow :</p><p>[REGISTRATION_DETAIL]</p><p>Regards,</p><p>Events management Team</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->cancel_message                         = MRequest::getVar('cancel_message', '<p>Your registration for event [EVENT_TITLE] was cancelled.</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->registration_cancel_message_free       = MRequest::getVar('registration_cancel_message_free', '<p>You have just cancel your registration for event [EVENT_TITLE]</p><p>Thanks,</p><p>Event Registration Team</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->registration_cancel_message_paid       = MRequest::getVar('registration_cancel_message_paid', '<p>Your registration for event <strong>[EVENT_TITLE]</strong> has successfully cancelled. Our event registration team will check your registration and process the refund within 24 hours from now .</p><p>Thanks,</p><p>Registration Team</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->invitation_form_message                = MRequest::getVar('invitation_form_message', '<p>Please enter information in the form below to send invitation to your friends to invite them to register for the event <strong>[EVENT_TITLE]</strong></p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->invitation_email_subject               = MRequest::getString('invitation_email_subject', 'Invitation to register for event [EVENT_TITLE]');
		$config->invitation_email_body                  = MRequest::getVar('invitation_email_body', '<p>Dear <strong>[NAME]</strong></p><p>Your friend <strong>[SENDER_NAME]</strong> has suggested you to view and register for the event <strong>[EVENT_TITLE]</strong> in our site. Please access to <strong>[EVENT_DETAIL_LINK]</strong> to view and register for the event.</p><p>Note from [SENDER_NAME] :</p><p><em>[PERSONAL_MESSAGE]</em></p><p>Regards,</p><p>Events manager team</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->invitation_complete                    = MRequest::getVar('invitation_complete', '<p>The invitation was sent to your friends. Thank you !</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->reminder_email_subject                 = MRequest::getString('invitation_email_subject', 'Reminder for event [EVENT_TITLE]');
		$config->reminder_email_body                    = MRequest::getVar('reminder_email_body', '<p>Dear <strong>[FIRST_NAME] [LAST_NAME]</strong></p><p>This email is used to remind you that you have registered for event [EVENT_TITLE]. The event will occur on <strong>[EVENT_DATE]</strong>, so please come and attend the event on time.</p><p>Regards,</p><p>Website administrator tea</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->registration_cancel_email_subject      = MRequest::getString('registration_cancel_email_subject', 'Registration Cancel for even [EVENT_TITLE]');
		$config->registration_cancel_email_body         = MRequest::getVar('registration_cancel_email_body', '<p>Dear administrator</p><p>User <strong>[FIRST_NAME] [LAST_NAME]</strong> has just cancel their registration for event <strong>[EVENT_TITLE]</strong> . You can login to back-end of your site to see the detail and process the refund if needed .</p><p>Regards,</p><p>Administrator Team</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->watinglist_confirmation_subject        = MRequest::getString('watinglist_confirmation_subject', 'Waitinglist confirmation');
		$config->watinglist_confirmation_body           = MRequest::getVar('watinglist_confirmation_body', '<p>Dear <strong>[FIRST_NAME] [LAST_NAME]</strong></p><p>Thanks for joining waitinglist of our event [EVENT_TITLE] . We will inform you as if there is someone cancel their registration and you can attend the event.</p><p>Regards,</p><p>Events management team<span style="white-space: pre;"> </span></p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->watinglist_notification_subject        = MRequest::getString('watinglist_notification_subject', 'Waitinglist Notification');
		$config->watinglist_notification_body           = MRequest::getVar('watinglist_notification_body', '<p>Dear Administrator</p><p>User <strong>[FIRST_NAME] [LAST_NAME] </strong>has just joined waitinglist for event <strong>[EVENT_TITLE] . </strong></p><p>Regards,</p><p>Events management team</p>', 'default', 'none', MREQUEST_ALLOWHTML);
		$config->calendar_theme                         = MRequest::getWord('calendar_theme', 'default');
		$config->show_multiple_days_event_in_calendar   = MRequest::getInt('show_multiple_days_event_in_calendar', '1');
		$config->show_event_time                        = MRequest::getInt('show_event_time', '0');
		$config->show_empty_cat                         = MRequest::getInt('show_empty_cat', '1');
		$config->show_number_events                     = MRequest::getInt('show_number_events', '1');
		$config->number_categories                      = MRequest::getInt('number_categories', '7');
		$config->number_events                          = MRequest::getInt('number_events', '5');
		$config->order_events                           = MRequest::getInt('order_events', '2');
		$config->show_capacity                          = MRequest::getInt('show_capacity', '1');
		$config->show_cut_off_date                      = MRequest::getInt('show_cut_off_date', '1');
		$config->show_individual_price                  = MRequest::getInt('show_individual_price', '1');
		$config->show_registered                        = MRequest::getInt('show_registered', '1');
		$config->show_available_place                   = MRequest::getInt('show_available_place', '1');
		$config->show_image_in_table_layout             = MRequest::getInt('show_image_in_table_layout', '1');
		$config->show_list_of_attenders                 = MRequest::getInt('show_list_of_attenders', '1');
		$config->show_location_in_category_view         = MRequest::getInt('show_location_in_category_view', '0');
		$config->show_event_location_in_email           = MRequest::getInt('show_event_location_in_email', '0');
		$config->load_plugins                           = MRequest::getInt('load_plugins', '0');
		$config->show_cat_decription_in_calendar_layout = MRequest::getInt('show_cat_decription_in_calendar_layout', '1');
		$config->display_message_for_full_event         = MRequest::getInt('display_message_for_full_event', '1');
		$config->show_price_for_free_event              = MRequest::getInt('show_price_for_free_event', '1');
		$config->show_discounted_price                  = MRequest::getInt('show_discounted_price', '0');
		$config->show_event_date                        = MRequest::getInt('show_event_date', '0');
//		$config->show_fb_like_button                    = MRequest::getInt('show_fb_like_button', '1');
		$config->show_social_bookmark                   = MRequest::getInt('show_social_bookmark', '1');
		$config->show_invite_friend                     = MRequest::getInt('show_invite_friend', '1');
		$config->activate_weekly_calendar_view          = MRequest::getInt('activate_weekly_calendar_view', '1');
		$config->activate_daily_calendar_view           = MRequest::getInt('activate_daily_calendar_view', '0');
		$config->calendar_start_date                    = MRequest::getInt('calendar_start_date', '1');
		$config->comments                               = MRequest::getInt('comments', '0');
		$config->firstname_field                        = MRequest::getWord('firstname_field', 'miwi_firstname');
		$config->lastname_field                         = MRequest::getWord('lastname_field', 'miwi_lastname');
		$config->email_field                            = MRequest::getWord('email_field', 'miwi_email');
		$config->button_class                           = MRequest::getString('button_class', MiwoEvents::is30() ? 'btn btn-primary' : 'miwoevents_button');

		$config->show_fields_in_category  = MRequest::getInt('show_fields_in_category', '0');
		$config->paid_order_status        = MRequest::getInt('paid_order_status', '5');
		$config->cancelled_order_status   = MRequest::getInt('cancelled_order_status', '7');
		$config->pending_order_status     = MRequest::getInt('pending_order_status', '1');
		$config->csv_delimiter            = MRequest::getString('csv_delimiter', ',');
		$config->show_map_info            = MRequest::getInt('show_map_info', '0');
		$config->show_price_in_mod_events = MRequest::getInt('show_price_in_mod_events', '1');
		$config->cart_or_checkout         = MRequest::getInt('cart_or_checkout', '1');

		$mforms = MRequest::getVar('mform');
		foreach ($mforms as $key => $mform) {
			if (is_array($mform)) {
				$config->$key  = new stdClass();
				foreach ($mform as $sub_key => $form) {
					$config->$key->$sub_key = $form;
				}
			}
			else {
				$config->$key = $mform;
			}
		}

		Miwoevents::get('utility')->storeConfig($config);

		$this->cleanCache('_system');
	}
}