<? 
/*
 * tb_editor_ext_data 편집기의 추가정보 저장 2014.05.28 by kdk
 */
$login_offset = true; //20141030 / minks / 사이트접근권한이 회원제일 경우 로그인페이지로 이동해서 추가
include "../_header.php";

$storage_id = $_POST[storage_id];
$g_storage_id = $_POST[g_storage_id];
$editor_return_json = $_POST[editor_return_json];
$preview_link = $_POST[preview_link];
$complete_result = $_POST[complete_result];
$orderinfo_result = $_POST[orderinfo_result];

if($storage_id) {
	
	$data = $db->fetch("select * from tb_editor_ext_data where storage_id = '$storage_id'",1);

	if ($data){
		//update
		$query = "
		update tb_editor_ext_data 
			set 
			editor_return_json	= '$editor_return_json',
			preview_link		= '$preview_link',
			g_storage_id		= '$g_storage_id',
			complete_result		= '$complete_result',
			orderinfo_result	= '$orderinfo_result' 
		where storage_id = '$storage_id'
		";
		$db->query($query);	
	}
	else {
		//insert
		$query = "
		insert into tb_editor_ext_data set
			storage_id			= '$storage_id',
			paper_type			= '',
			book_spine			= '',
			editor_return_json	= '$editor_return_json',
			preview_link		= '$preview_link',
			g_storage_id		= '$g_storage_id',
			complete_result		= '$complete_result',
			orderinfo_result	= '$orderinfo_result'
		";
		$db->query($query);
	}
	
	//20141204 / minks / 장바구니에서 수정시 편집데이터 값 수정
	$data2 = $db->fetch("select * from exm_cart where storageid='$storage_id'",1);
	
	if($data2) {
		$query2 = "update exm_cart set vdp_edit_data	= '$editor_return_json' where storageid='$storage_id'";
		$db->fetch($query2);
	}
	
	//20141204 / minks / 관리자페이지에서 수정시 편집데이터 값 수정
	$data3 = $db->fetch("select * from exm_ord_item where storageid='$storage_id'",1);
	
	if($data3) {
		$query3 = "update exm_ord_item set vdp_edit_data	= '$editor_return_json' where storageid='$storage_id'";
		$db->fetch($query3);
	}
	
	echo "OK";
}
else {
	echo "FAIL";
}

?>