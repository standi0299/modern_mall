<?
include dirname(__FILE__)."/lib/library.php";


if($cfg[main_page] != ""){
   $url = $cfg[main_page]; //특정 메인페이지 경로
}else{
   $url = ($cfg[apply_intro])? "main/intro.php" : "main/index.php";
}
if ($_SERVER["QUERY_STRING"]){
   $url = $url."?".$_SERVER["QUERY_STRING"];
}

//관리자 페이지를 개발하기 때문에 관리자 화면으로 이동시킨다.
//사용자 페이지 작업을 하면 소스를 바꿔야함. / 17.04.12 / kjm
//$url = "/main/index.php";

header("location:".$url);
?>