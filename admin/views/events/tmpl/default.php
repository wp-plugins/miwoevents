<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$ordering = ($this->lists['order'] == 'e.ordering');
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
            <?php echo $this->lists['filter_past']; ?>
            <?php echo $this->lists['filter_category']; ?>
            <?php echo $this->lists['filter_location']; ?>
            <?php echo $this->lists['filter_published']; ?>

            




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
			<th class="title" style="text-align: left;">
				<?php echo MHtml::_('grid.sort',  MText::_('COM_MIWOEVENTS_TITLE'), 'title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th class="title" width="18%" style="text-align: left;">
				<?php echo MText::_( 'COM_MIWOEVENTS_CATEGORY'); ?>				
			</th>
			<th class="title" width="7%">
				<?php echo MHtml::_('grid.sort',  MText::_( 'COM_MIWOEVENTS_EVENT_DATE'), 'e.event_date', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>			
			<th class="title" width="7%">
				<?php echo MHtml::_('grid.sort', MText::_( 'COM_MIWOEVENTS_CAPACITY'), 'e.event_capacity', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>																							
			<th class="title" width="7%">
				<?php echo MHtml::_('grid.sort',  MText::_( 'COM_MIWOEVENTS_NUMBER_ATTENDERS'), 'attenders', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
            <?php if (@$this->MiwoeventsConfig->activate_recurring_event) { ?>
             <th width="8%">
                 <?php echo MHtml::_('grid.sort', MText::_( 'COM_MIWOEVENTS_EVENT_TYPE'), 'e.event_type', $this->lists['order_Dir'], $this->lists['order'] ); ?>
             </th>
            <?php }	?>
            <th width="5%">
                <?php echo MHtml::_('grid.sort', MText::_( 'COM_MIWOEVENTS_PUBLISHED'), 'e.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
            </th>
			<th width="10%">
				<?php echo MHtml::_('grid.sort',  MText::_( 'COM_MIWOEVENTS_ORDER'), 'e.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
                <?php if ($ordering) { ?>
				<?php echo MHtml::_('grid.order',  $this->items , 'filesave.png', 'saveorder' ); ?>
                <?php } ?>
			</th>
            


            <th width="5%">
                <?php echo MHtml::_('grid.sort', 'MGRID_HEADING_LANGUAGE', 'e.language', $this->lists['order_Dir'], $this->lists['order']); ?>
            </th>
			<th width="1%">
				<?php echo MHtml::_('grid.sort',  MText::_( 'COM_MIWOEVENTS_ID'), 'e.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>			
		</tr>
	</thead>
	<?php
		if (@$this->MiwoeventsConfig->activate_recurring_event) {
			$colspan = 13;
		}
		else { 
			$colspan = 12;
		}
	?>
	<tfoot>
		<tr>
			<td colspan="<?php echo $colspan ; ?>">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
    $n = count($this->items);
	for ($i=0; $i < $n; $i++) {
		$row = &$this->items[$i];

		$link = MRoute::_('index.php?option=com_miwoevents&view=events&task=edit&cid[]='.$row->id);

		$checked = MHtml::_('grid.id', $i, $row->id);


        $published = $this->getIcon($i, $task = $row->published == '0' ? 'publish' : 'unpublish', $row->published ? 'publish_y.png' : 'publish_x.png', true);


		?>
		<tr class="<?php echo "row$k"; ?>">



			<td>
				<?php echo $checked; ?>
			</td>	
			<td>
                <?php if (MiwoEvents::get('acl')->canEdit()) { ?>
                <a href="<?php echo $link; ?>">
                    <?php echo $row->title; ?>
                </a>
                <?php } else { ?>
                <?php echo $row->title; ?>
                <?php } ?>
			</td>
			<td>
				<?php echo $row->categories; ?>
			</td>
			<td class="text_center">
				<?php echo MHtml::_('date', $row->event_date, @$this->MiwoeventsConfig->date_format, null); ?>
			</td>
			<td class="text_center">				
				<?php echo $row->event_capacity; ?>											
			</td>									
			<td class="text_center">
				<?php echo (int) $row->attenders; ?>
			</td>
            <?php if (@$this->MiwoeventsConfig->activate_recurring_event) { ?>
            <td style="float: right;">
                <?php
                if ($row->event_type == 0) {
                    echo MText::_('COM_MIWOEVENTS_STANDARD_EVENT');
                }
                elseif($row->event_type == 1) {
                    echo MText::_('COM_MIWOEVENTS_PARENT_EVENT');
                }
                else {
                    echo MText::_('COM_MIWOEVENTS_CHILD_EVENT');
                }
                ?>
            </td>
            <?php }	?>
            <td class="text_center">
                <?php echo $published; ?>
            </td>
			<td class="order">
                <?php if ($ordering) { ?>
				<span><?php echo $this->pagination->orderUpIcon($i, ($row->category_id == @$this->items[$i-1]->category_id), 'orderup', 'Move Up', $ordering ); ?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $n, ($row->category_id == @$this->items[$i+1]->category_id), 'orderdown', 'Move Down', $ordering ); ?></span>
                <?php } ?>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>				
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="text_area input-mini" style="text-align: center" <?php echo $disabled; ?> />
			</td>
            


            <td class="center nowrap">
                <?php if ($row->language == '*') { ?>
                <?php echo MText::alt('MALL', 'language'); ?>
                <?php } else { ?>
                <?php echo isset($this->langs[$row->language]->title) ? $this->escape($this->langs[$row->language]->title) : MText::_('MUNDEFINED'); ?>
                <?php } ?>
            </td>
			<td class="text_center">
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
	<input type="hidden" name="view" value="events" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo MHtml::_( 'form.token' ); ?>
</form>