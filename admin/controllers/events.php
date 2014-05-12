<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;


class MiwoEventsControllerEvents extends MiwoEventsController {

	public function __construct($config = array())	{
		parent::__construct('events');
	}

	public function save() {
		$post = MRequest::get('post', MREQUEST_ALLOWRAW);
		$cid = $post['cid'];
		$post['id'] = (int) $cid[0];

		$ret = $this->_model->store($post);
		if ($ret) {
			$msg = MText::_('COM_MIWOEVENTS_EVENT_SAVED');
		}
        else {
			$msg = MText::_('COM_MIWOEVENTS_EVENT_SAVE_ERROR');
		}

		parent::route($msg, $post);
	}

    public function copy() {
        # Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $cid = MRequest::getVar('cid', array(), 'post');
        
        foreach ($cid as $id) {
        	$this->_model->copy($id);
        }
        
        $msg = MText::_('COM_MIWOEVENTS_RECORD_COPIED');

        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
    }
    
	public function delete() {
    	# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		$cid = MRequest::getVar('cid', array(), 'post');
        //MArrayHelper::toInteger($cid);
        //$id = $cid[0];
		
		foreach ($cid as $id) {
			# Action
			if ($this->_model->deleteEvent($id) === true) {
				$msg = MText::_('COM_MIWOEVENTS_COMMON_RECORDS_DELETED');
			} else {
				$msg = MText::_('COM_MIWOEVENTS_COMMON_RECORDS_DELETED_NOT');
			}
        }
		
        $this->setRedirect('index.php?option='.$this->_option.'&view='.$this->_context, $msg);
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
}