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

#---------------------
# Handle child cloning
#---------------------

$modules = $this->ListModules();
foreach($modules as $module) {

	if($module->module_name === $params['name']) {

		$duplicator = new EasyListDuplicator();
		$duplicator->SetModule($module->module_name);
		$duplicator->SetDestination(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . $module->module_name);
		$duplicator->Run();
		
		break;
	}
}

#---------------------
# Field scanner
#---------------------

if($this->GetPreference('allow_autoscan'))
	EasyListFielddefOperations::ScanModules();

?>
