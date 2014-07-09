<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

?>
<script type="text/javascript">
    Miwi.submitbutton = function(pressbutton) {
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            Miwi.submitform( pressbutton );
            return;
        } else {
            //Should validate the information here
            if (form.name.value == "") {
                alert("<?php echo MText::_('COM_MIWOEVENTS_ENTER_FIELD_NAME'); ?>");
                form.name.focus();
                return ;
            }
            if (form.title.value == "") {
                alert("<?php echo MText::_("COM_MIWOEVENTS_ENTER_FIELD_TITLE"); ?>");
                form.title.focus();
                return ;
            }
            if (form.field_type.value == -1) {
                alert("<?php echo MText::_("COM_MIWOEVENTS_CHOOSE_FIELD_TYPE") ; ?>");
                return ;
            }
            Miwi.submitform( pressbutton );
        }
    }

    function checkFieldName() {
        var form = document.adminForm;
        var name = form.name.value;
        var oldValue = name;

        name = name.replace('miwi_','');
        name = name.replace(/[^a-zA-Z0-9_]*/ig, '');
        form.name.value='miwi_' + name.toLowerCase();
    }

    












































</script>

<form action="<?php echo MRoute::getActiveUrl(); ?>" method="post" name="adminForm" id="adminForm">

	<?php echo MHtml::_('tabs.start', 'miwoevents', array('useCookie' => 1)); ?>
	<?php echo MHtml::_('tabs.panel', MText::_('COM_MIWOEVENTS_DETAILS'), 'sl_details'); ?>
    <fieldset class="adminform">
        <table class="admintable">
            <tr>
                <td class="key2">
                    <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_NAME'); ?>::<?php echo MText::_('COM_MIWOEVENTS_FIELD_NAME_REQUIREMENT'); ?>"><?php echo MText::_('COM_MIWOEVENTS_NAME'); ?></span>
                </td>
                <td class="value2">
                    <?php $disabled = empty($this->item->id) ? '' : 'readonly="readonly"';?>
                    <input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->item->name;?>" onchange="checkFieldName();" <?php echo $disabled;?> />
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo  MText::_('COM_MIWOEVENTS_TITLE'); ?>
                </td>
                <td class="value2">
                    <input class="text_area" type="text" name="title" id="title" size="50" maxlength="250" onchange="titleRestrict();" value="<?php echo $this->item->title;?>" />
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOEVENTS_FIELD_TYPE'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['field_type']; ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo MText::_('COM_MIWOEVENTS_DISPLAY_IN'); ?>
                </td>
                <td class="value2">
                    <?php echo $this->lists['display_in']; ?>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <?php echo  MText::_('COM_MIWOEVENTS_DESCRIPTION'); ?>
                </td>
                <td class="value2">
                    <textarea rows="5" cols="50" name="description"><?php echo $this->item->description;?></textarea>
                </td>
            </tr>

            <tr>
                <td class="key2">
                    <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_VALUES'); ?>::<?php echo MText::_('COM_MIWOEVENTS_EACH_ITEM_LINE'); ?>"><?php echo MText::_('COM_MIWOEVENTS_VALUES'); ?></span>
                </td>
                <td class="value2">
                    <textarea rows="5" cols="50" name="values" onfocus="tara(this);" onblur="tara(this);"><?php if( empty($this->item->values)) { echo MText::_('COM_MIWOEVENTS_VALUES_VALUES'); } else {echo $this->item->values; } ?></textarea>

                    





            </tr>
            <tr>
                <td class="key2">
                    <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_DEFAULT_VALUES'); ?>::<?php echo MText::_('COM_MIWOEVENTS_EACH_ITEM_LINE'); ?>"><?php echo MText::_('COM_MIWOEVENTS_DEFAULT_VALUES'); ?></span>
                </td>
                <td class="value2">
                    <textarea rows="5" cols="50" name="default_values"><?php echo $this->item->default_values; ?></textarea>
                </td>
            </tr>

            <?php
            if (@$this->MiwoeventsConfig->cb_integration) {
                ?>
                <tr>
                    <td class="key2">
                        <span class="editlinktip hasTip" title="<?php echo MText::_('COM_MIWOEVENTS_FIELD_MAPPING'); ?>::<?php echo MText::_('COM_MIWOEVENTS_FIELD_MAPPING_EXPLAIN'); ?>"><?php echo MText::_('COM_MIWOEVENTS_FIELD_MAPPING'); ?></span>
                    </td>
                    <td class="value2">
                        <?php echo $this->lists['field_mapping']; ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="key2">
                    <?php echo  MText::_( 'COM_MIWOEVENTS_ROWS'); ?>
                </td>
                <td class="value2">
                    <input class="text_area" type="text" name="rows" id="rows" size="10" maxlength="250" value="<?php echo $this->item->rows;?>" />
                </td>
            </tr>
            <tr>
                <td class="key2">
                    <?php echo  MText::_( 'COM_MIWOEVENTS_COLS'); ?>
                </td>
                <td class="value2">
                    <input class="text_area" type="text" name="cols" id="cols" size="10" maxlength="250" value="<?php echo $this->item->cols;?>" />
                </td>
            </tr>
            <tr>
                <td class="key2">
                    <?php echo  MText::_( 'COM_MIWOEVENTS_SIZE'); ?>
                </td>
                <td class="value2">
                    <input class="text_area" type="text" name="size" id="size" size="10" maxlength="250" value="<?php echo $this->item->size;?>" />
                </td>
            </tr>
            <tr>
                <td class="key2">
                    <?php echo  MText::_('COM_MIWOEVENTS_CSS_CLASS'); ?>
                </td>
                <td class="value2">
                    <input class="text_area" type="text" name="css_class" id="css_class" size="10" maxlength="250" value="<?php echo $this->item->css_class;?>" />
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
        <!-- <tr>
            <td class="key2">
                <?php echo MText::_('COM_MIWOEVENTS_ACCESS_LEVEL'); ?>
            </td>
            <td class="value2">
                <?php echo $this->lists['access']; ?>
            </td>
        </tr> -->
        <tr>
            <td class="key2">
                <?php echo MText::_('COM_MIWOEVENTS_LANGUAGE'); ?>
            </td>
            <td class="value2">
                <?php echo $this->lists['language'] ; ?>
            </td>
        </tr>
    </table>
    <?php echo MHtml::_('tabs.end'); ?>

    <div class="clearfix"></div>

    <input type="hidden" name="option" value="com_miwoevents" />
    <input type="hidden" name="view" value="fields" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
    <?php echo MHtml::_( 'form.token' ); ?>
</form>


