<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$url = MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$this->item->id.$this->Itemid);
$canRegister = MiwoEvents::get('events')->canRegister($this->item->id);
$greyBox = MUri::base().'components/com_miwoevents/assets/js/greybox/';

$socialUrl = MUri::base().$url;

?>
<script type="text/javascript">
    var GB_ROOT_DIR = "<?php echo $greyBox; ?>";
</script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS.js"></script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS_fx.js"></script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/gb_scripts.js"></script>
<link href="<?php echo $greyBox; ?>/gb_styles.css" rel="stylesheet" type="text/css" />

<div name="adminForm" id="adminForm">
    <div class="miwoevents_box">
        <div class="miwoevents_box_heading">
            <h1 class="miwoevents_box_h1"><?php echo $this->item->title; ?></h1>
            <span class="ecalex">
            	<!-- ical -->
            	<a style="text-decoration: none;" title="<?php echo MText::_('COM_MIWOEVENTS_EXPORTCAL_ICAL'); ?>" href="<?php echo $this->exportcal['ical']; ?>" >
                	<img class="" src="<?php echo MURL_MIWOEVENTS.'/site/assets/images/ical.png'; ?>"/>
                </a>
                
            	<!-- google -->
            	<a style="text-decoration: none;" title="<?php echo MText::_('COM_MIWOEVENTS_EXPORTCAL_GOOGLE'); ?>" href="<?php echo $this->exportcal['google']; ?>" target="_blank">
            		<img class="" src="<?php echo MURL_MIWOEVENTS.'/site/assets/images/gcal.png'; ?>"/>
            	</a>
                
            	<!-- microsoft -->
            	<a style="text-decoration: none;" title="<?php echo MText::_('COM_MIWOEVENTS_EXPORTCAL_MICROSOFT'); ?>" href="<?php echo $this->exportcal['microsoft']; ?>" target="_blank">
            		<img class="" src="<?php echo MURL_MIWOEVENTS.'/site/assets/images/mcal.png'; ?>"/>
            	</a>
            </span>
        </div>
        <div class="miwoevents_box_content">
            <div class="miwoevents_box_content_cont">
                <?php if ($this->item->thumb and file_exists(MPATH_MEDIA.'/miwoevents/images/thumbs/'.$this->item->thumb)) { ?>
                    <a href="<?php echo MURL_MEDIA.'/miwoevents/images/'.$this->item->thumb; ?>" class="modal"><img class="miwoevents_box_content_img_ev" src="<?php echo MURL_MEDIA.'/miwoevents/images/thumbs/'.$this->item->thumb; ?>"/></a>
                <?php } ?>
                <div class="miwoevents_box_content_100"><?php echo $this->item->description; ?></div>

                <!-- FB / Twitter / Gplus sharing -->
                <?php if ($this->MiwoeventsConfig->show_social_bookmark) { ?>
                <div class="sharing">
                    <!-- FB -->
                    <div style="float:left;" id="rsep_fb_like">
                        <div id="fb-root"></div>
                        <script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>
                        <script type="text/javascript">
                            FB.init({appId: '340486642645761', status: true, cookie: true, xfbml: true});
                        </script>
                        <fb:like href="<?php echo $socialUrl; ?>" send="true" layout="button_count" width="150" show_faces="false"></fb:like>
                    </div>

                    <!-- Twitter -->
                    <div style="float:left;" id="rsep_twitter">
                        <a href="https://twitter.com/share" class="twitter-share-button" data-text="<?php echo $this->item->title." ".$socialUrl; ?>">Tweet</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                    </div>

                    <!-- GPlus -->
                    <div style="float:left;" id="rsep_gplus">
                        <!-- Place this tag where you want the +1 button to render -->
                        <g:plusone size="medium"></g:plusone>

                        <!-- Place this render call where appropriate -->
                        <script type="text/javascript">
                            (function() {
                                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                                po.src = 'https://apis.google.com/js/plusone.js';
                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                            })();
                        </script>
                    </div>
                </div>
                <div class="clear"></div>
                <?php } ?>
                <!--//end FB / Twitter / Gplus sharing -->
            </div>

            <div class="miwoevents_box_content_cont2">
                <div class="miwoevents_box_content_info">
                    <span class="miwoevents_box_content_40">
                        <?php echo MText::_('COM_MIWOEVENTS_EVENT_DATE'); ?>
                    </span>
                    <span class="miwoevents_box_content_60">
                    	&nbsp;:&nbsp;
                        <?php
                        echo MHtml::_('date', $this->item->event_date, $this->MiwoeventsConfig->event_date_format, null) ;
                        ?>
                    </span>

                    <?php if ($this->item->event_end_date != $this->nullDate) { ?>
                    <span class="miwoevents_box_content_40">
                        <?php echo MText::_('COM_MIWOEVENTS_EVENT_END_DATE'); ?>
                    </span>
                    <span class="miwoevents_box_content_60">
                    	&nbsp;:&nbsp;
                        <?php echo MHtml::_('date', $this->item->event_end_date, $this->MiwoeventsConfig->event_date_format, null) ; ?>
                    </span>
                    <?php } ?>

                    <?php if ($this->MiwoeventsConfig->show_capacity) { ?>
                    <span class="miwoevents_box_content_40">
                       <?php echo MText::_('COM_MIWOEVENTS_CAPACITY'); ?>
                    </span>
                    <span class="miwoevents_box_content_60">
                    	&nbsp;:&nbsp;
                        <?php
                        if ($this->item->event_capacity) {
                            echo $this->item->event_capacity;
                        } else {
                            echo MText::_('COM_MIWOEVENTS_UNLIMITED');
                        }
                        ?>
                    </span>
                    <?php } ?>

                    <?php if ($this->MiwoeventsConfig->show_registered) { ?>
                    <span class="miwoevents_box_content_40">
                       <?php echo MText::_('COM_MIWOEVENTS_REGISTERED'); ?>
                    </span>
                    <span class="miwoevents_box_content_60">
                    	&nbsp;:&nbsp;
                        <?php echo $this->item->total_attenders; ?>
                        <?php
                        if ($this->MiwoeventsConfig->show_list_of_attenders and ($this->item->total_attenders > 0) and MiwoEvents::get('acl')->canAccessAttenders('component')) {
                            $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $this->item->id), null, true);
                            ?>
                            &nbsp;&nbsp;&nbsp;<a href="index.php?option=com_miwoevents&view=attenders&event_id=<?php echo $this->item->id.$this->Itemid; ?>&tmpl=component" rel="gb_page_center[600, 600]" class="registrant_list_link"><span class="view_list"><?php echo MText::_("COM_MIWOEVENTS_VIEW_LIST"); ?></span></a>
                            <?php
                        }
                        ?>
                    </span>
                    <?php } ?>

                    <?php if ($this->MiwoeventsConfig->show_available_place and $this->item->event_capacity) { ?>
                    <span class="miwoevents_box_content_40">
                       <?php echo MText::_('COM_MIWOEVENTS_AVAILABLE_PLACE'); ?>
                    </span>
                    <span class="miwoevents_box_content_60">
                    	&nbsp;:&nbsp;
                        <?php echo $this->registration_diff; ?>
                    </span>
                    <?php } ?>

                    <?php if (($this->nullDate != $this->item->cut_off_date) and ($this->MiwoeventsConfig->show_cut_off_date)) { ?>
                    <span class="miwoevents_box_content_40">
                       <?php echo MText::_('COM_MIWOEVENTS_CUT_OFF_DATE'); ?>
                    </span>
                    <span class="miwoevents_box_content_60">
                    	&nbsp;:&nbsp;
                        <?php echo MHtml::_('date', $this->item->cut_off_date, $this->MiwoeventsConfig->date_format, null) ; ?>
                    </span>
                    <?php } ?>

                    <?php if ($this->show_price) { ?>
                    <span class="miwoevents_box_content_40">
                       <?php if ($this->MiwoeventsConfig->show_discounted_price and ($this->item->individual_price != $this->item->discounted_price)) { ?>
                       <?php echo MText::_('COM_MIWOEVENTS_ORIGINAL_PRICE'); ?>
                       <?php } else { 
							if ($this->MiwoeventsConfig->show_individual_price) { ?>
                       <?php echo MText::_('COM_MIWOEVENTS_INDIVIDUAL_PRICE'); ?>
                       <?php } } ?>
                    </span>
                    <span class="miwoevents_box_content_60">
                    	&nbsp;:&nbsp;
                        <?php
                        if ($this->item->individual_price > 0) {
                            echo MiwoEvents::get('utility')->getAmount($this->item->individual_price, $this->MiwoeventsConfig->currency_symbol);
                        } else {
                            echo '<span class="miwoevents_free">'.MText::_('COM_MIWOEVENTS_FREE').'</span>';
                        }
                        ?>
                    </span>
                    <?php if ($this->MiwoeventsConfig->show_discounted_price and ($this->item->individual_price != $this->item->discounted_price)) { ?>
                    <span class="miwoevents_box_content_40">
                       <?php echo MText::_('COM_MIWOEVENTS_DISCOUNTED_PRICE'); ?>
                    </span>
                    <span class="miwoevents_box_content_60">
                        &nbsp;:&nbsp;
                        <?php
                        if ($this->item->individual_price > 0) {
                            echo MiwoEvents::get('utility')->getAmount($this->item->discounted_price, $this->MiwoeventsConfig->currency_symbol);
                        } else {
                            echo '<span class="miwoevents_free">'.MText::_('COM_MIWOEVENTS_FREE').'</span>';
                        }
                        ?>
                    </span>
                    <?php } } ?>
                    
                    <?php
                    /*
                        if(!empty($this->fields)) {
                            foreach ($this->fields as $field) {
                            ?>
                                <span class="miwoevents_box_content_40">
                                   <?php echo $field->title; ?>
                                </span>
                                <span class="miwoevents_box_content_60">
                                     &nbsp;:&nbsp;<?php echo str_replace('***', ', ', $field->field_value); ?>
                                </span>
                            <?php
                            }
                        }
                        */
                    ?>
                   
                    <?php if (count($this->item->group_rates)) { ?>
                    <span class="miwoevents_box_content_group">
                        <table style="width: 96%;">
                            <tr>
                                <th style="text-align: center;">
                                    <?php echo MText::_('COM_MIWOEVENTS_NUMBER_ATTENDERS'); ?>
                                </th>
                                <th style="text-align: center;">
                                    <?php echo MText::_('COM_MIWOEVENTS_RATE_PERSON'); ?>(<?php echo $this->MiwoeventsConfig->currency_symbol; ?>)
                                </th>
                            </tr>
                            <?php foreach ($this->item->group_rates as $group) { ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php echo $group->number; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo number_format($group->price, 2); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </span>
                    <?php } ?>
                </div>

                <?php if ($this->item->location_id) { ?>
                <div class="miwoevents_box_content_map">
                    <?php
                    $event 								= $this->item;
                    $this->item 						= $this->location;
                    include MPATH_MIWOEVENTS.'/views/location/tmpl/map.php';
                    $this->item = $event;
                    ?>
                </div>
                <?php } ?>
            </div>
			<form method="post" name="adminForm" id="adminForm" action="index.php">
            <div class="miwoevents_box_content_cont2">
                <?php
                $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $this->item->id), null, true);
                
				$createduserid   = $event->created_by;
                $userid          = @$_SESSION['__default']['user']->id;
                ?>
            </div>
			<script language="javascript">
				function cancelRegistration(registrant_id) {
					var form = document.adminForm;
					if (confirm("<?php echo MText::_('COM_MIWOEVENTS_CANCEL_REGISTRATION_CONFIRM'); ?>")) {
						form.view.value = 'registration';
						form.task.value = 'cancel';
						form.id.value = registrant_id;
						form.submit() ;
					}
				}
			</script>

			<input type="hidden" name="option" value="com_miwoevents" />
			<input type="hidden" name="view" value="event" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
			<input type="hidden" name="id" value="0" />
			</form>

        </div>
        <div class="clr"></div>
    </div>
</div>

