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
   			$msg = MText::_('COM_MIWOEVENTS_EVENT_SAVED');
   		}
           else {
   			$msg = MText::_('COM_MIWOEVENTS_EVENT_SAVE_ERROR');
   		}

   		parent::routeFront($msg, $post);
   	}

    public function autoComplete(){
        $query = MRequest::getVar('query');
        $events = json_encode($this->_model->autoComplete($query));
        echo $events;
        exit();
    }

    public function createAutoFieldHtml(){
        $fieldid = MRequest::getInt('fieldid');
        $html = MiwoEvents::get('fields')->createAutoFieldHtml($fieldid);
        echo $html;
        exit();
    }

    public function cancel() {
        parent::setRedirect('index.php?option='.$this->_option.'&view=category');
    }

    public function updateStatus() {
        if (!MiwoEvents::get('acl')->canEditState()) {
            return;
        }

        $status = MiwoEvents::getInput()->getInt('st', 0);

        if ($this->_model->updateStatus($status)) {
            $msg = MText::_('COM_MIWOEVENTS_EVENT_UPDATED');
        }
        else {
            $msg = MText::_('COM_MIWOEVENTS_EVENT_UPDATED_ERROR');
        }

        parent::setRedirect('index.php?option='.$this->_option.'&view=category', $msg);
    }
	
	# Event Calendar Export
    public function ecalex() {
    	$Itemid = MRequest::getInt('Itemid');
		$eventid = MRequest::getInt('event_id');
		$this->_model->ecalex($eventid);
    }
}