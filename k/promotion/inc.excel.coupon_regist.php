<style>td {mso-number-format:"@"}</style>

<table border="1" bordercolor="#cccccc" style="border-collapse:collapse;font:11pt 굴림">
<tr>
	<th>쿠폰코드</th>
	<th>쿠폰명</th>
	<th>발행코드</th>
	<th>생성일</th>
	<th>등록일</th>
	<th>등록여부</th>
	<th>등록회원</th>
</tr>
<? while ($data = $db->fetch($res)){ ?>
<tr>
	<td><?=$data[coupon_code]?></td>
	<td><?=$data[coupon_name]?></td>
	<td><?=$data[coupon_issue_code]?></td>
	<td><?=substr($data[coupon_regdt],2,14)?></td>
	<td><?=substr($data[coupon_issuedt],2,14)?></td>
	<td><?=($data[coupon_issue_yn])?"등록":"미등록"?></td>
	<td><?=$data[mid]?></td>
</tr>
<? } ?>
</table>