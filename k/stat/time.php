<?

include "../_header.php";
include "../_left_menu.php";

$m_etc = new M_etc();

$addWhere = "where cid='$cid'";

if (!$_POST[start] && !$_POST['end']) {
	$_POST[start] = date('Y-m-d');
	$_POST['end'] = date('Y-m-d');
}
if ($_POST[start]) $addWhere .= " and date_format(day,'%Y-%m-%d') >= '{$_POST[start]}'";
if ($_POST['end']) $addWhere .= " and date_format(day,'%Y-%m-%d') < adddate('{$_POST[end]}',interval 1 day)";

$data = $m_etc->getSiteLog($cid, $addWhere);

$loop = array();

for ($i=0;$i<=23;$i++) {
	$time = sprintf("%02d", $i);
	$loop[$time] = array();
}

unset($p); unset($u);

foreach ($data as $value) {
	for ($i=0;$i<=23;$i++) {
		$loop[sprintf("%02d", $i)][u] += $value["u".sprintf("%02d", $i)];
		$loop[sprintf("%02d", $i)][p] += $value["p".sprintf("%02d", $i)];
		$tot[p] += $value["p".sprintf("%02d", $i)];
		$tot[u] += $value["u".sprintf("%02d", $i)];
	}
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
         <h4 class="panel-title"><?=_("시간별접속")?></h4>
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
         					<th><?=_("시간대")?></th>
         					<th><?=_("페이지뷰")?></th>
         					<th><?=_("증감률")?></th>
         					<th><?=_("방문자수")?></th>
         					<th><?=_("증감률")?></th>
         					<th><?=_("방문대비 페이지뷰")?></th>
         				</tr>
         			</thead>
         			<tbody>
         			<? foreach ($loop as $k=>$v) {
						 $b_time = sprintf("%02d", $k-1);

						 $b[p] = $loop[$b_time][p];
						 $b[u] = $loop[$b_time][u];
					
						 @$v[p_per] = ($v[p] - $b[p])/$b[p];
						 @$v[u_per] = ($v[u] - $b[u])/$b[u];
						 @$v[p_per_u] = number_format(($v[p]/$v[u])*100, 2)." %";
					
						 if ($v[p_per] > 0) {
						 	$v[p_flag] = "▲";
							$v[p_color] = "color:#006594";
						 } else if ($v[p_per] < 0) {
							$v[p_flag] = "▼";
							$v[p_color] = "color:#9d0038";
						 }
					
						 if ($v[u_per] > 0) {
							$v[u_flag] = "▲";
							$v[u_color] = "color:#006594";
						 } else if ($v[u_per] < 0) {
							$v[u_flag] = "▼";
							$v[u_color] = "color:#9d0038";
						 }
					
						 $v[p_per] = ($v[p_per]) ? number_format($v[p_per]*100, 2)." %":"-";
						 $v[u_per] = ($v[u_per]) ? number_format($v[u_per]*100, 2)." %":"-";     				
         			?>
         				<tr align="right">
							<td width="50" align="center"><?=$k?></td>
							<td width="100"><?=number_format($v[p])?></td>
							<td width="100" style="<?=$v[p_color]?>"><?=$v[p_per]?> <?=$v[p_flag]?></td>
							<td width="100"><?=number_format($v[u] + $v[r])?></td>
							<td width="100" style="<?=$v[u_color]?>"><?=$v[u_per]?> <?=$v[u_flag]?></td>
							<td width="100"><?=$v[p_per_u]?></td>
						</tr>
					<? } ?>
						<tr align="right">
							<td width="50" align="center"><b>TOTAL</b></td>
							<td width="100"><b><?=number_format($tot[p])?></b></td>
							<td width="100"><b>-</b></td>
							<td width="100"><b><?=number_format($tot[u])?></b></td>
							<td width="100"><b>-</b></td>
							<td width="100"><b><?=@number_format(($tot[p]/$tot[u])*100, 2)?> %</b></td>
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
</script>

<? include "../_footer_app_exec.php"; ?>