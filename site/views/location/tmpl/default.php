<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

?>
<?php
$page_title = $this->params->get('page_title', '');
if(empty($page_title) and !empty($this->page_title)){$page_title=$this->page_title;}
if (($this->params->get('show_page_heading', '0') == '1') && !empty($page_title)) {} else { $page_title == NULL; } ?>


<div class="miwoevents_box">
	<div class="miwoevents_box_heading">
		<h1 class="miwoevents_box_h1"><?php echo $page_title; ?></h1>
	</div>
	
	<div class="miwoevents_box_content">
	<!-- content -->
		<strong><?php echo MText::_('Address'); ?></strong>: <?php echo $this->item->address; ?>
		<br/>
		<?php echo $this->item->description; ?>
		<br/>
        <?php
        if(!empty($this->fields)) {
            foreach ($this->fields as $field) {
                ?>
                <span class="miwoevents_box_content_40"><?php echo $field->title; ?></span>
                <span class="miwoevents_box_content_60">&nbsp;:&nbsp;<?php echo str_replace('***', ', ', $field->field_value); ?></span>
            <?php
            }
        }
        ?>
		<br/>
		<?php $this->setLayout('map'); echo $this->loadTemplate(); ?>
	<!-- content // -->
	</div>
	<div class="clr"></div>
</div>