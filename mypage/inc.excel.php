<style>td,th {border:thin solid #cccccc;}</style>

<table style="border-collapse:collapse">
<tr>
	<th><?=_("아이디")?></th>
	<th><?=_("이름")?></th>
	<th><?=_("주문일자")?></th>
	<th><?=_("주문번호")?></th>
	<th><?=_("주문상품")?></th>
	<th><?=_("상품옵션")?></th>
	<th><?=_("추가옵션")?></th>
	<th><?=_("인화옵션")?></th>
	<th><?=_("주문수량")?></th>
	<th><?=_("주문금액")?></th>
	<th><?=_("출고처")?></th>
	<th><?=_("배송비")?></th>
	<th><?=_("주문상태")?></th>
</tr>
<? if (!$loop) $loop = array();
foreach ($loop as $k=>$v) {
	$idx2 = 0;
	foreach ($v[ord] as $k2=>$v2) {
		$idx3 = 0;
		foreach ($v2[item] as $k3=>$v3) {
			$r_addopt = array();
			
			if ($v3[addopt]) {
				foreach ($v3[addopt] as $k4=>$v4) {
					$r_addopt[] = $v4[addopt_bundle_name]." : ".$v4[addoptnm];
				}
			}
			
			$r_addopt = implode(", ", $r_addopt);
			$r_printopt = array();
			
			if (is_array($v3[printopt])) {
				foreach ($v3[printopt] as $k4=>$v4) {
					$r_printopt[] = $v4[printoptnm]." : ".$v4[ea];
				}
			}
			$r_printopt = implode(", ", $r_printopt);
?>
<tr align="center">
	<? if (!$idx2 && !$idx3) { ?>
	<td rowspan="<?=$v[rowspan]?>"><?=$v[mid]?></td>
	<td rowspan="<?=$v[rowspan]?>"><?=$v[orderer_name]?></td>
	<td rowspan="<?=$v[rowspan]?>"><?=substr($v[orddt], 0, 10)?></td>
	<td rowspan="<?=$v[rowspan]?>" style="mso-number-format:'@'"><?=$v3[payno]?></td>
	<? } ?>
	<td align="left"><?=$v3[goodsnm]?></td>
	<td align="left"><?=$v3[opt]?></td>
	<td align="left"><?=$r_addopt?></td>
	<td align="left"><?=$r_printopt?></td>
	<td><?=$v3[ea]?></td>
	<td><?=number_format($v3[ea]*$v3[saleprice])?></td>
	<? if (!$idx3) { ?>
	<td rowspan="<?=$v2[rowspan]?>"><?=$r_rid_x[$v2[rid]]?></td>
	<td rowspan="<?=$v2[rowspan]?>"><?=number_format($v2[shipprice])?></td>
	<? } ?>
	<td><?=$r_step[$v3[itemstep]]?></td>
</tr>
<?
			$idx3++;
		}
		$idx2++;
	}
	$idx++;
}
?>
</table>