{* header.tab.tpl与footer.tab.tpl必须成对出现 *}
{include file="global/header.tpl"}

{if $image|is_pic}
<img id="preview" src="{$image}" width="{$width}px">
{elseif $image}
<span id="preview">{$image}</span>
{/if}

{include file="global/footer.tpl"}
