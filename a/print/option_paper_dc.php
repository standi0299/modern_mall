<?
/*
* @date : 20180418
* @author : kdk
* @brief : 자동견적 지류 할인율 설정 셋팅
* @desc : 지류 할인율 설정
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

$mode = "paper_dc_update";
$title = "할인율설정";
$url = "";

/*
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
    
    //echo '<pre>';
    //if (move_uploaded_file($_FILES[excelFile][tmp_name],$uploaddir.$name)) { //move_uploaded_file($_FILES['excelFile']['tmp_name'], $uploadfile)
    //    echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
    //} else {
    //    print "파일 업로드 공격의 가능성이 있습니다!\n";
    //}

    //echo '자세한 디버깅 정보입니다:';
    //print_r($_FILES);
    //print_r($uploaddir.$name);
    //print "</pre>";
    
    $bExcelImport = true;
    $excelImportFileName = $uploaddir . $name;
}
*/

//지류 정보 조회.
$paper_data = GetPaper();
//debug($paper_data);

//가격 정보.
$paperPriceDataArr = GetPaperDcDataArr($paper_data);
//debug($paperPriceDataArr);

//테이블 가격 tag.
if ($bExcelImport) {
    //엑셀에서 가격정보 읽어오기
    $tablePriceContent = GetPaperDcDataXlsTag($excelImportFileName);
}
else {
    $tablePriceContent = GetPaperDcDataTag($paperPriceDataArr);
}
//debug($tablePriceContent);

//테이블 타이틀 tag.
$tableTitleContent = "
        <tr>
            <th colspan='2'>지류</th>
            <th rowspan='2'>할인율(100분율, %)</th>
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

<script>
$(".xls_div").hide();
</script>

<? include_once "../_pfooter.php"; ?>