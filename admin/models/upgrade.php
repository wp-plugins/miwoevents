<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Imports
mimport('framework.installer.installer');
mimport('framework.installer.helper');
mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');

# Model Class
class MiwoeventsModelUpgrade extends MiwoeventsModel {

	# Main constructer
	public function __construct() {
        parent::__construct('upgrade');
    }
    
	# Upgrade
    public function upgrade() {
        $utility = MiwoEvents::get('utility');

		# Get package
		$type = MRequest::getCmd('type');
		if ($type == 'upload') {
			$userfile = MRequest::getVar('install_package', null, 'files', 'array');
			$package = $utility->getPackageFromUpload($userfile);
		}
        else if ($type == 'server') {
			$package = $utility->getPackageFromServer('index.php?option=com_mijoextensions&view=download&model=miwoevents&pid='.$utility->getConfig()->pid);
		}

		# Was the package unpacked?
		if (!$package or empty($package['dir'])) {
			$this->setState('message', 'Unable to find install package.');
			return false;
		}

        
        $event_file = $package['dir'].'/miwoevents.zip';
        $shop_file = $package['dir'].'/miwoshop.zip';
        $shop_vqmod_file = $package['dir'].'/miwoshop_x_miwoevents.xml';

		# MiwoEvents Plugin
		if (MFile::exists($event_file)) {
			$p1 = $utility->unpack($event_file);
			MFolder::copy($p1['dir'], MPath::clean(MPATH_WP_CNT.'/plugins/miwoevents'), null, true);

			# Miwi Framework
			$miwi = MPATH_WP_CNT.'/plugins/miwoevents/miwi.zip';

			if (MFile::exists($miwi)) {
				MArchive::extract($miwi, MPATH_WP_CNT);
				MFile::delete($miwi);
			}
			
			# MiwoShop Plugin
			if (MFile::exists($shop_file)) {
				$p2 = $utility->unpack($shop_file);
				MFolder::copy($p2['dir'], MPath::clean(MPATH_WP_CNT.'/plugins/miwoshop'), null, true);
			}

			if (MFile::exists($shop_vqmod_file)) {
				MFile::copy($shop_vqmod_file, MPATH_WP_CNT.'/plugins/miwoshop/site/opencart/vqmod/xml/miwoshop_x_miwoevents.xml');
			}
		}
		
		MFolder::delete($package['dir']);
		
		# MiwoEvents Script
	    $script_file = MPATH_WP_CNT.'/plugins/miwoevents/script.php';
	    if (MFile::exists($script_file)) {
		    require_once($script_file);

		    $installer_class = 'com_MiwoeventsInstallerScript';

		    $installer = new $installer_class();

		    if (method_exists($installer, 'preflight')) {
			    $installer->preflight(null, null);
		    }

		    if (method_exists($installer, 'postflight')) {
			    $installer->postflight(null, null);
		    }
	    }
		
		# MiwoShop Script
	    $script_file = MPATH_WP_CNT.'/plugins/miwoshop/script.php';
	    if (MFile::exists($script_file)) {
		    require_once($script_file);

		    $installer_class = 'com_MiwoshopInstallerScript';

		    $installer = new $installer_class();

		    if (method_exists($installer, 'preflight')) {
			    $installer->preflight(null, null);
		    }

		    if (method_exists($installer, 'postflight')) {
			    $installer->postflight(null, null);
		    }
	    }
		







































		return true;
    }
}