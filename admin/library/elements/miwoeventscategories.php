<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.form.formfield');

class MFormFieldMiwoeventsCategories extends MFormField {

	protected $type = 'MiwoeventsCategories';

    public function getInput() {
		$db = MFactory::getDBO();			
		$db->setQuery("SELECT id, parent, parent AS parent_id, title FROM #__miwoevents_categories WHERE published = 1");
		$rows = $db->loadObjectList();

        if (isset($this->element['multiple']) and ($this->element['multiple'] == 'multiple')) {
            return $this->getInputMultiple($rows);
        }
		
		$children = array();
		if ($rows) {
			// first pass - collect children
			foreach ($rows as $v) {
				$pt 	= $v->parent;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		
		$list = MHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		
		$options = array();
		$options[] = MHtml::_('select.option', '0', MText::_('Top'));
		foreach ($list as $item) {
			$options[] = MHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename);
		}
		
		return MHtml::_('select.genericlist', $options, $this->name, array(
			'option.text.toHtml' => false ,
			'option.value' => 'value', 
			'option.text' => 'text', 
			'list.attr' => ' class="inputbox" ',
			'list.select' => $this->value    		        		
		));					    		
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
