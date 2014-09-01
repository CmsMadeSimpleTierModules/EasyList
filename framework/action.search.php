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
# Check params
#---------------------

if(isset($params['template_search'])) {

	$template = $params['template_search'];
}
elseif(isset($params['searchtemplate'])) {

	$template = $params['searchtemplate'];
}
else {
  $tpl = CmsLayoutTemplate::load_dflt_by_type($this->_GetModuleAlias().'::search');
    if( !is_object($tpl) ) {
	audit('',$this->GetName(),'Aucun gabarit recherche par défaut n\'a &eacute;t&eacute; trouv&eacute;');//fr
    return;
  }
  $template = $tpl->get_name();
}

$summarypage = $this->GetPreference('summarypage', $returnid);
if(isset($params['summarypage'])) {

	if(is_numeric($params['summarypage'])) {
		$summarypage = $params['summarypage'];
	}
	else {
		if(!isset($hm))
			$hm = cmsms()->GetHierarchyManager();
		
		$summarypage = $hm->sureGetNodeByAlias($params['summarypage'])->GetId();
	}
}

$debug = (isset($params['debug']) ? true : false);

#---------------------
# Grap template
#---------------------

$template = $this->ProcessTemplateFromDatabase($template);
#---------------------
# Init fielddefs
#---------------------

if(stripos($template, '$fielddefs')) { // <- Load fielddefs only if we have variable request on template

	$filters = array();
	if(!empty($params['filter'])) {

		$filters = explode(',', $params['filter']);
	}

	$fielddefs = $this->GetFieldDefs($filters);
	foreach($fielddefs as $fielddef) {

		EasyListFielddefOperations::LoadValuesForFieldDef($this, $fielddef);
	}

	$smarty->assign('fielddefs', $fielddefs);
	$smarty->assign('filterprompt', $this->ModLang('filterprompt', $this->GetPreference('item_plural', '')));
}

#---------------------
# Smarty processing
#---------------------

$smarty->assign('formstart', $this->CreateFormStart($id, 'default', $summarypage, 'post', 'multipart/form-data', false, '', $params));
$smarty->assign('formend', $this->CreateFormEnd());
$smarty->assign('modulealias', $this->_GetModuleAlias()); // Deprecated

echo $this->ProcessTemplateFromData($template);

if($debug) 
	$smarty->display('string:<pre>{$fielddefs|@print_r}</pre>');

?>
