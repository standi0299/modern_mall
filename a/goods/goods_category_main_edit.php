<?

include "../_pheader.php";

list ($mainhtml) = $db->fetch("select mainhtml from exm_category where cid = '$cid' and catno = '$_GET[catno]'",1);

?>

<form method="POST" action="indb.php" onsubmit="return submitContents(this);">
<input type="hidden" name="mode" value="catmain"/>
<input type="hidden" name="catno" value="<?=$_GET[catno]?>"/>

<div align="center" style="padding-top:10px;">
<textarea name="content" style="width:99.8%;height:400px" type="editor" id="content"><?=$mainhtml?></textarea><p>

<script type="text/javascript" src="../../js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/smarteditor/editorStart.js" charset="utf-8"></script>

<!--form 전송 취약점 개선 20160128 by kdk-->
<script src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
var oEditors = [];
smartEditorInit("content", true, "editor", false);

function submitContents(formObj) {  
   if (sendContents("content", true))
   {    
      try {
         formObj.content.value = Base64.encode(formObj.content.value);
         return form_chk(formObj);
      } catch(e) {return false;}
   }
   return false;
}

</script>

<div class="btn"><button type="submit" class="btn btn-primary"><?=_("확인")?></button></div>
</div>

</form>

<?include "../_pfooter.php"?>