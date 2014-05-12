<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$page_title = $this->params->get('page_title', '');
if (($this->params->get('show_page_heading', '0') == '1') && !empty($page_title)) { $page_title; } ?>

<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<h1 class="miwoevents_box_h1"><?php echo $page_title; ?></h1>
	</div>
	
	<div class="miwoevents_box_content">
	<!-- content -->
		<form method="post" name="adminForm" id="adminForm" action="<?php echo MRoute::_('index.php?option=com_miwoevents&view=locations'.$this->Itemid); ?>">
		    <table class="category" width="100%">
		        <tr>
		            <td style="float: right;">
		                <?php echo MText::_('COM_MIWOEVENTS_FILTER'); ?>:
		                <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		                <button onclick="this.form.submit();"><?php echo MText::_('COM_MIWOEVENTS_GO'); ?></button>
		                <button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo MText::_('COM_MIWOEVENTS_RESET'); ?></button>
		            </td >
		        </tr>
		    </table>
		    <?php if (count($this->items)) { ?>
		    <table class="category table table-striped" style="margin-top: 10px;">
		        <thead>
		            <tr>
		                <th>
		                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOEVENTS_NAME'), 'title', $this->lists['order_Dir'], $this->lists['order']); ?>
		                </th>
		                <th>
		                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOEVENTS_ADDRESS'), 'address', $this->lists['order_Dir'], $this->lists['order']); ?>
		                </th>
		                <th>
		                    <?php echo MHtml::_('grid.sort', MText::_('COM_MIWOEVENTS_COORDINATES'), 'coordinates', $this->lists['order_Dir'], $this->lists['order']); ?>
		                </th>
		            </tr>
		        </thead>
		        <tbody>
		            <?php
		            $k = 0;
		            $n = count($this->items);
		            for ($i = 0; $i < $n; $i++) {
		                $item = &$this->items[$i];
                        $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'location', 'location_id' => $item->id), null, true);

		                $url = MRoute::_('index.php?option=com_miwoevents&view=location&location_id='.$item->id.$this->Itemid);
		                ?>
		                <tr class="cat-list-row-<?php echo $i % 2; ?>">
		                    <td>
		                        <a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
		                            <?php echo $item->title; ?>
		                        </a>
		                    </td>
		                    <td>
		                        <?php echo $item->address; ?>
		                    </td>
		                    <td>
		                        <?php echo $item->coordinates; ?>
		                    </td>
		                </tr>
		                <?php
		                $k = 1 - $k;
		            }
		            if (count($this->items) == 0) {
		            ?>
		                <tr>
		                    <td colspan="4" style="text-align: center;">
		                        <div class="info"><?php echo MText::_('COM_MIWOEVENTS_NO_LOCATION_RECORDS');?></div>
		                    </td>
		                </tr>
		            <?php
		            }
		            ?>
		        </tbody>
		        <?php
		        if ($this->pagination->total > $this->pagination->limit) {
		        ?>
		        <tfoot>
		            <tr>
		                <td colspan="5">
		                    <div class="pagination">
		                        <?php echo $this->pagination->getListFooter(); ?>
		                    </div>
		                </td>
		            </tr>
		        </tfoot>
		        <?php
		        }
		        ?>
		    </table>
		    <?php } ?>
		
		    <input type="hidden" name="option" value="com_miwoevents" />
		    <input type="hidden" name="view" value="locations" />
		    <input type="hidden" name="task" value="" />
		    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		    <?php echo MHtml::_('form.token'); ?>
		</form>
	<!-- content // -->
	</div>
	<div class="clr"></div>
</div>