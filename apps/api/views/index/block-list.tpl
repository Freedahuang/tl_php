
        <ul class="pic-message-list">
            {foreach from=$list item=item}
            <li><span><img src="{$item.image}" align="absbottom">
                <span><label><a href="{$item.url}" target="_blank">{$item.name}</a><label><br/>
                {foreach from=$item.info item=info}
                    {if $info}{$info|truncate:25:""}/{/if}
                {/foreach}
                <br/>ISBN: {$item.isbn}&nbsp;&nbsp;
                数量: {$item.quantity}
                借出: {$item.lended}
                {if $item.quantity-$item.lended > 0}
                <input type="button" name="lend" alt="{$item.id}" title="lend" value="放入借阅单" class="button" />
                {/if}
                {if $item.quantity eq 0}
                <label class="pic_tip">请致电预约</label>
                {/if}

                <br/></span></span>
            </li>
            {/foreach}
            {if !$keyword}{include file="global/page-li.tpl"}{/if}
        </ul>
