<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Imports
mimport('framework.filesystem.file');

class MiwoeventsModelRestoreMigrate extends MiwoeventsModel {

	public function __construct() {
		parent::__construct('restoremigrate');
	}

    public function backup() {}

    public function restore() {}

    public function migrate() {}

    public function _getBackupVars() {}

    public function _getRestorePregLine($line) {}

    public function _getUploadedFile () {}
}