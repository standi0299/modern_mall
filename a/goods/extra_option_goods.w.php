<?
include "../_header.php";
include "../_left_menu.php";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<link href="/css/table.css" rel="stylesheet">

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("인쇄견적 옵션설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("인쇄견적 옵션설정")?> <small><?=_("등록된 상품의 인쇄견적 옵션설정 정보를 보실 수 있습니다.")?></small></h1>
      
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("인쇄견적 옵션설정")?></h4>
      </div>
     
      <div class="panel-body">
         <div class="table-responsive">
         	<!-- begin #content -->

<? 
if ($_GET[goodsno]){
	//$data = $db -> fetch("select * from exm_goods where goodsno='$_GET[goodsno]'");
	$mGoods = new M_goods();
	$data = $mGoods->getInfo($_GET[goodsno]);
	
	if (!$data){
		msg(_("상품데이터가 존재하지 않습니다."),-1);
		exit;
	}
	$fkey = $data[goodsno];
	
	//판매중인 몰 확인 2014.08.26 kdk
	if ($cid == $cfg_center[center_cid]) 
    {
		//$query = "select cid from exm_goods_cid where goodsno='$data[goodsno]'";
		//$res = $db->query($query);
		$res = $mGoods->getGoodsCidInfo("", $data[goodsno]);
		while ($tmp = $db->fetch($res)){
			if($tmp[cid] != "")	
				$data[cids][] = $tmp[cid];
		}  
	}

	//자동견적옵션 기본등록 프리셋별 처리
	//debug($data[extra_option]);
	$extra_option = explode('|',$data[extra_option]); //항목 분리
	if(count($extra_option) > 0) {
		if($extra_option[1] == "100106") $extra_option[0] = "CARD";
		$extra_product = $extra_option[0];
		$extra_preset = $extra_option[1];
		$extra_price_type = $extra_option[2];
		//$extra_editor = $extra_option[3];
		
		if($data[goods_group_code] == "20") {//스튜디오
			$extra_stu_order = $extra_option[3];
		}
	}	
	//exit;	
	$presetno = $extra_preset;
	if (!$presetno) {
    	$presetno = "100102";
  	}
	
	//자동견적옵션복사를 위한 source 용 상품정보 조회 2016.04.05 by kdk.
	if ($data[extra_option]) 
    {
		//$query = "select goodsno, goodsnm from exm_goods where extra_option = '$data[extra_option]' and goodsno !='$data[goodsno]'";
		//$res = $db->query($query);
		$res = $mGoods->getCopyGoodsExtraOption($data[extra_option], $data[goodsno]);
		while ($tmp = $db->fetch($res)){
			if($tmp[goodsno] != "")	
				$data[source_goods][] = array('goodsno' => $tmp[goodsno], 'goodsnm' => $tmp[goodsnm]); ;
		}  
	}
	//debug($data);
  
  	//$fkey = "100102";
  	//$presetno= "100102";
  	//$_GET[goodsno] = $presetno;
  
  	//<!--자동견적 자체상품 추가 2015.06.02 by kdk-->
  	$checkRegCid = FALSE; //자체상품 여부
  	if($data[reg_cid] && $data[reg_cid] = $cid) $checkRegCid = TRUE;
  
  	$mExtraOption = new M_extra_option();
  	$opt_data = $mExtraOption->getAdminOptionUseList($cid, $cfg_center[center_cid], $_GET[goodsno]);
  	if (!$opt_data) {
		$db->start_transaction();
    	try {
	      	//센터일 경우 preset 정보를 복사한다.
	      	//<!--자동견적 자체상품 추가 2015.06.02 by kdk-->
	      	//자체상품의 경우 preset code 에서 상품 복사해서 넣어준다.
      		if ($cid == $cfg_center[center_cid] || $checkRegCid) 
      		{
		        //센터인 경우 preset code 에서 상품 복사해서 넣어준다.
		        $mExtraOption->CopyMasterExtraOption($cid, $cfg_center[center_cid], $_GET[goodsno], $extra_price_type, $presetno);
		        $mExtraOption->CopyMasterExtraAfterOption($cid, $cfg_center[center_cid], $_GET[goodsno], $presetno);
		        $mExtraOption->CopyMasterExtraOptionUse($cid, $cfg_center[center_cid], $_GET[goodsno], $presetno);
		        $mExtraOption->CopyOrderCntExtraOption($cid, $cfg_center[center_cid], $_GET[goodsno], $presetno);
                
                //모던->상품->견적옵션->이미지링크 정보 전체 복사.
                $mExtraOption->CopyMasterExtraOptionImg($cid, $cfg_center[center_cid], $_GET[goodsno], $presetno);
      		} else {
		        //몰인경우 센터에서 상품을 복사한다.
		        $mExtraOption->CopyMasterExtraOption($cid, $cfg_center[center_cid], $_GET[goodsno], $extra_price_type, $_GET[goodsno]);
		        $mExtraOption->CopyMasterExtraAfterOption($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[goodsno]);
		        $mExtraOption->CopyMasterExtraOptionUse($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[goodsno]);
		        $mExtraOption->CopyOrderCntExtraOption($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[goodsno]);
				
				//가격 정보를 복사한다.
				$mExtraOption->CopyPriceExtraOptionS2($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[goodsno]);
                
                //모던->상품->견적옵션->이미지링크 정보 전체 복사.
                $mExtraOption->CopyMasterExtraOptionImg($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[goodsno]);
      		}
      
      		$db->query("commit");
    	} catch(Exception $e) {
      		$db->query("rollback");
      
      		msg(_("프리셋 옵션 등록중 오류가 발생되었습니다.").$e->getMessage(),-1);
      		exit;
    	}    
  	}
  	//<!--자동견적 자체상품 추가 2015.06.02 by kdk-->
  
	$clsExtraOption = new ExtraOption();
	
	//$extraOption->SetCenterMallID('bpc', '');
	$clsExtraOption->SetPreset($presetno);
	$optionKindCodeArr = $clsExtraOption->GetOptionKind($_GET[goodsno]);
	$javascriptArrayTag = $clsExtraOption->javascriptArrayTag;
	$afterOptionKindIndex = $clsExtraOption->GetAfterOptionKindIndex();

	//debug($optionKindCodeArr);
 	//debug($clsExtraOption->DocumentSizeScriptTag);
	//debug($afterOptionKindIndex);
	//debug($presetno);
	//debug($r_est_preset_sub_option_group[$presetno]);
	//exit;
	$goodsMallUseFlag = "N";
	
	//판매중인 몰 확인 2014.08.26 kdk
	if ($cid == $cfg_center[center_cid]) 
    {
		if($data[cids])
		$goodsMallUseFlag = "Y";
	}
	
	if($presetno == "100110") {
		//스튜디오 견적 관련
		$t_product_name = $r_goods_group_code[$data[goods_group_code]]. "(". $r_stu_order[$extra_option[3]].")";
		$t_preset_name = $r_stu_preset[$presetno];
	}
	else {
		$t_product_name = $r_goods_group_code[$data[goods_group_code]]. "(". $r_est_product[$extra_product] .")";
		$t_preset_name = $r_est_preset[$extra_product][$presetno];
	}
	
	if ($extra_stu_order == "UPL") { //스튜디오업로드면 표지옵션 필수 2015.08.06 by kdk
		//debug($clsExtraOption->GetOptionKindUse("97"));
		if($clsExtraOption->GetOptionKindUse("97") == "N") {
			//옵션 사용여부 업데이트
    		$mExtraOption->setUpdateUseFlag($cid, $cfg_center[center_cid], $_GET[goodsno], "97", "Y");
			//주문제목,메모 사용안함.
			$mExtraOption->setUpdateUseFlag($cid, $cfg_center[center_cid], $_GET[goodsno], "98", "N");
			$mExtraOption->setUpdateUseFlag($cid, $cfg_center[center_cid], $_GET[goodsno], "99", "N");
		}
	}	
?>

	<link href="../../css/PopupLayer.css" rel="stylesheet">
	
	<script type="text/javascript" src="/js/extra_option/jquery_client.js"></script>
	<script type="text/javascript" src="/js/extra_option/jquery.ui.js"></script>
	<script type="text/javascript" src="/js/extra_option/jquery.form.js"></script>
  
  	<script type="text/javascript">
  	var cid = '<?=$cid?>';
	var center_id = '<?=$cfg_center[center_cid]?>';
	var optino_group_code = '[<?=$javascriptArrayTag?>]';

	var document_size = {<?=$clsExtraOption->DocumentSizeScriptTag?>};

	var goods_mall_use_flag = '<?=$goodsMallUseFlag?>';
	//alert(optino_group_code);
	//alert(document_size);
	//alert(goods_mall_use_flag);

	function deletePrice() {
		if (confirm('<?=_("가격 테이블을 초기화 하시겠습니까?")?>' + "\n" + '<?=_("삭제된 데이터는 복구할 수 없습니다.")?>')) {
			var fm = document.frm1;
			fm.action = "indb_option.php?mode=price_delete&goodsno=<?=$_GET[goodsno]?>";			
			fm.submit();
		}
		else {
			return false;
		}
	}

	function deletePrice2(mode,kind) {
		if (confirm('<?=_("가격 테이블을 초기화 하시겠습니까?")?>' + "\n" + '<?=_("삭제된 데이터는 복구할 수 없습니다.")?>')) {
			var fm = document.frm1;
			fm.action = "indb_option.php?mode=price_delete_s2&goodsno=<?=$_GET[goodsno]?>&mode2="+mode+"&kind="+kind;
			fm.submit();
		}
		else {
			return false;
		}
	}

	function deleteOptionJson() {
		if (confirm('<?=_("옵션 Json 파일을 초기화 하시겠습니까?")?>' + "\n" + '<?=_("삭제된 데이터는 복구할 수 없습니다.")?>')) {
			var fm = document.frm1;
			fm.action = "indb_option.php?mode=optionjson_delete&goodsno=<?=$_GET[goodsno]?>";
			fm.submit();
		}
		else {
			return false;
		}
	}

	function call(url, param, act) {
		//판매중인 몰 확인 2014.08.26 kdk
		var chk = checkGoodsMallUseFlag();

		if(window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}

		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				callResult(xmlhttp, act);
			}
		};

		xmlhttp.open("POST", url, true);
		//-- 여기부분을 안넣었더니 서버 페이지에서 POST를 받지 못함
		xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=UTF-8");
		xmlhttp.setRequestHeader("Cache-Control","no-cache, must-revalidate");
		xmlhttp.setRequestHeader("Pragma","no-cache");
		//-----------------------------------------------------

		if(chk) {
			xmlhttp.send(param);
		}
		else {
			return false;
		}
	}

	function callResult(xhr, act)
	{
		if(xhr.responseText == "OK") {
			//alert(xhr.responseText);
			alert('<?=_("완료되었습니다.")?>');
			if(act == "reload") {
				document.location.reload();
			}
		}
		else {
			alert('<?=_("실패하였습니다. 다시 시도하시기 바랍니다.")?>' + "[" + xhr.responseText + "]");
		}
	}
	
	//수량 설정시 시작~끝~증가 에서 끝,증가 0 입력 제한
	function numZeroChk(obj) {
		var val = $(obj).val();
	
		if (val == "0"){
			alert('<?=_("0 은 입력할 수 없습니다.")?>');
			$(obj).val("")
			return false;
		} else {
			return true;
		}
	}

  </script>
  <script type="text/javascript" src="/js/extra_option/goods.extra.option.admin.js"></script>
  <script type="text/javascript" src="/js/extra_option/goods.extra.option.admin.action.js"></script>
  <script type="text/javascript" src="/js/extra_option/admin.goods.extra.option.js"></script>  
  
  <form name="fmView" method="post" action="extra_option_indb.goods.php" enctype="multipart/form-data" onsubmit="return form_chk(this)">
  <input type="hidden" id="mode" name="mode" value="<?=$_GET[mode]?>"/>
  <input type="hidden" id="fkey" name="fkey" value="<?=$fkey?>"/>
  <input type="hidden" id="goodsno" name="goodsno" value="<?=$_GET[goodsno]?>"/>
  <input type="hidden" id="url" name="url" value="<?=$_SERVER[HTTP_REFERER]?>"/>  
  <input type="hidden" id="goodskind" name="goodskind" value="<?=$extra_product?>"/>
  </form>
  <!--자동견적옵션항목-->
  <table class="tb1">
  <tr>
    <th><?=_("상품종류")?></th>
    <td><?=$t_product_name?></td>
  
  </tr>
  <tr>
    <th><?=_("프리셋스타일")?></th>
    <td><?=$t_preset_name?></td>
  </tr>
  <!--<tr>
    <th>편집기종류</th>
    <td><?=$r_est_editor[$extra_editor]?></td>
  </tr>
  <tr>
    <th>수량옵션</th>
    <td><?=$r_est_item_price_type_info[$extra_price_type]?></td>
  </tr>
  -->
  <tr>
    <th><?=_("옵션")?></th>
    <td>
      
<?
if ($presetno == "100101")//낱장(사용안함)
	include "";
else if ($presetno == "100102")//낱장
	include "extra_option_preset_100102.php";
else if ($presetno == "100103")//책자(사용안함)
	include "";
else if ($presetno == "100104")//책자
	include "extra_option_preset_100104.php";
else if ($presetno == "100106")//책자
	include "extra_option_preset_100106.php";
else if ($presetno == "100108")//책자
	include "extra_option_preset_100108.php";
else if ($presetno == "100110")//책자 스튜디오 견적 프리셋1
	include "extra_option_preset_100110.php";
else if ($presetno == "100112")//책자 신규 프리셋3
	include "extra_option_preset_100112.php";
else if ($presetno == "100114")//낱장(기본프리셋)
    include "extra_option_preset_100114.php";

if ($presetno == "100110" || $presetno == "100112") {//책자 스튜디오 견적 프리셋1 || 책자 견적 프리셋3
	$priceUnitTitle = _("할인");
} else {
	$priceUnitTitle = _("수량(건수)");
}
?>

    </td>
  </tr>
  </table><br/>
  <!--자동견적옵션항목-->
  
  <!--자동견적옵션가격-->
  <form name="frm1" action="indb_option.php" method="post">
  <div class="stit"><?=_("견적 가격 관리")?></div>
  <table class="tb1">
  <tr>  	
    <th><?=_("테이블 타입")?></th>
    <td>
		<? foreach ($r_est_price_table_type as $k=>$v){?>
		<input type="radio" id="priceTableType" name="priceTableType" value="<?=$k?>" <?=($clsExtraOption->GetOptionKindUse("100") == $k) ? "checked='checked'" : ""?>><span class="absmiddle"><?=$v?></span>
		<? }?>
    </td>  	
  </tr>  	
  <?if ($presetno == "100110" || $presetno == "100112") {//책자 스튜디오 견적 프리셋1 || 책자 견적 프리셋3 ?>
  <tr>  	
    <th><?=$priceUnitTitle?></th>
    <td>
    	<button onclick="openPriceUnit(); return false;"><?=_("설정")?></button><!--2014.12.23 by kdk-->
    </td>  	
  </tr>
  <?}?>
  <tr>  	
    <th><?=_("옵션")?></th>
    <td>
    	
<?
if($presetno == "100102"){
?>
  <button onclick="openPrice('FIX','<?=$extraOption->GoodsKind?>','<?=_("기본")?>'); return false;"><?=_("기본")?></button>
<?
}
else if($presetno == "100104") {
?>
  <button onclick="openPrice('C-FIX','<?=$extraOption->GoodsKind?>','<?=_("표지")?>'); return false;"><?=_("표지")?></button>
  <button onclick="openPrice('FIX','<?=$extraOption->GoodsKind?>','<?=_("내지")?>'); return false;"><?=_("내지")?></button>
  
  <?if ($clsExtraOption->GetOptionKindUse("96") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="openPrice('M-FIX','<?=$extraOption->GoodsKind?>','<?=_("면지")?>'); return false;"><?=_("면지")?></button>
  <?}?>
  <?if ($clsExtraOption->GetOptionKindUse("97") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="openPrice('G-FIX','<?=$extraOption->GoodsKind?>','<?=_("간지")?>'); return false;"><?=_("간지")?></button>
  <?}?>
  <?
}
else if($presetno == "100106") {
?>
  <button onclick="openPrice('FIX','<?=$extraOption->GoodsKind?>','<?=_("종이")?>'); return false;"><?=_("종이")?></button>
  <button onclick="openPrice('SEL','<?=$extraOption->GoodsKind?>','<?=_("인쇄")?>'); return false;"><?=_("인쇄")?></button>
  <?
}
else if($presetno == "100108") {
?>
  <button onclick="openPrice('C-FIX','<?=$extraOption->GoodsKind?>','<?=_("표지종이")?>'); return false;"><?=_("표지종이")?></button>
  <button onclick="openPrice('C-SEL','<?=$extraOption->GoodsKind?>','<?=_("표지인쇄")?>'); return false;"><?=_("표지인쇄")?></button>
  <button onclick="openPrice('FIX','<?=$extraOption->GoodsKind?>','<?=_("내지종이")?>'); return false;"><?=_("내지종이")?></button>
  <button onclick="openPrice('SEL','<?=$extraOption->GoodsKind?>','<?=_("내지인쇄")?>'); return false;"><?=_("내지인쇄")?></button>
  <div>&nbsp;</div>
  <?if ($clsExtraOption->GetOptionKindUse("96") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="openPrice('M-FIX','<?=$extraOption->GoodsKind?>','<?=_("면지종이")?>'); return false;"><?=_("면지종이")?></button>
  <button onclick="openPrice('M-SEL','<?=$extraOption->GoodsKind?>','<?=_("면지인쇄")?>'); return false;"><?=_("면지인쇄")?></button>
  <?}?>
  <?if ($clsExtraOption->GetOptionKindUse("97") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="openPrice('G-FIX','<?=$extraOption->GoodsKind?>','<?=_("간지종이")?>'); return false;"><?=_("간지종이")?></button>
  <button onclick="openPrice('G-SEL','<?=$extraOption->GoodsKind?>','<?=_("간지인쇄")?>'); return false;"><?=_("간지인쇄")?></button>
  <?}?>
  <?
}
else if($presetno == "100110" || $presetno == "100114") {
?>
  <button onclick="openPrice('FIX','<?=$extraOption->GoodsKind?>','<?=_("기본옵션")?>'); return false;"><?=_("기본")?></button>
  <?
}

else if($presetno == "100112") {
?>
  <button onclick="openPrice('C-FIX','<?=$extraOption->GoodsKind?>','<?=_("표지")?>'); return false;"><?=_("표지")?></button>
  <button onclick="openPrice('FIX','<?=$extraOption->GoodsKind?>','<?=_("내지")?>'); return false;"><?=_("내지")?></button>

  <div>&nbsp;</div>
  <?if ($clsExtraOption->GetOptionKindUse("96") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="openPrice('M-FIX','<?=$extraOption->GoodsKind?>','<?=_("면지")?>'); return false;"><?=_("면지")?></button>
  <?}?>
  <?if ($clsExtraOption->GetOptionKindUse("97") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="openPrice('G-FIX','<?=$extraOption->GoodsKind?>','<?=_("간지")?>'); return false;"><?=_("간지")?></button>
  <?}?>
<? }?>    	
    </td>  	
  </tr>

  <?
  if($presetno == "100110" || $presetno == "100114") {
      
  }
  else {    
  ?>
  
  <tr>  	
    <th>
    	<?=_("후가공")?><br>
    	<input type="checkbox" id="documentUse" name="documentUse" class="absmiddle" <?=($clsExtraOption->GetOptionKindUse("101") == "Y") ? "checked='checked'" : ""?>> (<?=_("규격 포함")?>)    	
    </th>
    <td>

<?if($presetno == "100112") {
	if ($clsExtraOption->GetOptionKindUse('2') == "Y") {
?>
  <button onclick="openPriceAfter('<?=$clsExtraOption->GetOptionKindCode('2')?>','<?=$extraOption->GoodsKind?>','<?=$clsExtraOption->GetDisplayName('2')?>'); return false;"><?=$clsExtraOption->GetDisplayName('2')?></button>
<?
	}
	if ($clsExtraOption->GetOptionKindUse('3') == "Y") {
?>  
  <!--<button onclick="openPriceAfter('<?=$clsExtraOption->GetOptionKindCode('3')?>','<?=$extraOption->GoodsKind?>','<?=$clsExtraOption->GetDisplayName('3')?>'); return false;"><?=$clsExtraOption->GetDisplayName('3')?></button>-->
<?
	}
?>  
<!--<div>&nbsp;</div>-->
<?
	if ($clsExtraOption->GetOptionKindUse('25') == "Y") {
?>
  <button onclick="openPriceAfter('<?=$clsExtraOption->GetOptionKindCode('25')?>','<?=$extraOption->GoodsKind?>','<?=$clsExtraOption->GetDisplayName('25')?>'); return false;"><?=$clsExtraOption->GetDisplayName('25')?></button>
<?
	}
	if ($clsExtraOption->GetOptionKindUse('26') == "Y") {
?>  
  <button onclick="openPriceAfter('<?=$clsExtraOption->GetOptionKindCode('26')?>','<?=$extraOption->GoodsKind?>','<?=$clsExtraOption->GetDisplayName('26')?>'); return false;"><?=$clsExtraOption->GetDisplayName('26')?></button>
<?
	}
	if ($clsExtraOption->GetOptionKindUse('27') == "Y") {
?>  
  <button onclick="openPriceAfter('<?=$clsExtraOption->GetOptionKindCode('27')?>','<?=$extraOption->GoodsKind?>','<?=$clsExtraOption->GetDisplayName('27')?>'); return false;"><?=$clsExtraOption->GetDisplayName('27')?></button>
<?
	}
?>      
<div>&nbsp;</div>
<?}?>

<?
foreach ($afterOptionKindIndex as $afterKindIndex) 
{
  	$afterOptionDisplayName = $clsExtraOption->GetDisplayName($afterKindIndex);
  	$afterOptionKindCode = $clsExtraOption->GetOptionKindCode($afterKindIndex);
  	$afterItemPriceType = $clsExtraOption->GetAfterOptionPriceType($afterOptionKindCode);

	if ($cid != $cfg_center[center_cid] && $clsExtraOption->GetOptionKindUse($afterKindIndex) == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
		if ($clsExtraOption->GetOptionKindUse($afterKindIndex) == "Y") {
?>  
	<button onclick="openPriceAfter('<?=$afterOptionKindCode?>','<?=$extraOption->GoodsKind?>','<?=$afterOptionDisplayName?>'); return false;"><?=$afterOptionDisplayName?></button>
<?
		}
	}
}
?>    	
    	
    </td>  	
  </tr>

  <? }?>

  </table>    
  <br />
  <!--자동견적옵션가격-->

<!--자동견적옵션가격초기화-->
<div id="extraPriceInit" style="display: none;">
  <div class="stit"><?=_("견적 가격 초기화")?></div>
  <table class="tb1">
  <tr>  	
    <th><?=_("전체 초기화")?></th>
    <td>
    	<button onclick="deletePrice(); return false;"><?=_("전체 초기화")?></button>
    </td>
  </tr>  	  	
  <tr>  	
    <th><?=_("옵션")?></th>
    <td>
  <?
  if($presetno == "100101" || $presetno == "100102"){
 ?>
  <button onclick="deletePrice2('F-FIX',''); return false;"><?=_("기본 초기화")?></button>
  <?
}
else if($presetno == "100103" || $presetno == "100104") {
 ?>
  <button onclick="deletePrice2('C-FIX',''); return false;"><?=_("표지 초기화")?></button>
  <button onclick="deletePrice2('F-FIX',''); return false;"><?=_("내지 초기화")?></button>
  
  <?if ($clsExtraOption->GetOptionKindUse("96") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="deletePrice2('M-FIX',''); return false;"><?=_("면지 초기화")?></button>
  <?}?>
  <?if ($clsExtraOption->GetOptionKindUse("97") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="deletePrice2('G-FIX',''); return false;"><?=_("간지 초기화")?></button>
  <?}?>
  <?
}
else if($presetno == "100106") { //P-FIX => F-SEL
 ?>
  <button onclick="deletePrice2('F-FIX',''); return false;"><?=_("종이 초기화")?></button>
  <button onclick="deletePrice2('F-SEL',''); return false;"><?=_("인쇄 초기화")?></button>
  <?
}
else if($presetno == "100108") {
 ?>
  <button onclick="deletePrice2('C-FIX',''); return false;"><?=_("표지종이 초기화")?></button>
  <button onclick="deletePrice2('C-SEL',''); return false;"><?=_("표지인쇄 초기화")?></button>
  <button onclick="deletePrice2('F-FIX',''); return false;"><?=_("내지종이 초기화")?></button>
  <button onclick="deletePrice2('F-SEL',''); return false;"><?=_("내지인쇄 초기화")?></button>
  <div>&nbsp;</div>
  <?if ($clsExtraOption->GetOptionKindUse("96") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="deletePrice2('M-FIX',''); return false;"><?=_("면지종이 초기화")?></button>
  <button onclick="deletePrice2('M-SEL',''); return false;"><?=_("면지인쇄 초기화")?></button>
  <?}?>
  <?if ($clsExtraOption->GetOptionKindUse("97") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="deletePrice2('G-FIX',''); return false;"><?=_("간지종이 초기화")?></button>
  <button onclick="deletePrice2('G-SEL',''); return false;"><?=_("간지인쇄 초기화")?></button>
  <?}?>
  <?
}
else if($presetno == "100112") {
 ?>
  <button onclick="deletePrice2('C-FIX',''); return false;"><?=_("표지 초기화")?></button>
  <button onclick="deletePrice2('F-FIX',''); return false;"><?=_("내지 초기화")?></button>

  <div>&nbsp;</div>
  <?if ($clsExtraOption->GetOptionKindUse("96") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="deletePrice2('M-FIX',''); return false;"><?=_("면지 초기화")?></button>
  <?}?>
  <?if ($clsExtraOption->GetOptionKindUse("97") == "Y") { //몰에서 옵션 사용여부에 따라 출력 처리함.?>
  <button onclick="deletePrice2('G-FIX',''); return false;"><?=_("간지 초기화")?></button>
  <?
  }
}
else if($presetno == "100110") {
 ?>
  <button onclick="deletePrice2('F-FIX',''); return false;"><?=_("기본 초기화")?></button>
  <? }?>    	
    	
    </td>  	
  </tr>

  <?
  if($presetno == "100110" || $presetno == "100114") {
      
  }
  else {
  ?>
  
  <tr>  	
    <th><?=_("후가공")?></th>
    <td>

<?if($presetno == "100112") {?>
	<!--<button onclick="deletePrice2('AFTER', '<?=$clsExtraOption->GetOptionKindCode('2')?>'); return false;"><?=$clsExtraOption->GetDisplayName('2')?> 초기화</button>
	<button onclick="deletePrice2('AFTER', '<?=$clsExtraOption->GetOptionKindCode('3')?>'); return false;"><?=$clsExtraOption->GetDisplayName('3')?> 초기화</button>
<div>&nbsp;</div>-->
<?}?>    	
<?
foreach ($afterOptionKindIndex as $afterKindIndex) 
{
  $afterOptionDisplayName = $clsExtraOption->GetDisplayName($afterKindIndex);
  $afterOptionKindCode = $clsExtraOption->GetOptionKindCode($afterKindIndex);
  $afterItemPriceType = $clsExtraOption->GetAfterOptionPriceType($afterOptionKindCode);

	if ($cid != $cfg_center[center_cid] && $clsExtraOption->GetOptionKindUse($afterKindIndex) == "N") {} //몰에서 옵션 사용여부에 따라 출력 처리함.
	else {
		if ($clsExtraOption->GetOptionKindUse($afterKindIndex) == "Y") {
?>  
	<button onclick="deletePrice2('AFTER', '<?=$afterOptionKindCode?>'); return false;"><?=$afterOptionDisplayName?> <?=_("초기화")?></button>
<?
		}
	}
}
?>    	
    </td>  	
  </tr>
<? }?>
  </table>
</div>
<br />    
<!--자동견적옵션가격초기화-->

<!--자동견적옵션초기화-->
<div id="extraOptionInit" style="display: none;">
  <div class="stit"><?=_("견적 옵션 초기화")?></div>
  <table class="tb1">
  <tr>  	
    <th><?=_("전체 초기화")?></th>
    <td>
    	<button onclick="deleteOption(); return false;"><?=_("전체 초기화")?></button>
    </td>
  </tr>  	  	
  <tr>  	
    <th><?=_("옵션 복사")?></th>
    <td>
    	<?=_("복사 대상 상품")?> : 
    	<select name="source_goodsno" id="source_goodsno">
    		<option value=""><?=_("선택")?></option>
    		
			<?
			if ($data[source_goods]) { 
				foreach ($data[source_goods] as $k=>$v) { 
			?>
			<option value="<?=$v[goodsno]?>">[<?=$v[goodsno]?>]<?=$v[goodsnm]?></option>
			<? 
				}
			}
			?>    		
    		
    	</select>
    	<button onclick="copyOption(); return false;"><?=_("선택한 상품 옵션으로 복사")?></button>
    </td>
  </tr>  
  </table>
</div>
<br />   
<!--자동견적옵션초기화-->

<!--자동견적옵션이미지초기화-->
<div id="extraOptionImgInit" style="display: none;">
  <div class="stit"><?=_("견적 옵션 이미지 초기화")?></div>
  <table class="tb1">
  <tr>      
    <th><?=_("전체 초기화")?></th>
    <td>
        <button onclick="deleteOptionImg(); return false;"><?=_("전체 초기화")?></button>
    </td>
  </tr>         
  </table>
</div>
<br />   
<!--자동견적옵션이미지초기화-->	
 
  <div class="btn">
  	<!--<input type="image" src="../img/bt_submit_l.png"/>
  	<a href="javascript:history.back()"><img src="../img/bt_cancel_l.png"></a>-->
  	<a style="cursor: pointer; color: #b2b2a8;" onclick="click_optioninit_div(); return false;">[<?=_("옵션 초기화")?>]</a>
  	<a style="cursor: pointer; color: #b2b2a8;" onclick="click_priceinit_div(); return false;">[<?=_("가격 초기화")?>]</a>
  	<?if($presetno == "100114") {?>
  	<a style="cursor: pointer; color: #b2b2a8;" onclick="click_optionimginit_div(); return false;">[<?=_("옵션 이미지 초기화")?>]</a>
  	<?}?>
  </div>
  </form>
  
  <!--</form>-->
  
  <style>
	#opt_div {
		position: absolute;
		left: 0;
		top: 0;
		width: 450px;
		border: 2px solid #000000;
	}
	#opt_div table {
		border-collapse: collapse;
		background: #FFFFFF;
	}
	.opt_tb {
		border-collapse: collapse;
		width: 100%;
		display: none;
		border: 1px solid #CCCCCC;
	}
	.opt_tb th {
		background: #EFEFEF;
		padding: 5px 3px;
		font: bold 8pt '돋움';
		border: 1px solid #CCCCCC;
	}
	.opt_tb td {
		padding: 2px;
		border: 1px solid #CCCCCC;
	}
	#add_opt1 input, #add_opt2 input {
		margin: 2px 5px;
		width: 90px;
	}
	#thumbnails img {
		width: 120px;
		border: 2px solid #DEDEDE;
	}
	.podstype_ul li {
		float: left;
		width: 200px;
	}
  </style>
    

<script type="text/javascript">
	$(function(){
	    $("input[id='priceTableType']").change(function(){
	        var flag = $(this).val();
			var url = "/lib/extra_option/set_extra_option_item_update.php";
			var param = "&mode=display_flag&goodsno=<?=$_GET[goodsno]?>&option_kind_index=100&flag=" + flag;
				//alert(param);
			call(url, param, "");
		});

		//후가공 규격 포함여부.
		$("#documentUse").click(function(){
			var chk = $(this).is(":checked");
			var flag = "";
			if(chk) flag = "Y";
			else flag = "N";
	
			var url = "/lib/extra_option/set_extra_option_item_update.php";
			var param = "&mode=display_flag&goodsno=<?=$_GET[goodsno]?>&option_kind_index=101&flag=" + flag;
			//alert(param);
			call(url, param, "");
		});
	
		//노출 여부 일괄처리
		$(".btnBatchSaveDisplayFlag").click(function(){
			var goodsNo = $('#goodsno').val();
			var preSet =  $('#preset_div').attr("preset");
			var param = "&mode=display_flag_batch&goodsno=" + goodsNo + "&preset=" + preSet;
	
			var selectOption = $("select[id^='use_flag_']");
	
			for (var i = 0; i < selectOption.length; i++) {
				param += "&" + $(selectOption[i]).attr("name") + "=" + $(selectOption[i]).val();
			}

			var url = "/lib/extra_option/set_extra_option_item_update.php";
			call(url, param, "reload");
		});
	});

	//사용 여부 일괄처리
	function batchSaveMasterItemDisplayFlag(optionKindIndex) {
		var goodsNo = $('#goodsno').val();
		var preSet =  $('#preset_div').attr("preset");
		var param = "&mode=masterItem_display_flag_batch&goodsno=" + goodsNo + "&preset=" + preSet + "&option_kind_index=" + optionKindIndex + "&option_kind_code=" + $("input[name='option_kind_code']:eq(0)").val();
	
		var selectOption = $("#selectedResourceList").find("select[id^='display_flag_']");
	
		for (var i = 0; i < selectOption.length; i++) {
			param += "&" + $(selectOption[i]).attr("id") + "=" + $(selectOption[i]).val();
		}
		//console.log(param);
	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
		call(url, param, "reload");
	}

	//사용자 후가공 옵션 삭제처리
	function afterOptionMasterRegistFlag(optionKindIndex) {
		var goodsNo = $('#goodsno').val();
		var preSet =  $('#preset_div').attr("preset");
		var param = "&mode=masterOption_regist_flag&goodsno=" + goodsNo + "&preset=" + preSet + "&option_kind_index=" + optionKindIndex + "&flag=N";
		//console.log(param);
	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
		if (confirm('<?=_("사용자 후가공 옵션을 삭제 하시겠습니까?")?>' + "\n" + '<?=_("삭제된 데이터는 복구할 수 없습니다.")?>')) {
			call(url, param, "reload");
		}
		else {
			return false;
		}
	}

	function click_priceinit_div() {
		if($('#extraPriceInit').css("display") == "none") {
			$('#extraPriceInit').show();
		}
		else {
			$('#extraPriceInit').hide();
		}
	}
	
	function click_optioninit_div() {
		if($('#extraOptionInit').css("display") == "none") {
			$('#extraOptionInit').show();
		}
		else {
			$('#extraOptionInit').hide();
		}
	}

    function click_optionimginit_div() {
        if($('#extraOptionImgInit').css("display") == "none") {
            $('#extraOptionImgInit').show();
        }
        else {
            $('#extraOptionImgInit').hide();
        }
    }

	//자동견적옵션(초기화,복사) 기능 추가 2016.04.06 by kdk.
	function deleteOption() {
		var goodsNo = $('#goodsno').val();
		var param = "&mode=userOption_delete&goodsno=" + goodsNo;
		//console.log(param);
	
		var url = "/lib/extra_option/set_extra_option_item_update.php";

		if (confirm('<?=_("견적 옵션 테이블을 초기화 하시겠습니까?")?>' + "\n" + '<?=_("삭제된 데이터는 복구할 수 없습니다.")?>')) {
			call(url, param, "reload");
		}
		else {
			return false;
		}
	}

	function copyOption() { //userOption_copy
		var goodsNo = $('#goodsno').val();
		var sourceGoodsno = $('#source_goodsno').val();
	
		if(sourceGoodsno == "") {
			alert('<?=_("복사 대상 상품을 선택하세요.")?>');
			$('#source_goodsno').focus();
			return false;
		}
	
		var param = "&mode=userOption_copy&source_goodsno=" + sourceGoodsno + "&goodsno=" + goodsNo;
		//console.log(param);
	
		var url = "/lib/extra_option/set_extra_option_item_update.php";
	
		if (confirm('<?=_("견적 옵션 데이터(테이블)을 복사 하시겠습니까?")?>' + "\n" + '<?=_("이전 데이터는 복구할 수 없습니다.")?>')) {
			call(url, param, "reload");
		}
		else {
			return false;
		}
	}
	
    //자동견적옵션 이미지 초기화 기능 추가 2017.10.17 by kdk.
    function deleteOptionImg() {
        var goodsNo = $('#goodsno').val();
        var param = "&mode=userOptionImg_delete&goodsno=" + goodsNo;
        //console.log(param);
    
        var url = "indb_extopt_img.php";

        if (confirm('<?=_("견적 옵션 이미지 정보를 초기화 하시겠습니까?")?>' + "\n" + '<?=_("삭제된 데이터는 복구할 수 없습니다.")?>')) {
            call(url, param, "reload");
        }
        else {
            return false;
        }
    }	
</script>

<?
}
?>
	  
			<!-- end #content -->
         </div>
      </div>
   </div>
   
   <div class="panel-body panel-form">
      <div class="form-group">
      	 <div class="col-md-12">
      	 	<!--<div><span class="warning">[주의]</span> 삭제된 관리자는 복구되지 않으니, 주의하여 주시기 바랍니다. 재등록으로만 가능합니다.</div>
      	 	<div><span class="notice">[설명]</span> 관리자의 메뉴별 권한 설정시 권한설정 기능에서 적용하실 수 있습니다.</div>-->
      	 </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>