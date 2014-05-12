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

class MFormFieldMiwoeventsFields extends MFormField {

	protected $type = 'MiwoeventsFields';
	
	function getInput() {
        MFactory::getDocument()->addStyleSheet(MURL_MIWOEVENTS.'/admin/assets/css/config.css');

        $config = MiwoEvents::getConfig();

		$rows = MiwoDatabase::loadObjectList("SELECT * FROM #__miwoevents_fields WHERE display_in = 1 AND published = 1 ORDER BY ordering");
		
		$fieldName = $this->name; // mform[individual_fields]
		$fieldName = str_replace ("mform[", "", $fieldName);
		$fieldName = str_replace ("]", "", $fieldName);

        if (!isset($config->individual_fields)) {
            $config->individual_fields = new stdClass();
        }

        if (!isset($config->group_fields)) {
            $config->group_fields = new stdClass();
        }
		
		$html = '';

		foreach ($rows as $row) {
			$_name = $row->name;

            if (!isset($config->individual_fields->$_name)) {
                $config->individual_fields->$_name = 0;
            }

            if (!isset($config->group_fields->$_name)) {
                $config->group_fields->$_name = 0;
            }
			
			$labelID 	= "mform_{$row->name}-lbl";
			$labelFor 	= "mform_{$row->name}";
			$labelDesc	= $row->description;
			$labelTitle = $row->title;
			
			$radioName	= "mform[{$fieldName}][{$_name}]";
			$fieldsetID = "mform{$fieldName}_{$_name}";

            if (MiwoEvents::is30() or MFactory::isW()) {
                $radioID = "mform_{$fieldName}_{$_name}";

                $html .= '<div class="control-group" style="margin-left: -220px !important; display: table;">';
                $html .= '<div class="control-label">';
                $html .= "<label id='$labelID' for='$labelFor' class='hasTip' title='{$labelDesc}'>{$labelTitle}</label>";
                $html .= '</div>';
                $html .= '<div class="controls">';
                $html .= $this->_getRadioList($radioName, $config->$fieldName->$_name, "", "", $fieldsetID, $radioID);
                $html .= '</div>';
                $html .= '</div>';
            }
            else {
                $html .= '<li>';
                $html .= "<label id='$labelID' for='$labelFor' class='hasTip' title='{$labelDesc}'>{$labelTitle}</label>";
                $html .= $this->_getRadioList($radioName, $config->$fieldName->$_name, "", "", $fieldsetID);
                $html .= '</li>';
            }
		}

		return $html;
	}

    public function getLabel() {
        return '';
    }
	
	public function _getRadioList($name, $selected, $attrs = '', $id = false, $fieldsetID, $radioID = null) {
        if (empty($attrs)) {
            $attrs = 'class="inputbox" size="2"';
        }

    	$arr = array(
    			MHtml::_('select.option', 0, MText::_('MHIDE')),
            	MHtml::_('select.option', 1, MText::_('MSHOW')),
            	MHtml::_('select.option', 2, MText::_('COM_MIWOEVENTS_REQUIRED'))
            );

        /*if (MiwoEvents::is30() or MFactory::isW()) {
            $radio = $this->_getRadioHtml($arr, $name, $attrs, 'value', 'text', (int) $selected, $id);
        }
        else {
            $radio = MHtml::_('select.radiolist', $arr, $name, $attrs, 'value', 'text', (int) $selected, $id);
            $radio = str_replace(array('<div class="controls">', '</div>'), '', $radio);
        }*/

        $html = "<fieldset id='{$fieldsetID}' class='radio btn-group'>";
        $html .= $this->_getRadioHtml($arr, $name, $attrs, 'value', 'text', (int) $selected, $radioID);
        $html .= '</fieldset>';

        return $html;
    }

    private function _getRadioHtml($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false) {
        reset($data);

        if (is_array($attribs)) {
            $attribs = MArrayHelper::toString($attribs);
        }

        $id_text = $idtag ? $idtag : $name;

        $html = '';

        foreach ($data as $obj) {
            $k = $obj->$optKey;

            $t = $translate ? MText::_($obj->$optText) : $obj->$optText;

            $id = (isset($obj->id) ? $obj->id : null);

            $class = 'radio';

            $extra = '';
            $extra .= $id ? ' id="' . $obj->id . '"' : '';

            if ((string) $k == (string) $selected) {
                if (MiwoEvents::is30() or MFactory::isW()) {
                    $class = 'btn';

                    if ($k == 0) {
                        $class .= ' btn-danger';
                    }
                    else if ($k == 1) {
                        $class .= ' btn-success';
                    }
                    else {
                        $class .= ' button-primary';
                    }
                }

                $extra .= ' checked="checked"';
            }

            $html .= "\n\t" . '<input type="radio" name="' . $name . '" id="' . $id_text . $k . '" value="' . $k . '" ' . $extra . ' '. $attribs . '>'."\n\t\t";
            $html .= '<label for="' . $id_text . $k . '" id="' . $id_text . $k . '-lbl" class="'.$class.'">' . $t . '</label>'."\t\t\t\t";
        }

        $html .= "\n";

        return $html;
    }
}