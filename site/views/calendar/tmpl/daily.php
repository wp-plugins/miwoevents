<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$param = null ;
?>

<script language="javascript">
    function gotoDate() {
        date = document.getElementById('date');
        if (date.value) {
            var url = "<?php echo MURL_ADMIN; ?>/admin-ajax.php?action=miwoevents&view=calendar&layout=daily&day="+date.value+"<?php echo $this->Itemid; ?>" ;
            location.href = url ;
        } else {
            alert("<?php echo MText::_('COM_MIWOEVENTS_PLEASE_CHOOSE_DATE'); ?>");
        }
    }
</script>

<?php
$page_title = $this->params->get('page_title', '');
if (($this->params->get('show_page_heading', '0') == '1') && !empty($page_title)) { $page_title = $page_title." - "; } else { $page_title = NULL; }
?>

<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<h1 class="miwoevents_box_h1"><?php echo $page_title.MText::_('COM_MIWOEVENTS_DAILY_VIEW'); ?></h1>
	</div>
	
	<div class="miwoevents_box_content">
	<!-- content -->
	<div id="extcalendar">
	    <div style="width: 100%;" class="topmenu_calendar">
	        <div class="left_calendar">
	            <table>
	                <tr>
	                    <td>
	                        <strong><?php echo MText::_('COM_MIWOEVENTS_CHOOSE_DATE'); ?>:</strong>
	                        <?php echo MHtml::_('calendar', MRequest::getString('day'), 'date', 'date', '%Y-%m-%d'); ?>
	                        <input type="button" class="<?php echo MiwoEvents::getButtonClass(); ?>" value="<?php echo MText::_('Go'); ?>" onclick="gotoDate();" />
	                    </td>
	                </tr>
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
	                        <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=weekly&date='.$date.$this->Itemid); ?>" class="calendar_link">
	                            <?php echo MText::_('COM_MIWOEVENTS_WEEKLY_VIEW')?>
	                        </a>
	                    </li>
	                    <?php
	                }
	
	                if ($this->MiwoeventsConfig->activate_daily_calendar_view) {
	                    ?>
	                    <li>
	                        <?php $day = date('Y-m-d', time()); ?>
	                        <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=daily&day='.$day.$this->Itemid); ?>" class="calendar_link active">
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
	        <table cellpadding="0" cellspacing="0" border="0" width="100%">
	            <tr class="tablec">
	                <td class="previousday">
	                    <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=daily&day='.date('Y-m-d',strtotime("-1 day", strtotime($this->day))).$this->Itemid); ?>">
	                        <?php echo MText::_('COM_MIWOEVENTS_PREVIOUS_DAY')?>
	                    </a>
	                </td>
	                <td class="currentday currentdaytoday">
	                    <?php
	                        $time = strtotime($this->day) ;
	                        echo $this->days[date('w', $time)].', '.$this->months[date('n', $time)].' '.date('d', $time).', '.date('Y', $time);
	                    ?>
	                </td>
	                <td class="nextday">
	                    <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=calendar&layout=daily&day='.date('Y-m-d',strtotime("+1 day", strtotime($this->day))).$this->Itemid); ?>">
	                        <?php echo MText::_('COM_MIWOEVENTS_NEXT_DAY')?>
	                    </a>
	                </td>
	            </tr>
	
	            <tr>
	                <td colspan="3">
	                    <?php
	                    if (count($this->events)){
	                    ?>
	                    <table cellpadding="0" cellspacing="0" width="100%" border="0">
	                        <?php
	                            foreach ($this->events AS $key => $event) {
                                    $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $event->id), null, true);
	                        ?>
	                            <tr>
	                                <td class="tableb">
	                                    <div class="eventdesc">
	                                        <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$event->id.$Itemid); ?>"><?php echo $event->title?> ( <?php echo MHtml::_('date', $event->event_date, $this->time_format, $param);?> )</a>
	                                    </div>
	                                </td>
	                            </tr>
	                        <?php }?>
	                    </table>
	                    <?php } else {
	                        echo '<span class="miwoevents_no_events">'.MText::_('COM_MIWOEVENTS_NO_EVENTS')."</span>";
	                    }
	                    ?>
	                </td>
	            </tr>
	        </table>
	    </div>
	</div>
	<!-- content // -->
	</div>
	<div class="clr"></div>
</div>