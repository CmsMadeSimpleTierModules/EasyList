<div class="pageoverflow">
	<p class="pagetext">{$fielddef->GetName()}{if $fielddef->IsRequired()}*{/if}:</p>
	<p class="pageinput">
		{if $fielddef->GetDesc()}({$fielddef->GetDesc()})<br />{/if}
		<select name="`$actionid`customfield[`$fielddef->GetId()`]">
		{if !$fielddef->IsRequired()}
		 <option value='null'>-- none --</option>
		 {/if}
		{html_options values=$fielddef->GetUDT($fielddef->GetOptionValue('udt')) output=$fielddef->GetUDT($fielddef->GetOptionValue('udt')) selected=$fielddef->GetValue()}
		</select>
	</p>
</div>