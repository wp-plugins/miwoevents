<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

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
if (MFactory::getUri()->isSSL() == true) { $http = "https://"; } else { $http = "http://"; }
$getDirectionLink = $http.'maps.google.com/maps?f=d&daddr='.$this->location->coordinates.'('.addslashes($this->location->address).')';
$param = null;
?>
<form method="post" name="adminForm" id="adminForm" action="<?php echo MFactory::getUri()->toString(); ?>">
	<!-- Events List -->
	<?php if(count($this->items)) { ?>	
	    <div id="miwoevents_docs">
	    <h1 class="location_header"><?php echo MText::sprintf('COM_MIWOEVENTS_EVENTS_FROM_LOCATION', $this->location->title); if ($this->location_id != 0){ ?><a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=location&location_id='.$this->location->id.$this->Itemid); ?>" class="view_map_link">[<?php echo MText::_('COM_MIWOEVENTS_DETAILS'); ?>]</a><a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=location&layout=map&location_id='.$this->location->id.$this->Itemid.'&tmpl=component'); ?>" rel="gb_page_center[600, 350]" title="<?php echo $this->location->title ; ?>" class="location_link view_map_link"><?php echo MText::_( 'COM_MIWOEVENTS_VIEW_MAP'); ?></a><a class="view_map_link" href="<?php echo $getDirectionLink ; ?>" target="_blank"><?php echo MText::_( 'COM_MIWOEVENTS_GET_DIRECTION'); ?></a><?php } ?></h1>
	    <?php
            $n = count($this->items);

	        for ($i = 0;  $i < $n; $i++) {
	        	$item = $this->items[$i];

                $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $item->id), null, true);

	        	$canRegister = MiwoEvents::get('events')->canRegister($item->id);
	        	$url = MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$item->id.$this->Itemid);

                if (($item->event_capacity > 0) && ($item->event_capacity <= $item->total_attenders) and $this->MiwoeventsConfig->waitinglist_enabled) {
	        	    $waitingList = true ;
	        	    $waitinglistUrl = MRoute::_('index.php?option=com_miwoevents&task=waitinglist_form&event_id='.$item->id.$this->Itemid);
	        	}
                else {
	        	    $waitingList = false;
                }

                $template = MFactory::getApplication()->getTemplate();
                $ovrr_path = MPATH_WP_CNT.'/themes/'.$template.'/com_miwoevents/event/common.php';
                if (file_exists($ovrr_path)) {
                    include $ovrr_path;
                }
                else {
                    include MPATH_MIWOEVENTS.'/views/event/tmpl/common.php';
                }
	        }
	    ?>
	    </div>	    
    	<?php if ($this->pagination->total > $this->pagination->limit) { ?>
        <div align="center">
            <?php echo $this->pagination->getListFooter(); ?>
        </div>
    	<?php } ?>
	<?php } else { ?>
	    <br />
	    <div id="miwoevents_docs">
	        <i><?php echo MText::_( 'COM_MIWOEVENTS_NO_EVENTS'); ?></i>
	    </div>
	<?php } ?>

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
	<input type="hidden" name="view" value="location" />
    <input type="hidden" name="task" value="" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ; ?>" />
    <input type="hidden" name="location_id" value="<?php echo $this->location_id; ?>" />
    <input type="hidden" name="id" value="0" />
</form>