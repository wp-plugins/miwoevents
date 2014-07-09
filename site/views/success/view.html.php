<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoeventsViewSuccess extends MiwoeventsView {

	public function display($tpl = null) {

           $Itemid = $_GET['Itemid'];

			$active_user = MFactory::getUser();
			if($active_user->guest){
			   $this->data['continue']          = MRoute::_("index.php?option=com_miwoevents&view=history&Itemid={$Itemid}&freeevent=1");
			}else{
			   $this->data['continue']          = MRoute::_("index.php?option=com_miwoevents&view=history&Itemid={$Itemid}");
			}
           
		   $this->data['event_product']     = $_SESSION['event_product'];

           $reg                             = new MRegistry(MComponentHelper::getParams('com_miwoevents'));
           $miwoevents_config               = $reg->toObject()->data;

           $this->data['heading_title']     = $miwoevents_config->thank_page_event;
           $this->data['text_message']      = $miwoevents_config->thank_page_event_body;
           $this->data['button_continue']   = "Continue";

		parent::display($tpl);
	}
}