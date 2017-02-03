<p>
<label>{lang txt=$label}:</label><br />
<select id="{$id}" name="{$id}">
    <option value="0">-- {lang txt="please select"} --</option>
    {include file="widget/opt-tree.tpl" items=$val selected=$necessarily}
</select>
<span class="tip"></span>
</p>