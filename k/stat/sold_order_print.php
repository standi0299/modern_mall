<?

include "../lib.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sold_order".date("YmdHi").".xls");
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
if ($startDate) $addWhere .= " and a.paydt>{$startDate}";
if ($endDate) $addWhere .= " and a.paydt<adddate({$endDate},interval 1 day)";

if ($_GET[catno]) $addWhere .= " and c.catno like '$_GET[catno]%'";

$_GET[sword] = urldecode($_GET[sword]);
if ($_GET[sword]) $addWhere .= " and b.goodsnm like '%$_GET[sword]%'";

$orderby = "order by payno desc";

$data = $m_etc->getSoldOrder($_GET[cid], $addWhere, $orderby);

$tableline = "border:thin solid #dedede";

?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>

<table>
	<thead>
		<tr>
			<th style="<?=$tableline?>">결제일</th>
			<th style="<?=$tableline?>">결제번호</th>
			<th style="<?=$tableline?>">주문자</th>
			<th style="<?=$tableline?>">상품명</th>
			<th style="<?=$tableline?>">옵션</th>
			<th style="<?=$tableline?>">수량</th>
			<th style="<?=$tableline?>">금액</th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($data as $k=>$v) {
			if ($v[addopt]) $v[addopt] = unserialize($v[addopt]);
			if ($v[printopt]) $v[printopt] = unserialize($v[printopt]);
		?>
		<tr align="right" style="<?=$rowcolor?>">
			<td width="150" align="center" style="<?=$tableline?>"><?=$v[paydt]?></td>
			<td width="100" align="center" style="mso-number-format:'@'; <?=$tableline?>"><?=$v[payno]?></td>
			<td width="200" align="center" style="<?=$tableline?>">
				<div><?=$v[orderer_name]?></div>
				<? if ($v[mid]) { ?>
					(<?=$v[mid]?>)
				<? } else { ?>
					(비회원)
				<? } ?>
			</td>
			<td width="200" align="left" style="<?=$tableline?>"><?=$v[goodsnm]?></td>
			<td width="200" align="left" style="<?=$tableline?>">
				<div><?=$v[opt]?></div>
				<? if ($v[addpage]) { ?>
					<div>추가페이지 : <?=number_format($v[addpage])?></div>
				<? } ?>
				<? if ($v[addopt]) foreach ($v[addopt] as $k2=>$v2) { ?>
					<div><?=$v2[addopt_bundle_name]?> : <?=$v2[addoptnm]?></div>
				<? } ?>
				<? if ($v[printopt]) foreach ($v[printopt] as $k2=>$v2) { ?>
					<div><?=$v2[printoptnm]?> : <?=$v2[ea]?></div>
				<? } ?>
			</td>
			<td width="100" style="<?=$tableline?>"><?=number_format($v[ea])?></td>
			<td width="100" style="<?=$tableline?>"><?=number_format($v[payprice])?></td>
		</tr>
		<? } ?>
	</tbody>
</table>
