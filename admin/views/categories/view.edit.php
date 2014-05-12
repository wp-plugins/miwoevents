<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsViewCategories extends MiwoeventsView {

	public function display($tpl = null) {
        if (!MiwoEvents::get('acl')->canEdit()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwoevents', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->document->addScript(MUri::root().'administrator/components/com_miwoevents/assets/js/colorpicker/jscolor.js');

        $task = MRequest::getCmd('task');
        $text = ($task == 'edit') ? MText::_('COM_MIWOEVENTS_EDIT') : MText::_('COM_MIWOEVENTS_NEW');


        if ($this->_mainframe->isAdmin()) {
            MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CATEGORY').': <small><small>[ ' . $text.' ]</small></small>', 'miwoevents');
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::save2new();
            MToolBarHelper::cancel();
            MToolBarHelper::divider();
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/miwoevents/user-manual/categories?tmpl=component', 650, 500);
        }

        MHtml::_('behavior.tooltip');
        
		$item = $this->get('EditData');			
		
		$options = array() ;
		$options[] = MHtml::_('select.option', '', MText::_('Default Layout')) ;
		$options[] = MHtml::_('select.option', 'table', MText::_('Table Layout')) ;
		$options[] = MHtml::_('select.option', 'calendar', MText::_('Calendar Layout')) ;				

		$lists['parent'] = MiwoEvents::get('utility')->buildParentCategoryDropdown($item);
		$lists['published'] = MiwoEvents::get('utility')->getRadioList('published', $item->published);
		
		$lists['language'] = MHtml::_('select.genericlist', MHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox"', 'value', 'text', $item->language);
		
		$this->item = $item;
		$this->lists = $lists;
		
		parent::display($tpl);				
	}
}