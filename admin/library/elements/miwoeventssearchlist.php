<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.form.formfield');

require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

class MFormFieldMiwoeventsSearchlist extends MFormField {

	protected $type = 'MiwoeventsSearchlist';
	
    public function getInput() {
        $html = '';

        $config = MiwoEvents::getConfig();

		$db = MFactory::getDBO();
		$db->setQuery("SELECT * FROM #__miwoevents_fields WHERE display_in = 1 AND published = 1 ORDER BY ordering");
		$rows = $db->loadObjectList();

        if (empty($rows)) {
            return $html;
        }
        
		$fieldName = $this->name; // mform[individual_fields]
		$fieldName = str_replace ("mform[", "", $fieldName);
		$fieldName = str_replace ("]", "", $fieldName);
		
		if (!isset($config->$fieldName)) {
			$config->$fieldName = new stdClass();
		}
        
        if (!isset($config->$fieldName->search)) {
            $config->$fieldName->search = new stdClass();
        }

        if (!isset($config->$fieldName->list)) {
            $config->$fieldName->list = new stdClass();
        }
		
		foreach ($rows as $row) {
			$_name = $row->name;

            if (!isset($config->$fieldName->search->$_name)) {
                $config->$fieldName->search->$_name = 0;
            }

            if (!isset($config->$fieldName->list->$_name)) {
                $config->$fieldName->list->$_name = 0;
            }
			
			$labelID 	= "mform_{$row->name}-lbl";
			$labelFor 	= "mform_{$row->name}";
			$labelDesc	= $row->description;
			$labelTitle = $row->title;
			
            $names[] = "mform[{$fieldName}][search][{$_name}]";
            $names[] = "mform[{$fieldName}][list][{$_name}]";
			
			$values[] = $config->$fieldName->search->$_name;
			$values[] = $config->$fieldName->list->$_name;

            if (MiwoEvents::is30() or MFactory::isW()) {
                $html .= '<div class="control-group" style="margin-left: -220px !important; display: table;">';
                $html .= '<div class="control-label">';
                $html .= "<label id='$labelID' for='$labelFor' class='hasTip' title='{$labelDesc}'>{$labelTitle}</label>";
                $html .= '</div>';
                $html .= '<div class="controls">';
                $html .= $this->getCheckboxList($names, $values);
                $html .= '</div>';
                $html .= '</div>';
            }
            else {
                $html .= '<li>';
                $html .= "<label id='$labelID' for='$labelFor' class='hasTip' title='{$labelDesc}'>{$labelTitle}</label>";
                $html .= $this->getCheckboxList($names, $values);
                $html .= '</li>';
            }
			
			# Empty the trash
            $names = $values = NULL;
		}

		return $html;
	}
	
	protected function getCheckboxList($names, $values) {
        $html = '';

        $c = count($names);
		for ($i = 0; $i < $c; $i++) {
            $name = $names[$i];
			$value = $values[$i];

			if ($value == 1) { $checked = "checked=\"checked\""; } else { $checked = NULL; }

            $_name = MText::_('COM_MIWOEVENTS_ENABLE_SEARCHING');
            if ($i == 1) {
                $_name = MText::_('COM_MIWOEVENTS_SHOW_IN_LIST');
            }

            $html .= '<div style="float: left; margin-right: 20px; vertical-align: middle;">'.
                        '<span style="float: left; margin: 3px 0px 0px 4px;">'.
                            $_name.
                        '</span>'.
                        '<span style="float: left; margin-left: 4px;"">'.
                            '<input type="checkbox" name="' . $name . '" value="1" ' . $checked . ' />'.
                        '</span>'.
                     '</div>';
		}
		
		return $html;
   }
}