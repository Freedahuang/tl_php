
<span class="right">|

    {if $page > 2}
    <a href="javascript:goPage(1);">第一页</a>
    {/if}
    {if $page > 1}
    <a href="javascript:history.go(-1);">上一页</a> 
    {/if}
    {if $limit == $count}
    <a href="javascript:goPage({$page+1})">下一页</a>
    {/if}
</span>