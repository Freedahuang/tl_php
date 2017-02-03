{* header.tab.tpl与footer.tab.tpl必须成对出现 *}
{include file="global/pair.header.tab.tpl"}

{tpl_exists tpl=$table.tpl|replace:"-":"/"|cat:"/list_button.tpl" assign='exists'}
{if $exists}
    {include file=$exists}
{else}
    {include file="widget/list_button.tpl"}
{/if}

{include file="global/page.tpl" count=$list|@count}

<table id="main_tbl" class="w98">
<thead>
    <tr>
        {foreach from=$table.head key=key item=head}
        {if $head.title != 'status' and $head.title != 'active'}
        <th {if $head.width}width="{$head.width}px"{/if}>{lang txt=$head.title}</th>
        {/if}
        {/foreach}

        {* 检测是否需要显示自定义字段 *}
        {tpl_exists tpl=$table.tpl|replace:"-":"/"|cat:"/list_head.tpl" assign='exists'}
        {if $exists}{include file=$exists}{/if}

        {* 把状态字段放到最后 *}
        {foreach from=$table.head item=head}
        {if $head.title == 'status' or $head.title == 'active'}
        <th width="{if $head.width}{$head.width}{else}40{/if}px">{lang txt=$head.title}</th>
        {/if}
        {/foreach}
    </tr>
</thead>
<tbody>
{foreach from=$list item=item}
    {cycle assign='bg_color' values='#e6e4d8,#f2f2f2'}
    <tr bgColor="{$bg_color}"
        onmouseover="this.bgColor='#d8d8d8';"
        onmouseout="this.bgColor='{$bg_color}';">
        {foreach from=$table.fields key=key item=column}
        {if $column == 'name'}
        <td align="{if $table.head[$key].align}{$table.head[$key].align}{else}center{/if}" valign="top" name="fld_name">
            {if $table.edit == 'remove'}
                {$item.name}
            {else}
            <a href="{rewrite_link controller=$controller_uri action='edit' option=$option id=$item.id thickbox=$thickbox}"
                {* nopop = 判断是否需要在当前页面弹出修改窗口 或新页面 *}
                alt="edit" class="{if $table.nopop == false}thickbox{/if}" title="{lang txt='edit'}{$item.name|truncate:20}">
                {$item.name|truncate}
                </a>
            {/if}
        </td>
        {* 如果是可定义字段 则加载相应信息 把可定义自动信息放入$data主数据 结构更清晰 *}
        {elseif $table.head[$key].customizable}
            {include file="`$table.type`/list_`$column`.tpl"}
        {elseif $column != 'active'}
        <td align="{if $table.head[$key].align}{$table.head[$key].align}{else}center{/if}" valign="top" name="fld_{$column}">
            {* key == 0 做链接是在数据表没有 name 字段的情况下 需要对记录进行编辑 *}
            {if $key == 0 and $table.edit != 'remove'}
            
                <a href="{rewrite_link controller=$controller_uri action='edit' id=$item.id thickbox=$thickbox}"
                    alt="edit" class="{if $table.nopop == false}thickbox{/if}" title="{lang txt='edit'}">{$item.$column}</a>
            {else}
                {assign var='link' value=$table.head[$key].link}
                {if $item.$link}
                    <a href="{$item.$link}">
                {/if}
                {if $table.head[$key].type == 'image'}
                    <img src='{$item.$column}' width="320px">
                {elseif $table.head[$key].type == 'link'}
                    <a href='{$item.$column}' target='_blank'>{$item.$column}</a>
                {else}
                    {$item.$column|unescape}
                {/if}
                {if $item.$link}
                    </a>
                {/if}
            {/if}
        </td>
        {/if}
        {/foreach}

        {* 检测是否需要显示自定义字段 *}
        {tpl_exists tpl=$table.tpl|replace:"-":"/"|cat:"/list_column.tpl" assign='exists'}
        {if $exists}{include file=$exists items=$item}{/if}

        {foreach from=$table.fields key=k item=column}
        {if $column == 'active'}
        {assign var="option" value="toggle"}
        {if $table.head[$k].option}{assign var="option" value=$table.head[$k].option}{/if}
        <td align="center" valign="top" name="fld_active">
            <a href="javascript:if (confirm('{lang txt='confirm'}{lang txt=$option}?')) {ldelim}location.href='{rewrite_link controller=$controller_uri action='list' submit=true option=$option id=$item.id token=$token referrer=$referrer}'{rdelim}"
                title={if $item.active}"{lang txt=$option}">&#8730;{else}"{lang txt='activate'}">&#935;{/if}</a>
        </td>
        {/if}
        {/foreach}
    </tr>
{/foreach}
</tbody>
</table>

{include file="global/pair.footer.tab.tpl"}
