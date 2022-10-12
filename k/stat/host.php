<?

include "../_header.php";
include "../_left_menu.php";

$m_etc = new M_etc();

$column = "site,host,sum(hit) as h,sum(u) as u";

$addWhere = "where cid='$cid'";

if (!$_POST[start] && !$_POST['end']) {
	$_POST[start] = date('Y-m-d');
	$_POST['end'] = date('Y-m-d');
}
if ($_POST[start]) $addWhere .= " and date_format(day,'%Y-%m-%d') >= '{$_POST[start]}'";
if ($_POST['end']) $addWhere .= " and date_format(day,'%Y-%m-%d') < adddate('{$_POST[end]}',interval 1 day)";

$groupby = "group by site,host";

$orderby = "order by h desc,no desc";

$data = $m_etc->getSiteLog2($cid, $column, $addWhere, $groupby, $orderby);

$loop = array();
$loop2 = array();

foreach ($data as $value) {
	$loop[$value[site]][h] += $value[h];
	$loop[$value[site]][u] += $value[u];
	$loop2[$value[site]][$value[host]][h] += $value[h];
	$loop2[$value[site]][$value[host]][u] += $value[u];
	$tot[h] += $value[h];
	$tot[u] += $value[u];
}

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST">
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("호스트별분석")?></h4>
      </div>
      
      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-1 control-label"><?=_("기간")?></label>
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
          	 	
          	<div class="col-md-1">
          		<button type="submit" class="btn btn-sm btn-success"><?=_("검색")?></button>
          	</div>
         </div>
         
         <div class="panel-body">
         	<div class="table-responsive">
         		<table id="data-table" class="table table-hover table-bordered">
         			<thead>
         				<tr>
         					<th><?=_("번호")?></th>
         					<th><?=_("호스트")?></th>
         					<th><?=_("유입수")?></th>
         					<th><?=_("방문자")?></th>
         					<th><?=_("방문자 비율")?></th>
         				</tr>
         			</thead>
         			<tbody>
         			<? foreach ($loop as $k=>$v) { ?>
         				<tr align="right">
							<td width="50" align="center" rowspan="<?=count($loop2[$k])+1?>"><?=++$idx?></td>
							<td width="200" align="left"><b><?=$k?></b></td>
							<td width="100"><?=number_format($v[h])?></td>
							<td width="100"><?=number_format($v[u])?></td>
							<td width="100"><?=@number_format(($v[u]/$tot[u])*100, 2)?> %</td>
						</tr>
						<? foreach ($loop2[$k] as $k2=>$v2) { ?>
							<tr align="right">
								<td width="200" align="left"><div class="textIndent"><?=$k2?></div></td>
								<td width="100"><?=number_format($v2[h])?></td>
								<td width="100"><?=number_format($v2[u])?></td>
								<td width="100"><?=@number_format(($v2[u]/$tot[u])*100, 2)?> %</td>
							</tr>
						<? } ?>
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
</script>

<? include "../_footer_app_exec.php"; ?>