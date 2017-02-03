{include file="global/header.top.menu.tpl"}

{if $select != 'Shenghuo'}
{include file="index/block-newest.tpl"}
{/if}

<div id="main-block">
<span class="fLeft">
{if $select == 'Shenghuo'}
{include file="index/block-corp.tpl"}
{/if}
{include file="index/block-category.tpl"}
</span>

<span class="fRight">
{include file="index/block-right.tpl"}
</span>

</div>

{include file="global/footer.tpl"}
