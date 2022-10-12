<?
include "../_header.php";
include "../_left_menu.php";

$m_order = new M_order();

if (!$_GET[start] && !$_GET['end']) {
	$_GET[start] = date('Y-m-d');
	$_GET['end'] = date('Y-m-d');
}

$selected[document_type][$_GET[document_type]] = "selected";
$selected[document_state][$_GET[document_state]] = "selected";

$addWhere = "";

$startDate = str_replace("-", "", $_GET[start]);
$endDate = str_replace("-", "", $_GET['end']);
if ($startDate) $addWhere .= " and a.regdt > '{$startDate}'";
if ($endDate) $addWhere .= " and a.regdt < adddate('{$endDate}',interval 1 day)";

if ($_GET[searchValue] != "") {
	$addWhere .= " and (a.payno = '$_GET[searchValue]' or a.mid = '$_GET[searchValue]')";
}

if ($_GET[document_type] != "") {
	$addWhere .= " and a.document_type = '$_GET[document_type]'";
}

if ($_GET[document_state] != "") {
	$addWhere .= " and a.state = '$_GET[document_state]'";
}

$addQuery = " order by a.regdt desc";
$Exel_addQuery = $addQuery;
$addQuery .= " limit 0, 5";

$list = $m_order->getDocumentList($cid, $addWhere, $addQuery, FALSE);
$totalCnt = $m_order->getDocumentListCnt($cid, $addWhere);

/******** 엑셀 저장 추가		20191217	kkwon *************/
$_query = $m_order->getDocumentList($cid, $addWhere, $Exel_addQuery, TRUE);
$_query = base64_encode(urlencode($_query));
$url_query = "/admin/xls/indb.php?query=".$_query;
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
/*****************************************************/


$postData = base64_encode(json_encode($_GET));



?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm">
	   <div class="panel panel-inverse">
	      <div class="panel-heading">
	         <h4 class="panel-title"><?=_("서류발급신청리스트")?></h4>
	      </div>

	      <div class="panel-body panel-form">
	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("검색")?></label>
	         	<div class="col-md-5 form-inline">
	         		<input type="text" class="form-control" name="searchValue" value="<?=$_GET[searchValue]?>" size="50" placeholder='<?=_("아이디, 주문번호를 입력하세요")?>'>
	         	</div>
	         </div>

			 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("처리상태")?></label>
	         	<div class="col-md-3 form-inline">
	         		<select name="document_state" class="form-control">
	         		   <option value="" <?=$selected[document_state]['']?>><?=_("전체")?></option>
	         		   <option value="0" <?=$selected[document_state][0]?>>미처리</option>
					   <option value="1" <?=$selected[document_state][1]?>>처리</option>
	                </select>
	         	</div>
	         	<label class="col-md-2 control-label"><?=_("서류발급종류")?></label>
	         	<div class="col-md-3 form-inline">
	         		<select name="document_type" class="form-control">
	         		   <option value="" <?=$selected[document_type]['']?>><?=_("전체")?></option>
	         		   <? foreach ($r_document_type as $k2=>$v2) { ?>
	               	   <option value="<?=$k2?>" <?=$selected[document_type][$k2]?>><?=$v2?></option>
	               	   <? } ?>
	                </select>
	         	</div>
	         </div>

	         <div class="form-group">
	         	<label class="col-md-2 control-label"><?=_("서류발급신청일")?></label>
	      	 	<div class="col-md-5">
	      	 		<div class="input-group input-daterange">
	      	 			<input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_GET[start]?>">
	      	 			<span class="input-group-addon"> ~ </span>
	      	 			<input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_GET['end']?>">
	      	 		</div>
	      	 	</div>

	         	<label class="col-md-2 control-label"></label>
	         	<div class="col-md-3">
	          		<button type="submit" class="btn btn-sm btn-success"><?=_("검색")?></button>
					<button type="button" class="btn btn-sm btn-default" onclick="xls_case('order_cash','<?=$_query?>&pod_signed=<?=$pod_signed?>&pod_expired=<?=$pod_expired?>')"><?=_("엑셀저장")?></button>
	          	</div>
	         </div>
	         </form>
			 <form method="post" action="indb.php" onsubmit="return submitContents(this);">
			 <input type="hidden" name="mode" value="order_cash">
			 <input type="hidden" name="bno">
	         <div class="panel-body">
	         	<div class="table-responsive">
	         		<table id="data-table" class="table table-hover table-bordered">
	         			<thead>
	         				<tr>
	         					<th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>
								<th><?=_("주문번호")?></th>
	         					<th><?=_("서류발급정보")?></th>
	         					<th><?=_("신청자아이디")?></th>
	         					<th><?=_("서류발급신청일")?></th>
								<th><?=_("처리상태")?></th>
	         				</tr>
	         			</thead>
	         			<tbody>
         					<? foreach ($list as $k=>$v) { ?>
         						<?
         							$payno_data = "<a href=\"javascript:;\" onclick=\"popup('order_detail_popup.php?payno=$v[payno]',1200,750)\"><b>$v[payno]</b></a>";

         							$document_data = _("서류발급종류")." : ".$r_document_type[$v[document_type]];

									if ($v[mobile] && $v[mobile] != "010--") {
										$document_data .= "<br>"._("핸드폰")." : ".$v[mobile];
									}

									if ($v[email] && $v[email] != "@") {
										$document_data .= "<br>"._("이메일")." : ".$v[email];
									}

									if ($v[card_num] && $v[card_num] != "---") {
										$document_data .= "<br>"._("카드번호")." : ".$v[card_num];
									}

									if ($v[licensee_num] && $v[licensee_num] != "--") {
										$document_data .= "<br>"._("사업자번호")." : ".$v[licensee_num];
									}

									if ($v[document_file]) {
										$document_data .= "<br>"._("첨부파일")." : <a href=\"../../data/document/$cid/$v[document_file]\" target=\"_blank\">".$v[document_file]."</a>";
									}

         							if ($v[mid]) {
         								if ($v[mid] != "admin") {
         									$mid_data = "<a href=\"javascript:;\" onclick=\"popup('../member/member_detail_popup.php?mode=member_modify&mid=$v[mid]',1100,800)\">$v[name]<br>($v[mid])</a>";
         								} else {
         									$mid_data = "$v[name]<br>($v[mid])";
         								}
         							} else {
         								$mid_data = _("비회원");
         							}

         						?>
         						<tr>
         							<td><input type="checkbox" name="chk[]" value="<?=$v[no]?>"></td>
									<td><?=$payno_data?></td>
         							<td><?=$document_data?></td>
         							<td><?=$mid_data?></td>
         							<td><?=$v[regdt]?></td>
									<td><? echo $v[state]==1 ? 'Y' : 'N' ?></td>
         						</tr>
         					<? } ?>
	         			</tbody>
	         		</table>
					<div class="text-left m-b-0">
						<button type="submit" class="btn btn-sm btn-success"><?=_("처리완료")?></button>
					</div>
	         	</div>
	         </div>
	      </div>
	   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script type="text/javascript">
function submitContents(f) {
	var tmp = [];
	var c = document.getElementsByName('chk[]');

	for (var i=0;i<c.length;i++) {
		if (c[i].checked) tmp[tmp.length] = c[i].value;
	}

	if (tmp[0]) {
		f.bno.value = tmp;
	} else {
		alert('<?=_("체크된 항목이 없습니다.")?>', -1);
		return false;
	}
}

/* Table initialisation */
$(document).ready(function() {
	$('#data-table').dataTable({
		"sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
		"sPaginationType" : "bootstrap",
        //"aLengthMenu": [5, 10, 25, 50, 100, 10],
    "aLengthMenu": [5, 10, 25, 50, 100], // 개씩보기 끝에 10개 삭제 210812 jtkim
		"bFilter" : false,
		"oLanguage" : {
			"sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
		},
		"aoColumns": [
        { "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": true },
		],
		"processing": false,
		"serverSide": true,
		"bAutoWidth": false,
		"deferLoading": <?=$totalCnt?>,
		"ajax": $.fn.dataTable.pipeline({
			url: 'document_list_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
		})
	});
});

var handleDatepicker = function() {
	$('.input-daterange').datepicker({
		language : 'kor',
		todayHighlight : true,
		autoclose : true,
		todayBtn : true,
		format : 'yyyy-mm-dd',
	});
};

handleDatepicker();
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>
