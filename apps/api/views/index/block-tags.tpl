
        <div id="content">
        <ul>
        {foreach from=$list key=key item=item}
            <li><strong>{$key}</strong><br />
            <span>
            {foreach from=$item item=sub}
            <label><a href="{rewrite_link controller='index' action='book' tag=$sub}">{$sub}</a></label>
            {/foreach}
            </span>
            </li>
        {/foreach}
        </ul>
        </div>
