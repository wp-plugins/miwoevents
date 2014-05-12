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
    	MError::raiseWarning(500, MText::_('COM_MIWOEVENTS_RESTOREMIGRATE_MSG_BACKUP_NO'));
    }

    public function restore() {}

    public function migrate() {}
}