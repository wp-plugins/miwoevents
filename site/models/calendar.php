<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsModelCalendar extends MiwoeventsModel {

    public $datenow = null;
    public $year = null;
    public $month = null;
    public $day = null;

	public function __construct() {
		parent::__construct('calendar', 'events');

        $this->setVars();
        $this->getNow();
        $this->getYMD();

        $this->_buildViewQuery();
	}

    public function setVars() {
        $month = MRequest::getInt('month');
        if (!$month) {
            $month = MRequest::getInt('default_month');

            if ($month) {
                MRequest::setVar('month', $month) ;
            }
        }

        $year = MRequest::getInt('year');
        if (!$year) {
            $year = MRequest::getInt('default_year');

            if ($year) {
                MRequest::setVar('year', $year);
            }
        }

        $this->month = $month;
        $this->year = $year;
    }
	
	public function getNow() {
		if (!isset($this->datenow)) {
            $this->datenow = MFactory::getDate("+0 seconds");
        }

		return $this->datenow;
	}
	
	public function getYMD(){
		static $data = array();

		if (empty($data) or !isset($this->year) or !isset($this->month) or !isset($this->day)) {
			list($year, $month, $day) = explode('-', $this->datenow->format('Y-m-d'));
			
			$year	= min(2100, abs(intval(MRequest::getString('year', $year))));
			$month	= min(99, abs(intval(MRequest::getString('month', $month))));
			$day	= min(3650, abs(intval(MRequest::getString('day', $day))));
			
			if ($day <= '9') {
                $day = '0' . $day;
            }

			if ($month <= '9') {
                $month = '0' . $month;
            }

            $this->year = $year;
            $this->month = $month;
            $this->day = $day;

			$data = array();
			$data[]	= $year;
			$data[]	= $month;
			$data[]	= $day;
		}

		return $data;
	}

    public function getItems() {
        if (empty($this->_data)) {
            $layout = MRequest::getCmd('layout', 'monthly');

            $rows = MiwoDatabase::loadObjectList($this->_query);

            if (($layout == 'monthly') and $this->MiwoeventsConfig->show_multiple_days_event_in_calendar) {
                $events = array();

                $n = count($rows);
                for ($i = 0; $i < $n; $i++) {
                    $row = $rows[$i];
                    $arrDates = explode('-', $row->event_date);

                    if (($arrDates[0] == $this->year) and ($arrDates[1] == $this->month)) {
                        $events[] = $row;
                    }

                    $startDateParts = explode(' ', $row->event_date);
                    $startTime = strtotime($startDateParts[0]);
                    $startDateTime = strtotime($row->event_date);
                    $endDateParts = explode(' ', $row->event_end_date);
                    $endTime = strtotime($endDateParts[0]);

                    $count = 0;
                    while ($startTime < $endTime) {
                        $count++;

                        $rowNew = clone ($row);
                        $rowNew->event_date = date('Y-m-d H:i:s', $startDateTime + $count * 24 * 3600);

                        $arrDates = explode('-', $rowNew->event_date);
                        if (($arrDates[0] == $this->year) and ($arrDates[1] == $this->month)) {
                            $events[] = $rowNew;
                        }

                        $startTime += 24 * 3600;
                    }
                }

                $rows = $events;
            }

            $this->_data = $rows;
        }

        return $this->_data;
    }
	
	public function getMonthlyEvents() {
        list($year, $month, $day) = $this->getYMD();

		$rows = $this->getItems();
		$rowcount = count($rows);

		$data = array();
		$data['year'] = $year;
		$data['month'] = $month;
		$month = intval($month);
		
		if ($month <= '9') {
			$month = '0' . $month;
		}

		$data['startday'] = $startday = (int) MiwoEvents::getConfig()->calendar_start_date;

		$data["daynames"] = array();
		for( $i = 0; $i < 7; $i++ ) {
			$data["daynames"][$i] = $this->getDayName(($i + $startday)%7, true);
		}		
		$data["dates"] = array();

		$start = (( date( 'w', mktime( 0, 0, 0, $month, 1, $year )) - $startday + 7 ) % 7 );		
		$priorMonth = $month-1;
		$priorYear = $year;
		
		if ($priorMonth <= 0) {
			$priorMonth += 12;
			$priorYear -= 1;
		}
		
		$dayCount = 0;
		
		for ($a = $start; $a > 0; $a--){
			$data["dates"][$dayCount] = array();
			$data["dates"][$dayCount]["monthType"]		= "prior";
			$data["dates"][$dayCount]["month"]			= $priorMonth;
			$data["dates"][$dayCount]["year"] 			= $priorYear;
			$data["dates"][$dayCount]['countDisplay'] 	= 0;
			$dayCount++;
		}
		
		sort($data["dates"]);

		# Current month
		$end = date('t', mktime(0, 0, 0,($month + 1), 0, $year));

		$now_adjusted = $this->datenow->toUnix(true);
		$xMonth	= strftime('%m', $now_adjusted);
		$xYear	= strftime('%Y', $now_adjusted);
		$xDay	= strftime('%d', $now_adjusted);
			
		for($d = 1; $d <= $end; $d++){
			
			$data["dates"][$dayCount] 					= array();
			$data["dates"][$dayCount]['countDisplay'] 	= 0;
			$data["dates"][$dayCount]["monthType"] 		= "current";
			$data["dates"][$dayCount]["month"] 			= $month;
			$data["dates"][$dayCount]["year"] 			= $year;		
						
			if( $month == $xMonth and $year == $xYear and $d == $xDay) {
				$data["dates"][$dayCount]["today"] = true;
			}else{
				$data["dates"][$dayCount]["today"] = false;
			}
			
			$data["dates"][$dayCount]['d'] = $d;						
			$data["dates"][$dayCount]['events'] = array();
			
			if ($rowcount > 0){
				foreach ($rows as $row) {
					$date_of_event = explode('-',$row->event_date);
					$date_of_event = (int)$date_of_event[2];

					if ($d == $date_of_event) {
						$i = count($data["dates"][$dayCount]['events']);

						$data["dates"][$dayCount]['events'][$i] = $row;
					}
				}
			}
			
			$dayCount++;
		}

		$days 	= ( 7 - date( 'w', mktime( 0, 0, 0, $month + 1, 1, $year )) + $startday ) %7;
		$d		= 1;
		$followMonth = $month + 1;
		$followYear = $year;
		if ($followMonth > 12) {
			$followMonth-=12;
			$followYear+=1;
		}
		
		$data["followingMonth"]=array();
		
		for($d = 1; $d <= $days; $d++) {
			$data["dates"][$dayCount]				= array();
			$data["dates"][$dayCount]["monthType"]	= "following";
			$data["dates"][$dayCount]["month"]		= $followMonth;
			$data["dates"][$dayCount]["year"]		= $followYear;
			$data["dates"][$dayCount]['countDisplay']= 0;
			$dayCount++;
		}

		return $data;
	}

    public function getDayName($daynb, $colored = false ){
        $i = $daynb % 7; # mod 7

        if (($i == 0) and ($colored === true)){
            $dayname = "<span class=\"sunday\">{$this->_getDayName($i)}</span>";
        }
        else if (($i == 6) and ($colored === true)){
            $dayname = "<span class=\"saturday\">{$this->_getDayName($i)}</span>";
        }
        else {
            $dayname = $this->_getDayName($i);
        }

        return $dayname;
    }

    public function _getDayName($daynb = 0, $array = 0){
        static $days = null;

        if ($days === null) {
            $days = array();

            $days[0] = MText::_('COM_MIWOEVENTS_SUNDAY');
            $days[1] = MText::_('COM_MIWOEVENTS_MONDAY');
            $days[2] = MText::_('COM_MIWOEVENTS_TUESDAY');
            $days[3] = MText::_('COM_MIWOEVENTS_WEDNESDAY');
            $days[4] = MText::_('COM_MIWOEVENTS_THURSDAY');
            $days[5] = MText::_('COM_MIWOEVENTS_FRIDAY');
            $days[6] = MText::_('COM_MIWOEVENTS_SATURDAY');
        }

        if ($array == 1) {
            return $days;
        }

        $i = $daynb % 7;

        return $days[$i];
    }

    public function _buildViewQuery() {
        $where = $this->_buildViewWhere();

        $orderby = " ORDER BY event_date ASC, ordering ASC";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = "SELECT * FROM #__{$this->_component}_{$this->_table} {$where}{$orderby}";
    }

    public function _buildViewWhere() {
        $view = MRequest::getCmd('view');
        $layout = MRequest::getCmd('layout', 'monthly');
        $category_id = MRequest::getInt('category_id');

        $userLevel = MFactory::getUser()->getAuthorisedViewLevels();

        if (($layout == 'monthly') or (($view == 'category') and ($layout == 'calendar'))) {
            $startdate = mktime(0, 0, 0, $this->month, 1, $this->year);
            $enddate = mktime(23, 59, 59, $this->month, date('t', $startdate), $this->year);
            $startdate = date('Y-m-d', $startdate)." 00:00:00";
            $enddate = date('Y-m-d', $enddate)." 23:59:59";
        }

        if ($layout == 'weekly') {
            $day = 0;
            $week_number = date('W', time());
            $year = date('Y', time());
            $date = date('Y-m-d', strtotime($year."W".$week_number.$day));

            $first_day_of_week 	= MRequest::getString('date', $date);
            $first_day_of_week 	= str_replace(':', '-', $first_day_of_week);
            $last_day_of_week 	= date('Y-m-d', strtotime("+6 day", strtotime($first_day_of_week)));
            $startdate 			= $first_day_of_week." 00:00:00";
            $enddate 			= $last_day_of_week." 23:59:59";
        }

        if ($layout == 'daily') {
            $day = MRequest::getString('day', date('Y-m-d', time()));
            $startdate	= $day." 00:00:00";
            $enddate	= $day." 23:59:59";
        }

        $where = array();

        $where[] = 'published = 1';

        if (($layout == 'monthly') and $this->MiwoeventsConfig->show_multiple_days_event_in_calendar) {
                $where[] = "((event_date BETWEEN '{$startdate}' AND '{$enddate}') OR (MONTH(event_end_date) = $this->month AND YEAR(event_end_date) = $this->year))";
        }
        else {
            $where[] = "(event_date BETWEEN '{$startdate}' AND '{$enddate}')";
        }

        if ($this->MiwoeventsConfig->hide_past_events) {
            $where[] = "DATE(event_date) >= CURDATE()";
        }

        $where[] = 'access IN ('.implode(',', $userLevel).')';

        if (($view == 'category') and !empty($category_id)) {
            $where[] = "id IN (SELECT event_id FROM #__miwoevents_event_categories WHERE category_id = $category_id)";
        }

        $where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');

        return $where;
    }
	public function getCalCategories(){
        return $categoryColors = MiwoDatabase::loadAssocList("SELECT title,color_code FROM `#__miwoevents_categories`");
    }
}