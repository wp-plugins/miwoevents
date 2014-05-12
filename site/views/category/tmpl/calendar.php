<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;
$param = null ;
$timeFormat = $this->MiwoeventsConfig->event_time_format ? $this->MiwoeventsConfig->event_time_format : 'g:i a' ;
?>
<script type="text/javascript">
	function cal_date_change(month,year,itemid){
		location.href="<?php echo MUri::root()?>index.php?option=com_miwoevents&view=category&layout=calendar&category_id=<?php echo $this->category->id; ?>&month=" + month + "&year=" + year + itemid;
	}	
</script>

<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<h1 class="miwoevents_box_h1"><?php echo MText::_("COM_MIWOEVENTS_CALENDAR"); ?></h1>
	</div>
	
	<div class="miwoevents_box_content">
	<!-- content -->
	<form method="post" name="adminForm" id="adminForm" action="index.php">	
	    <?php
	        //Calculate next and previous month, year
	        if ($this->month == 12) {
	            $nextMonth = 1 ;
	            $nextYear = $this->year + 1 ;
	            $previousMonth = 11 ;
	            $previousYear = $this->year ;
	        } elseif ($this->month == 1) {
	            $nextMonth = 2 ;
	            $nextYear = $this->year ;
	            $previousMonth = 12 ;
	            $previousYear = $this->year - 1 ;
	        } else {
	            $nextMonth = $this->month + 1 ;
	            $nextYear = $this->year ;
	            $previousMonth = $this->month - 1 ;
	            $previousYear = $this->year ;
	        }
	
	        if ($this->MiwoeventsConfig->show_cat_decription_in_calendar_layout) {
	        ?>
	            <div class="miwoevents_cat">
	                <?php
	                    if(isset($this->category->title) and $this->category->title != '') :
	                        ?><h1 class="miwoevents_title"><?php echo $this->category->title;?></h1><?php
	                    endif;
	                    if(isset($this->category->description) and $this->category->description != '') :
	                        ?><div class="miwoevents_description"><?php echo $this->category->description;?></div><?php
	                    endif;
	                ?>
	                <div class="clr"></div>
	            </div>
	        <?php
	        }
	    ?>
	    <div class="regpro_calendar" style="width: 98%">
	    <table class="regpro_calendarMonthHeader" border="0" width="100%">
	        <tr>
	            <td width="25%" align="right" valign="top">
	                <a href="<?php echo MUri::root()?>index.php?option=com_miwoevents&view=category&layout=calendar&category_id=<?php echo $this->category->id; ?>&month=<?php echo $previousMonth?>&year=<?php echo $previousYear.$this->Itemid; ?>">
	                    <img alt="<?php echo MText::_("Previous Month")?>" src="<?php echo MURL_MIWOEVENTS; ?>/site/assets/images/calendar_previous.png">
	                </a>
	            </td>
	            <td align="center" valign="middle">
	                <?php echo $this->search_month; ?>&nbsp;&nbsp;
	                <?php echo $this->search_year; ?>
	            </td>
	            <td width="25%" align="left" valign="top">
	                <a href="<?php echo MUri::root()?>index.php?option=com_miwoevents&view=category&layout=calendar&category_id=<?php echo $this->category->id; ?>&month=<?php echo $nextMonth ;?>&year=<?php echo $nextYear.$this->Itemid; ?>">
	                    <img alt="<?php echo MText::_("Next Month"); ?>" src="<?php echo MURL_MIWOEVENTS; ?>/site/assets/images/calendar_next.png">
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
	                 </td>
	                 <?php
	             } ?>
	        </tr>
	          <?php
	                $datacount = count($this->data["dates"]);
	                $dn=0;
	                for ($w=0;$w<6 && $dn<$datacount;$w++){
	                ?>
	                <tr >
	                    <?php
	                        for ($d=0;$d<7 && $dn<$datacount;$d++){
	                        $currentDay = $this->data["dates"][$dn];
	                        switch ($currentDay["monthType"]){
	                            case "prior":
	                            case "following":
	                            ?>
	                               <td onmouseout="this.className = 'regpro_calendarDay';" onmouseover="this.className = 'regpro_calenderday_highlight';" class="regpro_calendarDay">
	                                    &nbsp;
	                                </td>
	                        <?php
	                            break;
	                            case "current":
	                        ?>
	                               <td onmouseout="this.className = 'regpro_calendarDay';" onmouseover="this.className = 'regpro_calenderday_highlight';" class="regpro_calendarDay">
	                                    <?php echo $currentDay['d'];
	                                    foreach ($currentDay["events"] as $key=>$val){
                                            $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $val->id), null, true);
	                                    ?>
	                                        <div style="border:0;width:100%;">
	                                            <a class="miwoevents_event_link" href="<?php echo MText::_('index.php?option=com_miwoevents&view=event&event_id='.$val->id.$this->Itemid); ?>" title="<?php echo $val->title; ?>">
	                                                <img border="0" align="top" title="<?php echo MText::_("Event")?>" src="<?php echo MUri::root()?>components/com_miwoevents/assets/images/calendar_event.png">
	                                                <?php
	                                                    if ($this->MiwoeventsConfig->show_event_time) {
	                                                        echo $val->title.' ('.MHtml::_('date', $val->event_date, $timeFormat, $param).')';
	                                                    } else {
	                                                        echo $val->title ;
	                                                    }
	                                                ?>
	                                            </a>
	                                        </div>
	                               <?php }
	                                echo "</td>\n";
	                            break;
	                        }
	                                $dn++;
	                    }
	                    echo "</tr>\n";
	                }
	        ?>
	    </table>
	    </div>
	    <input type="hidden" name="option" value="com_miwoevents" />
	    <input type="hidden" name="view" value="category" />
	    <input type="hidden" name="layout" value="calendar" />
	    <input type="hidden" name="category_id" value="<?php echo $this->category->id; ?>" />
	    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid ; ?>" />
	</form>
	<!-- content // -->
	<?php
        foreach ($this->calCat as $item) {
            ?>
          &nbsp;<div style="float: left;">
              <div class="showCategoriesColors" style="background-color:<?php echo "#".$item['color_code'];?>;"></div>
              <div style="padding-left: 12px; padding-right: 15px;"><?php echo $item['title'];?></div>
          </div>
		  &nbsp;&nbsp;
        <?php } ?>
	</div>
	<div class="clr"></div>
</div>