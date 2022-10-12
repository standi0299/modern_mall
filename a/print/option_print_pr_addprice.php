<?
/*
* @date : 20190327
* @author : kdk
* @brief : 자동견적 옵션 가격을 셋팅
* @desc : 현수막 실사출력 추가 인쇄비
*/
?>
<?
include_once '../_pheader.php';
include_once '../../print/lib_const_print.php';
include_once '../../print/lib_util_print.php';
include "../../lib/class.excel.reader.php";
include 'lib_util_print_admin.php';

//set_time_limit(360);
//ini_set('memory_limit', '-1');
//테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk
$this_time = get_time();

$opt_mode = $_GET[opt_mode];
$opt_group = $_GET[opt_group];
$opt_prefix = $_GET[opt_prefix];
$opt_goods_kind = $_GET[opt_goods_kind];

$mode = "print_pr_addprice_update";
$title = "추가인쇄비 설정";

if (file_exists("../../data/print/goods_items/". $cid ."_". $opt_mode .".php"))
{
    include_once "../../data/print/goods_items/". $cid ."_". $opt_mode .".php";
    $file_data = json_decode(${"r_ipro_".$opt_mode}, 1);
}    
//debug($file_data);
?>

<? include '_option_print_pr_addprice_html.php'; ?>

<script>
$(".xls_div").hide();
</script>

<? include_once "../_pfooter.php"; ?>