<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

if (count($rows)) {
?>
	<ul class="menu location_list">
		<?php
			foreach ($rows as $row) {
                $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'location', 'location_id' => $row->id), null, true);

                if ($params->get('link_type', 0) == 0) {
	    		    $link = MRoute::_('index.php?option=com_miwoevents&view=location&location_id='.$row->id .$Itemid);
                }
                else {
	    		    $link = MRoute::_('index.php?option=com_miwoevents&view=location&layout=events&location_id='.$row->id . $Itemid);
                }
                ?>
				<li>
					<a href="<?php echo $link; ?>"><?php echo $row->title; ?>
					<?php if ($showNumberEvents) { ?>
							<span class="number_events">&nbsp;(&nbsp;<?php echo $row->total_events .' '. ($row->total_events > 1 ? MText::_('COM_MIWOEVENTS_EVENTS') : MText::_('COM_MIWOEVENTS_EVENT')) ?>&nbsp;)&nbsp;</span>
					<?php } ?>
					</a>
				</li>
	  <?php } ?>
	</ul>
<?php
}