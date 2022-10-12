<?
include "../lib/library.php";
include "../lib/class.pods.service.php";

function error_msg($msg){
   echo "<script>alert('$msg');parent.closeLayer();</script>";
   exit;
}
$browser = chkIEBrowser();

# cookie check
if (!$_COOKIE[cartkey] && !$sess[mid] && !$sess_admin[mid]) error_msg(_("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.")."\\n"._("쿠키가 허용되어야만 정상적인 이용이 가능하십니다."));

if($cfg[member_system][order_system] == "edit_close" && !$sess[mid] && !$sess_admin[mid]){
   msgNlocationReplace(_("비회원은 편집을 할 수 없습니다.로그인을 해주세요."), "../member/login.php", "Y");
   exit;
}

# pods editor class
$pods = new PodsEditor($_GET[goodsno]);

//상품에 설정된 pods 정보.
$editor_arr = $pods->GetPodsEditor();

if($editor_arr) {
   foreach ($editor_arr as $key => $val) {
      if($pods_use && $podskind && $podsno){
         if($val[pods_use] == $pods_use && $val[podskind] == $podskind && $val[podsno] == $podsno) {
            $editor = $val;
         }
      } else {
         $editor = $val;
      }
   }
}
//debug($editor);

//pods편집기별 호출에 필요한 data 생성.
$info = $pods->GetPodsEditorData($editor);

/*
//왜 넣었는지 모르겠음. 여기서 ,로 묶으면 _inc.파일에서 오류가 나서 주석처리함 / 17.03.30 / kjm
if (is_array($info[addopt])) {
   if ($info[addopt])
      $info[addopt] = implode(",", $info[addopt]);
} else if ($info[addopt] == "") $info[addopt] = "";
*/

if (!$_GET[goodsno]){
   error_msg(_("상품정보가 없습니다."));
}

if($_GET[mid])
   $sess[mid] = $_GET[mid];

//아래의 자체편집상품일경우에도 변수룰 dummy를 사용하기 때문에 위에서 사용한 dummy값 초기화 / 14.10.13 / kjm
$dummy = '';

# podstation siteid
if (!$cfg[podsiteid]) $cfg[podsiteid] = $cfg_center[podsiteid];

# 자체편집상품일경우
// 조건 오류로 인해 자체상품 편집기 호출되지 않았음. 조건 수정   20131202    chunter
// cfg 배열을 변경할 경우 추후 센터 상품 편집기 호출시 문제됨. 변수 할당으로 변경.   20131202    chunter
$data[podsiteid] = $cfg[podsiteid];
if ($data[pods_useid]!="center" && $data[podsno]){
   list($dummy) = $db->fetch("select self_podsiteid from exm_mall where cid = '$cid'",1);
   //if ($dummy) $cfg[podsiteid] = $dummy;
   if ($dummy) $data[podsiteid] = $dummy;
}

# 기업그룹 판매가및 적립금 보정
if ($sess[bid]) $data[price] = get_business_goods_price($data[goodsno],$data[price]);
if ($sess[bid]) $data[reserve] = get_business_goods_reserve($data[goodsno],$data[reserve]);

if($info[module] == "p20mweb")
   $_GET[mode] = "outside";

# include file 정의
$inc = "_inc.editor.".$info[module].".php";
//debug($inc);
if($cfg[outside_cancel]) $_GET[rurl] = $cfg[outside_cancel];

?>

<script src="/js/jquery-1.9.1.min.js"></script>

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

   if (err == "1"){
   //(podgroup)일 경우. (메시지 출력 안함.)
      if (center_cid=="podgroup" || skin_name=="photowiki"){
      } else {
         //alert("편집이 중단되었습니다.\n"+result);
         alert('<?=_("편집을 취소하셨습니다.")?>'+'\n'+'<?=_("오늘도 좋은 하루 보내세요.")?>'+'\n'+'<?=_("감사합니다.")?>'+' ^..^');
      }

      if (mode == "outside")
         parent.location.href = "<?=$_GET[rurl]?>";
      else if("<?=$_GET[rurl]?>")
         location.href = "<?=$_GET[rurl]?>";
      else 
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
            parent.document.location='/service/info.php';
            break;
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
      
      case "outside":
         document.fmView.storageid.value = result;
         document.fmView.storageids.value = "<?=$_GET[storageids]?>";
         
         document.fmView.method = "post";
         exec_outside('cart');
      break;

      default:
         alert('<?=_("편집이 정상적으로 종료되었습니다.")?>');
         var storageid = "<?=$info[storageid]?>";
         if (!storageid){
            storageid = result;
         }
         $.ajax({
            type: "post",
            url: "../order/indb.php",
            data: "mode=editupdate&storageid=" + storageid,
            async: false,
            success: parent.location.reload()
         });
      break;
   }
   return;
}

function exec_outside(mode){
   if (!navigator.cookieEnabled){
      alert('<?=_("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.")?>'+'\n'+'<?=_("쿠키가 허용되어야만 정상적인 이용이 가능하십니다.")?>');
      return;
   }
   
   url = (mode!="wish") ? "../order/cart.php" : "../mypage/wishlist.php";

   var centerid = "<?=$cfg_center[center_cid]?>"; //cartwpod.php를 사용하는 센터로 인해 체크함 by 2014.05.15 kdk
   var skin = "<?=$cfg[skin]?>"; //20140522 / minks / skin이 bizcard일 때 cartwpod로 이동
   var storageid = "<?=$_GET[storageid]?>";
   var yellowkitaddress = "<?=$_GET[yellowkitaddress]?>"; //20150616 / minks / yellowkit 주소
   if (centerid=="wpod" || skin=="bizcard") {
      if (yellowkitaddress) url = "../yellowkit/module_preview.php?storageid="+storageid;
      else url = "../order/cartwpod.php";
   }
   else if (skin=="pod_group") url = "../order/cart_n_order.php"; //WPOD 편집기 연동 2014.07.14 by kdk

   var fm = document.fmView;
   fm.action = url;
   fm.mode.value = mode;
   fm.submit();
}
</script>

<? $formTarget = "_parent"; ?>

<form name="fmView" target="<?=$formTarget?>">
<input type="hidden" name="mode"/>
<input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>"/>
<input type="hidden" name="productid" value="<?=$_GET[productid]?>"/>
<input type="hidden" name="podoptno"/>
<input type="hidden" name="storageid"/>
<input type="hidden" name="storageids"/>
<input type="hidden" name="optno" value="<?=$_GET[optno]?>"/>
<input type="hidden" name="addopt" value="<?=$info[intro_addopt]?>"/>
<input type="hidden" name="cart_seq" value="<?=$_GET[cart_seq]?>"/>
<input type="hidden" name="pay_method" value="<?=$_GET[pay_method]?>"/>
<input type="hidden" name="mid" value="<?=$_GET[mid]?>"/>
</form>

<? if($_GET[mode] == "outside") { ?>
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

<? include $inc; ?>