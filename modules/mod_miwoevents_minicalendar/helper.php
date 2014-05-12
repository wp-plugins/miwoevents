<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');
require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

class modMiwoeventsMiniCalendarHelper {

	public function _getNow() {		
		static $datenow = null;
		
		if (!isset($datenow)) {						
			$datenow = MFactory::getDate("+0 seconds");
		}
		
		return $datenow;
	}
	
	public function _getYMD(){

		static $data;

		if (!isset($data)) {
			$datenow = $this->_getNow();
			
			list($year, $month, $day) = explode('-', $datenow->format('Y-m-d'));
			
			$year	= min(2100,abs(intval(MRequest::getString('year',	$year))));
			$month	= min(99,abs(intval(MRequest::getString('month',	$month))));
			$day	= min(3650,abs(intval(MRequest::getString('day',	$day))));
			
			if( $day <= '9' ) {
				$day = '0' . $day;
			}
			
			if( $month <= '9') {
				$month = '0' . $month;
			}
			
			$data = array();
			$data[] = $year;
			$data[] = $month;
			$data[] = $day;
		}
		
		return $data;
	}

	public function _listIcalEventsByMonth($year, $month) {
		$db 	= MFactory::getDBO();
		$user 	= MFactory::getUser();
		$app 	= MFactory::getApplication();
		
		$startdate 	= mktime( 0, 0, 0,  $month,  1, $year );
		$enddate 	= mktime( 23, 59, 59,  $month, date( 't', $startdate), $year );
		$startdate = date('Y-m-d',$startdate)." 00:00:00";
		$enddate = date('Y-m-d',$enddate)." 23:59:59";
		
		if ($app->getLanguageFilter()) {
			$extraWhere = ' AND `language` IN (' . $db->Quote(MFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')';
		} else {
			$extraWhere = '' ;
		}
		
		$query = "SELECT * FROM #__miwoevents_events WHERE (`published` = 1) AND (`event_date` BETWEEN '$startdate' AND '$enddate') AND (`access`=0 OR `access` IN (".implode(',', $user->getAuthorisedViewLevels()).")) "
			. $extraWhere." ORDER BY event_date ASC, ordering ASC";		
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}

	public function _getDayName( $daynb, $colored = false ){

		$i = $daynb % 7;
		if ($i == '0' && $colored === true) {
			$dayname = '<span class="sunday">' . $this->getDayName($i) . '</span>';
		}
		else if ($i == '6' && $colored === true){
			$dayname = '<span class="saturday">' . $this->getDayName($i) . '</span>';
		}
		else {
			$dayname = $this->getDayName($i);
		}
		
		return $dayname;
	}
	
	public function getDayName( $daynb=0, $array=0){
		static $days = null;
		
		if ($days === null) {
			$days = array();

			$days[0] = MText::_('COM_MIWOEVENTS_MINICAL_SUNDAY');
			$days[1] = MText::_('COM_MIWOEVENTS_MINICAL_MONDAY');
			$days[2] = MText::_('COM_MIWOEVENTS_MINICAL_TUESDAY');
			$days[3] = MText::_('COM_MIWOEVENTS_MINICAL_WEDNESDAY');
			$days[4] = MText::_('COM_MIWOEVENTS_MINICAL_THURSDAY');
			$days[5] = MText::_('COM_MIWOEVENTS_MINICAL_FRIDAY');
			$days[6] = MText::_('COM_MIWOEVENTS_MINICAL_SATURDAY');
		}
		
		if ($array == 1) {
			return $days;
		}
		
		$i = $daynb % 7;
		
		return $days[$i];
	}

	public function _getCalendarData($year, $month, $day){				
		$rows = $this->_listIcalEventsByMonth($year, $month);		
		$rowcount = count($rows);		
		$data = array();
		
		$data['year'] = $year;
		$data['month'] = $month;
		
		$month = intval($month);
		if ($month <= '9') {
			$month = '0' . $month;
		}

		$data['startday'] = $startday = MiwoEvents::getConfig()->calendar_start_date;

		// get days in week
		$data["daynames"] = array();
		for($i = 0; $i < 7; $i++ ) {
			$data["daynames"][$i] = $this->_getDayName(($i + $startday) % 7, true );
		}
		
		$data["dates"] = array();		
		
		//Start days
		$start = ((date( 'w', mktime(0, 0, 0, $month, 1, $year)) - $startday + 7) % 7);	
		
		// previous month
		$priorMonth = $month-1;
		$priorYear = $year;		
		if ($priorMonth <= 0) {
			$priorMonth += 12;
			$priorYear -= 1;
		}
		
		$dayCount = 0;
		for ($a = $start; $a > 0; $a--) {
			$data["dates"][$dayCount] = array();
			$data["dates"][$dayCount]["monthType"] = "prior";
			$data["dates"][$dayCount]["month"] = $priorMonth;
			$data["dates"][$dayCount]["year"] = $priorYear;
			$data["dates"][$dayCount]['countDisplay'] = 0;
			
			$dayCount++;
		}
		sort($data["dates"]);
		
		//Current month
		$end = date('t', mktime( 0, 0, 0,( $month + 1 ), 0, $year ));
		for($d = 1; $d <= $end; $d++) {
			$data["dates"][$dayCount] = array();
			
			// utility field used to keep track of events displayed in a day!
			$data["dates"][$dayCount]['countDisplay']=0;
			$data["dates"][$dayCount]["monthType"]="current";
			$data["dates"][$dayCount]["month"]=$month;
			$data["dates"][$dayCount]["year"]=$year;		
			
			$t_datenow = $this->_getNow();
			$now_adjusted = $t_datenow->toUnix(true);
			
			if ($month == strftime( '%m', $now_adjusted) && $year == strftime( '%Y', $now_adjusted) && $d == strftime( '%d', $now_adjusted)) {
				$data["dates"][$dayCount]["today"] = true;
			}
			else {
				$data["dates"][$dayCount]["today"] = false;
			}

			$Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'cateory', 'layout' => 'calendar', null, true));
			$link = MRoute::_('index.php?option=com_miwoevents&view=category&layout=calendar&year='. $year . '&month=' . $month . $Itemid);
			
			$data["dates"][$dayCount]['d'] = $d;
			$data["dates"][$dayCount]["link"] = $link;
			$data["dates"][$dayCount]['events'] = array();
			
			if ($rowcount > 0){
				foreach ($rows as $row) {
					$date_of_event = explode('-',$row->event_date);
					$date_of_event = (int)$date_of_event[2];
					
					if ($d == $date_of_event){							
						$i = count($data["dates"][$dayCount]['events']);
						$data["dates"][$dayCount]['events'][$i] = $row;
					}
				}
			}
			
			$dayCount++;
		}
		
		// followmonth
		$days 	= (7 - date( 'w', mktime( 0, 0, 0, $month + 1, 1, $year )) + $startday ) %7;
		$d		= 1;
		
		$followMonth = $month+1;
		$followYear = $year;
		if ($followMonth > 12) {
			$followMonth -= 12;
			$followYear += 1;
		}
		
		$data["followingMonth"] = array();
		
		for ($d = 1; $d <= $days; $d++) {
			$data["dates"][$dayCount] = array();
			$data["dates"][$dayCount]["monthType"] = "following";
			$data["dates"][$dayCount]["month"] = $followMonth;
			$data["dates"][$dayCount]["year"] = $followYear;
			$data["dates"][$dayCount]['countDisplay'] = 0;
			$dayCount++;
		}
		
		return $data;		
	}
}