<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$col = 2;

if ($this->MiwoeventsConfig->show_location_in_category_view) {
//Load greybox lib
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
$width = 600;
$height = 350;
}
MHtml::_('behavior.modal');

		if ($this->MiwoeventsConfig->show_cat_decription_in_calendar_layout) {
		if($this->category->title != '') {
		?>
		<div class="miwoevents_box">
			<div class="miwoevents_box_heading">
				<h1 class="miwoevents_box_h1"><?php echo $this->category->title; ?></h1>
			</div>
			
			<div class="miwoevents_box_content">
			<?php echo $this->category->description;?>
			</div>
			<div class="clr"></div>
		</div>
		<?php }
		} else {
			
?>

<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<?php if ($this->MiwoeventsConfig->show_cat_decription_in_calendar_layout) { ?>
		<h1 class="miwoevents_box_h1"><?php echo $this->category->title; ?></h1>
		<?php } else { ?>
		<h1 class="miwoevents_title"><?php echo MText::_('COM_MIWOEVENTS_EVENT_LIST'); ?></h1>
		<?php } ?>
	</div>
	
	<div class="miwoevents_box_content">
	<!-- content -->
	<?php if ($this->MiwoeventsConfig->show_cat_decription_in_calendar_layout) { ?>
	<div class="miwi_description"><?php echo $this->category->introtext.$this->category->fulltext; ?></div>
	<?php } else { ?>
	<p class="miwoevents_message"><?php echo MText::_('COM_MIWOEVENTS_EVENT_GUIDE'); ?></p>
	<?php } ?>
	
	<form method="post" name="adminForm" id="adminForm" action="index.php">
		<table class="item_list table table-striped table-bordered table-condensed">
			<thead>
			<tr>			
			<?php
			if ($this->MiwoeventsConfig->show_image_in_table_layout) { ?>
				<th class="sectiontableheader">
					<?php echo MText::_('COM_MIWOEVENTS_EVENT_IMAGE'); ?>
				</th>
			<?php	
				$col++;
			}
			?>
				<th class="sectiontableheader">
					<?php echo MText::_('COM_MIWOEVENTS_EVENT_TITLE'); ?>
				</th>							
				<th class="sectiontableheader date_col">
					<?php echo MText::_('COM_MIWOEVENTS_EVENT_DATE'); ?>
				</th>
			<?php
			if ($this->MiwoeventsConfig->show_location_in_category_view) {
				$col++ ; 
			?>
				<th class="sectiontableheader location_col">
					<?php echo MText::_('COM_MIWOEVENTS_LOCATION'); ?>
				</th>
			<?php	
			}
			if ($this->MiwoeventsConfig->show_price_in_table_layout) {
			?>
				<th class="sectiontableheader table_price_col">
					<?php echo MText::_('COM_MIWOEVENTS_INDIVIDUAL_PRICE'); ?>
				</th>
			<?php    
			    $col++;
			}
			if ($this->MiwoeventsConfig->show_capacity) {
				$col++ ;
			?>
				<th class="sectiontableheader capacity_col">
					<?php echo MText::_('COM_MIWOEVENTS_CAPACITY'); ?>
				</th>	
			<?php	
			}
			?>








			</tr>
			</thead>
			<tbody>
			<?php			
				$total = 0;
				$k = 0;
                $n = count($this->items);

				for ($i = 0; $i < $n; $i++) {
					$item = $this->items[$i];
					
                    $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event', 'event_id' => $item->id), null, true);

				    







		        	$k = 1 - $k;
				?>
					<tr>
						<?php 
							if ($this->MiwoeventsConfig->show_image_in_table_layout) {
							?>
								<td class="miwoevents_image_column">
									<?php
										if ($item->thumb) {
										?>
											<a href="<?php echo MURL_MEDIA.'/miwoevents/images/'.$item->thumb; ?>" class="modal"><img src="<?php echo MURL_MEDIA.'/miwoevents/images/thumbs/'.$item->thumb; ?>" class="miwoevents_thumb-left" height="<?php echo $this->MiwoeventsConfig->thumb_width?>" width="<?php echo $this->MiwoeventsConfig->thumb_height?>"/></a>
										<?php	
										} else {
											echo ' ';
										}	
									?>	
								</td>			
							<?php	
							}
						?>
						<td>
							<a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=event&event_id='.$item->id.$this->Itemid) ?>" class="miwoevents_event_link"><?php echo $item->title ; ?></a>
						</td>					
						<td>	
							<?php
	                           echo MHtml::_('date', $item->event_date, $this->MiwoeventsConfig->event_date_format, null);
							?>
						</td>
						<?php
							if ($this->MiwoeventsConfig->show_location_in_category_view) {
							?>
								<td>
									<?php
										if ($item->location_id) {
                                            $this->Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'location', 'layout' => 'map', 'location_id' => $item->location_id), null, true);
										?>
											<a href="<?php echo MRoute::_('index.php?option=com_miwoevents&view=location&layout=map&location_id='.$item->location_id.$this->Itemid.'&tmpl=component'); ?>" rel="gb_page_center[<?php echo $width; ?>, <?php echo $height; ?>]" title="<?php echo $item->location_name ; ?>" class="location_link"><?php echo $item->location_name ; ?></a>
										<?php
										} else {
										?>
											&nbsp;
										<?php
										}
									?>
								</td>
							<?php
							}

				            if ($this->MiwoeventsConfig->show_price_in_table_layout) {
							    if ($this->MiwoeventsConfig->show_discounted_price) {
							        $price = $item->discounted_price;
                                }
                                else {
							        $price = $item->individual_price;
                                }
							?>
								<td>
									<?php echo MiwoEvents::get('utility')->getAmount($price, $item->currency_symbol); ?>
								</td>
							<?php
							}
							if ($this->MiwoeventsConfig->show_capacity) {
							?>
								<td style="text-align: center;">
									<?php
										if ($item->event_capacity){
											echo $item->event_capacity ;
                                        }
                                        else {
											echo MText::_('COM_MIWOEVENTS_UNLIMITED') ;
                                        }
									?>
								</td>
							<?php	
							}
							?>









					</tr>
					<?php
						$k = 1 - $k ;





















































				}
				
			?>
			</tbody>
			<?php
			if ($this->pagination->total > $this->pagination->limit) { 
			?>
			<tfoot>
				<tr>
					<td colspan="<?php echo $col ; ?>"><div align="center" class="pagination"><?php echo $this->pagination->getListFooter(); ?></div></td>
				</tr>
			</tfoot>
			<?php } ?>
		</table>
	
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />	
		<input type="hidden" name="option" value="com_miwoevents" />
		<input type="hidden" name="view" value="category" />
		<input type="hidden" name="layout" value="table" />	
		<input type="hidden" name="category_id" value="<?php echo $this->category->id; ?>" />	
	</form>
	<!-- content // -->
	</div>
	<div class="clr"></div>
</div>

<?php } ?>
			