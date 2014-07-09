<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsViewFields extends MiwoeventsView {

    public function display($tpl = null) {
        if (!MiwoEvents::get('acl')->canEdit()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwoevents', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $db	= MFactory::getDBO();

        $item = $this->get('EditData');

		$task = MiwoEvents::getInput()->getCmd('task', '');

		$text = ($task == 'edit') ? MText::_('COM_MIWOEVENTS_EDIT') : MText::_('COM_MIWOEVENTS_NEW');

		MToolBarHelper::title(MText::_('Field').': <small><small>[ ' . $text.' ]</small></small>' , 'miwoevents' );
		MToolBarHelper::apply();
		MToolBarHelper::save();
        MToolBarHelper::save2new();
		MToolBarHelper::cancel();
		MToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/fields?tmpl=component', 650, 500);
		
		$options = array();
		$options[] = MHtml::_('select.option', '', 						MText::_('COM_MIWOEVENTS_SEL_FIELD_TYPE'));
		$options[] = MHtml::_('select.option', 'text', 					MText::_('COM_MIWOEVENTS_FIELDS_TEXT'));
		$options[] = MHtml::_('select.option', 'textarea', 				MText::_('COM_MIWOEVENTS_FIELDS_TEXTAREA'));
        $options[] = MHtml::_('select.option', 'radio',	 				MText::_('COM_MIWOEVENTS_FIELDS_RADIO'));
		$options[] = MHtml::_('select.option', 'list', 					MText::_('COM_MIWOEVENTS_FIELDS_LIST'));
		$options[] = MHtml::_('select.option', 'multilist', 			MText::_('COM_MIWOEVENTS_FIELDS_MULTILIST'));
		$options[] = MHtml::_('select.option', 'checkbox', 				MText::_('COM_MIWOEVENTS_FIELDS_CHECKBOX'));
		$options[] = MHtml::_('select.option', 'calendar', 				MText::_('COM_MIWOEVENTS_FIELDS_CALENDAR'));
        $options[] = MHtml::_('select.option', 'miwoeventscountries',	MText::_('COM_MIWOEVENTS_FIELDS_MIWOEVENTSCOUNTRIES'));
		$options[] = MHtml::_('select.option', 'email', 				MText::_('COM_MIWOEVENTS_FIELDS_EMAIL'));
		$options[] = MHtml::_('select.option', 'color', 				MText::_('COM_MIWOEVENTS_FIELDS_COLOR'));
		$options[] = MHtml::_('select.option', 'language', 				MText::_('COM_MIWOEVENTS_FIELDS_LANGUAGE'));
		$options[] = MHtml::_('select.option', 'timezone', 				MText::_('COM_MIWOEVENTS_FIELDS_TIMEZONE'));
		$lists['field_type'] = MHTML::_('select.genericlist', $options, 'field_type',' class="inputbox" ', 'value', 'text', $item->field_type);

		if (@$this->MiwoeventsConfig->cb_integration) {
			if ($this->MiwoeventsConfig->cb_integration == 1) {
				$sql = 'SELECT name AS `value`, name AS `text` FROM #__comprofiler_fields WHERE `table` = "#__comprofiler"';
			}
            elseif ($this->MiwoeventsConfig->cb_integration == 2) {
				$sql = 'SELECT fieldcode AS `value`, fieldcode AS `text` FROM #__community_fields WHERE published = 1 AND fieldcode != ""' ;
			}

			$db->setQuery($sql);
			$options = array();

			$options[] = MHtml::_('select.option', '', MText::_('COM_MIWOEVENTS_SEL_FIELD'));
			$options = array_merge($options, $db->loadObjectList());

			$lists['field_mapping'] = MHtml::_('select.genericlist', $options, 'field_mapping', ' class="inputbox" ', 'value', 'text', $item->field_mapping);
		}
					
		$options = array();
		
		$options[] = MHtml::_('select.option', 2, MText::_('COM_MIWOEVENTS_FIELDS_EVENT'));
		$lists['display_in'] = MHtml::_('select.genericlist', $options, 'display_in', ' class="inputbox" onchange="checkDisable();"', 'value', 'text', $item->display_in);

        $lists['published'] = MiwoEvents::get('utility')->getRadioList('published', $item->published);
		$lists['language'] = MHtml::_('select.genericlist', MHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox" ', 'value', 'text', $item->language);

        MHtml::_('behavior.tooltip');

		$this->item		= $item;
		$this->lists	= $lists;

		parent::display($tpl);				
	}
}