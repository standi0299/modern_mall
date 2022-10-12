<?
include "../_header.php";
include "../_left_menu.php";

$m_order = new M_order();

//날짜 버튼 클릭 후 조회시 버튼 색상 변경
if (!$_POST[date_buton_type]) $_POST[date_buton_type] = "week";
$button_color = array("yesterday" => "inverse","today" => "inverse","tdays" => "inverse","week" => "inverse","month" => "inverse","all" => "inverse");
if ($_POST[date_buton_type]) {
   $button_color[$_POST[date_buton_type]] = "warning";
}

if (!$_GET[start] && !$_GET['end']) {
  $timestamp = strtotime("-1 months");

	$_GET[start] = date('Y-m-d', $timestamp);
	$_GET['end'] = date('Y-m-d');
}

$selected[category][$_GET[category]] = "selected";

$addWhere = "";

$startDate = str_replace("-", "", $_GET[start]);
$endDate = str_replace("-", "", $_GET['end']);
if ($startDate) $addWhere .= " and a.regdt > '{$startDate}'";
if ($endDate) $addWhere .= " and a.regdt < adddate('{$endDate}',interval 1 day)";	

if ($_GET[searchValue] != "") {
	$addWhere .= " and (a.goodsnm like '%$_GET[searchValue]%' or a.request_company like '%$_GET[searchValue]%' or a.request_name like '%$_GET[searchValue]%')";
}

if ($_GET[category] != "") {
	$addWhere .= " and a.category = '$_GET[category]'";
}

$addQuery = " order by a.regdt desc";;
$addQuery .= " limit 0, 10";

$list = $m_order->getBigorderList($cid, $addWhere, $addQuery);
$totalCnt = $m_order->getBigorderListCnt($cid, $addWhere);

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
	         <h4 class="panel-title"><?=_("대량구매문의리스트")?></h4>
	      </div>
	      
	      <div class="panel-body panel-form">
	      	 <div class="form-group">
	      	 	<label class="col-md-2 control-label"><?=_("검색")?></label>
	         	<div class="col-md-5 form-inline">
	         		<input type="text" class="form-control" name="searchValue" value="<?=$_GET[searchValue]?>" size="50" placeholder='<?=_("상품명, 학교(단체명), 담당자를 입력하세요")?>'>
	         	</div>
	         	
	         	<label class="col-md-2 control-label"><?=_("문의종류")?></label>
	         	<div class="col-md-3 form-inline">
	         		<select name="category" class="form-control">
	         		   <option value="" <?=$selected[category]['']?>><?=_("전체")?></option>
	         		   <? foreach ($r_bigorder_type as $k2=>$v2) { ?>
	               	   <option value="<?=$k2?>" <?=$selected[category][$k2]?>><?=$v2?></option>
	               	   <? } ?>
	                </select>
	         	</div>
	         </div>
	         
	         <div class="form-group">
	         	<label class="col-md-2 control-label"><?=_("문의요청일")?></label>
	      	 	<div class="col-md-5">
	      	 		<div class="input-group input-daterange">
	      	 			<input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_GET[start]?>">
	      	 			<span class="input-group-addon"> ~ </span>
	      	 			<input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_GET['end']?>">
	      	 		</div>
	      	 	</div>
           </div>
           <div class="form-group">
	         	<label class="col-md-2 control-label">날짜</label>
             <div class="col-md-8">
                <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');">
                  <?=_("오늘")?>
                </button>
                <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start', 'tdays'); regdtOnlyOne('today','end');">
                  <?=_("3일")?>
                </button>
                <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');">
                  <?=_("1주일")?>
                </button>
                <button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start', 'month'); regdtOnlyOne('today','end');">
                  <?=_("1달")?>
                </button>
                <button type="button" class="btn btn-sm btn-<?=$button_color[all]?>" onclick="regdtOnlyOne('all','start', 'all'); regdtOnlyOne('today','end');">
                  <?=_("전체")?>
                </button>
                <button type="submit" class="btn btn-sm btn-success"><?=_("검색")?></button>
            </div>
	         </div>
	         
	         <div class="panel-body">
	         	<div class="table-responsive">
	         		<table id="data-table" class="table table-hover table-bordered">
	         			<thead>
	         				<tr>
	         					<th><?=_("대량구매문의종류")?></th>
	         					<th><?=_("상품명")?></th>
	         					<th><?=_("학교(단체명)")?></th>
	         					<th><?=_("담당자")?></th>
	         					<th><?=_("문의요청일")?></th>
	         					<th><?=_("보기")?></th>
	         				</tr>
	         			</thead>
	         			<tbody>
         					<? foreach ($list as $k=>$v) { ?>
         						<?
         							$view_button = "<a href=\"javascript:;\" onclick=\"popup('bigorder_popup.php?no=$v[no]',650,650)\"><span class=\"btn btn-xs btn-default\">보기</span></a>";
         						?>
         						<tr>
         							<td><?=$r_bigorder_type[$v[category]]?></td>
         							<td><?=$v[goodsnm]?></td>
         							<td><?=$v[request_company]?></td>
         							<td><?=$v[request_name]?></td>
         							<td><?=$v[regdt]?></td>
         							<td><?=$view_button?></td>
         						</tr>
         					<? } ?>
	         			</tbody>
	         		</table>
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
/* Table initialisation */
$(document).ready(function() {
	$('#data-table').dataTable({
		"sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
		"sPaginationType" : "bootstrap",
        "aLengthMenu": [10, 25, 50, 100],
		"bFilter" : false,
		"oLanguage" : {
			"sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
		},
		"aoColumns": [
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": true },
		{ "bSortable": false },
		],
		"processing": false,
		"serverSide": true,
		"bAutoWidth": false,
		"deferLoading": <?=$totalCnt?>,
		"ajax": $.fn.dataTable.pipeline({
			url: 'bigorder_list_page.php?postData=<?=$postData?>',
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