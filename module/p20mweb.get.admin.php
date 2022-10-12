<?

include "../lib/library.php";

if($_REQUEST[editor_type] == "web"){
   $podskind = $_GET[podskind];
   
   $data = $_REQUEST[storageid];
   $data = explode("|", $data);
   $data = base64_encode(stripslashes($data[1]));
}
//debug($cid);
//debug($cfg_center[center_cid]); 
//exit;
?>

<script src="/js/jquery.js"></script>
<script>var $j = jQuery.noConflict();</script>

<script>
function xs_ret(ret){

	var mode = "<?=$_GET[mode]?>";
	var err = ret.slice(0,1);
	var result = ret.slice(2);
	var editor_type = "<?=$_GET[editor_type]?>";

	if (err == "1"){
		//alert("편집을 취소하셨습니다.\n"+result);
		alert('<?=_("편집을 취소하셨습니다.")?>'+'\n'+'<?=_("오늘도 좋은 하루 보내세요.")?>'+'\n'+'<?=_("감사합니다.")?>'+' ^..^');
		parent.close();
		return;
	}
	if (!result){
		alert('<?=_("편집정보가 유실되었습니다.")?>');
		parent.close();
		return;
	}

	var storageid = result;
	var yellowkitaddress = "<?=urlencode($_GET[yellowkitaddress])?>"; //20150407 / minks / yellowkit 주소, 핸드폰, 인사말
	var yellowkitphone = "<?=$_GET[yellowkitphone]?>";
	var yellowkitgreeting = "<?=urlencode($_GET[yellowkitgreeting])?>";
	var yellowkitemoticon = "<?=$_GET[yellowkitemoticon]?>"; //20150408 / minks / yellowkit 이모티콘
	
	if(editor_type == "web"){
      var data = "<?=$data?>";
      storageid = data;
   }

	$j.ajax({
		type: "post",
		url: "../order/indb.php",
		data: "mode=wpodeditupdate&storageid=" + storageid + "&yellowkitaddress=" + yellowkitaddress + "&yellowkitphone=" + yellowkitphone + "&yellowkitgreeting=" + yellowkitgreeting + "&yellowkitemoticon=" + yellowkitemoticon
		//success: parent.close()
	});
	alert('<?=_("편집이 정상적으로 저장되었습니다.")?>');
	if(editor_type == "web"){
      opener.parent.closeWebEditor();
      window.close();
   } else 
      parent.close();
}
</script>

<? if($_REQUEST[editor_type] == "web") { ?>

   <script>
   xs_ret('<?=$_REQUEST[storageid]?>');
   </script>
   
<? } else { ?>

   <? if ($_GET[result]=="30"){ ?>
   
   <script>
   xs_ret("0|<?=$_GET[storageid]?>");
   </script>
   
   <? } else { ?>
   
   <script>
   xs_ret("1|<?=$_GET[storageid]?>");
   </script>
   
   <? } ?>
<? } ?>
