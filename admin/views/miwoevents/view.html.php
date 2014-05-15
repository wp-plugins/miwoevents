<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die( 'Restricted access' );

class MiwoeventsViewMiwoevents extends MiwoeventsView {

	function display($tpl = null) {
		MToolBarHelper::title(MText::_('COM_MIWOEVENTS_COMMON_PANEL'),'miwoevents');

		if (MFactory::getUser()->authorise('core.admin', 'com_miwoevents')) {
			MToolBarHelper::preferences('com_miwoevents');
			MToolBarHelper::divider();
		}
		
		$this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/control-panel?tmpl=component', 650, 500);

        $this->info = $this->get('Info');
		$this->stats = $this->get('Stats');
		
		parent::display($tpl);
	}
	
	function quickIconButton($link, $image, $text, $modal = 0, $x = 500, $y = 450, $new_window = false) {
		// Initialise variables
		$lang = MFactory::getLanguage();
		
		$new_window	= ($new_window) ? ' target="_blank"' : '';
  		?>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<?php
				if ($modal) {
					MHtml::_('behavior.modal');
					
					if (!strpos($link, 'tmpl=component')) {
						$link .= '&amp;tmpl=component';
					}
				?>
					<a href="<?php echo $link; ?>" style="cursor:pointer" class="modal" rel="{handler: 'iframe', size: {x: <?php echo $x; ?>, y: <?php echo $y; ?>}}"<?php echo $new_window; ?>>
				<?php
				} else {
				?>
					<a href="<?php echo $link; ?>"<?php echo $new_window; ?>>
				<?php
				}
                ?>
                    <img src="<?php echo MURL_MIWOEVENTS; ?>/admin/assets/images/<?php echo $image; ?>" alt="<?php echo $text; ?>" />
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
}