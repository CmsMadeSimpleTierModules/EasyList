<div id="{$modulealias}_filter">

	<h3>{$filterprompt}</h3>

	{$formstart}

		{foreach from=$fielddefs item=fielddef}
		<div class="form-row">
			
		{if $fielddef.type != 'Categories'}
			<label for="filter_{$fielddef->alias}">{$fielddef->name}</label>
			
			{if $fielddef.type == 'Checkbox'}
				<input type="checkbox" name="{$actionid}search_{$fielddef->alias}" id="filter_{$fielddef->alias}" value="{$fielddef->value}" />
			{else}
			
			<select name="{$actionid}search_{$fielddef->alias}" id="filter_{$fielddef->alias}">
				<option value=''>{$mod->ModLang('all')}</option>
				{foreach from=$fielddef->values item=value}
				<option>{$value}</option>
				{/foreach}
			</select>
			{/if}
			
		{/if}
			
		</div>
		{/foreach}
	
		<input class="search-button" name="submit" value="{$mod->ModLang('search')}" type="submit" />

	{$formend}

</div>