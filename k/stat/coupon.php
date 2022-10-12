<?

include "../_header.php";
include "../_left_menu.php";

$postData = base64_encode(json_encode($_POST));

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST">
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("쿠폰통계")?></h4>
      </div>
      
      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("발급기간")?></label>
      	 	<div class="col-md-3">
      	 		<div class="input-group input-daterange">
      	 			<input type="text" class="form-control" name="start_one" placeholder="Date Start" value="<?=$_POST[start_one]?>">
      	 			<span class="input-group-addon"> ~ </span>
      	 			<input type="text" class="form-control" name="end_one" placeholder="Date End" value="<?=$_POST[end_one]?>">
      	 		</div>
      	 	</div>
      	 	
      	 	<div class="col-md-4">
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[yesterday]?>" onclick="regdtOnlyOne('yesterday','start_one', 'yesterday'); regdtOnlyOne('yesterday','end_one');"><?=_("어제")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start_one', 'today'); regdtOnlyOne('today','end_one');"><?=_("오늘")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start_one', 'week'); regdtOnlyOne('today','end_one');"><?=_("1주일")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start_one', 'month'); regdtOnlyOne('today','end_one');"><?=_("1달")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[tmonth]?>" onclick="regdtOnlyOne('tmonth','start_one', 'tmonth'); regdtOnlyOne('today','end_one');"><?=_("3달")?></button>
      	 	</div>
      	 	
      	 	<div class="col-md-3">
      	 		<div data="one" class="fa fa-chevron-left fa-lg cursorPointer year_left"></div> 
      	 			<span id="year_val_one"><?=date("Y")?></span>년 
      	 		<div data="one" class="fa fa-chevron-right fa-lg cursorPointer year_right"></div>
      	 		<div data="one" class="fa fa-chevron-left fa-lg cursorPointer month_left"></div> 
      	 			<span id="month_val_one"><?=date("m")?></span>월 
      	 		<div data="one" class="fa fa-chevron-right fa-lg cursorPointer month_right"></div>
      	 		<button type="button" data="one" class="btn btn-sm btn-month year_month_submit"><?=_("연월적용")?></button>
      	 	</div>
         </div>
         
         <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("사용기간")?></label>
      	 	<div class="col-md-3">
      	 		<div class="input-group input-daterange">
      	 			<input type="text" class="form-control" name="start_two" placeholder="Date Start" value="<?=$_POST[start_two]?>">
      	 			<span class="input-group-addon"> ~ </span>
      	 			<input type="text" class="form-control" name="end_two" placeholder="Date End" value="<?=$_POST[end_two]?>">
      	 		</div>
      	 	</div>
      	 	
      	 	<div class="col-md-4">
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[yesterday]?>" onclick="regdtOnlyOne('yesterday','start_two', 'yesterday'); regdtOnlyOne('yesterday','end_two');"><?=_("어제")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start_two', 'today'); regdtOnlyOne('today','end_two');"><?=_("오늘")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start_two', 'week'); regdtOnlyOne('today','end_two');"><?=_("1주일")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start_two', 'month'); regdtOnlyOne('today','end_two');"><?=_("1달")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[tmonth]?>" onclick="regdtOnlyOne('tmonth','start_two', 'tmonth'); regdtOnlyOne('today','end_two');"><?=_("3달")?></button>
      	 	</div>
      	 	
      	 	<div class="col-md-3">
      	 		<div data="two" class="fa fa-chevron-left fa-lg cursorPointer year_left"></div> 
      	 			<span id="year_val_two"><?=date("Y")?></span>년 
      	 		<div data="two" class="fa fa-chevron-right fa-lg cursorPointer year_right"></div>
      	 		<div data="two" class="fa fa-chevron-left fa-lg cursorPointer month_left"></div> 
      	 			<span id="month_val_two"><?=date("m")?></span>월 
      	 		<div data="two" class="fa fa-chevron-right fa-lg cursorPointer month_right"></div>
      	 		<button type="button" data="two" class="btn btn-sm btn-month year_month_submit"><?=_("연월적용")?></button>
      	 	</div>
         </div>
         
         <div class="form-group">
         	<label class="col-md-2 control-label"><?=_("쿠폰명검색")?></label>
         	<div class="col-md-3">
         		<input type="text" class="form-control" name="sword" value="<?=$_POST[sword]?>">
         	</div>
         </div>
         
         <div class="form-group">
         	<label class="col-md-2 control-label"></label>
         	<div class="col-md-10">
          		<button type="submit" class="btn btn-sm btn-success"><?=_("검색")?></button>
          	</div>
         </div>
         
         <div class="panel-body">
         	<div class="table-responsive">
         		<div><span class="notice">[설명]</span> 발급기간 입력시 지정된 발급기간중 1일이라도 발급이 가능한 쿠폰이 조회됩니다.</div>
         		<div><span class="notice">[설명]</span> 사용기간은 고객의 쿠폰사용일을 나타내며, 아래 표에서 사용수에 영향을 미칩니다.</div><p>
         		<div><span class="notice">[설명]</span> 발행수는 쿠폰생성시 관리자가 입력한 쿠폰발급 개수입니다.</div>
         		<div><span class="notice">[설명]</span> 등록수는 쿠폰생성후 관리자에 의해 지급되었거나 사용자의 다운로드 및 오프라인쿠폰으로 등록된 개수입니다.</div>
         		<div><span class="notice">[설명]</span> 사용수는 실제적으로 사용자가 주문을 통하여 사용된 개수입니다.</div><p>
         		<table id="data-table" class="table table-hover table-bordered">
         			<thead>
         				<tr>
         					<th><?=_("번호")?></th>
         					<th><?=_("상태")?></th>
         					<th><?=_("쿠폰명")?></th>
         					<th><?=_("발행수")?></th>
         					<th><?=_("등록수")?></th>
         					<th><?=_("사용수")?></th>
         					<th><?=_("사용률(발행대비)")?></th>
         					<th><?=_("사용률(등록대비)")?></th>
         				</tr>
         			</thead>
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
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>

<script type="text/javascript">
/* Table initialisation */
$(document).ready(function() {
	$('#data-table').dataTable({
		"sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
		"sPaginationType" : "bootstrap",
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
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		],
		"processing": false,
		"serverSide": true,
		"bAutoWidth": false,
		"ajax": $.fn.dataTable.pipeline({
			url: 'coupon_page.php?postData=<?=$postData?>',
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

$j(".year_left").click(function() {
	var data = $(this).attr("data");
	if ($j("#year_val_" + data).text() > 1) $j("#year_val_" + data).text(parseInt($j("#year_val_" + data).text()) - 1);
});
$j(".year_right").click(function() {
	var data = $(this).attr("data");
	$j("#year_val_" + data).text(parseInt($j("#year_val_" + data).text()) + 1);
});
$j(".month_left").click(function() {
	var data = $(this).attr("data");
	if ($j("#month_val_" + data).text() > 1) $j("#month_val_" + data).text(getRegdt(parseInt($j("#month_val_" + data).text()) - 1));
});
$j(".month_right").click(function() {
	var data = $(this).attr("data");
	if ($j("#month_val_" + data).text() < 12) $j("#month_val_" + data).text(getRegdt(parseInt($j("#month_val_" + data).text()) + 1));
});

$j(".year_month_submit").click(function() {
	var data = $(this).attr("data");
	var start = $j("#year_val_" + data).text() + "-" + $j("#month_val_" + data).text() + "-01";
	
	var sd = new Date(start);
	sd.setMonth(sd.getMonth() + 1);
	sd.setDate(sd.getDate() - 1);
	
	var end = sd.getFullYear() + "-" + getRegdt(sd.getMonth() + 1) + "-" + getRegdt(sd.getDate());
	
	$j("[name=start_" + data + "]").val(start);
	$j("[name=end_" + data + "]").val(end);
});
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>