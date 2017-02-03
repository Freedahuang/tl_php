<dd class="page">
    {if $page > 1}
    <a href="javascript:goPage(1);" title="第一页">~</a>
    <a href="javascript:history.go(-1);" title="上一页">&lt;&lt;</a> 
    {/if}
    {if $limit == $count}
    <a href="javascript:goPage({$page+1})" title="下一页">&gt;&gt;</a>
    {/if}
&nbsp;&nbsp;</dd>
