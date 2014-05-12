<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

require_once(dirname(__FILE__).'/initialise.php');

abstract class MiwoEvents {

    public static function &get($class, $options = null) {
        static $instances = array();
		
		if (!isset($instances[$class])) {			
			require_once(MPATH_MIWOEVENTS_LIB.'/'.$class.'.php');
			
			$class_name = 'Miwoevents'.ucfirst($class);
			
			$instances[$class] = new $class_name($options);
		}

		return $instances[$class];
    }

    public static function is30() {
        return self::get('utility')->is30();
   	}

    public static function getConfig() {
        return self::get('utility')->getConfig();
    }

	public static function getTable($name) {
        return self::get('utility')->getTable($name);
	}

    public static function getInput() {
        return MFactory::getApplication()->input;
    }

    public static function getButtonClass() {
        return self::get('utility')->getConfig()->button_class;
    }
}