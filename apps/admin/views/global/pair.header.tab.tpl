{include file="global/header.toolbar.tpl"}

<div id="main">

<div class="tab-nav group-mods">
    <ul>
    {foreach from=$actions key=key item=action}
        {if $action.tab}
        <li {if $key == $action_uri}class='current'{/if}>
            <a href="{rewrite_link controller=$controller_uri action=$key}">
                <!-- <img src="/images/tab/{$key}.gif"/> --> 
                {if $action.tab == 'name'}
                    {$action.name}
                {else}
                    {$action.tab}
                {/if}
            </a>
        </li>
        {/if}
    {/foreach}
    </ul>
</div>


