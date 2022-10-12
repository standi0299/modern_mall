<?

$res = $db->query($query);
$loop = array();
while ($data = $db->fetch($res)){
	$data[ID]				= $data[ID];
	$data[cid]				= $data[cid];
	$data[bid]				= $data[bid];
	$data[group_code]		= $data[group_code];
	$data[group_sub_code]	= $data[group_sub_code];
	$data[data_kor]			= $data[data_kor];
	$data[data_eng]			= $data[data_eng];
	$data[regist_flag]		= $data[regist_flag];
	$data[regist_date]		= $data[regist_date];

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