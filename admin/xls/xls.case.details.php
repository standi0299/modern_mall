<?
include "../_pheader.php";
include "../../lib/class.page.php";
/*
 GET을 통해 얻는 정보
 mode : mysql 테이블의 mod 값을 결정짓는 역할을 한다. 엑셀의 종류
 query : xls 파일을 작성할 기초데이터의 쿼리문 이다. 전달방식은 기본쿼리문의 base64_encode 를 준수한다.
 */

# 기초환경 설정 ----------------------------------------------------------------------------------------------------------------

#엑셀의 환경값을 저장할 테이블을 선언한다.
$exm_xls_case = "exm_excel";

# 엑셀의 조건 필드 (샵아이디)
$cid = $cid;
if (!$cid)
    print_xls_err(_("샵아이디가 선언되지 않음"));

# 엑셀의 조건 필드 (엑셀의 종류)
$mode = "details";
$_GET[mode] = "details";
if (!$mode)
    print_xls_err(_("엑셀모드가 선언되지 않음"));

# 엑셀의 컬럼을 저장한 파일의 경로 (파일 내용은 mysql column명을 키로 갖고 명칭을 value로 갖는 형식을 취해야 한다.)
$column_file = "column/xls.column." . $_GET[mode] . ".php";
if (!is_file($column_file))
    print_xls_err(_("컬럼파일")." : '<b class='red'>$column_file</b>' "._("없음"));

# 엑셀 컬럼파일 참조
include $column_file;

if (!$r_column) {
    print_xls_err(_("컬럼정보가 없습니다."));
}

# 에러 출력용 함수
function print_xls_err($str) {
    echo "<div style='padding:30px;text-align:center;'>ERROR! " . $str . "</div>";
    exit ;
}

# /기초환경 설정 ---------------------------------------------------------------------------------------------------------------

$query = "select * from exm_xls_case where cid = '$cid' and mode = '$mode' and name = '$sess[mid]' order by `default`";
$res = $db -> query($query);

$loop = array();
while ($data = $db -> fetch($res)) {
    $loop[$data[no]] = $data;
    if (($data['default'] == -1 && !$_GET[no]) || $_GET[no] == $data[no]) {
        $_GET[no] = $data[no];
        $default = $data;
    }
}
$b_column = array();
if ($_GET[no]) {
    $b_column = array_notnull(explode("|@|", $default[columns]));
}
$base_column = array_diff(array_keys($r_column), $b_column);

$selected[no][$_GET[no]] = "selected";

$_GET[addquery_] = urldecode(base64_decode($_GET[addquery]));
$_GET[addquery_] = explode("&", $_GET[addquery_]);
$addquery = array();

foreach ($_GET[addquery_] as $k => $v) {
    $v = array_map("trim", explode("=", $v));
    if (!$v[0] || !$v[1])
        continue;
    $addquery[$v[0]] = $v[1];
}

$member_query = "select * from exm_member where cid = '$cid' and mid = '$sess[mid]'";
$list = $db->fetch($member_query);
//debug($list);

$query = "select * from exm_xls_case where cid = '$cid' and mode = '$mode' and name = '$sess[mid]' order by `default`";
$data = $db -> fetch($query);

//debug($data);

$columns = explode("|@|", $data[columns]);
$columns2 = array_keys(array_keys($r_column));

$list_query = "select * from tb_studioupload_bluepod_join_db
      where cid = '$cid' and mid = '$sess[mid]' and input_flag = '1' and (paystep>=2 and itemstep>=2 and itemstep<=5) or (paystep>=2 and itemstep>=91)";

if ($_GET[orddt][0]) $list_query = $list_query." and"." orddt > {$_GET[orddt][0]}";
if ($_GET[orddt][1]) $list_query = $list_query." and"." orddt < adddate({$_GET[orddt][1]},interval 1 day)+0";

if (!$_GET[orderby])
{
    $list_query = $list_query."order by orddt";
}
else {
    $list_query = $list_query."order by".$_GET[orderby];
}

//$list_query_url =  base64_encode(urlencode($list_query));
//$query = urldecode(base64_decode($list_query_url));


include "form/form." . $_GET[mode] . ".php";
?>