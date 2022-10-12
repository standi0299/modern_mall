<style>td {mso-number-format:"@"}</style>

<table border="1" bordercolor="#cccccc" style="border-collapse:collapse;font:11pt '굴림'">
<tr>
	<th><?=_("쿠폰코드")?></th>
	<th><?=_("쿠폰명/발행코드")?></th>
	<th><?=_("발행일")?></th>
	<th><?=_("발행회원")?></th>
	<th><?=_("사용일")?></th>
	<th><?=_("사용여부")?></th>
</tr>
<? while ($data = $db->fetch($res)) { 
	$coupon_issue_code = ($data[coupon_issue_code_history]) ? str_replace("|","<br>",$data[coupon_issue_code_history]) : $data[coupon_issue_code];?>
<tr>
	<td><?=$data[coupon_code]?></td>
	<td>
		<?=$data[coupon_name]?>
		
		<? if ($data[coupon_type] == "coupon_money") { ?>
		- 잔액 : <?=number_format($data[coupon_able_money])?>
		<? } ?>
		
		<div><?=$coupon_issue_code?></div>
	</td>
	<td><?=substr($data[coupon_setdt],2,14)?></td>
	<td><?=$data[mid]?></td>
	<td><?=substr($data[coupon_usedt],2,14)?></td>
	<td><?=($data[coupon_use])?_("사용"):_("미사용")?></td>
</tr>
<? } ?>
</table>