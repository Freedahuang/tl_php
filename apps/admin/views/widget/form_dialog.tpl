
<fieldset style="clear:both;padding:4px;padding-top:8px;margin-left: 0.8em;border: 1px solid #7d7d7d;">
<legend style="padding:4px;background:#7d7d7d;">{lang txt='message'}</legend>
{foreach from=$val item=message}
{assign var='msg_background' value='yellow'}
{if $message.status gt 0}
    {assign var='msg_background' value='#e6e4d8'}
{/if}
{assign var='msg_float' value='left'}
{assign var='msg_color' value='green'}
{if $message.status eq 2}
    {assign var='msg_float' value='right'}
    {assign var='msg_color' value='#002157'}
{/if}
<p style="clear:both;float:{$msg_float};width:80%">
<label style="clear:both;float:{$msg_float};">{$message.date_add}</label><br/>
<span style="float:{$msg_float};color:{$msg_color};background:{$msg_background};">{$message.content}
{if $message.link}<a href="{$message.link}" target="_blank" class="tip">{lang txt='detail'}</a>{/if}
{if $message.image}<br/><a href="{$message.image}" target="_blank"><img src="{$message.image}" width="140"></a>{/if}
</span>
</p>
{/foreach}
</fieldset>
<a name="end"></a>
{literal}
<script type="text/javascript">
$(document).ready(function(){
if (location.href.indexOf("#end") == -1) {
    location.href += "#end";
}

});
</script>
{/literal}