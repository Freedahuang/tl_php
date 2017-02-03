<p>
<label>{lang txt=$label}:</label><br />
<input type="radio" name="status" value="1" {if $val == 1}checked{/if}/>{lang txt='activate'}
<input type="radio" name="status" value="0" {if $val == 0}checked{/if}/>{lang txt='deactivate'}
<input type="hidden" id="{$id}" name="{$id}" value="1">
</p>