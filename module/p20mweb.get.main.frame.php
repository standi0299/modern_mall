<?

/*
* @date : 20180808
* @author : kdk
* @brief : 알래스카프린트 wpod 편집기용  editor_type 파라미터 추가.
* @request : 
* @desc : come_back="web_list" : 템플릿리스트에서 편집기 실행, come_back="web_view" : 상품상세페이지에서 편집기 실행.
* @todo :
*/

/*
* @date : 20180104 (20180104)
* @author : kdk
* @brief : 패키지 상품 관련 파라미터 추가.
* @request : 
* @desc : package_mode,cartno,rurl
* @todo :
*/

include "../lib/library.php";

header("Access-Control-Allow-Headers: Origin, Accept, X-Requested-With, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Max-Age: 3600");

if($_REQUEST[editor_type] == "web"){
   $podskind = $_GET[podskind];
   
   $data = $_REQUEST[storageid];
   $data = explode("|", $data);
   $data = base64_encode(urlencode(stripslashes($data[1])));
}
/*elseif($_REQUEST[editor_type] == "web_list" || $_REQUEST[editor_type] == "web_view") {
    $ret_data = explode("|", $_REQUEST[storageid]);
    $ret = stripslashes($ret_data[1]);

    $ret = json_decode($ret,1);
    //debug($ret);
    if($ret[uploaded_list]) {
        foreach ($ret[uploaded_list] as $k=>$v){
            //$storageid = $v[rsid];
        }
    }
    
    if($ret[storageId]) $storageid = $ret[storageId];
    //debug($storageid);
}*/

if($_GET[package_mode] == 'Y')
   $_GET[rurl] = urldecode(base64_decode($_GET[rurl]));
//debug($cid);
//debug($cfg_center[center_cid]);
//exit;
?>

<script src="/js/jquery.js"></script>
<script>var $j = jQuery.noConflict();</script>

<script>
function xs_ret(ret){
//alert(ret);
   var mode = "<?=$_GET[mode]?>";
   var err = ret.slice(0,1);
   //alert(err);
   var result = ret.slice(2);
   var editor_type = "<?=$_GET[editor_type]?>";
   var exit_type = "<?=$_REQUEST[exit_type]?>";
   
   if (err == "1"){
      //alert("편집을 취소하셨습니다.\n"+result);
      alert('<?=_("편집을 취소하셨습니다.")?>'+'\n'+'<?=_("오늘도 좋은 하루 보내세요.")?>'+'\n'+'<?=_("감사합니다.")?>'+' ^..^');
      //history.go(-2);
      //alert("<?=$_GET[rurl]?>")
      parent.location.href = "<?=$_GET[rurl]?>";
      return;
   }
   if (!result){
      alert('<?=_("편집정보가 유실되었습니다.")?>');
      //history.go(-2);
      //alert("<?=$_GET[rurl]?>")
      parent.location.href = "<?=$_GET[rurl]?>";
      return;
   }

   switch (mode){
      case "view":
         document.fmView.storageid.value = result;
         document.fmView.storageids.value = "<?=$_GET[storageids]?>";
         var package_mode = "<?=$_GET[package_mode]?>";
         
         if(package_mode == "Y" || editor_type == "web_list" || editor_type == "web_view") { // || editor_type == "web_list" // || editor_type == "web_view"
             alert('<?=_("편집이 정상적으로 저장되었습니다.")?>');
             if (exit_type == "save") return; //임시저장시 화면이동을 하지 않는다. 
         }
         //else {
         //   if (exit_type == "cart") //alert('<?=_("장바구니에 저장됩니다.")?>');
         //}

         document.fmView.method = "post";
         exec('cart');

      break;

      case "order":
         document.fmView.storageid.value = result;
         document.fmView.storageids.value = "<?=$_GET[storageids]?>";

         alert('<?=_("편집리스트에 저장됩니다.")?>');
         document.fmView.method = "post";
         exec('order');
      break;

      case "proxy":
         //alert('<?=_("장바구니에 저장됩니다.")?>');
         $j.ajax({
            type: "post",
            url: "../admin/goods/indb.php",
            data: "mode=editor&goodsno=<?=$_GET[goodsno]?>&optionid=<?=$_GET[optionid]?>&mid=<?=$_GET[mid]?>&storageid=" + result,
            success: window.history.go(-2)
         });
      break;

      case "edit_admin": //관리자 페이지에서 오픈
         alert('<?=_("편집이 정상적으로 종료되었습니다.")?>');
         parent.window.close();
      break;

      default:
         
         var storageid = result;
         var centerid = "<?=$cfg_center[center_cid]?>"; //cartwpod.php를 사용하는 센터로 인해 체크함 by 2014.05.15 kdk
         var skin = "<?=$cfg[skin]?>"; //20140522 / minks / skin이 bizcard일 때 cartwpod로 이동

         if(editor_type == "web"){
            var data = "<?=$data?>";
            storageid = data;
         }
         
         var url = "../order/cart.php";
         
         $j.ajax({
            type: "post",
            url: "../order/indb.php",
            data: "mode=wpodeditupdate&storageid=" + storageid
            //success: parent.document.location.href=url
         });

         if(editor_type == "web"){
            alert('<?=_("편집이 정상적으로 저장되었습니다.")?>');
            opener.parent.location.reload();
            window.close();
         } else {
           alert('<?=_("편집이 정상적으로 저장되었습니다.")?>');
           parent.location.href=url;
         }

      break;
   }
}

function exec(mode){

   var editor_type = "<?=$_GET[editor_type]?>";
   var exit_type = "<?=$_REQUEST[exit_type]?>";
   if (!navigator.cookieEnabled){
      alert('<?=_("현재 고객님의 브라우저는 쿠키를 허용하고 있지않습니다.")?>'+'\n'+'<?=_("쿠키가 허용되어야만 정상적인 이용이 가능하십니다.")?>');
      return;
   }

   url = (mode!="wish") ? "../order/cart.php" : "../mypage/wishlist.php";

   var centerid = "<?=$cfg_center[center_cid]?>"; //cartwpod.php를 사용하는 센터로 인해 체크함 by 2014.05.15 kdk
   var skin = "<?=$cfg[skin]?>"; //20140522 / minks / skin이 bizcard일 때 cartwpod로 이동
   var storageid = "<?=$_GET[storageid]?>";
   
   var package_mode = "<?=$_GET[package_mode]?>";
   
   fm = document.fmView;
   fm.mode.value = mode;
   
   if(editor_type == "web")
   {
      $j.ajax({
         type : "POST",
         url : url,
         data : $j("#fm").serialize(),
         async : false,
         success : function(data) {
            if(exit_type == "cart"){
               if(package_mode == "Y"){
                  opener.location.reload();
               }
               else
                  opener.parent.location.href = "../order/cart.php";

               window.close();
            }
         }
      });
   }
   //else if(editor_type == "web_list") {
   //     //템플릿리스트에 편집기가 실행되어 상품상세페이지로 바로 이동함.
   //     fm = document.fmView2;
   //     fm.method = "get";
   //     url = "../goods/view.php?editor_type=web_list&"+$j("#fm2").serialize();
   //     fm.action = url;
   //     fm.submit();
   //}
   //else if(editor_type == "web_view") {
   //     //상품상세페이지에서 편집기가 실행되어 견적데이타 유지를 위해 sid만 전달함.
   //     try {
   //         //iframe에서 부모 창 함수 호출
   //         parent.frameRetValue('<?=$storageid?>'); 
   //         self.close();
   //     }catch(e) { 
   //         alert("Error:"+e); 
   //     }
   //}
   else {
      fm.action = url;
      fm.submit();
   }
}
</script>

<?
   $formTarget = "_parent";
?>

<form name="fmView" id="fm" target="<?=$formTarget?>">
<input type="hidden" name="mode"/>
<input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>"/>
<input type="hidden" name="productid" value="<?=$_GET[productid]?>"/>
<input type="hidden" name="podoptno"/>
<input type="hidden" name="storageid"/>
<input type="hidden" name="storageids"/>
<input type="hidden" name="ea" value="<?=$_GET[ea]?>"/>
<input type="hidden" name="optno" value="<?=$_GET[optno]?>"/>
<input type="hidden" name="addopt" value="<?=$_GET[addopt]?>"/>

<input type="hidden" name="editor_type" value="<?=$_GET[editor_type]?>">

<?
#복수 편집기 처리 pods_use, podskind, podsno _frame_main_wpod_edit_v2.php에서 넘기 2016.03.16 by kdk
if($_GET[pods_use] && $_GET[podskind] && $_GET[podsno]) {
?>
<input type="hidden" name="pods_use" value="<?=$_GET[pods_use]?>"/>
<input type="hidden" name="podskind" value="<?=$_GET[podskind]?>"/>
<input type="hidden" name="podsno" value="<?=$_GET[podsno]?>"/>
<?
}
### 복수 편집기 견적 정보 임시 저장을 20160325 by kdk
if($_GET[pod_signed] && $_GET[pod_signed] != "") { 
?>
<input type="hidden" name="pod_signed" value="<?=$_GET[pod_signed]?>"/>
<? 
}
//패키지상품 관련 파라미터 추가 / 20180104 / kdk
if($_GET[package_mode]) {
?>
<input type="hidden" name="package_mode" value="<?=$_GET[package_mode]?>"/>
<input type="hidden" name="cartno" value="<?=$_GET[cartno]?>"/>
<input type="hidden" name="rurl" value="<?=$_GET[rurl]?>"/>
<? } ?>
<input type="hidden" name="wpod_mode" value="Y"/>

<input type="hidden" name="est_order_type" value="EDITOR"/>

</form>

<form name="fmView2" id="fm2" target="<?=$formTarget?>">
<input type="hidden" name="editor_type" value="web_list"/>
<input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>"/>
<input type="hidden" name="storageid" value='<?=$storageid?>'/>
</form>

<? if($_REQUEST[editor_type] == "web" || $_REQUEST[editor_type] == "web_list" || $_REQUEST[editor_type] == "web_view") { ?>

   <script>
   xs_ret('<?=$_REQUEST[storageid]?>');
   </script>
   
<? } else { ?>
   
   <? if ($_REQUEST[result]=="30"){ ?>
   
   <script>
   xs_ret("0|<?=$_REQUEST[storageid]?>");
   </script>

   <? } else { ?>
   
   <script>
   xs_ret('1|<?=$_REQUEST[storageid]?>');
   </script>
   
   <? } ?>
<? } ?>