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
$greyBox = MURL_MIWOEVENTS.'/site/assets/js/greybox/';

$socialUrl = MUri::base().$url;

?>
<script type="text/javascript">
    var GB_ROOT_DIR = "<?php echo $greyBox; ?>";
</script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS.js"></script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS_fx.js"></script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/gb_scripts.js"></script>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,300' rel='stylesheet' type='text/css'>
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
                <?php } ?>
                <!--//end FB / Twitter / Gplus sharing -->
            </div>
		<?php if(isset($this->MiwoeventsConfig->early_bird_discount_date) AND ($this->MiwoeventsConfig->early_bird_discount_date == 1) AND @($this->item->individual_price != $this->item->discounted_price) AND @($this->item->discounted_price > 0)){ ?>	
            <div class="miwoevents_box_content_cont">
                <?php if (isset($this->item->earl_bird_day_date_Timestamp) AND $this->item->earl_bird_day_date_Timestamp > 0) { ?>
                    <section>
						<div class="big-countdown">
                            <div>
                            <div class="earl_price_show">
                               <span class="orjinal">
                                   <del>
                                <?php if ($this->show_price) { ?>
                                    <?php
                                    if ($this->item->individual_price > 0 and $this->MiwoeventsConfig->show_individual_price) {
                                        echo MiwoEvents::get('utility')->getAmount($this->item->individual_price, $this->MiwoeventsConfig->currency_symbol);
                                    } else {
                                        echo '<span class="miwoevents_free">'.MText::_('COM_MIWOEVENTS_FREE').'</span>';
                                    }
                                    ?>
                                   </del>
                               </span>
                                &nbsp;&nbsp;
                                <span class="discount">
                                    <?php if ($this->MiwoeventsConfig->show_discounted_price and ($this->item->individual_price != $this->item->discounted_price)) { ?>
                                        <?php
                                    if ($this->item->individual_price > 0) {
                                        echo MiwoEvents::get('utility')->getAmount($this->item->discounted_price, $this->MiwoeventsConfig->currency_symbol);
                                    } else {
                                        echo '<span class="miwoevents_free">'.MText::_('COM_MIWOEVENTS_FREE').'</span>';
                                    }
                                    ?>
                                    <?php } } ?>
                                </span>

                            </div>
                            </div>
                        </div>
                        <div class="clearfixDiscont"></div>
                        <div class="big-countdown">
                            <div>
                                <span data-time="<?php echo $this->item->earl_bird_day_date_Timestamp; ?>" class="miwoevents_earl_bird_day"></span>
                            </div>
                        </div>
                    </section>
                <?php }  ?>
            </div>
            <div class="clear"></div>
		<?php } ?>
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
			    <?php  if(1==$this->MiwoeventsConfig->hide_location_maps){ ?>
                <div class="miwoevents_box_content_map" style="display: none;">
				<?php }else{ ?>
                <div class="miwoevents_box_content_map">
                <?php } ?>
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

                
					if (MiwoEvents::get('acl')->canEdit()) {
                    $edit_itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'layout' => 'submit'), null, true);
			














































                    $edit_url = MRoute::_('index.php?option=com_miwoevents&view=event&layout=submit&event_id='.$this->item->id.$edit_itemid, false);
                    ?>
                    <a class="<?php echo MiwoEvents::getButtonClass(); ?>" href="<?php echo $edit_url; ?>"><strong><?php echo MText::_('COM_MIWOEVENTS_EDIT'); ?></strong></a>
                    <?php
                }

                if (MiwoEvents::get('acl')->canEditState()) {
                    $unpublish_url = MRoute::_('index.php?option=com_miwoevents&view=event&task=updatestatus&event_id='.$this->item->id.$this->Itemid, false);
                    ?>
                    <a class="<?php echo MiwoEvents::getButtonClass(); ?>" href="<?php echo $unpublish_url; ?>"><strong><?php echo MText::_('COM_MIWOEVENTS_UNPUBLISH'); ?></strong></a>
                    <?php
                }

                 ?>












            </div>
			











			<input type="hidden" name="option" value="com_miwoevents" />
			<input type="hidden" name="view" value="event" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
			<input type="hidden" name="id" value="0" />
			</form>


            <?php if ($this->MiwoeventsConfig->comments != 0) { ?>
            <div class="miwoevents_box_content_cont3">
                <?php
                if ($this->MiwoeventsConfig->comments == 1) {
                    require_once(MPATH_SITE.'/components/com_jcomments/jcomments.php');

                    echo JComments::showComments($this->item->id, 'com_miwoevents', $this->item->title);
                }
                else if ($this->MiwoeventsConfig->comments == 2) {
                    require_once(MPATH_SITE.'/components/com_komento/bootstrap.php');

					mimport('framework.html.parameter');
					//$p_options = array('params' => new MInput());
                    $p_options = array('params' => new JParameter('')); //2013-07-24 Lodos
                    $p_object = new stdClass();
                    $p_object->id = $this->item->id;
                    $p_object->title = $this->item->title;

                    echo Komento::commentify('com_miwoevents', $p_object, $p_options);
                }
                else if ($this->MiwoeventsConfig->comments == 3) {
                    MLoader::discover('ccommentHelper', MPATH_SITE . '/components/com_comment/helpers');
                    ccommentHelperUtils::commentInit('com_miwoevents', $this->item, null);
                }
                else if ($this->MiwoeventsConfig->comments == 4) {
                    ?>
                    <div class="jwDisqusForm">
                        <?php echo Miwoevents::get('utility')->renderDisqus($this->item)->comments; ?>
                        <div id="jwDisqusFormFooter">
                            <div class="clr"></div>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>
            <?php } ?>
        </div>
        <div class="clr"></div>
    </div>
</div>

<?php if(isset($this->MiwoeventsConfig->early_bird_discount_date) AND ($this->MiwoeventsConfig->early_bird_discount_date == 1)){ ?>
	   <script>
            $(document).ready(function(){
                $(".miwoevents_earl_bird_day").miwoevents_earl_bird_day({
                    dayText     : '<?php echo MText::_('COM_MIWOEVENTS_EARL_BIRD_DAY'); ?> ',
                    daysText    : '<?php echo MText::_('COM_MIWOEVENTS_EARL_BIRD_DAYS'); ?> ',
                    hoursText   : '<?php echo MText::_('COM_MIWOEVENTS_EARL_BIRD_HOUR'); ?> ',
                    minutesText : '<?php echo MText::_('COM_MIWOEVENTS_EARL_BIRD_MINUTE'); ?> ',
                    secondsText : '<?php echo MText::_('COM_MIWOEVENTS_EARL_BIRD_SECOND'); ?> ',
                    displayZeroDays : true,
                    rusNumbers  :   false
                });
            });
        </script>
<?php } ?>		