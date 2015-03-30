<?php
#-------------------------------------------------------------------------
#
# Jean-Christophe ghio <jcg@interphacepro.com>
#
#-------------------------------------------------------------------------
# EasyList is a CMS Made Simple module that enables the web developer to create
# multiple lists throughout a site. It can be duplicated and given friendly
# names for easier client maintenance.
#
#-------------------------------------------------------------------------

class EasyListfd_DropdownUDT extends EasyListFielddefBase
{
	public function __construct(&$db_info) 
	{	
		parent::__construct($db_info);
		
		$this->SetFriendlyType($this->ModLang('fielddef_'.$this->GetType()));
	}
	
	public function GetOptions()
	{
		  $utops = cmsms()->GetUserTagOperations();
          return $utops->ListUserTags();
	}
	public function GetUDT($udt)
	{
          $parms = array();
		  $utops = cmsms()->GetUserTagOperations();
          return $utops->CallUserTag($udt,$parms);
	}
	
}
?>