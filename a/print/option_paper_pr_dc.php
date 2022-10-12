<?
/*
* @date : 20190320
* @author : kdk
* @brief : 자동견적 지류 할인율 설정 셋팅
* @desc : 현수막, 실사출력 지류 할인율 설정
*/
?>
<?
include_once '../_pheader.php';
include_once '../../print/lib_const_print.php';
include_once '../../print/lib_util_print.php';
include_once "../../lib/class.excel.reader.php";
include 'lib_util_print_admin.php';

set_time_limit(360);
ini_set('memory_limit', '-1');
//테스트용 임시 메모리 제한 풀기 2014.07.10 by kdk
$this_time = get_time();

$opt_mode = $_GET[opt_mode];

$mode = "paper_pr_dc_update";
$title = "할인율설정";
$url = "option_paper_pr_dc_excel.php?filename=paper_pr_dc";

//자동견적 가격 엑셀 파일 처리
$bExcelImport = false;
if ($_FILES['excelFile']['name']) {
    $uploaddir = '../../data/excel_temp/';

    if (!is_dir($uploaddir)) {
        mkdir($uploaddir, 0707);
    } else
        @chmod($uploaddir, 0707);
    //$uploadfile = $uploaddir.basename($_FILES['excelFile']['name']);

    $ext = explode(".", $_FILES['excelFile']['name']);

    $name = time() . "." . $ext[1];
    //move_uploaded_file($_FILES[img][tmp_name][$k],$uploaddir.$name);

    move_uploaded_file($_FILES[excelFile][tmp_name], $uploaddir . $name);

    $bExcelImport = true;
    $excelImportFileName = $uploaddir . $name;
}

//지류 정보 조회.
//$paper_data = GetPaper("and opt_prefix='EPR'");
//debug($paper_data);

//가격 정보.
$paperPriceDataArr = GetPaperPrDcDataArr();
//debug($paperPriceDataArr);

//테이블 가격 tag.
if ($bExcelImport) {
    //엑셀에서 가격정보 읽어오기
    $tablePriceContent = GetCtpOpsetDataXlsTag($excelImportFileName);
}
else {
    $tablePriceContent = GetCtpOpsetDataTag($paperPriceDataArr);
}
//debug($tablePriceContent);

//테이블 타이틀 tag.
$tableTitleContent = "
        <tr>
            <th>수량</th>
            <th>할인율(100분율, %)</th>
        </tr>";
        
//debug($paperPriceDataArr);
$paperJsonData = base64_encode(json_encode($paperPriceDataArr));
//debug($paperJsonData);
?>

<? include '_option_print_html.php'; ?>

<? include_once "../_pfooter.php"; ?>