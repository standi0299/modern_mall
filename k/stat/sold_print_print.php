<?

include "../lib.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sold_print".date("YmdHi").".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

$m_etc = new M_etc();

if (!$_GET[start] && !$_GET['end']) {
	$_GET[start] = date('Y-m-d', strtotime("-1 MONTH"));
	$_GET['end'] = date('Y-m-d');
}

$startDate = str_replace("-", "", $_GET[start]);
$endDate = str_replace("-", "", $_GET['end']);

$data = $m_etc->getSoldPrint($_GET[cid], $startDate, $endDate);

$loop = array();

foreach ($data as $value) {
	$value[printopt] = unserialize($value[printopt]);
	$loop[$value[goodsno]][goodsnm] = $value[goodsnm];
	
	foreach ($value[printopt] as $k=>$v) {
		$loop[$value[goodsno]][printopt][$v[printoptnm]][ea] += $v[ea];
		$loop[$value[goodsno]][printopt][$v[printoptnm]][price] += $v[print_price] * $v[ea];
		$tot_sumea += $v[ea];
		$tot_sumprice += $v[print_price] * $v[ea];
	}
}

$tableline = "border:thin solid #dedede";

?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>

<table>
	<thead>
		<tr>
			<th style="<?=$tableline?>">상품번호</th>
			<th style="<?=$tableline?>">상품명</th>
			<th style="<?=$tableline?>">옵션</th>
			<th style="<?=$tableline?>">수량</th>
			<th style="<?=$tableline?>">판매가</th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($loop as $k=>$v) {
			foreach ($v[printopt] as $k2=>$v2) { ?> 
			<tr align="right" style="<?=$rowcolor?>">
				<td width="100" align="center" style="<?=$tableline?>"><?=$k?></td>
				<td width="300" align="left" style="<?=$tableline?>"><?=$v[goodsnm]?></td>
				<td width="100" align="left" style="<?=$tableline?>"><?=$k2?></td>
				<td width="100" style="<?=$tableline?>"><?=number_format($v2[ea])?></td>
				<td width="100" style="<?=$tableline?>"><?=number_format($v2[price])?></td>
			</tr>
		<? }} ?>
			<tr align="right">
				<td width="500" colspan="3" align="center" style="<?=$tableline?>"><b>TOTAL</b></td>
				<td width="100" style="<?=$tableline?>"><b><?=number_format($tot_sumea)?></b></td>
				<td width="100" style="<?=$tableline?>"><b><?=number_format($tot_sumprice)?></b></td>
			</tr>
	</tbody>
</table>
