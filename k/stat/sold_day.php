<?

include "../_header.php";
include "../_left_menu.php";

$m_etc = new M_etc();
$r_w = array(0=>"일",1=>"월",2=>"화",3=>"수",4=>"목",5=>"금",6=>"토");

if (!$_POST[year]) $_POST[year] = date("Y");
if (!$_POST[month]) $_POST[month] = date("m");

$_POST[year] = sprintf("%04d", $_POST[year]);
$_POST[month] = sprintf("%02d", $_POST[month]);

$month = $_POST[year]."-".$_POST[month];

$t = date("t", strtotime($month."-01"));

for ($i=1;$i<=$t;$i++) {
	$day = $month."-".sprintf("%02d", $i);
	$loop[$day] = array("w", $r_w[$w]);
}

unset($day);

$data = $m_etc->getSoldDay($cid, $month);

foreach ($data as $value) {
   $loop[$value[paydt]] = $value;
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
         <h4 class="panel-title"><?=_("일별판매통계")?></h4>
      </div>
      
      <div class="panel-body panel-form">
      	 <div class="form-group">
      	 	<label class="col-md-2 control-label"><?=_("기간")?></label>      	 	
      	 	<div class="col-md-8 month_day">
      	 		<div id="year_left" class="fa fa-chevron-left fa-lg cursorPointer"></div> 
      	 			<span id="year_val"><?=date("Y")?></span>년 
      	 		<div id="year_right" class="fa fa-chevron-right fa-lg cursorPointer"></div>
      	 		<div id="month_left" class="fa fa-chevron-left fa-lg cursorPointer"></div> 
      	 			<span id="month_val"><?=date("m")?></span>월 
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
         					<th><?=_("결제일")?></th>
         					<th><?=_("결제건")?></th>
         					<th><?=_("결제상품건")?></th>
         					<th><?=_("결제액")?></th>
         					<th><?=_("배송비")?></th>
         					<th><?=_("환불금액")?></th>
         					<th><?=_("매출액")?></th>
         					<th><?=_("평균주문액")?></th>
         					<th><?=_("평균상품가")?></th>
         				</tr>
         			</thead>
         			<tbody>
         			<? $sum = array();
					   $sum[paycnt] = array();
					   $sum[item_ea_cnt] = array();
					   $sum[payprice] = array();
					   $sum[shipprice] = array();
					   $sum[refund] = array();
					   $sum[shipprice] = array();
					   $sum[shipprice] = array();
					   $sum[totprice] = array();
					   $sum[paycnt] = array();
         			
         			   foreach ($loop as $k=>$v) {
						 $v[w] = date("w", strtotime($k));
					 	 $v[wstr] = $r_w[$v[w]];
						 $sum[paycnt][] = $v[paycnt];
						 $sum[shipprice][] = $v[shipprice];
						 $sum[payprice][] = $v[payprice];
						 $v[totprice] = $v[payprice] - $v[shipprice] - $v[refund];
						 $sum[totprice][] = $v[totprice];
						 $sum[refund][] = $v[refund];
						 $sum[item_ea_cnt][] = $v[item_ea_cnt];
						 if ($v[paycnt]) $v[avgpayprice] = round($v[payprice] - $v[shipprice] - $v[refund])/$v[paycnt];
						 else $v[avgpayprice] = 0;
						 if ($v[item_ea_cnt]) $v[avgitemprice] = round($v[payprice] - $v[shipprice] - $v[refund])/$v[item_ea_cnt];
						 else $v[avgitemprice] = 0;      
						 
						 switch($v[w]) {
						 	case "0": $rowcolor = "background:#F5F1C6"; break;
						   	case "6": $rowcolor = "background:#F2FACF"; break;
						   	default : $rowcolor = ""; break;
						 }				
         			?>
         				<tr align="right" style="<?=$rowcolor?>">
							<td width="150" align="center"><?=$k?> (<?=$v[wstr]?>)</td>
							<td width="100"><?=number_format($v[paycnt])?></td>
							<td width="100"><?=number_format($v[item_ea_cnt])?></td>
							<td width="100"><?=number_format($v[payprice])?></td>
							<td width="100"><?=number_format($v[shipprice])?></td>
							<td width="100"><?=number_format($v[refund])?></td>
							<td width="100"><?=number_format($v[totprice])?></td>
							<td width="100"><?=($v[avgpayprice]) ? number_format($v[avgpayprice]) : "-"?></td>
							<td width="100"><?=($v[avgitemprice]) ? number_format($v[avgitemprice]) : "-"?></td>
						</tr>
					<? } ?>
						<tr align="right">
							<td width="150" align="center"><b>TOTAL</b></td>
							<td width="100"><b><?=number_format(array_sum($sum[paycnt]))?></b></td>
							<td width="100"><b><?=number_format(array_sum($sum[item_ea_cnt]))?></b></td>
							<td width="100"><b><?=number_format(array_sum($sum[payprice]))?></b></td>
							<td width="100"><b><?=number_format(array_sum($sum[shipprice]))?></b></td>
							<td width="100"><b><?=number_format(array_sum($sum[refund]))?></b></td>
							<td width="100"><b><?=number_format(array_sum($sum[totprice]))?></b></td>
							<td width="100"><b><?=(array_notnull($sum[paycnt])) ? number_format(array_sum($sum[totprice])/array_sum($sum[paycnt])) : "-"?></b></td>
							<td width="100"><b><?=(array_notnull($sum[item_ea_cnt])) ? number_format(array_sum($sum[totprice])/array_sum($sum[item_ea_cnt])) : "-"?></b></td>
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