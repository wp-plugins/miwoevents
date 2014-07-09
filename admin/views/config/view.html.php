<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die;

class MiwoeventsViewConfig extends MiwoeventsView {
	
	public function display($tpl = null) {
        $form = MForm::getInstance('config', MPATH_WP_CNT.'/plugins/miwoevents/admin/config.xml', array(), false, '/config');
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