<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

class MiwoEventsControllerCalendar extends MiwoEventsController {

	public function __construct($config = array()) {
		parent::__construct('calendar');
	}

    public function changeMinicalendar(){
        $lang = MFactory::getLanguage() ;
        $tag = $lang->getTag();
        if (!$tag) { $tag = 'en-GB'; }

        $lang->load('mod_miwoevents_minicalendar', MPATH_ROOT, $tag);

        $module_title = MiwoEvents::getInput()->getString('module_title', '');
        $module = MModuleHelper::getModule('mod_miwoevents_minicalendar', $module_title);

        $content = MModuleHelper::renderModule($module);

        echo "<div id='minical_change'>";
        echo $content;
        echo "</div>";

        ?>
        <script type="text/javascript">
            function doit(){
                var minical_change = document.getElementById('minical_change');
                parent.navminicalLoaded(minical_change);
            }

            window.onload = doit;
        </script>
        <?php
    }
}###