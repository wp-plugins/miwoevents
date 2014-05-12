<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

class MiwoeventsModelMiwoevents extends MiwoeventsModel {

	public function __construct(){
		parent::__construct('miwoevents');
	}
	
    public function savePersonalID() {
		$pid = trim(MRequest::getVar('pid', '', 'post', 'string'));
		
		if (!empty($pid)) {
			$MiwoeventsConfig = MiwoEvents::getConfig();
			$MiwoeventsConfig->pid = $pid;
			
			MiwoEvents::get('utility')->storeConfig($MiwoeventsConfig);
		}
	}
	
	# Check info
    public function getInfo() {
		static $info;
		
		if (!isset($info)) {
			$info = array();
			
			if (@$this->MiwoeventsConfig->version_checker == 1){
				$utility = MiwoEvents::get('utility');
				$info['version_installed'] = $utility->getMiwoeventsVersion();
				$info['version_latest'] = $utility->getLatestMiwoeventsVersion();

				# Set the version status
				$info['version_status'] = version_compare($info['version_installed'], $info['version_latest']);
				$info['version_enabled'] = 1;
			}
			else {
				$info['version_status'] = 0;
				$info['version_enabled'] = 0;
			}
			
			$info['pid'] = @$this->MiwoeventsConfig->pid;
		}
		
		return $info;
	}
	
    public function getStats() {
		$count= array();
		
		$count['categories'] = MiwoDatabase::loadResult("SELECT COUNT(*) FROM #__miwoevents_categories");
		$count['events'] = MiwoDatabase::loadResult("SELECT COUNT(*) FROM #__miwoevents_events");
		$count['attenders'] = MiwoDatabase::loadResult("SELECT COUNT(*) FROM #__miwoevents_attenders");
		
		return $count;
	}
}