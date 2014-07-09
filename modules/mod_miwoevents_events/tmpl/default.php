<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

if ($showLocation) {
	$greyBox = MURL_MIWOEVENTS.'/site/assets/js/greybox/';
?>
	<script type="text/javascript">
	    var GB_ROOT_DIR = "<?php echo $greyBox ; ?>";
	</script>
	<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS.js"></script>
	<script type="text/javascript" src="<?php echo $greyBox; ?>/AJS_fx.js"></script>
	<script type="text/javascript" src="<?php echo $greyBox; ?>/gb_scripts.js"></script>
	<link href="<?php echo $greyBox; ?>/gb_styles.css" rel="stylesheet" type="text/css" />
<?php	
}

if (count($rows)) {
?>
	<table class="miwi_event_list" width="100%">
		<?php
			$tabs = array('sectiontableentry1' , 'sectiontableentry2');
			$k = 0 ;
			foreach ($rows as  $row) {
				$tab = $tabs[$k];
				$k = 1 - $k ;

                $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $row->id), null, true);
			?>	
				<tr class="<?php echo $tab; ?>">
					<td class="miwi_event">
						<a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$row->id . $Itemid); ?>" class="miwoevents_event_link"><?php echo $row->title ; ?></a>
						<br />
						<span class="event_date"><?php echo MHtml::_('date', $row->event_date, $config->event_date_format, null); ?></span>
						<?php
							if ($showCategory) {
							?>
								<br />		
								<span><?php echo MText::_('COM_MIWOEVENTS_CATEGORY'); ?>:&nbsp;&nbsp;<?php echo $row->categories; ?></span>
							<?php	
							}
							
							if ($showLocation and strlen($row->location_title)) {
							?>
								<br />		
								<a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=location&layout=map&location_id='.$row->location_id.'&tmpl=component'); ?>" rel="gb_page_center[600,350]" title="<?php echo $row->location_title; ?>" class="location_link"><?php echo $row->location_title; ?></a>
							<?php	 
							}
							
							if ($showPrice) {
							?>
								<br />		
								<span>
								<?php
								echo MText::_('COM_MIWOEVENTS_PRICE');
								echo ":&nbsp;&nbsp;";
								
								if ($row->price > 0) {
									echo MiwoEvents::get('utility')->getAmount($row->price, $config->currency_symbol);
								} else {
									echo MText::_('COM_MIWOEVENTS_FREE');
								}
								?>
								</span>
							<?php	
							}
							
						?>											
					</td>
				</tr>
			<?php
			}
		?>
	</table>
<?php	
} else {
?>
	<div class="miwi_empty"><?php echo MText::_('COM_MIWOEVENTS_NO_EVENTS') ?></div>
<?php	
}