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
	<ul class="menu">
		<?php
			foreach ($rows as $row) {
                $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'category', 'category_id' => $row->id), null, true);

	    		$link = MRoute::_('index.php?option=com_miwoevents&view=category&category_id='.$row->id . $Itemid);?>
				<li>
					<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
				</li>
	  <?php } ?>
	</ul>
<?php
}