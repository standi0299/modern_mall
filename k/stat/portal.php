<?

include "../_header.php";
include "../_left_menu.php";

$m_etc = new M_etc();
$r_y = array("日","月","火","水","木","金","土");
$r_portal = array("naver"=>"네이버","daum"=>"다음","google"=>"구글","yahoo"=>"야후","nate"=>"네이트","about"=>"어바웃");
$r_method = array(
	"naver"		=> array("etc"=>"검색/기타"),
	"daum"		=> array("etc"=>"검색/기타"),
	"google"	=> array("etc"=>"검색/기타"),
	"yahoo"		=> array("etc"=>"검색/기타"),
	"nate"		=> array("etc"=>"검색/기타"),
	"about"		=> array("etc"=>"검색/기타"),
);
$r_adtype = array(
	"shopping"	=> "쇼핑",
	"mail"		=> "메일",
	"blog"		=> "블로그",
	"cafe"		=> "카페",
	"etc"		=> "검색/기타",
);

$column = "day,site,adtype,sum(hit) as h,sum(u) as u";

$addWhere = "where cid='$cid' and site!='bookmark'";

if (!$_POST[start] && !$_POST['end']) {
	$_POST[start] = date('Y-m-d', strtotime("-1 MONTH"));
	$_POST['end'] = date('Y-m-d');
}
if ($_POST[start]) $addWhere .= " and date_format(day,'%Y-%m-%d') >= '{$_POST[start]}'";
if ($_POST['end']) $addWhere .= " and date_format(day,'%Y-%m-%d') < adddate('{$_POST[end]}',interval 1 day)";

$groupby = "group by day,site,adtype";

$data = $m_etc->getSiteLog2($cid, $column, $addWhere, $groupby);

$day1 = strtotime($_POST[start]);
$day2 = strtotime($_POST['end']);
$gap = ($day2 - $day1)/60/60/24;
	
$loop = array();

for ($i=0;$i<=$gap;$i++) {
	$loop[date("Y-m-d", strtotime($_POST[start]." + $i days"))] = array();
}

foreach ($data as $value) {
	$value[y] = date('w', strtotime($value[day]));

	if (!in_array($value[site], array_keys($r_portal))) $value[site] = "etc";
	if (!$value[adtype]) $value[adtype] = "etc";
	
	$loop[toDate($value[day])][$value[site]][$value[adtype]][h] = $value[h];
	$loop[toDate($value[day])][$value[site]][$value[adtype]][u] = $value[u];
	$tot[$value[site]][$value[adtype]][h] += $value[h];
	$tot[$value[site]][$value[adtype]][u] += $value[u];
	$r_method[$value[site]][$value[adtype]] = $r_adtype[$value[adtype]];
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
         <h4 class="panel-title"><?=_("포털별분석")?></h4>
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
         					<th rowspan="2"><?=_("날짜")?></th>
         					<? foreach ($r_portal as $k=>$v) { ?>
         						<th colspan="<?=count($r_method[$k])?>"><?=_("$v")?></th>
         					<? } ?>
         					<th rowspan="2"><?=_("기타")?></th>
         				</tr>
         				<tr>
         					<? foreach ($r_portal as $k=>$v) { ?>
         						<? foreach ($r_method[$k] as $k2=>$v2) { ?>
         							<th><?=_("$v2")?></th>
         						<? } ?>
         					<? } ?>
         				</tr>
         			</thead>
         			<tbody>
         			<? foreach ($loop as $k=>$v) {
         				$v[y] = $r_y[date('w', strtotime($k))];
						
         				switch (date('w', strtotime($k))) {
							case "0": $rowcolor = "background:#F5F1C6"; break;
							case "6": $rowcolor = "background:#F2FACF"; break;
							default : $rowcolor = ""; break;
						}
         			?>
         				<tr align="right" style="<?=$rowcolor?>">
							<td width="150" align="center"><?=$k?> -<?=$v[y]?>-</td>
							<? foreach ($r_portal as $k2=>$v2) { ?>
								<? foreach ($r_method[$k2] as $k3=>$v3) { ?>
									<td width="75"><?=($v[$k2][$k3][u]) ? number_format($v[$k2][$k3][u]) : "-"?></td>
								<? } ?>
							<? } ?>
							<td width="75"><?=($v[etc][etc][h]) ? number_format($v[etc][etc][u]) : "-"?></td>
						</tr>
					<? } ?>
						<tr align="right">
							<td width="150" align="center"><b>TOTAL</b></td>
							<? foreach ($r_portal as $k2=>$v2) { ?>
								<? foreach ($r_method[$k2] as $k3=>$v3) { ?>
									<td width="75"><b><?=($tot[$k2][$k3][u]) ? number_format($tot[$k2][$k3][u]) : "-"?></b></td>
								<? } ?>
							<? } ?>
							<td width="75"><b><?=($tot[etc][etc][u]) ? number_format($tot[etc][etc][u]) : "-"?></b></td>
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