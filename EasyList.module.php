<?php
#-------------------------------------------------------------------------
#
# Author: Ben Malen, <ben@conceptfactory.com.au>
# Co-Maintainer: Simon Radford, <simon@conceptfactory.com.au>
# Web: www.conceptfactory.com.au
#
#-------------------------------------------------------------------------
#
# Maintainer since 2011: Jonathan Schmid, <hi@jonathanschmid.de>
# Web: www.jonathanschmid.de
#
#-------------------------------------------------------------------------
#
# Some wackos started destroying stuff since 2012: 
#
# Tapio Löytty, <tapsa@orange-media.fi>
# Web: www.orange-media.fi
#
# Goran Ilic, <uniqu3e@gmail.com>
# Web: www.ich-mach-das.at
#
#-------------------------------------------------------------------------
# ListIt2 become EasyList due to the departure of the wackos in summer 2014.
#
# Jean-Christophe ghio <jcg@interphacepro.com>
#
#-------------------------------------------------------------------------
# EasyList is a CMS Made Simple module that enables the web developer to create
# multiple lists throughout a site. It can be duplicated and given friendly
# names for easier client maintenance.
#
#-------------------------------------------------------------------------

/*****************************************************************
 GLOBAL DEFINES
*****************************************************************/

$config = cmsms()->GetConfig();

define('EASYLIST', 'EasyList');
define('EASYLIST_VALUE_SEPARATOR', ',');
define('EASYLIST_CONFIG_FILE', 'listit2_config.php');
define('EASYLIST_FRAMEWORK_PATH', cms_join_path($config['root_path'],'modules',EASYLIST,'framework'));
define('EASYLIST_TEMPLATE_PATH', cms_join_path(EASYLIST_FRAMEWORK_PATH,'templates'));
define ('EZVERSION', '1.2-beta1');
/*****************************************************************
 MAIN CLASS
*****************************************************************/

class EasyList extends CMSModule
{
	#---------------------
	# Attributes
	#---------------------

	public $prefix; // <- Move to variables
	public $use_hints; // <- Move to variables
	private $_config;
	protected $_fielddef_cache;
	protected $_item_cache;

	#---------------------
	# Magic methods
	#---------------------		
	
	public function __construct()
	{	
		spl_autoload_register(array(&$this, '_autoloader'));
	
		$this->_item_cache 	= new EasyListCache(EasyListItemOperations::$identifiers);
		$this->_config 		= new EasyListConfig($this);
		$this->prefix 		= $this->GetPreference('url_prefix', munge_string_to_url($this->GetName(), true));
		parent::__construct();	
	}
	
	#---------------------
	# Internal autoloader
	#---------------------	

	private final function _autoloader($classname)
	{	
		$parts = explode('\\', $classname);
		$classname = end($parts);	
	
		$fn = $this->GetModulePath()."/lib/class.{$classname}.php";
		if(file_exists($fn)) {
		
			require_once($fn);
		}	
	}	
	
	#---------------------
	# Module api methods
	#---------------------		
	
	public function GetName()
	{
		return EASYLIST;
	}

	public function GetFriendlyName()
	{
		return EASYLIST;
	}

	public function GetVersion()
	{
		return EZVERSION;
	}

	public function GetHelp()
	{
		$smarty = cmsms()->GetSmarty();
		$config = cmsms()->GetConfig();
		
		$smarty->assign('parent_name', $this->GetName());		
		$smarty->assign('root_url', $config['root_url']);
		$smarty->assign('mod', $this);

		return $this->ProcessTemplate('help.tpl');
	}	

	public function GetAuthor()
	{
		return 'jean-Christophe Ghio';
	}

	public function GetAuthorEmail()
	{
		return 'jcg@interphacepro.com';
	}

	public function GetChangeLog()
	{
		return @file_get_contents(dirname(__FILE__).'/changelog.html');
	}

	public function IsPluginModule()
	{
		return false;
	}

	public function HasAdmin()
	{
		return true;
	}

	public function GetAdminSection()
	{
		return 'extensions';
	}

	public function GetAdminDescription()
	{
		return $this->ModLang('moddescription');
	}

	public function VisibleToAdminUser()
	{
		return $this->CheckPermission('Modify Site Preferences');
	}

	public function MinimumCMSVersion()
	{
		return '2.0-beta2';
	}

	public function InstallPostMessage()
	{
		return $this->ModLang('postinstall');
	}

	public function UninstallPostMessage()
	{
		return $this->ModLang('postuninstall');
	}
	
	function GetEventDescription( $eventname )
	{
		return $this->ModLang('eventdesc_' . $eventname);
	}

	function GetEventHelp( $eventname )
	{
		return $this->ModLang('eventhelp_' . $eventname);
	}	

    public function GetHeaderHTML()
    {
		// SSL check (Hopefully core would do this soon...)
		$use_ssl = false;
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')
			$use_ssl = true;	
	
        $config = cmsms()->GetConfig();
		$globals_js = ($use_ssl?$config['ssl_url']:$config['root_url']). '/modules/'. EASYLIST .'/lib/js/';
		$globals_css = ($use_ssl?$config['ssl_url']:$config['root_url']). '/modules/'. EASYLIST .'/lib/css/';		

        $tmpl = <<<EOT
<link type="text/css" rel="stylesheet" href="{$globals_css}easylist-globals.css" />	
<script type="text/javascript" src="{$globals_js}easylist-globals.js"></script>
EOT;
        return $tmpl;
    }	

	public function DoAction($name,$id,$params,$returnid='')
	{
		global $CMS_ADMIN_PAGE;
		$config = cmsms()->GetConfig();
		$smarty = cmsms()->GetSmarty();
		$db = cmsms()->GetDb();
	
		$smarty->assign_by_ref('mod', $this);
		$smarty->assign('actionid', $id);
		$smarty->assign('returnid', $returnid);
		
		if($CMS_ADMIN_PAGE) {
		
			$themeObject = cms_utils::get_theme_object();
			$smarty->assign_by_ref('themeObject', $themeObject);		
		}
				
		if ($name != '') {
	
			$contents = '';		
			$name = preg_replace('/[^A-Za-z0-9\-_+]/', '', $name);

			if($CMS_ADMIN_PAGE)
				$contents .= '<div class="listit2-admin-wrapper">';
			
			$filename = $this->GetModulePath() . '/action.' . $name . '.php';
			if (@is_file($filename)) {
						
				ob_start();	
				include($filename);
				$contents .= ob_get_contents();
				ob_end_clean();	
			}			
				
			if($CMS_ADMIN_PAGE)
				$contents .= '</div>';

			echo $contents;			
		}		
	}
	
	function Upgrade($oldversion, $newversion)
	{
		$config = cmsms()->GetConfig();
		$smarty = cmsms()->GetSmarty();
		$db = cmsms()->GetDb();
		
		$response = FALSE;
	
		$filename = $this->GetModulePath() . '/method.upgrade.php';
		if (@is_file($filename)) {
			
			$response = FALSE;
			$res = include($filename);
			if($res == 1 || $res == '') $response = TRUE;
		}

		return $response;
	}

	function Install()
	{
		$config = cmsms()->GetConfig();
		$smarty = cmsms()->GetSmarty();
		$db = cmsms()->GetDb();
		
		$response = FALSE;
	
		$filename = $this->GetModulePath() . '/method.install.php';
		if (@is_file($filename)) {
			
			$res = include($filename);
			if($res == 1 || $res == '') {
				$response = FALSE;
			} else {
				$response = $res;
			}
		}	
	
		return $response;
	}
	
	function Uninstall()
	{
		$config = cmsms()->GetConfig();
		$smarty = cmsms()->GetSmarty();
		$db = cmsms()->GetDb();
		
		$response = FALSE;
		
		$filename = $this->GetModulePath() . '/method.uninstall.php';
		if (@is_file($filename)) {
			
			$res = include($filename);
			if($res == 1 || $res == '') {
				$response = FALSE;
			} else {
				$response = $res;
			}
		}	
	
		return $response;
	}	

	#---------------------
	# Manipulation methods
	#--------------------- 
	
	public function ModLang()
	{	
		$args = func_get_args();
	
		return EasyListLangOperations::li_lang_from_realm(EASYLIST, $args, $this->GetName());
	}
	
	public function ModProcessTemplate($tpl_name)
	{
		$ok = (strpos($tpl_name, '..') === false);
		if (!$ok) return;

		$smarty = cmsms()->GetSmarty();	
		$config = cmsms()->GetConfig();
		$result = '';
		
		$oldcache = $smarty->caching;
		$smarty->caching = $this->can_cache_output() ? Smarty::CACHING_LIFETIME_CURRENT : Smarty::CACHING_OFF;

		$files = array();
		$files[] = cms_join_path($config['root_path'],'module_custom',$this->GetName(),'templates',$tpl_name);
		$files[] = cms_join_path($this->GetModulePath(),'templates',$tpl_name);
		$files[] = cms_join_path(EASYLIST_TEMPLATE_PATH,$tpl_name);

		foreach($files as $file) {
		
			if(is_readable($file)) {

				$result = $smarty->fetch($file);
				break;
			}
		}
		
		$smarty->caching = $oldcache;

		return $result;		
	}

	public function ModGetTemplateFromFile($tpl_name)
	{
		$ok = (strpos($tpl_name, '..') === false);
		if (!$ok) return;

		$config = cmsms()->GetConfig();
		
		$template = cms_join_path(EASYLIST_TEMPLATE_PATH,$tpl_name.'.tpl');
		
		if (is_readable($template)) {
			return @file_get_contents($template);
		} else {
			return null;
		}
	}
  public static function reset_templates_defaults(CmsLayoutTemplateType $type)
  {
    $fn = null;
    switch( $type->get_name() ) {
    case 'summary':
      $fn = 'fe_summary_default.tpl';
      break;

    case 'detail':
      $fn = 'fe_detail_default.tpl';
      break;

    case 'search':
      $fn = 'fe_search_default.tpl';
      break;
	  
    case 'archive':
      $fn = 'fe_archive_default.tpl';
      break;

    case 'category':
      $fn = 'fe_category_default.tpl';
    }

    $fn = cms_join_path(EASYLIST_TEMPLATE_PATH,$fn);
    if( file_exists($fn) ) return @file_get_contents($fn);
  }
  public static function template_type_lang_callback($str)
  {
      $mod = cms_utils::get_module(LISTIT);
    if( is_object($mod) ) return $mod->Lang('type_'.$str);
  }
	
	
	#---------------------
	# Instance methods
	#--------------------- 		

	/**
	 * List Modules
	 * NOTE: listit2_utils::array_to_object() not called with purpose.
	 * NOTE: Make this static method.
	 * @internal
	 * @return array of objects
	 */		
	final public function ListModules()
	{
		$db = cmsms()->GetDb();	
		
		$entryarray = array();
	
		$query = "SELECT * FROM " . cms_db_prefix() . "module_easylist_instances ORDER BY module_id";	
		$dbr = $db->Execute($query);

		while ($dbr && $row = $dbr->FetchRow()) {
		
			$obj = new stdClass();
		
			$obj->module_id = $row['module_id'];
			$obj->module_name = $row['module_name'];
		
			$entryarray[$row['module_id']] = $obj;
		}
	
		return $entryarray;	
	}	

	/**********************************************************************
	* FRAMEWORK STARTS
	**********************************************************************/
	
	#---------------------
	# Cross module support
	#--------------------- 	
	
	// CGE hint system support	
	public function LoadModuleHints(&$params)
	{
		$this->use_hints = cms_utils::get_app_data('__MODULE_HINT__'.$this->GetName());		
		if(count($this->use_hints) > 0) {
			
			foreach((array)$this->use_hints as $key=>$value) {
			
				if(isset($params[$key])) continue;
				$params[$key] = $value;
			}
		}	
	}
	
	#---------------------
	# Search methods
	#--------------------- 

	public function SearchResult($returnid, $item_id, $attr = '')
	{
		$result = array();

		if($this->GetPreference('reindex_search', 0)) {		
			
			if ($attr == 'title')
			{
				$db = cmsms()->GetDb();
				$row = $db->GetRow('SELECT alias, title FROM ' . cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_item WHERE active = 1 AND item_id = ?', array($item_id));

				if ($row)
				{
					//0 position is the prefix displayed in the list results.
					$result[0] = $this->GetFriendlyName();

					//1 position is the title
					$result[1] = $row['title'];

					//2 position is the URL to the item.
					$inline = $this->GetPreference('display_inline', 0) == 1;

					$detailtemplate = $this->GetPreference($this->_GetModuleAlias() . '_default_detail_template');
					
					$params = array('item' => $row['alias'], 'detailtemplate' => $detailtemplate);				
					$this->LoadModuleHints($params);

					$result[2] = $this->CreatePrettyLink('cntnt01', 'detail', $returnid, '', $params, '', true, $inline);
				}
			}
		}
		
		return $result;
	}
	
    function SearchReindex($module)
    {			
		if($this->GetPreference('reindex_search', 0)) {	
	
			$db = cmsms()->GetDb();
			$query = 'SELECT * FROM '.cms_db_prefix(). 'module_' . $this->_GetModuleAlias() . '_item ORDER BY item_id';
			$result = $db->Execute($query);
			while ($result && !$result->EOF)
			{
				if ($result->fields['active'] == '1')
				{
					$module->AddWords($this->GetName(), $result->fields['item_id'], 'title', $result->fields['title'], NULL);
				}
				$result->MoveNext();
			}
		}
    }		

	#---------------------
	# Template methods
	#--------------------- 
	
	public function GetFileTemplatesByType($type)
	{
		$result = array();	
		
		if ($handle = opendir(EASYLIST_TEMPLATE_PATH)) {
		
			while (false !== ($entry = readdir($handle))) {
			
				if ($entry == "." || $entry == "..") continue;
				
				if(startswith($entry, 'fe_')) {
			
					list($tpl_prefix, $tpl_type, $tpl_name) = explode('_', $entry, 3);
					if($tpl_type == $type) {
					
						$split_file = explode('.', $entry);
						$split_name = explode('.', $tpl_name);
						
						$result[$split_name[0]] = $split_file[0];
					}
				}
			}
			
			closedir($handle);
		}		
	
		ksort($result);
	
		return $result;
	
	}
	
	#---------------------
	# Fielddef methods
	#--------------------- 	
	
	// Get Field Definitions (Review this method ASAP)
	public function GetFieldDefs($include_list = array())
	{			
		// Load from cache
		if(isset($this->_fielddef_cache)) {
			
			$fielddefs = array();
			foreach($this->_fielddef_cache as $field) {

				if(count($include_list) && 
					!in_array($field->GetAlias(), $include_list) && 
					!in_array($field->GetId(), $include_list))
						continue;
			
				$fielddefs[$field->GetId()] = clone $field;
			}
			
			return new EasyListFielddefArray($fielddefs);
		}
		
		// Load from database
		$db = cmsms()->GetDb();
		$this->_fielddef_cache = new EasyListCache(EasyListFielddefOperations::$identifiers);

		$query = "SELECT * FROM " . cms_db_prefix() . "module_" . $this->_GetModuleAlias() . "_fielddef GROUP BY fielddef_id ORDER BY position";	
		$dbr = $db->Execute($query);

		while ($dbr && $row = $dbr->FetchRow()) {
		
			$obj = EasyListFielddefOperations::Load($this, $row);
			if(is_object($obj)) 
				$this->_fielddef_cache[] = $obj;
		}
			
		return $this->GetFieldDefs($include_list);
	}

	// Make bulk action (using this: action.ajax.php)
	public function SaveFieldDefOrder($fielddef_order)
	{
		$db = cmsms()->GetDb();
		$query = 'UPDATE ' . cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_fielddef SET position = ? WHERE fielddef_id = ?';
		foreach($fielddef_order as $key => $item){
			$db->Execute($query, array($key, $item));
		}
		return $this->ModLang('changessaved');
	}	
		
	#---------------------
	# Item methods
	#--------------------- 	
	
	public function SaveItem(EasyListItem &$obj)
	{
		EasyListItemOperations::Save($this, $obj);
	}
	
	public function InitiateItem($load_fields = array())
	{
		$obj = new EasyListItem;
		if($load_fields !== false) {
		
			$obj->fielddefs = $this->GetFieldDefs($load_fields);
			$obj->fielddefs->SetParentItem($obj);
		}
			
		return $obj;
	}
	
	public function LoadItemByIdentifier($identifier, $value)
	{	
		if(!$obj = $this->_item_cache->GetCachedByIdentifier($identifier, $value)) {		

			$obj = $this->InitiateItem();
			$obj->$identifier = $value;
			
			if(EasyListItemOperations::Load($this, $obj))			
				$this->_item_cache[] = $obj;
		}
					
		return clone $obj;
	}
	
	public function DeleteItemById($id)
	{
		$obj = $this->LoadItemByIdentifier('item_id', $id);
		EasyListItemOperations::Delete($this, $obj);
	}	
	
	// Make bulk action (using this: action.ajax.php)
	public function SaveItemOrder($item_order)
	{
		$db = cmsms()->GetDb();
		$query = 'UPDATE ' . cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_item SET position = ? WHERE item_id = ?';
		foreach($item_order as $key => $item){
			$db->Execute($query, array($key, $item));
		}
		return $this->ModLang('changessaved');
	}
	
	public function GetItemQuery(&$params)
	{
		$class = $this->_GetModuleConfig('item_query_class');
	
		return new $class($this, $params);
	}
	
	#---------------------
	# Category methods
	#--------------------- 	
	
	public function SaveCategory(EasyListCategory &$obj)
	{
		EasylistCategoryOperations::Save($this, $obj);
	}
	
	public function InitiateCategory()
	{
		return new EasyListCategory;
	}
	
	public function LoadCategoryByIdentifier($identifier, $value)
	{	
		if(!in_array($identifier, EasylistCategoryOperations::$identifiers))
			throw new EasyListException("Illegal identifier: $identifier!");

		$obj = $this->InitiateCategory();
		$obj->$identifier = $value;
		
		EasylistCategoryOperations::Load($this, $obj);
			
		return $obj;
	}	
	
	public function DeleteCategoryById($id)
	{
		$obj = $this->LoadCategoryByIdentifier('category_id', $id);
		EasylistCategoryOperations::Delete($this, $obj);
	}		
	
	// Make bulk action (using this: action.ajax.php)
	public function SaveCategoryOrder($category_order)
	{
		$db = cmsms()->GetDb();
		$query = 'UPDATE ' . cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_category SET position = ? WHERE category_id = ?';
		foreach($category_order as $key => $item){
			$db->Execute($query, array($key, $item));
		}
		EasylistCategoryOperations::UpdateHierarchyPositions($this);
		return $this->ModLang('changessaved');
	}
	
	public function GetCategoryQuery(&$params)
	{
		$class = $this->_GetModuleConfig('category_query_class');
	
		return new $class($this, $params);
	}	

	#---------------------
	# Archive methods
	#--------------------- 	

	public function GetArchiveQuery(&$params)
	{
		$class = $this->_GetModuleConfig('archive_query_class');
	
		return new $class($this, $params);
	}	
	
	#---------------------
	# Internal methods
	#--------------------- 		

	/**
	 * Get field types
	 * @return array Returns an array of field types
	 */
	public function _GetModuleConfig()
	{
		if(count(func_num_args())) {
			
			$key = func_get_arg(0);
			return $this->_config[$key];
		}
	
		return $this->_config;
	}
	
	/**
	 * Get field types
	 * @return array Returns an array of field types
	 */
	public function _GetModuleAlias()
	{
		return $this->_GetModuleConfig('module_alias');
	}
	
	#---------------------
	# Help methods
	#--------------------- 	
	
	// Get ridd of this shit
	public function CreateBackLink($tab)
	{
		$secureparam = CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY];
		return '<a class="pageback" href="moduleinterface.php?' . $secureparam .'&module='.$this->GetName().'&amp;m1_active_tab='.$tab.'">'.$this->ModLang('back').'</a>';
	}

	function CreatePrettyLink($id, $action, $returnid='', $contents='', $params=array(), $warn_message='', $onlyhref=false, $inline=false, $addtext='', $targetcontentonly=false, $prettyurl='')
	{
		switch($action) {
	
			case 'detail':
			
				$string_array = array();
				$string_array[] = $this->prefix;
				
				// Category / hierarchial stuff
				if(isset($params['category']) && isset($params['id_hierarchy'])) {
				
					if(!isset($this->use_hints['category'])) 
						$string_array[] = $params['category'];
					
					if(!isset($this->use_hints['id_hierarchy']))
						$string_array[] = $params['id_hierarchy'];
				}		
						
				$string_array[] = $params['item'];
				
				if(!isset($this->use_hints['returnid']))
					$string_array[] = $returnid;

				if(!isset($this->use_hints['detailtemplate']) && isset($params['detailtemplate'])) 
					$string_array[] = $params['detailtemplate'];
				
				$prettyurl = implode('/', $string_array);			
				break;

			case 'default':
			
				$string_array = array();
				$string_array[] = $this->prefix;			
				
				// Category / hierarchial stuff
				if(!isset($this->use_hints['category']) && isset($params['category'])) 
					$string_array[] = $params['category'];					
				
				if(!isset($this->use_hints['id_hierarchy']) && isset($params['id_hierarchy']))
					$string_array[] = $params['id_hierarchy'];				

				// Pagelimit stuff, won't work together with hierarchy stuff.
				if(isset($params['pagelimit'])) {
				
					$string_array[] = 'page';
					$string_array[] = $params['pagenumber'];
				
					if(!isset($this->use_hints['pagelimit'])) 
						$string_array[] = $params['pagelimit'];	
				}
				
				// Archive stuff.
				if(isset($params['filter_year']) || isset($params['filter_month'])) {
				
					$string_array[] = 'archive';
					
					if(!isset($this->use_hints['filter_year']) && isset($params['filter_year'])) 
						$string_array[] = $params['filter_year'];	
				
					if(!isset($this->use_hints['filter_month']) && isset($params['filter_month'])) 
						$string_array[] = $params['filter_month'];	
				}				
				
				if(!isset($this->use_hints['returnid']))
					$string_array[] = $returnid;	
	
				if(!isset($this->use_hints['detailtemplate']) && isset($params['detailtemplate'])) 
					$string_array[] = $params['detailtemplate'];					
					
				$prettyurl = implode('/', $string_array);			
				break;			
		}

		return $this->CreateLink($id,$action,$returnid,$contents,$params,$warn_message,$onlyhref,$inline,$addtext,$targetcontentonly,$prettyurl);
	}	
	
} // end of class

?>
