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

if (!is_object(cmsms())) exit;

#---------------------
# Database tables
#---------------------

$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

// create item table
$fields = '
    item_id I KEY AUTO,
	category_id I,
    title C(255),
	alias C(255),
    position I,
    active I4,
    create_time DT,
	modified_time DT,
	start_time DT,
	end_time DT,
	key1 C(50),
	key2 C(50),
	key3 C(50),
	owner I
';

$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_item', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// create category table
$fields = '
	category_id I KEY AUTO,
	category_name C(255),
    category_alias C(255),
	category_description X,
    parent_id I,
    position I,
    hierarchy C(255),
	hierarchy_path C(255),
	id_hierarchy C(255),
	active I4,
	create_date DT,
	modified_date DT,
	key1 C(50),
	key2 C(50),
	key3 C(50)
';

$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_category', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// create item_categories
$fields = '
    item_id I KEY NOT null,
    category_id I KEY NOT null
';

$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_item_categories', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// create fielddef table
$fields = '
    fielddef_id I KEY AUTO,
    name C(255),
    alias C(255),
    help C(255),
    type C(50),
    position I,
    required I,
	extra X
';

$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_fielddef', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// create fielddef options table
$fields = '
    fielddef_id I KEY,
    name C(255) KEY,
    value X
';

$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_fielddef_opts', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// create fieldval table
$fields = '
    item_id I KEY NOT null,
    fielddef_id I KEY NOT null,
    value_index I KEY NOT null,
    value X,
	data X
';

$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . 'module_' . $this->_GetModuleAlias() . '_fieldval', $fields, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

#---------------------
# Templates
#---------------------	
	
// **************************************************************** templates types ***********************************************
# -------------------------- Summary Type -----------------------
try {
  $EasyList_summary_template_type = new CmsLayoutTemplateType();
  $EasyList_summary_template_type->set_originator($this->GetName());
  $EasyList_summary_template_type->set_name('summary');
  $EasyList_summary_template_type->set_dflt_flag(TRUE);
  $EasyList_summary_template_type->set_lang_callback(EASYLIST.'::template_type_lang_callback');
  $EasyList_summary_template_type->set_content_callback(EASYLIST.'::reset_templates_defaults');
  $EasyList_summary_template_type->reset_content_to_factory();
  $EasyList_summary_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}
# -------------------------- Detail Type -----------------------
try {
  $EasyList_detail_template_type = new CmsLayoutTemplateType();
  $EasyList_detail_template_type->set_originator($this->GetName());
  $EasyList_detail_template_type->set_name('detail');
  $EasyList_detail_template_type->set_dflt_flag(TRUE);
  $EasyList_detail_template_type->set_lang_callback(EASYLIST.'::template_type_lang_callback');
  $EasyList_detail_template_type->set_content_callback(EASYLIST.'::reset_templates_defaults');
  $EasyList_detail_template_type->reset_content_to_factory();
  $EasyList_detail_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}
# -------------------------- Search Type -----------------------
try {
  $EasyList_search_template_type = new CmsLayoutTemplateType();
  $EasyList_search_template_type->set_originator($this->GetName());
  $EasyList_search_template_type->set_name('search');
  $EasyList_search_template_type->set_dflt_flag(TRUE);
  $EasyList_search_template_type->set_lang_callback(EASYLIST.'::template_type_lang_callback');
  $EasyList_search_template_type->set_content_callback(EASYLIST.'::reset_templates_defaults');
  $EasyList_search_template_type->reset_content_to_factory();
  $EasyList_search_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}
# -------------------------- Category Type -----------------------
try {
  $EasyList_category_template_type = new CmsLayoutTemplateType();
  $EasyList_category_template_type->set_originator($this->GetName());
  $EasyList_category_template_type->set_name('category');
  $EasyList_category_template_type->set_dflt_flag(TRUE);
  $EasyList_category_template_type->set_lang_callback(EASYLIST.'::template_type_lang_callback');
  $EasyList_category_template_type->set_content_callback(EASYLIST.'::reset_templates_defaults');
  $EasyList_category_template_type->reset_content_to_factory();
  $EasyList_category_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}
# -------------------------- Archive Type -----------------------
try {
  $EasyList_archive_template_type = new CmsLayoutTemplateType();
  $EasyList_archive_template_type->set_originator($this->GetName());
  $EasyList_archive_template_type->set_name('archive');
  $EasyList_archive_template_type->set_dflt_flag(TRUE);
  $EasyList_archive_template_type->set_lang_callback(EASYLIST.'::template_type_lang_callback');
  $EasyList_archive_template_type->set_content_callback(EASYLIST.'::reset_templates_defaults');
  $EasyList_archive_template_type->reset_content_to_factory();
  $EasyList_archive_template_type->save();
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

// **************************************************************** templates ***********************************************
# -------------------------- Summaries templates -----------------------

try {
$fn = cms_join_path(EASYLIST_TEMPLATE_PATH, 'fe_summary_default.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($this->GetName().'_summary_default');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($EasyList_summary_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
$fn = cms_join_path(EASYLIST_TEMPLATE_PATH, 'fe_summary_searchresults.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($this->GetName().'_summary_searchresults');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($EasyList_summary_template_type);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}
# -------------------------- Detail templates -----------------------
try {
$fn = cms_join_path(EASYLIST_TEMPLATE_PATH, 'fe_detail_default.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($this->GetName().'_detail_default');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($EasyList_detail_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}
# -------------------------- Search templates -----------------------
try {
$fn = cms_join_path(EASYLIST_TEMPLATE_PATH, 'fe_search_default.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($this->GetName().'_search_default');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($EasyList_search_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}
try {
$fn = cms_join_path(EASYLIST_TEMPLATE_PATH, 'fe_search_filter.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($this->GetName().'_search_filter');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($EasyList_search_template_type);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}
try {
$fn = cms_join_path(EASYLIST_TEMPLATE_PATH, 'fe_search_multiselect.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($this->GetName().'_search_multiselect');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($EasyList_search_template_type);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

# -------------------------- Categories templates -----------------------
try {
$fn = cms_join_path(EASYLIST_TEMPLATE_PATH, 'fe_category_default.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($this->GetName().'_category_default');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($EasyList_category_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
$fn = cms_join_path(EASYLIST_TEMPLATE_PATH, 'fe_category_hierarchy.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($this->GetName().'_category_hierarchy');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($EasyList_category_template_type);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}
# -------------------------- Archive templates -----------------------
try {
$fn = cms_join_path(EASYLIST_TEMPLATE_PATH, 'fe_archive_default.tpl');
  if( file_exists( $fn ) ) {
    $template = @file_get_contents($fn);
    $tpl = new CmsLayoutTemplate();
    $tpl->set_name($this->GetName().'_archive_default');
    $tpl->set_owner($uid);
    $tpl->set_content($template);
    $tpl->set_type($EasyList_archive_template_type);
    $tpl->set_type_dflt(TRUE);
    $tpl->save();
  }
}
catch( CmsException $e ) {
  // log it
  debug_to_log(__FILE__.':'.__LINE__.' '.$e->GetMessage());
  audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

#---------------------
# Preferences
#---------------------

$this->SetPreference('sortorder', 'ASC');
$this->SetPreference('adminsection', 'content');
$this->SetPreference('url_prefix', munge_string_to_url($this->GetName(), true));
$this->SetPreference('friendlyname', $this->GetName());
$this->SetPreference('item_singular', utf8_encode(html_entity_decode($this->ModLang('item'))));
$this->SetPreference('item_plural', utf8_encode(html_entity_decode($this->ModLang('items'))));
$this->SetPreference('item_title', utf8_encode(html_entity_decode($this->ModLang('item_title'))));

#---------------------
# Permissions
#---------------------

$this->CreatePermission($this->_GetModuleAlias() . '_modify_item', $this->GetName() . ': Modify Items');
$this->CreatePermission($this->_GetModuleAlias() . '_modify_category', $this->GetName() . ': Modify Categories');
$this->CreatePermission($this->_GetModuleAlias() . '_modify_option', $this->GetName() . ': Modify Options');
$this->CreatePermission($this->_GetModuleAlias() . '_remove_item', $this->GetName() . ': Remove items');
$this->CreatePermission($this->_GetModuleAlias() . '_approve_item', $this->GetName() . ': Approve items');
$this->CreatePermission($this->_GetModuleAlias() . '_modify_all_item', $this->GetName() . ': Modify all items');

#---------------------
# Events
#---------------------

$this->CreateEvent('PreItemSave');
$this->CreateEvent('PostItemSave');

$this->CreateEvent('PreItemDelete');
$this->CreateEvent('PostItemDelete');

$this->CreateEvent('PreItemLoad');
$this->CreateEvent('PostItemLoad');

$this->CreateEvent('PreRenderAction');
