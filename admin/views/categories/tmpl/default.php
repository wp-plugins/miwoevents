<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;
	
$ordering = ($this->lists['order'] == 'c.ordering');

MHtml::_('behavior.tooltip');
?>
<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm">
<table width="100%">
<tr>
	<td style="float: right;">
		<?php echo MText::_( 'Filter' ); ?>:
		<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area search-query" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();" class="button"><?php echo MText::_( 'Go' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.submit();" class="button"><?php echo MText::_( 'Reset' ); ?></button>
	</td>
	<td style="float: left;">
			<?php echo $this->lists['bulk_actions']; ?>
                <button onclick="Miwi.submitform(document.getElementById('bulk_actions').value);" class="button"><?php echo MText::_('Apply'); ?></button>
                &nbsp;&nbsp;&nbsp;
		<?php echo $this->lists['filter_parent']; ?>
		<?php echo $this->lists['filter_published'] ; ?>

        




		<select name="filter_language" class="inputbox" onchange="this.form.submit()">
    		<option value=""><?php echo MText::_('MOPTION_SELECT_LANGUAGE');?></option>
    		<?php echo MHtml::_('select.options', MHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->filter_language);?>
    	</select>
			<button onclick="this.form.submit();" class="button"><?php echo MText::_('Filter'); ?></button>
	</td>	
</tr>
</table>
<div id="editcell">
	<table class="wp-list-table widefat">
	<thead>
		<tr>
			


			<th width="20">
                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo MText::_('MGLOBAL_CHECK_ALL'); ?>" onclick="Miwi.checkAll(this)" />
			</th>
			<th width="20">
				&nbsp;
			</th>
			<th style="text-align: left;">
				<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOEVENTS_NAME'), 'c.title', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>											
			<th width="10%" style="text-align: center;">
				<?php echo MText::_('COM_MIWOEVENTS_NUMBER_EVENTS'); ?>
			</th>
            <th width="5%" style="text-align: center;">
                <?php echo MHtml::_('grid.sort', MText::_('MSTATUS'), 'c.published', $this->lists['order_Dir'], $this->lists['order']); ?>
            </th>
			<th width="<?php echo MiwoEvents::is30() ? '12' : '8'; ?>%" style="text-align: right;">
				<?php echo MHtml::_('grid.sort', MText::_('MGRID_HEADING_ORDERING'), 'c.ordering', $this->lists['order_Dir'], $this->lists['order']); ?>
                <?php if ($ordering) { ?>
				<?php echo MHtml::_('grid.order', $this->items, 'filesave.png', 'saveOrder'); ?>
                <?php } ?>
			</th>
            


            <th width="5%" style="text-align: center;">
                <?php echo MHtml::_('grid.sort', 'MGRID_HEADING_LANGUAGE', 'c.language', $this->lists['order_Dir'], $this->lists['order']); ?>
            </th>
			<th width="2%" style="text-align: center;">
				<?php echo MHtml::_('grid.sort', MText::_('ID'), 'c.id', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>													
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="10">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
    $n = count($this->items);
    for ($i = 0; $i < $n; $i++) {
		$row = $this->items[$i];

		$link = MRoute::_('index.php?option=com_miwoevents&view=categories&task=edit&cid[]='.$row->id);

		$checked = MHtml::_('grid.id', $i, $row->id);
   


        $published = $this->getIcon($i, $task = $row->published == '0' ? 'publish' : 'unpublish', $row->published ? 'publish_y.png' : 'publish_x.png', true);


		?>
		<tr class="<?php echo "row$k"; ?>">



			<td>
				<?php echo $checked; ?>
			</td>
			<td>
                <?php if (!empty($row->color_code)) { ?>
                <div style="background:#<?php echo $row->color_code; ?>;width:20px;height:20px;-moz-border-radius: 3px;border-radius: 3px; "></div>
                <?php } ?>
			</td>
			<td>
                <?php if (MiwoEvents::get('acl')->canEdit()) { ?>
                    <a href="<?php echo $link; ?>">
                        <?php echo $row->treename; ?>
                    </a>
                <?php } else { ?>
                    <?php echo $row->treename; ?>
                <?php } ?>
			</td>
			<td style="text-align: center;">
				<?php echo $row->total_events; ?>
			</td>
            <td style="text-align: center;">
                <?php echo $published; ?>
            </td>
			<td class="ordering" style="text-align: right;">
                <?php if ($ordering) { ?>
				<span><?php echo $this->pagination->orderUpIcon($i, ($row->parent==0 || $row->parent == @$this->items[$i-1]->parent),'orderup', 'Move Up', $ordering ); ?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $n, ($row->parent ==0 || $row->parent == @$this->items[$i+1]->parent), 'orderdown', 'Move Down', $ordering ); ?></span>
                <?php } ?>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>				
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" style="text-align: center; width: 30px;" <?php echo $disabled; ?> />
			</td>
            


            <td style="text-align: center;">
                <?php if ($row->language == '*') { ?>
                <?php echo MText::alt('MALL', 'language'); ?>
                <?php } else { ?>
                <?php echo isset($this->langs[$row->language]->title) ? $this->escape($this->langs[$row->language]->title) : MText::_('MUNDEFINED'); ?>
                <?php } ?>
            </td>
			<td style="text-align: center;">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
	</div>

	<input type="hidden" name="option" value="com_miwoevents" />
	<input type="hidden" name="view" value="categories" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />	
	<?php echo MHtml::_('form.token'); ?>
</form>