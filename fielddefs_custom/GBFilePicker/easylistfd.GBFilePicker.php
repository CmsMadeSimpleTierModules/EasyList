<?php
#-------------------------------------------------------------------------
#
# Tapio Löytty, <tapsa@orange-media.fi>
# Web: www.orange-media.fi
#
# Goran Ilic, <uniqu3e@gmail.com>
# Web: www.ich-mach-das.at
#
#-------------------------------------------------------------------------# ListIt2 become EasyList due to the departure of the wackos in summer 2014.## Jean-Christophe ghio <jcg@interphacepro.com>##-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

class EasyListfd_GBFilePicker extends EasyListFielddefBase
{
	public function __construct(&$db_info) 
	{	
		parent::__construct($db_info);
		
		$this->SetFriendlyType($this->ModLang('fielddef_'.$this->GetType()));
	}
	
	static public function GetModuleDeps()
	{
		return array('GBFilePicker'=>'1.3.3');
	}
	
	public function RenderForAdminListing($id, $returnid)
	{
		if(!$this->HasValue())
			return;
	
		if(!$this->GetOptionValue('image'))
			return $this->GetValue();

		$config = cmsms()->GetConfig();
		
		$_invalid = array('.', '..');
        $href = $config['uploads_url'] . '/' . $this->GetValue();
	
		// Try if we can use CGSmartImage
		$cgsi = cmsms()->GetModuleInstance('CGSmartImage');
		if(is_object($cgsi)) {
		
			$params['src'] = $href;
			$params['filter_croptofit'] = '48,48';
			$params['notag'] = true;
			$params['noembed'] = true;
		
			$output = cgsi_utils::process_image($params);
			
			$url = $output['output'];
		}
		// Nope, lets use internal thumbs instead.
		else {
		
			$url = $config['uploads_url'] . '/GBFilePickerThumbs';	
			$value = explode('/', $this->GetValue()); // <- Static presentation of DIRECTORY_SEPARATOR, wrong but GBFilePicker ain't supporting it!		
			$actual_value = array_pop($value);
			$url .= '/thumb_';
			if(count($value)) {
				
				foreach($_invalid as $one) {
								
					if(in_array($one, $value)) {
					
						$key = array_search($one, $value);
						if($key !== FALSE)
							unset($value[$key]);
					}
				}
				
				$url .= implode("-", $value) . '_';	
			}
			$url .= $actual_value;
		}
		
		return '<a href="'.$href.'" class="cbox thumb"><img src="'.$url.'" width="48" height="48" /></a>';		
	}
			
	public function GetInput($id)
	{
		$config = cmsms()->GetConfig();
		$mod = $this->GetModuleInstance();
		
		$GBFilePicker = cmsms()->GetModuleInstance('GBFilePicker');
		if(is_object($GBFilePicker)) {
		
			//$dir = $this->_CompileExtraDir($this->GetOptionValue('dir'), $this->GetItemId(), $this->GetAlias());
			$dir = $this->GetOptionValue('dir');

			//if($dir !== false){
			
			// make dir if not exists
			$path = $config['uploads_path'] . DIRECTORY_SEPARATOR . $dir;
			if(!is_dir($path)){
				@mkdir($path, 0777, true);
			}

			// GBFilePicker "Update dropdown" does not work if id contains "[]", strip it out
			$name = 'customfield['.$this->GetId().']';
			$savename = 'customfield_'.$this->GetId().'_';

			$gbfilepicker = $GBFilePicker->CreateFilePickerInput($GBFilePicker, $id, $savename, $this->GetValue(), array(
				'media_type' => ($this->GetOptionValue('image')?'image':'file'),
				'file_extensions' => $this->GetOptionValue('allowed'),
				'dir' => $dir,
				'upload' => true,
				'exclude_prefix' => $this->GetOptionValue('exclude_prefix'),
				'mode' => ($this->GetOptionValue('filebrowser')?'browser':'dropdown'),
				'create_dirs' => $this->GetOptionValue('create_dirs'),
				'delete' => $this->GetOptionValue('delete'),
				'show_subdirs' => $this->GetOptionValue('show_subdirs')
			));

			$search       = 'name="'.$id.$savename;
			$replace      = 'name="'.$id.$name;
			$gbfilepicker = str_replace($search, $replace, $gbfilepicker);

			return $gbfilepicker;
			/*	
			} else {
			
				return $this->ModLang('pathcontainsvars', $mod->GetPreference('item_singular', ''));
			}
			*/
		}
	}
/*	
	private function _CompileExtraDir($path, $item_id = null, $item_alias = null){

		if(strpos($path, '{$item_alias}') !== false){
			if(!isset($item_alias) || strlen($item_alias) == 0)
				return false;
			$path = str_replace('{$item_alias}', $item_alias, $path);
		}
		if(strpos($path, '{$item_id}') !== false){
			if(!isset($item_id) || !is_int($item_id))
				return false;
			$path = str_replace('{$item_id}', $item_id, $path);
		}

		return $path;
	}
*/	
} // end of class
?>