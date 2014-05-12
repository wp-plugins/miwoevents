<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');


class MiwoeventsAcl {

    public function __construct() {
		$this->MiwoeventsConfig = MiwoEvents::getConfig();

        $this->actions = $this->getActions();
	}

    public function canAdmin() {
        return $this->actions->get('core.admin');
    }

    public function canManage() {
        return $this->actions->get('core.manage');
    }

    public function canCreate() {
        return $this->actions->get('core.create');
    }

    public function canEdit() {
        return $this->actions->get('core.edit');
    }

    public function canEditOwn($id = null) {
        return $this->actions->get('core.edit.own');
    }

    public function canEditState() {
        return $this->actions->get('core.edit.state');
    }

    public function canDelete() {
        return $this->actions->get('core.delete');
    }

    public function canAccessAttenders($tmpl = '') {
        if ($tmpl == 'component') {
            $action = 'view.attenders';
        }
        else {
            $action = 'manage.attenders';
        }

        return $this->actions->get($action);
    }

    public function canAccess($access) {
        $user = MFactory::getUser();

        if (!in_array($access, $user->getAuthorisedViewLevels())) {
            return false;
        }

        return true;
    }

    public function canAccessHistory() {
        $user = MFactory::getUser();

        if (!$user->get('id')) {
            return false;
        }

        return true;
    }

    public function canExportAttenders($event_id = 0) {
        $user = MFactory::getUser();

        if (!empty($event_id)) {
            $createdBy = (int) MiwoDatabase::loadResult('SELECT created_by FROM #__miwoevents_events WHERE id='.$event_id);

            return (($createdBy > 0 and $createdBy == $user->id) or $this->actions->get('manage.attenders'));
        }
        else {
            return $this->actions->get('manage.attenders');
        }
    }

    public function canCancelRegistration($event_id) {
        $db = MFactory::getDbo();
        $user = MFactory::getUser();

        $user_id = $user->get('id');
        if (!$user_id) {
            return false;
        }

        $registrant = MiwoDatabase::loadObject('SELECT * FROM #__miwoevents_attenders WHERE event_id = '.$event_id.' AND user_id = '.$user_id);
        if (!is_object($registrant)) {
            return false;
        }

        if (!in_array($registrant->status, array(1, 3, 11, 12))) {
            return false;
        }

        $registrant_id = $registrant->id;

        $total = MiwoDatabase::loadResult('SELECT COUNT(*) FROM #__miwoevents_events WHERE id='.$event_id.' AND enable_cancel_registration = 1 AND (DATEDIFF(cancel_before_date, NOW()) >=0)');
        if (!$total) {
            return false;
        }

        return $registrant_id;
    }

    public function getActions($category_id = 0, $event_id = 0) {
        $acts = new MObject;
        $user = MFactory::getUser();

        $assetName = 'com_miwoevents';
        /*if (empty($event_id) and empty($category_id)) {
            $assetName = 'com_miwoevents';
        }
        elseif (empty($event_id)) {
            $assetName = 'com_miwoevents.category.'.(int) $category_id;
        }
        else {
            $assetName = 'com_miwoevents.event.'.(int) $event_id;
        }*/

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete', 'manage.attenders', 'view.attenders'
        );

        foreach ($actions as $action) {
            $acts->set($action, $user->authorise($action, $assetName));
        }

        return $acts;
    }
}