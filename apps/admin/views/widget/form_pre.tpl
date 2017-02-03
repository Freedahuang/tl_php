<p>
<label>{lang txt=$label}:</label><input type="button" name="button" value='{lang txt="open/close"}' class="button" onclick="pre_toggle('pre_{$id}')"/><br />
<pre id="pre_{$id}" style="height:200px;overflow:hidden;background:#f2f2f2;padding:8px;word-wrap: break-word; word-break: normal;">{$val}</pre>
</p>