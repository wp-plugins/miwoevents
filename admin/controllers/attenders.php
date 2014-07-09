<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsControllerAttenders extends MiwoEventsController {
	
	public function __construct($config = array()) {
		parent::__construct('attenders');
	}

    public function save() {
   		$post = MRequest::get('post', MREQUEST_ALLOWRAW);
   		$cid = $post['cid'];
   		$post['id'] = (int) $cid[0];

        $ret = $this->_model->store($post);
   		if ($ret) {
   			$msg = MText::_('COM_MIWOEVENTS_REGISTRANT_SAVED');
   		}
           else {
   			$msg = MText::_('COM_MIWOEVENTS_REGISTRANT_SAVE_ERROR');
   		}

   		parent::route($msg, $post);
   	}
   	
   	public function exportCSV() {
   		if (!$this->_model->exportCSV()) {
            parent::route('No records.');
        }
   	}
}