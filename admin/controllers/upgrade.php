<?php
/*
* @package		MiwoEvents
* @copyright	2009-2014 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
# No Permission
defined('MIWI') or die ('Restricted access');

# Controller Class
class MiwoeventsControllerUpgrade extends MiwoeventsController {

	# Main constructer
	public function __construct() {
		parent::__construct('upgrade');
	}
	
	# Upgrade
    public function upgrade() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');
		
		# Upgrade
		if ($this->_model->upgrade()) {
            $msg = MText::_('COM_MIWOEVENTS_UPGRADE_SUCCESS');
        }
        else {
            $msg = '';
        }
		
		# Return
		$this->setRedirect('index.php?option=com_miwoevents&view=upgrade', $msg);
    }
}