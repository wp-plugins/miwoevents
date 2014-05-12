<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsControllerCategories extends MiwoEventsController {

	public function __construct($config = array()) {
		parent::__construct('categories');
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
}