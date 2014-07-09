<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

class plgSystemMiwoevents extends MPlugin {

	public static $running;
	
	public function onAfterInitialise() {
		$miwoevents = MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php';
		
		if (!file_exists($miwoevents)) {
			return true;
		}
		
		if (!file_exists(self::$running)) {
			return true;
		}
		
		self::$running = true;
		
		$last_run = (int) $this->params->get('last_run', 0);
		$frequency = (int) $this->params->get('frequency', 50);
		$number_attenders = (int) $this->params->get('number_attenders', 0);
		$current_time = time();
		$number_minutes = ($current_time - $last_run) / 60;
		
		if ($number_minutes >= $frequency) {
			$db = MFactory::getDbo();
			
			require_once($miwoevents);
			
			$this->_sendEmails($number_attenders);
			
			$this->params->set('last_run', $current_time);
			$params = $this->params->toString();
			
			$db->setQuery('SELECT extension_id FROM #__extensions WHERE element = "miwoeventsreminder" AND `folder` = "system"');
			$p_id = $db->loadResult();
			
			$db->setQuery('UPDATE #__extensions SET params = '.$db->quote($params).' WHERE extension_id = '.$p_id);
			$db->query();
		}
		
		self::$running = false;
		
		return true;		
	}

    public function onAfterRoute() {
        $app = MFactory::getApplication();
        $db = MFactory::getDBO();

        if ($app->isSite()) {
			$this->_checkRedirection();
			
            return true;
        }

        if (!isset($_POST['mform']['currency_symbol']) or !isset($_POST['component']) or $_POST['component'] != 'com_miwoevents'){
            return true;
        }

		$miwoevents = MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php';
		if (!file_exists($miwoevents)) {
			return true;
		}

		require_once($miwoevents);

        $miwoshop = MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php';
        if (!file_exists($miwoshop)) {
            return true;
        }

        require_once($miwoshop);

        $symbol = $_POST['mform']['currency_symbol'];
        
        $code = MiwoDatabase::loadResult("SELECT code FROM #__miwoshop_currency WHERE symbol_left = '{$symbol}' OR symbol_right = '{$symbol}'");

        MiwoDatabase::query("UPDATE #__miwoshop_setting SET `value` = '{$code}' WHERE `key` = 'config_currency'");
	}
	
	public function _checkRedirection() {
		$app = MFactory::getApplication();

        $miwoevents = MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php';
		if (!file_exists($miwoevents)) {
			return;
		}
		
		require_once($miwoevents);
		
		$plugin = MPluginHelper::getPlugin('system', 'miwoevents');
	    $params = new MRegistry($plugin->params);

		$option = MRequest::getCmd('option');
		
		$link = '';
		
		if (!empty($option)) {
			switch ($option) {
				case 'com_jevents':
					if ($params->get('redirect_jevents', '0') == '1') {
						$link = self::_getJeventsLink();
					}
					break;
				case 'com_eventsnova':
					if ($params->get('redirect_eventsnova', '0') == '1') {
						$link = self::_getEventsnovaLink();
					}
					break;
				case 'com_ohanah':
					if ($params->get('redirect_ohanah', '0') == '1') {
						$link = self::_getOhanahLink();
					}
					break;
				case 'com_eventbooking':
					if ($params->get('redirect_eventbooking', '0') == '1') {
						$link = self::_getEventbookingLink();
					}
					break;
				case 'com_rseventpro':
					if ($params->get('redirect_rseventpro', '0') == '1') {
						$link = self::_getRseventproLink();
					}
					break;
				case 'com_redevent':
					if ($params->get('redirect_redevent', '0') == '1') {
						$link = self::_getRedeventLink();
					}
					break;
				case 'com_dtregister':
					if ($params->get('redirect_dtregister', '0') == '1') {
						$link = self::_getDtregisterLink();
					}
					break;
				case 'com_icagenda':
					if ($params->get('redirect_icagenda', '0') == '1') {
						$link = self::_getIcagendaLink();
					}
					break;
				default:
					return true;
			}
		}
		
		if (empty($link)) {
			return true;
		}
		
		$Itemid = MRequest::getInt('Itemid', '');
		$lang = MRequest::getWord('lang', '');
		
		if (!empty($Itemid)) {
			$Itemid = '&Itemid='.$Itemid;
		}
		
		if (!empty($lang)) {
			$lang = '&lang='.$lang;
		}
		
		$url = MRoute::_('index.php?option=com_miwoevents&'.$link.$Itemid.$lang);

		$app->redirect($url, '', 'message', true);
	}
	
	protected function _sendEmails($number_attenders = 0) {
		$db 		= MFactory::getDBO();
		$jconfig 	= MFactory::getConfig();
		$config 	= MiwoEvents::getConfig();
		
		$fromEmail 	= $jconfig->get('mailfrom');
		$fromName 	= $jconfig->get('fromname');
		$subject 	= $config->reminder_email_subject;
		$body 		= $config->reminder_email_body;
		
		$is_j3 		= MiwoEvents::is30();
		
		if ($is_j3) {
			$mailer = MFactory::getMailer();
		}
		
		$sql = "
		SELECT a.id, a.fields, a.register_date, b.id AS event_id, b.title AS event_title, b.event_date
		FROM #__miwoevents_attenders AS a INNER JOIN #__miwoevents_events AS b
		ON a.event_id = b.id
		WHERE a.reminder_sent = 0 AND b.enable_auto_reminder=1 AND (b.remind_before_x_days =< DATEDIFF(b.event_date, NOW())) AND (DATEDIFF(b.event_date, NOW()) >=0) 
		ORDER BY b.event_date, a.register_date
		LIMIT {$number_attenders}
		";
		
//		SELECT a.id, a.fields, a.register_date, b.id AS event_id, b.title AS event_title, b.event_date, DATEDIFF(b.event_date, NOW()) as rx, b.remind_before_x_days	
//		WHERE a.reminder_sent = 0 AND b.enable_auto_reminder=1 AND b.remind_before_x_days <= rx
		
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		
		$param = null;
		$ids = array();
		
		foreach ($rows as $row) {
			$ids[] 	= $row->id;
			$fields = json_decode($row->fields);
			
			$emailSubject 	= $subject;
			$emailSubject 	= str_replace('[EVENT_TITLE]', $row->event_title , $emailSubject);
			$emailBody 		= $body;
			
			$replaces 				= array();
			$replaces['event_date']	= MHtml::_('date', $row->event_date, $config->event_date_format, $param);
			$replaces['event_title']= $row->event_title;
			$first_name				= $config->firstname_field;
			$last_name				= $config->lastname_field;
			$email					= $config->email_field;
			$replaces['first_name'] = $fields->$first_name;
			$replaces['last_name'] 	= $fields->$last_name;
			
			foreach ($replaces as $key=>$value) {
				$emailBody = str_replace('['.strtoupper($key).']', $value, $emailBody);
			}
			
			if ($is_j3) {
				$mailer->sendMail($fromEmail, $fromName, $fields->email, $emailSubject, $emailBody, 1);
			}
			else {
				MUtility::sendMail($fromEmail, $fromName, $fields->email, $emailSubject, $emailBody, 1);
			}							 		
		}	
		
		if (count($ids)) {
			$db->setQuery('UPDATE #__miwoevents_attenders SET reminder_sent = 1 WHERE id IN ('.implode(',', $ids).')');
			$db->query() ;	
		}
	}
	
	public function _getJeventsLink(){
		$link = '';
		
		$task = MRequest::getString('task');
		$ev_id = MRequest::getInt('evid');
		$cat_id = MRequest::getInt('category_fv');
		
		if (!empty($task)) {
			switch ($task) {
				case 'cat.listevents':
					if (!empty($cat_id)) {
						$link = 'view=category&category_id='.$cat_id;
					}
					break;
				case 'icalrepeat.detail':
					if (!empty($ev_id)) {
						$link = 'view=event&event_id='.$ev_id;
					}
					break;
			}
		}
		
		return $link;
	}
	
	public function _getEventsnovaLink(){
		$link = '';
		
		$view = MRequest::getString('view');
		$ev_id = MRequest::getInt('eventid');
		$cat_id = MRequest::getString('catid');
		$loc_id = MRequest::getInt('venueid');

        if (!empty($view)) {
            switch ($view) {
                case 'categorydetail':
                    if (!empty($cat_id)) {
                        $link = 'view=category&category_id='.$cat_id;
                    }
                    break;
                case 'eventdetail':
                    if (!empty($ev_id)) {
                        $link = 'view=event&event_id='.$ev_id;
                    }
                    break;
                case 'venuedetail':
                    if (!empty($loc_id)) {
                        $link = 'view=location&location_id='.$loc_id;
                    }
                    break;
            }
        }
		
		return $link;
	}
	
	public function _getOhanahLink(){
		$link = '';
		
		$view = MRequest::getString('view');
		$ev_id = MRequest::getInt('id');
		$cat_id = MRequest::getString('ohanah_category_id');
		$o_ev_id = MRequest::getString('ohanah_event_id');
		$loc_id = MRequest::getInt('ohanah_venue_id');

        if (!empty($view)) {
            switch ($view) {
                case 'events':
                    if (!empty($cat_id)) {
                        $link = 'view=category&category_id='.$cat_id;
                    }
                    if (!empty($loc_id)) {
                        $link = 'view=location&location_id='.$loc_id;
                    }
                    break;
                case 'event':
                    if (!empty($ev_id)) {
                        $link = 'view=event&event_id='.$ev_id;
                    }
                    else if (!empty($o_ev_id)) {
                        $link = 'view=event&event_id='.$o_ev_id;
                    }
                    break;
            }
        }
		
		return $link;
	}

    public function _getEventbookingLink(){
        $link = '';
		
        $task = MRequest::getString('task');
        $ev_id = MRequest::getInt('event_id');
        $cat_id = MRequest::getInt('category_id');
        $loc_id = MRequest::getInt('location_id');

        if (!empty($task)) {
            switch ($task) {
                case 'view_category':
                    if (!empty($cat_id)) {
                        $link = 'view=category&category_id='.$cat_id;
                    }
                    break;
                case 'view_event':
                    if (!empty($ev_id)) {
                        $link = 'view=event&event_id='.$ev_id;
                    }
                    break;
                case 'view_map':
                    if (!empty($loc_id)) {
                        $link = 'view=event&event_id='.$loc_id;
                    }
                    break;
            }
        }

        return $link;
    }
	
	public function _getRseventproLink(){
		$link = '';
		
		$layout = MRequest::getString('layout');
		$ev_id = MRequest::getInt('cid');
		$cat_id = MRequest::getInt('category');
		$loc_id = MRequest::getInt('location');

        if (!empty($cat_id)) {
            $link = 'view=category&category_id='.$cat_id;
        }
        else if (!empty($loc_id)) {
            $link = 'view=event&event_id='.$loc_id;
        }
        else if (!empty($ev_id) && !empty($layout) && $layout == 'show') {
            $link = 'view=event&event_id='.$ev_id;
        }
		
		return $link;
	}

	public function _getRedeventLink(){
		$link = '';
		
		$view = MRequest::getString('view');
		$id = MRequest::getInt('id');

        if (!empty($view)) {
            switch ($view) {
                case 'categoryevents':
                    if (!empty($id)) {
                        $link = 'view=category&category_id='.$id;
                    }
                    break;
                case 'details':
                    if (!empty($id)) {
                        $link = 'view=event&event_id='.$id;
                    }
                    break;
                case 'venueevents':
                    if (!empty($id)) {
                        $link = 'view=event&event_id='.$id;
                    }
                    break;
            }
        }

		return $link;
	}

	public function _getDtregisterLink(){
		$link = '';

		$controller = MRequest::getString('controller');
		$task = MRequest::getString('task');
		$id = MRequest::getInt('id');
		$ev_id = MRequest::getInt('eventId');

        if (!empty($controller)) {
            switch ($controller) {
                case 'category':
                    if (!empty($id)) {
                        $link = 'view=category&category_id='.$id;
                    }
                    break;
                case 'event':
                    if (!empty($ev_id)) {
                        $link = 'view=event&event_id='.$ev_id;
                    }
                    break;
                case 'location':
                    if (!empty($id) && $task == 'show') {
                        $link = 'view=event&event_id='.$id;
                    }
                    break;
            }
        }

		return $link;
	}

	public function _getIcagendaLink(){
		$link = '';
		
		$layout = MRequest::getString('layout');
		$view = MRequest::getString('view');
		$id = MRequest::getInt('id');

        if (!empty($id) && $view == 'list' && $layout == 'event') {
            $link = 'view=event&event_id='.$id;
        }

		return $link;
	}
}
