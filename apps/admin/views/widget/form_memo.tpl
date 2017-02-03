<p class="memo">
<span class="tip">*</span>为必填项
{tpl_exists tpl=$form.tpl|replace:"-":"/"|cat:"/form_memo.tpl" assign='exists'}
{if $exists}{include file=$exists}<br/><br/>{/if}

</p>