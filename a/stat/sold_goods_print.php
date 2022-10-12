<?

include "../lib.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sold_goods".date("YmdHi").".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

$m_etc = new M_etc();

$addWhere = "where a.cid='$_GET[cid]' and b.itemstep in (2,3,4,5,92)";

if (!$_GET[start] && !$_GET['end']) {
	$_GET[start] = date('Y-m-d', strtotime("-1 MONTH"));
	$_GET['end'] = date('Y-m-d');
}

$startDate = str_replace("-", "", $_GET[start]);
$endDate = str_replace("-", "", $_GET['end']);
if ($startDate) $addWhere .= " and a.paydt>'{$startDate}'";
if ($endDate) $addWhere .= " and a.paydt<adddate('{$endDate}',interval 1 day)";

if ($_GET[catno]) $addWhere .= " and b.catno like '$_GET[catno]%'";

$_GET[sword] = urldecode($_GET[sword]);
if ($_GET[sword]) $addWhere .= " and b.goodsnm like '%$_GET[sword]%'";

$data = $m_etc->getSoldGoods($_GET[cid], $addWhere);

$tableline = "border:thin solid #dedede";

?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>

<table>
	<thead>
		<tr>
			<th style="<?=$tableline?>"><?=_("카테고리")?></th>
			<th style="<?=$tableline?>"><?=_("상품명")?></th>
			<th style="<?=$tableline?>"><?=_("수량")?></th>
			<th style="<?=$tableline?>"><?=_("합계")?></th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($data as $k=>$v) {
			$tot_sumea += $v[sumea];
			$tot_sumprice += $v[sumprice];
		?>
		<tr align="right" style="<?=$rowcolor?>">
			<td width="300" align="left" style="<?=$tableline?>"><?=$v[catnm]?></td>
			<td width="300" align="left" style="<?=$tableline?>"><?=$v[goodsnm]?></td>
			<td width="100" style="<?=$tableline?>"><?=number_format($v[sumea])?></td>
			<td width="100" style="<?=$tableline?>"><?=number_format($v[sumprice])?></td>
		</tr>
		<? } ?>
		<tr align="right">
			<td width="600" colspan="2" align="center" style="<?=$tableline?>"><b>TOTAL</b></td>
			<td width="100" style="<?=$tableline?>"><b><?=number_format($tot_sumea)?></b></td>
			<td width="100" style="<?=$tableline?>"><b><?=number_format($tot_sumprice)?></b></td>
		</tr>
	</tbody>
</table>
