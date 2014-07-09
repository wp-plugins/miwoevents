<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsViewCalendar extends MiwoeventsView {

    public function __construct() {
        parent::__construct();

        $this->_loadTheme();

        $this->show_menu = ($this->MiwoeventsConfig->activate_weekly_calendar_view or $this->MiwoeventsConfig->activate_daily_calendar_view);
        $this->time_format = ($this->MiwoeventsConfig->event_time_format ? $this->MiwoeventsConfig->event_time_format : 'g:i a');

        $this->days = array(
            0 => MText::_('COM_MIWOEVENTS_SUNDAY'),
            1 => MText::_('COM_MIWOEVENTS_MONDAY'),
            2 => MText::_('COM_MIWOEVENTS_TUESDAY'),
            3 => MText::_('COM_MIWOEVENTS_WEDNESDAY'),
            4 => MText::_('COM_MIWOEVENTS_THURSDAY'),
            5 => MText::_('COM_MIWOEVENTS_FRIDAY'),
            6 => MText::_('COM_MIWOEVENTS_SATURDAY')
        );

        $this->months = array(
            1  => MText::_('COM_MIWOEVENTS_JAN'),
            2  => MText::_('COM_MIWOEVENTS_FEB'),
            3  => MText::_('COM_MIWOEVENTS_MARCH'),
            4  => MText::_('COM_MIWOEVENTS_APR'),
            5  => MText::_('COM_MIWOEVENTS_MAY'),
            6  => MText::_('COM_MIWOEVENTS_JUNE'),
            7  => MText::_('COM_MIWOEVENTS_JUL'),
            8  => MText::_('COM_MIWOEVENTS_AUG'),
            9  => MText::_('COM_MIWOEVENTS_SEP'),
            10 => MText::_('COM_MIWOEVENTS_OCT'),
            11 => MText::_('COM_MIWOEVENTS_NOV'),
            12 => MText::_('COM_MIWOEVENTS_DEC')
        );
    }

    public function display($tpl = null) {
        $input = MiwoEvents::getInput();
        $utility = MiwoEvents::get('utility');

        $default_month = $input->getCmd('default_month', 0);
        $default_year = $input->getCmd('default_year', '');
        $Itemid = $utility->getItemid(array('view' => 'calendar', 'default_month' => $default_month, 'default_year' => $default_year), null, true);

        list($year, $month, $day) = $this->get('YMD');

        $this->data 	= $this->get('MonthlyEvents');
        $this->month 	= $month;
        $this->year 	= $year;

        $listmonth = array(MText::_('COM_MIWOEVENTS_JAN'), MText::_('COM_MIWOEVENTS_FEB'), MText::_('COM_MIWOEVENTS_MARCH'), MText::_('COM_MIWOEVENTS_APR'), MText::_('COM_MIWOEVENTS_MAY'), MText::_('COM_MIWOEVENTS_JUNE'), MText::_('COM_MIWOEVENTS_JULY'), MText::_('COM_MIWOEVENTS_AUG'), MText::_('COM_MIWOEVENTS_SEP'), MText::_('COM_MIWOEVENTS_OCT'),MText::_('COM_MIWOEVENTS_NOV'),MText::_('COM_MIWOEVENTS_DEC'));
        $option_month = array();
        foreach ($listmonth AS $key => $omonth){
            /*
			if ($key < 9) {
                $value = "0".($key + 1);
            }
            else {
			*/
                $value = $key + 1;
            //}

            $option_month[] = MHtml::_('select.option', $value, $omonth);
        }

        $javascript = "onchange=\"cal_date_change(this.value,'$year','$Itemid');\"";
        $this->search_month = MHtml::_('select.genericlist', $option_month, 'month','class="regpro_calendar_months" '.$javascript, 'value', 'text', $month);

        $option_year = array();
        $javascript = "onchange=\"cal_date_change('$month',this.value,'$Itemid');\"";
        for ($i = $year-3; $i < ($year+5); $i++){
            $option_year[] = MHtml::_('select.option',$i,$i);
        }
        $this->search_year = MHtml::_('select.genericlist', $option_year, 'year', 'class="regpro_calendar_years" '.$javascript, 'value', 'text', $year);

        $this->Itemid = $Itemid;
        $this->params = $this->_mainframe->getParams();

        parent::display($tpl);
    }

    public function displayWeekly($tpl = null) {
        $input = MiwoEvents::getInput();
        $utility = MiwoEvents::get('utility');

        $default_month = $input->getCmd('default_month', 0);
        $default_year = $input->getCmd('default_year', '');
        $Itemid = $utility->getItemid(array('view' => 'calendar', 'default_month' => $default_month, 'default_year' => $default_year), null, true);
        //$Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'calendar', 'layout' => 'weekly'), null, true);

        $items = $this->get('Items');

        $events = array();
        foreach ($items as $item) {
            $events[date('w', strtotime($item->event_date))][] = $item;
        }

        $this->rows = $events;

        $day = 0;
        $week_number = date('W',time());
        $year = date('Y',time());
        $date = date('Y-m-d', strtotime($year."W".$week_number.$day));
        
		$first_day_of_week = MRequest::getString('date', $date);
        $this->first_day_of_week 	= str_replace(':', '-', $first_day_of_week);
			
        $this->Itemid = $Itemid;
        $this->params = $this->_mainframe->getParams();

        parent::display($tpl);
    }

    public function displayDaily($tpl = null){
        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'calendar', 'layout' => 'daily'), null, true);

        $this->events = $this->get('Items');
        $this->day 	= MRequest::getString('day', date('Y-m-d', time()));
        $this->Itemid = $Itemid;
        $this->params = $this->_mainframe->getParams();

        parent::display($tpl);
    }

    public function _loadTheme() {
        if ($this->MiwoeventsConfig->calendar_theme) {
            $theme = $this->MiwoeventsConfig->calendar_theme;
        }
        else {
            $theme = 'default';
        }

        $styleUrl = MURL_MIWOEVENTS.'/site/assets/css/themes/'.$theme.'.css';
        $this->document->addStylesheet($styleUrl, 'text/css', null, null);
    }
}