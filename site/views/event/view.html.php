<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsViewEvent extends MiwoeventsView {

    public function __construct($config = array()) {
        parent::__construct($config);
    }
	
	public function display($tpl = null) {
        $user = MFactory::getUser();
        $pathway = $this->_mainframe->getPathway();

        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'category'), null, true);

		if (!$this->get('TotalEvents')) {
			$this->_mainframe->redirect('index.php?option=com_miwoevents&view=category'.$Itemid, MText::_('COM_MIWOEVENTS_INVALID_EVENT'));
		}

		$item = $this->get('Data');

        if (is_object($item) and !MiwoEvents::get('acl')->canAccess($item->access)) {
            MFactory::getApplication()->redirect(MRoute::_('index.php?option=com_miwoevents&view=category'), MText::_('JERROR_ALERTNOAUTHOR'), 'error');
        }

        ####Joomla####
		if(isset($this->MiwoeventsConfig->early_bird_discount_date) AND ($this->MiwoeventsConfig->early_bird_discount_date == 1)){
			$scriptUrl = MURL_MIWOEVENTS.'/site/assets/js/early_bird_date/jquery.js';
			$this->document->addScript($scriptUrl, 'text/javascript', null, null);

			$styleUrl = MURL_MIWOEVENTS.'/site/assets/js/early_bird_date/miwoevents_early_bird_date.css';
			$this->document->addStylesheet($styleUrl, 'text/css', null, null);

			$scriptUrl = MURL_MIWOEVENTS.'/site/assets/js/early_bird_date/miwoevents_early_bird.js';
			$this->document->addScript($scriptUrl, 'text/javascript', null, null);

			$scriptUrl = MURL_MIWOEVENTS.'/site/assets/js/early_bird_date/qunit-1.14.0.js';
			$this->document->addScript($scriptUrl, 'text/javascript', null, null);

			$date = new DateTime($item->early_bird_discount_date);
			$now_date = getdate();
			
			if (MiwoEvents::is30()) {
				if($now_date[0] < $date->getTimestamp()){
				$item->earl_bird_day_date_Timestamp =  $date->getTimestamp();
				}
			}else{
				if($now_date[0] < $date->format('U')){
				$item->earl_bird_day_date_Timestamp =  $date->format('U');
				}
			}
		}
        ####Joomla####

        $item->description = $item->introtext.$item->fulltext;

		$category = Miwoevents::get('utility')->getCategory($item->category_id);

        if ($this->MiwoeventsConfig->load_plugins) {
            $item->description = MHtml::_('content.prepare', $item->description);
        }
        
		# BreadCrumbs
		$cats = Miwoevents::get('utility')->getCategories($item->category_id);
		if (!empty($cats)) {
			asort($cats);
            foreach ($cats as $cat) {
                $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'category', 'category_id' => $cat->id), null, true);

                $path_url = MRoute::_('index.php?option=com_miwoevents&view=category&category_id='.$cat->id.$Itemid);
                $pathway->addItem($cat->title, $path_url);
            }
            $pathway->addItem($item->title);
        }

		if ($item->location_id) {
			$this->location = Miwoevents::get('utility')->getLocation($item->location_id);
		}

		$page_title = MText::_('COM_MIWOEVENTS_EVENT_PAGE_TITLE');
        $page_title = str_replace('[EVENT_TITLE]', $item->title, $page_title);
        $page_title = str_replace('[CATEGORY_NAME]', $category->title, $page_title);

        if ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 1) {
            $page_title = MText::sprintf('MPAGETITLE', $this->_mainframe->getCfg('sitename'), $page_title);
        }
        elseif ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 2) {
            $page_title = MText::sprintf('MPAGETITLE', $page_title, $this->_mainframe->getCfg('sitename'));
        }

		$this->document->setTitle($page_title);
		$this->document->setMetaData('keywords', $item->meta_key);
        $this->document->setMetaData('description', $item->meta_desc);
        $this->document->setMetadata('author', $item->meta_author);
        
        $item->group_rates = json_decode($item->group_rates);



















        MHtml::_('behavior.modal');

        $this->item             = $item;
		$this->view_levels      = $user->getAuthorisedViewLevels();
		$this->Itemid           = $Itemid;
		$this->userId           = $user->get('id');
		$this->nullDate         = MFactory::getDBO()->getNullDate();
		$this->tmpl             = MRequest::getCmd('tmpl');
        $this->show_price       = (($item->individual_price > 0) or ($this->MiwoeventsConfig->show_price_for_free_event = 1));
        $this->params           = $this->_mainframe->getParams();
        $this->fields           = MiwoEvents::get('fields')->getEventFields($item->id, "yes");
		$this->exportcal		= $this->getModel()->exportCal($this->item,$this->Itemid);

		parent::display($tpl);										
	}

    public function displaySubmit($tpl = null) {
        if (!MiwoEvents::get('acl')->canEdit() and !MiwoEvents::get('acl')->canCreate()) {
            MFactory::getApplication()->redirect('index.php?option=com_miwoevents&view=category', MText::_('JERROR_ALERTNOAUTHOR'));
        }

        $_lang = MFactory::getLanguage();
        $_lang->load('com_miwoevents', MPATH_ADMINISTRATOR, 'en-GB', true);
        $_lang->load('com_miwoevents', MPATH_ADMINISTRATOR, $_lang->getDefault(), true);
        $_lang->load('com_miwoevents', MPATH_ADMINISTRATOR, null, true);

        if (MiwoEvents::is30()) {
            MHtml::_('formbehavior.chosen', 'select');
        }

        $this->document->addStyleSheet(MURL_MIWOEVENTS.'/site/assets/css/submit.css');
        $this->document->addStyleSheet(MURL_MIWOEVENTS.'/admin/assets/css/joomla3.css');

        require_once(MPATH_MIWOEVENTS_ADMIN.'/views/events/view.edit.php');

        $options['name'] = 'events';
        $options['layout'] = 'default_edit';
        $options['base_path'] = MPATH_MIWOEVENTS_ADMIN;
        $view = new MiwoEventsViewEvents($options);

        $view->setModel($this->getModel(), true);

        $view->display();
    }
}