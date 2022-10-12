<?
include "../lib.php";
$m_config = new M_config();
if($_POST[naver_site_verification]){
   $m_config -> setConfigInfo($cid, "naver_site_verification", $_POST[naver_site_verification], "naver_meta_tag");
}
switch ($_POST[mode]) {
   
   case "seo" :
			MakeSEOTagFile($cid, "main", $_POST[main_title], $_POST[main_author], $_POST[main_description], $_POST[main_keyword]);			
			MakeSEOTagFile($cid, "cate", $_POST[cate_title], $_POST[cate_author], $_POST[cate_description], $_POST[cate_keyword]);			
         MakeSEOTagFile($cid, "item", $_POST[item_title], $_POST[item_author], $_POST[item_description], $_POST[item_keyword]);		         
			
   break;
   
   case "delBank" :
   
   break;
}

msgNlocationReplace(_("저장 완료되었습니다."), $_SERVER['HTTP_REFERER']);
?>