<li><fieldset><legend>{lang txt=$option}设置</legend><div>
    <form method=post name="{$option}" action="{rewrite_link controller=$controller_uri action=$action_uri option=$option token=$token}"
    >
    {if $description}
    <p>
    <span class="tip">说明：{$description}</span>
    </p>{/if}
    <p>
    <label>{lang txt=$option}:</label><br />
    {if $option|strstr:"input_"}
    <input type="input" id="{$option}" name="{$option}" value="{$content}" class="input"/>
    {else}
    <textarea id="{$option}" name="{$option}" cols="80" rows="15">{$content}</textarea>
    {/if}
    </p>
    <p>
    <p>
    <input type="submit" name="submit" value="提交" class="button">
    <input type="submit" name="submit" value="清空" onclick="clean('{$option}');" class="button">    
    </p>
</form></div></fieldset></li>