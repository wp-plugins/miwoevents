<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsViewEvents extends MiwoeventsView {

    public function display($tpl = null) {
	
		
        if (!MiwoEvents::get('acl')->canEdit()) {
            JFactory::getApplication()->redirect('index.php?option=com_miwoevents', MText::_('JERROR_ALERTNOAUTHOR'));
        }
			











        $task = MRequest::getCmd('task');

        if ($this->_mainframe->isAdmin()) {
            $text = ($task == 'edit') ? MText::_('COM_MIWOEVENTS_EDIT') : MText::_('COM_MIWOEVENTS_NEW');

            MToolBarHelper::title(MText::_('COM_MIWOEVENTS_CPANEL_EVENTS').': <small><small>[ ' . $text.' ]</small></small>' , 'miwoevents' );
            MToolBarHelper::apply();
            MToolBarHelper::save();
            MToolBarHelper::save2new();
            MToolBarHelper::cancel();
            MToolBarHelper::divider();
            $this->toolbar->appendButton('Popup', 'help1', MText::_('Help'), 'http://miwisoft.com/support/docs/wordpress/miwoevents/user-manual/events?tmpl=component', 650, 500);
        }

		$item 		= $this->get('EditData');
        $locations 	= $this->get('Locations');
        $categories = $this->get('Categories');
        
        $null_date 	= MFactory::getDbo()->getNullDate();
        $params 	= new MRegistry($item->params);

		//Get list of location
		$options = array();
		$options = array_merge($options, $locations);
		$lists['location_id'] = MHtml::_('select.genericlist', $options, 'location_id', ' class="inputbox required" aria-required="true" required="required" aria-invalid="false" ', 'id', 'title', $item->location_id);

		$children = array();
		if ($categories) {
			foreach ($categories as $v) {
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$list = MHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		$options = array();
		foreach ($list as $listItem) {
			$options[] = MHtml::_('select.option', $listItem->id, '&nbsp;&nbsp;&nbsp;'. $listItem->treename);
		}

		$itemCategories = array() ;
		if ($item->id) {
			$cats = $this->get('EventCategories');

            $n = count($cats);
			for ($i = 0; $i < $n; $i++) {
				$itemCategories[] = MHtml::_('select.option', $cats[$i], $cats[$i]);
			}
		}
		$lists['category_id'] = MHtml::_('select.genericlist', $options, 'category_id[]', array(
				'option.text.toHtml'=> false ,
				'option.text' 		=> 'text' ,
				'option.value' 		=> 'value',
				'list.attr' 		=> 'class="inputbox required" size="5" multiple="multiple" aria-required="true" required="required" aria-invalid="false"',
				'list.select' 		=> $itemCategories
		));

		$options = array() ;
		$options[] = MHtml::_('select.option', 0, MText::_( 'COM_MIWOEVENTS_INDIVIDUAL_GROUP'));
		$options[] = MHtml::_('select.option', 1, MText::_( 'COM_MIWOEVENTS_INDIVIDUAL_ONLY'));
		$options[] = MHtml::_('select.option', 2, MText::_( 'COM_MIWOEVENTS_GROUP_ONLY'));
		$options[] = MHtml::_('select.option', 3, MText::_( 'COM_MIWOEVENTS_DISABLE_REGISTRATION'));
		$lists['registration_type'] = MHtml::_('select.genericlist', $options, 'registration_type', ' class="inputbox" ', 'value', 'text', $item->registration_type);

        
		
        $lists['enable_cancel_registration'] = MiwoEvents::get('utility')->getRadioList('enable_cancel_registration', $item->enable_cancel_registration);
        $lists['enable_auto_reminder'] = MiwoEvents::get('utility')->getRadioList('enable_auto_reminder', $item->enable_auto_reminder);

		$lists['attachment'] = MiwoEvents::get('utility')->getAttachmentList($item->attachment, $this->MiwoeventsConfig);
        $lists['published'] = MiwoEvents::get('utility')->getRadioList('published', $item->published);

        if ($item->event_date != $null_date) {
			$selectedHour = date('G', strtotime($item->event_date)) ;
			$selectedMinute = date('i', strtotime($item->event_date)) ;
		} else {
			$selectedHour = 0 ;
			$selectedMinute = 0 ;
		}
		$lists['event_date_hour'] = MHtml::_('select.integerlist', 0, 23, 1, 'event_date_hour', ' class="inputbox input-mini" style="margin-top: 10px;"', $selectedHour);
		$lists['event_date_minute'] = MHtml::_('select.integerlist', 0, 60, 5, 'event_date_minute', ' class="inputbox input-mini" ', $selectedMinute, '%02d');

        if ($item->event_end_date != $null_date) {
			$selectedHour = date('G', strtotime($item->event_end_date)) ;
			$selectedMinute = date('i', strtotime($item->event_end_date));
		} else {
			$selectedHour = 0;
			$selectedMinute = 0;
		}

		$lists['event_end_date_hour'] = MHtml::_('select.integerlist', 0, 23, 1, 'event_end_date_hour', ' class="inputbox input-mini" ', $selectedHour);
		$lists['event_end_date_minute'] = MHtml::_('select.integerlist', 0, 60, 5, 'event_end_date_minute', ' class="inputbox input-mini" ', $selectedMinute, '%02d');

		// Field setting
		$options = array() ;
		$options[] = MHtml::_('select.option', 0, MText::_('MHIDE'));
		$options[] = MHtml::_('select.option', 1, MText::_('MSHOW'));
		$options[] = MHtml::_('select.option', 2, MText::_('COM_MIWOEVENTS_REQUIRED'));

		$prm = new MRegistry($this->MiwoeventsConfig);
		$prm->merge($params);

		$this->ifNames = array();
		$this->gfNames = array();

		$eventParams= json_decode($item->params);
		$cEventParams = count($eventParams);

        if (!isset($this->MiwoeventsConfig->individual_fields)) {
            $this->MiwoeventsConfig->individual_fields = new stdClass();
        }

        if (!isset($this->MiwoeventsConfig->group_fields)) {
            $this->MiwoeventsConfig->group_fields = new stdClass();
        }

        $fields = $this->get("Fields");
		foreach ($fields as $row){
			$_name = $row->name;

            if (!isset($this->MiwoeventsConfig->individual_fields->$_name)) {
                $this->MiwoeventsConfig->individual_fields->$_name = 0;
            }

            if (!isset($this->MiwoeventsConfig->group_fields->$_name)) {
                $this->MiwoeventsConfig->group_fields->$_name = 0;
            }

            $listNameIF = "if_{$row->name}";
         	$listNameGF = "gf_{$row->name}";

			$ifSelected2 = $this->MiwoeventsConfig->individual_fields->$_name;
			$gfSelected2 = $this->MiwoeventsConfig->group_fields->$_name;

			if (isset($eventParams->$listNameIF)) { $ifSelected = $eventParams->$listNameIF; } else { $ifSelected = $ifSelected2; }
			if (isset($eventParams->$listNameGF)) { $gfSelected = $eventParams->$listNameGF; } else { $gfSelected = $gfSelected2; }

			$lists[$listNameIF] = MHtml::_('select.radiolist', $options, $listNameIF, 'class="btn-group"', 'value', 'text', $ifSelected);
			$lists[$listNameGF] = MHtml::_('select.radiolist', $options, $listNameGF, 'class="btn-group"', 'value', 'text', $gfSelected);

			$this->ifNames["name"][] 	= $listNameIF;
			$this->ifNames["title"][] 	= $row->title;

			$this->gfNames["name"][] 	= $listNameGF;
			$this->gfNames["title"][] 	= $row->title;
		}

        $lists['language'] = MHtml::_('select.genericlist', MHtml::_('contentlanguage.existing', true, true), 'language', ' class="inputbox" ', 'value', 'text', $item->language);

        # Early Bird Percent / Fix Settings
        $options = array() ;
		$options[] = MHtml::_('select.option', 0, MText::_('COM_MIWOEVENTS_DISCOUNT_PERCENT'));
		$options[] = MHtml::_('select.option', 1, MText::_('COM_MIWOEVENTS_DISCOUNT_FIX'));
        $lists['early_bird_option'] = MHtml::_('select.genericlist', $options, 'early_bird_option', ' class="inputbox" ', 'value', 'text', $item->early_bird_discount_type);

        









   	 	$selected_tax_class = ($task == 'edit') ? $item->tax_class : '0';

		



		//Trigger plugins
		MiwoEvents::get('utility')->trigger('onMiwoeventsEditEvent', array($item));

        MHtml::_('behavior.tooltip');
        MHtml::_('behavior.modal');

		$this->item		    = $item;
		$this->lists	    = $lists;
		$this->null_date	= $null_date;
		$this->fields	    = MiwoEvents::get('fields')->getEventFields($item->id);
				
		parent::display($tpl);				
	}
}