

<div id="wrap_right"><div id="right">
<div>
<dl>
<dt>{lang txt='latest project'}</dt>
{foreach from=$latest_list key=key item=item}
{if $key < 10}
<dd><a href="{rewrite_link controller='show' action='project' id=$item.id}">{$item.name}</a></dd>
{/if}
{/foreach}
</dl>

<dl>
<dt>{lang txt='public resource'}</dt>
{foreach from=$attach_list key=key item=item}
{if $key < 10}
<dd><a href="{rewrite_link controller='show' action='project' id=$item.id_project}" title="{$item.desc}">{$item.orig}</a></dd>
{/if}
{/foreach}
</dl>
</div>

</div></div>
