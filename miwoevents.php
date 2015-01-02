<?php
/*
Plugin Name: MiwoEvents
Plugin URI: http://miwisoft.com
Description: MiwoEvents offers you an All-in-One platform to create Events (free or paid), Custom Fields (event or registration), Locations (with map) and allows your visitors to Register (individual or group) with and easy to use interface.
Author: Miwisoft LLC
Version: 1.2.2
Author URI: http://miwisoft.com
*/

defined('ABSPATH') or die('MIWI');

if (!class_exists('MWordpress')) {
    require_once(dirname(__FILE__) . '/wordpress.php');
}

final class MEvents extends MWordpress {

    public function __construct() {
		if (!defined('MURL_MIWOEVENTS')) {
		    define('MURL_MIWOEVENTS', plugins_url('', __FILE__));
	    }

		if (!defined('MIWOEVENTS_PACK')) {
		    define('MIWOEVENTS_PACK', 'lite');
	    }
		
		parent::__construct('miwoevents', '35.0032');
    }
}

$mevents = new MEvents();

register_activation_hook(__FILE__, array($mevents, 'activate'));
register_deactivation_hook(__FILE__, array($mevents, 'deactivate'));