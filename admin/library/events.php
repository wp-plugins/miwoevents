<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class MiwoeventsEvents {

    public function __construct() {
		$this->MiwoeventsConfig = MiwoEvents::getConfig();
	}
	
    public function getEvent($event_id) {
        static $cache = array();

        if (!isset($cache[$event_id])) {
            $cache[$event_id] = MiwoDatabase::loadObject('SELECT *, DATEDIFF(NOW(), event_date) AS ed_number_days, DATEDIFF(NOW(), cut_off_date) AS cod_number_days, DATEDIFF(early_bird_discount_date, NOW()) AS date_diff FROM #__miwoevents_events WHERE id = '.(int)$event_id.' AND published = 1');
        }

        return $cache[$event_id];
    }

    
















































    public function getTotalEventsByCategory($category_id, $inc_children = 1) {
        static $cache = array();

        if (!isset($cache[$category_id][$inc_children])) {
            $db = MFactory::getDbo();
            $user = MFactory::getUser();

            $tmp_cats = array();
            $cats = array();

            $tmp_cats[] = $category_id;
            $cats[] = $category_id;

            if ($inc_children) {
                while (count($tmp_cats)) {
                    $cat_id = array_pop($tmp_cats);

                    //Get list of children category
                    $db->setQuery('SELECT id FROM #__miwoevents_categories WHERE parent = '.(int)$cat_id.' AND published = 1');
                    $rows = $db->loadObjectList();

                    foreach ($rows as $row) {
                        $tmp_cats[] = $row->id;
                        $cats[] = $row->id;
                    }
                }
            }

            if (MiwoEvents::getConfig()->hide_past_events) {
                $sql = 'SELECT COUNT(a.id) FROM #__miwoevents_events AS a INNER JOIN #__miwoevents_event_categories AS b ON a.id = b.event_id WHERE b.category_id IN('.implode(',', $cats).') AND published = 1 AND `access` IN ('.implode(',', $user->getAuthorisedViewLevels()).') AND event_date >= NOW()';
            }
            else {
                $sql = 'SELECT COUNT(a.id) FROM #__miwoevents_events AS a INNER JOIN #__miwoevents_event_categories AS b ON a.id = b.event_id WHERE b.category_id IN('.implode(',', $cats).') AND `access` IN ('.implode(',', $user->getAuthorisedViewLevels()).') AND published = 1';
            }

            $db->setQuery($sql);

            $cache[$category_id][$inc_children] = (int)$db->loadResult();
        }

   		return $cache[$category_id][$inc_children];
   	}

   	public function getDailyRecurringEventDates($startDate, $endDate, $dailyFrequency, $numberOccurencies) {
   		$eventDates = array();

   		$eventDates[] = $startDate;

   		$startTime = strtotime($startDate);
   		$endTime = strtotime($endDate.' 23:59:59');

        if ($numberOccurencies) {
   			$count = 1 ;
   			$i = 1 ;

            while ($count < $numberOccurencies) {
   				$i++;
   				$count++;
   				$nextEventDate = $startTime + ($i-1)*$dailyFrequency*24*3600;

   				$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
   			}
   		}
        else {
   			$i = 1;

   			while (true) {
   				$i++;
   				$nextEventDate = $startTime + ($i -1)*24*$dailyFrequency*3600;

                if ($nextEventDate <= $endTime) {
   					$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
   				}
                else {
   					break;
   				}
   			}
   		}

   		return $eventDates;
   	}

   	public function getWeeklyRecurringEventDates($startDate, $endDate, $weeklyFrequency, $numberOccurrencies, $weekDays) {
   		$eventDates = array();

   		$startTime = strtotime($startDate);
   		$originalStartTime = $startTime;
   		$endTime = strtotime($endDate.' 23:59:59');

   		if ($numberOccurrencies) {
   			$count = 0;
   			$i = 0;
   			$weekDay =  date('w', $startTime);
   			$startTime = $startTime - $weekDay*24*3600;

   			while ($count < $numberOccurrencies) {
   				$i++ ;
   				$startWeekTime = $startTime + ($i -1)*$weeklyFrequency*7*24*3600;

   				foreach ($weekDays as $weekDay) {
   					$nextEventDate = $startWeekTime + $weekDay*24*3600;

   					if (($nextEventDate >= $originalStartTime) and ($count < $numberOccurrencies)) {
   						$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
   						$count++ ;
   					}
   				}
   			}
   		}
        else {
   			$weekDay =  date('w', $startTime);
   			$startTime = $startTime - $weekDay*24*3600;

   			while ($startTime < $endTime) {
   				foreach ($weekDays as $weekDay) {
   					$nextEventDate = $startTime + $weekDay*24*3600;

   					if ($nextEventDate < $originalStartTime) {
   						continue;
                    }

                    if ($nextEventDate <= $endTime) {
   						$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
   					} else {
   						break ;
   					}
   				}

   				$startTime += $weeklyFrequency*7*24*3600;
   			}
   		}

   		return $eventDates ;
   	}

   	public function getMonthlyRecurringEventDates($startDate, $endDate, $monthlyFrequency, $numberOccurrencies, $monthDays) {
   		$eventDates = array();

   		$startTime = strtotime($startDate);
   		$hour = date('H', $startTime);
   		$minute = date('i', $startTime);
   		$originalStartTime = $startTime;
   		$endTime = strtotime($endDate.' 23:59:59');
   		$monthDays = explode(',', $monthDays);

        if ($numberOccurrencies) {
   			$count = 0 ;
   			$currentMonth = date('m', $startTime);
   			$currentYear = date('Y', $startTime);

   			while($count < $numberOccurrencies) {
   				foreach ($monthDays as $day) {
   					$nextEventDate = mktime($hour, $minute, 0, $currentMonth, $day, $currentYear);

                    if (($nextEventDate >= $originalStartTime) and ($count < $numberOccurrencies)) {
   						$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
   						$count++;
   					}
   				}

   				$currentMonth += $monthlyFrequency;

   				if ($currentMonth > 12) {
   					$currentMonth -= 12;
   					$currentYear++ ;
   				}
   			}
   		}
        else {
   			$currentMonth = date('m', $startTime);
   			$currentYear = date('Y', $startTime);

   			while ($startTime < $endTime) {
   				foreach ($monthDays as $day) {
   					$nextEventDate = mktime($hour, $minute, 0, $currentMonth, $day, $currentYear);

                    if (($nextEventDate >= $originalStartTime) and ($nextEventDate <= $endTime)) {
   						$eventDates[] = strftime('%Y-%m-%d %H:%M:%S', $nextEventDate);
   					}
   				}

   				$currentMonth += $monthlyFrequency;

   				if ($currentMonth > 12) {
   					$currentMonth -= 12;
   					$currentYear++ ;
   				}

   				$startTime = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
   			}
   		}

   		return $eventDates ;
   	}

    public function getColor($event_id) {
   		static $colors = array();

   		if (!isset($colors[$event_id])) {
   			$db = MFactory::getDbo();

   			$db->setQuery('SELECT c.color_code FROM #__miwoevents_categories AS c INNER JOIN #__miwoevents_event_categories AS ec ON c.id = ec.category_id WHERE ec.event_id = '.(int)$event_id);

            $colors[$event_id] = $db->loadResult();
   		}

   		return $colors[$event_id] ;
   	}

   	public function canRegister($event_id) {
   		$db = MFactory::getDbo();
   		$user = MFactory::getUser();

   		if (empty($event_id)) {
   			return false;
        }

   		$event = $this->getEvent($event_id);

   		if (empty($event)) {
   			return false;
        }

   		if ($event->registration_type == 3) {
   			return false;
        }

       	if (!in_array($event->registration_access, $user->getAuthorisedViewLevels())) {
       		return false;
       	}

   		if ($event->cut_off_date == $db->getNullDate()) {
            $number_days = $event->ed_number_days;
   		} else {
            $number_days = $event->cod_number_days;
   		}

   		if ($number_days > 0) {
   			return false;
   		}

   		if ($event->event_capacity) {
   			$total_attenders = MiwoEvents::get('attenders')->getTotalAttenders($event_id);

   			if ($total_attenders >= $event->event_capacity) {
   				return false;
            }
   		}

   		if ($this->MiwoeventsConfig->prevent_duplicate_registration and $user->get('id')) {
   			$db->setQuery('SELECT COUNT(id) FROM #__miwoevents_attenders WHERE user_id = '.$user->get('id') .' AND event_id = '.(int)$event_id.' AND status <> 100');
   			$total = $db->loadResult();

            if (!empty($total)) {
   				return false;
   			}
   		}

   		return true;
   	}

    public function getRegistrationRate($event_id, $attenders) {
        static $cache = array();

        if (!isset($cache[$event_id][$attenders])) {
            $rate = MiwoDatabase::loadResult('SELECT price FROM #__miwoevents_event_group_prices WHERE event_id='.(int)$event_id.' AND registrant_number <= '.$attenders.' ORDER BY registrant_number DESC LIMIT 1');

            if (!$rate) {
                $rate = MiwoDatabase::loadResult('SELECT individual_price FROM #__miwoevents_events WHERE id='.(int)$event_id);
            }

            $cache[$event_id][$attenders] = $rate;
        }

   		return $cache[$event_id][$attenders];
   	}
}