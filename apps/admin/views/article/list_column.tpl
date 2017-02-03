<td style="width:80px;" valign="top">
<a href="{rewrite_link controller='article-attachment' action='add' option=$items.id thickbox=$thickbox}" class="thickbox" title="{lang txt='add'}">+</a>
{foreach from=$items.attachment key=key item=item}
<br /><div class="betterTip" style="display:inline-block;" id="div{$item.id}{$key}">
<a id="a{$item.id}{$key}" href="{rewrite_link controller='page' action='show' width='240' image=$item.name|replace:'/':'~'}" class="betterTip" title="{$item.brief}"></a>
<a href="{$item.name}">{$item.brief}</a>&nbsp;
</div>
<a href="{rewrite_link controller='article-attachment' action='del' id=$item.id submit=true token=$token referrer=$referrer}" title="删除">-</a>
{/foreach}
</td>
<td style="width:80px;" valign="top">
<a href="{rewrite_link controller='article-region' action='add' option=$items.id thickbox=$thickbox}" class="thickbox" title="{lang txt='add'}">+</a>
{foreach from=$items.region key=key item=item}
<br />{$item.brief}
<a href="{rewrite_link controller='article-region' action='del' id=$item.id submit=true token=$token referrer=$referrer}" title="删除">-</a>
{/foreach}
</td>
<!--td align="center">
<input type="checkbox" name="article_select[]" value="{$items.id}">
</td
//-->