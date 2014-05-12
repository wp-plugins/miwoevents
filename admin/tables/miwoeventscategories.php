<?php
/**
 * @package		MiwoEvents
 * @copyright	2009-2014 Miwisoft LLC, miwisoft.com
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
# No Permission
defined('MIWI') or die('Restricted Access');

class TableMiwoeventsCategories extends MTable {

    public $id 	 		    = 0;
    public $parent 			= 0;
    public $title 			= '';
    public $alias			= '';
    public $description		= '';
    public $introtext		= '';
    public $fulltext		= '';
    public $ordering		= 0;
    public $access		    = 1;
    public $color_code		= '';
    public $language		= '*';
    public $meta_desc 		= '';
    public $meta_key		= '';
    public $meta_author		= '';
    public $published		= 1;

	public function __construct(&$db) {
		parent::__construct('#__miwoevents_categories', 'id', $db);
	}

    public function check() {
        # Set title
        $this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

        # Set alias
        $this->alias = MApplication::stringURLSafe($this->alias);
        if (empty($this->alias)) {
            $this->alias = MApplication::stringURLSafe($this->title);
        }

        if (!$this->id) {
            $where = '`parent` = ' . (int) $this->parent;
            $this->ordering = $this->getNextOrder($where);
        }
        
    	# Description Exploding
		$delimiter = "<hr id=\"system-readmore\" />";
				
		if(strpos($this->description, $delimiter) == true){
			$exp = explode($delimiter, $this->description);
			$this->introtext	= $exp[0];
			$this->fulltext		= $exp[1];
		} else {
			$this->introtext	= $this->description;
			$this->fulltext		= "";
		}
		
		unset($this->description);
		
        return true;
    }
}