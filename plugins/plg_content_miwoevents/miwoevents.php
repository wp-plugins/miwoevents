<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

mimport('framework.plugin.plugin');

class plgContentMiwoevents extends MPlugin {

	function __construct(&$subject, $params) {
		parent::__construct($subject, $params);
	}

	public function onContentPrepare($context, &$article, &$params, $limitstart) {
		if (MFactory::getApplication()->isAdmin()) {
			return true;
		}
		
		if (strpos($article->text, '{miwoevents id=') === false) {
			return true;
		}
		
		$regex = "#{miwoevents id=(\d+)}#s";
		
		$article->text = preg_replace_callback($regex, array(&$this, '_processMatches'), $article->text);
		
		return true;
	}

	public function _processMatches(&$matches) {
        require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

        $old_option = MRequest::getCmd('option');
        $old_view = MRequest::getCmd('view');

		MRequest::setVar('option', 'com_miwoevents');
		MRequest::setVar('view', 'event');
		MRequest::setVar('event_id', $matches[1]);

		ob_start();

        require_once(MPATH_WP_PLG.'/miwoevents/site/controllers/event.php');
        require_once(MPATH_WP_PLG.'/miwoevents/site/models/event.php');
        require_once(MPATH_WP_PLG.'/miwoevents/site/views/event/view.html.php');

		$controller = new MiwoeventsControllerEvent();
        $controller->_model = new MiwoeventsModelEvent();

        $options['name'] = 'event';
        $options['layout'] = 'default';
        $options['base_path'] = MPATH_MIWOEVENTS;
        $view = new MiwoeventsViewEvent($options);

        $view->setModel($controller->_model, true);

        //$view->setLayout('common');
        $view->display();

		$output = ob_get_contents();
		ob_end_clean();

        MRequest::setVar('option', $old_option);
        MRequest::setVar('view', $old_view);
		
		return $output;
	}
}