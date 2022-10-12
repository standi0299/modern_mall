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
include_once "../../lib/class.excel.reader.php";
include_once "../../lib/extra_option/extra_option_price_proc.php";

$PAGE_ROW_MAX = 20;
//page 출력단위시 최대 갯수

set_time_limit(360);
ini_set('memory_limit', '-1');
//테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk

$this_time = get_time();
if ($_GET[goodsno]) {
	//$data = $db->fetch("select * from exm_goods where goodsno='$_GET[goodsno]'");
	$mGoods = new M_goods();
	$data = $mGoods->getInfo($_GET[goodsno]);
	if (!$data) {
		msg(_("상품데이터가 존재하지 않습니다."), -1);
		exit ;
	}
	$fkey = $data[goodsno];
}

if ($data) {
	//debug($data[extra_option]);
	$extra_option = explode('|', $data[extra_option]);
	//항목 분리
	if (count($extra_option) > 0) {
		$extra_product = $extra_option[0];
		$extra_preset = $extra_option[1];
		$extra_price_type = $extra_option[2];
	}
	//debug($extra_product);
	//debug($extra_preset);
	//debug($extra_price_type);

	//스튜디오 견적 관련
	if ($data[goods_group_code] == "20") {
		$extra_stu_order = $extra_option[3];
	}
	//debug($extra_stu_order);

}

$ptType = $_GET[ptType];
//가격 테이블 타입 기본: x(수량) , y(옵션/가격)  _reverse : (옵션/가격) , y(수량)
$docUse = $_GET[docUse];
//후가공(규격포함) 여부

$inc_path = "extra_option_price.f.php";
if ($ptType == "2")
	$inc_path = "extra_option_price.f_reverse.php";

//debug("ptType : " . $ptType);
//debug("docUse : " . $docUse);
//debug("inc_path : " . $inc_path);
//exit;

$optionGroupType = $_GET[mode] . "OPTION";
$addWhere = " and option_group_type = '" . $_GET[mode] . "' ";
$option_group_type = $_GET[mode];

//후가공이면...
if ($_GET[mode] == "AFTER") {
	$option_group_type = $_GET[kind];
	//후가공 코드가 있으면...
	if ($_GET[kind]) {
		$addWhere .= " and option_kind_code = '$_GET[kind]' ";
		$optionKindCode = $_GET[kind];
	}
}

//debug($addWhere);
//debug($optionGroupType);
//debug($option_group_type_where);
//exit ;

//<!--자동견적 자체상품 추가 2015.06.02 by kdk-->
$checkRegCid = FALSE;
//자체상품 여부
if ($data[reg_cid] && $data[reg_cid] = $cid)
	$checkRegCid = TRUE;

$extraOption = new ExtraOption();
$extraOption->SetPreset($extra_preset); //프리셋코드 넘김
$extraOption->SetGoodsKind($extra_product); //GoodsKind
$extraOption->SetOptionGroupType($optionGroupType); //OptionGroupType

$m_extra_option = new M_extra_option();
$opt_data = $m_extra_option->getOptionPriceListS3($cid, $cfg_center[center_cid], $_GET[goodsno], $option_group_type);

//가격 정보 삭제 후 다시 설정
$bPriceInsertFlag = true;
$mode3 = "new";
if ($opt_data) {
	$bPriceInsertFlag = false;
	$mode3 = "update";

	foreach ($opt_data as $key => $value) {
		$InsertExtraOptionTable[$value[ID]] = array("option_item" => $value[option_item], "option_price" => $value[option_price]);
	}
	//debug($InsertExtraOptionTable);
	// exit;
}

if ($_GET[all_delete] == "Y") {
	if ($_GET[mode] == "AFTER")
		$ogt = $_GET[kind];
	else
		$ogt = $_GET[mode];

	$m_extra_option->setDeleteAllOptionListS3($cid, $cfg_center[center_cid], $_GET[goodsno], $ogt);
	$bPriceInsertFlag = true;
	$mode3 = "new";
}

//$bPriceInsertFlag = true;       //테스트위해..
//debug($bPriceInsertFlag);
//exit;
//가격 테이블 정보 신규 복사
if ($bPriceInsertFlag) {

	$extraOption->getOptionDataInDB($_GET[goodsno], $optionGroupType, $extraOption->GoodsKind, $_GET[kind]);
	//debug($extraOption->OptionData);
	//exit;

	$InsertExtraOptionTable = $extraOption->InsertExtraOptionPriceTable($extraOption->Preset, $_GET[mode]);
	//debug($InsertExtraOptionTable);
	//exit;
	
	if ($docUse == "Y") {//후가공(규격포함) 가격 설정 추가 2015.04.07 by kdk
		$InsertExtraOptionTable = $extraOption->InsertExtraOptionPriceTableWithDocument($_GET[goodsno], $InsertExtraOptionTable);
		//exit;
	}

	if ($extra_stu_order == "UPL") {//스튜디오견적 업로더용(표지규격포함) 가격 설정 추가 2015.08.04 by kdk
		if ($extraOption->getOptionKindUse("11") == "Y") {
			$InsertExtraOptionTable = $extraOption->InsertExtraOptionPriceTableWithCoverDocument($_GET[goodsno], $InsertExtraOptionTable);
		}
	}

}

//debug($InsertExtraOptionTable);
//exit;

$debug_data .= "1 - " . number_format(get_time() - $this_time, 4) . _("초")."<BR>";

//자동견적 가격 엑셀 파일 처리
$bExcelImport = false;
if ($_FILES['excelFile']['name']) {

	$uploaddir = '../../data/excel_temp/';

	if (!is_dir($uploaddir)) {
		mkdir($uploaddir, 0707);
	} else
		@chmod($uploaddir, 0707);

	$ext = explode(".", $_FILES['excelFile']['name']);

	$name = time() . "." . $ext[1];

	move_uploaded_file($_FILES[excelFile][tmp_name], $uploaddir . $name);

	$bExcelImport = true;
	$excelImportFileName = $uploaddir . $name;
}
?>
<script type="text/javascript" src="/js/extra_option/jquery_client.js"></script>
<script type="text/javascript" src="/js/extra_option/jquery.ui.js"></script>
<script type="text/javascript" src="/js/extra_option/jquery.form.js"></script>

<link href="../../css/table.css" rel="stylesheet">
<link href="../../css/PopupLayer.css" rel="stylesheet">

<form name="frm1" action="indb_option.php" method="post">

	<?
	include $inc_path;
	?>

	<?
	$path_inc = "extra_option_price_excel.php";
	if ($ptType == "2")
		$path_inc = "extra_option_price_excel_reverse.php";

	if ($bPriceInsertFlag) {
		$excel_link = "javascript:priceIsNull();";
		$excel_load = "priceIsNull(1);";
	} else {
		$excel_link = $path_inc . "?goodsno=$_GET[goodsno]&mode=$_GET[mode]&kind=$_GET[kind]&filename=$_GET[filename]&docUse=$_GET[docUse]&ptType=$_GET[ptType]";
		$excel_load = "openFile(event);";
	}
	?>

	<div class="btn">
		<img src='../img/bt_excelupload_s.png' alt='<?=_("엑셀 불러오기")?>' style='cursor:pointer;' onclick='<?=$excel_load ?>' />
		<a href='<?=$excel_link ?>'><img src='../img/bt_exceldown_s.png' alt='<?=_("엑셀 저장")?>' /></a>
	</div>

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
	<div class="bg"> </div>

	<!--loading-->
	<div id="loading_back" style="width:100%;height:100%;padding:0;margin:0;position:absolute;top:0;left:0;z-index:90;filter:alpha(opacity=0); opacity:0.0; -moz-opacity:0.0;background-color:#fff;">
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
				<form id="myForm" action="extra_option_price.php?goodsno=<?=$_GET[goodsno] ?>&mode=<?=$_GET[mode] ?>&kind=<?=$_GET[kind] ?>&filename=<?=$_GET[filename] ?>&docUse=<?=$_GET[docUse] ?>&ptType=<?=$_GET[ptType] ?>" enctype="multipart/form-data" method="post">
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
	
	//<!--자동견적 자체상품 추가 2015.06.02 by kdk-->
	<? if($cid == $cfg_center[center_cid] || $checkRegCid) { ?>
		$j("input[name^='supply_price_']").removeAttr("readonly");
	<? } else { ?>
		$j("input[name^='supply_price_']").attr('readonly', "true");
	<? } ?>
	
	function priceIsNull(m) {
		var msg = '<?=_("다운로드")?>';

		if (m == "1")
			msg = '<?=_("불러오기")?>';

		alert('<?=_("가격 정보가 저장되어 있지 않습니다!")?>' + "\n\n" + '<?=_("엑셀 파일을")?>' + " " + msg + " " + '<?=_("하시려면 먼저 \"확인\" 버튼을 클릭하여 정보를 입력하시기 바랍니다.")?>');
	}
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