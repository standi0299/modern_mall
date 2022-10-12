<?

/**
 * PODStation Call Editor v2
 * 2016.03.16 by kdk
 * PODStation 편집기 호출 class 와 분리함.
 * 빠른 시일안에 include file 파일에서 처리하는 script부분을 통합할 예정.
 */

include "../lib/library.php";
include "../lib/class.pods.service.php";

function error_msg($msg){
    echo "<script>alert('$msg');parent.closeLayer();</script>";
    exit;
}

# cookie check
if (!$_COOKIE[cartkey] && !$sess[mid] && !$sess_admin[mid]) error_msg(_("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.")."\\n"._("쿠키가 허용되어야만 정상적인 이용이 가능하십니다."));

if($cfg[member_system][order_system] == "edit_close" && !$sess[mid] && !$sess_admin[mid]){
   msgNlocationReplace("비회원은 편집을 할 수 없습니다.로그인을 해주세요.", "../member/login.php?rurl=/main/index.php", "Y");
   exit;
}

# goodsno check
if (!$_GET[goodsno]){
    error_msg(_("상품정보가 없습니다."));
}

# get params
$pods_use = $_GET[pods_use];
$podskind = $_GET[podskind];
$podsno = $_GET[podsno];
$mode = $_GET[mode];
$goodsno = $_GET[goodsno];
$optno = $_GET[optno];
$addopt = $_GET[addopt];
$productid = $_GET[productid];
$optionid = $_GET[optionid];
$ea = $_GET[ea];
$cover_id = $_GET[cover_id];
$coverrangedata = explode("_", $cover_id);
$coverrangedata = "size$".$coverrangedata[0]."^cover_type$".$coverrangedata[1]."^desc_paper$".$coverrangedata[2];
$startdate = $_GET[startdate];

//$coverrangedata = json_encode($coverrangedata);

//debug($_GET);

# pods editor class
$pods = new PodsEditor($goodsno);

//상품에 설정된 pods 정보.
$editor_arr = $pods->GetPodsEditor();

if($editor_arr) {
	foreach ($editor_arr as $key => $val) {
		if($val[pods_use] == $pods_use && $val[podskind] == $podskind && $val[podsno] == $podsno) {
			$editor = $val;
		}
	}
}

//pods편집기별 호출에 필요한 data 생성.
$info = $pods->GetPodsEditorData($editor);

//파라미터에 토큰 및 센터아이디가있는경우 info에 추가함. 211113 jtkim
if($_GET[token]){
  $info[token] = $_GET[token];
}

if($_GET[center_id]){
  $info[center_id] = $_GET[center_id];
}

//debug($info);
//통합형 북 옵션 변경 데이터
if($_REQUEST[mode] == "edit") {
   list($goods_options) = $db->fetch("select editor_return_json from tb_editor_ext_data where storage_id = '$_REQUEST[storageid]'",1);

   if($goods_options){
      $goods_options = json_decode($goods_options,1);

      //debug($goods_options);

      $info[podsno] = $goods_options[code];
      $info[productid] = $goods_options[code];
   }
}
//debug($info); exit;

# goods data check
if (!$info){
    error_msg(_("상품정보가 없습니다."));
}

# include file 정의
$inc = "_inc.editor.".$info[module].".php";
?>
<script src="/js/webtoolkit.base64.js"></script>
<script>
function xs_ret(ret){
   var mode = "<?=$_GET[mode]?>";

   var editmode = "<?=$_GET[editmode]?>"; //savaas 경우 장바구니 처리.
   if(editmode == "saveas") mode = "view";

   var err = ret.slice(0,1);
   var result = ret.slice(2);

   var center_cid = "<?=$cfg_center[center_cid]?>";
   var sess_id = "<?=$sess[mid]?>";
   var skin_name = "<?=$cfg[skin]?>";

   var addData = "";

   if (err == "1"){
      //(podgroup)일 경우. (메시지 출력 안함.)
  		if (center_cid=="podgroup" || skin_name=="photowiki"){
         if(mode == "edit_admin") { //관리자 페이지에서 오픈
            parent.closeLayer();
         }
  		} else {
         //alert("편집이 중단되었습니다.\n"+result);
        	alert('<?=_("편집을 취소하셨습니다.")?>'+'\n'+'<?=_("오늘도 좋은 하루 보내세요.")?>'+'\n'+'<?=_("감사합니다.")?>'+' ^..^');
    	}
      parent.closeLayer();
      return;
   }

   if (!result){
      //(podgroup)일 경우. (메시지 출력 안함.)
  		if (center_cid=="podgroup"){
  		} else {
         alert('<?=_("편집정보가 유실되었습니다.")?>');
      }
      parent.closeLayer();
      return;
   }

   switch (mode){
      case "view":
         parent.document.fmView.storageid.value = result;
         if (skin_name == 'kids')
            alert('<?=_("보관함에 저장됩니다.")?>');
         else
            alert('<?=_("장바구니에 저장됩니다.")?>');
         parent.document.fmView.method = "post";

         if(editmode == "saveas")
            parent.exec('cart', 'saveas');
         else
            parent.exec('cart');
      break;

      case "order":
      //비회원일 경우. (podgroup)
         if (center_cid=="podgroup" && sess_id==""){
            //alert("비회원으로 편집하셨습니다. 적절한 페이지로 이동합니다..");
  				//parent.document.location='/service/info.php';
  				//break;
         }

         parent.document.fmView.storageid.value = result;
         alert('<?=_("편집리스트에 저장됩니다.")?>');
         parent.document.fmView.method = "post";
         parent.exec('order');
      break;

      case "podgroup_edit": //편집 수정 완료 후 재합성 처리 20140103 by kdk
         alert('<?=_("편집이 정상적으로 종료되었습니다.")?>');
         var storageid = "<?=$info[storageid]?>";
         var payno = "<?=$_GET[payno]?>";
         var ordno = "<?=$_GET[ordno]?>";
         var ordseq = "<?=$_GET[ordseq]?>";

         if (!storageid){
            storageid = result;
         }
         $.ajax({
            type: "post",
            url: "../order/indb.php",
            data: "mode=podgroup_editupdate&storageid="+storageid+"&payno="+payno+"&ordno="+ordno+"&ordseq="+ordseq,
            async: false,
            success: parent.document.location.reload(),
            error:function(request,status,error){
               alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
    			}
         });

      break;

      case "proxy":
         alert('<?=_("장바구니에 저장됩니다.")?>');
         $.ajax({
             type: "post",
             url: "../admin/goods/indb.php",
             data: "mode=editor&goodsno=<?=$data[goodsno]?>&optionid=<?=$info[optionid]?>&mid=<?=$sess[mid]?>&storageid=" + result,
             success: parent.window.close()
         });
      break;

      case "edit_admin": //관리자 페이지에서 오픈
     	   alert('<?=_("편집이 정상적으로 종료되었습니다.")?>');
         parent.closeLayer();
      break;

      default:
         alert('<?=_("편집이 정상적으로 종료되었습니다.")?>');
         var storageid = "<?=$info[storageid]?>";
         if (!storageid){
             storageid = result;
         }

         var podskind = '<?=$podskind?>';
         if(podskind == "3055"){
            var data = Base64.encode(result);
            addData = "&data=" + data;
         }

         $.ajax({
            type: "post",
            url: "../order/indb.php",
            data: "mode=editupdate&storageid=" + storageid + addData,
            async: false,
            success: parent.location.reload()
         });
      break;
    }

    return;
}
</script>

<? include $inc; ?>
