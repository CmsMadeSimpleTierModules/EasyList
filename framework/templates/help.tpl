{strip}
<link rel="stylesheet" href="{$root_url}/modules/{$parent_name}/framework/css/colorbox.css" type="text/css" />
<link rel="stylesheet" href="{$root_url}/modules/{$parent_name}/lib/css/easylistHelp.css" type="text/css" />
<script type="text/javascript" language="javascript" src="{$root_url}/modules/{$parent_name}/framework/js/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){ldelim}
	jQuery('.cbox').colorbox({ldelim}
		rel:'group',
		iframe: true,
		innerWidth: 800,
		innerHeight: 450,
		opacity: 0.2
	{rdelim});
{rdelim});
</script>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_donations">
<input type="hidden" name="business" value="jcg@interphacepro.com">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" value="jcg@interphacepro.com">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="currency_code" value="EUR">
<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/fr_XC/i/scr/pixel.gif" width="1" height="1">
</form>

<div class="clear"></div>
<div id="page_tabs">
	<div id="general">
		{$mod->ModLang('general')}
	</div>
	<div id="usage">
		{$mod->ModLang('usage')}
	</div>
	<div id="permissions">
		{$mod->ModLang('permissions')}
	</div>
	<div id="field">
		{$mod->ModLang('fielddefs')}
	</div>
	<div id="categories">
		{$mod->ModLang('categories')}
	</div>
	<div id="templates">
		{$mod->ModLang('templates')}
	</div>
</div>
<div class="clearb"></div>
<div id="page_content">  
	<div id="general_c">
		{$mod->ModLang('help_general')}
	</div>	
	<div id="usage_c">		
		{$usage_text}
		<div style="float:left;">
			{$mod->ModLang('help_usage_options')}
		</div>
		<br style="clear:both;" />
		<div style="float:left; display: inline;">
			{$mod->ModLang('help_usage_fielddefs')}
		</div>
		<br style="clear:both;" />
		<div style="float:left;">
			{$mod->ModLang('help_usage_categories')}
		</div>
		<br style="clear:both;" />
		<div style="float:left;">
			{$mod->ModLang('help_usage_items')}
		</div>
		<br style="clear:both;" />
	</div>
	<div id="permissions_c">
		{$permissions_text}
	</div>
	<div id="field_c">
		{$mod->ModLang('help_fielddefs')}
		<a title="Creating custom field definition" class="cbox" rel="group" href="//player.vimeo.com/video/55728127">
		</a>
	</div>
	<div id="categories_c">
		{$mod->ModLang('help_categories')}
	</div>
	<div id="templates_c">
		{$templates_text}
<pre class="code">
<code>
{literal}
{foreach from=$items item=item}
	{$item->fielddefs.position.value|cms_escape}&lt;br /&gt;
{/foreach}
{/literal}

{literal}
{foreach from=$items item=item}
	{$item->position|cms_escape}&lt;br /&gt;
{/foreach}
{/literal}
</code>
</pre>
	</div>
	<div id="about_c">
		<div class="pageoverflow">
			<h3>About this module</h3>
			<p>Origin of this module comes from <a href="http://dev.cmsmadesimple.org/projects/listit" target="_blank">ListIt Module</a> developed by Ben Malen.<br />As there were no plans on further development of the module some people decided to fork the module and continue with development.<br />
			If you find any bugs please feel free to submit a bug report <a href="http://dev.cmsmadesimple.org/bug/list/1015" target="_blank">here</a> or for any good ideas consider submiting a feature request <a href="http://dev.cmsmadesimple.org/feature_request/list/1015" target="_blank">here</a>. </p>
			<p>Please keep in mind that developers do have their daily jobs which means that feature requests are considered and done as time allows. If you need a feature really badly consider contacting one of the developers for a sponsored development.
			</p>
			
			<h3>Support</h3>
			<p>As per the GPL, this software is provided as-is. Please read the text of the license for the full disclaimer.</p>
			
			<h3>Copyright and License</h3>
			
			<p>IDT Media - Goran Ilic & Tapio Löytty</p>
			<p>Web: <a href="http://www.idt-media.com" rel="external">www.idt-media.com</a></p>
			<p>Email: <a href="mailto:hi@idt-media.com">hi@idt-media.com</a></p>
			
			<br />
		
			<p>Goran Ilic, <a href="mailto:uniqu3e@gmail.com">&lt;uniqu3e@gmail.com&gt;</a></p>
			<p>Web: <a href="http://www.ich-mach-das.at" rel="external">www.ich-mach-das.at</a></p>

			<br />
			
			<p>Tapio Löytty, <a href="mailto:tapsa@orange-media.fi">&lt;tapsa@orange-media.fi&gt;</a></p>
			<p>Web: <a href="http://www.orange-media.fi" rel="external">www.orange-media.fi</a></p>

			<br />
			
			<p>Copyright &copy; 2012-2013. All Rights Are Reserved.</p>
			<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>			
			
			<h4>Contributors</h4>
			<ul>
				<li>Jonathan Schmid (Foaly*) hi@jonathanschmid.de <br />www.jonathanschmid.de</li>
				<li>Robert Campbell (calguy1000) calguy1000@cmsmadesimple.org  <br />www.calguy1000.com</li>
				<li>Lukas Blatter (nockenfell) nockenfell@gmail.com <br />www.blattertech.ch</li>
				<li>Arnoud (arnoud) arnoud@upservice.nl <br />www.upservice.nl</li>
				<li>Wayne ONeil (wishbone) wayne@teamwishbone.com <br />www.teamwishbone.com</li>
			</ul>
		</div>
	</div>  
	<div class="clearb"></div>	
</div>
{/strip}