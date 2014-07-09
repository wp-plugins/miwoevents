<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

$editor = MFactory::getEditor();
?>
<script type="text/javascript">
	Miwi.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Miwi.submitform( pressbutton );
			return;				
		} else {
			<?php echo $editor->save('description'); ?>
			Miwi.submitform( pressbutton );
		}
	}
</script>

<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm">
    <?php if ($this->_mainframe->isSite()) { ?>
    <div style="float: left; width: 99%; margin-left: 10px;">
        <button class="button btn-success" onclick="Miwi.submitbutton('apply')"><span class="icon-apply icon-white"></span> <?php echo MText::_('COM_MIWOEVENTS_SAVE'); ?></button>
        <button class="button" onclick="Miwi.submitbutton('save')"><span class="icon-save"></span> <?php echo MText::_('COM_MIWOEVENTS_SAVE_CLOSE'); ?></button>
        <button class="button" onclick="Miwi.submitbutton('save2new')"><span class="icon-save-new"></span> <?php echo MText::_('COM_MIWOEVENTS_SAVE_NEW'); ?></button>
        <button class="button" onclick="Miwi.submitbutton('cancel')"><span class="icon-cancel"></span> <?php echo MText::_('COM_MIWOEVENTS_CANCEL'); ?></button>
    </div>
    <br/>
    <br/>
    <?php } ?>
	<?php echo MHtml::_('tabs.start', 'miwoevents', array('useCookie' => 1)); ?>
	<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOEVENTS_DETAILS'), 'sl_details'); ?>
		<fieldset class="adminform">
            <table class="admintable">
            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOEVENTS_TITLE'); ?>
                </td>
                <td class="value2">
                    <input class="text_area inputbox required" type="text" name="title" id="title" style="font-size: 1.364em;" size="65" maxlength="250" value="<?php echo $this->item->title;?>" />
                </td>
            </tr>
            
            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOEVENTS_ALIAS'); ?>
                </td>
                <td class="value2">
                    <input class="text_area" type="text" name="alias" id="alias" size="45" maxlength="250" value="<?php echo $this->item->alias;?>" />
                </td>
            </tr>
            
            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOEVENTS_PARENT'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['parent']; ?>
                </td>
            </tr>
            <tr>
                <td class="key2">
                    <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_COLOR'); ?>::<?php echo MText::_('COM_MIWOEVENTS_COLOR_EXPLAIN'); ?>"><?php echo MText::_('COM_MIWOEVENTS_COLOR'); ?></span>
                </td>
                <td class="value2">
                    <input type="text" name="color_code" class="inputbox color {required:false}" value="<?php echo $this->item->color_code; ?>" size="10" />
                </td>
            </tr>
            
            <tr>
                <td class="key2">
                    <?php echo MText::_( 'COM_MIWOEVENTS_DESCRIPTION'); ?>
                </td>
                <td class="value2">
                	<?php 
		            # Description
		            $pageBreak = "<hr id=\"system-readmore\">";
		            
		            $fulltextLen = strlen($this->item->fulltext);
		            
		            if ($fulltextLen > 0){
		            	$content = "{$this->item->introtext}{$pageBreak}{$this->item->fulltext}";
		            } else {
		            	$content = "{$this->item->introtext}";
		            }
		            
                    echo $editor->display( 'description',  $content , '100%', '250', '75', '10') ; ?>
                </td>
            </tr>
        </table>
	</fieldset>

    <!-- Publishing Options -->
    <?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOEVENTS_PUBLISHING_OPTIONS'), 'publishing'); ?>
    <table class="admintable" width="100%">
    <tr>
        <td class="key2">
            <?php echo MText::_('COM_MIWOEVENTS_PUBLISHED'); ?>
        </td>
        <td class="value2">
                      <?php echo $this->lists['published']; ?>
            </td>
        </tr>











    <tr>
        <td class="key2">
            <?php echo MText::_('COM_MIWOEVENTS_LANGUAGE'); ?>
        </td>
        <td class="value2">
            <?php echo $this->lists['language'] ; ?>
        </td>
    </tr>
    </table>

    <!-- Meta Settings -->
    <?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOEVENTS_META_OPTIONS'), 'publishing'); ?>
    <table class="admintable" width="100%">
    <tr>
        <td class="key2">
            <?php echo MText::_('COM_MIWOEVENTS_META_DESC'); ?>
        </td>
        <td class="value2">
            <textarea name="meta_desc" id="meta_desc" cols="40" rows="3" class="" aria-invalid="false"><?php echo $this->item->meta_desc;?></textarea>
        </td>
    </tr>
    <tr>
        <td class="key2">
            <?php echo MText::_('COM_MIWOEVENTS_META_KEYWORDS'); ?>
        </td>
        <td class="value2">
            <textarea name="meta_key" id="meta_key" cols="40" rows="3" class="" aria-invalid="false"><?php echo $this->item->meta_key;?></textarea>
        </td>
    </tr>
    <tr>
        <td class="key2">
            <?php echo MText::_('COM_MIWOEVENTS_META_AUTHOR'); ?>
        </td>
        <td class="value2">
            <input class="text_area" type="text" name="meta_author" id="meta_author" size="40" maxlength="250" value="<?php echo $this->item->meta_author;?>" />
        </td>
    </tr>
    </table>
    <?php echo MHtml::_('tabs.end'); ?>

    <div class="clearfix"></div>

	<input type="hidden" name="option" value="com_miwoevents" />

    <?php if ($this->_mainframe->isSite()) { ?>
    <input type="hidden" name="view" value="category" />
    <input type="hidden" name="Itemid" value="<?php echo MiwoEvents::getInput()->getInt('Itemid', 0); ?>" />
    <?php } else { ?>
    <input type="hidden" name="view" value="categories" />
    <?php } ?>

    <input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
    <?php echo MHtml::_( 'form.token' ); ?>
</form>