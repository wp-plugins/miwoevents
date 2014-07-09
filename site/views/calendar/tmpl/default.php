<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;
?>
<script type="text/javascript">
    function cal_date_change(month, year, itemid){
        link = "<?php echo MUri::root(); ?>index.php?option=com_miwoevents&view=calendar&month=" + month + "&year=" + year + itemid;
        window.location.href = link;
    }
</script>
<?php
$page_title = $this->params->get('page_title', '');
if (($this->params->get('show_page_heading', '0') == '1') && !empty($page_title)) { $page_title = $page_title." - "; } else { $page_title = NULL; }
?>
<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<h1 class="miwoevents_box_h1"><?php echo $page_title.MText::_('COM_MIWOEVENTS_MONTHLY_VIEW'); ?></h1>
	</div>
	
	<div class="miwoevents_box_content">
	<!-- content -->
	<div id="extcalendar">
	    <?php if ($this->show_menu) { ?>
	    <div style="width: 100%;" class="topmenu_calendar">
	        <ul class="menu_calendar">
	            <li>
	                <?php $month = date('m', time()); $year = date('Y', time()); ?>
	                <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&month='.$month.'&year='.$year.$this->Itemid); ?>" class="calendar_link active">
	                    <?php echo MText::_('COM_MIWOEVENTS_MONTHLY_VIEW'); ?>
	                </a>
	            </li>
	            <?php
	            if ($this->MiwoeventsConfig->activate_weekly_calendar_view) { ?>
                <li>
                    <?php
                        $day = 0;
                        $week_number = date('W', time());
                        $date = date('Y-m-d', strtotime($year."W".$week_number.$day));
                    ?>
                    <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=weekly&date='.$date.$this->Itemid); ?>" class="calendar_link">
                        <?php echo MText::_('COM_MIWOEVENTS_WEEKLY_VIEW')?>
                    </a>
                </li><?php
	            }
	
	            if ($this->MiwoeventsConfig->activate_daily_calendar_view) {
	            ?>
                <li>
                    <?php $day = date('Y-m-d', time()); ?>
                    <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=daily&day='.$day.$this->Itemid); ?>" class="calendar_link">
                        <?php echo MText::_('COM_MIWOEVENTS_DAILY_VIEW')?>
                    </a>
                </li><?php
	            } ?>
	        </ul>
	    </div>
	    <?php } ?>
	
	    <div class="wraptable_calendar">
	        <div class="regpro_calendar" style="width: 100%">
	            <?php
                if ($this->month == 12) {
                    $nextMonth		= 1;
                    $nextYear 		= $this->year + 1;
                    $previousMonth 	= 11;
                    $previousYear 	= $this->year;
                }
                elseif ($this->month == 1) {
                    $nextMonth 		= 2;
                    $nextYear 		= $this->year;
                    $previousMonth 	= 12;
                    $previousYear 	= $this->year - 1;
                }
                else {
                    $nextMonth 		= $this->month + 1;
                    $nextYear 		= $this->year;
                    $previousMonth 	= $this->month - 1;
                    $previousYear 	= $this->year;
                }
	            ?>
	            <table class="regpro_calendarMonthHeader" border="0" width="100%">
	                <tr>
	                    <td width="25%" align="right" valign="top">
	                        <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&month='.$previousMonth.'&year='.$previousYear.$this->Itemid); ?>">
	                            <img alt="<?php echo MText::_("COM_MIWOEVENTS_PREVIOUS_MONTH")?>" src="<?php echo MURL_MIWOEVENTS; ?>/site/assets/images/calendar_previous.png">
	                        </a>
	                    </td>
	                    <td align="center" valign="middle">
	                        <?php echo $this->search_month; ?>&nbsp;&nbsp;<?php echo $this->search_year; ?>
	                    </td>
	                    <td width="25%" align="left" valign="top">
	                        <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&month='.$nextMonth.'&year='.$nextYear.$this->Itemid); ?>">
	                            <img alt="<?php echo MText::_("COM_MIWOEVENTS_NEXT_MONTH")?>" src="<?php echo MURL_MIWOEVENTS; ?>/site/assets/images/calendar_next.png">
	                        </a>
	                    </td>
	                </tr>
	            </table>
	
	            <table cellpadding="0" cellspacing="0" border="0" class="regpro_calendar_table" width="100%">
	                <tr>
                    <?php foreach ($this->data["daynames"] as $dayname) { ?>
                         <td align="center" valign="top" class="regpro_calendarWeekDayHeader">
                             <?php
                             echo $dayname;?>
                         </td><?php
                     } ?>
	                </tr>
	                <?php
	                $dn = 0;
	                $datacount = count($this->data["dates"]);
	
	                for ($w = 0; $w<6 and $dn < $datacount; $w++){ ?>
                    <tr>
                    <?php
                    for ($d = 0; $d < 7 and $dn < $datacount; $d++){
						$currentDay = $this->data["dates"][$dn];

						switch ($currentDay["monthType"]){
							case "prior":
							case "following": ?>
								<td onmouseout="this.className = 'regpro_calendarDay';" onmouseover="this.className = 'regpro_calenderday_highlight';" class="regpro_calendarDay">&nbsp;</td>
							<?php
							break;
							case "current": ?>
							<td onmouseout="this.className = 'regpro_calendarDay';" onmouseover="this.className = 'regpro_calenderday_highlight';" class="regpro_calendarDay">
								<?php 
								echo $currentDay['d'];
								foreach ($currentDay["events"] as $key => $val){
									$color	= MiwoEvents::get('events')->getColor($val->id);
									$Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $val->id), null, true);
									?>
									<div style="border: 0; width: 100%;">
										<a class="miwoevents_event_link" href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$val->id.$Itemid); ?>" title="<?php echo $val->title; ?>" <?php if ($color) echo 'style="background-color:#'.$color.'";' ; ?>>
											<img border="0" align="top" title="<?php echo MText::_("Event")?>" src="<?php echo MURL_MIWOEVENTS; ?>/site/assets/images/calendar_event.png">
											<?php
											if ($this->MiwoeventsConfig->show_event_time) {
												echo $val->title.' ('.MHtml::_('date', $val->event_date, $this->MiwoeventsConfig->event_date_format, null).')';
											}
											else {
												echo $val->title;
											} ?>
										</a>
									</div><?php 
								} ?>
							</td>
							<?php
							break;
						}
						$dn++;
                    } ?>
                    </tr><?php
	                }
	                ?>
	            </table>
	        </div>
	    </div>
	</div>
	<!-- content // -->
	</div>
	<div class="clr"></div>
</div>