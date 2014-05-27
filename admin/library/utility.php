<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

# Imports
mimport('framework.filesystem.file');
mimport('framework.filesystem.folder');
mimport('framework.filesystem.archive');
mimport('framework.filesystem.path');
mimport('framework.application.component.helper');

# Utility class
class MiwoeventsUtility {

	private static $data = array();

    public function __construct() {
        $this->MiwoeventsConfig = $this->getConfig();
    }

    public function get($name, $default = null) {
        if (!is_array(self::$data) || !isset(self::$data[$name])) {
            return $default;
        }

        return self::$data[$name];
    }

    public function set($name, $value) {
        if (!is_array(self::$data)) {
            self::$data = array();
        }

        $previous = self::get($name);

        self::$data[$name] = $value;

        return $previous;
    }

    public function import($path) {
        require_once(MPATH_WP_PLG.'/miwoevents/admin/' . str_replace('.', '/', $path).'.php');
    }

	public static function is30() {
		static $status;

		if (!isset($status)) {
			if (version_compare(MVERSION, '3.0.0', 'ge')) {
				$status = true;
			}
			else {
				$status = false;
			}
		}

		return $status;
	}

    public function checkRequirements($src = 'all') {
        if ((MIWOEVENTS_PACKAGE == 'booking') and !file_exists(MPATH_WP_PLG.'/miwoshop/site/miwoshop/miwoshop.php')) {
            MError::raiseWarning(404, MText::_('MiwoShop component is not installed. Please, install MiwoShop in order to use MiwoEvents Booking version.'));
            return false;
        }

        return true;
    }

    public static function getConfig() {
   		static $instance;

        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
   			MError::raiseWarning('100', MText::sprintf('MiwoEvents requires PHP 5.2.x to run, please contact your hosting company.'));
   			return false;
   		}

   		if (!is_object($instance)) {
            $reg = new MRegistry(MComponentHelper::getParams('com_miwoevents'));

            $instance = $reg->toObject()->data;
   		}

   		return $instance;
   	}

    public static function getTable($name) {
   		static $tables = array();

   		if (!isset($tables[$name])) {
   			MTable::addIncludePath(MPATH_WP_PLG.'/miwoevents/admin/tables');
   			$tables[$name] = MTable::getInstance($name, 'Table');
   		}

   		return $tables[$name];
   	}
	
	public function getMiwoeventsVersion() {
        static $version;

        if (!isset($version)) {
            $version = $this->getXmlText(MPATH_WP_PLG.'/miwoevents/miwoevents.xml', 'version');
        }

		return $version;
	}

	public function getLatestMiwoeventsVersion() {
        static $version;

        if (!isset($version)) {
            $cache = MFactory::getCache('com_miwoevents', 'output');
            $cache->setCaching(1);

            $version = $cache->get('me_version', 'com_miwoevents');

            if (empty($version)) {
                $version = $this->getRemoteVersion();
                $cache->store($version, 'me_version', 'com_miwoevents');
            }
        }

		return $version;
	}
	
	public function getMenu() {
		mimport('framework.application.menu');
		$options = array();
		
		$menu = MMenu::getInstance('site', $options);
		
		if (MError::isError($menu)) {
			$null = null;
			return $null;
		}
		
		return $menu;
	}

    public function getItemid($vars = array(), $params = null, $with_name = true) {
        $ret = '';

        unset($vars['Itemid']);

        $item = $this->findItemid($vars, $params);

        if (!empty($item->id)) {
            if ($with_name == true) {
                $ret = '&Itemid='.$item->id;
            }
            else {
                $ret = $item->id;
            }

            return $ret;
        }

        return $ret;
    }

    public function findItemid($vars = array(), $params = null) {
        static $items;

        if (!isset($items)) {
            $component = MComponentHelper::getComponent('com_miwoevents');

            $items = $this->getMenu()->getItems('component_id', $component->id);
        }

        if (empty($items)) {
            return null;
        }

        if (empty($vars) or !is_array($vars)) {
            $vars = array();
        }

        $option_found = null;

        foreach ($items as $item) {
            if (!is_object($item) or !isset($item->query)) {
                continue;
            }

            if (count($vars) == 1) {
                return $item;
            }

            if (is_null($option_found)) {
                $option_found = $item;
            }

            if ($this->_checkMenu($item, $vars, $params)) {
                return $item;
            }
        }

        if (!empty($option_found)) {
            return $option_found;
        }

        return null;
    }

    protected function _checkMenu($item, $vars, $params = null) {
        $query = $item->query;

        unset($vars['option']);
        unset($query['option']);

        foreach ($vars as $key => $value) {
            if (is_null($value)) {
                return false;
            }

            if (!isset($query[$key])) {
                return false;
            }

            if ($query[$key] != $value) {
                return false;
            }
        }

        if (!is_null($params)) {
            $menus = $this->getMenu();
            $check = $item->params instanceof MRegistry ? $item->params : $menus->getParams($item->id);

            foreach ($params as $key => $value) {
                if (empty($value)) {
                    continue;
                }

                if ($check->get($key) != $value) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getRecordAlias($id, $type = 'event') {
        $id = intval($id);

        if (empty($id)) {
            return '';
        }

        static $rows = array('event' => array(), 'category' => array(), 'location' => array());

        if (!isset($rows[$type][$id])) {
            $table = $type.'s';
            if ($table == 'categorys') {
                $table = 'categories';
            }

            $_name = MiwoDatabase::loadResult("SELECT alias FROM #__miwoevents_{$table} WHERE id = '{$id}'");

            $rows[$type][$id] = MFilterOutput::stringURLSafe(html_entity_decode($_name, ENT_QUOTES, 'UTF-8'));
        }

        return $rows[$type][$id];
    }

    public function getLocation($id) {
        static $cache = array();

        if (!isset($cache[$id])) {
            $cache[$id] = MiwoDatabase::loadObject("SELECT * FROM #__miwoevents_locations WHERE id = {$id} AND published = 1");
        }

        return $cache[$id];
    }

    public function getCategory($id) {
        static $cache = array();

        if (!isset($cache[$id])) {
            $cache[$id] = MiwoDatabase::loadObject("SELECT * FROM #__miwoevents_categories WHERE id = {$id} AND published = 1");
        }

        if (!is_object($cache[$id])) {
            $row = MiwoEvents::getTable('MiwoeventsCategories');
            $row->load($id);

            $cache[$id] = $row;
        }

        return $cache[$id];
    }

    public function getEventCategory($id) {
        static $cache = array();

        if (!isset($cache[$id])) {
            $cache[$id] = MiwoDatabase::loadObject("SELECT c.* FROM #__miwoevents_categories AS c, #__miwoevents_event_categories AS ec WHERE ec.event_id = {$id} AND c.id = ec.category_id");
        }

        if (!is_object($cache[$id])) {
            $row = MiwoEvents::getTable('MiwoeventsCategories');
            $row->load($id);

            $cache[$id] = $row;
        }

        return $cache[$id];
    }

    public function getCategories($id, $is_event = false) {
        $categories = array();

        if ($is_event == true) {
            $id = $this->getEventCategory($id)->id;
        }

        while ($id != 0) {
            $cat = $this->getCategory($id);

            if (empty($cat)) {
                break;
            }

            $categories[] = $cat;

            $id = $cat->parent;
        }

        return $categories;
    }
	
    public function replaceLoop($search, $replace, $text) {
        $count = 0;
		
		if (!is_string($text)) {
			return $text;
		}
		
		while ((strpos($text, $search) !== false) && ($count < 10)) {
            $text = str_replace($search, $replace, $text);
			$count++;
        }

        return $text;
    }

    public function getAccessLevels() {
        static $levels;

        if (!isset($levels)) {
            $levels = MiwoDatabase::loadObjectList("SELECT id, title FROM #__viewlevels", 'id');
        }

        return $levels;
   	}

    public function getLanguages() {
        mimport('framework.language.helper');
        $langs = MLanguageHelper::getLanguages('lang_code');

        return $langs;
    }
	
	public function storeConfig($MiwoeventsConfig) {
        $reg = new MRegistry($MiwoeventsConfig);
		$config = $reg->toString();
		
		$db = MFactory::getDBO();
		$db->setQuery('UPDATE #__options SET `option_value` = '.$db->Quote($config).' WHERE `option_name` = "miwoevents"');
		$db->query();
	}
	
	public function getParam($text, $param) {
		$params = new MRegistry($text);
		return $params->get($param);
	}
	
	public function storeParams($table, $id, $db_field, $new_params) {
		$row = MiwoEvents::getTable($table);
		if (!$row->load($id)) {
			return false;
		}
		
		$params = new MRegistry($row->$db_field);
		
		foreach ($new_params as $name => $value) {
			$params->set($name, $value);
		}
		
		$row->$db_field = $params->toString();
		
		if (!$row->check()) {
			return false;
		}
		
		if (!$row->store()) {
			return false;
		}
	}
	
	public function setData($table, $id, $db_field, $new_field) {
		$row = MiwoEvents::getTable($table);
		if (!$row->load($id)) {
			return false;
		}
		$row->$db_field = $new_field;	

		if (!$row->check()) {
			return false;
		}
		
		if (!$row->store()) {
			return false;
		}
	}

    public function getPackageFromUpload($userfile) {
        # Make sure that file uploads are enabled in php
        if (!(bool) ini_get('file_uploads')) {
            MError::raiseWarning(100, MText::_('WARNINSTALLFILE'));
            return false;
        }

        # Make sure that zlib is loaded so that the package can be unpacked
        if (!extension_loaded('zlib')) {
            MError::raiseWarning(100, MText::_('WARNINSTALLZLIB'));
            return false;
        }

        # If there is no uploaded file, we have a problem...
        if (!is_array($userfile) ) {
            MError::raiseWarning(100, MText::_('No file selected'));
            return false;
        }

        # Check if there was a problem uploading the file.
        if ( $userfile['error'] || $userfile['size'] < 1 ) {
            MError::raiseWarning(100, MText::_('WARNINSTALLUPLOADERROR'));
            return false;
        }

        # Build the appropriate paths
        $JoomlaConfig = MFactory::getConfig();

        $tmp_dest = $JoomlaConfig->get('tmp_path').'/'.$userfile['name'];

        $tmp_src  = $userfile['tmp_name'];

        # Move uploaded file
        mimport('framework.filesystem.file');
        $uploaded = MFile::upload($tmp_src, $tmp_dest);

        if (!$uploaded) {
            MError::raiseWarning('SOME_ERROR_CODE', '<br /><br />' . MText::_('File not uploaded, please, make sure that your "MiwoEvents => Configuration => Personal ID" and/or the "Global Configuration => Server => Path to Temp-folder" field has a valid value.') . '<br /><br /><br />');
            return false;
        }

        # Unpack the downloaded package file
        $package = self::unpack($tmp_dest);

        # Delete the package file
        MFile::delete($tmp_dest);

        return $package;
    }

    public function unpack($p_filename) {
        # Path to the archive
        $archivename = $p_filename;

        # Temporary folder to extract the archive into
        $tmpdir = uniqid('install_');

        # Clean the paths to use for archive extraction
        $extractdir = MPath::clean(dirname($p_filename).'/'.$tmpdir);
        $archivename = MPath::clean($archivename);

        $package = array();
        $package['dir'] = $extractdir;

        # do the unpacking of the archive
        $package['res'] = MArchive::extract($archivename, $extractdir);

        return $package;
    }

    public function getMiwoeventsIcon($link, $image, $text) {
    	$lang = MFactory::getLanguage();

    	$div_class = 'class="icon-wrapper"';
    	?>
    	<div <?php echo $div_class; ?> style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
    		<div class="icon">
    			<a href="<?php echo $link; ?>">
    				<img src="<?php echo MURL_MIWOEVENTS; ?>/admin/assets/images/<?php echo $image; ?>" alt="<?php echo $text; ?>" />
    				<span><?php echo $text; ?></span>
    			</a>
    		</div>
    	</div>
    	<?php
    }

    public function getPackageFromServer($url) {
        # Make sure that file uploads are enabled in php
        if (!(bool) ini_get('file_uploads')) {
            MError::raiseWarning('1001', MText::_('Your PHP settings does not allow uploads'));
            return false;
        }

        # Make sure that zlib is loaded so that the package can be unpacked
        if (!extension_loaded('zlib')) {
            MError::raiseWarning('1001', MText::_('The PHP extension ZLIB is not loaded, file cannot be unziped'));
            return false;
        }

        # Get temp path
        $JoomlaConfig = MFactory::getConfig();

        $tmp_dest = $JoomlaConfig->get('tmp_path');

        $url = str_replace('http://miwisoft.com/', '', $url);
        $url = str_replace('https://miwisoft.com/', '', $url);
        $url = 'http://miwisoft.com/'.$url;

        # Grab the package
        $data = $this->getRemoteData($url);

        $target = $tmp_dest.'/miwoevents_upgrade.zip';

        # Write buffer to file
        $written = MFile::write($target, $data);

        if (!$written) {
            MError::raiseWarning('SOME_ERROR_CODE', '<br /><br />' . MText::_('File not uploaded, please, make sure that your "MiwoEvents => Configuration => Personal ID" and/or the "Global Configuration=>Server=>Path to Temp-folder" field has a valid value.') . '<br /><br /><br />');
            return false;
        }

        $p_file = basename($target);

        # Was the package downloaded?
        if (!$p_file) {
            MError::raiseWarning('SOME_ERROR_CODE', MText::_('Invalid Personal ID'));
            return false;
        }

        # Unpack the downloaded package file
        $package = self::unpack($tmp_dest.'/'.$p_file);

        if (!$package) {
            MError::raiseWarning('SOME_ERROR_CODE', MText::_('An error occured, please, make sure that your "MiwoEvents => Configuration => Personal ID" and/or the "Global Configuration=>Server=>Path to Temp-folder" field has a valid value.'));
            return false;
        }

        # Delete the package file
        MFile::delete($tmp_dest.'/'.$p_file);

        return $package;
    }

    public function getRemoteVersion() {
        $version = '?.?.?';

        $components = $this->getRemoteData('http://miwisoft.com/index.php?option=com_mijoextensions&view=xml&format=xml&catid=5');

        if (!strstr($components, '<?xml version="1.0" encoding="UTF-8" ?>')) {
            return $version;
        }

        $manifest = simplexml_load_string($components, 'SimpleXMLElement');

        if (is_null($manifest)) {
            return $version;
        }

        $category = $manifest->category;
        if (!($category instanceof SimpleXMLElement) or (count($category->children()) == 0)) {
            return $version;
        }

        foreach ($category->children() as $component) {
            $option = (string)$component->attributes()->option;
            $compability = (string)$component->attributes()->compability;

            if (($option == 'miwoevents') and ($compability == 'wpall')) {
                $version = trim((string)$component->attributes()->version);
                break;
            }
        }

        return $version;
    }
	
	public function getRemoteData($url) {
		$user_agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)";
		$data = false;

		# cURL
		if (extension_loaded('curl')) {
			$process = @curl_init($url);

            @curl_setopt($process, CURLOPT_HEADER, false);
            @curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
            @curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
            @curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            @curl_setopt($process, CURLOPT_AUTOREFERER, true);
            @curl_setopt($process, CURLOPT_FAILONERROR, true);
            @curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
            @curl_setopt($process, CURLOPT_TIMEOUT, 10);
            @curl_setopt($process, CURLOPT_CONNECTTIMEOUT, 10);
            @curl_setopt($process, CURLOPT_MAXREDIRS, 20);

            $data = @curl_exec($process);

            @curl_close($process);
			
			return $data;
		}

		# fsockopen
		if (function_exists('fsockopen')) {
			$errno = 0;
			$errstr = '';
			
			$url_info = parse_url($url);
			if($url_info['host'] == 'localhost')  {
				$url_info['host'] = '127.0.0.1';
			}

			# Open socket connection
			$fsock = @fsockopen($url_info['scheme'].'://'.$url_info['host'], 80, $errno, $errstr, 5);
		
			if ($fsock) {				
				@fputs($fsock, 'GET '.$url_info['path'].(!empty($url_info['query']) ? '?'.$url_info['query'] : '').' HTTP/1.1'."\r\n");
				@fputs($fsock, 'HOST: '.$url_info['host']."\r\n");
				@fputs($fsock, "User-Agent: ".$user_agent."\n");
				@fputs($fsock, 'Connection: close'."\r\n\r\n");
		
				# Set timeout
				@stream_set_blocking($fsock, 1);
				@stream_set_timeout($fsock, 5);
				
				$data = '';
				$passed_header = false;
				while (!@feof($fsock)) {
					if ($passed_header) {
						$data .= @fread($fsock, 1024);
					} else {
						if (@fgets($fsock, 1024) == "\r\n") {
							$passed_header = true;
						}
					}
				}
				
				#  Clean up
				@fclose($fsock);
				
				# Return data
				return $data;
			}
		}

		# fopen
		if (function_exists('fopen') && ini_get('allow_url_fopen')) {
			# Set timeout
			if (ini_get('default_socket_timeout') < 5) {
				ini_set('default_socket_timeout', 5);
			}
			
			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);
			@ini_set('user_agent',$user_agent);
			
			$url = str_replace('://localhost', '://127.0.0.1', $url);
			
			$handle = @fopen($url, 'r');
			
			if ($handle) {
				$data = '';
				while (!feof($handle)) {
					$data .= @fread($handle, 8192);
				}
				
				# Clean up
				@fclose($handle);
			
				# Return data
				return $data;
			}
		}
		
		# file_get_contents
		if (function_exists('file_get_contents') && ini_get('allow_url_fopen')) {
			$url = str_replace('://localhost', '://127.0.0.1', $url);
			@ini_set('user_agent',$user_agent);
			$data = @file_get_contents($url);
			
			# Return data
			return $data;
		}
		
		return $data;
	}
	
	public function getXmlText($file, $variable) {
		mimport('framework.filesystem.file');
        
		$value = '';
		
		if (MFile::exists($file)) {
            $xml = simplexml_load_file($file, 'SimpleXMLElement');

            if (is_null($xml) || !($xml instanceof SimpleXMLElement)) {
                return $value;
            }

            $value = $xml->$variable;
		}
		
		return $value;
    }

    public function trigger($function, $args = array(), $folder = 'miwoevents') {
        mimport('framework.plugin.helper');

        MPluginHelper::importPlugin($folder);
        $dispatcher = MDispatcher::getInstance();
        $result = $dispatcher->trigger($function, $args);

        return $result;
    }

    public function triggerContentPlg($text) {
        $config = $this->getConfig();

        $item = new stdClass();
        $item->id = null;
        $item->rating = null;
        $item->rating_count = null;
        $item->text = $text;

        $params = $config;
        $limitstart = MRequest::getInt('limitstart');

        $this->trigger('onContentPrepare', array('com_miwoevents.event', &$item, &$params, $limitstart), 'content');

        return $item->text;
    }

    public function plgEnabled($folder, $name) {
        static $status = array();

        if (!isset($status[$folder][$name])) {
            mimport('framework.plugin.helper');
            $status[$folder][$name] = MPluginHelper::isEnabled($folder, $name);
        }

        return $status[$folder][$name];
    }

    public function isAjax($output = '') {
        $is_ajax = false;

        $tmpl = MRequest::getWord('tmpl');
        $format = MRequest::getWord('format');

        if ($tmpl == 'component' || $format == 'raw') {
            $is_ajax = true;
        }
        else if (!empty($output)) {
            if ($this->isJson($output)) {

                $is_ajax = true;

                MRequest::setVar('format', 'raw');
                MRequest::setVar('tmpl', 'component');
            }
        }

        return $is_ajax;
    }

    public function isJson($string) {
		$status = false;

		if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
			$a = json_decode($string);
			$status = (json_last_error() == JSON_ERROR_NONE);
		}
		else {
			if (substr($string, 0, 8) == '{"guid":') {
				$status = true;
			}
		}

		return $status;
    }

    public function isMiwosefInstalled() {
        static $status;

        if (!isset($status)) {
            $status = true;

            $file = MPATH_WP_PLG.'/miwosef/admin/library/miwosef.php';
            if (!file_exists($file)) {
                $status = false;

                return $status;
            }

            require_once($file);

            if (Miwosef::getConfig()->mode == 0) {
                $status = false;
            }
        }

        return $status;
    }

    public function isSh404sefInstalled() {
        static $status;

        if (!isset($status)) {
            $status = true;

            $file = MPATH_ADMINISTRATOR.'/components/com_sh404sef/sh404sef.class.php';
            if (!file_exists($file)) {
                $status = false;

                return $status;
            }

            require_once($file);

            if (Sh404sefFactory::getConfig()->Enabled == 0) {
                $status = false;
            }
        }

        return $status;
    }

    public function isJoomsefInstalled() {
        static $status;

        if (!isset($status)) {
            $status = true;

            $file = MPATH_ADMINISTRATOR.'/components/com_sh404sef/classes/config.php';
            if (!file_exists($file)) {
                $status = false;

                return $status;
            }

            require_once($file);

            if (!SEFConfig::getConfig()->enabled) {
                $status = false;
            }
        }

        return $status;
    }

    public function renderDisqus(&$row) {
        $output = new MObject();
        $document = MFactory::getDocument();

        # ----------------------------------- Get plugin parameters -----------------------------------
        $disqus_domain = MiwoEvents::getConfig()->disqus_domain;

        if (!$disqus_domain){
            MError::raiseNotice('', MText::_('Please, enter your Disqus domain into MiwoEvents config.'));
            return $output;
        }
        else {
            $disqus_domain = str_replace(array('http://','.disqus.com/','.disqus.com'), array('','',''), $disqus_domain);
        }

        # ----------------------------------- Prepare elements -----------------------------------
        # Article URLs
        $websiteURL = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https://".$_SERVER['HTTP_HOST'] : "http://".$_SERVER['HTTP_HOST'];
        $itemURL = $_SERVER['REQUEST_URI'];


        $itemURLbrowser = explode("#",$websiteURL.$_SERVER['REQUEST_URI']);
        $itemURLbrowser = $itemURLbrowser[0];

        # Article URL assignments
        $output->itemURL 			= $websiteURL.$itemURL;
        $output->itemURLrelative 	= $itemURL;
        $output->itemURLbrowser		= $itemURLbrowser;
        $output->disqusIdentifier   = substr(md5($disqus_domain),0,10).'_event_id'.$row->id;

        # Fetch elements specific to the "article" view only
        $output->comments = "
            <div id=\"disqus_thread\"></div>
            <script type=\"text/javascript\">
                //<![CDATA[
                var disqus_shortname = '".$disqus_domain."';
                var disqus_url = '".$output->itemURL."';
                var disqus_identifier = '".substr(md5($disqus_domain),0,10)."_event_id".$row->id."';
                (function() {
                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                    dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                })();
                //]]>
            </script>
            <noscript>
                <a href=\"http://".$disqus_domain.".disqus.com/?url=ref\">".MText::_("JW_DISQUS_VIEW_THE_DISCUSSION_THREAD")."</a>
            </noscript>
            ";

        # ----------------------------------- Render the output -----------------------------------
        # Append head includes only when the document is in HTML mode
        if (MRequest::getCmd('format') == 'html' or MRequest::getCmd('format') == '') {
            MHtml::_('behavior.framework');

            if (!defined('JW_DISQUS_JS')){
                $document->addScriptDeclaration("
                    window.addEvent('load',function(){
                        // Smooth Scroll
                        new SmoothScroll({
                            duration: 500
                        });
                    });
                ");

                define('JW_DISQUS_JS', true);
            }
        }

        return $output;
    }

    public function getRadioList($name, $selected, $attrs = '', $id = false, $old_style = false, $text_1 = 'MYES', $text_0 = 'MNO') {
        if (MiwoEvents::is30()) {
            if (empty($attrs)) {
                $attrs = 'class="inputbox" size="2"';
            }

            if ($old_style == true) {
                $arr = array(MHtml::_('select.option', 2, MText::_($text_1)), MHtml::_('select.option', 1, MText::_($text_0)));
            }
            else {
                $arr = array(MHtml::_('select.option', 1, MText::_($text_1)), MHtml::_('select.option', 0, MText::_($text_0)));
            }

            $output = MHtml::_('select.radiolist', $arr, $name, $attrs, 'value', 'text', (int) $selected, $id);

            $html  = '<fieldset class="radio btn-group">';
            $html .= str_replace(array('<div class="controls">', '</div>'), '', $output);
            $html .= '</fieldset>';
        }
        else {
            $html = MHtml::_('select.booleanlist', $name, 'class="inputbox"', $selected);
        }

        return $html;
    }
    
    # For development
    public function vardump($param,$exit = NULL) {
    	echo "<pre>";
    	var_dump($param);
    	echo "</pre>";
    	if ($exit == "yes"){ exit(); } 
    }

    public function deleteAttenders($event_id){
        $ids = $this->getAttenderIdsFromSession($event_id);
        if (empty($ids)) {
            return;
        }

        MiwoDatabase::query("DELETE FROM #__miwoevents_attenders WHERE id IN ($ids)");

        unset($_SESSION['meregid']);
	}
	
	public function getAttenderIdsFromSession($event_id) {
		$ids = 0;
		
		if (empty($_SESSION['meregid']) or empty($_SESSION['meregid'][$event_id])) {
            return $ids;
        }

        $_ids = json_decode($_SESSION['meregid'][$event_id]);
        $ids = implode(',', $_ids);

        return $ids;
	}

    public function buildCategoryDropdown($selected, $name="parent", $onChange=true) {
        $db = MFactory::getDBO();
        $db->setQuery("SELECT id, parent, parent AS parent_id, title FROM #__miwoevents_categories");
        $rows = $db->loadObjectList();

        $children = array();
        if ($rows) {
            # first pass - collect children
            foreach ($rows as $v) {
                $pt = $v->parent;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push( $list, $v );
                $children[$pt] = $list;
            }
        }

        $list = MHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

        $options = array();
        $options[] = MHtml::_('select.option', '0', MText::_('COM_MIWOEVENTS_SELECT_CATEGORY'));
        foreach ($list as $item) {
            $options[] = MHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename);
        }

        if ($onChange) {
            return MHtml::_('select.genericlist', $options, $name, array(
                                                                    'option.text.toHtml' => false ,
                                                                    'option.text' => 'text',
                                                                    'option.value' => 'value',
                                                                    'list.attr' => 'class="inputbox" ',
                                                                    'list.select' => $selected)
                            );
        }
        else {
            return MHtml::_('select.genericlist', $options, $name, array(
                                                                    'option.text.toHtml' => false ,
                                                                    'option.text' => 'text',
                                                                    'option.value' => 'value',
                                                                    'list.attr' => 'class="inputbox" ',
                                                                    'list.select' => $selected)
                            );
        }
    }

    public function buildParentCategoryDropdown($row) {
   		$db = MFactory::getDBO();

   		$sql = "SELECT id, parent, parent AS parent_id, title FROM #__miwoevents_categories";

        if ($row->id) {
   			$sql .= ' WHERE id != '.$row->id;
        }

   		$db->setQuery($sql);
   		$rows = $db->loadObjectList();

        if (!$row->parent) {
   			$row->parent = 0;
   		}

   		$children = array();
   		if ($rows) {
   			# first pass - collect children
   			foreach ($rows as $v) {
   				$pt = $v->parent;
   				$list = @$children[$pt] ? $children[$pt] : array();
   				array_push($list, $v);
   				$children[$pt] = $list;
   			}
   		}

   		$list = MHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

   		$options = array();
   		$options[] = MHtml::_('select.option', '0', MText::_('COM_MIWOEVENTS_NO_PARENT'));
   		foreach ($list as $item) {
   			$options[] = MHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename);
   		}

   	    return MHtml::_('select.genericlist', $options, 'parent', array(
                                                                      'option.text.toHtml' => false,
                                                                      'option.text' => 'text',
                                                                      'option.value' => 'value',
                                                                      'list.attr' => ' class="inputbox" ',
                                                                      'list.select' => $row->parent)
                           );
   	}

    public function getGDVersion($user_ver = 0) {
        if (!extension_loaded('gd')) {
            return 0;
        }

        static $gd_ver = 0;

        # just accept the specified setting if it's 1.
        if ($user_ver == 1) {
            $gd_ver = 1;
            return $gd_ver;
        }

        # use static variable if function was cancelled previously.
        if (($user_ver != 2) and ($gd_ver > 0)) {
            return $gd_ver;
        }

        # use the gd_info() function if posible.
        if (function_exists('gd_info')) {
            $ver_info = gd_info();
            $match = null;
            preg_match('/\d/', $ver_info['GD Version'], $match);
            $gd_ver = $match[0];

            return $gd_ver;
        }

        # if phpinfo() is disabled use a specified / fail-safe choice...
        if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
            if ($user_ver == 2) {
                $gd_ver = 2;
                return $gd_ver;
            }
            else {
                $gd_ver = 1;
                return $gd_ver;
            }
        }

        # ...otherwise use phpinfo().
        ob_start();
        phpinfo(8);
        $info = ob_get_contents();
        ob_end_clean();
        $info = stristr($info, 'gd version');
        $match = null;
        preg_match('/\d/', $info, $match);
        $gd_ver = $match[0];

        return $gd_ver;
    }

    public function resizeImage($srcFile, $desFile, $thumbWidth, $thumbHeight, $quality) {
        $app = MFactory::getApplication();

        $imgTypes = array( 1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF');
        $imgInfo = getimagesize($srcFile);

        if ($imgInfo == null) {
            $app->enqueueMessage(MText::_('COM_MIWOEVENTS_IMAGE_NOT_FOUND', 'error'));
            return false;
        }

        $type = strtoupper($imgTypes[$imgInfo[2]]) ;
        $gdSupportedTypes = array('JPG', 'PNG', 'GIF');

        if (!in_array($type, $gdSupportedTypes)) {
            $app->enqueueMessage(MText::_('COM_MIWOEVENTS_ONLY_SUPPORT_TYPES'), 'error');
            return false;
        }

        $srcWidth = $imgInfo[0];
        $srcHeight = $imgInfo[1];

        //Should canculate the ration
        $ratio =  max($srcWidth/$thumbWidth, $srcHeight/$thumbHeight , 1.0);
        $desWidth = (int) $srcWidth / $ratio;
        $desHeight = (int) $srcHeight / $ratio;

        $gd_version = $this->getGDVersion();

        if ($gd_version <= 0) {
            //Simply copy the source to target folder
            mimport('framework.filesystem.file');
            MFile::copy($srcFile, $desFile);
            return false;
        }
        else if ($gd_version == 1) {
            if ($type == 'JPG') {
                $srcImage =  imagecreatefromjpeg($srcFile);
            }
            elseif ($type == 'PNG') {
                $srcImage = imagecreatefrompng($srcFile);
            }
            else {
                $srcImage = imagecreatefromgif($srcFile);
            }

            $desImage = imagecreate($desWidth, $desHeight);

            imagecopyresized($desImage, $srcImage, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight);
            imagejpeg($desImage, $desFile, $quality);
            imagedestroy($srcImage);
            imagedestroy($desImage);
        }
        else {
            if (!function_exists('imagecreatefromjpeg')) {
                echo MText::_('GD_LIB_NOT_INSTALLED');
                return false;
            }

            if (!function_exists('imagecreatetruecolor')) {
                echo MText::_('GD2_LIB_NOT_INSTALLED');
                return false ;
            }

            if ($type == 'JPG') {
                $srcImage =  imagecreatefromjpeg($srcFile);
            }
            elseif ($type == 'PNG') {
                $srcImage = imagecreatefrompng($srcFile);
            }
            else {
                $srcImage = imagecreatefromgif($srcFile);
            }

            if (!$srcImage) {
                echo MText::_('JA_INVALID_IMAGE');
                return false;
            }

            $desImage = imagecreatetruecolor($desWidth, $desHeight);

            imagecopyresampled($desImage, $srcImage, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight);
            imagejpeg($desImage, $desFile, $quality);
            imagedestroy($srcImage);
            imagedestroy($desImage);
        }

        return true;
    }

    public function getAttachmentList($selected) {
   	    mimport('framework.filesystem.folder') ;
   	    $path = MPATH_MEDIA.'/miwoevents';

   		$options = array();
   		$options[] = MHtml::_('select.option', '', MText::_('COM_MIWOEVENTS_SELECT_ATTACHMENT'));

        $files = MFolder::files($path, strlen(trim(@$this->MiwoeventsConfig->attachment_file_types)) ? @$this->MiwoeventsConfig->attachment_file_types : 'bmp|gif|jpg|png|swf|zip|doc|pdf|xls');

        foreach ($files as $file) {
   			$options[] = MHtml::_('select.option', $file, $file);
   		}

   		return MHtml::_('select.genericlist', $options, 'attachment', 'class="inputbox"', 'value', 'text', $selected);
   	}

    public function getUserInputbox($user_id, $field_name = 'user_id') {
   		# Initialize variables.
   		$html = array();
   		$link = MRoute::_('index.php?option=com_miwoevents&view=users&layout=modal&field=user_id');

   		# Initialize some field attributes.
   		$attr = ' class="inputbox"';

   		# Load the modal behavior script.
   		MHtml::_('behavior.modal', 'a.modal_user_id');

   		# Build the script.
   		$script = array();
   		$script[] = '	function jSelectUser_user_id(id, title) {';
   		$script[] = '		var old_id = document.getElementById("'.$field_name.'").value;';
   		$script[] = '		if (old_id != id) {';
   		$script[] = '			document.getElementById("'.$field_name.'").value = id;';
   		$script[] = '			document.getElementById("user_id_name").value = title;';
   		$script[] = '		}';
   		$script[] = '		SqueezeBox.close();';
   		$script[] = '	}';

   		# Add the script to the document head.
   		MFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

   		# Load the current username if available.
   		$table = MTable::getInstance('user');
   		if ($user_id) {
   			$table->load($user_id);
   		}
   		else {
   			$table->user_login = '' ;
   		}

   		# Create a dummy text field with the user name.
   		$html[] = '<div class="fltlft">';
   		$html[] = '<input type="text" id="user_id_name"' . ' value="' . htmlspecialchars($table->user_login, ENT_COMPAT, 'UTF-8') . '"' . ' disabled="disabled"' . $attr . ' />&nbsp;&nbsp;';
   		$html[] = '</div>';

   		# Create the user select button.
   		$html[] = '<div class="button2-left">';
   		$html[] = '<div class="blank">';
   		$html[] = '<a class="modal_user_id button-primary" title="' . MText::_('MLIB_FORM_CHANGE_USER') . '"' . ' href="' . $link . '"' . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
   		$html[] = '	' . MText::_('MLIB_FORM_CHANGE_USER') . '</a>';
   		$html[] = '</div>';
   		$html[] = '</div>';

   		# Create the real field, hidden, that stored the user id.
   		$html[] = '<input type="hidden" id="'.$field_name.'" name="'.$field_name.'" value="'.$user_id.'" />';

   		return implode("\n", $html);
   	}

    public function getAmount($amount, $currency_symbol = null) {
   	    $decimals = isset($this->MiwoeventsConfig->decimals) ?  $this->MiwoeventsConfig->decimals : 2;
        $dec_point = isset($this->MiwoeventsConfig->dec_point) ? $this->MiwoeventsConfig->dec_point : '.';
        $thousands_sep = isset($this->MiwoeventsConfig->thousands_sep) ? $this->MiwoeventsConfig->thousands_sep : ',';
        $symbol = (MIWOEVENTS_PACKAGE != 'booking' and !empty($currency_symbol)) ? $currency_symbol : $this->MiwoeventsConfig->currency_symbol;

        return $this->MiwoeventsConfig->currency_position ? (number_format($amount, $decimals, $dec_point, $thousands_sep).$symbol) : ($symbol.number_format($amount, $decimals, $dec_point, $thousands_sep));
   	}

    public function getCurrencies(){
        return MiwoDatabase::loadObjectList("SELECT code, CONCAT(symbol_left, symbol_right) AS symbol, title FROM #__miwoshop_currency WHERE status = 1 ORDER BY title");
    }

    public function getCurrencySelectbox($selected_currency = null,$type = null) {
        static $list;
        
        if (!isset($list)) {
            $currencies = $this->getCurrencies();

            if (empty($selected_currency)) {
				if (!isset($this->MiwoeventsConfig->currency_symbol) ){
					$selected_currency = "$";
				} else {
                	$selected_currency = $this->MiwoeventsConfig->currency_symbol;
                }
            }

            $options = array();
            foreach ($currencies as $currency){
                $currency_title = $currency->symbol." - ".$currency->title;
                $options[] = MHtml::_('select.option', $currency->symbol, $currency_title);
            }
            
            if ($type == "config") {
				$list = MHtml::_('select.genericlist', $options, 'mform[currency_symbol]', ' class="inputbox" ', 'value', 'text', $selected_currency);
			} else {
            	$list = MHtml::_('select.genericlist', $options, 'currency_symbol', ' class="inputbox" ', 'value', 'text', $selected_currency);
            }
        }

        return $list;
    }

    public function getTaxClassesSelectBox($selected_tax_class = 0){
        static $list;

        if (!isset($list)) {
           $tax_classes = $this->getTaxClasses();

            $options = array();
            $options[] = MHtml::_('select.option', '0', MText::_( 'COM_MIWOEVENTS_SELECT_TAX_CLASS'));
            foreach ($tax_classes as $tax_class){
               $options[] = MHtml::_('select.option', $tax_class->id, $tax_class->title);
            }

            $list = MHtml::_('select.genericlist', $options, 'tax_class', ' class="inputbox" ', 'value', 'text', $selected_tax_class);

        }

       return $list;
    }

    public function getTaxClasses(){
        return MiwoDatabase::loadObjectList("SELECT tax_class_id as id, title FROM #__miwoshop_tax_class ORDER BY title");
    }
	
    public function backupDB($query, $file_name, $fields, $line) {
		$sql_data = '';
		
		$rows = MiwoDatabase::loadObjectList($query);

		if (!empty($rows)) {
			foreach ($rows as $row) {
				$values = array();
				foreach ($fields as $field) {
					if (isset($row->$field)) {
						$values[] = "'".self::_cleanBackupFields($row->$field)."'";
					} else {
						$values[] = "''";
					}
				}
				$sql_data .= $line." VALUES (".implode(', ', $values).");\n";;
			}
		} else {
			return false;
		}

		if(!headers_sent()) {
			// flush the output buffer
			while(ob_get_level() > 0) {
				ob_end_clean();
			}

			ob_start();
			header ('Expires: 0');
			header ('Last-Modified: '.gmdate ('D, d M Y H:i:s', time()) . ' GMT');
			header ('Pragma: public');
			header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header ('Accept-Ranges: bytes');
			header ('Content-Length: ' . strlen($sql_data));
			header ('Content-Type: Application/octet-stream');
			header ('Content-Disposition: attachment; filename="'.$file_name.'"');
			header ('Connection: close');

			echo($sql_data);

			ob_end_flush();
			die();
			return true;
		} else {
			return false;
		}
    }
	
	// Clean backup fields
    public function _cleanBackupFields($text) {
		$text = str_replace(array('\r\n', '\r', '\n', '\t', '\n\n', '`', '”', '“', '¿', '\0', '\x0B'), ' ', $text);
		$text = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', ' ', $text);
		$text = preg_replace('/\s/u', ' ', $text);
		$text = stripslashes($text);
		$text = self::replaceSpecialChars($text);
		$text = str_replace('\\', '\\', $text);
		
		return $text;
	}
	
	// Replace some special chars
    public function replaceSpecialChars($text, $reverse = false) {
		if (is_string($text)) {
			if (!$reverse) {
				$text = str_replace("\'", "'", $text);
				$text = addslashes($text);
			} else {
				$text = stripslashes($text);
			}
		}
		
		return $text;
	}
	
	public function findOption() {
		$option = strtolower(MRequest::getCmd('option'));

		$user = MFactory::getUser();
		if (($user->get('guest')) || !$user->authorise('core.login.admin')) {
			$option = 'com_login';
		}

		if (empty($option)) {
			$option = 'com_cpanel';
		}

		MRequest::setVar('option', $option);
		return $option;
	}
	
	public function cleanText($text) {
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('#<script[^>]*>.*?</script>#si', ' ', $text);
        $text = preg_replace('#<style[^>]*>.*?</style>#si', ' ', $text);
        $text = preg_replace('#<!.*?(--|]])>#si', ' ', $text);
        $text = preg_replace('#<[^>]*>#i', ' ', $text);
        $text = preg_replace('/{.+?}/', '', $text);
        $text = preg_replace("'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text);

        $text = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', ' ', $text);

        $text = preg_replace('/\s\s+/', ' ', $text);
        $text = preg_replace('/\n\n+/s', ' ', $text);
        $text = preg_replace('/\s/u', ' ', $text);

        $text = strip_tags($text);

        return $text;
    }

    public function cleanUrl($url) {
        $url = $this->cleanText($url);

        $bad_chars = array('#', '>', '<', '\\', '="', 'px;', 'onmouseover=');
        $url = trim(str_replace($bad_chars, '', $url));

        mimport('framework.filter.input');
        MFilterInput::getInstance(array('br', 'i', 'em', 'b', 'strong'), array(), 0, 0, 1)->clean($url);

        return $url;
    }
	public function getActiveUrl() {
        return $this->cleanUrl(MFactory::getURI()->toString());
    }
	
}