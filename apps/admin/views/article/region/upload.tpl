{include file="global/header.tpl"}

<script language="JavaScript">
<!--
    document.domain = "{$domain}";  // cross domain
    //alert(self.parent.location);
    var u = self.parent.location.href.split("#");
    self.parent.location.href = u[0] + "#src={if $image}{$image}{else}Upload-failed!{/if}";
    self.parent.location.reload();
//-->
</script>

{include file="global/footer.tpl"}