<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class MiwoeventsEmail {

    public function __construct() {
		$this->MiwoeventsConfig = MiwoEvents::getConfig();
	}

    public function sendNewRegistration($order_id, $status_id, $group_id = null) {
        switch ($status_id) {
            case 3:
                $registrant_mail = array('subject' => 'registrant_email_subject', 'body' => 'registrant_email_body');
                $admin_mail = array('subject' => 'admin_email_subject', 'body' => 'admin_email_body');

                $this->_send($order_id, $registrant_mail, true, true, $admin_mail, $group_id);

                break;
            case 11:
            case 12:
                if ($this->MiwoeventsConfig->waitinglist_enabled) {
                    $this->sendWaitinglist($order_id);
                }

                break;
        }

        return;
    }

    public function sendRegistrationApproved($order_id) {
        $registrant_mail = array('subject' => 'registration_approved_email_subject', 'body' => 'registration_approved_email_body');

        $this->_send($order_id, $registrant_mail);
    }

    public function sendWaitinglist($order_id) {
        $registrant_mail = array('subject' => 'watinglist_confirmation_subject', 'body' => 'watinglist_confirmation_body');
        $admin_mail = array('subject' => 'watinglist_notification_subject', 'body' => 'watinglist_notification_body');

        $this->_send($order_id, $registrant_mail, true, true, $admin_mail);
    }

    public function sendCancelRegistration($order_id) {
        $admin_mail = array('subject' => 'registration_cancel_email_subject', 'body' => 'registration_cancel_email_body');

        $this->_send($order_id, null, false, false, $admin_mail);
    }

    public function sendCancelRegistrationRequest($attender, $event) {
        $jconfig = MFactory::getConfig();

        if (MiwoEvents::is30()) {
            $mailer = MFactory::getMailer();
        }

        $fields = json_decode($attender->fields);

        $fromName = !empty($this->MiwoeventsConfig->from_name) ? $this->MiwoeventsConfig->from_name : $jconfig->get('fromname');
        $fromEmail = !empty($this->MiwoeventsConfig->from_email) ? $this->MiwoeventsConfig->from_email : $jconfig->get('mailfrom');

        if (strlen(trim($event->notification_emails)) > 0) {
            $emails = $event->notification_emails;
        }
        else if (strlen(trim($this->MiwoeventsConfig->notification_emails)) > 0) {
            $emails = $this->MiwoeventsConfig->notification_emails;
        }
        else {
            $emails = $fromEmail;
        }

        $emails = explode(',', str_replace(' ', '', $emails));

        $subject = MText::_('COM_MIWOEVENTS_REGISTRATION_CANCEL_REQUEST_SUBJECT');
        $body = MText::_('COM_MIWOEVENTS_REGISTRATION_CANCEL_REQUEST_BODY');

        $subject = str_replace('[EVENT_TITLE]', $event->title, $subject);
        $body = str_replace('[EVENT_TITLE]', $event->title, $body);
        $body = str_replace('[ORDER_ID]', $attender->order_id, $body);
        $body = str_replace('[ATTENDER_FIRSTNAME]', $fields->{$this->MiwoeventsConfig->firstname_field}, $body);
        $body = str_replace('[ATTENDER_LASTNAME]', $fields->{$this->MiwoeventsConfig->lastname_field}, $body);
		
		# Multi Language
		if ($this->MiwoeventsConfig->load_plugins) {
            $subject = MHtml::_('content.prepare', $subject);
            $body = MHtml::_('content.prepare', $body);
        }
		
        foreach ($emails as $email) {
            if (MiwoEvents::is30()) {
                $mailer->sendMail($fromEmail, $fromName, $email, $subject, $body, 1);
            }
            else {
                MUtility::sendMail($fromEmail, $fromName, $email, $subject, $body, 1);
            }
        }
    }

    public function _send($order_id, $registrant_mail = array(), $cc = false, $attachment = false, $admin_mail = array(), $group_id = null) {
    	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	$jconfig = MFactory::getConfig();
        if($order_id == 999999998 || $order_id==999999999){
		// order_id 999999998 ve Individual olduğunu anlıyor. mail yollarken kullanılıyor. order_id 999999999 ve Individual olduğunu anlıyor.  
			$attenders = MiwoDatabase::loadObjectList("SELECT * FROM #__miwoevents_attenders WHERE order_id = {$order_id} ORDER BY id DESC LIMIT 1");
		}elseif($order_id == 999999997){
			// order_id 999999997 ve Individual olduğunu anlıyor. mail yollarken kullanılıyor. 
			$order_id = 999999998;
			$attenders = MiwoDatabase::loadObjectList("SELECT * FROM #__miwoevents_attenders WHERE group_id = {$group_id}");
		}else{
			$attenders = MiwoDatabase::loadObjectList("SELECT * FROM #__miwoevents_attenders WHERE order_id = {$order_id}");
		}
  
  //  	$attenders = MiwoDatabase::loadObjectList("SELECT * FROM #__miwoevents_attenders WHERE order_id = {$order_id}");
    	
    	if (empty($attenders)) {
            return;
        }

        if($this->MiwoeventsConfig->version_select_control == "booking2"){
            # MiwoShop
            require_once(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php');
        }
            $user_id  = $attenders[0]->user_id;
            $event_id = $attenders[0]->event_id;
            $is_group = (count($attenders) > 1) ? true : false;

            $user 				= MFactory::getUser($user_id);


        $event = MiwoEvents::get('events')->getEvent($event_id);

        if($this->MiwoeventsConfig->version_select_control == "booking2" and (!($this->MiwoeventsConfig->free_events_control) or $event->individual_price> 0)){
            $registrant 		= MiwoShop::get('user')->getOCustomerByEmail($user->get('email'));
            $registrant_address = MiwoDatabase::loadObject("SELECT * FROM #__miwoshop_address WHERE address_id = {$registrant['address_id']}");
        }else{
            $ids = $user->get('id') ;
            if(!empty($ids)){
                $evet_without_shop = MiwoDatabase::loadObject("SELECT * FROM #__miwoevents_attenders WHERE user_id = {$user->get('id')}");
                $fields_ws = json_decode($evet_without_shop->fields);
            }else{
                $evet_without_shop = $attenders[0];
                $fields_ws = json_decode($evet_without_shop->fields);

            }
        }

         //   $event = MiwoEvents::get('events')->getEvent($event_id);

            $fromName 	= !empty($this->MiwoeventsConfig->from_name) ? $this->MiwoeventsConfig->from_name : $jconfig->get('fromname');
            $fromEmail 	= !empty($this->MiwoeventsConfig->from_email) ? $this->MiwoeventsConfig->from_email : $jconfig->get('mailfrom');

            $registration_details = $this->_getRegistrationDetails($attenders, $event_id, $is_group);

            $replaces = array();
            $replaces['event_title']	= $event->title;
            $replaces['event_date'] 	= MHtml::_('date', $event->event_date, $this->MiwoeventsConfig->event_date_format, null);
            $replaces['first_name'] 	= (!empty ($fields_ws->miwi_firstname))? $fields_ws->miwi_fistname:$registrant_address->firstname;
            $replaces['last_name'] 		= (!empty ($fields_ws->miwi_lastname))? $fields_ws->miwi_lastname:$registrant_address->lastname;
            $replaces['organization'] 	= (!empty ($fields_ws->miwi_ordanizasyon))?'':$registrant_address->company;
            $replaces['address'] 		= (!empty ($fields_ws->miwi_address))? $fields_ws->miwi_address:$registrant_address->address_1;
            $replaces['address2'] 		= (!empty ($fields_ws->miwi_address2))? $fields_ws->miwi_address2:$registrant_address->address_2;
            $replaces['city'] 			= (!empty ($fields_ws->miwi_city))? $fields_ws->miwi_city:$registrant_address->city;
            $replaces['zip'] 			= (!empty ($fields_ws->miwi_zip))? $fields_ws->miwi_zip:$registrant_address->postcode;
            $replaces['country'] 		= (!empty ($fields_ws->miwi_country))? $fields_ws->miwi_country:$registrant_address->country_id;
            $replaces['phone'] 			= (!empty ($fields_ws->miwi_phone))? $fields_ws->miwi_phone:$registrant['telephone'];
            $replaces['fax'] 			= (!empty ($fields_ws->miwi_fax))? $fields_ws->miwi_fax:$registrant['fax'];
            $replaces['email'] 			= (!empty ($fields_ws->miwi_email))? $fields_ws->miwi_email:$registrant['email'];

    	//Add support for location tag
        $location = MiwoEvents::get('utility')->getLocation($event->location_id);
        if ($location) {
            $replaces['location'] = $location->title.' ('.$location->address.')';
        } else {
            $replaces['location'] = '';
        }
        
        # J3 Mail Function
    	if (MiwoEvents::is30()) {
        	$mailer = MFactory::getMailer();
        }

        # Get Clander Link
        $exportcal = $this->exportCal($event,$this->Itemid);

        # Calendar İcal Google and Microsoft
        $calendarAdd  = '<span class="ecalex">';
        $calendarAdd .= "\n";
        $calendarAdd .= "<!-- ical -->\n";
        $calendarAdd .= '<a style="text-decoration: none;" title="'.MText::_('COM_MIWOEVENTS_EXPORTCAL_ICAL').'" href="'.$exportcal['ical'].'" > ';
        $calendarAdd .= "\n";
        $calendarAdd .= '<img class="" src="'.MURL_MIWOEVENTS.'/site/assets/images/ical.png'.'"/> </a>';
        $calendarAdd .= "\n";
        $calendarAdd .= "<!-- google -->\n";
        $calendarAdd .= '<a style="text-decoration: none;" title="'.MText::_('COM_MIWOEVENTS_EXPORTCAL_GOOGLE').'" href="'.$exportcal['google'].'" target="_blank">';
        $calendarAdd .= "\n";
        $calendarAdd .= '<img class="" src="'.MURL_MIWOEVENTS.'/site/assets/images/gcal.png'.'"/></a>';
        $calendarAdd .= "\n";
        $calendarAdd .= "<!-- microsoft -->\n";
        $calendarAdd .= '<a style="text-decoration: none;" title="'.MText::_('COM_MIWOEVENTS_EXPORTCAL_MICROSOFT').'" href="'.$exportcal['microsoft'].'" target="_blank">';
        $calendarAdd .= "\n";
        $calendarAdd .= '<img class="" src="'.MURL_MIWOEVENTS.'/site/assets/images/mcal.png'.'"/></a>';
        $calendarAdd .= "\n";
        $calendarAdd .= "</span>";
        $calendarAdd .= "\n";

    	# Admin Mail
    	# Send notification emails
        if (strlen(trim($event->notification_emails)) > 0) {
            $emails = $event->notification_emails;
        }
        else if (strlen(trim($this->MiwoeventsConfig->notification_emails)) > 0) {
            $emails = $this->MiwoeventsConfig->notification_emails;
        }
        else {
            $emails = $fromEmail;
        }
        
        if (!empty($admin_mail)) {
        	
        	$emails = explode(',', str_replace(' ', '', $emails));

        	$subject = $this->MiwoeventsConfig->$admin_mail['subject'];

            $body = $calendarAdd . $this->MiwoeventsConfig->$admin_mail['body'];
	
	        $subject = str_replace('[EVENT_TITLE]', $event->title, $subject);
	        $body = str_replace('[REGISTRATION_DETAIL]', $registration_details, $body);
	        
	        foreach ($replaces as $key => $value) {
	            $key = strtoupper($key) ;
	            $body = str_replace("[$key]", $value, $body);
	        }
        	$AdminEmails = $emails;
			
			# Multi Language
			if ($this->MiwoeventsConfig->load_plugins) {
				$subject = MHtml::_('content.prepare', $subject);
				$body = MHtml::_('content.prepare', $body);
			}
			
	        foreach ($emails as $email) {
	        	if (MiwoEvents::is30()) {
					$mailer->sendMail($fromEmail, $fromName, $email, $subject, $body, 1);
				}
				else {
					MUtility::sendMail($fromEmail, $fromName, $email, $subject, $body, 1);
				}	
	        }
        }
        
        # Attenders Mail
    	if (!empty($registrant_mail)) {
            $subject = $this->MiwoeventsConfig->$registrant_mail['subject'];

            if (strlen(trim(strip_tags($event->$registrant_mail['body'])))) {
                $body = $calendarAdd . $event->$registrant_mail['body'];
            }
            else {
                $body = $calendarAdd . $this->MiwoeventsConfig->$registrant_mail['body'];
            }

            $subject = str_replace('[EVENT_TITLE]', $event->title, $subject);
            $body = str_replace('[REGISTRATION_DETAIL]', $registration_details, $body);

            foreach ($replaces as $key => $value) {
                $key = strtoupper($key) ;
                $body = str_replace("[$key]", $value, $body);
            }

            $ccEmails = null;
            if ($cc and $is_group and $this->MiwoeventsConfig->send_email_to_group_members) {
                $ccEmails = array();

                foreach($attenders as $attender) {
                    $fieldatt = $attender->fields;
                    $fieldatt = json_decode($fieldatt);
                    //$attender_email = MiwoEvents::get('attenders')->getEmail($attender);
					$attender_email = $fieldatt->miwi_email;
					if(!empty($registrant)){
						if (!empty($attender_email) and ($attender_email != $registrant['email']) and !in_array($attender_email, $ccEmails)) {
							$ccEmails[] = $attender_email;
						}
					}else{
						if (!empty($attender_email) and ($attender_email != $fields_ws->miwi_email) and !in_array($attender_email, $ccEmails)) {
							$ccEmails[] = $attender_email;
						}
					 }
                }
            }
			
			# Multi Language
			if ($this->MiwoeventsConfig->load_plugins) {
				$subject = MHtml::_('content.prepare', $subject);
				$body = MHtml::_('content.prepare', $body);
			}
			
			if(!empty($registrant)){
                $diffMail = $registrant['email'] ;
            }else{
                $diffMail =  $fields_ws->miwi_email;
            }
            
            ## Send Attender Mail
            if ($attachment and $event->attachment) {
                if (MiwoEvents::is30()) {
                    $this->attenderMail($fromEmail, $fromName, $diffMail, $subject, $body, 1, $ccEmails, null, MPATH_MEDIA.'/miwoevents/'.$event->attachment);
                }
                else {
                    MUtility::sendMail($fromEmail, $fromName, $diffMail, $subject, $body, 1, $ccEmails, null, MPATH_MEDIA.'/miwoevents/'.$event->attachment);
                }
            }elseif (empty($ccEmails)) {
                if (MiwoEvents::is30()) {
                    $this->attenderMail($fromEmail, $fromName, $diffMail, $subject, $body, 1);
                }
                else {
                    MUtility::sendMail($fromEmail, $fromName, $diffMail, $subject, $body, 1);
                }
            }else {
                if (MiwoEvents::is30()) {
                    $this->attenderMail($fromEmail, $fromName, $diffMail, $subject, $body, 1, $ccEmails);
                }
                else {
                    MUtility::sendMail($fromEmail, $fromName, $diffMail, $subject, $body, 1, $ccEmails);
                }
            }
        }
    }

    public function attenderMail($fromEmail, $fromName, $diffMail, $subject, $body, $mode = false, $ccEmails = null, $attachment = null){
        $mailer = MFactory::getMailer();
        if(empty($ccEmails)){
            $mailer->sendMail($fromEmail, $fromName, $diffMail, $subject, $body, 1);
        }elseif(!empty($attachment)){
            $mailer->sendMail($fromEmail, $fromName, $diffMail, $subject, $body, 1,$ccEmails,$attachment);
        }else{
            $mailer->sendMail($fromEmail, $fromName, $diffMail, $subject, $body, 1,$ccEmails);
        }
    }
	
    public function _getRegistrationDetails($attenders, $event_id, $is_group = false) {
        $old_option = MRequest::getCmd('option');
        $old_view = MRequest::getCmd('view');

        MRequest::setVar('option', 'com_miwoevents');
        MRequest::setVar('view', 'registration');

        ob_start();

        require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

        require_once(MPATH_WP_PLG.'/miwoevents/site/views/registration/view.html.php');

        $options['name'] = 'registration';
        $options['layout'] = 'email';
        $options['base_path'] = MPATH_MIWOEVENTS;

        $view = new MiwoeventsViewRegistration($options);

        $event = MiwoEvents::get('events')->getEvent($event_id);
        $location = MiwoEvents::get('utility')->getLocation($event->location_id);

        $view->event = $event;
        $view->location = $location;
        $view->attenders = $attenders;

        $view->displayEmail();

        $output = ob_get_contents();

        ob_end_clean();

        MRequest::setVar('option', $old_option);
        MRequest::setVar('view', $old_view);

        return $output;
    }

       public function exportCal($event,$Itemid) {

        $eTitle 	= strip_tags($event->title);
        $eDateStart = $this->ts($event->event_date);
        $eDateEnd 	= $this->ts($event->event_end_date);
        $eDates 	= $eDateStart."/".$eDateEnd;
        $eDetail 	= strip_tags($event->introtext.$event->fulltext);
        $eLocation 	= MiwoDatabase::loadResult("SELECT title FROM #__miwoevents_locations WHERE id = {$event->location_id} ORDER BY id DESC LIMIT 1");

        $ical  = "BEGIN:VCALENDAR\n";
        $ical .= "VERSION:2.0\n";
        $ical .= "PRODID:-//Miwisoft//MiwoEvents//EN\n";
        $ical .= "BEGIN:VEVENT\n";
        $ical .= "UID:" . md5(uniqid(mt_rand(), true)) . "@miwisoft.com\n";
        $ical .= "DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z\n";
        $ical .= "DTSTART:{$eDateStart}\n";
        $ical .= "DTEND:{$eDateEnd}\n";
        $ical .= "SUMMARY:{$eTitle}\n";
        $ical .= "END:VEVENT\n";
        $ical .= "END:VCALENDAR";

        $sessID = "miwoevents_ical".$event->id;
        $_SESSION[$sessID] = $ical;

        $url = MUri::base().'index.php?option=com_miwoevents&view=event&task=ecalex&event_id='.$event->id.$Itemid;

        $exportcal
            = array(
            "google"	=> "http://www.google.com/calendar/event?action=TEMPLATE&text={$eTitle}&dates={$eDates}&details={$eDetail}&location={$eLocation}&trp=true",
            "microsoft"	=> "http://calendar.live.com/calendar/calendar.aspx?rru=addevent&dtstart={$eDateStart}={$eDateEnd}&summary={$eTitle}&location={$eLocation}",
            "ical" 		=> $url,
            "facebook" 	=> "http://www.facebook.com/sharer/sharer.php?u="
        );
        return $exportcal;
    }

    # Set Time
    function ts($ts){
        $x = array("-", ":");
        $ts = str_replace(" ", "T", $ts."Z");
        return $ts = str_replace($x, "", $ts);
    }
}