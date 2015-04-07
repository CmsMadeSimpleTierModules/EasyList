<div class="pageoverflow">
	<p class="pagetext">{$fielddef->GetName()}{if $fielddef->IsRequired()}*{/if}:</p>
	<p class="pageinput">
		{if $fielddef->GetDesc()}({$fielddef->GetDesc()})<br />{/if}
		<select name="{$actionid}customfield[{$fielddef->GetId()}]">
		 <option value='none'>-- none --</option>
		{html_options values=$fielddef->GetUDT($fielddef->GetOptionValue('udt')) output=$fielddef->GetUDT($fielddef->GetOptionValue('udt')) selected=$fielddef->GetValue()}
		</select>
	</p>
</div>