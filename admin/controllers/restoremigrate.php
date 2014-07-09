<?php
/*
* @package		MiwoEvents
* @copyright	2009-2014 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
# No Permission
defined('MIWI') or die ('Restricted access');

class MiwoeventsControllerRestoreMigrate extends MiwoeventsController {

    public function __construct() {
        parent::__construct('restoremigrate');
    }

    public function backup() {
		MRequest::checkToken() or mexit('Invalid Token');

		if(!$this->_model->backup()){
			MError::raiseWarning(500, MText::_('COM_MIWOEVENTS_RESTOREMIGRATE_MSG_BACKUP_NO'));
		}
    }

    public function restore() {
		MRequest::checkToken() or mexit('Invalid Token');

		if(!$this->_model->restore()){
			$msg = MText::_('COM_MIWOEVENTS_RESTOREMIGRATE_MSG_RESTORE_NO');
		} else {
			$msg = MText::_('COM_MIWOEVENTS_RESTOREMIGRATE_MSG_RESTORE_OK');
		}

		parent::route($msg);
    }

    public function migrate() {
        MRequest::checkToken() or mexit('Invalid Token');

        if(!$this->_model->migrate()){
            $msg = MText::_('COM_MIWOEVENTS_RESTOREMIGRATE_MSG_RESTORE_NO');
        } else {
            $msg = MText::_('COM_MIWOEVENTS_RESTOREMIGRATE_MSG_RESTORE_OK');
        }

        parent::route($msg);
    }
}