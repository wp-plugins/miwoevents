<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted access');

require_once(MPATH_WP_PLG.'/miwoevents/admin/library/miwoevents.php');

function MiwoeventsBuildRoute(&$query) {
    $segments = array();
    $utility = MiwoEvents::get('utility');

    $menu = $utility->getMenu();

    if (!empty($query['Itemid'])) {
        $Itemid = $query['Itemid'];
    }
    else {
        $Itemid = $utility->getItemid($query, null, false);
    }

    if (empty($Itemid)) {
        $a_menu = $menu->getActive();
    }
    else {
        $a_menu = $menu->getItem($Itemid);
    }

    if (isset($query['view'])) {
        switch($query['view']) {
            case 'category':
                if (is_object($a_menu) and ($a_menu->query['view'] == 'category') and (@$a_menu->query['category_id'] == @$query['category_id'])){
                    $brk = true;

                    if (isset($query['layout'])) {
                        if (!isset($a_menu->query['layout']) or (isset($a_menu->query['layout']) and ($a_menu->query['layout'] != $query['layout']))) {
                            $brk = false;
                        }
                    }

                    if (isset($a_menu->query['layout'])) {
                        if (!isset($query['layout']) or (isset($query['layout']) and ($query['layout'] != $a_menu->query['layout']))) {
                            $brk = false;
                        }
                    }

                    if ($brk == true) {
                        unset($query['category_id']);
                        unset($query['layout']);
                        break;
                    }
                }

                $segments[] = $query['view'];

                if (isset($query['layout'])) {
                    $segments[] = $query['layout'];
                    unset($query['layout']);
                }

                if (isset($query['category_id'])) {
                    $alias = $utility->getRecordAlias($query['category_id'], 'category');

                    $segments[] = $query['category_id'].':'.$alias;

                    unset($query['category_id']);
                }
                break;
            case 'event':
                if (is_object($a_menu) and ($a_menu->query['view'] == 'event') and (@$a_menu->query['event_id'] == @$query['event_id'])){
                    unset($query['event_id']);
                    break;
                }

                $segments[] = $query['view'];

                if (isset($query['event_id'])) {
                    $alias = $utility->getRecordAlias($query['event_id'], 'event');

                    $segments[] = $query['event_id'].':'.$alias;
                    unset($query['event_id']);
                }

                break;
            case 'location':
                if (is_object($a_menu) and ($a_menu->query['view'] == 'location') and (@$a_menu->query['location_id'] == @$query['location_id'])) {
                    $brk = true;

                    if (isset($query['layout'])) {
                        if (!isset($a_menu->query['layout']) or (isset($a_menu->query['layout']) and ($a_menu->query['layout'] != $query['layout']))) {
                            $brk = false;
                        }
                    }

                    if (isset($a_menu->query['layout'])) {
                        if (!isset($query['layout']) or (isset($query['layout']) and ($query['layout'] != $a_menu->query['layout']))) {
                            $brk = false;
                        }
                    }

                    if ($brk == true) {
                        unset($query['location_id']);
                        unset($query['layout']);
                        break;
                    }
                }

                $segments[] = $query['view'];

                if (isset($query['layout'])) {
                    $segments[] = $query['layout'];
                    unset($query['layout']);
                }

                if (isset($query['location_id'])) {
                    $alias = $utility->getRecordAlias($query['location_id'], 'location');

                    $segments[] = $query['location_id'].':'.$alias;

                    unset($query['location_id']);
                }

                break;
            default:
                if (is_object($a_menu) and ($a_menu->query['view'] == $query['view'])) {
                    $brk = true;

                    if (isset($query['layout'])) {
                        if (!isset($a_menu->query['layout']) or (isset($a_menu->query['layout']) and ($a_menu->query['layout'] != $query['layout']))) {
                            $brk = false;
                        }
                    }

                    if (isset($a_menu->query['layout'])) {
                        if (!isset($query['layout']) or (isset($query['layout']) and ($query['layout'] != $a_menu->query['layout']))) {
                            $brk = false;
                        }
                    }

                    if ($brk == true) {
                        unset($query['layout']);
                        break;
                    }
                }

                $segments[] = $query['view'];

                if (isset($query['layout'])) {
                    $segments[] = $query['layout'];
                    unset($query['layout']);
                }

                if (isset($query['date'])) {
                    $segments[] = $query['date'];
                    unset($query['date']);
                }

                if (isset($query['month'])) {
                    $segments[] = $query['month'];
                    unset($query['month']);
                }

                if (isset($query['year'])) {
                    $segments[] = $query['year'];
                    unset($query['year']);
                }

                break;
        }

        unset($query['view']);
    }

    return $segments;

}

function MiwoeventsParseRoute($segments) {
    $count = count($segments);

    if ($count == 1) {
        $vars['view'] = $segments[0];

        return $vars;
    }

    switch($segments[0]) {
        case 'event':
        case 'category':
        case 'location':
            $vars['view'] = $segments[0];

            if (isset($segments[2])) {
                $vars['layout'] = $segments[1];
                $alias = $segments[2];
            }
            else {
                $alias = $segments[1];
            }

            $id = explode(':', $alias);
            $vars[$segments[0].'_id'] = (int) $id[0];

            break;
        case 'calendar':
            $vars['view'] = $segments[0];

            if (isset($segments[1]) and !is_numeric($segments[1])) {
                $vars['layout'] = $segments[1];
            }

            if (isset($segments[2])) {
                $vars['date'] = $segments[2];
            }

            break;
        default:
            $vars['view'] = $segments[0];

            if (isset($segments[1]) and !is_numeric($segments[1])) {
                $vars['layout'] = $segments[1];
            }

            break;
    }

    return $vars;
}