<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsModelEvent extends MiwoeventsModel {

    public function __construct() {
		parent::__construct('event', 'events');

        $this->event_id = MiwoEvents::getInput()->getInt('event_id', 0);

        $task = MiwoEvents::getInput()->getCmd('task', '');
        $layout = MiwoEvents::getInput()->getCmd('layout', '');
        $tasks = array('edit', 'apply', 'save', 'save2new');
        if (in_array($task, $tasks) or ($layout == 'submit')) {
            $this->setId((int)$this->event_id);

            require_once(MPATH_MIWOEVENTS_ADMIN.'/models/events.php');
            $this->_admin_model = new MiwoEventsModelEvents();
        }
	}

    public function getData() {
		if (empty($this->_data)) {
            $nullDate = $this->_db->getNullDate();
			$user_id = MFactory::getUser()->get('id');

			$this->_data = MiwoEvents::get('events')->getEvent($this->event_id);

			//Get total registered user
			$this->_data->total_attenders = (int)MiwoDatabase::loadResult('SELECT SUM(number_attenders) FROM #__miwoevents_attenders WHERE event_id='.$this->_data->id);

			$this->_data->category_id = Miwoevents::get('utility')->getEventCategory($this->event_id)->id;
			
			
			if ($user_id > 0) {
				$this->_data->user_registered = (int)MiwoDatabase::loadResult('SELECT COUNT(id) FROM #__miwoevents_attenders WHERE user_id='.$user_id.' AND event_id='.$this->event_id);

 				if ($this->MiwoeventsConfig->show_discounted_price) {
 			        $discount = 0 ;
 			        $row = $this->_data;

 				    if (($row->early_bird_discount_date != $nullDate) and ($row->date_diff >= 0)) {
         		        if ($row->early_bird_discount_type == 0) {
         					$discount += $row->individual_price * $row->early_bird_discount_amount / 100 ;
         				}
                         else {
         					$discount += $row->early_bird_discount_amount;
         				}
 				    }

         			$this->_data->discounted_price = $this->_data->individual_price - $discount;
         			
         			if ($this->_data->discounted_price <= 0){ $this->_data->discounted_price = 0; }
 				}
			}
            else {
				$this->_data->user_registered = 0;

				if ($this->MiwoeventsConfig->show_discounted_price) {
				    $discount = 0;
			        $row = $this->_data;

                    if (($row->early_bird_discount_date != $nullDate) and ($row->date_diff >= 0)) {
        		        if ($row->early_bird_discount_type == 0) {
        					$discount += $row->individual_price * $row->early_bird_discount_amount / 100;
        				}
                        else {
        					$discount += $row->early_bird_discount_amount;
        				}
				    }

				    $this->_data->discounted_price = $this->_data->individual_price - $discount;
				}
			}
			
		}

		return $this->_data;
	}
	
	# Get Total Events
    public function getTotalEvents() {
        return MiwoDatabase::loadResult("SELECT COUNT(*) FROM #__miwoevents_events WHERE id = {$this->event_id} AND published = 1");
    }
    
    # Get Event Product ID from Miwoshop
	public function getProductID() {
		return MiwoDatabase::loadResult("SELECT product_id FROM #__miwoevents_events WHERE id = {$this->event_id} ORDER BY id DESC LIMIT 1");
	}

    public function autoComplete($query){
        return $this->_admin_model->autoComplete($query);
    }

    public function getFields() {
        return $this->_admin_model->getFields();
    }

    public function getCategories() {
   		return $this->_admin_model->getCategories();
   	}

   	public function getEventCategories() {
   		return MiwoDatabase::loadResultArray('SELECT category_id FROM #__miwoevents_event_categories WHERE event_id='.$this->_id);
   	}

   	public function getLocations() {
   		return $this->_admin_model->getLocations();
   	}

    public function getEditData($table = NULL) {
   		if (empty($this->_data)) {
            $row = MiwoEvents::getTable('MiwoEventsEvents');
            $row->load($this->_id);

   			if (empty($this->_id)) {
                $row->early_bird = '';
            }
            else {
                $row->early_bird = ''; # TODO:: get from miwoshop tables
            }

            switch ($row->recurring_type) {
               case 1:
                   $row->number_days   = $row->recurring_frequency;
                   $row->number_weeks  = 0;
                   $row->number_months = 0;
                   break;
               case 2:
                   $row->number_days   = 0;
                   $row->number_weeks  = $row->recurring_frequency;
                   $row->number_months = 0;
                   break;
               case 3:
                   $row->number_days   = 0;
                   $row->number_weeks  = 0;
                   $row->number_months = $row->recurring_frequency;
                   break;
               default:
                   $row->number_days   = 0;
                   $row->number_weeks  = 0;
                   $row->number_months = 0;
                   break;
            }

            $row->early_bird_option = 0;

            $this->_data = $row;
   		}

   		return $this->_data;
   	}

    public function store(&$data) {}

    public function updateStatus($value = 0) {
        if (!MiwoDatabase::query("UPDATE #__{$this->_component}_{$this->_table} SET published = '{$value}' WHERE id = ".$this->event_id)) {
            return false;
        }

        return true;
    }
	
	public function exportCal($event,$Itemid) {
    	
    	# Set Time
    	function ts($ts){
    		$x = array("-", ":");
    		$ts = str_replace(" ", "T", $ts."Z");
    		return $ts = str_replace($x, "", $ts);
    	}
    	
    	$eTitle 	= strip_tags($event->title);
    	$eDateStart = ts($event->event_date);
    	$eDateEnd 	= ts($event->event_end_date);
    	$eDates 	= $eDateStart."/".$eDateEnd;
    	$eDetail 	= strip_tags($event->introtext.$event->fulltext);
    	$eLocation 	= MiwoDatabase::loadResult("SELECT title FROM #__miwoevents_locations WHERE id = {$event->location_id} ORDER BY id DESC LIMIT 1");
    	// 20130828T131500Z/20130828T151500Z date time start/finish
    	
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
    	
    	$url = MRoute::_('index.php?option=com_miwoevents&view=event&task=ecalex&event_id='.$event->id.$Itemid);
    	
        $exportcal	
        	= array(
        		"google"	=> "http://www.google.com/calendar/event?action=TEMPLATE&text={$eTitle}&dates={$eDates}&details={$eDetail}&location={$eLocation}&trp=true",
				"microsoft"	=> "http://calendar.live.com/calendar/calendar.aspx?rru=addevent&dtstart={$eDateStart}={$eDateEnd}&summary={$eTitle}&location={$eLocation}",
        		"ical" 		=> $url,
        		"facebook" 	=> "http://www.facebook.com/sharer/sharer.php?u="
        	);
        return $exportcal;
    }
    
    # Event Calendar Export (iCal)
    public function ecalex($eventid) {
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: inline; filename=calendar.ics');
		$sessID = "miwoevents_ical".$eventid;
		echo $_SESSION[$sessID];
		exit();
    }
}