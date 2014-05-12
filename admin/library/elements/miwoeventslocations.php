<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.form.formfield');

class MFormFieldMiwoeventsLocations extends MFormField {

	protected $type = 'MiwoeventsLocations';
	
	public function getInput() {
		$db = MFactory::getDBO();
		$db->setQuery("SELECT id, title FROM #__miwoevents_locations WHERE published = 1 ORDER BY title");
		$rows = $db->loadObjectList();

        if (isset($this->element['multiple']) and ($this->element['multiple'] == 'multiple')) {
            return $this->getInputMultiple($rows);
        }
		
		$options = array();
		$options[] = MHtml::_('select.option', '0', MText::_('Select Location'), 'id', 'title');
		$options = array_merge($options, $rows);
		
		return MHtml::_('select.genericlist', $options, $this->name, ' class="inputbox" ', 'id', 'title', $this->value);
	}

    public function getInputMultiple($options) {
        $html = array();
        $attr = '';

        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }

        $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';
        $attr .= $this->required ? ' required="required" aria-required="true"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

        // Get the field options.
        //$options = (array) $this->getOptions();

        // Create a read-only list (no name) with a hidden input to store the value.
        if ((string) $this->element['readonly'] == 'true') {
            $html[] = MHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
            $html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
        }
        // Create a regular list.
        else {
            $html[] = MHtml::_('select.genericlist', $options, $this->name, trim($attr), 'id', 'title', $this->value, $this->id);
        }

        return implode($html);
    }
}
