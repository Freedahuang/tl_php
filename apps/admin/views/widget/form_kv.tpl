<p>
<input type="hidden" name="form_kv[]" value="{$id}" />
<label>{lang txt=$label}:</label>
<a href="#" onclick='$("#{$id}_ul").prepend($("#{$id}"+"_li").clone().removeClass("hide").removeAttr("id"));' title="增加">+</a><br />
<ul id="{$id}_ul">
{foreach from=$val key=code item=item}
<li>
{if $necessarily}
	<select name="k_{$id}[]">
    	{include file="widget/opt-tree.tpl" items=$necessarily selected=$code}
	</select>
{else}
	<input type="text" name="k_{$id}[]" value="{$code}" class="input short"/>
{/if}
<input type="text" name="v_{$id}[]" value="{$item}" class="input short" />
<a href="#" onclick="$(this).parent().remove();" title="删除">-</a>
</li>
{/foreach}

<li id="{$id}_li" class="hide">
{if $necessarily}
	<select name="k_{$id}[]">
    	{include file="widget/opt-tree.tpl" items=$necessarily}
	</select>
{else}
	<input type="text" name="k_{$id}[]" value="" class="input short"/>
{/if}
<input type="text" name="v_{$id}[]" value="" class="input short" />
<a href="#" onclick="$(this).parent().remove();" title="删除">-</a>
</li>

</ul>
</p>