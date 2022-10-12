<?

$login_offset = 1;
include "../_header.php";
	
	
	$SERVER_ADDR = $_SERVER[SERVER_ADDR];
	if (strpos($_SERVER[SERVER_ADDR], "192.168.1.") > -1) 
		$SERVER_ADDR = "210.96.184.229";
	
	$post_data[confmKey] = $r_zipcode_api_key[$SERVER_ADDR];
	$post_data[countPerPage] = "10";
	
	
	if ($_POST[mode] == "zipcode_key" && $_POST[mobile_type] == "Y")
	{
		echo $post_data[confmKey];
		exit;
	}
	
$data = array(
'zipcode' => '153-768',
'diso'    => _("서울"),
'gungu'   => _("금천구"),
'dong'    => _("가산동"),
'bunji'   => _("IT캐슬"),
'seq'     => '2514',
);
//$loop[] = $data;
//debug($loop);

$tpl->assign('data',$post_data);
$tpl->print_('tpl');
?>