<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsViewAttenders extends MiwoeventsView {
	
	public function display($tpl = null) {
        if (!MiwoEvents::get('acl')->canAccessAttenders()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwoevents', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->addToolbar();
		
		
























































		if (MiwoEvents::get('acl')->canManage()) {
			$options[] = MHtml::_('select.option', 'exportCSV', MTEXT::_('COM_cpanel_miwoevents_EXPORT'));
		}
			

		if (MiwoEvents::get('acl')->canDelete()) {
			$options[] = MHtml::_('select.option', 'delete', MText::_('MTOOLBAR_DELETE'));
		}

		$lists['bulk_actions'] = MHtml::_('select.genericlist', $options, 'bulk_actions', ' class="inputbox"', 'value', 'text', '');
			
		
		$this->lists 		= $lists;
		$this->pagination 	= $this->get('Pagination');
			
		parent::display($tpl);				
	}

    protected function addToolbar() {
        $acl = MiwoEvents::get('acl');

        MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_ATTENDERS'), 'miwoevents');
		
        





















        $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/attenders?tmpl=component', 650, 500);
    }
}