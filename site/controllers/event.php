<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsControllerEvent extends MiwoEventsController {
	
	public function __construct($config = array()) {
		parent::__construct('event', 'events');
	}

    public function save() {}

    public function autoComplete(){}

    public function createAutoFieldHtml(){}

    public function cancel() {}

    public function updateStatus() {}
	
	# Event Calendar Export
    public function ecalex() {
    	$Itemid = MRequest::getInt('Itemid');
		$eventid = MRequest::getInt('event_id');
		$this->_model->ecalex($eventid);
    }
}