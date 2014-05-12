<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

$page_title = $this->params->get('page_title', '');
if (($this->params->get('show_page_heading', '0') == '1') && !empty($page_title)) {} else { $page_title = MText::_('COM_MIWOEVENTS_HISTORY'); } ?>
<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<h1 class="miwoevents_box_h1"><?php echo $page_title; ?></h1>
	</div>
	
	<div class="miwoevents_box_content">
	<!-- content -->
		<form action="<?php echo MRoute::_('index.php?option=com_miwoevents&view=history'.$this->Itemid); ?>" method="post" name="adminForm">
		    <table class="category" width="100%">
		        <tr>
		            <td style="float: right;">
		                <?php echo MText::_('COM_MIWOEVENTS_FILTER'); ?>:
		                <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		                <button class="<?php echo MiwoEvents::getButtonClass(); ?>" onclick="this.form.submit();"><?php echo MText::_('COM_MIWOEVENTS_GO'); ?></button>
		                <button class="<?php echo MiwoEvents::getButtonClass(); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><?php echo MText::_('COM_MIWOEVENTS_RESET'); ?></button>
		            </td >
		            <td style="float: left;">
		                <?php echo $this->lists['filter_event']; ?>
		            </td>
		        </tr>
		    </table>
		    <?php if (count($this->items)) { ?>
				<table class="category table table-striped" style="margin-top: 10px;">
					<thead>
						<tr>
							


											
							<th>
								<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOEVENTS_EVENT'), 'e.title', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							
							<?php
							if ($this->MiwoeventsConfig->show_event_date) { ?>
							<th>
								<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOEVENTS_EVENT_DATE'), 'e.event_date', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<?php } ?>
							
							<th>
								<?php echo MHtml::_('grid.sort', MText::_('COM_MIWOEVENTS_DATE'), 'r.register_date', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
										
							<th style="text-align: center;">
								<?php echo MText::_('COM_MIWOEVENTS_PARTICIPANT'); ?>
							</th>
							
							<th style="text-align: center;">
								<?php echo MText::_('MSTATUS'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$k = 0;
		            $n = count($this->items);
		            
					for ($i = 0; $i < $n; $i++) {
						$row = &$this->items[$i];
                        $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $row->event_id), null, true);

						$link = MRoute::_("index.php?option=com_miwoevents&view=event&event_id={$row->event_id}".$this->Itemid);
						?>
						<tr class="cat-list-row-<?php echo $i % 2; ?>">
							<td>
								<?php echo $this->pagination->getRowOffset($i); ?>
							</td>
							
							<td>
								<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
							</td>
							
							<?php if ($this->MiwoeventsConfig->show_event_date) {?>
	                        <td>
	                            <?php echo MHtml::_('date', $row->event_date, $this->MiwoeventsConfig->date_format, null); ?>
	                        </td>
		                    <?php } ?>
		                    
							<td align="center">
								<?php echo MHtml::_('date', $row->register_date, $this->MiwoeventsConfig->date_format, null); ?>
							</td>
							
							<?php 
							
							# Get field name from config
							$firstnameKey	= $this->MiwoeventsConfig->firstname_field;
							$lastnameKey	= $this->MiwoeventsConfig->lastname_field;
							$emailKey 		= $this->MiwoeventsConfig->email_field;
							
							?>						
							<td align="center" style="font-weight: bold;">
								<?php
								$fields = json_decode($row->fields);
								foreach ($fields as $key => $value){
									if ($key == $firstnameKey)	{ $firstname	= $value; }
									if ($key == $lastnameKey)	{ $lastname 	= $value; }
									if ($key == $emailKey)		{ $email 		= $value; }
								}
								
								echo "$firstname $lastname";
								?>
							</td>												
													
							<td align="center">
								<?php
									switch($row->status) {
				                        case 1:
				                            echo MText::_('COM_MIWOEVENTS_STATUS_PENDING');
				                            break;
				                        case 3:
				                            echo MText::_('COM_MIWOEVENTS_STATUS_PAID');
				                            echo "<br/><a href=\"index.php?option=com_miwoevents&view=registration&task=cancel&id={$row->id}\">Cancel</a>";
				                            break;
				                        case 5:
				                            echo MText::_('COM_MIWOEVENTS_STATUS_CANCELLED');
				                            break;
				                        case 6:
				                            echo MText::_('COM_MIWOEVENTS_STATUS_CANCEL_PENDING');
				                            break;
				                        case 11:
				                            echo MText::_('COM_MIWOEVENTS_STATUS_WAITING');
				                            break;
				                        case 12:
				                            echo MText::_('COM_MIWOEVENTS_STATUS_WAITING_PAID');
				                            echo "<br/><a href=\"index.php?option=com_miwoevents&view=registration&task=cancel&id={$row->id}\">Cancel</a>";
				                            break;
										case 100:
				                            echo MText::_('COM_MIWOEVENTS_STATUS_MISSING');
				                            break;
				                    }
								?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
					?>
					</tbody>
		            <tfoot>
		            <tr>
		                <?php
		                if ($this->pagination->total > $this->pagination->limit) {
		                    $colspan = 5;
		                    if ($this->MiwoeventsConfig->show_event_date) {
		                        $colspan = 6;
		                    }
		                    ?>
		                    <td colspan="<?php echo $colspan; ?>">
		                        <?php echo $this->pagination->getListFooter(); ?>
		                    </td>
		                <?php }  ?>
		            </tr>
		            </tfoot>
				</table>								
			<?php	
			} else {
				echo '<div align="center" class="info">'.MText::_( 'COM_MIWOEVENTS_YOU_HAVENT_REGISTER_FOR_EVENTS').'</div>' ;
			}
		    ?>
		
			<input type="hidden" name="option" value="com_miwoevents" />
			<input type="hidden" name="view" value="history" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />	
			<?php echo MHtml::_('form.token'); ?>
		</form>
	<!-- content // -->
	</div>
	<div class="clr"></div>
</div>