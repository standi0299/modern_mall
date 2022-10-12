<?

$res = $db->query($query);
$loop = array();
while ($data = $db->fetch($res)){
	$data[addressno]			= $data[addressno];
	$data[cid]					= $data[cid];
	$data[bid]					= $data[bid];
	$data[addressnm]			= $data[addressnm];
	$data[receiver_name]		= $data[receiver_name];
	$data[receiver_phone]		= $data[receiver_phone];
	$data[receiver_mobile]		= $data[receiver_mobile];
	$data[receiver_zipcode]		= $data[receiver_zipcode];
	$data[receiver_addr]		= $data[receiver_addr];
	$data[receiver_addr_sub]	= $data[receiver_addr_sub];
	$data[use_check]			= $data[use_check];

	$loop[] = $data;
}

?>

<meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=utf-8"/>
<table>
<tr>
<? foreach ($columns as $column_code){ ?>
	<th style="border:thin solid #666666;white-space:nowrap"><?=$r_column[$column_code]?></th>
<? } ?>
</tr>
<? foreach ($loop as $v){ ?>
<tr>
	<? foreach ($columns as $column_code){ ?>
	<td style="border:thin solid #666666;white-space:nowrap;mso-number-format:'\@';"><?=$v[$column_code]?></td>
	<? } ?>
</tr>
<? } ?>
</table>

<style>
table {border-collapse:collapse;font:9pt 돋움;}
th {border:thin solid #666666;white-space:nowrap}
td {border:thin solid #666666;white-space:nowrap}
</style>