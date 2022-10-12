<?
/*
* @date : 20190404
* @author : kdk
* @brief : 자동견적 지류 가격을 셋팅 (용지비,인쇄비,코팅비 1m2당 단가 입력하여 1mm당 단가로 계산)
* @desc : 현수막, 실사출력 지류
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

$mode = "paper_pr_price_1mm_update";
$title = "가격설정";
$url = "option_paper_pr_price_1mm_excel.php?filename=paper_pr_price_1mm";

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
$paper_data = GetPaper("and opt_prefix='EPR'");
//debug($paper_data);

//가격 정보.
$paperPriceDataArr = GetPaperPrPrice1mmDataArr($paper_data);
//debug($paperPriceDataArr);

//테이블 가격 tag.
if ($bExcelImport) {
    //엑셀에서 가격정보 읽어오기
    $tablePriceContent = GetPaperPrPrice1mmDataXlsTag($excelImportFileName);
}
else {
    $tablePriceContent = GetPaperPrPrice1mmDataTag($paperPriceDataArr);
}
//debug($tablePriceContent);

//테이블 타이틀 tag.
$tableTitleContent = "
        <tr>
            <th colspan='2'>지류</th>
            <th rowspan='2'>평량 (무게g)</th>
            <th rowspan='2'>두께 (mm)</th>
            <th rowspan='2'>용지비(1mm당 단가)</th>
            <th rowspan='2'>인쇄비(1mm당 단가)</th>            
            <th rowspan='2'>코팅비(1mm당 단가)</th>
        </tr>
        <tr>
            <th>분류</th>
            <th>용지명</th>
        </tr>";
        
//debug($paperPriceDataArr);
$paperJsonData = base64_encode(json_encode($paperPriceDataArr));
//debug($paperJsonData);
?>

<? include '_option_print_html.php'; ?>

<? include_once "../_pfooter.php"; ?>