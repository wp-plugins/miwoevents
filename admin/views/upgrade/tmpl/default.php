<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

if (MiwoEvents::get('utility')->is30()) {
?>

<script type="text/javascript">
	Miwi.submitbutton = function(pressbutton) {
		var form = document.getElementById('upgradeFromUpload');

		if (form.install_package.value == ""){
			alert("<?php echo MText::_('No file selected', true); ?>");
		}
        else {
			form.submit();
		}
	}
</script>

<fieldset class="adminform">
	<legend><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_VERSION_INFO'); ?></legend>
	<table class="adminform">
		<tr>
			<th>
				<?php echo MText::_('COM_MIWOEVENTS_INSTALLED_VERSION'); ?> : <?php echo MiwoEvents::get('utility')->getMiwoeventsVersion();?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo MText::_('COM_MIWOEVENTS_LATEST_VERSION'); ?> : <?php echo MiwoEvents::get('utility')->getLatestMiwoeventsVersion();?>
			</th>
		</tr>
	</table>
</fieldset>
<br/><br/>
<div id="installer-install">
  <ul class="nav nav-tabs">
      <li class="active"><a href="#automatic" data-toggle="tab"><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_FROM_SERVER'); ?></a></li>
      <li><a href="#manual" data-toggle="tab"><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_FROM_FILE'); ?></a></li>
  </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="automatic">
            <form enctype="multipart/form-data" action="index.php" method="post" name="upgradeFromServer" id="upgradeFromServer" class="form-horizontal">
                <fieldset class="uploadform">
                    <legend><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_FROM_SERVER'); ?></legend>
                    <?php
                    $pid = MiwoEvents::get('utility')->getConfig()->pid;
					if (empty($pid)) {
						echo '<b><font color="red">'.MText::_('COM_MIWOEVENTS_UPGRADE_PERSONAL_ID_3').'</font></b>';
					} else {
				    ?>
                    <div class="form-actions">
                        <input class="button button-primary" type="button" value="<?php echo MText::_('COM_MIWOEVENTS_UPGRADE_FROM_SERVER_BTN'); ?>" onclick="form.submit()" />
                    </div>
                    <?php } ?>
                </fieldset>

                <input type="hidden" name="option" value="com_miwoevents" />
                <input type="hidden" name="view" value="upgrade" />
                <input type="hidden" name="task" value="upgrade" />
                <input type="hidden" name="type" value="server" />
                <?php echo MHtml::_('form.token'); ?>
            </form>
        </div>
        <div class="tab-pane" id="manual">
            <form enctype="multipart/form-data" action="index.php" method="post" name="upgradeFromUpload" id="upgradeFromUpload" class="form-horizontal">
                <fieldset class="uploadform">
                    <legend><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_FROM_FILE'); ?></legend>
                    <div class="control-group">
                        <label for="install_package" class="control-label"><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_PACKAGE'); ?></label>
                        <div class="controls">
                            <input class="input_box" id="install_package" name="install_package" type="file" size="57" />
                        </div>
                    </div>
                    <div class="form-actions">
                        <input class="button button-primary" type="button" value="<?php echo MText::_('COM_MIWOEVENTS_UPGRADE_UPLOAD_UPGRADE'); ?>" onclick="Miwi.submitbutton()" />
                    </div>
                </fieldset>

                <input type="hidden" name="option" value="com_miwoevents" />
                <input type="hidden" name="view" value="upgrade" />
                <input type="hidden" name="task" value="upgrade" />
                <input type="hidden" name="type" value="upload" />
                <?php echo MHtml::_('form.token'); ?>
            </form>
        </div>
    </div>
</div>

<?php } else { ?>

<fieldset class="adminform">
	<legend><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_VERSION_INFO'); ?></legend>
	<table class="adminform">
		<tr>
			<th>
				<?php echo MText::_('COM_MIWOEVENTS_INSTALLED_VERSION'); ?> : <?php echo MiwoEvents::get('utility')->getMiwoeventsVersion();?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo MText::_('COM_MIWOEVENTS_LATEST_VERSION'); ?> : <?php echo MiwoEvents::get('utility')->getLatestMiwoeventsVersion();?>
			</th>
		</tr>
	</table>
</fieldset>

<table class="noshow">
	<tr>
		<td width="50%">
			<fieldset class="adminform">
				<legend><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_FROM_SERVER'); ?></legend>
				<div class="miwi_paid">
					<strong><?php echo MText::sprintf('MLIB_X_PRO_MEMBERS', 'This feature'); ?></strong><br /><br />
					<?php echo MText::sprintf('MLIB_PRO_MEMBERS_DESC', 'http://miwisoft.com/wordpress-plugins/miwoevents-manage-book-events#pricing', 'MiwoEvents'); ?>
				</div>
			</fieldset>
		</td>

		<td width="%50">
			<fieldset class="adminform">
				<legend><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_FROM_FILE'); ?></legend>
				<form enctype="multipart/form-data" action="index.php" method="post" name="upgradeFromUpload">
					<table class="adminform">
						<tr>
							<th colspan="2"><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_PACKAGE'); ?></th>
						</tr>
						<tr>
							<td width="100">
								<label for="install_package"><?php echo MText::_('COM_MIWOEVENTS_UPGRADE_SELECT_FILE'); ?>:</label>
							</td>
							<td>
								<input class="input_box" id="install_package" name="install_package" type="file" size="40" />
								<input class="button" type="submit" value="<?php echo MText::_('COM_MIWOEVENTS_UPGRADE_UPLOAD_UPGRADE'); ?>" />
							</td>
						</tr>
					</table>
					<input type="hidden" name="option" value="com_miwoevents" />
					<input type="hidden" name="view" value="upgrade" />
					<input type="hidden" name="task" value="upgrade" />
					<input type="hidden" name="type" value="upload" />
					<?php echo MHtml::_('form.token'); ?>
				</form>
			</fieldset>
		</td>
	</tr>
</table>
<?php } ?>