<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;
MHtml::_('behavior.modal');	

# Load greybox lib
$greyBox = MUri::base().'components/com_miwoevents/assets/js/greybox/';
?>
<script type="text/javascript">
    var GB_ROOT_DIR = "<?php echo $greyBox ; ?>";
</script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS.js"></script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS_fx.js"></script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/gb_scripts.js"></script>
<link href="<?php echo $greyBox; ?>/gb_styles.css" rel="stylesheet" type="text/css" />
<?php

$param = null;	

$page_title = $this->params->get('page_title', '');
if (($this->params->get('show_page_heading', '0') == '1') && !empty($page_title)) { 
	$title = $page_title;
} else {
	$title = MText::_('COM_MIWOEVENTS_EVENTS_ARCHIVE'); 
}
	?>
<h1><?php echo $title; ?></h1>
<form method="post" name="adminForm" id="adminForm" action="index.php">
	<!-- Events List -->
	<?php if(count($this->item)) { ?>
	    <div id="miwoevents_docs">
            <?php
            $n = count($this->item);

            for ($i = 0;  $i < $n; $i++) {
                $item = $this->item[$i];
                $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $item->id), null, true);

                $canRegister = MiwoEvents::get('events')->canRegister($item->id);
                $url = MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$item->id.$Itemid);

                if (($item->event_capacity > 0) and ($item->event_capacity <= $item->total_attenders) and $this->MiwoeventsConfig->waitinglist_enabled) {
                    $waitingList = true ;
                    $waitinglistUrl = MRoute::_('index.php?option=com_miwoevents&task=waitinglist_form&event_id='.$item->id.$Itemid);
                }
                else {
                    $waitingList = false;
                }

                $template = MFactory::getApplication()->getTemplate();
                $ovrr_path = MPATH_WP_CNT.'/themes/'.$template.'/html/com_miwoevents/event/common.php';
                if (file_exists($ovrr_path)) {
                    include $ovrr_path;
                }
                else {
                    include MPATH_MIWOEVENTS.'/views/event/tmpl/common.php';
                }
            }
            ?>
	    </div>	    
    	<?php
    		if ($this->pagination->total > $this->pagination->limit) {
    		?>
    			<div align="center" class="pagination">
    				<?php echo $this->pagination->getListFooter(); ?>
    			</div>
    		<?php	
    		}
    	?>	    		   
	<?php } else { ?>
	    <br />
	    <div id="miwoevents_docs">
	        <i><?php echo MText::_( 'COM_MIWOEVENTS_NO_EVENTS'); ?></i>
	    </div>
	<?php } ?>
    <input type="hidden" name="option" value="com_miwoevents" />
    <input type="hidden" name="view" value="archive" />
</form>