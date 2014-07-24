<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsViewArchive extends MiwoeventsView {

	public function display($tpl = null) {
        $user = MFactory::getUser();

		$category_id = MRequest::getInt('category_id');
		
		$Itemid = MiwoEvents::get('utility')->getItemid(array('view' => 'category'), null, true);

		














		$item = $this->get('Item');

		$this->document->setTitle(MText::_('COM_MIWOEVENTS_EVENTS_ARCHIVE'));

		if ($this->MiwoeventsConfig->load_plugins) {
			$n = count($item);
			
			for ($i = 0; $i < $n; $i++) {			    
				$item = &$item[$i];
				
				$item->short_description = MHtml::_('content.prepare', $item->introtext);
			}				
		}
		
		unset($item);

		$this->item			= $this->get('Item');
		$this->Itemid       = $Itemid;										
		$this->pagination	= $this->get('Pagination');
		$this->nullDate		= MFactory::getDBO()->getNullDate();
		$this->params		= $this->_mainframe->getParams();
        $this->view_levels	= $user->getAuthorisedViewLevels();

		parent::display($tpl) ;									
	}	
}