<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Mijosoft LLC, mijosoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die;

class MiwoeventsControllerConfig extends MiwoEventsController {

	public function __construct($config = array()) {
        parent::__construct('config');
	}

    // Save changes
    function save() {
        // Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->save();

        $this->setRedirect('index.php?page=miwoevents', MText::_('COM_MIWOEVENTS_CONFIG_SAVED'));
    }

    // Apply changes
    function apply() {
        // Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->_model->save();

        $this->setRedirect('index.php?page=miwoevents&view=config', MText::_('COM_MIWOEVENTS_CONFIG_SAVED'));
    }

    // Cancel saving changes
    function cancel() {
        // Check token
        MRequest::checkToken() or mexit('Invalid Token');

        $this->setRedirect('index.php?page=miwoevents', MText::_('COM_MIWOEVENTS_CONFIG_NOT_SAVED'));

    }
}