<?

/*
* @date : 20180829
* @author : kdk
* @brief : 미오디오용 필름 스캔이미지 리스트.
* @desc : 
*/

include "../_header.php";
include "../lib/class.page.php";

chkMember();

$m_print = new M_print();
//$r_y = array("일","월","화","수","목","금","토");

$data = $m_print->getEtcUploadFileList($cid, $sess[mid]);
//debug($data);

$loop = array();
foreach ($data as $value) {
    //debug($r_y[date('w', strtotime($value[regist_date]))]);
    
    //썸네일 이미지 경로 생성.
    //$value[thumb_path] = str_replace($value[upload_file_name],"thumb/thumb_$value[upload_file_name]",$value[server_path]);

    $loop[$value[regist_dt]][] = $value;
}
//debug($loop);
//exit;

$tpl->assign('loop',$loop);
$tpl->print_('tpl');
?>