<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$format = 'Y-m-d' ;
$editor = MFactory::getEditor();

if (version_compare(MVERSION, '1.6.0', 'ge')) {
    $format = 'Y-m-d';
    $param = null ;
}
else {
    $format = 'Y-m-d';
    $param = 0 ;
}
?>
<style>
	.calendar { vertical-align: bottom; }
    .form-horizontal .controls { margin-left: 5px !important; }
</style>


<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form form-horizontal">
    <?php if ($this->_mainframe->isSite()) { ?>
    <div style="float: left; width: 99%; margin-left: 10px;">
        <button class="button btn-success" onclick="Miwi.submitbutton('apply')"><span class="icon-apply icon-white"></span> <?php echo MText::_('COM_MIWOEVENTS_SAVE'); ?></button>
        <button class="button" onclick="Miwi.submitbutton('save')"><span class="icon-save"></span> <?php echo MText::_('COM_MIWOEVENTS_SAVE_CLOSE'); ?></button>
        <button class="button" onclick="Miwi.submitbutton('save2new')"><span class="icon-save-new"></span> <?php echo MText::_('COM_MIWOEVENTS_SAVE_NEW'); ?></button>
        <button class="button" onclick="Miwi.submitbutton('cancel')"><span class="icon-cancel"></span> <?php echo MText::_('COM_MIWOEVENTS_CANCEL'); ?></button>
    </div>
    <br/>
    <br/>
    <?php } ?>
	<div class="width-60 fltlft">
		<fieldset class="adminform">
            <legend><?php echo MText::_('COM_MIWOEVENTS_DETAILS'); ?></legend>
			<table class="admintable" width="100%">
				<tr>
					<td class="key2">
                        <?php echo MText::_( 'COM_MIWOEVENTS_TITLE') ; ?>
                    </td>
					<td class="value2">
						<input type="text" name="title" value="<?php echo $this->item->title; ?>" class="inputbox required" style="font-size: 1.364em;" size="65" aria-required="true" required="required" aria-invalid="false"/>
					</td>
				</tr>

				<tr>
	                <td class="key2">
	                    <?php echo MText::_('COM_MIWOEVENTS_ALIAS'); ?>
	                </td>
	                <td class="value2">
	                    <input class="text_area" type="text" name="alias" id="alias" size="45" maxlength="250" value="<?php echo $this->item->alias;?>" />
	                </td>
	            </tr>

				<tr>
					<td class="key2">
                        <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_CPANEL_CATEGORIES');?>::<?php echo MText::_('COM_MIWOEVENTS_EVENT_CATEGORY_EXPLAIN'); ?>"><?php echo MText::_('COM_MIWOEVENTS_CPANEL_CATEGORIES'); ?></span>
                    </td>
					<td class="value2">
						<?php echo $this->lists['category_id'] ; ?>
					</td>
				</tr>
                <tr>
                    <td class="key2"><?php echo MText::_('COM_MIWOEVENTS_LOCATION'); ?></td>
                    <td class="value2">
                        <?php echo $this->lists['location_id'] ; ?>
                    </td>
                </tr>
				<tr>
					<td class="key2"><?php echo MText::_('COM_MIWOEVENTS_PICTURE'); ?></td>
					<td class="value2">
						<input type="file" class="inputbox" name="thumb_image" size="32" />
						<?php if ($this->item->thumb) { ?>
                            <a href="<?php echo MURL_MEDIA.'/miwoevents/images/'.$this->item->thumb; ?>" class="modal"><img src="<?php echo MURL_MEDIA.'/miwoevents/images/thumbs/'.$this->item->thumb; ?>" class="img_preview" /></a>
                            <input type="checkbox" name="del_thumb" value="1" /><?php echo MText::_('COM_MIWOEVENTS_DELETE_CURRENT'); ?>
						<?php } ?>
					</td>
				</tr>

				<tr>
					<td class="key2">
						<span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_ATTACHMENT');?>::<?php echo MText::_('COM_MIWOEVENTS_ATTACHMENT_EXPLAIN'); ?>"><?php echo MText::_('COM_MIWOEVENTS_ATTACHMENT'); ?></span>
					</td>
					<td class="value2">
						<?php echo $this->lists['attachment']; ?>
					</td>
				</tr>
			</table>

            <?php echo MHtml::_('tabs.start', 'left', array('useCookie'=>1)); ?>
            <!-- Main -->
            <?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOEVENTS_DESCRIPTION'), 'sl_main'); ?>

            <?php
            # Description
            $pageBreak = "<hr id=\"system-readmore\">";

            $fulltextLen = strlen($this->item->fulltext);

            if ($fulltextLen > 0){
            	$content = "{$this->item->introtext}{$pageBreak}{$this->item->fulltext}";
            } else {
            	$content = "{$this->item->introtext}";
            }

            echo $editor->display('description', $content , '100%', '250', '90', '6'); ?>

			<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOEVENTS_EVENTS_SL_EMAIL_TITLE'), 'sl_email'); ?>
			<table class="admintable" width="100%">
				<div class="miwi_paid">
					<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'This feature'); ?></strong><br /><br />
					<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoevents-manage-book-events#pricing', 'MiwoEvents'); ?>
				</div>
			</table>
            <?php echo MHtml::_('tabs.end'); ?>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
        <?php echo MHtml::_('sliders.start', 'miwoeventsright', array('startOffset' => 'sl_dates', 'useCookie' => 1)); ?>
        <!-- Publishing -->
        <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_PUBLISHING_OPTIONS'), 'sl_publishing'); ?>
        <table class="admintable" width="100%">
            
            <?php
            $publishing = 
            "<tr>
                <td class=\"key2\">
                    ".MText::_('COM_MIWOEVENTS_PUBLISHED')."
                </td>
                <td class=\"value2\">
                   {$this->lists['published']}
                </td>
            </tr>";
            
            // MiwoPublishHidden
            function mpblshh($param) { echo "<input type=\"hidden\" name=\"published\" value=\"{$param}\" />"; }
                   
            if (MFactory::getApplication()->isSite()) {
				if (MiwoEvents::get('acl')->canEditState()) {
					echo $publishing;
				} else {
					mpblshh(0);
				}
            } else { 
				              echo $publishing;
			} ?> 











            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOEVENTS_LANGUAGE'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['language']; ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOEVENTS_CREATED_BY'); ?>
                </td>
                <td class="value2">
                    <?php echo MiwoEvents::get('utility')->getUserInputbox($this->item->created_by, 'created_by'); ?>
                </td>
            </tr>
        </table>

        <!-- Dates -->
        <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_EVENTS_SL_DATES_TITLE'), 'sl_dates'); ?>
        <?php $_style = array('class'=>'inputbox', 'size'=>'15', 'style'=>'width: 100px;'); ?>
        <table class="admintable" width="100%">
            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOEVENTS_EVENT_START_DATE'); ?>
                </td>
                <td class="value2">
                	<?php
					echo MHtml::_('calendar', ($this->item->event_date != $this->null_date) ?  MHtml::_('date', $this->item->event_date, $format, null) :'', 'event_date', 'event_date', '%Y-%m-%d', $_style);
                    echo $this->lists['event_date_hour'].' '.$this->lists['event_date_minute'];
                    ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOEVENTS_EVENT_END_DATE'); ?>
                </td>
                <td class="value2">
                	<?php
					echo MHtml::_('calendar', ($this->item->event_end_date != $this->null_date) ?  MHtml::_('date', $this->item->event_end_date, $format, null) :'', 'event_end_date', 'event_end_date', '%Y-%m-%d', $_style);
                    echo $this->lists['event_end_date_hour'].' '.$this->lists['event_end_date_minute']; ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_CUT_OFF_DATE');?>::<?php echo MText::_('COM_MIWOEVENTS_CUT_OFF_DATE_EXPLAIN'); ?>"><?php echo MText::_('COM_MIWOEVENTS_CUT_OFF_DATE'); ?></span>
                </td>
                <td class="value2">
               		<?php
					echo MHtml::_('calendar', ($this->item->cut_off_date != $this->null_date) ?  MHtml::_('date', $this->item->cut_off_date, $format, null) :'', 'cut_off_date', 'cut_off_date', '%Y-%m-%d', $_style);
                    ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOEVENTS_CANCEL_BEFORE_DATE'); ?>
                </td>
                <td class="value2">
                    <?php
                    echo MHtml::_('calendar', ($this->item->cancel_before_date != $this->null_date) ?  MHtml::_('date', $this->item->cancel_before_date, $format, null) :'', 'cancel_before_date', 'cancel_before_date', '%Y-%m-%d', $_style);
                    ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_EARLY_BIRD_DISCOUNT_DATE');?>::<?php echo MText::_('COM_MIWOEVENTS_EARLY_BIRD_DISCOUNT_DATE_EXPLAIN'); ?>"><?php echo MText::_('COM_MIWOEVENTS_EARLY_BIRD_DISCOUNT_DATE'); ?></span>
                </td>
                <td class="value2">
                	<?php
                    echo MHtml::_('calendar', ($this->item->early_bird_discount_date != $this->null_date) ?  MHtml::_('date', $this->item->early_bird_discount_date, $format, null) :'', 'early_bird_discount_date', 'early_bird_discount_date', '%Y-%m-%d', $_style);
                    ?>
                </td>
            </tr>
        </table>

        <!-- Recurring Event Settings -->
		<?php if (@$this->MiwoeventsConfig->activate_recurring_event) {
				echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_SL_RECURRING_OPTIONS'), 'sl_recurring'); ?>
		<table class="admintable" width="100%">
		<tr>
	    	<td class="key2">
	        	<?php echo MText::_('COM_MIWOEVENTS_REPEAT_TYPE'); ?>
			</td>
			<td class="value2">
				<table width="100%">
					<tr>
						<td>
							<input type="radio" name="recurring_type" value="0" <?php if ($this->item->recurring_type == 0) echo ' checked="checked" ' ; ?> onclick="setDefaultDate();" /> <?php echo MText::_('COM_MIWOEVENTS_NO_REPEAT'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" name="recurring_type" value="1" <?php if ($this->item->recurring_type == 1) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo MText::_('COM_MIWOEVENTS_REPEAT_EVERY'); ?> <input type="text" name="number_days" size="5" class="inputbox" value="<?php echo $this->item->number_days ; ?>" /> <?php echo MText::_('COM_MIWOEVENTS_DAYS'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="radio" name="recurring_type" value="2" <?php if ($this->item->recurring_type == 2) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo MText::_('COM_MIWOEVENTS_REPEAT_EVERY'); ?> <input type="text" name="number_weeks" size="5" class="inputbox" value="<?php echo $this->item->number_weeks ; ?>" /> <?php echo MText::_('COM_MIWOEVENTS_WEEKS'); ?>
                                <br />
                                <strong><?php echo MText::_('COM_MIWOEVENTS_ON'); ?></strong>
                                <?php
                                    $weekDays = explode(',', $this->item->weekdays) ;
                                    $daysOfWeek = array(0=> 'COM_MIWOEVENTS_SUN', 1 => 'COM_MIWOEVENTS_MON', 2=> 'COM_MIWOEVENTS_TUE', 3=>'COM_MIWOEVENTS_WED', 4 => 'COM_MIWOEVENTS_THUR', 5=>'COM_MIWOEVENTS_FRI', 6=> 'COM_MIWOEVENTS_SAT') ;
                                    foreach ($daysOfWeek as $key=>$value) {
                                    ?>
                                        <input type="checkbox" class="inputbox" value="<?php echo $key; ?>" name="weekdays[]" <?php if (in_array($key, $weekDays)) echo ' checked="checked"' ; ?> /> <?php echo MText::_($value); ?>&nbsp;&nbsp;
                                    <?php
                                        if ($key == 4)
                                            echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ;
                                    }
                                ?>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" name="recurring_type" value="3" <?php if ($this->item->recurring_type == 3) echo ' checked="checked" ' ; ?> onclick="setDefaultData();" /> <?php echo MText::_('COM_MIWOEVENTS_REPEAT_EVERY'); ?> <input type="text" name="number_months" size="5" class="inputbox" value="<?php echo $this->item->number_months ; ?>" /> <?php echo MText::_('COM_MIWOEVENTS_MONTHS'); ?>
					        <?php echo MText::_('COM_MIWOEVENTS_ON'); ?> <input type="text" name="monthdays" class="inputbox" size="10" value="<?php echo $this->item->monthdays; ?>" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="key2">
				<?php echo MText::_('COM_MIWOEVENTS_RECURRING_ENDING'); ?>
			</td>
			<td class="value2">
				<p style="width: 150px;">
				<input type="radio" name="repeat_until" value="1"  <?php if (($this->item->recurring_occurrencies > 0) || ($this->item->recurring_end_date == '') || ($this->item->recurring_end_date == '0000-00-00 00:00:00')) echo ' checked="checked" ' ; ?> /> <?php echo MText::_('COM_MIWOEVENTS_AFTER'); ?> <input type="text" name="recurring_occurrencies" size="5" class="inputbox" value="<?php echo $this->item->recurring_occurrencies ; ?>" /> <?php echo MText::_('COM_MIWOEVENTS_OCCURENCIES'); ?>
				<br />
				<input type="radio" name="repeat_until" value="2" <?php if (($this->item->recurring_end_date != '') && ($this->item->recurring_end_date != '0000-00-00 00:00:00')) echo ' checked="checked"' ; ?> /> <?php echo MText::_('COM_MIWOEVENTS_AFTER_DATE') ?> <?php echo MHtml::_('calendar', $this->item->recurring_end_date != '0000-00-00 00:00:00' ? MHtml::_('date', $this->item->recurring_end_date, $format, $param) : '', 'recurring_end_date', 'recurring_end_date'); ?>
				<br />
				</p>
			</td>
		</tr>

		<?php if ($this->item->id) { ?>
		<tr>
			<td class="key2">
				<?php echo MText::_('COM_MIWOEVENTS_UPDATE_CHILD_EVENT'); ?>
			</td>
			<td class="value2">
				<input type="checkbox" name="update_children_event" value="1" class="inputbox" />
			</td>
		</tr>
		<?php } ?>
		</table>
		<?php } ?>

        <!-- Registration -->
        <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_EVENTS_SL_REGISTRATION_TITLE'), 'sl_registration'); ?>
        <table class="admintable" width="100%">
        	<tr>
                <td class="key2"><?php echo MText::_('COM_MIWOEVENTS_REGISTRATION_TYPE'); ?></td>
                <td class="value2">
                	<div class="miwi_paid">
						<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'This feature'); ?></strong><br /><br />
						<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoevents-manage-book-events#pricing', 'MiwoEvents'); ?>
					</div>
                </td>
            </tr>


			
            <tr>
                <td class="key2">
                    <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_EVENT_CAPACITY');?>::<?php echo MText::_('COM_MIWOEVENTS_CAPACITY_EXPLAIN'); ?>"><?php echo MText::_('COM_MIWOEVENTS_CAPACITY'); ?></span>
                </td>
                <td class="value2">
                    <input type="text" name="event_capacity" id="event_capacity" class="inputbox" size="10" value="<?php echo $this->item->event_capacity; ?>" />
                </td>
            </tr>
            
            
            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOEVENTS_CURRENCY_SYMBOL'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['currency_symbol']; ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOEVENTS_PRICE'); ?>
                </td>
                <td class="value2">
                    <input type="text" name="individual_price" id="individual_price" class="inputbox" size="10" value="<?php echo $this->item->individual_price; ?>" />
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOEVENTS_TAX_CLASS'); ?>
                </td>
                <td class="value2">
                    <div class="miwi_paid">
						<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'This feature'); ?></strong><br /><br />
						<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoevents-manage-book-events#pricing', 'MiwoEvents'); ?>
					</div>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_MAX_GROUP_ATTENDERS');?>::<?php echo MText::_('COM_MIWOEVENTS_MAX_GROUP_ATTENDERS_EXPLAIN'); ?>"><?php echo MText::_('COM_MIWOEVENTS_MAX_GROUP_ATTENDERS'); ?></span>
                </td>
                <td class="value2">
                    <input type="text" name="max_group_number" id="max_group_number" class="inputbox" size="10" value="<?php echo $this->item->max_group_number; ?>" />
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOEVENTS_ENABLE_CANCEL'); ?>
                </td>
                <td class="value2">
                   <div class="miwi_paid">
						<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'This feature'); ?></strong><br /><br />
						<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoevents-manage-book-events#pricing', 'MiwoEvents'); ?>
					</div>
                </td>
            </tr>
        </table>

        <!-- Discount Settings -->
		<?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_SL_DISCOUNTS'), 'sl_discount'); ?>
		<table class="admintable" width="100%">
			<tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOEVENTS_EARLY_BIRD'); ?>
                </td>
                <td class="value2">
                    <input type="text" name="early_bird" id="early_bird" class="inputbox" size="10" style="width: 40px;"  value="<?php echo $this->item->early_bird_discount_amount; ?>" />&nbsp;
                    <?php echo $this->lists['early_bird_option']; ?>
                </td>
            </tr>

            <tr>
                <td>
                    <td>&nbsp;</td>
                </td>
                <td>&nbsp;</td>
            </tr>
		</table>

		<table class="adminlist" id="price_list"">
            <tr>
                <th colspan="2">
                    <?php echo MText::_('COM_MIWOEVENTS_GROUP_SETTINGS'); ?>
                </th>
            </tr>

            <tr>
                <th width="30%">
                    <?php echo MText::_('COM_MIWOEVENTS_REGISTRANT_NUMBER'); ?>
                </th>
                <th>
                    <?php echo MText::_('COM_MIWOEVENTS_RATE'); ?>
                </th>
            </tr>
            <?php
                $group_rates = json_decode($this->item->group_rates, true);

                $n = max(count($group_rates), 3);
                for ($i = 0; $i < $n ; $i++) {
                    if (isset($group_rates[$i])) {
                        $group              = $group_rates[$i];

                        $registrantNumber 	= $group['number'];
                        $price 				= $group['price'];
                    }
                    else {
                        $registrantNumber 	=  null;
                        $price 				=  null;
                    }
                    ?>
                    <tr>
                        <td>
                            <input type="text" class="inputbox input-mini" name="registrant_number[]" size="10" value="<?php echo $registrantNumber; ?>" />
                        </td>
                        <td>
                            <input type="text" class="inputbox input-mini" name="price[]" size="10" value="<?php echo $price; ?>" />
                        </td>
                    </tr>
                    <?php
                }
            ?>
            <tr>
                <td colspan="3">
                    <input type="button" class="button" value="<?php echo MText::_('COM_MIWOEVENTS_ADD'); ?>" onclick="addRow();" />
                    &nbsp;
                    <input type="button" class="button" value="<?php echo MText::_('COM_MIWOEVENTS_REMOVE'); ?>" onclick="removeRow();" />
                </td>
            </tr>
        </table>

        <!-- Individual Fields -->
        <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_EVENTS_SL_IF_TITLE'), 'sl_bf'); ?>
        <table class="admintable" width="100%">
        	<div class="miwi_paid">
				<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'This feature'); ?></strong><br /><br />
				<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoevents-manage-book-events#pricing', 'MiwoEvents'); ?>
			</div>
        </table>

        <!-- Group Fields -->
        <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_EVENTS_SL_GM_TITLE'), 'sl_gmf'); ?>
        <table class="admintable" width="100%">
        	<div class="miwi_paid">
				<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'This feature'); ?></strong><br /><br />
				<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoevents-manage-book-events#pricing', 'MiwoEvents'); ?>
			</div>
        </table>

        <!-- Custom Fields -->
        <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_EVENTS_SL_CF_TITLE'), 'sl_custom'); ?>
         <table class="admintable" width="100%">
             <div class="miwi_paid">
				<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'This feature'); ?></strong><br /><br />
				<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoevents-manage-book-events#pricing', 'MiwoEvents'); ?>
			</div>
         </table>

        <!-- Reminder -->
        <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_EVENTS_SL_REMINDER_TITLE'), 'sl_reminder'); ?>
        <table class="admintable" width="100%">
	        <div class="miwi_paid">
				<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'This feature'); ?></strong><br /><br />
				<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoevents-manage-book-events#pricing', 'MiwoEvents'); ?>
			</div>
        </table>

        <!-- Meta Settings -->
		<?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_META_OPTIONS'), 'publishing'); ?>
		<table class="admintable" width="100%">
		<tr>
	    	<td class="key2">
	        	<?php echo MText::_('COM_MIWOEVENTS_META_DESC'); ?>
			</td>
			<td class="value2">
				<textarea name="meta_desc" id="meta_desc" cols="40" rows="3" class="" aria-invalid="false"><?php echo $this->item->meta_desc;?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key2">
				<?php echo MText::_('COM_MIWOEVENTS_META_KEYWORDS'); ?>
			</td>
			<td class="value2">
				<textarea name="meta_key" id="meta_key" cols="40" rows="3" class="" aria-invalid="false"><?php echo $this->item->meta_key;?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key2">
				<?php echo MText::_('COM_MIWOEVENTS_META_AUTHOR'); ?>
			</td>
			<td class="value2">
				<input class="text_area" type="text" name="meta_author" id="meta_author" size="40" maxlength="250" value="<?php echo $this->item->meta_author;?>" />
			</td>
		</tr>
		</table>

        <?php echo MHtml::_('sliders.end'); ?>
	</div>

	<input type="hidden" name="option" value="com_miwoevents" />

    <?php if ($this->_mainframe->isSite()) { ?>
    <input type="hidden" name="view" value="event" />
    <input type="hidden" name="Itemid" value="<?php echo MiwoEvents::getInput()->getInt('Itemid', 0); ?>" />
    <?php } else { ?>
    <input type="hidden" name="view" value="events" />
    <?php } ?>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<?php echo MHtml::_( 'form.token' ); ?>

	<script type="text/javascript">
		Miwi.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				Miwi.submitform(pressbutton);
				return;
			} else {
				//Should have some validations rule here
				//Check something here
				if (form.title.value == '') {
					alert("<?php echo MText::_( 'COM_MIWOEVENTS_PLEASE_ENTER_TITLE'); ?>");
					form.title.focus();
					return ;
				}
				if (form.category_id.value == 0) {
					alert("<?php echo MText::_("COM_MIWOEVENTS_CHOOSE_CATEGORY"); ?>");
					form.category_id.focus();
					return ;
				}
				if (form.event_date.value == '') {
					alert("<?php echo MText::_( 'COM_MIWOEVENTS_ENTER_EVENT_DATE'); ?>");
					form.event_date.focus();
					return ;
				}
				if (form.recurring_type) {
					//Check the recurring setting
					if (form.recurring_type[1].checked) {
						if (form.number_days.value == '') {
							alert("<?php echo MText::_("COM_MIWOEVENTS_ENTER_NUMBER_OF_DAYS"); ?>");
							form.number_days.focus();
							return ;
						}
						if (!parseInt(form.number_days.value)) {
							alert("<?php echo MText::_("COM_MIWOEVENTS_NUMBER_DAY_INTEGER"); ?>");
							form.number_days.focus();
							return ;
						}
					}else if (form.recurring_type[2].checked) {
						if (form.number_weeks.value == '') {
							alert("<?php echo MText::_("COM_MIWOEVENTS_ENTER_NUMBER_OF_WEEKS"); ?>");
							form.number_weeks.focus();
							return ;
						}
						if (!parseInt(form.number_weeks.value)) {
							alert("<?php echo MText::_("COM_MIWOEVENTS_NUMBER_WEEKS_INTEGER"); ?>");
							form.number_weeks.focus();
							return ;
						}
						//Check whether any days in the week
						var checked = false ;
						for (var i = 0 ; i < form['weekdays[]'].length ; i++) {
							if (form['weekdays[]'][i].checked)
								checked = true ;
						}
						if (!checked) {
							alert("<?php echo MText::_("COM_MIWOEVENTS_CHOOSE_ONEDAY"); ?>");
							form['weekdays[]'][0].focus();
							return ;
						}
					} else if (form.recurring_type[3].checked) {
						if (form.number_months.value == '') {
							alert("<?php echo MText::_("COM_MIWOEVENTS_ENTER_NUMBER_MONTHS"); ?>");
							form.number_months.focus();
							return ;
						}
						if (!parseInt(form.number_months.value)) {
							alert("<?php echo MText::_("COM_MIWOEVENTS_NUMBER_MONTH_INTEGER"); ?>");
							form.number_months.focus();
							return ;
						}
						if (form.monthdays.value == '') {
							alert("<?php echo MText::_("COM_MIWOEVENTS_ENTER_DAY_IN_MONTH"); ?>");
							form.monthdays.focus();
							return ;
						}
					}
				}

				<?php
					$editorFields = array('description', 'registrant_email_body', 'thanks_message', 'registration_approved_email_body');
					foreach ($editorFields as $editorField) {
						echo $editor->save($editorField);
					}

				?>
				Miwi.submitform(pressbutton);
			}
		}
		function addRow() {
			var table = document.getElementById('price_list');
			var newRowIndex = table.rows.length - 1 ;
			var row = table.insertRow(newRowIndex);
			var registrantNumber = row.insertCell(0);
			var price = row.insertCell(1);
			registrantNumber.innerHTML = '<input type="text" class="inputbox" name="registrant_number[]" size="10" />';
			price.innerHTML = '<input type="text" class="inputbox" name="price[]" size="10" />';

		}
		function removeRow() {
			var table = document.getElementById('price_list');
			var deletedRowIndex = table.rows.length - 2 ;
			if (deletedRowIndex >= 1) {
				table.deleteRow(deletedRowIndex);
			} else {
				alert("<?php echo MText::_( 'COM_MIWOEVENTS_NO_ROW_TO_DELETE'); ?>");
			}
		}

		function setDefaultData() {
			var form = document.adminForm ;
			if (form.recurring_type[1].checked) {
				if (form.number_days.value == '') {
					form.number_days.value =1 ;
				}
			} else if (form.recurring_type[2].checked) {
				if (form.number_weeks.value == '') {
					form.number_weeks.value = 1 ;
				}
			} else if (form.recurring_type[3].checked) {
				if (form.number_months.value == '') {
					form.number_months.value = 1 ;
				}
			}
		}
	</script>
</form>
<?php
    if (MFactory::getApplication()->isSite()) {
        echo '</div><br/><br/>';
    }
?>