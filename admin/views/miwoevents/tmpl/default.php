<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');
?>

<script language="javascript" type="text/javascript">
	function upgrade() {	    
	    document.adminForm.view.value = 'upgrade';
		document.adminForm.submit();
	}
</script>

<form name="adminForm" id="adminForm" action="<?php echo MRoute::_('admin.php?page=miwoevents&amp;option=com_miwoevents'); ?>" method="post">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>










			<td valign="top" width="58%">
				<table>
					<tr>
						<td>
							<div id="cpanel_miwoevents" width="30%">
							<?php
							$option = MRequest::getWord('option');

                            if (MiwoEvents::get('utility')->is30() or MFactory::isW()) {
                                $uri = (string) MUri::getInstance();
                                $return = urlencode(base64_encode($uri));

                                $link = 'admin.php?page=miwoevents&option=com_miwoevents&view=config';
                                $this->quickIconButton($link, 'icon-48-miwoevents-config.png', MText::_('COM_MIWOEVENTS_CPANEL_CONFIGURATION'));
                            }
                            else {
                                $link = 'admin.php?page=miwoevents&option=com_miwoevents&view=config';
                                $this->quickIconButton($link, 'icon-48-miwoevents-config.png', MText::_('COM_MIWOEVENTS_CPANEL_CONFIGURATION'), true, 875, 550);
                            }

							$link = 'admin.php?page=miwoevents&amp;option='.$option.'&amp;view=fields';
							$this->quickIconButton($link, 'icon-48-miwoevents-fields.png', MText::_('COM_MIWOEVENTS_CPANEL_FIELDS'));
							
							$link = 'admin.php?page=miwoevents&amp;option='.$option.'&view=restoremigrate';
							$this->quickIconButton($link, 'icon-48-miwoevents-restore.png', MText::_('COM_MIWOEVENTS_CPANEL_RESTORE'));
							
							$link = 'admin.php?page=miwoevents&amp;option='.$option.'&amp;view=upgrade';
							$this->quickIconButton($link, 'icon-48-miwoevents-upgrade.png', MText::_('COM_MIWOEVENTS_CPANEL_UPGRADE'));
							?>

                            <br /><br /><br /><br /><br /><br /><br /><?php if (!MiwoEvents::is30() and !MFactory::isW()) { ?><br /><br /><?php } ?>
							
							<?php
							$link = 'admin.php?page=miwoevents&amp;option='.$option.'&amp;view=categories';
							$this->quickIconButton($link, 'icon-48-miwoevents-categories.png', MText::_('COM_MIWOEVENTS_CPANEL_CATEGORIES'));

                            $link = 'admin.php?page=miwoevents&amp;option='.$option.'&amp;view=locations';
                         	$this->quickIconButton($link, 'icon-48-miwoevents-locations.png', MText::_('COM_MIWOEVENTS_CPANEL_LOCATIONS'));
							
							$link = 'admin.php?page=miwoevents&amp;option='.$option.'&amp;view=events';
							$this->quickIconButton($link, 'icon-48-miwoevents-events.png', MText::_('COM_MIWOEVENTS_CPANEL_EVENTS'));
							
							$link = 'admin.php?page=miwoevents&amp;option='.$option.'&amp;view=attenders';
							$this->quickIconButton($link, 'icon-48-miwoevents-attenders.png', MText::_('COM_MIWOEVENTS_CPANEL_ATTENDERS'));
							?>

                            <br /><br /><br /><br /><br /><br /><br /><?php if (!MiwoEvents::is30() and !MFactory::isW()) { ?><br /><br /><?php } ?>
							
							<?php
							$link = 'admin.php?page=miwoevents&amp;option='.$option.'&amp;view=support&amp;task=support';
							$this->quickIconButton($link, 'icon-48-miwoevents-support.png', MText::_('COM_MIWOEVENTS_CPANEL_SUPPORT'), true, 650, 420);
							
							$link = 'admin.php?page=miwoevents&amp;option='.$option.'&amp;view=support&amp;task=translators';
							$this->quickIconButton($link, 'icon-48-miwoevents-translators.png', MText::_('COM_MIWOEVENTS_CPANEL_TRANSLATORS'), true);
							
							$link = 'http://www.miwisoft.com/wordpress-plugins/miwoevents/changelog?tmpl=component';
							$this->quickIconButton($link, 'icon-48-miwoevents-changelog.png', MText::_('COM_MIWOEVENTS_CPANEL_CHANGELOG'), true);
							
							$link = 'http://miwisoft.com';
							$this->quickIconButton($link, 'icon-48-miwisoft.png', 'Miwisoft.com', false, 0, 0, true);
							
							?>
							</div>
						</td>
					</tr>
				</table>
			</td>
		
			<td valign="top" width="42%" style="padding: 7px 0 0 5px">
                <?php echo MHtml::_('sliders.start', 'miwoevents'); ?>
                <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_CPANEL_WELLCOME'), 'welcome'); ?>
				<table class="adminlist">
					<tr>
						<td valign="top" width="50%" align="center">
							<table class="wp-list-table widefat">
								<?php
									$rowspan = 5;
									if (empty($this->info['pid'])) {
										$rowspan = 6;
									}
								?>
								<tr height="70">
									<td width="%25">
										<?php
                                            $template = 'bluestork';
                                            if (MiwoEvents::is30()) {
                                                $template = 'hathor';
                                            }

											if ($this->info['version_enabled'] == 0) {
												echo MHtml::_('image', 'administrator/templates/'.$template.'/images/header/icon-48-info.png', null);
											} elseif ($this->info['version_status'] == 0) {
												echo MHtml::_('image', 'administrator/templates/'.$template.'/images/header/icon-48-checkin.png', null);
											} elseif($this->info['version_status'] == -1) {
												echo MHtml::_('image', 'administrator/templates/'.$template.'/images/header/icon-48-help_header.png', null);
											} else {
												echo MHtml::_('image', 'administrator/templates/'.$template.'/images/header/icon-48-help_header.png', null);
											}
										?>
									</td>
									<td width="%35">
										<?php
											if ($this->info['version_enabled'] == 0) {
												echo '<b>'.MText::_('COM_MIWOEVENTS_CPANEL_VERSION_CHECKER_DISABLED_1').'</b>';
											} elseif ($this->info['version_status'] == 0) {
												echo '<b><font color="green">'.MText::_('COM_MIWOEVENTS_CPANEL_LATEST_VERSION_INSTALLED').'</font></b>';
											} elseif($this->info['version_status'] == -1) {
												echo '<b><font color="red">'.MText::_('COM_MIWOEVENTS_CPANEL_OLD_VERSION').'</font></b>';
											} else {
												echo '<b><font color="orange">'.MText::_('COM_MIWOEVENTS_CPANEL_NEWER_VERSION').'</font></b>';
											}
										?>
									</td>
									<td align="center" style="vertical-align: middle;" rowspan="<?php echo $rowspan; ?>">
										<a href="http://www.miwisoft.com/wordpress-plugins/miwoevents" target="_blank">
										<img src="<?php echo MURL_MIWOEVENTS; ?>/site/assets/images/logo.png" width="140" height="140" style="display: block; margin: auto;" alt="MiwoEvents" title="MiwoEvents" align="middle" border="0">
										</a>
									</td>
								</tr>
								












									<td>
										<?php
											if($this->info['version_status'] == 0 || $this->info['version_enabled'] == 0) {
												echo MText::_('COM_MIWOEVENTS_CPANEL_LATEST_VERSION');
											} elseif($this->info['version_status'] == -1) {
												echo '<b><font color="red">'.MText::_('COM_MIWOEVENTS_CPANEL_LATEST_VERSION').'</font></b>';
											} else {
												echo '<b><font color="orange">'.MText::_('COM_MIWOEVENTS_CPANEL_LATEST_VERSION').'</font></b>';
											}
										?>
									</td>
									<td>
										<?php
											if ($this->info['version_enabled'] == 0) {
												echo MText::_('COM_MIWOEVENTS_CPANEL_VERSION_CHECKER_DISABLED_2');
											} elseif($this->info['version_status'] == 0) {
												echo $this->info['version_latest'];
											} elseif($this->info['version_status'] == -1) {
												// Version output in red
												echo '<b><font color="red">'.$this->info['version_latest'].'</font></b>&nbsp;&nbsp;&nbsp;&nbsp;';
												?>
												<input type="button" class="button btn-danger" class="button hasTip" value="<?php echo MText::_('COM_MIWOEVENTS_CPANEL_UPGRADE'); ?>" onclick="upgrade();" />
												<?php
											} else {
												echo '<b><font color="orange">'.$this->info['version_latest'].'</font></b>';
											}
										?>
									</td>
								</tr>
								<tr height="40">
									<td>
										<?php echo MText::_('COM_MIWOEVENTS_CPANEL_INSTALLED_VERSION'); ?>
									</td>
									<td>
										<?php 
											if ($this->info['version_enabled'] == 0) {
												echo MText::_('COM_MIWOEVENTS_CPANEL_VERSION_CHECKER_DISABLED_2');
											} else {
												echo $this->info['version_installed'];
											}
										?>
									</td>
								</tr>
								<tr height="40">
									<td>
										<?php echo MText::_('COM_MIWOEVENTS_CPANEL_COPYRIGHT'); ?>
									</td>
									<td>
										<a href="http://www.miwisoft.com" target="_blank"><?php echo MiwoEvents::get('utility')->getXmlText(MPATH_WP_PLG.'/miwoevents/miwoevents.xml', 'copyright'); ?></a>
									</td>
								</tr>
							</table>
						</td>		
					</tr>
				</table>
                <?php echo MHtml::_('sliders.panel', MText::_('COM_MIWOEVENTS_CPANEL_STATISTICS'), 'stats'); ?>
				<table class="wp-list-table widefat">
					<tr>
						<td width="25%">
							<?php echo MText::_('COM_MIWOEVENTS_CPANEL_CATEGORIES'); ?>
						</td>
						<td width="75%">
							<b><?php echo $this->stats['categories']; ?></b>
						</td>
					</tr>
					<tr>
						<td width="25%">	
							<?php echo MText::_('COM_MIWOEVENTS_CPANEL_EVENTS'); ?>
						</td>
						<td width="75%">
							<b><?php echo $this->stats['events'];?></b>
						</td>
					</tr>
					<tr>
						<td width="25%">	
							<?php echo MText::_('COM_MIWOEVENTS_CPANEL_ATTENDERS'); ?>
						</td>
						<td width="75%">
							<b><?php echo $this->stats['attenders'];?></b>
						</td>
					</tr>
				</table>
                <?php echo MHtml::_('sliders.end'); ?>
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="option" value="com_miwoevents" />
	<input type="hidden" name="view" value="miwoevents"/>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo MHtml::_('form.token'); ?>
</form>