<?
//일반,포토북/앨범 상품 리스트.

$goods = new Goods();
$goods->getList();
$goods->getMain();

$category = $goods->category;
//debug($category);
//debug($goods->listPage);
//debug($goods->listData);

//Hot 상품 조회.
$hotitem = $goods->getHotItem($_GET[catno]);
//debug($hotitem);

//메인 블럭 컨텐츠 정보 조회.
$mcdata = $goods->mainBlockContentData;
//debug($mcdata);

//메인 블럭 정보 조회.
$data = $goods->mainBlockData;
//debug($data);

if($data) {
   foreach ($data as $key => $val) {
      if($val[ID] == 13){
         //debug($val[block_code]);
         //debug($val[order_by]);
         //debug(strlen($val[order_by]));
           
         if (strlen($val[order_by]) > 1) { // order_by가 9이상이면...
            if ($val[state] == "0")
               $define_url = "main/".$val[block_code].".htm"; //$define_url = "main/main_block_".$val[order_by].".htm";
            else
               $define_url = "main/blank.htm";
   
            $tpl->define("main_block_".$val[order_by], $define_url);
            //$blockDataArr["main_block_".$val[order_by]] = $val;
            $blockDataArr[$val[block_code]] = $val;
         } else {
            if ($val[state] == "0")
               $define_url = "main/".$val[block_code].".htm"; //$define_url = "main/main_block_0".$val[order_by].".htm";
            else
               $define_url = "main/blank.htm";
   
            $tpl->define("main_block_0".$val[order_by], $define_url);
            //$blockDataArr["main_block_0".$val[order_by]] = $val;
            $blockDataArr[$val[block_code]] = $val;
         }
      }
   }
}
//debug($blockDataArr);
$selected[page_num][$_GET[page_num]] = "selected";
//debug($goods->listPage);

$tpl->define('tpl', 'goods/list_main.htm');
$tpl->assign("catnm",$category[catnm]);
$tpl->assign("pg",$goods->listPage);
$tpl->assign("loop",$goods->listData);

$tpl->assign("sub_cate",$all_sub_catetory);
$tpl->print_('tpl');
?>