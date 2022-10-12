<?

$res = $db->query($query);
$loop = array();

$r_state = array("0"=>_("편집중"),"1"=>_("편집완료"),"9"=>_("주문접수"));

while ($data = $db->fetch($res)){
	//debug($data);
	//$progress = editListPodsProgress($data);
	//debug($progress);

	if(!$data[mid]) {
    	$data[mid] = _("비회원");
	}
	else {
		//$data[mid] = $data[name] ."<br>(". $data[mid]. ")";
	}

	//편집상태
	$data[progress] = editListPodsProgress($data);
    //$data[progress] = "";
		
	//$data[state] = $r_state[$data[state]] ."<br>". $data[storageid];
    $data[state] = $r_state[$data[state]];

	if($data[_hide]) {
    	$data[_hide] = _("삭제")."<br>". $data[_hidelog];
   	} 
   	else {
    	$data[_hide] = _("보존");
   	}

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