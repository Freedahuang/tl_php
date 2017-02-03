<p>
<label>{lang txt=$label}:</label><br />

<input type="hidden" id="{$id}" name="{$id}" value="">

<script language="JavaScript">
<!--
var tree = {$val};
function select(tree, i) {ldelim}
	i = i * 10;
	// console.log();
	if (typeof(tree) == 'object') {ldelim}
		console.log(i);
		$("select[name^={$id}_"+i+"]").remove();
		$("#{$id}").before('<select id="{$id}_'+i+'" name="{$id}_'+i+'"><option value="">-- {lang txt="please select"} --</option></select>');
		for (name in tree)
		{ldelim}
			$("#{$id}_"+i).append('<option value="'+name+'">'+name+'</option>');
		{rdelim}

		$("#{$id}_"+i).change(function(){ldelim}
			select(tree[$(this).val()], i);
    	{rdelim});
	{rdelim}
{rdelim}
$(document).ready(function(){ldelim}
	select(tree, 1);
	// for (name in tree)
	// {ldelim}
	// 	$("#{$id}_0").append('<option value="'+name+'">'+name+'</option>');
	// {rdelim}
 //    $("#{$id}_0").change(function(){ldelim}
 //    	var val = tree[$(this).val()];
 //    	var len = Object.keys(val).length;
 //    	$("#{$id}_1").remove();
 //    	if (len > 1) {ldelim}
 //    		$(this).after('<select id="{$id}_1" name="{$id}_1"></select>');
 //    		var option = '';
	// 		for (name in val)
	// 		{ldelim}
	// 			$("#{$id}_1").append('<option value="'+name+'">'+name+'</option>');
	// 		{rdelim}
 //    	{rdelim}
 //    {rdelim});
{rdelim});
//-->
</script>