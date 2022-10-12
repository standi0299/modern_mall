<?

/*
* @date : 20180104 (20180104)
* @author : kdk
* @brief : 패키지 상품 관련 파라미터 추가.
* @request : 
* @desc : package_mode,cartno,rurl
* @todo :
*/

include "../lib/library.php";

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
			var yellowkitaddress = "<?=$_GET[yellowkitaddress]?>"; //20150616 / minks / yellowkit 주소
			if (!yellowkitaddress) alert('<?=_("장바구니에 저장됩니다.")?>');
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
			alert('<?=_("장바구니에 저장됩니다.")?>');
			$j.ajax({
				type: "post",
				url: "../admin/goods/indb.php",
				data: "mode=editor&goodsno=<?=$_GET[goodsno]?>&optionid=<?=$_GET[optionid]?>&mid=<?=$_GET[mid]?>&storageid=" + result,
				success: window.history.back()
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
			//var url = (centerid!="wpod" && skin!="bizcard") ? "../order/cart.php" : "../order/cartwpod.php";
			var yellowkitaddress = "<?=urlencode($_GET[yellowkitaddress])?>"; //20150407 / minks / yellowkit 주소, 핸드폰, 인사말
			var yellowkitphone = "<?=$_GET[yellowkitphone]?>";
			var yellowkitgreeting = "<?=urlencode($_GET[yellowkitgreeting])?>";
			var yellowkitemoticon = "<?=$_GET[yellowkitemoticon]?>"; //20150408 / minks / yellowkit 이모티콘
			
			var url = "../order/cart.php";
			if (centerid=="wpod" || skin=="bizcard") {
				if (yellowkitaddress) url = "../yellowkit/module_preview.php?storageid="+storageid + "&yellowkitaddress=" + yellowkitaddress + "&yellowkitphone=" + yellowkitphone + "&yellowkitgreeting=" + yellowkitgreeting  + "&yellowkitemoticon=" + yellowkitemoticon;
				else url = "../order/cartwpod.php";
			}
			else if (skin=="pod_group") {
				url = "../mypage/orderlist.php"; //ibizcard pod_group 추가 by 2014.07.21 kdk
			}
			
			$j.ajax({
				type: "post",
				url: "../order/indb.php",
				data: "mode=wpodeditupdate&storageid=" + storageid
				//success: window.location.href=url
			});
			if (yellowkitaddress) {
				location.href=url;
			}
			else {
				alert('<?=_("편집이 정상적으로 저장되었습니다.")?>');
				parent.location.href=url;
			}
			break;
	}
}

function exec(mode){
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

<?
	if ($_GET[yellowkitaddress])
		$formTarget = "";
	else 
		$formTarget = "_parent";
?>

<form name="fmView" target="<?=$formTarget?>">
<input type="hidden" name="mode"/>
<input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>"/>
<input type="hidden" name="productid" value="<?=$_GET[productid]?>"/>
<input type="hidden" name="podoptno"/>
<input type="hidden" name="storageid"/>
<input type="hidden" name="storageids"/>
<input type="hidden" name="optno" value="<?=$_GET[optno]?>"/>
<input type="hidden" name="addopt" value="<?=$_GET[addopt]?>"/>
<input type="hidden" name="yellowkitaddress" value="<?=$_GET[yellowkitaddress]?>"/>
<input type="hidden" name="yellowkitphone" value="<?=$_GET[yellowkitphone]?>"/>
<input type="hidden" name="yellowkitgreeting" value="<?=$_GET[yellowkitgreeting]?>"/>
<input type="hidden" name="yellowkitemoticon" value="<?=$_GET[yellowkitemoticon]?>"/>

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
<?  
}
?>
<input type="hidden" name="wpod_mode" value="Y"/>

<input type="hidden" name="est_order_type" value="EDITOR"/>

</form>

<? if ($_GET[result]=="30"){ ?>

<script>
xs_ret("0|<?=$_GET[storageid]?>");
</script>

<? } else { ?>

<script>
xs_ret("1|<?=$_GET[storageid]?>");
</script>

<? } ?>