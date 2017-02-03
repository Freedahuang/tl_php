<p>
<label>{lang txt=$label}:</label><br />
<input type="button" name="minus" value="&lt;" alt="{$id}" class="button" />
<input type="text" id="{$id}" name="{$id}" value="{if $val}{$val}{else}0{/if}" class="input picker" style="margin-left:0;margin-right:0;" readonly/>
<input type="button" name="add" value="&gt;" alt="{$id}" class="button"  style="margin-left:0;margin-right:0;"/>
</p>