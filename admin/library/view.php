<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Imports
mimport('framework.application.component.view');

if (!class_exists('MiwisoftView')) {
	if (interface_exists('MView')) {
		abstract class MiwisoftView extends MViewLegacy {}
	}
	else {
		class MiwisoftView extends MView {}
	}
}

class MiwoeventsView extends MiwisoftView {

	public $toolbar;
	public $document;

    public function __construct($config = array()) {
		parent::__construct($config);


		$rootURL = MUri::root();


        $this->_mainframe = MFactory::getApplication();
        if ($this->_mainframe->isAdmin()) {
            $this->_option = MiwoEvents::get('utility')->findOption();
        }
        else {
            $this->_option = MRequest::getCmd('option');
        }

		# Get toolbar object
        if ($this->_mainframe->isAdmin()) {
		    $this->toolbar = MToolBar::getInstance();
        }

		# Import CSS
		$this->document = MFactory::getDocument();



		if (MiwoEvents::is30()) {
            $this->document->addStyleSheet(MURL_MIWOEVENTS.'/site/assets/css/joomla3.css');

            if ($this->_mainframe->isAdmin()) {
				$this->document->addStyleSheet(MURL_MIWOEVENTS.'/admin/assets/css/miwoevents.css');
                MHtml::_('formbehavior.chosen', 'select');
            }else{
			$this->document->addStyleSheet(MURL_MIWOEVENTS.'/site/assets/css/miwoevents.css');
	
			}
        }


        else {		

            if ($this->_mainframe->isAdmin()) {
                $this->document->addStyleSheet(MURL_MIWOEVENTS.'/admin/assets/css/table.css');
				$this->document->addStyleSheet(MURL_MIWOEVENTS.'/admin/assets/css/miwoevents.css');
				$this->document->addStyleSheet(MURL_MIWOEVENTS.'/admin/assets/css/joomla2.css');
            }
			else {
				$this->document->addStyleSheet(MURL_MIWOEVENTS.'/site/assets/css/miwoevents.css');
				$this->document->addStyleSheet(MURL_MIWOEVENTS.'/site/assets/css/joomla2.css');
			}
        }
		
		$this->acl = MiwoEvents::get('acl');



		# Get config object
		$this->MiwoeventsConfig = MiwoEvents::getConfig();
	}
	

	public function getIcon($i, $task, $img, $check_acl = false) {
        if ($check_acl and !$this->acl->canEditState()) {
            $html = '<img src="'.MURL_MIWOEVENTS.'/admin/assets/images/'.$img.'" border="0" />';
        }
        else {
            $html = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'.$i.'\',\''.$task.'\')">';
            $html .= '<img src="'.MURL_MIWOEVENTS.'/admin/assets/images/'.$img.'" border="0" />';
            $html .= '</a>';


        }

		return $html;
	}

}