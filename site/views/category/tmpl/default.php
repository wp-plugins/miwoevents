<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$param = null;

//Load greybox lib
$greyBox = MUri::base().'components/com_miwoevents/assets/js/greybox/';
?>
<script type="text/javascript">
    var GB_ROOT_DIR = "<?php echo $greyBox ; ?>";
</script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS.js"></script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS_fx.js"></script>
<script type="text/javascript" src="<?php echo $greyBox; ?>/gb_scripts.js"></script>
<link href="<?php echo $greyBox; ?>/gb_styles.css" rel="stylesheet" type="text/css" />

<!-- categories -->
<?php if (($this->params->get('show_page_heading', '0') == '1')) { ?>
    <?php $page_title = $this->params->get('page_title', ''); ?>

    <?php if (!empty($this->category->title)) { ?>
        <h1><?php echo $this->category->title;?></h1>
    <?php } else if (!empty($page_title)) { ?>
        <h1><?php echo $page_title; ?></h1>
    <?php } ?>
<?php } ?>

<?php if (!empty($this->category->id)) { ?>
	<div class="miwoevents_cat">
        <?php if (!empty($this->category->introtext) or !empty($this->category->fulltext)) { ?>
            <div class="miwi_description"><?php echo $this->category->introtext.$this->category->fulltext; ?></div>
        <?php } ?>
	</div>
    <div class="clr"></div>
<?php 
}

if (!empty($this->categories)) {
    ?>

	<div id="miwoevents_cats">
        <?php if (!empty($this->category->id)) { ;?>
	        <h2 class="miwoevents_title"><?php echo MText::_('COM_MIWOEVENTS_SUB_CATEGORIES');?></h2>
        <?php } ?>

	    <?php			     	
    	foreach ($this->categories as $category) {
	    	if (!$this->MiwoeventsConfig->show_empty_cat and !$category->total_events) {
	    		continue;
            }

	    	$link = MRoute::_('index.php?option=com_miwoevents&view=category&category_id='.$category->id.$this->Itemid);
    	?>

        <div class="miwoevents_box">
            <div class="miwoevents_box_heading">
                <h3 class="miwoevents_box_h3">
                    <a href="<?php echo $link; ?>" title="<?php echo $category->title; ?>">
                        <?php echo $category->title; ?>
                        <?php if ($this->MiwoeventsConfig->show_number_events) { ?>
                            <small>( <?php echo $category->total_events ;?> <?php echo $category->total_events > 1 ? MText::_('COM_MIWOEVENTS_EVENTS') :  MText::_( 'COM_MIWOEVENTS_EVENT'); ?>)</small>
                        <?php } ?>
                    </a>
					<?php if (MiwoEvents::get('acl')->canEdit()) {
						$edit_itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'category', 'layout' => 'submit'), null, true);
						$edit_url = MRoute::_('index.php?option=com_miwoevents&view=category&layout=submit&category_id='.$category->id.$edit_itemid, false);
						?>
						&nbsp;[<a href="<?php echo $edit_url; ?>"><strong><?php echo MText::_('COM_MIWOEVENTS_EDIT'); ?></strong></a>]
					<?php } ?>
                </h3>
            </div>
            <?php if (!empty($category->introtext)) { ?>
            <div class="miwoevents_box_content">
                <?php echo $category->introtext; ?>
            </div>
            <?php } ?>
        </div>
    	<?php } ?>
    </div>
    <div class="clr"></div>
<?php 
}
?>
<!-- categories -->

<!-- category -->
<?php if ($this->category->id != 0){ ?>
<form method="post" name="adminForm" id="adminForm" action="<?php echo MRoute::_('index.php?option=com_miwoevents&view=category&category_id='.$this->category->id.$this->Itemid); ?>">
	<!-- Events List -->
	<?php if(count($this->items)) { ?>
	    <div id="miwoevents_docs">
	   		<h2 class="miwoevents_title"><?php echo MText::_('COM_MIWOEVENTS_EVENTS'); ?></h2>
		    <?php
                $n = count($this->items);
		        for ($i = 0; $i < $n; $i++) {
		        	$item = $this->items[$i];
		        	$canRegister = MiwoEvents::get('events')->canRegister($item->id);

                    $waitingList = false;

		        	$url = MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$item->id.$this->Itemid);
		        	
		        	if (($item->event_capacity > 0) and ($item->event_capacity <= $item->total_attenders) and $this->MiwoeventsConfig->waitinglist_enabled and !$item->user_registered) {
		        	    $waitingList = true;
		        	    $waitinglistUrl = MRoute::_('index.php?option=com_miwoevents&view=waitinglist&task=waitinglist_form&id='.$item->id.$this->Itemid);
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
        <?php if ($this->pagination->total > $this->pagination->limit) { ?>
            <div align="center" class="pagination">
                <?php echo $this->pagination->getListFooter(); ?>
            </div>
        <?php } ?>
    <?php } else { ?>
    <div id="miwoevents_docs">
        <i><?php echo MText::_('COM_MIWOEVENTS_NO_EVENTS'); ?></i>
    </div>
	<?php } ?>

	<input type="hidden" name="option" value="com_miwoevents" />	
	<input type="hidden" name="view" value="category" />
	<input type="hidden" name="task" value="" />	
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ; ?>" />
    <input type="hidden" name="id" value="0" />

	<script type="text/javascript">
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
</form>
<?php } ?>