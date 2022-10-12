<?
include "../_header.php";
//검색 리스트.

$goods = new Goods();

if($_GET[ht]) {
	//$search_field = "hash_tag";
	$search_field = "search_word";
	$search_word = $_GET[ht];
}	
else {
	//$search_field = "goodsnm,search_word,hash_tag";
	$search_field = "goodsnm,search_word";
	$search_word = $_GET[sw];
}

if($_GET[ht] || $_GET[sw]) {
	//$search_field, $search_word
	$goods->getSearch($search_field, $search_word);
}

//debug($goods->listPage);
//debug($goods->listData);

$selected[page_num][$_GET[page_num]] = "selected";
//debug($goods->listPage);

//추천 검색어
$cfg[search_word] = getCfg('search_word');
if($cfg[search_word]) {
	$word = array_notnull(explode(",",$cfg[search_word]));
	//debug($word);
}

$tpl->define('tpl', 'goods/search.htm');
$tpl->assign("pg",$goods->listPage);
$tpl->assign("loop",$goods->listData);
$tpl->print_('tpl');
?>