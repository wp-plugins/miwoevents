<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class MiwoeventsFields {

    public function __construct() {
		$this->MiwoeventsConfig = MiwoEvents::getConfig();
	}

    public function getFields($display_in = 1) {
		static $cache = array();

        if (!isset($cache[$display_in])) {
            $cache[$display_in] = MiwoDatabase::loadObjectList("SELECT * FROM `#__miwoevents_fields` WHERE `display_in` = '{$display_in}'");
        }

        return $cache[$display_in];
	}

    public function getField($name) {
		static $cache = array();

        if (!isset($cache[$name])) {
            $cache[$name] = MiwoDatabase::loadObject("SELECT * FROM `#__miwoevents_fields` WHERE `name` = '{$name}'");
        }

        return $cache[$name];
	}

    public function getFieldTitle($name) {
        return $this->getField($name)->title;
	}

    public function getCustomField($name, $default = '', $options = null, $type = 'text', $label = 'miwi_label', $description = 'miwi_description', $name_is_array = false) {
        mimport('framework.form.form');

        $form = MForm::getInstance('custom_fields', MPATH_WP_PLG.'/miwoevents/admin/library/custom_fields.xml', array(), true, 'config');

        if (!empty($options) and !is_array($options)) {
        	$search = array("\r\n","\n\n","\r","\n");
        	
        	for ($i = 0; $i < 4; $i++) {
        		$options = str_replace($search, ";;;", $options);
        	}
        	
        	$options = explode(";;;", $options);
        }
        
        
        $name_suffix = ($name_is_array == true) ? '[]' : '';

        if (($type == 'radio') or ($type == 'list') or ($type == 'multilist')) {
            $b_default = $default;

            if (($type == 'multilist') and strstr($default, '***')) {
                $default = '';
            }

            $xml = "<field name=\"custom_fields[{$name}]{$name_suffix}\" type=\"{$type}\" default=\"{$default}\" label=\"{$label}\" description=\"{$description}\" class=\"nopadding\" >";

            if ($type == 'radio') {
                $xml = str_replace("class=\"nopadding\"", "class=\"btn-group nopadding\"", $xml);
            }

            if ($type == 'multilist') {
                $xml = str_replace("type=\"multilist\"", "type=\"list\" multiple=\"multiple\"", $xml);
            }

			foreach ($options as $option) {
                $xml .= "<option value=\"{$option}\">{$option}</option>";
            }

            $xml .= '</field>';

            $_f = simplexml_load_string($xml);
        }
		elseif ($type == 'checkbox') {
			foreach ($options as $option) {
				if ($option == $default){ $defaultOption = $default; } else  { $defaultOption = NULL; }
				$xml = "<field name=\"custom_fields[{$name}]{$name_suffix}\" type=\"checkbox\" default=\"{$defaultOption}\" label=\"{$label}\" description=\"{$description}\" class=\"nopadding\" />";
				continue;
            }
			$_f = simplexml_load_string($xml);
		}
        else {
            $_f = simplexml_load_string("<field name=\"custom_fields[{$name}]{$name_suffix}\" type=\"$type\" default=\"$default\" label=\"{$label}\" description=\"{$description}\" />");
        }

        @$form->setField($_f);

        if (($type == 'multilist') and strstr($b_default, '***')) {
            $v = explode("***", $b_default);
            $selected = array_combine($v, $v);

            $f_name = 'custom_fields['.$name.']'.$name_suffix.'';

            $data = new stdClass();
            $data->$f_name = $selected;

            $form->bind(array('field' => $data));
        }

        $field = $form->getField("custom_fields[{$name}]{$name_suffix}");

        return $field;
    }

	public function getRegistrationFields($clear = null, $name_is_array = false) {
		# General Settings
		$app	= MFactory::getApplication();
		$db		= MFactory::getDBO();
		$user	= MFactory::getUser();
		$userId = $user->get('id');

		$sql = "
                SELECT f.id, f.name, f.title, f.description, f.field_type, f.values, f.default_values, f.rows, f.cols, f.size, f.css_class
                FROM #__miwoevents_fields f
                WHERE
                    f.display_in = 1 AND
                    f.published = 1
                ORDER BY f.ordering
                ";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		if ($clear == "yes") {
			return $rows;
		}
        else {
			if (empty($rows)){ return; }

			foreach ($rows as $row){
					if($row->name=="miwi_password"){
					$row->field_type="password";
					}
				$x[] = $this->getCustomField($row->name, $row->default_values, $row->values, $row->field_type, $row->title, $row->description, $name_is_array);
			}

			return $x;
		}
    }

    public function getEventFields($eventId, $clear = NULL) {
		# General Settings
		$app	= MFactory::getApplication();
		$db		= MFactory::getDBO();
		$user	= MFactory::getUser();
		$userId = $user->get('id');

        $sql = "SELECT fields FROM #__miwoevents_events WHERE id = $eventId";
        $db->setQuery($sql);
		$eventFields = $db->loadResult();

		if (empty($eventFields)) {
            return null;
        }

        $eventFields = json_decode($eventFields);

        if (!empty($eventFields)){
	        foreach ($eventFields as $key => $eventField){
	            $sql = "
	                SELECT
	                    f.ordering, f.name, f.title, f.description, f.field_type, f.values, f.default_values, f.rows, f.cols, f.size, f.css_class
	                FROM #__miwoevents_fields f
	                WHERE f.display_in = 2 AND f.name = '$key'
	                ORDER BY f.ordering
	                ";
	            $db->setQuery($sql);
	            $obj = $db->loadObject();
	            $obj->field_value = $eventField;
	            $rows[] = $obj;
	        }

	        # sorting Array From ordering
	        asort($rows);
        }

        if ($clear == "yes") {
            if(empty($rows)){ return; } else { return $rows;}
        }
        else {
            if(empty($rows)){ return; }

            foreach ($rows as $row){
                $x[] = $this->getCustomField($row->name, $row->field_value, $row->values, $row->field_type, $row->title, $row->description);
            }

            return $x;
        }
    }

    public function createAutoFieldHtml($field_id){
        $db	= MFactory::getDBO();

        $sql = "
        SELECT f.id, f.name, f.title, f.description, f.field_type, f.values, f.default_values, f.rows, f.cols, f.size, f.css_class
        FROM #__miwoevents_fields f
        WHERE id = {$field_id}";

        $db->setQuery($sql);
        $row = $db->loadObject();

        $_html = $this->getCustomField($row->name, $row->default_values, $row->values, $row->field_type, $row->title, $row->description);

        $trID = "{$_html->id}tr";

        $html = '<tr id="'.$trID.'">
                    <td class="key2" style="vertical-align: middle;">
                        <img style="vertical-align: middle;" src="'.MURL_MIWOEVENTS.'/admin/assets/images/delete.png" onclick="removeField(\''.$trID.'\');">&nbsp;'.$_html->label.'</td>
                    <td class="value2" style="vertical-align: middle;">
                        '.$_html->input.'
            		</td>
                </tr>';

        return $html;
    }
}