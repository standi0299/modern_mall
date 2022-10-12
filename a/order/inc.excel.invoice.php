<? 

### 출고처 추출
$r_rid = get_release(); 

?>

<style>
tr th {font-weight:bold;text-align:center;}
td {mso-number-format:"@"}
</style>

<table border="1" bordercolor="#cccccc" style="border-collapse:collapse;">
<tr>
	<th><?=_("결제번호")?></th>
	<th><?=_("주문번호")?></th>
	<th><?=_("아이템번호")?></th>
	<th><?=_("결제방식")?></th>
	<th><?=_("주문일")?></th>
	<th><?=_("입금/승인일")?></th>
	<th><?=_("주문자명")?></th>
	<th><?=_("주문상품")?></th>
	<th><?=_("수량")?></th>
	<th><?=_("판매가")?></th>
	<th><?=_("출고처")?></th>
	<th><?=_("주문상태")?></th>
	<th><?=_("받는자명")?></th>
	<th><?=_("연락처")?></th>
	<th><?=_("주소")?></th>
	<th><?=_("배송방식")?></th>
</tr>
<? while ($data = $db->fetch($res)) {
	if ($data[addopt]) $data[addopt] = unserialize($data[addopt]);
	if ($data[printopt]) $data[printopt] = unserialize($data[printopt]);
	
	$goodsinfo = "<div>".$data[goodsnm]." (".number_format($data[goods_price]*$data[ea]).")</div>";
	if ($data[opt]) {
		$goodsinfo .= "<div style=\"color:blue;\">".$data[opt]." (+".number_format($data[aprice]*$data[ea]).")</div>";
	}
	if ($data[addopt]) {
		foreach ($data[addopt] as $k=>$v) {
			$goodsinfo .= "<div style=\"color:green;\">".$v[addopt_bundle_name].":".$v[addoptnm]." (+".number_format($v[addopt_aprice]*$data[ea]).")</div>";
		}
	}
	if ($data[printopt]) {
		foreach ($data[printopt] as $k=>$v) {
			$goodsinfo .= "<div style=\"color:red;\">".$v[printoptnm]." (+".number_format($v[print_price]*$v[ea]).")</div>";
		}
	}
	if ($data[addpage_aprice]) {
		$goodsinfo .= "<div>"._("추가페이지").":".$data[addpage]." (+".number_format($data[addpage_aprice]*$data[ea]).")</div>";
	}
	if ($data[dc_member]) {
		$goodsinfo .= "<div>"._("회원할인")." (-".number_format($data[dc_member]*$data[ea]).")</div>";
	}
	if ($data[dc_coupon]) {
		$goodsinfo .= "<div>"._("쿠폰할인")." (-".number_format($data[dc_coupon]).")</div>";
	} ?>
<tr>
	<td><?=$data[payno]?></td>
	<td><?=$data[ordno]?></td>
	<td><?=$data[ordseq]?></td>
	<td><?=$r_paymethod[$data[paymethod]]?></td>
	<td><?=$data[orddt]?></td>
	<td>
		<? if ($data[paymethod] == "t") { ?>
			<?=$data[confirmdt]?>
		<? } else { ?>
			<?=$data[paydt]?>
		<? } ?>
	</td>
	<td>
		<?=$data[orderer_name]?> 
		
		<? if ($data[mid]) { ?>
			(<?=$data[mid]?>)
		<? } else { ?>
			(<?=_("비회원")?>)
		<? } ?>
	</td>
	<td>
		<div><?=_("상품번호")?> : <?=$data[goodsno]?> / <?=_("공급사")?> : <?=$r_rid[$data[item_rid]]?></div>
		<?=$goodsinfo?>
	</td>
	<td><?=$data[ea]?></td>
	<td><?=number_format($data[payprice])?></td>
	<td><?=$r_rid[$data[rid]]?></td>
	<td><?=$r_step[$data[itemstep]]?></td>
	<td><?=$data[receiver_name]?></td>
	<td>
		<?=$data[receiver_mobile]?> 
		
		<? if ($data[receiver_phone]) { ?>
			/ <?=$data[receiver_phone]?>
		<? } ?>
			
	</td>
	<td>
		<div>[<?=$data[receiver_zipcode]?>]</div>
		<?=$data[receiver_addr]?> <?=$data[receiver_addr_sub]?>
	</td>
	<td>
		<? if ($data[order_shiptype]) { ?>
			<?=$r_shiptype[$data[order_shiptype]]?>
		<? } else { ?>
			<?=_("일반배송")?>
		<? } ?>
	</td>
</tr>
<? } ?>
</table>