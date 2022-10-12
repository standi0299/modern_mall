<?

$res = $db->query($query);
$loop = array();
while ($data = $db->fetch($res)){
	$data[orddt]   = $data[orddt];
	$data[payno]   = $data[payno];
    
    if($data[modify_goodsnm]) $data[goodsnm] = $data[modify_goodsnm].'_'.$data[goodsnm];
    else $data[goodsnm]= $data[goodsnm];
    
	$data[mid]         = $data[mid];
	$data[class_name]  = $data[class_name];
	$data[child_name]  = $data[child_name];
	$data[ea]          = $data[ea];

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