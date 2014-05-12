<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$param = null;
?>
<?php
$page_title = $this->params->get('page_title', '');
if (($this->params->get('show_page_heading', '0') == '1') && !empty($page_title)) { $page_title = $page_title." - "; } else { $page_title = NULL; }
?>

<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<h1 class="miwoevents_box_h1"><?php echo $page_title.MText::_('COM_MIWOEVENTS_WEEKLY_VIEW'); ?></h1>
	</div>
	
	<div class="miwoevents_box_content">
	<!-- content -->
	<div id="extcalendar">
	<div style="width: 100%;" class="topmenu_calendar">
		<div class="left_calendar">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
	            <tr>
	                <td class="tableh1" colspan="3">
	                    <table class="jcl_basetable">
	                        <tr>
	                            <td align="left" class="today">&nbsp;</td>
	                            <td align="right" class="today">
	                            	<?php

										$startWeekTime = strtotime($this->first_day_of_week);
										$endWeekTime = strtotime("+6 day", strtotime($this->first_day_of_week)) ;
										echo $this->days[date('w', $startWeekTime)].'. '.date('d', $startWeekTime).' '.$this->months[date('n', $startWeekTime)].', '.date('Y', $startWeekTime).' - '.$this->days[date('w', $endWeekTime)].'. '.date('d', $endWeekTime).' '.$this->months[date('n', $endWeekTime)].', '.date('Y', $endWeekTime) ;
	                            	?>                                
	                            </td>
	                        </tr>
	                    </table>
	                </td>                
	       </table>
		</div>
	
	    <?php if ($this->show_menu) { ?>
	    <div style="width: 100%;" class="topmenu_calendar">
	        <ul class="menu_calendar">
	            <li>
	                <?php
	                $month = date('m', time());
	                $year = date('Y', time());
	                ?>
	                <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&month='.$month.'&year='.$year.$this->Itemid); ?>" class="calendar_link">
	                    <?php echo MText::_('COM_MIWOEVENTS_MONTHLY_VIEW')?>
	                </a>
	            </li>
	            <?php
	
	            if ($this->MiwoeventsConfig->activate_weekly_calendar_view) {
	                ?>
	                <li>
	                    <?php
	                    $day = 0;
	                    $week_number = date('W', time());
	                    $date = date('Y-m-d', strtotime($year."W".$week_number.$day));
	                    ?>
	                    <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=weekly&date='.$date.$this->Itemid); ?>" class="calendar_link active">
	                        <?php echo MText::_('COM_MIWOEVENTS_WEEKLY_VIEW')?>
	                    </a>
	                </li>
	                <?php
	            }
	
	            if ($this->MiwoeventsConfig->activate_daily_calendar_view) {
	                ?>
	                <li>
	                    <?php $day = date('Y-m-d', time()); ?>
	                    <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=daily&day='.$day.$this->Itemid); ?>" class="calendar_link">
	                        <?php echo MText::_('COM_MIWOEVENTS_DAILY_VIEW')?>
	                    </a>
	                </li>
	                <?php
	            }
	            ?>
	        </ul>
	    </div>
	    <?php } ?>
	</div>
	
	<div class="wraptable_calendar">
	    <table cellpadding="0" cellspacing="0" width="100%" border="0">
	        <tr class="tablec">
	            <td class="previousweek">
	                <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=weekly&date='.date('Y-m-d', strtotime("-7 day", strtotime($this->first_day_of_week))).$this->Itemid); ?>">
	                    <?php echo MText::_('COM_MIWOEVENTS_PREVIOUS_WEEK')?>
	                </a>
	            </td>
	            <td class="currentweek currentweektoday">
	                <?php echo MText::_('COM_MIWOEVENTS_WEEK')?> <?php echo date('W',strtotime("+6 day", strtotime($this->first_day_of_week)));?>
	            </td>
	            <td class="nextweek">
	                <a class="extcalendar prefetch" href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=weekly&date='.date('Y-m-d', strtotime("+7 day", strtotime($this->first_day_of_week))).$this->Itemid); ?>">
	                    <?php echo MText::_('COM_MIWOEVENTS_NEXT_WEEK')?>
	                </a>
	            </td>
	        </tr>
	
	        <?php foreach ($this->rows AS $key => $events) { ?>
	            <tr class="tableh2">
	                <td class="tableh2" colspan="3">
	                    <?php
	                        $time = strtotime("+$key day", strtotime($this->first_day_of_week)) ;
	                        echo $this->days[date('w', $time)].'. '.date('d', $time).' '.$this->months[date('n', $time)].', '.date('Y', $time) ;
	                    ?>
	                </td>
	            </tr>
	            <tr class="tableb">
	                    <?php if (!count($events)) { ?>
	                        <td align="center" class="tableb" colspan="3">
	                            <br />
	                                <strong>
	                                    <?php echo MText::_( 'COM_MIWOEVENTS_NO_EVENT_ON_THIS_DAY');?>
	                                </strong>
	                            <br />
	                            <br />
	                        </td>
	                    <?php } else { ?>
	                        <td align="left" style="padding-left: 10px;" colspan="3">
	                            <br />
	                             <?php foreach ($events as $event) {
                                     $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $event->id), null, true);
                                     ?>
	                                <p>
	                                    <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$event->id.$Itemid); ?>"><?php echo $event->title?> ( <?php echo MHtml::_('date', $event->event_date, $this->time_format, $param); ?> )</a>
	                                </p>
	                            <?php } ?>
	                            <br />
	                            <br />
	                        </td>
	                    <?php } ?>
	            </tr>
	        <?php } ?>
	    </table>
	</div>
	</div>
	<!-- content // -->
	</div>
	<div class="clr"></div>
</div>