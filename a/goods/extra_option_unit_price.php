<?
include_once "../_pheader.php";
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("가격설정")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <div class="panel-body">
	         <div class="table-responsive">
	         	<!-- begin #content -->
	
<?
include "../../lib/class.excel.reader.php";
include "../../lib/extra_option/extra_option_price_proc.php";

set_time_limit(360);
ini_set('memory_limit', '-1');
//테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk

$this_time = get_time();
if ($_GET[goodsno]) {
	//$data = $db -> fetch("select * from exm_goods where goodsno='$_GET[goodsno]'");
	$mGoods = new M_goods();
	$data = $mGoods->getInfo($_GET[goodsno]);
	if (!$data) {
		msg(_("상품데이터가 존재하지 않습니다."), -1);
		exit ;
	}
	$fkey = $data[goodsno];
}

if ($data) {
	$extra_option = explode('|', $data[extra_option]);
	//항목 분리
	if (count($extra_option) > 0) {
		$extra_product = $extra_option[0];
		$extra_preset = $extra_option[1];
		$extra_price_type = $extra_option[2];
		$goodsKind = $extra_product;
	}
	//debug($extra_product);
	//debug($extra_preset);
	//debug($extra_price_type);
}

//가격 테이블에 데이타가 없을 경우만 처리
$adminExtraOption = new M_extra_option();

$opt_data = $adminExtraOption -> getOptionUnitPrice($cid, $cfg_center[center_cid], $_GET[goodsno]);

//tb_extra_option_order_cnt unit_cnt_rule 조회
$data = $adminExtraOption -> getOrderCntList($cid, $cfg_center[center_cid], $_GET[goodsno], "OCNT");

//$_GET[all_delete] = 'Y';
$bPriceInsertFlag = false;
if ($opt_data) {
	if ($_GET[all_delete] == 'Y') {
		//가격 정보 데이타가 있을 경우 옵션 추가한 날짜와 가격 입력한 날짜를 비교후 신규 옵션 항목 추가가 있을 경우 기존 데이타 삭제후 처리한다.
		$adminExtraOption -> setOptionUnitPrice($cid, $cfg_center[center_cid], $_GET[goodsno], "");
		$bPriceInsertFlag = true;
	}
} else {
	//가격 테이블 정보가 전혀 없을 경우 신규 복사
	$bPriceInsertFlag = true;
}

//가격 테이블 정보 신규 복사
if ($bPriceInsertFlag) {
	$adminExtraOption -> setInsertOptionUnitPriceRule($cid, $cfg_center[center_cid], $_GET[goodsno], $data[unit_cnt_rule]);
}

$debug_data .= "1 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";
//자동견적 가격 엑셀 파일 처리
$bExcelImport = false;
if ($_FILES['excelFile']['name']) {

	$uploaddir = '../../data/excel_temp/';

	if (!is_dir($uploaddir)) {
		mkdir($uploaddir, 0707);
	} else
		@chmod($uploaddir, 0707);
	//$uploadfile = $uploaddir.basename($_FILES['excelFile']['name']);

	$ext = explode(".", $_FILES['excelFile']['name']);

	$name = time() . "." . $ext[1];
	//move_uploaded_file($_FILES[img][tmp_name][$k],$uploaddir.$name);

	move_uploaded_file($_FILES[excelFile][tmp_name], $uploaddir . $name);
	/*
	 echo '<pre>';
	 if (move_uploaded_file($_FILES[excelFile][tmp_name],$uploaddir.$name)) { //move_uploaded_file($_FILES['excelFile']['tmp_name'], $uploadfile)
	 echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
	 } else {
	 print "파일 업로드 공격의 가능성이 있습니다!\n";
	 }

	 echo '자세한 디버깅 정보입니다:';
	 print_r($_FILES);
	 print_r($uploaddir.$name);
	 print "</pre>";
	 */
	$bExcelImport = true;
	$excelImportFileName = $uploaddir . $name;
}
?>
<script type="text/javascript" src="/js/extra_option/jquery_client.js"></script>
<script type="text/javascript" src="/js/extra_option/jquery.ui.js"></script>
<script type="text/javascript" src="/js/extra_option/jquery.form.js"></script>

<link href="../../css/table.css" rel="stylesheet">
<link href="../../css/PopupLayer.css" rel="stylesheet">

<form name="frm1" action="indb_unit_price.php" method="post">

	<?
	include "extra_option_unit_price.f.php";
	?>

	<!--<div class="btn">
		<input type="image" src="../img/bt_submit_l.png" onclick="return confirm('입력된 가격이 그대로 저장되며, 추후 복원이 되지 않습니다. 저장하시겠습니까?');" />
		<a href="javascript:self.close()"><img src="../img/bt_cancel_l.png"></a>
	</div>-->
	<div class="form-group">
		<label class="col-md-2 control-label"></label>
		<div class="col-md-10">
			<button type="submit" class="btn btn-sm btn-success" onclick="return confirm('<?=_("입력된 가격이 그대로 저장되며, 추후 복원이 되지 않습니다. 저장하시겠습니까?")?>');"><?=_("등록")?></button>
			<button type="button" class="btn btn-sm btn-default" onclick="javascript:self.close()"><?=_("취소")?></button>
		</div>
	</div>	
</form>

<script type="text/javascript" src="/js/extra_option/goods.extra.option.admin.js"></script>
<script type="text/javascript" src="/js/extra_option/goods.extra.option.admin.action.js"></script>

<!--옵션추가 //-->
<div class="layer1">
	<div class="bg">

	</div>

	<!--loading-->
	<div id="loading_back" style="width:100%;height:100%;padding:0;margin:0;position:absolute;top:0;left:0;z-index:90;filter:alpha(opacity=0); opacity:0.0; -moz-opacity:0.0;background-color:#fff;">
		<!-- filter:alpha(opacity=30); opacity:0.3; -moz-opacity:0.3;background-color:#fff; -->
		&nbsp;
	</div>
	<div id="loading_div" style="width:50px;height:50px;padding:0;position:absolute;top:50%;left:50%;margin:-25px 0 0 -25px;z-index:91;">
		<img src="../img/loading_s.gif" />
	</div>

	<!--가격항목엑셀파일불러오기 //-->
	<div id="dlayer-openfile" class="pop-layer">
		<div class="pop-container">
			<div class="pop-conts">
				<!--content //-->
				<form id="myForm" action="extra_option_unit_price.php?goodsno=<?=$_GET[goodsno] ?>" enctype="multipart/form-data" method="post">
					<div>
						<p class="ctxt mb20">
							<?=_("파일 불러오기 창")?>.
							<br>
						</p>
						<div>

							<input type="file" id="excelFile" name="excelFile" />

						</div>
					</div>
					<div class="btn-r">
						<!--<input type="submit" value="확인" class="cbtn-c" />-->
						<a href="#" class="cbtn-c" onclick="saveFile();"><?=_("확인")?></a>
						<a href="#" class="cbtn"><?=_("취소")?></a>
					</div>
				</form>
				<!--// content-->
			</div>
		</div>
	</div>
	<!--가격항목엑셀파일불러오기 //-->
</div>
<!--옵션추가 //-->

<script type="text/javascript">
hideLoading();

<? if($cid == $cfg_center[center_cid]) { ?>
	$j("input[name^='supply_price_']").removeAttr("readonly");
<? } else { ?>
	$j("input[name^='supply_price_']").attr('readonly', "true");
<? } ?>
</script>
		  
				<!-- end #content -->
	         </div>
            </div>
         </div>
	    <div class="form-group">
	        <label class="col-md-3 control-label"></label>
	        <div class="col-md-9">
	            <!--<button type="button" style="margin-bottom: 15px;" class="btn btn-default"onclick="window.close();">닫  기</button>-->
	        </div>
	    </div>         
      </div>
   </div>
</div>

<script>

</script>

<? include_once "../_pfooter.php"; ?>
