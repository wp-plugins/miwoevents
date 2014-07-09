<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsControllerLocation extends MiwoEventsController {
	
	public function __construct($config = array()) {
		parent::__construct('location', 'locations');
	}

    public function save() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

   		$post = MRequest::get('post', MREQUEST_ALLOWRAW);
   		$cid = $post['cid'];
   		$post['id'] = (int) $cid[0];

        $_lang = MFactory::getLanguage();
        $_lang->load('com_miwoevents', MPATH_ADMINISTRATOR, 'en-GB', true);
        $_lang->load('com_miwoevents', MPATH_ADMINISTRATOR, $_lang->getDefault(), true);
        $_lang->load('com_miwoevents', MPATH_ADMINISTRATOR, null, true);

   		if ($this->_model->store($post)) {
   			$msg = MText::_('COM_MIWOEVENTS_LOCATION_SAVED');
   		}
           else {
   			$msg = MText::_('COM_MIWOEVENTS_LOCATION_SAVE_ERROR');
   		}

   		parent::routeFront($msg, $post);
   	}

    public function cancel() {
        parent::setRedirect('index.php?option='.$this->_option.'&view=category');
    }
}