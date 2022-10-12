<?
include "../_header.php";
include "../_left_menu.php";
$m_goods = new M_goods();

$r_bid = getBusiness();

$fkey = md5(crypt(''));
$ls = array();
$r_bcatno = array();
$r_rid = get_release();
$r_brand = get_brand();
$r_opt = array();
$r_printopt = array();
$r_addtion_goods_kind = array("recomand", "relation");

//20170308 / minks / 아이콘 추가
$dir = "../../data/icon/$cid/";
$file = array();
$r_icon = array();

if (is_dir($dir)) {
	$dir_handle = opendir($dir);
	
	while (($file = readdir($dir_handle)) != false) {
		if ($file != "." && $file != ".." && is_file($dir."/".$file)) {
			$r_icon[] = $file;
		}
	}
	
	closedir($dir_handle);
}
sort($r_icon);

if ($_GET[goodsno]){
   $query = "
   select 
      a.*,
      a.price center_price,
      b.desc mall_desc,
      b.price mall_price,
      b.strprice,
      b.reserve,
      b.clistimg,
      b.clistimg_w,
      b.cimg,
      b.nodp,
      b.self_deliv,
      b.self_dtype,
      b.self_dprice,
      b.mall_pageprice,
      b.mall_pagereserve,
      b.b2b_goodsno,
      b.goodsnm_deco,
      b.goodsnm_deco_out,
      b.csummary,
      b.csearch_word,
      b.cmatch_goodsnm,
      b.cetc1,
      b.cetc2,
      b.cetc3,
      b.mall_cprice,
      b.exp,
      b.cresolution,
      b.cgoods_size,
      b.icon_filename,
      b.goods_desc mall_goods_desc
   from
      exm_goods a
      inner join exm_goods_cid b on a.goodsno = b.goodsno
   where
      cid = '$cid'
      and a.goodsno='$_GET[goodsno]'
   ";

   $data = $db->fetch($query);
   if (!$data){
      msg(_("상품데이터가 존재하지 않습니다."),-1);
      exit;
   }

   $fkey = $data[goodsno];
   $ls = ls("../../data/goods/l/$fkey/");

   $query = "select * from exm_goods_bid where cid = '$cid' and goodsno = '$data[goodsno]'";
   $res = $db->query($query);
   while ($tmp = $db->fetch($res)){
      $checked[bid][$tmp[bid]] = "checked";
   }

   $query = "select *,aprice center_aprice from exm_goods_opt where goodsno = '$data[goodsno]' order by osort";
   $res = $db->query($query);
   while ($tmp = $db->fetch($res)){
      if(!$tmp[opt_view]) {
         $r_opt[$tmp[optno]] = $tmp;
         
         //상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk
         list($opt_view) = $db->fetch("select view from tb_goods_opt_mall_view where cid = '$cid' and goodsno = '$data[goodsno]' and optno = '$tmp[optno]' ",1);
         if($opt_view)
            $r_opt[$tmp[optno]][opt_view] = $opt_view;
      }
   }

   $query = "select * from exm_goods_opt_price where cid = '$cid' and goodsno = '$data[goodsno]'";
   $res = $db->query($query);
   while ($tmp = $db->fetch($res)){
      $r_opt[$tmp[optno]][mall_aprice] = $tmp[aprice];
      $r_opt[$tmp[optno]][mall_areserve] = $tmp[areserve];
      $r_opt[$tmp[optno]][mall_opt_cprice] = $tmp[mall_opt_cprice];
      $r_opt[$tmp[optno]][b2b_optno] = $tmp[b2b_optno];
   }
   
   $query = "select * from exm_goods_printopt where goodsno = '$data[goodsno]' order by osort";
	$res = $db->query($query);
	while ($tmp = $db->fetch($res)){
		$r_printopt[$tmp[printoptnm]] = $tmp;
	}

	$query = "select * from exm_goods_printopt_price where cid = '$cid' and goodsno = '$data[goodsno]'";
	$res = $db->query($query);
	while ($tmp = $db->fetch($res)){
		$r_printopt[$tmp[printoptnm]][mall_print_price] = $tmp[print_price];
		$r_printopt[$tmp[printoptnm]][mall_print_reserve] = $tmp[print_reserve];
	}

   $query = "select * from exm_goods_addopt_bundle where goodsno = '$_GET[goodsno]' order by addopt_bundle_sort";
   $res = $db->query($query);
   $r_addopt_bundle = array();
   while ($tmp = $db->fetch($res)) {
      if(!$tmp[addopt_bundle_view]) {
         $r_addopt_bundle[$tmp[addopt_bundle_no]] = $tmp;
         
         //상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk
         list($addopt_bundle_view) = $db->fetch("select view from tb_goods_addopt_bundle_mall_view where cid = '$cid' and goodsno = '$data[goodsno]' and addopt_bundle_no = '$tmp[addopt_bundle_no]' ",1);
         if($addopt_bundle_view)
            $r_addopt_bundle[$tmp[addopt_bundle_no]][addopt_bundle_view] = $addopt_bundle_view;
         
         $query = "select * from exm_goods_addopt where addopt_bundle_no = '$tmp[addopt_bundle_no]' order by addopt_sort";
         $res2 = $db->query($query);
         while ($tmp2 = $db->fetch($res2)){

            if(!$tmp2[addopt_view]) {
               $r_addopt_bundle[$tmp[addopt_bundle_no]][addopt][$tmp2[addoptno]] = $tmp2;
               
               //상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk
               list($addopt_view) = $db->fetch("select view from tb_goods_addopt_mall_view where cid = '$cid' and goodsno = '$data[goodsno]' and addoptno = '$tmp2[addoptno]' ",1);
               if($addopt_view)
                  $r_addopt_bundle[$tmp[addopt_bundle_no]][addopt][$tmp2[addoptno]][addopt_view] = $addopt_view;

               $query = "select * from exm_goods_addopt_price where cid = '$cid' and goodsno = '$_GET[goodsno]' and addoptno = '$tmp2[addoptno]'";
               $res3 = $db->query($query);
               while ($tmp3 = $db->fetch($res3)){
                  $r_addopt_bundle[$tmp[addopt_bundle_no]][addopt][$tmp3[addoptno]][mall_addopt_aprice] = $tmp3[addopt_aprice];
                  $r_addopt_bundle[$tmp[addopt_bundle_no]][addopt][$tmp3[addoptno]][mall_addopt_areserve] = $tmp3[addopt_areserve];
                  $r_addopt_bundle[$tmp[addopt_bundle_no]][addopt][$tmp3[addoptno]][mall_addopt_cprice] = $tmp3[mall_addopt_cprice];
               }
            }
         }
      }
   }

   if ($data[icon_filename]) {
   	  $data[icon_filename] = explode("||", $data[icon_filename]);
   	  
   	  foreach ($data[icon_filename] as $k => $v) {
   	  	 $checked[icon_filename][$v] = "checked";
	  }
   }

   $data[img] = explode("||",$data[img]);

    //패키지 상품 정보  / 2017.12.11 / kdk
    $addWhere = "where cid='$cid' and addtion_key_kind='P' and addtion_key='$data[goodsno]' and addtion_goods_kind='package'";
    $data2 = $m_goods->getAddtionGoodsItem($cid, $addWhere);
    if ($data2) $r_addtion_goods_data_package = "Y";
   
   foreach ($_r_mdn_goodslist_extra_kind as $_r_k=>$_r_v) {
   	  if (in_array($_r_k, $r_addtion_goods_kind)) {
   	  	  $addWhere = "where cid='$cid' and addtion_key_kind='I' and addtion_key='$data[goodsno]' and addtion_goods_kind='$_r_k'";
   	  	  $data2 = $m_goods->getAddtionGoodsItem($cid, $addWhere);
   	  	  if (!$data2[regist_flag]) $data2[regist_flag] = "N";
   	  	  
   	   	  $r_addtion_goods_data[$_r_k] = $_r_v;
		  $r_addtion_goods_data[$_r_k][flag] = $data2[regist_flag];
		  $checked[$_r_k."_flag"][$data2[regist_flag]] = "checked";
	  }
   }

   if ($data[goods_desc]) {
   	  $data[goods_desc] = unserialize($data[goods_desc]);
   }

   if ($data[mall_goods_desc]) {
   	  $data[mall_goods_desc] = unserialize($data[mall_goods_desc]);
   }

	//자동견적옵션 기본등록 프리셋별 처리
	//debug($data);
	$extra_option = explode('|',$data[extra_option]); //항목 분리
	if(count($extra_option) > 0) {
		if($extra_option[1] == "100106") $extra_option[0] = "CARD";
		$extra_product = $extra_option[0];
		$extra_preset = $extra_option[1];
		$extra_price_type = $extra_option[2];
	}
   
}

$checked[podskind][$data[podskind]+0] = $checked[state][$data[state]+0] = $checked[usestock][$data[usestock]+0] = $checked[dtype][$data[dtype]+0] = "checked";
$checked[useopt][$data[useopt]+0] = "checked";
$checked[input_str][$data[input_str]+0] = $checked[input_file][$data[input_file]+0] = "checked";
$checked[nodp][$data[nodp]] = "checked";
$checked[self_dtype][$data[self_dtype]] = "checked";

//$catLinkNo = $m_goods->getCategoryLinkList($cid, $data[goodsno], "cat_index asc");

//순서 문제 처리 / 20190412 / kdk
$catLinkNo = array(0 => "", 1 => "", 2 => "", 3 => "");   
$catData = $m_goods->getCategoryLinkList($cid, $data[goodsno], "cat_index asc");

if ($catData) {
    foreach ($catData as $key => $value) {
        if ($value) {
            $index = $value[cat_index] - 1;
            $catLinkNo[$index] = $value;
        }
    }
}

//몰 카테고리 분류       20160328    chunter
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

//템플릿 카테고리 분류 / 20170921 / kdk
$tem_catLinkNo = $m_goods->getTemplateCategoryLinkNo($cid, $data[goodsno]);
$tem_cate_data = $m_goods->getTopTemplateCategoryList($cid);
$tem_ca_list = makeCategorySelectOptionTag($tem_cate_data);
//debug($tem_catLinkNo);
//debug($tem_cate_data);
//debug($tem_ca_list);

//현재 카테고리에 사용후기 공유 사용유무 추가 2017.04.28 by kdk
if($data[share_category_review_flag] == "1")
	$checked[share_category_review_flag] = "checked";
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("상품수정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("상품수정")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("상품수정")?></h4>
            </div>
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">
                  <input type="hidden" name="mode" value="goods_modify">
                  <input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>">
                  <input type="hidden" name="extra_flag" id="extra_flag" />

                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("상품 기본정보")?></div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("분류")?></label>
                     <div class="col-md-7 form-inline">   
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("1차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[0][catno])?></select>
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("2차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[1][catno])?></select>
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("3차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[2][catno])?></select>
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("4차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[3][catno])?></select>
                        <br>
                        <!--현재 카테고리에 사용후기 공유 사용유무 추가 2017.04.28 by kdk-->
      						<input type="checkbox" name="share_category_review_flag" value="1" class="absmiddle" <?=$checked[share_category_review_flag]?> /><span class="stxt absmiddle"><?=_("현재 카테고리에 후기 공유")?></span>                        
                           </div>
                           <label class="col-md-1 control-label"><?=_("템플릿 분류")?></label>
                           <div class="col-md-2 form-inline">
      						<select name="tem_catno" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("템플릿 분류 선택")?></option><?=conv_selected_option($tem_ca_list, $tem_catLinkNo[catno])?></select>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품종류")?></label>
                     <div class="col-md-10 form-inline">
                        <?=$r_goods_group_code[$data[goods_group_code]]?>
                        <!--패키지상품 정보-->
                        <div id="pack">
                        <table class="tb1">
                        <tr>
                            <th><?=_("패키지상품 선택")?></th>
                            <td>
                                <section class="srel">
                                    <div class="compare_wrap">
                                        <section class="compare_left">
                                            <h3><?=_("등록된 전체상품 목록")?></h3>
                                            <span class="srel_pad">
                                                <label for="package_catno" class="sound_only"><?=_("상품분류")?></label>
                                                <select id="package_catno" class="form-control" name="package_catno" style="width:35%;">
                                                    <option value="">+ <?=_("분류 선택")?></option>
                                                    <?=conv_selected_option($ca_list, "")?>
                                                </select>
                                                <label for="package_name" class="sound_only"><?=_("상품명")?></label>
                                                <input type="text" id="package_name" class="form-control" name="package_name" style="width:45%;">
                                                <button type="button" id="btn_package_search" class="btn btn-sm btn-success"><?=_("검색")?></button>
                                            </span>
                                            <div id="package" class="srel_list">
                                                <p><?=_("등록된 상품이 없습니다.")?></p>
                                            </div>
                                            
                                            <script>
                                            $j(function() {
                                                $j("#btn_package_search").click(function() {
                                                    var catno = $j("#package_catno").val();
                                                    var goodsnm = $j.trim($("#package_name").val());
                                                    var $kind = $j("#package");
                                        
                                                    if (catno == "" && goodsnm == "") {
                                                        $kind.html("<p>" + '<?=_("등록된 상품이 없습니다.")?>' + "</p>");
                                                        return false;
                                                    }
                                        
                                                    $j("#package").load(
                                                        "indb.php", { mode:"addtion_goods_list", kind: "package", catno: catno, goodsnm: goodsnm }
                                                    );
                                                });
                                                
                                                $(document).on("click", "#package .add_item", function() {
                                                    // 이미 등록된 상품인지 체크
                                                    var $li = $j(this).closest("li");
                                                    var goodsno = $li.find("input:hidden").val();
                                                    var reg_goodsno;
                                                    var dup = false;
                        
                                                    //패키지상품 갯수 제한.
                                                    if ($j("#reg_package li").size() >= 3) {
                                                        alert('<?=_("패키지 상품은 3개만 등록이 가능합니다.")?>');
                                                        return false;
                                                    }
                        
                                                    $j("#reg_package input[name='package_select_goodsno[]']").each(function() {
                                                        reg_goodsno = $j(this).val();
                                
                                                        if(goodsno == reg_goodsno) {
                                                            dup = true;
                                                            return false;
                                                        }
                                                    });
                                        
                                                    if (dup) {
                                                        alert('<?=_("이미 선택된 상품입니다.")?>');
                                                        return false;
                                                    }
                                        
                                                    var cont = "<li>" + $li.html().replace("add_item", "del_item").replace("추가", "삭제").replace("package_goodsno[]", "package_select_goodsno[]") + "</li>";
                                                    var count = $j("#reg_package li").size();
                                        
                                                    if (count > 0) $j("#reg_package li:last").after(cont);
                                                    else $j("#reg_package").html("<ul id=\"package_sortable\">" + cont + "</ul>");
                                
                                                    $j("#package_sortable").sortable();
                                
                                                    $li.remove();
                                                });
                                
                                                $(document).on("click", "#reg_package .del_item", function() {
                                                    if (!confirm('<?=_("상품을 삭제하시겠습니까?")?>')) return false;
                                                    $j(this).closest("li").remove();
                                
                                                    var count = $j("#reg_package li").size();
                                                    if (count < 1) $("#reg_package").html("<p>" + '<?=_("선택된 상품이 없습니다.")?>' + "</p>");
                                                });
                                            });
                                            </script>
                                        </section>
                                
                                        <section class="compare_right">
                                            <h3><?=_("선택된 패키지상품 목록")?></h3>
                                            <span class="srel_pad"></span>
                                            <div id="reg_package" class="srel_sel">
                                                <p><?=_("선택된 상품이 없습니다.")?></p>
                                            </div>
                                        </section>
                                    </div>
                                </section>
                            </td>
                        </tr>
                        <tr>
                            <th><?=_("안내")?></th>
                            <td>
                            <span class="notice">[<?=_("설명")?>]</span> <?=_("선택된 상품 목록의 순서를 변경하시려면 드래그해주시면 됩니다.")?>
                            </td>
                        </tr>
                        </table>    
                        </div>
                        <!--패키지상품 정보-->
                        <!--인쇄 견적 정보-->
                        <div id="extra">
                        <table class="tb1">
                        <tr>
                        	<th><?=_("상품종류선택")?></th>
                        	<td><?=$r_est_product[$extra_product]?></td>
                        </tr>
                        <tr>
                        	<th><?=_("프리셋스타일선택")?></th>
                        	<td><?=$r_est_preset[$extra_product][$extra_preset]?></td>
                        </tr>
                        <tr>
                        	<th><?=_("인쇄견적 옵션설정")?></th>
                        	<td>
                        		<button type="submit" class="btn btn-xs btn-danger" onclick="$j('#extra_flag').val('Y');"><?=_("설정하기")?></button>
                        		<?if($data[extra_auto_pay_flag]) {?>
                        			<span class="absmiddle"><?=_("자동견적결제")?></span>
                        		<?}?>&nbsp;
                        		<?if($data[extra_price_view_flag]) {?>
                        			<span class="absmiddle"><?=_("견적가격노출")?></span>
                        		<?}?>&nbsp;
                        		<?if($extra_price_type == "SIZE") {?>
                        			<span class="absmiddle"><?=_("면적당 계산하기")?></span>
                        		<?}?>
                        	</td>
                        </tr>
                        </table><br/>
                        </div>
                        <!--인쇄 견적 정보-->
                        <!--견적 정보-->
                        <div id="extra_print">
                        <table class="tb1">
                        <tr>
                            <th><?=_("견적종류선택")?></th>
                            <td><?=$r_est_print_product[$data[extra_option]]?></td>
                        </tr>
                        <? if ($cfg_center[center_cid] == "minterpro")  {?>
                        <tr>
                            <th><?=_("견적종류선택")?></th>
                            <td><?$r_inpro_print_goods_group[$data[print_goods_group]]?></td>
                        </tr>
                        <? } ?>
                        </table>
                        </div>
                        <!--견적 정보-->
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품코드")?></label>
                     <div class="col-md-2">
                        <b><?=$_GET[goodsno]?></b>
                        <? if ($data[podsno]){ ?>
                        <u class="hand" onclick="popupLayer('../../module/popup_calleditor.php?mode=edit&goodsno=<?=$data[goodsno]?>&productid=<?=$data[podsno]?>&optionid=1');">TEST</u> / 
                        <u class="hand" onclick="popup('goods.p.php?podsno=<?=$data[podsno]?>&optionid=1',630,275)"><?=_("제작대행")?></u>
                        <? } ?>
                     </div>

                     <label class="col-md-2 control-label"><?=_("제휴사 상품코드")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="b2b_goodsno" value="<?=$data[b2b_goodsno]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품명")?></label>
                     <div class="col-md-2">
                        <?=$data[goodsnm]?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("센터분류")?></label>
                     <div class="col-md-2">
                        <? foreach ($r_bcatno as $k=>$v) { ?>
                        <div class="gray desc"><?=$v?></div>
                        <? } ?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("제작사")?></label>
                     <div class="col-md-2">
                        <?=$r_rid[$data[rid]]?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("브랜드")?></label>
                     <div class="col-md-2">
                        <?=$r_brand[$data[brandno]]?>
                     </div>
                  </div>


                  <!-- ==============================상세설명 JSON형식 (센터)============================== -->                  
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("상품설명(센터)")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
			            <div class="table-responsive">
			               <table class="table table-bordered table-hover">
			                  <thead>
			                     <tr>
			                        <th width="200px"><?=_("필드명")?></th>
			                        <th><?=_("내용")?></th>                                                      
			                     </tr>
			                  </thead>
			                  <tbody>			                  
			                  <?
			                  if(is_array($data[goods_desc])) {
			                  	for ($i=0; $i < 5; $i++) { 
			                  ?>
			                     <tr>
			                     	<td name="goods_desc_name_<?=$i?>"><?=$data[goods_desc][$i][name]?></td>
                                 <td name="goods_desc_value_<?=$i?>"><?=$data[goods_desc][$i][value]?></td>
                              </tr>
			                  <?
                              	}
                              }
			                  ?>
			                  </tbody>
			               </table>
			            </div>
                     </div>
                  </div>

                  <!-- ==============================상세설명 JSON형식 (몰)============================== -->                  
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("상품설명(몰)")?>
                        <li style="display:inline"><u onclick="center_info_sync();" class="hand">[<?=_("상품설명(센터) 동기화")?>]</u></li>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
			            <div class="table-responsive">
			               <table class="table table-bordered table-hover">
			                  <thead>
			                     <tr>
			                        <th width="200px"><?=_("필드명")?></th>
			                        <th><?=_("내용")?></th>
			                     </tr>
			                  </thead>
                           <tbody>
                              <?
                              if(is_array($data[mall_goods_desc])) {
                                 for ($i=0; $i < 5; $i++) { 
                              ?>
                                 <tr>
                                    <td>
                                       <input type="text" class="form-control" name="goods_desc[<?=$i?>][name]" id="mall_goods_desc_name_<?=$i?>" value="<?=$data[mall_goods_desc][$i][name]?>"/>
                                    </td>
                                    <td>
                                       <input type="text" class="form-control" name="goods_desc[<?=$i?>][value]" id="mall_goods_desc_value_<?=$i?>" value="<?=$data[mall_goods_desc][$i][value]?>"/>
                                    </td>
                                 </tr>
                                 <?
                                 }
                              } else {
                                 for ($i=0; $i < 5; $i++) { 
                              ?>
                              <tr>
                                 <td>
                                    <input type="text" class="form-control" name="goods_desc[<?=$i?>][name]" id="mall_goods_desc_name_<?=$i?>" value=""/>
                                 </td>
                                 <td>
                                   <input type="text" class="form-control" name="goods_desc[<?=$i?>][value]" id="mall_goods_desc_value_<?=$i?>" value=""/>
                                 </td>
                              </tr>
                              <? } } ?>
                           </tbody>
			               </table>
                     </div>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("간략설명")?></label>
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[summary]?><BR>
                        <input type="text" class="form-control" name="csummary" value="<?=$data[csummary]?>"/>
                     </div>
                  </div>

                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("해시태그")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="hash_tag"  value="<?=$data[hash_tag]?>"/>
                     </div>
                  </div>-->

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품검색어/해시태그")?></label>         
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[search_word]?><BR>
                        <input type="text" class="form-control" name="csearch_word" value="<?=$data[csearch_word]?>"/>
                        <span class="stxt absmiddle">- <?=_("구분은 ,(쉼표) 입니다. 정확하게 구분하세요. 예)포토북,앨범")?></span>
                     </div>
                  </div>

                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("매칭상품명")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="match_goodsnm"  value="<?=$data[match_goodsnm]?>"/>
                     </div>
                  </div>-->

                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("매칭상품명")?></label>
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[match_goodsnm]?><BR>
                        <input type="text" class="form-control" name="match_goodsnm" value="<?=$data[match_goodsnm]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("권장해상도")?></label>         
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[resolution]?><BR>
                        <input type="text" class="form-control" name="resolution" value="<?=$data[resolution]?>"/>
                     </div>
                  </div>-->

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품사이즈")?></label>         
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[cgoods_size]?><BR>
                        <input type="text" class="form-control" name="cgoods_size" value="<?=$data[cgoods_size]?>"/>
                     </div>
                  </div>
                  
                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품특이사항1")?></label>         
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[etc1]?><BR>
                        <input type="text" class="form-control" name="cetc1" value="<?=$data[cetc1]?>"/>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품특이사항2")?></label>         
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[etc2]?><BR>
                        <input type="text" class="form-control" name="cetc2" value="<?=$data[cetc2]?>"/>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품특이사항3")?></label>         
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[etc3]?><BR>
                        <input type="text" class="form-control" name="cetc3" value="<?=$data[cetc3]?>"/>
                     </div>
                  </div>-->
                  
                  <? if ($r_icon) { ?>
                  	<div class="form-group">
                  		<label class="col-md-2 control-label"><?=_("아이콘")?></label>         
                  		<div class="col-md-10">
                  			<table class="iconTable">
							<tr>
							<? foreach ($r_icon as $k => $v){ ?>
								<td align="center" valign="top">
									<input type="checkbox" name="icon_filename[]" value="<?=$v?>" <?=$checked[icon_filename][$v]?> />
									<img src="../../data/icon/<?=$cid?>/<?=$v?>" />
								</td>
							<? } ?>
							</tr>
							</table>
                  		</div>
                  	</div>
				  <? } ?>
         
                  <div class="form-group" id="input_div">
                     <label class="col-md-2 control-label"><?=_("추가기능")?></label>         
                     <div class="col-md-10">
                        <?if ($data[input_str]==1){?>
                                                <?=_("문구입력 사용")?>
                        <?}?>
                        <?if ($data[input_file]==1){?>
                        <br><?=_("파일업로드 사용")?>
                        <?}?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("세트수량")?></label>
                     <div class="col-md-10">
                        <?=$data[goods_set_ea]?>
                     </div>
                  </div>
                  
                  <!-- ==============================판매 정보============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("판매 정보")?></div>
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("판매여부")?></label>
                     <div class="col-md-2">
                        <?=$r_goods_state[$data[state]]?>
                     </div>
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("재고연동")?></label>

                     <div class="col-md-10">
                        <?=($data[usestock])?_("재고가 있을 경우만 판매(출고완료시 자동감소)"):_("재고와 상관없이 무제한으로 판매(재고관리 안함)");?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("배송비")?></label>
                     <div class="col-md-10">
                     <? 
                     if ($data[shiptype]==1)
                        echo $r_shiptype[1];
                     else if ($data[shiptype]==2)
                        echo $r_shiptype[2];
                     else if ($data[shiptype]==4)    //착불 배송
                        echo $r_shiptype[4];
                     else {
                        $release = $db->fetch("select * from exm_release where rid = '$data[rid]'");
                        if ($release[shiptype]==1)
                           echo $r_shiptype[1];
                        else if ($release[shiptype]==4)   //착불 배송
                           echo $r_shiptype[4];
                        else {
                           echo $r_shiptype[0]; //    일반배송
                           echo number_format($release[shipprice]). _("원");
                        if ($release[shiptype]==3)
                           echo "(" .number_format($release[shipconditional]) . _("원 이상 주문시 무료").")";
                        }
                     } 
                     ?>
                     <div class="desc"><?=_("환경설정->운영설정>자체배송비설정 이 '사용함'으로 설정되어있을 경우 자체배송비 정책을 따릅니다.")?></div>
                     </div>
                  </div>
                  
                  <? if ($data[shiptype]==2){ ?>
                     
                     <div class="form-group">
                        <label class="col-md-2 control-label"><?=_("개별배송비")?></label>
                        <div class="col-md-10">
                           <?=number_format($data[shipprice])?>
                        </div>
                     </div>

                     <div class="form-group">
                        <label class="col-md-2 control-label"><?=_("배송비원가")?></label>
                        <div class="col-md-10">
                           <?=number_format($data[oshipprice])?>
                        </div>
                     </div>
                  <? } ?>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("진열불가")?></label>
                     <div class="col-md-2">
                        <label class="checkbox-inline">
                           <input type="checkbox" name="nodp" value="1" <?=$checked[nodp][1]?>> <span class="desc">(<?=_("체크시 진열되지 않습니다.")?>)</span>
                        </label>
                     </div>
                  </div>

                  
                  
                  <!-- ==============================가격/재고 관리============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("가격/재고 관리")?></div>
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("원가(센터공급가)")?></label>
                     <div class="col-md-2">
                        <?=number_format($data[sprice])?>
                     </div>
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("소비자가(권장소비자가)")?></label>
                     <div class="col-md-2">
                        <?=number_format($data[cprice])?>
                     </div>
            
                     <label class="col-md-2 control-label"><?=_("소비자가(몰소비자가)")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="mall_cprice" value="<?=$data[mall_cprice]?>" pt="_pt_numplus" required>
                     </div>
                  </div>
            
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("몰판매가")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="price" value="<?=$data[mall_price]?>" pt="_pt_numplus"required>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("가격대체문구")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="strprice" value="<?=$data[strprice]?>"> <span class="desc">(<?=_("입력시 구매가 불가능합니다.")?>) </span>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("페이지당 추가가격")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="mall_pageprice" value="<?=$data[mall_pageprice]?>" pt="_pt_numplus">
                        <?=_("적립금")?>
                        <input type="text" class="form-control" name="mall_pagereserve" value="<?=$data[mall_pagereserve]?>" pt="_pt_numplus">

                        <?=_("센터판매가")?> : <b><?=number_format($data[pageprice])?></b>, <?=_("공급가")?> : <b><?=number_format($data[spageprice])?></b>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("적립금")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="reserve" value="<?=$data[reserve]?>" pt="_pt_numplus">
                     </div>

                     <label class="col-md-2 control-label"><?=_("총재고")?></label>
                     <div class="col-md-2">
                        <?=number_format($data[totstock])?>
                     </div>
                  </div>


                  <!-- ==============================옵션별 가격/재고 관리============================== -->
                  
                  <div class="form-group option-pro">
                     <div class="col-md-12"> > > <?=_("옵션별 가격/재고 관리")?> [<?=_("명함상품은 반드시 1차옵션에 추가금액을 넣어주셔야 편집기와 주문금액이 연동됩니다. / 옵션추가 > center관리자 에서 등록하세요")?>]</span></div>
                  </div>
         
                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"><?=_("옵션유무")?></label>
                     <div class="col-md-10 form-inline">
                        <?=($data[useopt])?_("옵션사용"):_("옵션사용안함");?>
                     </div>
                  </div>
                  
                  
                  <? if ($data[useopt]){ ?>
                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"><?=_("1차옵션명")?></label>
                     <div class="col-md-10 form-inline">
                        <?=($data[optnm1])?$data[optnm1]:"-"?>
                     </div>
                  </div>
                  
                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"><?=_("2차옵션명")?></label>
                     <div class="col-md-10 form-inline">
                        <?=($data[optnm2])?$data[optnm2]:"-"?>
                     </div>
                  </div>
                  
                  <? } ?>
                  
                  <div class="form-group option-pro">
                     <div class="col-md-12">
                        <table class="table table-hover table-bordered">
                           <thead>
                              <th><?=_("1차옵션")?></th>
                              <th><?=_("2차옵션")?></th>
                              <th><?=_("추가가격(센터)")?></th>
                              <th><?=_("추가가격(몰)")?></th>
                              <th><?=_("소비자가")?></th>
                              <th><?=_("적립금")?></th>
                              <th><?=_("제휴사상품번호")?></th>
                              <th><?=_("옵션공급가")?></th>
                              <th><?=_("재고")?></th>
                              <th><?=_("노출여부")?></th>
                              <th><?=_("편집기호출")?></th>
                           </thead>
                           
                           <tbody>
                              <? if ($r_opt) foreach ($r_opt as $k=>$v){ if (!$r_opt[$v[optno]][podsno]) $r_opt[$v[optno]][podsno] = $data[podsno]; ?>
                              <input type="hidden" name="optno[]" value="<?=$k?>">
                              <tr align="center">
                                 <td><?=$v[opt1]?></td>
                                 <td><?=$v[opt2]?></td>
                                 <td><?=number_format($v[center_aprice])?></td>
                                 <td><input type='text' class="form-control" name='aprice[<?=$v[optno]?>]' value='<?=$v[mall_aprice]?>' size='12'/></td>
                                 <td><input type='text' class="form-control" name='mall_opt_cprice[<?=$v[optno]?>]' value='<?=$v[mall_opt_cprice]?>' size='12'/></td>
                                 <td><input type='text' class="form-control" name='areserve[<?=$v[optno]?>]' value='<?=$v[mall_areserve]?>' size='12'/></td>
                                 <td><input type="text" class="form-control" name="b2b_optno[<?=$v[optno]?>]" value="<?=$v[b2b_optno]?>" size="13"/></td>
                                 <td><?=number_format($v[asprice])?></td>
                                 <td><?=$v[stock]?></td>
                                 <td>
                                    <!--<?=($v[opt_view])?"N":"Y"?>-->
                                    <!--//상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk-->
                                    <select name="opt_view[<?=$v[optno]?>]">
                                    <option value="0">Y
                                    <option value="1" <?if($v[opt_view]){?>selected<?}?>>N
                                    </select>      
                                 </td>
                                 <td>
                                 <u class="hand" onclick="popupLayer('../../module/popup_calleditor.php?mode=edit&goodsno=<?=$data[goodsno]?>&productid=<?=$data[podsno]?>&optionid=<?=$r_opt[$v[optno]][podoptno]?>');">TEST</u> / 
                                 <u class="hand" onclick="popup('goods.p.php?podsno=<?=$data[podsno]?>&optionid=<?=$v[podoptno]?>', 630, 275)"><?=_("제작대행")?></u>
                                 </td>
                              </tr>
                              <? } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  
                  
                  
                  <!-- ==============================인화 옵션============================== -->
                  <? if ($r_printopt) { ?>
                  <div class="form-group option-pro">
                     <div class="col-md-12"> > > <?=_("인화옵션")?></span></div>
                  </div>
                  
                  <div class="form-group option-pro" id="setopt_div">
                     <label class="col-md-2 control-label"><?=_("인화옵션설정")?></label>
                     <div class="col-md-10 form-inline">
                        <span class="desc"><?=_("인화상품의 경우 인화종류별 금액을 설정해주셔야 올바른 주문을 접수받으실 수 있습니다.")?></span><br><br>
                     </div>
                  </div>
                  
                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <table id="printopt_tb" class="table table-hover table-bordered">
                           <thead>
                              <th><?=_("옵션명")?></th>
                              <th><?=_("인화옵션크기")?></th>
                              <th><?=_("인화옵션판매가")?></th>
                              <th><?=_("인화옵션공급가")?></th>
                              <th><?=_("인화옵션상점판매가")?></th>
                              <th><?=_("인화옵션상점적립금")?></th>
                           </thead>
                           
                           <tbody>
                              <? foreach ($r_printopt as $k=>$v) { ?>
                              <input type="hidden" name="printoptnm[]" value="<?=$k?>">
                              <tr align="center">
                                 <td><?=$v[printoptnm]?></td>
                                 <td><?=$v[print_size]?></td>
                                 <td align="right"><?=number_format($v[print_price])?></td>
                                 <td align="right"><?=number_format($v[print_sprice])?></td>
                                 <td><input type="text" class="form-control" name="mall_print_price[<?=$v[printoptnm]?>]" value="<?=$v[mall_print_price]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="mall_print_reserve[<?=$v[printoptnm]?>]" value="<?=$v[mall_print_reserve]?>" pt="_pt_numplus"/></td>
                              </tr>
                              <? } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  <? } ?>


                  <!-- ==============================추가 옵션============================== -->
                  <? if ($r_addopt_bundle){ ?>
                  <div class="form-group option-pro">
                     <div class="col-md-12"> > > <?=_("추가옵션")?></div>
                  </div>

                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"><?=_("추가옵션설정")?></label>
                     <div class="col-md-10">
                        <!--<span type="button" class="btn btn-primary" onclick="window.open('img_addopt_popup.php?goodsno=<?=$_GET[goodsno]?>','','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');"><?=_("옵션이미지추가")?></span><br><br>--></br>

                        <div id="addoptbox_div">
                           <? if ($r_addopt_bundle) foreach ($r_addopt_bundle as $k=>$v){ ?>
                           <div addoptbox_idx="<?=$k?>">
                              <table class="table table-striped table-bordered">
                                 <tr class="form-inline">
                                    <td>
                                       
                                       
                                       <div class="addoptbox_div" addoptbox_idx="<?=$k?>">
                                          <table class="table table-bordered">
                                          <tr>
                                             <th><?=_("추가옵션묶음명")?></th>
                                             <td>
                                             <?=$v[addopt_bundle_name]?>
                                             </td>
                                             <? if ($v[addopt_bundle_required]){ ?>
                                             <td><b class="absmiddle stxt red">*<?=_("필수옵션")?></b></td>
                                             <? } ?>
                                             <td>
                                                <!--<?=($v[addopt_bundle_view])?"숨김":"노출";?>-->
                                                <!--//상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk-->
                                                <select name="addopt_bundle_view[<?=$v[addopt_bundle_no]?>]">
                                                <option value="0"><?=_("노출")?>
                                                <option value="1" <?if($v[addopt_bundle_view]){?>selected<?}?>><?=_("숨김")?>
                                                </select>         
                                             </td>
                                          </tr>
                                          </table>
                                    
                                          <table class="table table-bordered">
                                             <tr>
                                                <th><?=_("옵션명")?></th>
                                                <th><?=_("옵션판매가(센터)")?></th>
                                                <th><?=_("옵션공급가(센터)")?></th>
                                                <th><?=_("옵션판매가(몰)")?></th>
                                                <th><?=_("옵션판매가(적립금)")?></th>
                                                <th><?=_("소비자가")?></th>
                                                <th><?=_("노출여부")?></th>
                                             </tr>
                                             <? foreach ($v[addopt] as $k2=>$v2) { ?>
                                             <tr align="center">
                                                <td><?=$v2[addoptnm]?></td>
                                                <td><?=number_format($v2[addopt_aprice])?></td>
                                                <td><?=number_format($v2[addopt_asprice])?></td>
                                                <td><input type="text" class="form-control" name="addopt_aprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[mall_addopt_aprice]?>" size='12'/></td>
                                                <td><input type="text" class="form-control" name="addopt_areserve[<?=$k?>][<?=$k2?>]" value="<?=$v2[mall_addopt_areserve]?>" size='12'/></td>
                                                <td><input type="text" class="form-control" name="mall_addopt_cprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[mall_addopt_cprice]?>" size='12'/></td>
                                                <td>
                                                   <!--<?=($v2[addopt_view])?"숨김":"노출";?>-->
                                                   <!--//상점별 옵션 노출여부 설정 변경 2014.10.23 by kdk-->
                                                   <select name="addopt_view[<?=$k2?>]"><option value="0"><?=_("노출")?><option value="1" <?if($v2[addopt_view]){?>selected<?}?>><?=_("숨김")?></select>
                                                </td>
                                             </tr>
                                             <? } ?>
                                          </table>
                                    
                                       </div>
                                       
                                       
                                    </td>
                                 </tr>
                              </table>
                           </div>
                           <? } ?>
                        </div>
                     </div>                                       
                  </div>
                  <? } ?>


                  <!-- ==============================이미지설정============================== -->

                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("이미지 설정")?>
                        <li style="float:left;"><u><a href="indb.php?mode=img&goodsno=<?=$data[goodsno]?>">[<?=_("상세이미지동기화")?>]</a></u>&nbsp;/</li>
                        <li style="display:inline">&nbsp;<u><a href="indb.php?mode=listimg&goodsno=<?=$data[goodsno]?>">[<?=_("리스트이미지동기화")?>]</a></u></li>
                     </div>
                  </div>

                  <div class="form-group">
                     <? 
                        list ($img) = $db->fetch("select img from exm_goods where goodsno = '$data[goodsno]'",1);
                        list ($cimg) = $db->fetch("select cimg from exm_goods_cid where cid = '$cid' and goodsno = '$data[goodsno]'",1);
                        $img = explode("||",$img);
                        $cimg = explode("||",$cimg);
                     
                        for ($i=0;$i<5;$i++){ 
                     ?>
                     <div class="form-group">
                        <label class="col-md-2 control-label"><?=_("상세")?><?=$i+1?></label>
                        <div class="col-md-6">
                           <input type="file" class="form-control" name="img[]">
                           <?                     
                           if (!$cimg[$i]) {
                              $imgDir = $data[cimg][$i];
                           } else {
                              $imgDir = $cimg[$i];
                           }
                           
                           ?>
                           <?if($cimg[$i]){?>
                              <img src="../img/bt_preview.png" align="absmiddle" onclick="vLayer(this.nextSibling)"/><div style="display:none;">
                              <img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/l/<?=$data[goodsno]?>/<?=$imgDir?>"></div>
                           <?}?>
                        </div>
                        <div class="col-md-2">
                           <input type="radio" name="thumbnail" value="<?=$i?>" onclick="$j('#listimg').attr('disabled',true)"> <span><?=_("리스트 이미지 생성")?></span>
                        </div>
                        <div class="col-md-2">
                           <input type="checkbox" name="delimg[]" value="<?=$i?>"><span><?=_("삭제")?></span>
                        </div>
                     </div>
                     
                     <? } ?>
                     
                     
                     <div class="form-group">
                        <label class="col-md-2 control-label"><?=_("리스트")?></label>
                        <div class="col-md-6">
                           <input type="file" class="form-control" name="listimg" id="listimg">
                           <? if ($data[clistimg]){ ?>
                           <i class="fa fa-2x fa-eye" onclick="vLayer(this.nextSibling)"></i><div style="display:none;">
                           <img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/s/<?=$data[goodsno]?>/<?=$data[clistimg]?>">
                           </div>   
                           <? } ?>
                        </div>
                        <div class="col-md-2">
                           <input type="radio" name="thumbnail" value="" onclick="$j('#listimg').attr('disabled',false)" checked>
                           <span><?=_("리스트 이미지 수동 적용")?></span>
                        </div>
                        <div class="col-md-2">
                           <input type="checkbox" name="dellistimg"><span><?=_("삭제")?></span>
                        </div>
                     </div>
                     
					 <div class="form-group">
                        <label class="col-md-2 control-label"><?=_("리스트 와이드")?></label>
                        <div class="col-md-6">
                           <input type="file" class="form-control" name="listimg_w" id="listimg_w">
                           <? if ($data[clistimg_w]){ ?>
                           <i class="fa fa-2x fa-eye" onclick="vLayer(this.nextSibling)"></i><div style="display:none;">
                           <img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/s/<?=$data[goodsno]?>/<?=$data[clistimg_w]?>">
                           </div>   
                           <? } ?>
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-2">
                           <input type="checkbox" name="dellistimg_w"><span><?=_("삭제")?></span>
                        </div>
                     </div>                     
                     
                  </div>
                  
                  
                  
                  <!-- ==============================상세설명(센터)============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("상세설명(센터)")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <div style="width:100%;overflow:auto;height:500px;" id="center_desc"><?=$data[desc]?></div>
                     </div>
                  </div>
                  
                  
                  
                  <!-- ==============================상세설명(몰)============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("상세설명(몰)")?>
                        <li style="display:inline"><u onclick="syncContents('center_desc', 'desc');" class="hand">[<?=_("상세설명동기화")?>]</u></li>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <textarea name="content" style="width:99.8%;height:400px" type="editor" id="desc"><?=$data[mall_desc]?></textarea><p>
                     </div>
                  </div>
                  
                  
                  <!-- ==============================추천상품,연관상품============================== -->
                  
                  <? foreach ($r_addtion_goods_data as $r_k=>$r_v) { ?>
                     <div class="form-group">
	                     <div class="col-md-12">
	                        > > <?=$r_v[display]?>
	                     </div>
	                  </div>
	                  
	                  <div class="form-group">
	                     <label class="col-md-2 control-label"><?=_("사용여부")?></label>
	                     <div class="col-md-10">
	                     	<input type="radio" class="radio-inline" name="<?=$r_k?>_flag" value="N" <?=$checked[$r_k."_flag"][N]?>> <?=_("사용안함")?>
	      	 				<input type="radio" class="radio-inline" name="<?=$r_k?>_flag" value="Y" <?=$checked[$r_k."_flag"][Y]?>> <?=_("사용")?>
	                     </div>
	                  </div>
	                  
	                  <div class="form-group notView" id="<?=$r_k?>_div">
	                     <label class="col-md-2 control-label"></label>
	                     <div class="col-md-10 form-inline">
	                     	<section class="srel">
							    <div class="compare_wrap">
							        <section class="compare_left">
							            <h3><?=_("등록된 전체상품 목록")?></h3>
							            <span class="srel_pad">
							            	<label for="<?=$r_k?>_catno" class="sound_only"><?=_("상품분류")?></label>
							            	<select id="<?=$r_k?>_catno" class="form-control" name="<?=$r_k?>_catno" style="width:35%;">
							            		<option value="">+ <?=_("분류 선택")?></option>
							            		<?=conv_selected_option($ca_list, "")?>
							            	</select>
							                <label for="<?=$r_k?>_name" class="sound_only"><?=_("상품명")?></label>
							                <input type="text" id="<?=$r_k?>_name" class="form-control" name="<?=$r_k?>_name" style="width:45%;">
							                <button type="button" id="btn_<?=$r_k?>_search" class="btn btn-sm btn-success"><?=_("검색")?></button>
							            </span>
							            <div id="<?=$r_k?>" class="srel_list">
							                <p><?=_("등록된 상품이 없습니다.")?></p>
							            </div>
							            
							            <script>
							            $j(function() {
							                $j("#btn_<?=$r_k?>_search").click(function() {
							                    var catno = $j("#<?=$r_k?>_catno").val();
							                    var goodsnm = $j.trim($("#<?=$r_k?>_name").val());
							                    var $kind = $j("#<?=$r_k?>");
							
							                    if (catno == "" && goodsnm == "") {
							                        $kind.html("<p>" + '<?=_("등록된 상품이 없습니다.")?>' + "</p>");
							                        return false;
							                    }
							
							                    $j("#<?=$r_k?>").load(
							                        "indb.php",
							                        { mode:"addtion_goods_list", kind: "<?=$r_k?>", catno: catno, goodsnm: goodsnm }
							                    );
							                });
							
							                $(document).on("click", "#<?=$r_k?> .add_item", function() {
							                    // 이미 등록된 상품인지 체크
							                    var $li = $j(this).closest("li");
							                    var goodsno = $li.find("input:hidden").val();
							                    var reg_goodsno;
							                    var dup = false;
							                    
							                    $j("#reg_<?=$r_k?> input[name='<?=$r_k?>_select_goodsno[]']").each(function() {
							                        reg_goodsno = $j(this).val();
							                        
							                        if(goodsno == reg_goodsno) {
							                            dup = true;
							                            return false;
							                        }
							                    });
							
							                    if (dup) {
							                        alert('<?=_("이미 선택된 상품입니다.")?>');
							                        return false;
							                    }
							
							                    var cont = "<li>" + $li.html().replace("add_item", "del_item").replace('<?=_("추가")?>', '<?=_("삭제")?>').replace("<?=$r_k?>_goodsno[]", "<?=$r_k?>_select_goodsno[]") + "</li>";
							                    var count = $j("#reg_<?=$r_k?> li").size();
							
							                    if (count > 0) $j("#reg_<?=$r_k?> li:last").after(cont);
							                    else $j("#reg_<?=$r_k?>").html("<ul id=\"<?=$r_k?>_sortable\">" + cont + "</ul>");
							                    
							                    $j("#<?=$r_k?>_sortable").sortable();
							                    
							                    $li.remove();
							                });
							
							                $(document).on("click", "#reg_<?=$r_k?> .del_item", function() {
							                    if (!confirm('<?=_("상품을 삭제하시겠습니까?")?>')) return false;
							                    $j(this).closest("li").remove();
							
							                    var count = $j("#reg_<?=$r_k?> li").size();
							                    if (count < 1) $("#reg_<?=$r_k?>").html("<p>" + '<?=_("선택된 상품이 없습니다.")?>' + "</p>");
							                });
							            });
							            </script>
							        </section>
							
							        <section class="compare_right">
							            <h3><?=_("선택된")?> <?=$r_v[display]?> <?=_("목록")?></h3>
							            <span class="srel_pad"></span>
							            <div id="reg_<?=$r_k?>" class="srel_sel">
							                <p><?=_("선택된 상품이 없습니다.")?></p>
							            </div>
							        </section>
							    </div>
							</section>
							<div><span class="notice">[<?=_("설명")?>]</span> <?=_("선택된 상품 목록의 순서를 변경하시려면 드래그해주시면 됩니다.")?></div>
	                     </div>
	                  </div>
                  <? } ?>

                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success" <?if($_GET[mode] =="member_modify") { ?> onclick="option_chk()" <?}?> >
                        <? if($_GET[mode] == "member_join") { ?>
                           <?=_("등록")?>
                        <?} else {?>
                           <?=_("수정")?>
                        <? } ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
                     </div>
                  </div>

               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>

<!--form 전송 취약점 개선 20160128 by kdk-->
<script src="../../js/webtoolkit.base64.js"></script>
<script src="../../js/jquery-ui.js"></script>
<script src="../../js/jquery.ui.touch.js"></script>

<script type="text/javascript">
$j(function() {
    <? if ($r_addtion_goods_data_package == "Y") { ?>
        $j("#reg_package").load(
            "indb.php",
            { mode:"addtion_goods_select", kind: "package", kind2: "P", addtion_key : "<?=$data[goodsno]?>" },
            function(response, status, xhr) {
                $j("#package_sortable").sortable();
            }
        );
    <? } ?>
        
	<? foreach ($r_addtion_goods_data as $r_k=>$r_v) { ?>
		$j("[name=<?=$r_k?>_flag][value=<?=$r_v[flag]?>]").trigger("click");
			
		$j("#reg_<?=$r_k?>").load(
			"indb.php",
			{ mode:"addtion_goods_select", kind: "<?=$r_k?>", kind2: "I", addtion_key : "<?=$data[goodsno]?>" },
			function(response, status, xhr) {
				$j("#<?=$r_k?>_sortable").sortable();
			}
		);
	<? } ?>
});

<? foreach ($r_addtion_goods_data as $r_k=>$r_v) { ?>
	$j("[name=<?=$r_k?>_flag]").click(function() {
		if ($j(this).val() == "Y") {
			$j("#<?=$r_k?>_div").show();
		} else {
			$j("#<?=$r_k?>_div").hide();
		}
	});
<? } ?>

var oEditors = [];
smartEditorInit("desc", true, "goods", true);
    
function submitContents(formObj) {
    //패키지상품 갯수 체크.
    if($j("#reg_package li").length > 0) {
        if ($j("#reg_package li").size() < 3) {
            alert('<?=_("3개 상품을 패키지로 구성해야 합니다.")?>');
            return false;
        }
    }
    
    if (sendContents("desc", false))
    {
        try {
        	formObj.desc.value = Base64.encode(formObj.desc.value);
            return form_chk(formObj);
        } catch(e) {return false;}
    }
    return false;
}

//상품 상세설명동기화
function syncContents(sourceTagIDName, targetTagIDName) {
   var sHTML = document.getElementById(sourceTagIDName).innerHTML

   pasteHTML(targetTagIDName, sHTML);
}

function center_info_sync(){
   var i;
   
   for(i = 0 ; i<5 ; i++ ){
      if($("td[name=goods_desc_name_"+i+"]").text()){
         var goods_desc_name = $("td[name=goods_desc_name_"+i+"]").text();
         document.getElementById("mall_goods_desc_name_"+i).value = goods_desc_name;
      }
      
      if($("td[name=goods_desc_value_"+i+"]").text()){
         var goods_desc_value = $("td[name=goods_desc_value_"+i+"]").text();
         document.getElementById("mall_goods_desc_value_"+i).value = goods_desc_value;
      }
   }
   
}
</script>

<script>
/* 인쇄,스튜디오 견적 / 패키지 정보 */
<?if($data[goods_group_code] == "30"){?>
	$j("#extra").show();
	$j("#pack").hide();
	$j("#extra_print").hide();
	$j(".option-pro").show();
<?}else if($data[goods_group_code] == "50"){?>
    $j("#extra").hide();
    $j("#pack").show();
    $j("#extra_print").hide();
    $j(".option-pro").hide();
<?}else if($data[goods_group_code] == "60"){?>
    $j("#extra").hide();
    $j("#pack").hide();
    $j("#extra_print").show();
    $j(".option-pro").hide();
<?} else {?>
	$j("#extra").hide();
	$j("#pack").hide();
	$j("#extra_print").hide();
	$j(".option-pro").show();
<?}?>
/* 인쇄,스튜디오 견적 / 패키지 정보 */
</script>

<script src="../assets/plugins/DataTables-1.9.4/js/jquery.dataTables.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>

<? include "goods_r.js.php"; ?>
<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>
