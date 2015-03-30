<div class="pageoverflow">
    <p class="pagetext">{$fielddef->ModLang('options')}:</p>
    <p class="pageinput">
    	{$themeObject->DisplayImage('icons/system/info.gif')}<em> {$fielddef->ModLang('fielddef_optionsUDT_help')}</em><br />
		{html_options name="`$actionid`custom_input[udt]" values=$fielddef->GetOptions() output=$fielddef->GetOptions() selected=$fielddef->GetOptionValue('udt', 'Dropdown')}
    </p>
</div>

