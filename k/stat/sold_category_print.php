<?

include "../lib.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sold_category".date("YmdHi").".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

$m_goods = new M_goods();
$m_etc = new M_etc();
$r_w = array(0=>"일",1=>"월",2=>"화",3=>"수",4=>"목",5=>"금",6=>"토");
$r_category = array();

$c_data = $m_goods->getTopCategoryList($_GET[cid]);

foreach ($c_data as $value) {
	$r_category[$value[catno]] = $value[catnm];
}

if (!$_GET[start] && !$_GET['end']) {
	$_GET[start] = date('Y-m-d', strtotime("-1 MONTH"));
	$_GET['end'] = date('Y-m-d');
}

$startDate = str_replace("-", "", $_GET[start]);
$endDate = str_replace("-", "", $_GET['end']);

$day1 = strtotime($_GET[start]);
$day2 = strtotime($_GET['end']);
$gap = ($day2 - $day1)/60/60/24;
	
$loop = array();

for ($i=0;$i<=$gap;$i++) {
	$loop[date("Y-m-d", strtotime($_GET[start]." + $i days"))] = array();
}

$data = $m_etc->getSoldCategory($_GET[cid], $startDate, $endDate, array_keys($r_category));

foreach ($data as $value) {
	$loop[$value[dt]][price][$value[catno]] = $value[payprice];
	$tot[$value[catno]] += $value[payprice];
}

krsort($loop);

$tableline = "border:thin solid #dedede";

?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>

<table>
	<thead>
		<tr>
			<th style="<?=$tableline?>">날짜</th>
			<? foreach ($r_category as $k=>$v) { ?>
				<th style="<?=$tableline?>"><?=$v?></th>
			<? } ?>
			<th style="<?=$tableline?>">합계</th>
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
			<td width="150" align="center" style="<?=$tableline?>"><?=$k?> (<?=$v[wstr]?>)</td>
			<? foreach ($r_category as $k2=>$v2) { ?>
				<td width="75" style="<?=$tableline?>"><?=number_format($v[price][$k2])?></td>
			<? } ?>
			<td width="75" style="<?=$tableline?>"><?=(is_array($v[price])) ? number_format(array_sum($v[price])) : 0?></td>
		</tr>
		<? } ?>
		<tr align="right">
			<td width="150" align="center" style="<?=$tableline?>"><b>TOTAL</b></td>
			<? foreach ($r_category as $k2=>$v2) { ?>
				<td width="75" style="<?=$tableline?>"><b><?=number_format($tot[$k2])?></b></td>
			<? } ?>
			<td width="75" style="<?=$tableline?>"><b><?=(is_array($tot)) ? number_format(array_sum($tot)) : 0?></b></td>
		</tr>
	</tbody>
</table>
