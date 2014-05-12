<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ('Restricted access');

?>
<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<h3 class="miwoevents_box_h3"><a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>"><?php echo $item->title; ?></a></h3>
	</div>
    <div class="miwoevents_box_content">
        <div class="miwoevents_box_content_left">
            <?php if ($item->thumb and file_exists(MPATH_MEDIA.'/miwoevents/images/thumbs/'.$item->thumb)) { ?>
                <a href="<?php echo MURL_MEDIA.'/miwoevents/images/'.$item->thumb; ?>" class="modal"><img class="miwoevents_box_content_img" src="<?php echo MURL_MEDIA.'/miwoevents/images/thumbs/'.$item->thumb; ?>" class="miwoevents_thumb-left"/></a>
            <?php } ?>
            <?php echo $item->introtext; ?>
		</div>
		
		<div class="miwoevents_box_content_right_left">
        <div class="miwoevents_box_content_right">
            <div class="miwoevents_props">
                <div class="miwoevents_prop">
                    <span class="miwoevents_label">
                        <?php echo MText::_('COM_MIWOEVENTS_EVENT_DATE'); ?>
                    </span>
                    <span class="miwoevents_content">:&nbsp;
                        <?php echo MHtml::_('date', $item->event_date, $this->MiwoeventsConfig->event_date_format, $param); ?>
                    </span>
                </div>
                <?php
                    if ($item->event_end_date != $this->nullDate) {
                    ?>
                        <div class="miwoevents_prop">
                            <span class="miwoevents_label">
                                <?php echo MText::_('COM_MIWOEVENTS_EVENT_END_DATE'); ?>
                            </span>
                            <span class="miwoevents_content">:&nbsp;
                                <?php echo MHtml::_('date', $item->event_end_date, $this->MiwoeventsConfig->event_date_format, $param); ?>
                            </span>
                        </div>
                    <?php
                    }
                    if (($item->cut_off_date != $this->nullDate) and ($this->MiwoeventsConfig->show_cut_off_date)) {
                    ?>
                        <div class="miwoevents_prop">
                            <span class="miwoevents_label">
                                <?php echo MText::_('COM_MIWOEVENTS_CUT_OFF_DATE'); ?>
                            </span>
                            <span class="miwoevents_content">:&nbsp;
                                <?php echo MHtml::_('date', $item->cut_off_date, $this->MiwoeventsConfig->date_format,$param); ?>
                            </span>
                        </div>
                    <?php
                    }
                    if ($this->MiwoeventsConfig->show_capacity) {
                    ?>
                        <div class="miwoevents_prop">
                            <span class="miwoevents_label">
                                <?php echo MText::_('COM_MIWOEVENTS_CAPACTIY'); ?>
                            </span>
                            <span class="miwoevents_content">:&nbsp;
                                <?php
                                    if ($item->event_capacity) {
                                        echo $item->event_capacity;
                                    } else {
                                        echo MText::_('COM_MIWOEVENTS_UNLIMITED');
                                    }
                                ?>
                            </span>
                        </div>
                    <?php
                    }
                    if ($this->MiwoeventsConfig->show_registered) {
                    ?>
                        <div class="miwoevents_prop">
                            <span class="miwoevents_label">
                                <?php echo MText::_('COM_MIWOEVENTS_REGISTERED'); ?>
                            </span>
                            <span class="miwoevents_content">:&nbsp;
                                <?php echo (int) $item->total_attenders; ?>
                                <?php
                                    if ($this->MiwoeventsConfig->show_list_of_attenders and ($item->total_attenders > 0) and MiwoEvents::get('acl')->canAccessAttenders('component')) {
                                        $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $item->id), null, true);
                                    ?>
                                        &nbsp;&nbsp;&nbsp;<a href="index.php?option=com_miwoevents&view=attenders&event_id=<?php echo $item->id.$this->Itemid; ?>&tmpl=component" rel="gb_page_center[600, 600]" class="registrant_list_link"><span class="view_list"><?php echo MText::_("COM_MIWOEVENTS_VIEW_LIST"); ?></span></a>
                                    <?php
                                    }
                                ?>
                            </span>
                        </div>
                    <?php
                    }
                    if ($this->MiwoeventsConfig->show_available_place && $item->event_capacity) {
                    ?>
                        <div class="miwoevents_prop">
                            <span class="miwoevents_label">
                                <?php echo MText::_('COM_MIWOEVENTS_AVAILABLE_PLACE'); ?>
                            </span>
                            <span class="miwoevents_content">:&nbsp;
                                <?php echo $difference = $item->event_capacity - $item->total_attenders; ?>
                            </span>
                        </div>
                    <?php
                    }
					if ($item->individual_price > 0) {
                        $showPrice = true;
                    } else {
                        $showPrice = false;
                    }
                    
                    if ($this->MiwoeventsConfig->show_discounted_price && ($item->individual_price != $item->discounted_price)) {
                        if ($showPrice) {
                        ?>
                            <div class="miwoevents_prop">
                                <span class="miwoevents_label">
                                    <?php echo MText::_('COM_MIWOEVENTS_ORIGINAL_PRICE'); ?>
                                </span>
                                <span class="miwoevents_content miwoevents_price">:&nbsp;
                                    <?php
                                        if ($item->individual_price > 0) {
                                            echo MiwoEvents::get('utility')->getAmount($item->individual_price, $this->MiwoeventsConfig->currency_symbol);
                                        }  else {
                                            echo '<span class="miwoevents_price">'.MText::_('COM_MIWOEVENTS_FREE').'</span>' ;
                                        }
                                    ?>
                                </span>
                            </div>
                            <div class="miwoevents_prop">
                                <span class="miwoevents_label">
                                    <?php echo MText::_('COM_MIWOEVENTS_DISCOUNTED_PRICE'); ?>
                                </span>
                                <span class="miwoevents_content miwoevents_price">:&nbsp;
                                    <?php
                                        if ($item->discounted_price > 0) {
                                            if($item->early_bird_discount_type==0){
                                            $disc_amountper=$item->individual_price*$item->early_bird_discount_amount/100;
                                            $resultt=$item->individual_price-$disc_amountper;
                                            echo MiwoEvents::get('utility')->getAmount($resultt, $this->MiwoeventsConfig->currency_symbol);
                                            }else{
                                                $disc_amountfix=$item->individual_price-$item->early_bird_discount_amount;
                                                echo MiwoEvents::get('utility')->getAmount($disc_amountfix, $this->MiwoeventsConfig->currency_symbol);
                                            }
                                        }  else {
                                            echo '<span class="miwoevents_price">'.MText::_('COM_MIWOEVENTS_FREE').'</span>' ;
                                        }
                                    ?>
                                </span>
                            </div>
                        <?php
                        }
                    } else {
                        if ($showPrice) {
                        ?>
                            <div class="miwoevents_prop">
                                <span class="miwoevents_label">
                                    <?php echo MText::_('COM_MIWOEVENTS_PRICE'); ?>
                                </span>
                                <span class="miwoevents_content miwoevents_price">:&nbsp;
                                    <?php
                                        if ($item->individual_price > 0) {
                                            echo MiwoEvents::get('utility')->getAmount($item->individual_price, $this->MiwoeventsConfig->currency_symbol);
                                        }  else {
                                            echo '<span class="miwoevents_price">'.MText::_( 'COM_MIWOEVENTS_FREE').'</span>' ;
                                        }
                                    ?>
                                </span>
                            </div>
                        <?php
                        }
                    }
                    
                    # Custom Fields
                    /*
                    ########################################################################################################################
                    if (MiwoEvents::getConfig()->show_fields_in_category == 1) {
                   	$this->fields  = MiwoEvents::get('fields')->getEventFields($item->id,"yes");
                    if(!empty($this->fields)) { ?>
                    <div class="miwoevents_prop">
                    	<?php
						foreach ($this->fields as $field) { ?>
							<span class="miwoevents_label">
                            	<?php echo $field->title; ?>
                            </span>
                            <span class="miwoevents_content">:&nbsp;
                            	<?php echo str_replace('***', ', ', $field->field_value); ?>
                            </span>
                            <?php } ?>
                    </div>
                   <?php } }
                   */
                    ########################################################################################################################
                    
                    if (isset($item->paramData)) {
                        foreach ($item->paramData as $paramItem) {
                            if ($paramItem['value']) {
                            ?>
                                <div class="miwoevents_prop">
                                    <span class="miwoevents_label">
                                        <?php echo $paramItem['title']; ?>
                                    </span>
                                    <span class="miwoevents_content">:&nbsp;
                                        <?php
                                            echo $paramItem['value'];
                                        ?>
                                    </span>
                                </div>
                            <?php
                            }
                        ?>
                        <?php
                        }
                    }
                    if ($item->location_id && $this->MiwoeventsConfig->show_location_in_category_view) {
                        $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'location', 'layout' => 'map', 'location_id' => $item->id), null, true);
                    ?>
                        <div class="miwoevents_prop">
                            <span class="miwoevents_label">
                                <strong><?php echo MText::_( 'COM_MIWOEVENTS_LOCATION'); ?></strong>
                            </span>
                            <span class="miwoevents_content">:&nbsp;
                                <a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=location&layout=map&location_id='.$item->location_id.$this->Itemid.'&tmpl=component'); ?>" rel="gb_page_center[600, 350]" title="<?php echo $item->location_name ; ?>" class="location_link"><?php echo $item->location_name ; ?></a>
                            </span>
                        </div>
                    <?php
                    }
                    ?>
            </div>
        </div>
        
        </div>
        

        <div class="clr"></div>
        <div class="miwoevents_box_content_bottom">
            <?php
           
            $url = MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$item->id.$this->Itemid, false);
            
			$createduserid  = $item->created_by;
            $userid         = @$_SESSION['__default']['user']->id;
            ?>
            
            <a class="<?php echo MiwoEvents::getButtonClass(); ?>" href="<?php echo $url; ?>"><?php echo MText::_('COM_MIWOEVENTS_DETAILS'); ?></a>
        </div>
        <div class="clr"></div>
    </div>
</div>