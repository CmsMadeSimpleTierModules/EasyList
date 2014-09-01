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

class EasyListConfig implements ArrayAccess
{
	#---------------------
	# Attributes
	#---------------------	
 
	static private $_allowed = array('module_alias', 'item_query_class', 'category_query_class', 'archive_query_class');
	private $_data = array();
	private $_mod_instance;

	#---------------------
	# Magic methods
	#--------------------- 	
	
	public function __construct(EasyList &$mod) 
	{
		$this->_mod_instance = $mod; // <- May be memory heave, do some testing...
	
		$listit2_config = array();
		$fn = cms_join_path($mod->GetModulePath(), EASYLIST_CONFIG_FILE);
		if(is_readable($fn)) {
		
			include($fn);
			if(isset($listit2_config)) {
			
				foreach($listit2_config as $key => $value) {
			
					$this->offsetSet($key, $value);
				}
			}
		}
	}

	#---------------------
	# Array methods
	#---------------------		

	public function offsetExists($key)
	{		
		if(isset(self::$_allowed[$key]))
			return true;
	
		return false;
	}

	public function offsetGet($key)
	{	
		// Check file values
		if(isset($this->_data[$key])) 
			return $this->_data[$key];
		
		// Check pre-defined values
		switch($key) {
		
			case 'module_alias':
				return strtolower($this->_mod_instance->GetName());
			
			case 'item_query_class':
				return 'EasyListItemQuery';
			
			case 'category_query_class':
				return 'EasyListCategoryQuery';
				
			case 'archive_query_class':
				return 'EasylistArchiveQuery';

			default:
				return null;
		}
	}

	public function offsetSet($key,$value)
	{
		if(in_array($key, self::$_allowed))
			$this->_data[$key] = $value;
	}

	public function offsetUnset($key)
	{
		trigger_error('Unsetting ListIt2 config variable '.$key.' is invalid', E_USER_ERROR);
	}

} // end of class

?>
