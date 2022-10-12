<?

include "../_header.php";
include "../_left_menu.php";

$m_goods = new M_goods();
$m_etc = new M_etc();
$r_w = array(0=>"일",1=>"월",2=>"화",3=>"수",4=>"목",5=>"금",6=>"토");
$r_category = array();

$c_data = $m_goods->getTopCategoryList($cid);

foreach ($c_data as $value) {
	$r_category[$value[catno]] = $value[catnm];
}

if (!$_POST[start] && !$_POST['end']) {
	$_POST[start] = date('Y-m-d', strtotime("-1 MONTH"));
	$_POST['end'] = date('Y-m-d');
}

$startDate = str_replace("-", "", $_POST[start]);
$endDate = str_replace("-", "", $_POST['end']);

$day1 = strtotime($_POST[start]);
$day2 = strtotime($_POST['end']);
$gap = ($day2 - $day1)/60/60/24;
	
$loop = array();

for ($i=0;$i<=$gap;$i++) {
	$loop[date("Y-m-d", strtotime($_POST[start]." + $i days"))] = array();
}

$data = $m_etc->getSoldCategory($cid, $startDate, $endDate, array_keys($r_category));

foreach ($data as $value) {
	$loop[$value[dt]][price][$value[catno]] = $value[payprice];
	$tot[$value[catno]] += $value[payprice];
}

krsort($loop);

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST">
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("카테고리별 매출통계")?></h4>
      </div>
      
      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("기간")?></label>
      	 	<div class="col-md-3">
      	 		<div class="input-group input-daterange">
      	 			<input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_POST[start]?>">
      	 			<span class="input-group-addon"> ~ </span>
      	 			<input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_POST['end']?>">
      	 		</div>
      	 	</div>
      	 	
      	 	<div class="col-md-4">
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[yesterday]?>" onclick="regdtOnlyOne('yesterday','start', 'yesterday'); regdtOnlyOne('yesterday','end');"><?=_("어제")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');"><?=_("오늘")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');"><?=_("1주일")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[month]?>" onclick="regdtOnlyOne('month','start', 'month'); regdtOnlyOne('today','end');"><?=_("1달")?></button>
      	 		<button type="button" class="btn btn-sm btn-<?=$button_color[tmonth]?>" onclick="regdtOnlyOne('tmonth','start', 'tmonth'); regdtOnlyOne('today','end');"><?=_("3달")?></button>
      	 	</div>
      	 	
      	 	<div class="col-md-3">
      	 		<div id="year_left" class="fa fa-chevron-left fa-lg cursorPointer"></div> 
      	 			<span id="year_val"><?=date("Y")?></span>년 
      	 		<div id="year_right" class="fa fa-chevron-right fa-lg cursorPointer"></div>
      	 		<div id="month_left" class="fa fa-chevron-left fa-lg cursorPointer"></div> 
      	 			<span id="month_val"><?=date("m")?></span>월 
      	 		<div id="month_right" class="fa fa-chevron-right fa-lg cursorPointer"></div>
      	 		<button type="button" id="year_month_submit" class="btn btn-sm btn-month"><?=_("연월적용")?></button>
      	 	</div>
         </div>
         
         <div class="form-group">
      	 	<label class="col-md-2 control-label"></label>
          	<div class="col-md-10">
          		<button type="submit" class="btn btn-sm btn-success"><?=_("검색")?></button>
          		<button type="button" id="excel_submit" class="btn btn-sm btn-default"><?=_("엑셀저장")?></button></a>
          	</div>
         </div>
         
         <div class="panel-body">
         	<div class="table-responsive">
         		<table id="data-table" class="table table-hover table-bordered">
         			<thead>
         				<tr>
         					<th><?=_("날짜")?></th>
         					<? foreach ($r_category as $k=>$v) { ?>
         						<th><?=_("$v")?></th>
         					<? } ?>
         					<th><?=_("합계")?></th>
         				</tr>
         			</thead>
         			<tbody>
         			<? foreach ($loop as $k=>$v) {
						 $v[w] = date("w", strtotime($k));
						 $v[wstr] = $r_w[$v[w]];   
						 
						 switch($v[w]) {
						 	case "0": $rowcolor = "background:#F5F1C6"; break;
						   	case "6": $rowcolor = "background:#F2FACF"; break;
						   	default : $rowcolor = ""; break;
						 }       				
         			?>
         				<tr align="right" style="<?=$rowcolor?>">
         					<td width="150" align="center"><?=$k?> (<?=$v[wstr]?>)</td>
         					<? foreach ($r_category as $k2=>$v2) { ?>
         						<td width="75"><?=number_format($v[price][$k2])?></td>
         					<? } ?>
							<td width="75"><?=(is_array($v[price])) ? number_format(array_sum($v[price])) : 0?></td>
						</tr>
					<? } ?>
						<tr align="right">
							<td width="150" align="center"><b>TOTAL</b></td>
							<? foreach ($r_category as $k2=>$v2) { ?>
								<td width="75"><b><?=number_format($tot[$k2])?></b></td>
							<? } ?>
							<td width="75"><b><?=(is_array($tot)) ? number_format(array_sum($tot)) : 0?></b></td>
						</tr>
         			</tbody>
         		</table>
         	</div>
         </div>
      </div>
   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>

<script type="text/javascript">
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

$j("#year_left").click(function() {
	if ($j("#year_val").text() > 1) $j("#year_val").text(parseInt($j("#year_val").text()) - 1);
});
$j("#year_right").click(function() {
	$j("#year_val").text(parseInt($j("#year_val").text()) + 1);
});
$j("#month_left").click(function() {
	if ($j("#month_val").text() > 1) $j("#month_val").text(getRegdt(parseInt($j("#month_val").text()) - 1));
});
$j("#month_right").click(function() {
	if ($j("#month_val").text() < 12) $j("#month_val").text(getRegdt(parseInt($j("#month_val").text()) + 1));
});

$j("#year_month_submit").click(function() {
	var start = $j("#year_val").text() + "-" + $j("#month_val").text() + "-01";
	
	var sd = new Date(start);
	sd.setMonth(sd.getMonth() + 1);
	sd.setDate(sd.getDate() - 1);
	
	var end = sd.getFullYear() + "-" + getRegdt(sd.getMonth() + 1) + "-" + getRegdt(sd.getDate());
	
	$j("[name=start]").val(start);
	$j("[name=end]").val(end);
});

$j("#excel_submit").click(function() {
	location.href = "sold_category_print.php?cid=<?=$cid?>&start=<?=$_POST[start]?>&end=<?=$_POST['end']?>";
});
</script>

<? include "../_footer_app_exec.php"; ?>