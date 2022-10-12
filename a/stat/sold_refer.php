<?

include "../_header.php";
include "../_left_menu.php";

$m_etc = new M_etc();
$r_w = array(0=>_("일"),1=>_("월"),2=>_("화"),3=>_("수"),4=>_("목"),5=>_("금"),6=>_("토"));
$r_portal = array(
	"naver"=>array("search.naver.com",_("네이버키워드")),
	"navershopping"=>array("shopping.naver.com",_("네이버지식쇼핑")),
	"daum"=>array("search.daum.net",_("다음키워드")),
	"daumshopping"=>array("shopping.daum.net",_("다음쇼핑하우")),
	"nate"=>array("search.nate.net",_("네이트")),
	"google"=>array("google.co.kr",_("구글")),
	"about"=>array("about.co.kr",_("어바웃")),
);

if (!$_POST[year]) $_POST[year] = date("Y");
if (!$_POST[month]) $_POST[month] = date("m");

$_POST[year] = sprintf("%04d", $_POST[year]);
$_POST[month] = sprintf("%02d", $_POST[month]);

$month = $_POST[year]."-".$_POST[month];

$t = date("t", strtotime($month."-01"));

for ($i=1;$i<=$t;$i++) {
	$day = $month."-".sprintf("%02d", $i);
	$loop[$day] = array("cnt"=>array(), "payprice"=>array());
}

unset($day);

$tot[payprice] = array();
$tot[cnt] = array();

$data = $m_etc->getSoldRefer($cid, $month);

foreach ($data as $value) {
	$parse = parse_url($value[referer]);
	
	if (!$value[referer]) { # direct
		$loop[$value[paydt]][cnt][direct]++;
		$loop[$value[paydt]][payprice][direct] += $value[payprice];
		$tot[cnt][direct]++;
		$tot[payprice][direct] += $value[payprice];
		continue;
	}

	if (strpos($parse[host], "mail") !== false) { # 메일
		$loop[$value[paydt]][cnt][mail]++;
		$loop[$value[paydt]][payprice][mail] += $value[payprice];
		$tot[cnt][mail]++;
		$tot[payprice][mail] += $value[payprice];
		continue;
	}
	
	if (strpos($value[referer], "sea36.chol.com/webmail") !== false) { # 메일
		$loop[$value[paydt]][cnt][mail]++;
		$loop[$value[paydt]][payprice][mail] += $value[payprice];
		$tot[cnt][mail]++;
		$tot[payprice][mail] += $value[payprice];
		continue;
	}
	
	foreach ($r_portal as $k=>$v) {
		if (strpos($parse[host], $v[0]) !== false) {
			$loop[$value[paydt]][cnt][$k]++;
			$loop[$value[paydt]][payprice][$k] += $value[payprice];
			$tot[cnt][$k]++;
			$tot[payprice][$k] += $value[payprice];
			continue;		
		}
	}
	
	## 기타
	$loop[$value[paydt]][cnt][etc]++;
	$loop[$value[paydt]][payprice][etc] += $value[payprice];
	$tot[cnt][etc]++;
	$tot[payprice][etc] += $value[payprice];
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
         <h4 class="panel-title"><?=_("유입경로별 매출통계")?></h4>
      </div>
      
      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("기간")?></label>      	 	
      	 	<div class="col-md-8 month_day">
      	 		<div id="year_left" class="fa fa-chevron-left fa-lg cursorPointer"></div> 
      	 			<span id="year_val"><?=date("Y")?></span><?=_("년")?> 
      	 		<div id="year_right" class="fa fa-chevron-right fa-lg cursorPointer"></div>
      	 		<div id="month_left" class="fa fa-chevron-left fa-lg cursorPointer"></div> 
      	 			<span id="month_val"><?=date("m")?></span><?=_("월")?> 
      	 		<div id="month_right" class="fa fa-chevron-right fa-lg cursorPointer"></div>
      	 	</div>
          	 	
          	<div class="col-md-2">
          		<button type="submit" class="btn btn-sm btn-success"><?=_("검색")?></button>
          	</div>
         </div>
         
         <div class="panel-body">
         	<div class="table-responsive">
         		<table id="data-table" class="table table-hover table-bordered">
         			<thead>
         				<tr>
         					<th><?=_("날짜")?></th>
         					<? foreach ($r_portal as $k=>$v) { ?>
         						<th><?=_("$v[1]")?></th>
         					<? } ?>
         					<th><?=_("메일")?></th>
         					<th><?=_("Direct")?></th>
         					<th><?=_("ETC")?></th>
         					<th><?=_("TOTAL")?></th>
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
							<? foreach ($r_portal as $k2=>$v2) { ?>
								<td width="75"><b><?=number_format($v[payprice][$k2])?></b> (<?=number_format($v[cnt][$k2])?>)</td>
							<? } ?>
							<td width="75"><b><?=number_format($v[payprice]['mail'])?></b> (<?=number_format($v[cnt]['mail'])?>)</td>
							<td width="75"><b><?=number_format($v[payprice][direct])?></b> (<?=number_format($v[cnt][direct])?>)</td>
							<td width="75"><b><?=number_format($v[payprice][etc])?></b> (<?=number_format($v[cnt][etc])?>)</td>
							<td width="75"><b><?=number_format(array_sum($v[payprice]))?></b> (<?=number_format(array_sum($v[cnt]))?>)</td>
						</tr>
					<? } ?>
						<tr align="right">
							<td width="150" align="center"><b>TOTAL</b></td>
							<? foreach ($r_portal as $k2=>$v2) { ?>
								<td width="75"><b><?=number_format($tot[payprice][$k2])?></b> (<?=number_format($tot[cnt][$k2])?>)</td>
							<? } ?>
							<td width="75"><b><?=number_format($tot[payprice]['mail'])?></b> (<?=number_format($tot[cnt]['mail'])?>)</td>
							<td width="75"><b><?=number_format($tot[payprice][direct])?></b> (<?=number_format($tot[cnt][direct])?>)</td>
							<td width="75"><b><?=number_format($tot[payprice][etc])?></b> (<?=number_format($tot[cnt][etc])?>)</td>
							<td width="75"><b><?=number_format(array_sum($tot[payprice]))?></b> (<?=number_format(array_sum($tot[cnt]))?>)</td>
						</tr>
         			</tbody>
         		</table>
         	</div>
         </div>
      </div>
   </div>
   </form>
</div>

<script type="text/javascript">
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

$j("[type=submit]").click(function() {
	$j(fm).append("<input type='hidden' name='year' value='" + $j("#year_val").text() + "' />");
	$j(fm).append("<input type='hidden' name='month' value='" + $j("#month_val").text() + "' />");
	$j(fm).submit();
});
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>