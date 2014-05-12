<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsViewCategory extends MiwoeventsView {
	
	public function display($tpl = null) {
        $user = MFactory::getUser();
		$nullDate = MFactory::getDBO()->getNullDate();
		$pathway = $this->_mainframe->getPathway();

		$category_id = MiwoEvents::getInput()->getInt('category_id', 0);

        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'category'), null, true);
		
		$events 			= $this->get('Events');
        $categories 		= $this->get('Categories');
		$category 			= Miwoevents::get('utility')->getCategory($category_id);

        if (is_object($category) and !MiwoEvents::get('acl')->canAccess($category->access)) {
            MFactory::getApplication()->redirect(MRoute::_('index.php?option=com_miwoevents&view=category'), MText::_('JERROR_ALERTNOAUTHOR'), 'error');
        }

        if (!empty($category_id)) {
            $page_title = MText::_('COM_MIWOEVENTS_CATEGORY_PAGE_TITLE');
            $page_title = str_replace('[CATEGORY_NAME]', $category->title, $page_title);

            if ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 1) {
                $page_title = MText::sprintf('MPAGETITLE', $this->_mainframe->getCfg('sitename'), $page_title);
            }
            elseif ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 2) {
                $page_title = MText::sprintf('MPAGETITLE', $page_title, $this->_mainframe->getCfg('sitename'));
            }

            $this->document->setTitle($page_title);
            $this->document->setMetadata('description', $category->meta_desc);
            $this->document->setMetadata('keywords', 	$category->meta_key);
            $this->document->setMetadata('author', 		$category->meta_author);
        }
        else {
            $this->document->setTitle(MText::_('COM_MIWOEVENTS_CATEGORIES_PAGE_TITLE'));
        }

		if ($this->MiwoeventsConfig->load_plugins) {
            $n = count($events);
			
			for ($i = 0; $i < $n; $i++) {
				$item = &$events[$i];
				
				$item->introtext = MHtml::_('content.prepare', $item->introtext);
			}
			
			if ($category) {	
				$category->description = MHtml::_('content.prepare', $category->introtext.$category->fulltext);
			}
		}
		
		# BreadCrumbs
        $cats = Miwoevents::get('utility')->getCategories($category_id);
        
        if (!empty($cats)) {
        	asort($cats);

            foreach ($cats as $cat) {
            	if($cat->id != $category_id) {
                    $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'category', 'category_id' => $cat->id), null, true);

	                $path_url = MRoute::_('index.php?option=com_miwoevents&view=category&category_id='.$cat->id.$Itemid);
	                $pathway->addItem($cat->title, $path_url);
            	}
            }

            $pathway->addItem($category->title);
        }
		

		$userId = $user->get('id');
		$_SESSION['last_category_id'] = $category_id;

        MHtml::_('behavior.modal');

		$this->userId			= $userId;
		$this->items			= $events;
		$this->categories		= $categories;									
		$this->pagination		= $this->get('Pagination');
		$this->Itemid			= $Itemid;
		$this->category			= $category;
		$this->nullDate			= $nullDate;
        $this->params       	= $this->_mainframe->getParams();
        $this->viewLevels		= $user->getAuthorisedViewLevels();
        $this->view_levels      = $user->getAuthorisedViewLevels();
		
		parent::display($tpl);
	}

	public function displayCalendar($tpl = null) {
        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'calendar'), null, true);

        if ($this->MiwoeventsConfig->calendar_theme) {
            $theme = $this->MiwoeventsConfig->calendar_theme;
        }
        else {
            $theme = 'default';
        }

        $styleUrl = MUri::base(true).'/components/com_miwoevents/assets/css/themes/'.$theme.'.css';
        $this->document->addStylesheet($styleUrl, 'text/css', null, null);

        
        $category_id = MiwoEvents::getInput()->getInt('category_id', 0);
        
		$category 	= Miwoevents::get('utility')->getCategory($category_id);
		$calendar 	= MiwoeventsModel::getInstance('Calendar', 'MiwoeventsModel', array());
		$calCat     = $calendar->getCalCategories();
		
		list($year, $month, $day) = $calendar->getYMD();

		$this->data 	= $calendar->getMonthlyEvents();
		$this->month 	= $month;
		$this->year 	= $year;
		
        $listmonth = array(MText::_('COM_MIWOEVENTS_JAN'), MText::_('COM_MIWOEVENTS_FEB'), MText::_('COM_MIWOEVENTS_MARCH'), MText::_('COM_MIWOEVENTS_APR'), MText::_('COM_MIWOEVENTS_MAY'), MText::_('COM_MIWOEVENTS_JUNE'), MText::_('COM_MIWOEVENTS_JULY'), MText::_('COM_MIWOEVENTS_AUG'), MText::_('COM_MIWOEVENTS_SEP'), MText::_('COM_MIWOEVENTS_OCT'),MText::_('COM_MIWOEVENTS_NOV'),MText::_('COM_MIWOEVENTS_DEC'));
        $option_month = array();
        foreach ($listmonth AS $key => $omonth){
            if ($key < 9){ $value = "0".($key + 1); } else { $value = $key + 1; }

            $option_month[] = MHtml::_('select.option', $value, $omonth);
        }

        $javascript = 'onchange="cal_date_change(this.value,'.$year.', '.$Itemid.');"';
        $this->search_month = MHtml::_('select.genericlist', $option_month, 'month','class="regpro_calendar_months" '.$javascript, 'value', 'text', $month);

        $option_year = array();
        $javascript = 'onchange="cal_date_change('.$month.',this.value, '.$Itemid.');"';
        for ($i = $year-3; $i < ($year+5); $i++){
            $option_year[] = MHtml::_('select.option',$i,$i);
        }
        $this->search_year = MHtml::_('select.genericlist', $option_year, 'year', 'class="regpro_calendar_years" '.$javascript, 'value', 'text', $year);
			
		$this->category = $category;
		
		$this->Itemid 	= $Itemid;
        $this->params   = $this->_mainframe->getParams();
		$this->calCat   = $calCat;
			
		parent::display($tpl);			
	}

    public function displayTable($tpl = null) {
        $this->display($tpl);
    }

    public function displaySubmit($tpl = null) {}
}