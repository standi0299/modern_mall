<?
$podspage = true;
include "../_pheader.php";

if($_GET[ie_info] == "Y") {
?>
<div>
	<a href="javascript:closeLayer();"><img src="/admin/img/ie_info.png" /></a>
</div>
<?
}
else {	
?>

<!--EditorCall-->
<script src="/js/pods_editor.php" type="text/javascript"></script>
<div id="editor_layer" class="dh_layer" style="display:none;z-index:0; position: relative; left:1px; top:1px; overflow: auto; overflow-y: hidden;"><iframe id="wpod_editor_frame" width="100%" height="100%" frameborder="0" title="편집기 실행" name="wpod_editor_frame"></iframe></div>
<!--EditorCall-->

<script>
	//PodsCallEditorUpdate(pods_use,podskind, podsno,goodsno,optno,storageid,productid,optionid,addopt,ea,vdp,mode,adminmode,payno,ordno,ordseq,mid)
	PodsCallEditorUpdate('<?=$_GET[pods_use]?>','<?=$_GET[podskind]?>','<?=$_GET[podsno]?>','<?=$_GET[goodsno]?>','<?=$_GET[optno]?>','<?=$_GET[storageid]?>','<?=$_GET[podsno]?>','<?=$_GET[podoptno]?>','<?=$_GET[addoptno]?>','<?=$_GET[ea]?>','','edit_admin','Y','','','','<?=$_GET[mid]?>');
</script>

<?
}
?>

<script type="text/javascript">
function closeLayer(){
	parent.closeLayer();
}
</script>

<?include "../_pfooter.php"?>