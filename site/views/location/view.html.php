<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsViewLocation extends MiwoeventsView {

    public function display($tpl = null) {
        $user = MFactory::getUser();

        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'location'), null, true);

		$item = $this->get('Item');

        $page_title = $item->title;
        if ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 1) {
            $page_title = MText::sprintf('MPAGETITLE', $this->_mainframe->getCfg('sitename'), $page_title);
        }
        elseif ($this->_mainframe->getCfg('sitename_pagetitles', 0) == 2) {
            $page_title = MText::sprintf('MPAGETITLE', $page_title, $this->_mainframe->getCfg('sitename'));
        }

		$this->document->setTitle($page_title);
		$this->document->setMetadata('description', $item->meta_desc);
		$this->document->setMetadata('keywords', $item->meta_key);
		$this->document->setMetadata('author', $item->meta_author);

		if ($this->MiwoeventsConfig->load_plugins) {
            $item->description = MHtml::_('content.prepare', $item->description);
		}
        $this->fields   = $item->fields;
        $this->item     = $item;
        $this->Itemid   = $Itemid;
        $this->params   = $this->_mainframe->getParams();
        $this->view_levels	= $user->getAuthorisedViewLevels();

$this->page_title = $page_title;
		parent::display($tpl);
	}

    public function displayMap($tpl = null) {
        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'map'), null, true);

        $this->item     = $this->get('Item');
        $this->Itemid   = $Itemid;

        parent::display($tpl);
    }

    public function displayEvents($tpl = null) {
        $user = MFactory::getUser();
        $db = MFactory::getDBO();
        $document = MFactory::getDocument();

        $Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'event'), null, true);

        $events = $this->get('Events');
        $location = $this->get('Item');

        $document->setTitle($location->title);

        if ($this->MiwoeventsConfig->load_plugins) {
            $n = count($events);
            
            for ($i = 0; $i < $n; $i++) {
                $event = &$events[$i];
				
                $event->introtext = MHtml::_('content.prepare', $event->introtext);
            }
        }

        $this->items 		= $events;
        $this->pagination 	= $this->get('EventsPagination');
        $this->Itemid 		= $Itemid;
        $this->viewLevels 	= $user->getAuthorisedViewLevels();
        $this->userId 		= $user->get('id');
        $this->location 	= $location;
        $this->location_id 	= MRequest::getInt('location_id');
        $this->nullDate 	= $db->getNullDate();

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

        require_once(MPATH_MIWOEVENTS_ADMIN.'/views/locations/view.edit.php');

        $options['name'] = 'locations';
        $options['layout'] = 'default_edit';
        $options['base_path'] = MPATH_MIWOEVENTS_ADMIN;
        $view = new MiwoEventsViewLocations($options);

        $view->setModel($this->getModel(), true);

        $view->display();
    }
}