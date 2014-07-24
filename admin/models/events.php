<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die ;

class MiwoEventsModelEvents extends MiwoeventsModel {
	
	public $process;
	
    public function __construct() {
		parent::__construct('events');

        $task = MRequest::getCmd('task');
        $tasks = array('edit', 'apply', 'save', 'save2new');

		if (in_array($task, $tasks)) {
			$cid = MRequest::getVar('cid', array(0), '', 'array');
			$this->setId((int)$cid[0]);
		}
		else {
			$this->_getUserStates();
			$this->_buildViewQuery();
		}
	}
	
	public function _getUserStates(){
		$this->filter_order			= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order',			'filter_order',			'title');
		$this->filter_order_Dir		= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_order_Dir',		'filter_order_Dir',		'ASC');
        $this->filter_past	        = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_past', 	        'filter_past', 	        -1);
		$this->filter_category	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_category', 	    'filter_category', 	    0);
		$this->filter_location	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_location', 	    'filter_location', 	    0);
		$this->filter_published	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_published', 	'filter_published', 	'');
		$this->filter_access	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_access', 	    'filter_access', 	    '');
		$this->filter_language	    = parent::_getSecureUserState($this->_option . '.' . $this->_context . '.filter_language', 	    'filter_language', 	    '');
		$this->search				= parent::_getSecureUserState($this->_option . '.' . $this->_context . '.search', 				'search', 				'');
		$this->search 	 			= MString::strtolower($this->search);
	}

	public function getItems() {
		if (empty($this->_data)) {
			$rows = parent::getItems();
			
			foreach ($rows as $row) {
				$sql = "SELECT c.title FROM #__miwoevents_categories AS c, #__miwoevents_event_categories AS ec WHERE c.id = ec.category_id AND ec.event_id = {$row->id}";
				$this->_db->setQuery($sql);
				
				$row->categories = implode(' | ', $this->_db->loadColumn());
				if(empty($row->categories)){
                    $row->categories =  MText::_('COM_MIWOEVENTS_UNRELATED_CATEGORY');
                }
			}
			
			$this->_data = $rows;									
		}
		
		return $this->_data;
	}

    public function getTotal() {
		if (empty($this->_total)) {
			$this->_total = MiwoDatabase::loadResult("SELECT COUNT(*) FROM #__{$this->_component}_{$this->_table} AS e".$this->_buildViewWhere());
		}

		return $this->_total;
	}
	
	public function getCategories() {
		return MiwoDatabase::loadObjectList('SELECT id, parent, parent AS parent_id, title FROM #__miwoevents_categories');
	}

	public function getEventCategories() {
		return MiwoDatabase::loadResultArray('SELECT category_id FROM #__miwoevents_event_categories WHERE event_id='.$this->_id);
	}

	public function getLocations() {
		return MiwoDatabase::loadObjectList('SELECT * FROM #__miwoevents_locations WHERE published=1 ORDER BY title');
	}

    public function _buildViewQuery() {
        $where = self::_buildViewWhere();

        $orderby = "";
        if (!empty($this->filter_order) and !empty($this->filter_order_Dir)) {
            $orderby = " ORDER BY {$this->filter_order} {$this->filter_order_Dir}";
        }

        $this->_query = 'SELECT e.*, COUNT(a.id) AS attenders FROM #__miwoevents_events AS e '
            .' LEFT JOIN #__miwoevents_attenders AS a '
            .' ON (e.id = a.event_id AND a.status <> 100) '
            . $where
            .' GROUP BY e.id '
            . $orderby
        ;
    }

	public function _buildViewWhere() {
		$where = array();

        if ($this->search) {
            $src = parent::secureQuery($this->search, true);
            $where[] = "LOWER(e.title) LIKE {$src}";
        }

        if ($this->filter_past == 0) {
            $where[] = 'DATE(e.event_date) >= CURDATE()';
        }

		if ($this->filter_category) {
			$where[] = 'e.id IN (SELECT event_id FROM #__miwoevents_event_categories WHERE category_id='.$this->filter_category.')';
		}

		if ($this->filter_location) {
			$where[] = 'e.location_id = '.$this->filter_location;
        }
				
		if (is_numeric($this->filter_published)) {
			$where[] = 'e.published = '.(int) $this->filter_published;
		}

        if (is_numeric($this->filter_access)) {
            $where[] = 'e.access = '.(int) $this->filter_access;
        }

        if ($this->filter_language) {
            $where[] = 'e.language IN (' . $this->_db->Quote($this->filter_language) . ',' . $this->_db->Quote('*') . ')';
        }

		$where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');
		
		return $where;
	}
	
	public function store(&$data) {
		$this->saveEventData($data);
		
		if ($this->process == true){ return true; }
	}
	
	public function getEditData($table = NULL) {
		if (empty($this->_data)) {
			$row = parent::getEditData();
			
			if (empty($this->_id)) {
                $row->early_bird = '';
            }
            else {
            	$row->early_bird = ''; # TODO:: get from miwoshop tables
            }

            switch ($row->recurring_type) {
                case 1:
                    $row->number_days   = $row->recurring_frequency;
                    $row->number_weeks  = 0;
                    $row->number_months = 0;
                    break;
                case 2:
                    $row->number_days   = 0;
                    $row->number_weeks  = $row->recurring_frequency;
                    $row->number_months = 0;
                    break;
                case 3:
                    $row->number_days   = 0;
                    $row->number_weeks  = 0;
                    $row->number_months = $row->recurring_frequency;
                    break;
                default:
                    $row->number_days   = 0;
                    $row->number_weeks  = 0;
                    $row->number_months = 0;
                    break;
            }
            
            $row->early_bird_option = 0;
                
            $this->_data = $row;
		}

		return $this->_data;
	}

	public function copy($id) {
		$db = MFactory::getDBO();
		
		$rowOld = MiwoEvents::getTable('MiwoeventsEvents');
		$rowOld->load($id);

		$row = MiwoEvents::getTable('MiwoeventsEvents');
		
		$data = get_object_vars($rowOld);
		$data['edit_product_id']= 0;
		$data['title'] 			= $data['title']." Copy";
		$data['alias']			= $data['alias']."-copy";
		$data["product_id"]		= 0;
		
		
    	# Get Product ID
		$row->product_id =$data["product_id"];
		
		$data = MArrayHelper::fromObject($rowOld);
		$row->bind($data);

		$row->id = 0;
		$row->title = $row->title.' Copy';
		$row->alias = $row->alias.'-copy';
		
		$row->store();
	
		# Need to enter categories for this event
		$sql = 'INSERT INTO #__miwoevents_event_categories(event_id, category_id) '
		.' SELECT '.$row->id.' , category_id FROM #__miwoevents_event_categories '
		.' WHERE event_id='.$id;
		
		$this->_db->setQuery($sql);
		$this->_db->query();
	
		return $row->id;
	}
	
	public function getProductID() {
		if ($this->_id) {
			$sql = "SELECT product_id FROM #__miwoevents_events WHERE id= {$this->_id} ORDER BY id DESC LIMIT 1";
			$this->_db->setQuery($sql);
			$productID = $this->_db->loadResult();
		}
        else {
			$productID = 0;
		}
		return $productID ;
	}
	
	public function autoComplete($query){
        if (!empty($query)) {
            $sql = "SELECT id, name FROM #__miwoevents_fields WHERE display_in = 2 AND LOWER(name) LIKE '%".strtolower($query)."%' ORDER BY name DESC";
            $this->_db->setQuery($sql);
            $events = $this->_db->loadAssocList();
        }
        else {
            $events = array();
        }

        return $events;
    }
    
    public function getFields() {
    	return MiwoDatabase::loadObjectList("SELECT * FROM #__miwoevents_fields WHERE display_in = 1 AND published = 1 ORDER BY ordering");
    }
    
    public function delete($id) {
    	MiwoDatabase::query('DELETE FROM #__miwoevents_event_categories WHERE event_id = '.$id);
    }
	
	public function deleteEvent($id) {
		$this->delete($id);
    	if(MiwoDatabase::query('DELETE FROM #__miwoevents_events WHERE id = '.$id)) {
			return true;
		} else {
			return false;
		}
    }
    
    
    # Event Process
	######################################################################################################################################################################################################
	public function saveEventData(&$data) {
    	mimport('framework.filesystem.file');
    	$db = MFactory::getDBO();
    	
    	# Thumb Image
		if ($_FILES['thumb_image']['name']) {
			$fileExt = MString::strtoupper(MFile::getExt($_FILES['thumb_image']['name']));
			$supportedTypes = array('JPG', 'PNG', 'GIF');

			if (in_array($fileExt, $supportedTypes)) {
				if (MFile::exists(MPATH_MEDIA.'/miwoevents/images/'.MString::strtolower($_FILES['thumb_image']['name']))) {
					$fileName = time().'_'.MString::strtolower($_FILES['thumb_image']['name']);
				}
                else {
					$fileName = MString::strtolower($_FILES['thumb_image']['name']);
				}

				$imagePath = MPATH_MEDIA.'/miwoevents/images/'.$fileName;
				$thumbPath = MPATH_MEDIA.'/miwoevents/images/thumbs/'.$fileName;
				MFile::upload($_FILES['thumb_image']['tmp_name'], $imagePath);

				if (!$this->MiwoeventsConfig->thumb_width) { $this->MiwoeventsConfig->thumb_width = 120; }
				if (!$this->MiwoeventsConfig->thumb_height) { $this->MiwoeventsConfig->thumb_height = 120; }

				MiwoEvents::get('utility')->resizeImage($imagePath, $thumbPath, $this->MiwoeventsConfig->thumb_width, $this->MiwoeventsConfig->thumb_height, 95);

				$data['thumb'] = $fileName;
			}
		}
		
		# Default data
		if (!isset($data['weekdays']))			        { $data['weekdays'] 				= array(); }
		if (!isset($data['monthdays'])) 		        { $data['monthdays'] 				= ''; }
		if (!isset($data['number_days'])) 				{ $data['number_days'] 				= 1; }
		if (!isset($data['number_weeks'])) 			    { $data['number_week'] 				= 1; }
		if (!isset($data['recurring_occurrencies'])) 	{ $data['recurring_occurrencies']	= 0; }
		if (!isset($data['recurring_end_date'])) 		{ $data['recurring_end_date'] 		= $this->_db->getNullDate(); }
		if (isset($data['payment_methods'])) 	        { $data['payment_methods'] 			= implode(',', $data['payment_methods']); }
		
		$row = MiwoEvents::getTable('MiwoeventsEvents');
		
		if ($this->_id) {
			$isNew = false;
			$row->load($this->_id);

			if (isset($data['del_thumb']) and $row->thumb) {
				if (MFile::exists(MPATH_MEDIA.'/miwoevents/images/'.$row->thumb)) {
					MFile::delete(MPATH_MEDIA.'/miwoevents/images/'.$row->thumb);
				}

				if (MFile::exists(MPATH_MEDIA.'/miwoevents/images/thumbs/'.$row->thumb)) {
					MFile::delete(MPATH_MEDIA.'/miwoevents/images/thumbs/'.$row->thumb);
				}

				$data['thumb'] = '';
			}
		}
        else {
			$isNew = true;
		}
		
		# Check ordering of the fields
		if (!$row->id) {
			$where = ' category_id = ' . (int) $row->category_id;
			$row->ordering = $row->getNextOrder($where);
		}
		
		# Description Exploding
		$delimiter = "<hr id=\"system-readmore\" />";
		$exp = explode($delimiter, $data['description']);
				
		if(strpos($data['description'], $delimiter) == true){
			$data['introtext']	= $exp[0];
			$data['fulltext']	= $exp[1];
		} else {
			$data['introtext']	= $exp[0];
			$data['fulltext']	= "";
		}
		
		if (!$row->created_by) {
			$user = MFactory::getUser();
			
			$row->created_by = $user->get('id');
            $data['created_by']	= $row->created_by;
		}
		
		# Custom Fields
		$db->setQuery("SELECT * FROM #__miwoevents_fields WHERE display_in = 1 AND published = 1 ORDER BY title");
		$rows_x = $db->loadObjectList();
		
		$this->ifNames = array();
		$this->gfNames = array();
		
		foreach ($rows_x as $row_x) {
			$listNameIF = "if_{$row_x->name}";
			$listNameGF = "gf_{$row_x->name}";
			
			$this->ifNames[] = $listNameIF;
			$this->gfNames[] = $listNameGF;
			$this->_fields[$row_x->name] = $row_x;
		}
		
		$keys = $this->ifNames;
		$keys = array_merge($keys, $this->gfNames);
		
		# Setup the parameters, too
		$params = array();
		foreach ($keys as $key) {
			$params[$key] = MRequest::getInt($key);
		}
		
		$prm = new MRegistry($params);
		$row->params = $prm->toString();
		
		# Custom Fields for Events
		$data['fields'] = json_encode($data['custom_fields']);
		
		$row->event_date = "{$data['event_date']} {$data['event_date_hour']}:{$data['event_date_minute']}:00";
		$data['event_date'] = $row->event_date;
		
		$row->event_end_date ="{$data['event_end_date']} {$data['event_end_date_hour']}:{$data['event_end_date_minute']}:00";
		$data['event_end_date'] = $row->event_end_date;
		
		if(empty($row->event_type)){
			if (empty($data['recurring_type'])) {
				# Normal events
				$row->event_type = 0;
			}
			else {
				# Recurring Event
				if ($data['recurring_type'] == 1 or $data['recurring_type'] == 2){
					# Parent
					$row->event_type = 1;
				} else {
					# Child
					$row->event_type = 2;
				}
			}	
		}
		
		






		# ReSet Group Rates
		$group_rates = array();
		if (!empty($data['registrant_number'])) {
		    foreach ($data['registrant_number'] as $id => $reg_number) {
		        if (empty($reg_number)) {
					continue;
		        }
		
		        $group_rates[$id] = array('number' => $reg_number, 'price' => $data['price'][$id]);
		    }
		}

		$data['group_rates'] = json_encode($group_rates);
		
		if (empty($data['recurring_type'])) {
			# Normal events
			$this->saveNormalEvent($data, $row, $isNew);
		}
		else {
			# Recurring Event
			$this->saveRecurringEvent($data, $row, $isNew);
		}
		
		MiwoEvents::get('utility')->trigger('onMiwoeventsAfterSaveEvent', array($row, $data, $isNew));
    }
    
	















































































































































































































	public function saveNormalEvent(&$data, $row, $isNew) {
		
		$group_rates = array();
		if (!empty($data['registrant_number'])) {
		    foreach ($data['registrant_number'] as $id => $reg_number) {
		        if (empty($reg_number)) {
					continue;
		        }
		
		        $group_rates[$id] = array('number' => $reg_number, 'price' => $data['price'][$id]);
		    }
		}

		$data['group_rates'] = json_encode($group_rates);

		if (!$row->bind($data, array('category_id'))) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
			
		MiwoEvents::get('utility')->trigger('onMiwoeventsBeforeSaveEvent', array(&$row, &$data, &$isNew));

		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		MiwoDatabase::query('DELETE FROM #__miwoevents_event_categories WHERE event_id = '.$row->id);

		$categories = $data['category_id'];
		foreach ($categories AS $category_id) {
		    MiwoDatabase::query("INSERT INTO #__miwoevents_event_categories(event_id, category_id) VALUES({$row->id}, {$category_id})");
		}

		$data['id'] = $row->id;
		
		$this->process = true;
	}
   
	public function saveRecurringEvent(&$data, $row, $isNew) {
		
		$group_rates = array();
        if (!empty($data['registrant_number'])) {
            foreach ($data['registrant_number'] as $id => $reg_number) {
                if (empty($reg_number)) {
                    continue;
                }

                $group_rates[$id] = array('number' => $reg_number, 'price' => $data['price'][$id]);
            }
        }

        $data['group_rates'] = json_encode($group_rates);
        
		if (!$row->bind($data, array('category_id', 'params'))) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		






		# Adjust event start date and event end date
		if ($data['recurring_type'] == 1) {
			$eventDates = MiwoEvents::get('events')->getDailyRecurringEventDates($row->event_date, $data['recurring_end_date'], (int)$data['number_days'], (int)$data['recurring_occurrencies']);
			$row->recurring_frequency = $data['number_days'] ;
		}
        elseif ($data['recurring_type'] == 2) {
			$eventDates = MiwoEvents::get('events')->getWeeklyRecurringEventDates($row->event_date, $data['recurring_end_date'], (int) $data['number_weeks'], (int)$data['recurring_occurrencies'], $data['weekdays']);
			$row->recurring_frequency = $data['number_weeks'] ;
		}
        else {
			# TODO: Monthly recurring
			$eventDates = MiwoEvents::get('events')->getMonthlyRecurringEventDates($row->event_date, $data['recurring_end_date'], (int) $data['number_months'], (int)$data['recurring_occurrencies'], $data['monthdays']);
			$row->recurring_frequency = $data['number_months'] ;
		}
		$eventDuration = abs(strtotime($row->event_end_date) - strtotime($row->event_date));
	
		if (strlen(trim($row->cut_off_date))) {
			$cutOffDuration =  abs(strtotime($row->cut_off_date) - strtotime($row->event_date));
		}
        else {
			$cutOffDuration = 0;
		}

		if (strlen(trim($row->cancel_before_date))) {
			$cancelDuration = abs(strtotime($row->cancel_before_date) - strtotime($row->event_date));
		}
        else {
			$cancelDuration = 0;
		}

		if (count($eventDates) == 0) {
			$this->_mainframe->redirect('index.php?option=com_miwoevents&view=events', MText::_('Invalid recurring setting'));
		}
        else {
			$row->event_date = $eventDates[0];
			$row->event_end_date =  strftime('%Y-%m-%d %H:%M:%S', strtotime($row->event_date) + $eventDuration) ;
		}
		
		# Check ordering of the fields
		if (!$row->id) {
			$where = ' category_id = ' . (int) $row->category_id;
			$row->ordering = $row->getNextOrder( $where );
		}

		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

        MiwoEvents::get('utility')->trigger('onMiwoeventsBeforeSaveEvent', array($row, $data, $isNew));

		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$data['id'] = $row->id;

        MiwoDatabase::query('DELETE FROM #__miwoevents_event_categories WHERE event_id = '.$row->id);
		
		$categories = $data['category_id'];
        foreach ($categories AS $category_id) {
            MiwoDatabase::query("INSERT INTO #__miwoevents_event_categories (event_id, category_id) VALUES ({$row->id}, {$category_id})");
        }

        // MiwoEvents DayLight İssue solved start :)
        if(empty($hideEventDate)){
            $hideEventDate    =  date('H',strtotime($eventDates[0])) ;
            $hideEventEndDate =  date('H',strtotime($eventDates[0]) + $eventDuration);
        }
        // MiwoEvents DayLight İssue solved finish :)

		if (!$this->_id) {
            $n = count($eventDates);
			for ($i = 1; $i < $n ; $i++) {
				$rowChildEvent = clone($row);
				$rowChildEvent->id = 0;
             //   MiwoEvents DayLight İssue solved there fixed:)
                $rowChildEvent->event_date      = date("Y-m-d H:i:s",mktime($hideEventDate, date("i",strtotime($eventDates[$i])), 0, date("m",strtotime($eventDates[$i])), date("d",strtotime($eventDates[$i])), date("Y",strtotime($eventDates[$i]))));
               // $rowChildEvent->event_date = $eventDates[$i];
            //   MiwoEvents DayLight İssue solved there fixed:)
                $rowChildEvent->event_end_date  = date("Y-m-d H:i:s",mktime($hideEventEndDate, date("i",strtotime($eventDates[$i])+ $eventDuration), 0, date("m",strtotime($eventDates[$i])+ $eventDuration), date("d",strtotime($eventDates[$i])+ $eventDuration), date("Y",strtotime($eventDates[$i])+ $eventDuration)));
				//$rowChildEvent->event_end_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($eventDates[$i]) + $eventDuration);

				if ($cutOffDuration) {
					$rowChildEvent->cut_off_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $cutOffDuration);
				}

				if ($cancelDuration) {
					$rowChildEvent->cancel_before_date = strftime('%Y-%m-%d %H:%M:%S', strtotime($rowChildEvent->event_date) - $cancelDuration);
				}

				$rowChildEvent->event_type				= 2;
				$rowChildEvent->parent_id 				= $row->id;
				$rowChildEvent->recurring_type 			= 0;
				$rowChildEvent->recurring_frequency 	= 0;
				$rowChildEvent->weekdays 				= '';
				$rowChildEvent->monthdays 				= '';
				$rowChildEvent->recurring_end_date 		= $this->_db->getNullDate();
				$rowChildEvent->recurring_occurrencies 	= 0;
				$rowChildEvent->created_by 				= $row->created_by;
				$rowChildEvent->group_rates 			= $group_rates;

				$rowChildEvent->check();
				$rowChildEvent->store();

                foreach ($categories AS $category_id) {
                    MiwoDatabase::query("INSERT INTO #__miwoevents_event_categories (event_id, category_id) VALUES ({$rowChildEvent->id}, {$category_id})");
                }
			}
		}
        elseif (isset($data['update_children_event'])) {
			$children = MiwoDatabase::loadResultArray('SELECT id FROM #__miwoevents_events WHERE parent_id='.$row->id);

			if (count($children)) {
				$fieldsToUpdate = array('category_id', 'location_id', 'product_id', 'title',
						'introtext', 'fulltext', 'access',
						'registration_access', 'individual_price', 'event_capacity',
						'cut_off_date', 'registration_type', 'max_group_number', 'group_rates',
                        'notification_emails', 'registrant_email_body',
						'thanks_message', 'params', 'published'
				);

				$rowChildEvent = MiwoEvents::getTable('MiwoeventsEvents');
				foreach ($children as $childId) {
					$rowChildEvent->load($childId);

					foreach ($fieldsToUpdate as $field) {
						$rowChildEvent->$field = $row->$field;
                    }
                    
                    $rowChildEvent->title			= $data['title'];
                    $rowChildEvent->individual_price= $data['individual_price'];
                    $rowChildEvent->event_capacity	= $data['event_capacity'];
                    
                    // registration_type
                    
                    $rowChildEvent->product_id		= $data['product_id'];
					$rowChildEvent->location_id		= $data['location_id'];
					$rowChildEvent->introtext		= $data['introtext'];
					$rowChildEvent->fulltext		= $data['fulltext'];
					
					

					$rowChildEvent->check();
					$rowChildEvent->store();

                    MiwoDatabase::query('DELETE FROM #__miwoevents_event_categories WHERE event_id = '.$rowChildEvent->id);

					foreach ($categories AS $category_id) {
                        MiwoDatabase::query("INSERT INTO #__miwoevents_event_categories (event_id, category_id) VALUES({$rowChildEvent->id}, {$category_id})");
					}
				}
			}
		}
		$this->process = true;
	}

    





}