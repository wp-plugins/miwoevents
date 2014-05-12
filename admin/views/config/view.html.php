<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Mijosoft LLC, mijosoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die;

class MiwoeventsViewConfig extends MiwoeventsView {
	
	public function display($tpl = null) {
        $form = MForm::getInstance('config', ABSPATH.'/wp-content/plugins/miwoevents/admin/config.xml', array(), false, '/config');
        $params = MiwoEvents::getConfig();
        $form->bind($params);

        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('Configuration').':' , 'miwoevents' );
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::cancel();
        }

        $this->form = $form;
			
		parent::display($tpl);				
	}

}