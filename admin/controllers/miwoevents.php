<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die( 'Restricted access' );

class MiwoeventsControllerMiwoevents extends MiwoeventsController {

	# Main constructer
    public function __construct() {
        parent::__construct('miwoevents');
    }
	
	public function savePersonalID() {
		# Check token
		MRequest::checkToken() or mexit('Invalid Token');

		$msg = $this->_model->savePersonalID();
        
        $this->setRedirect('index.php?option=com_miwoevents', $msg);
    }
}