<?

/*
* @date : 20180321
* @author : kdk
* @brief : SNS회원가입항목설정 수정.
* @request : 
* @desc : 이름 사용자 입력.
* @todo :
*/

$login_offset = true;
include "../_header.php";


$data = getCfg('personal_data_collect_use_choice');

$tpl -> assign('data', $data);
$tpl -> print_('tpl');
?>