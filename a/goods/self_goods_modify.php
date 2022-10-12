<?
include "../_header.php";
include "../_left_menu.php";
$m_goods = new M_goods();

$query = "select count(*) from exm_release where cid = '$cid'";
list ($chk) = $db->fetch($query,1);
if ($chk < 1){
   msg(_("등록된 제작사가 없습니다."),-1);
   exit;
}
 
$fkey = md5(crypt(''));
$ls = array();
$r_bcatno = array();
$r_rid = get_release(1);
$r_opt = array();
$r_printopt = array();
$r_brand = get_brand();
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

$data[shiptype] = 0;

if ($_GET[goodsno]){
   $data = $db->fetch("select * from exm_goods where goodsno='$_GET[goodsno]'");
   if (!$data){
      msg(_("상품데이터가 존재하지 않습니다."),-1);
      exit;
   }

   if($cid != $data[reg_cid]) {
      //debug("reg_cid=".$data[reg_cid]);
      msg(_("자체 상품만 수정이 가능합니다."),-1);
      exit;
   }

   $fkey = $data[goodsno];
   $ls = ls("../../data/goods/l/$fkey/");

   $query = "select * from exm_goods_opt where goodsno = '$data[goodsno]' order by osort";
   $res = $db->query($query);
   while ($tmp = $db->fetch($res)){
      $r_opt[$tmp[optno]] = $tmp;
   }

   $query = "select * from exm_goods_printopt where goodsno = '$data[goodsno]' order by osort";
   $res = $db->query($query);
   while ($tmp = $db->fetch($res)){
      $r_printopt[] = $tmp;
   }

   $query = "select * from exm_goods_addopt_bundle where goodsno = '$_GET[goodsno]' order by addopt_bundle_sort";
   $res = $db->query($query);
   $r_addopt_bundle = array();
   while ($tmp = $db->fetch($res)){
      $r_addopt_bundle["b_".$tmp[addopt_bundle_no]] = $tmp;
      $query = "select * from exm_goods_addopt where addopt_bundle_no = '$tmp[addopt_bundle_no]' order by addopt_sort";
      $res2 = $db->query($query);
      while ($tmp2 = $db->fetch($res2)){
         $r_addopt_bundle["b_".$tmp[addopt_bundle_no]][addopt]["b_".$tmp2[addoptno]] = $tmp2;
      }
   }
   
   //통합형 옵션 데이터 가져오기 / 18.01.17 / kjm
   $cover_range_option = $m_goods->getCoverRangeOption($_GET[goodsno]);
   $cover_range_standard = $m_goods->getCoverRangeStandard($_GET[goodsno]);
   
   //상품 다중 카테고리로 변경        20150224    chunter
   //$CcatLinkNo = $m_goods->getCategoryLinkList($cfg[center_cid], $data[goodsno], "cat_index asc");
   //$catLinkNo = $m_goods->getCategoryLinkList($cid, $data[goodsno], "cat_index asc");

    //순서 문제 처리 / 20190412 / kdk
    $CcatLinkNo = array(0 => "", 1 => "", 2 => "", 3 => "");
    $catLinkNo = array(0 => "", 1 => "", 2 => "", 3 => "");   
    $CcatData = $m_goods->getCategoryLinkList($cfg[center_cid], $data[goodsno], "cat_index asc");
    $catData = $m_goods->getCategoryLinkList($cid, $data[goodsno], "cat_index asc");

    if ($CcatData) {
        foreach ($CcatData as $key => $value) {
            if ($value) {
                $index = $value[cat_index] - 1;
                $CcatLinkNo[$index] = $value;
            }
        }
    }

    if ($catData) {
        foreach ($catData as $key => $value) {
            if ($value) {
                $index = $value[cat_index] - 1;
                $catLinkNo[$index] = $value;
            }
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
   
   $_GET[mode] = "modify";


    //진열불가 여부 조회 / 20190305 / kdk
    list ($nodp) = $db->fetch("select nodp from exm_goods_cid where cid = '$cid' and goodsno = '$data[goodsno]'",1);
    if ($nodp) {
        $checked[nodp][1] = "checked";
    }

}

$checked[podskind][$data[podskind]+0] = $checked[state][$data[state]+0] = $checked[usestock][$data[usestock]+0] = $checked[shiptype][$data[shiptype]+0] = "checked";
$checked[useopt][$data[useopt]+0] = "checked";
$checked[input_str][$data[input_str]+0] = $checked[input_file][$data[input_file]+0] = "checked";


//debug($data[pods_editor_type]);

$data[pods_editor_type] = json_decode($data[pods_editor_type],1);

if($data[pods_editor_type][editor_active_exe])
   $checked[editor_active_exe][$data[pods_editor_type][editor_active_exe]] = "selected";
else $checked[editor_active_exe][active] = "selected";

if($data[pods_editor_type][editor_web])
   $checked[editor_web][$data[pods_editor_type][editor_web]] = "selected";
else $checked[editor_web][$data[pods_editor_type][N]] = "selected";

$selected[rid][$data[rid]] = "selected";
$selected[privatecid][$data[privatecid]] = "selected";
$selected[brandno][$data[brandno]] = "selected";

list($mall_info[podsid],$mall_info[siteid]) = $db->fetch("select self_podsid,self_podsiteid from exm_mall where cid = '$cid'",1);


$checked[pods_use][$data[pods_use]] = "checked";
if (!$data[pods_use]){
   if ($data[podskind] && in_array($data[podskind],$r_podskind20)){
      $checked[pods_use][2]   = "checked";
      $is_podskind20       = "true";
   } else if ($data[podskind]){
      $checked[pods_use][1]   = "checked";
   }
}

//상세 가로보기(견적) 추가 2016.04.14 by kdk
$horizon_view_flag = $data[horizon_view_flag];
if($data[horizon_view_flag])
   $checked[horizon_view_flag] = "checked";

$checked[goods_group_code][$data[goods_group_code]] = "checked";
if (!$data[goods_group_code]){
   $checked[goods_group_code][10]   = "checked";
}

//자동견적 정보
if($data[extra_option]) {
	$extra_option = explode('|',$data[extra_option]); //항목 분리
	if(count($extra_option) > 0) {

		if($extra_option[1] == "100106") $extra_option[0] = "CARD";

		$checked[extra_product][$extra_option[0]] = "checked";
		$checked[extra_preset][$extra_option[0]][$extra_option[1]] = "checked";
		$checked[extra_price_type][$extra_option[2]] = "checked";
		//$checked[extra_editor][$extra_option[3]] = "checked";
		$extra_preset_div = $extra_option[0];

		$extra_product = $extra_option[0];
		$extra_preset = $extra_option[1];
		$extra_price_type = $extra_option[2];

		//스튜디오 견적 관련
		if($data[goods_group_code] == "20") {
			$extra_stu_preset = $extra_option[1];
			$extra_stu_order = $extra_option[3];

			$checked[extra_stu_preset][$extra_option[1]] = "checked";

			if($extra_option[3])
				$checked[extra_stu_order][$extra_option[3]] = "checked";
			else
				$checked[extra_stu_order]["UPL"] = "checked";

			//검수 기능 추가 2015.06.23 by kdk
			if($data[studio_order_check_flag])
				$checked[studio_order_check_flag] = "checked";
		}
        else if ($data[goods_group_code] == "60") { //견적상품 / 20180517 / kdk.
            $checked[extra_print_product][$extra_product] = "checked";
        }

		//자동견적결제 여부 기능 추가 2015.06.24 by kdk
		if($data[extra_auto_pay_flag])
			$checked[extra_auto_pay_flag] = "checked";

		//견적가격 노출 여부 기능 추가 2015.06.24 by kdk
		if($data[extra_price_view_flag])
			$checked[extra_price_view_flag] = "checked";

		$extra_auto_pay_flag = $data[extra_auto_pay_flag];
		$extra_price_view_flag = $data[extra_price_view_flag];

		//견적가격(부가세 계산 방식) 추가 2016.03.30 by kdk
		$extra_price_vat_flag = $data[extra_price_vat_flag];

		//견적 두번째 수량 표시 여부 추가 2016.04.14 by kdk
		if($data[extra_unitcnt_view_flag])
			$checked[unitcnt_view_flag] = "checked";

		//면적당 계산 여부 추가 / 16.11.03 / kdk
      if($extra_price_type == "SIZE")
         $checked[price_type_size_flag] = "checked";
   }
}
else {
	$checked[extra_product]["CARD"] = "checked";
	$checked[extra_preset]["CARD"]["100102"] = "checked";
	$checked[extra_price_type]["CNT"] = "checked";
	//$checked[extra_editor]["0"] = "checked";
	$extra_preset_div = "CARD";

	//$extra_product = "100102";
	//$extra_preset = "CARD";
	$extra_price_type = "CNT";

	//스튜디오 견적 관련
	$extra_stu_preset = "UPL";
	$extra_stu_order = "100110";

	$checked[extra_stu_order]["UPL"] = "checked";
	$checked[extra_stu_preset]["100110"] = "checked";
	
	//자동견적결제 여부 기능 추가 2015.06.24 by kdk
	$checked[extra_auto_pay_flag] = "checked";
	$extra_auto_pay_flag = "1";

	//견적가격 노출 여부 기능 추가 2015.06.24 by kdk
	$checked[extra_price_view_flag] = "checked";
	$extra_price_view_flag = "1";
}

if ($data[icon_filename]) {
	$data[icon_filename] = explode("||", $data[icon_filename]);

	foreach ($data[icon_filename] as $k => $v) {
		$checked[icon_filename][$v] = "checked";
	}
}

if ($data[goods_desc]) {
	$data[goods_desc] = unserialize($data[goods_desc]);
}

//미리보기 형식			20171017		chunter
if (!$data[preview_app_type]) $data[preview_app_type] = "basic";
$checked[preview_app_type][$data[preview_app_type]] = "checked";

//센터 카테고리 분류
$c_cate_data = $m_goods->getCategoryList($cfg_center[cid]);
$c_ca_list = makeCategorySelectOptionTag($c_cate_data);

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

//템플릿 카테고리 분류 / 20170921 / kdk
$tem_catLinkNo = $m_goods->getTemplateCategoryLinkNo($cid, $data[goodsno]);
$tem_cate_data = $m_goods->getTopTemplateCategoryList($cid);
$tem_ca_list = makeCategorySelectOptionTag($tem_cate_data);
//debug($tem_catLinkNo);
//debug($tem_cate_data);
//debug($tem_ca_list);

//인터프로용 견적종류선택.
$checked[print_goods_group][$data[print_goods_group]] = "checked";

//견적상품 종류 사용 설정.
$extra_use = getCfg('extra_use');
if ($extra_use) {
    $extra_use = json_decode($extra_use,1);
}
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
         <?=_("자체상품등록")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("자체상품등록")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("자체상품등록")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">
                  <input type="hidden" name="mode" value="self_goods_modify">
                  <input type="hidden" name="privatecid" value="<?=$cid?>"/>
                  <input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>"/>

   					<input type="hidden" name="extra_price_type" value="<?=$extra_price_type?>" />
   					<input type="hidden" name="extra_flag" id="extra_flag" />
   					<input type="hidden" name="extra_auto_pay_flag" id="extra_auto_pay_flag" value="<?=$extra_auto_pay_flag?>" />
   					<input type="hidden" name="extra_price_view_flag" id="extra_price_view_flag" value="<?=$extra_price_view_flag?>" />

   					<input type="hidden" name="goods_group_code" value="<?=$data[goods_group_code]?>" />
   					<input type="hidden" name="extra_product" value="<?=$extra_product?>" />
   					<input type="hidden" name="extra_preset" value="<?=$extra_preset?>" />

                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("상품 기본정보")?></div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("센터분류")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="ccatno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("1차 분류 선택")?></option><?=conv_selected_option($c_ca_list, $CcatLinkNo[0][catno])?></select>
                        <select name="ccatno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("2차 분류 선택")?></option><?=conv_selected_option($c_ca_list, $CcatLinkNo[1][catno])?></select>
                        <select name="ccatno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("3차 분류 선택")?></option><?=conv_selected_option($c_ca_list, $CcatLinkNo[2][catno])?></select>
                        <select name="ccatno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("4차 분류 선택")?></option><?=conv_selected_option($c_ca_list, $CcatLinkNo[3][catno])?></select>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품분류")?></label>
                     <div class="col-md-7 form-inline">
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("1차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[0][catno])?></select>
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("2차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[1][catno])?></select>
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("3차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[2][catno])?></select>
                        <select name="catno[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("4차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[3][catno])?></select>
                     </div>
                     <label class="col-md-1 control-label"><?=_("템플릿 분류")?></label>
                     <div class="col-md-2 form-inline">
						      <select name="tem_catno" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("템플릿 분류 선택")?></option><?=conv_selected_option($tem_ca_list, $tem_catLinkNo[catno])?></select>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품종류")?></label>
                     <div class="col-md-10 form-inline">
                        <? 
                        foreach ($r_goods_group_code as $k=>$v)
                        {
                            if($k == 10 || $k == 30 || $k == 50 || $k == 60) 
                            {
                                if ($k>10 && (!is_array($extra_use) || !in_array($k, $extra_use[goods_group_code]))) continue; 
                        ?>
                        <input type="radio" name="goods_group_code" value="<?=$k?>" onclick="click_group_div('<?=$k?>')" <?=$checked[goods_group_code][$k]?>><span class="absmiddle"><?=$v?></span>
                        <?
                            }
                        } 
                        ?>

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

                                                    var cont = "<li>" + $li.html().replace("add_item", "del_item").replace('<?=_("추가")?>', '<?=_("삭제")?>').replace("package_goodsno[]", "package_select_goodsno[]") + "</li>";
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
                        	<td>
                        	<?
                        	foreach ($r_est_product as $k=>$v)
                        	{
                        	    if (!in_array($k, $extra_use[extra_product])) continue; 
                        	?>
                        	<input type="radio" name="extra_product" value="<?=$k?>" onclick="click_preset_div('<?=$k?>')" <?=$checked[extra_product][$k]?>><span class="absmiddle"><?=$v?></span>
                        	<?
                        	}
                            ?>
                        	</td>
                        </tr>
                        <tr>
                        	<th><?=_("프리셋스타일선택")?></th>
                        	<td>
                        	<? foreach ($r_est_preset as $k=>$v){ ?>
                        	<div id="preset_<?=$k?>" class="preset_div">
                        		<? 
                        		foreach ($v as $k1=>$v1)
                        		{
                        		    if (!in_array($k1, $extra_use[extra_preset])) continue;
                        		?>
                        		<input type="radio" name="extra_preset" value="<?=$k1?>" <?=$checked[extra_preset][$k][$k1]?>><span class="absmiddle"><?=$v1?></span>
                        		<?
                        		}
                                ?>
                        	</div>
                        	<? } ?>
                        	</td>
                        </tr>
                        <tr>
                        	<th><?=_("인쇄견적 옵션설정")?></th>
                        	<td>
                        		<? if ($data[cids]) { ?>
                        			<button type="submit" class="btn btn-xs btn-danger" onclick="alert('<?=_("본 상품은 1개 이상의 분양몰에서 이미 상품으로 연결되고 진열되어 있기에 판매정보 외에는 가격설정을 등을 포함한 모든 상품설정을 수정할 수가 없습니다.")?>');$j('#extra_flag').val('Y');"><?=_("설정하기")?></button>
                        		<? } else {?>
                        			<button type="submit" class="btn btn-xs btn-danger" onclick="$j('#extra_flag').val('Y');"><?=_("설정하기")?></button>
                        		<? }?>

                        		<!--//자동견적결제 여부 기능 추가 2015.06.24 by kdk-->
                        		<input type="checkbox" name="extra_auto_pay_flag_" value="1" class="absmiddle" <?=$checked[extra_auto_pay_flag]?> onclick="click_auto_pay_div()" /><span class="stxt absmiddle"><?=_("자동견적결제")?></span>

                        		<!--//견적가격노출 기능 추가 2015.06.24 by kdk-->
                        		<input type="checkbox" name="extra_price_view_flag_" value="1" class="absmiddle" <?=$checked[extra_price_view_flag]?> onclick="click_price_view()" /><span class="stxt absmiddle"><?=_("견적가격노출")?></span>
                        		
                        		<!--//견적가격(부가세 계산 방식) 추가 2016.03.30 by kdk-->
                        		<input type="radio" name="extra_price_vat_flag" value="0" <?if($extra_price_vat_flag == "0"){?>checked<?}?>><span class="absmiddle"><?=_("부가세 별도")?></span>		
                        		<input type="radio" name="extra_price_vat_flag" value="1" <?if($extra_price_vat_flag == "1"){?>checked<?}?>><span class="absmiddle"><?=_("부가세 포함")?></span>

                        		<!--상세 가로보기(견적) 추가 2016.04.14 by kdk-->
                        		<input type="checkbox" name="horizon_view_flag" value="1" class="absmiddle" <?=$checked[horizon_view_flag]?> /><span class="stxt absmiddle"><?=_("견적가로보기")?></span>

                        		<!--견적 두번째 수량 표시 여부 추가 2016.04.14 by kdk-->
                        		<input type="checkbox" name="unitcnt_view_flag" value="1" class="absmiddle" <?=$checked[unitcnt_view_flag]?> /><span class="stxt absmiddle"><?=_("견적두번째수량숨기기")?></span>

                        		<!--면적당 계산 여부 추가 / 16.11.03 / kdk-->
                        		<input type="checkbox" name="price_type_size_flag" value="1" class="absmiddle" <?=$checked[price_type_size_flag]?> /><span class="stxt absmiddle"><?=_("면적당 계산하기")?></span>

                        		<span id="msg_auto_pay" class="desc small red" style="display: none; margin-left: 85px;">
                        			<div>
                        			<br>
                        			(<?=_("자동견적결제를 선택하지 않는 경우 이 상품은 견적의뢰 프로세스로 처리되며 상품 등록 후 운영 중에는 변경이 불가능 합니다.")?>)
                        			</div>
                        		</span>

                        		<? if ($data[cids]) { ?>
                        		<div>
                        		<br>	
                        		<span class="desc"><?=_("판매중인 몰")?></span>
                        		<? foreach ($data[cids] as $k=>$v){ if($v){ ?>
                        			<span class="small red">-<?=$r_cid[$v]?></span>
                        		<? }} ?>
                        		</div>
                        		<? }?>
                        	</td>
                        </tr>
                        <tr>
                        	<th><?=_("인쇄견적 안내")?></th>
                        	<td>
                        	<textarea name="extra_info" style="width:98%;height:50px;overflow:visible" pt="_pt_txt"><?=$data[extra_info]?></textarea>
                        	</td>
                        </tr>
                        </table>
                        </div>
                        <!--인쇄 견적 정보-->

                        <!--견적 정보-->
                        <div id="extra_print">
                        <table class="tb1">
                        <tr>
                            <th><?=_("견적종류선택")?></th>
                            <td>
                            <?
                            foreach ($r_est_print_product as $k=>$v)
                            {
                                if (!in_array($k, $extra_use[extra_print_product])) continue;
                            ?>
                            <input type="radio" name="extra_print_product" value="<?=$k?>" <?=$checked[extra_print_product][$k]?>><span class="absmiddle"><?=$v?></span><br>
                            <?
                            }
                            ?>
                            </td>
                        </tr>
                        <? if ($cfg_center[center_cid] == "minterpro")  {?>
                        <tr>
                            <th><?=_("견적종류선택")?></th>
                            <td>
                            <?
                            foreach ($r_inpro_print_goods_group as $k=>$v)
                            {
                            ?>
                            <input type="radio" name="print_goods_group" value="<?=$k?>" <?=$checked[print_goods_group][$k]?>><span class="absmiddle"><?=$v?></span><br>
                            <?
                            }
                            ?>
                            </td>
                        </tr>
                        <?  }   ?>                                                 
                        </table>
                        </div>
                        <!--견적 정보-->

                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품코드")?></label>
                     <div class="col-md-2">
                        <b style="margin-right:30px;"><?=($_GET[goodsno])?$_GET[goodsno]:_("자동생성");?></b>
                        <? if ($cid=="exmall"){ ?>
                        <?=_("업체코드")?> : <input type="text" class="form-control" name="shop_code" value="<?=$data[shop_code]?>"/>
                        <? } ?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("맵핑 상품코드")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="mapping_goodsno" value="<?=$data[mapping_goodsno]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품명")?></label>
                     <div class="col-md-6">
                        <input type="text" class="form-control" name="goodsnm" value="<?=$data[goodsnm]?>" required pt="_pt_txt"/>
                     </div>
                  </div>


                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("미리보기 화면 설정")?></label>
                     <div class="col-md-10">
                     	<input type="radio" name="preview_app_type" value="basic" <?=$checked['preview_app_type']['basic']?>><span class="absmiddle"><?=_("기본")?></span>
                     	<!--a href="javascript:popup('/skin/modern/help/preview1.htm',1000,600)">미리보기</a-->
                     	<input type="radio" name="preview_app_type" value="hsoft" <?=$checked['preview_app_type']['hsoft']?>><span class="absmiddle"><?=_("표지")?> Hard - <?=_("내지")?> Soft</span>
                     	<!--a href="javascript:popup('/skin/modern/help/preview3.htm',1000,600)">미리보기</a-->
                     	<input type="radio" name="preview_app_type" value="ssoft" <?=$checked['preview_app_type']['ssoft']?>><span class="absmiddle"><?=_("표지")?> Soft - <?=_("내지")?> Soft</span>
                     	<!--a href="javascript:popup('/skin/modern/help/preview4.htm',1000,600)">미리보기</a-->
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("제작사")?></label>
                     <div class="col-md-2">
                        <select name="rid" class="form-control" required>
                        <option value=""/> <?=_("제작사선택")?>
                        <? foreach ($r_rid as $k=>$v){ ?>
                        <option value="<?=$k?>" <?=$selected[rid][$k]?>/><?=$v?>
                        <? } ?>
                        </select>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("브랜드")?></label>
                     <div class="col-md-2">
                        <select name="brandno" class="form-control">
                        <option value=""/> <?=_("브랜드선택")?>
                        <? foreach ($r_brand as $k=>$v){ ?>
                        <option value="<?=$k?>" <?=$selected[brandno][$k]?>/><?=$v?>
                        <? } ?>
                        </select>
                     </div>
                  </div>

                  <!-- ==============================상세설명 JSON형식 (몰)============================== -->
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("상품설명(몰)")?>
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
			                  <? for ($i=0; $i < 5; $i++) { ?>
			                     <tr>
			                     	<td>
			                     		<input type="text" class="form-control" name="goods_desc[<?=$i?>][name]" value="<?=$data[goods_desc][$i][name]?>"/>
			                     	</td>
			   						   <td>
			   							  <input type="text" class="form-control" name="goods_desc[<?=$i?>][value]" value="<?=$data[goods_desc][$i][value]?>"/>
			   						   </td>
		                        </tr>
			                  <? } ?>
			                  </tbody>
			               </table>
			            </div>
                     </div>
                  </div>

                  <!--<div class="form-group">
                     <label class="col-md-2 control-label"><?=_("원산지")?></label>
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[summary]?><BR>
                        <input type="text" class="form-control" name="origin" value="<?=$data[origin]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("제조사")?></label>
                     <div class="col-md-10">
                        <?=_("센터")?> : <?=$data[search_word]?><BR>
                        <input type="text" class="form-control" name="maker" value="<?=$data[maker]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("제작기간안내")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="leadtime" value="<?=$data[leadtime]?>"/>
                        <div class="desc"><?=_("표기하지 않을 경우 노출되지 않습니다.")?></div>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("권장해상도")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="resolution" value="<?=$data[resolution]?>">
                     </div>
                  </div>-->

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품사이즈")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="goods_size" value="<?=$data[goods_size]?>">
                     </div>
                  </div>


                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("관리자메모")?></label>
                     <div class="col-md-10">
                        <textarea name="adminmemo" class="form-control" style="width:98%;height:50px;overflow:visible" pt="_pt_txt"><?=$data[adminmemo]?></textarea>
                     </div>
                  </div>

                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"><?=_("PodStation편집코드")?></label>
                     <div class="col-md-10">
                        <input type="radio" name="pods_use" value="0" class="absmiddle" style="height:12px" onclick="click_pods_div(0)" <?=$checked[pods_use][0]?>><span class="absmiddle"><?=_("일반상품")?></span>
                        <!--<input type="radio" name="pods_use" value="1" class="absmiddle" style="height:12px" onclick="click_pods_div(1)" <?=$checked[pods_use][1]?>><span class="absmiddle">PODs1.0편집기</span>-->
                        <input type="radio" name="pods_use" value="2" class="absmiddle" style="height:12px" onclick="click_pods_div(2)" <?=$checked[pods_use][2]?>><span class="absmiddle"><?=_("PODs2.0편집기")?></span>
                        <input type="radio" name="pods_use" value="3" class="absmiddle" style="height:12px" onclick="click_pods_div(3)" <?=$checked[pods_use][3]?>><span class="absmiddle"><?=_("PODs2.0 w편집기 사용")?></span>

                        <select name="pods_useid" class="absmiddle">
                        <option value="center" /> <?=_("센터아이디사용")?>
                        <option value="mall" <? if ($data[pods_useid]=="mall"){?>selected<?}?> /> <?=_("몰아이디사용")?>
                        </select>
                       
                        <div id="pods_div" <?if(!$data[podskind]){?>style="display:none"<?}?>>
                           <table class="tb1" style="margin:5px 0;">
                              <tr style="height:40px;">
                                 <th><?=_("상품코드")?></th>
                                 <td width="170">
                                 <input type="text" class="form-control" name="goods_podsno" value="<?=$data[podsno]?>" id="podsno" <?if($data[podsno]=="1"){?> readonly<?}?>/>
                                 <div id="podskind_str" style="margin-top:5px;"><?=$r_podskind[$data[podskind]]?></div>
                                 </td>
                                 <td>
                                    <span class="btn btn-danger btn-icon btn-circle btn_get_productid" onclick="_get_productid(1,'')" <?if($is_podskind20 || $data[pods_use]=="3"){?>style="display:none"<?}?>> <i class="fa fa-bars"></i></span>
                                    
                                    <!--<img id="btn_get_productid" src="../img/bt_check_s.png" onclick="_get_productid(1,'')" class="absmiddle hand" <?if($is_podskind20 || $data[pods_use]=="3"){?>style="display:none"<?}?>/>-->
                                    <span id="pods_userdataurl" style="display: none;">
                                       <input type="checkbox" name="pods_userdataurl_flag" class="absmiddle" style="width:11px;" value="1" <?if($data[pods_userdataurl_flag]){?>checked<?}?>> <?=_("사용자정보 연동(명함편집기)")?>
                                    </span>
                                 </td>
                              </tr>
                              <tr>
                                 <th><?=_("상품분류")?></th>
                                 <td><input type="text" class="form-control" name="podskind" id="podskind" value="<?=$data[podskind]?>" id="podskind" readonly/></td>
                                 <td>
                                 <? if ($cfg[skin_theme] == "M2") { ?>    
                                 <div class="stxt red">- <?=_("피규어 상품은  W편집기 사용으로 선택하고 상품분류를 '99999'입력. (추가옵션에 가족구성을 입력)")?></div>
                                 <? } ?>
                                 <div class="stxt red">- <?=_("PODs2.0편집기 수동입력")?>:
                                 <? foreach ($r_podskind20 as $v){ ?>
                                 <?=$v?>
                                 <? } ?>
                                 </div>
                                 </td>
                              </tr>
                              
                              <tr style="height:40px;" id="edit_type">
                                 <th><?=_("편집기 타입")?></th>
                                 <td colspan="2">
                                    <table>
                                       <tr>
                                          <td colspan="2">
                                             <span class="stxt red">
                                             - <?=_("해당 설정은 3040,3041,3050,3052,3055 편집기에만 적용되는 설정입니다.")?>
                                             </span>
                                          </td>
                                       </tr>
                                       <tr>
                                          <td>
                                             active - exe 방식
                                             <select name="editor_active_exe">
                                                <option value="N" <?=$checked[editor_active_exe][N]?>>선택안함</option>
                                                <option value="active" <?=$checked[editor_active_exe][active]?>>active 방식</option>
                                                <option value="exe" <?=$checked[editor_active_exe][exe]?>>exe 방식</option>
                                             </select>
                                             <!--
                                             <input type="radio" name="editor_active_exe" value="N" <?=$checked[editor_active_exe][N]?>> 선택안함<br>
                                             <input type="radio" name="editor_active_exe" value="active" <?=$checked[editor_active_exe][active]?>> active 방식<br>
                                             <input type="radio" name="editor_active_exe" value="exe" <?=$checked[editor_active_exe][exe]?>> exe 방식<br>
                                             -->
                                          </td>
                                          <td>
                                             <span class="stxt red">
                                             - <?=_("active, exe방식은 IE에서만 적용되는 설정입니다. IE이외의 브라우저는 설정에 상관없이 exe로 작동합니다.")?><br>
                                             - <?=_("최초 설정된 값이 없으면 기본값인 active 방식으로 편집기가 실행됩니다. 선택안함을 선택하시면 편집버튼이 나오지 않습니다.")?>
                                             </span>
                                          </td>
                                       </tr>
                                       <tr>
                                          <td>
                                             web 방식<br>
                                             <select name="editor_web">
                                                <option value="N" <?=$checked[editor_web][N]?>>선택안함</option>
                                                <option value="web" <?=$checked[editor_web][web]?>>web 방식</option>
                                             </select>
                                             <!--
                                             <input type="checkbox" name="editor_web" <?=$checked[editor_web][Y]?>> web 방식
                                             -->
                                          </td>
                                          <td>
                                             <span class="stxt red">
                                             - <?=_("web 방식으로 편집기를 실행합니다.")?><br>
                                             </span>
                                          </td>
                                       </tr>
                                    </table>
                                 </td>
                              </tr>
                              
                              <tr style="height:40px;">
                                 <th><?=_("기본페이지수")?></th>
                                 <td><input type="text" class="form-control" id="defaultpage" name="defaultpage" value="<?=$data[defaultpage]?>" <?if(!$is_podskind20){?>disabled<?}?>></td>
                                 <td><span class="stxt red">- <?=_("PODs2.0편집기 사용시 숫자입력")?></span></td>
                              </tr>
                              <tr style="height:40px;">
                                 <th><?=_("최소페이지수")?></th>
                                 <td><input type="text" class="form-control" id="minpage" name="minpage" value="<?=$data[minpage]?>" <?if(!$is_podskind20){?>disabled<?}?>></td>
                                 <td><span class="stxt red">- <?=_("PODs2.0편집기 사용시 숫자입력")?></span></td>
                              </tr>
                              <tr style="height:40px;">
                                 <th><?=_("최대페이지수")?></th>
                                 <td><input type="text" class="form-control" id="maxpage" name="maxpage" value="<?=$data[maxpage]?>" <?if(!$is_podskind20){?>disabled<?}?>></td>
                                 <td><span class="stxt red">- <?=_("PODs2.0편집기 사용시 숫자입력")?></span></td>
                              </tr>
                              <tr style="height:40px;">
                                 <th>TemplateSet ID</th>
                                 <td><input type="text" class="form-control" id="templatesetID" name="templatesetID" value="<?=$data[templateset_id]?>" <?if(!$is_podskind20){?>disabled<?}?>></td>
                                 <td><span class="stxt red">- TemplateSet ID <?=_("입력")?></span></td>
                              </tr>
                              <tr style="height:40px;" id="vidiobook_link_tr">
                                 <th><?=_("비디오북 링크 사용여부")?></th>
                                 <td>
                                    <select class="form-control" name="vidiobook_link" id="vidiobook_link">
                                    <option value="Y" <?if($data[vidiobook_link]!="N"){?>selected<?}?>><?=_("사용함")?></option> 
                                    <option value="N" <?if($data[vidiobook_link]=="N"){?>selected<?}?>><?=_("사용하지 않음")?></option>
                                    </select>
                                 </td>
                                 <td>
                                    <span class="stxt red">- <?=_("비디오북 편집기만 설정가능(편집기번호 : 25, 31,32)")?></span>
                                 </td>
                              </tr>
                           </table>
                        </div>
                     </div>
                  </div>
                  
                  <? if($data[podskind] == "3055") { ?>
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("커버혼용북 옵션설정")?></label>
                     <div class="col-md-10">
                        <table class="table table-bordered table-hover">
                        
                           <!--
                           <tr>
                              <td colspan="6">
                                 <span onclick="cover_ragne_opt()">추가</span>
                                 <span onclick="cover_ragne_opt_remove()">제거</span>
                              </td>
                           </tr>
                           -->
                           <tr>
                              <th><?=_("제어")?></th>
                              <!--<th class="col-md-3"><?=_("커버규격")?></th>-->
                              <th class="col-md-2"><?=_("커버규격")?></th>
                              <th><?=_("커버타입")?></th>
                              <th><?=_("용지 코드")?></th>
                              <th><?=_("용지 명칭")?></th>
                              <th><?=_("상품가격")?></th>
                              <th><?=_("페이지당 추가가격")?></th>
                              <th class="col-md-2"><?=_("커버규격 ID")?></th>
                           </tr>
                           <? if($cover_range_option) { foreach($cover_range_option as $key => $val) { ?>
                           <tr>
                              <td style="text-align: center;"><input type="radio" name="dummy_cover_ragne" on></td>
                              <!--
                              <td class="form-inline col-md-3" style="text-align: center;">
                                 <select name="cover_range1[]" id="cover_range1_<?=$cnt?>" onchange="make_cover_id('<?=$cnt?>', this);" class="form-control">
                                    <option value="">선택안함</option>
                                    <? foreach($r_cover_ragne as $v) { ?>
                                    <option value="<?=$v?>"><?=$v?></option>
                                    <? } ?>
                                 </select>
                                 X
                                 <select name="cover_range2[]" id="cover_range2_<?=$cnt?>"  onchange="make_cover_id('<?=$cnt?>', this);" class="form-control">
                                    <option value="">선택안함</option>
                                    <? foreach($r_cover_ragne as $v) { ?>
                                    <option value="<?=$v?>"><?=$v?></option>
                                    <? } ?>
                                 </select>
                              </td>
                              -->
                              <td class="col-md-2">
                                 <input type="text" name="cover_range[]" id="cover_range_<?=$key?>" onblur="make_cover_id('<?=$key?>');" class="form-control" value="<?=$val[cover_range]?>">
                              </td>

                              <td>
                                 <select name="cover_type[]" id="cover_type_<?=$key?>" onchange="make_cover_id('<?=$key?>', this);" class="form-control">
                                    <option value=""><?=_("선택안함")?></option>
                                    <? foreach($r_cover_type as $k => $v) { ?>
                                    <option value="<?=$k?>" <? if($k == $val[cover_type]) { ?> selected <? } ?> ><?=$v?></option>
                                    <? } ?>
                                 </select>
                              </td>
                              <td><input type="text" name="cover_paper_code[]"    value="<?=$val[cover_paper_code]?>" id="cover_paper_<?=$key?>" onblur="make_cover_id('<?=$key?>');" class="form-control"></td>
                              <td><input type="text" name="cover_paper_name[]"    value="<?=$val[cover_paper_name]?>" class="form-control"></td>
                              <td><input type="text" name="cover_goods_price[]"   value="<?=$val[cover_goods_price]?>" class="form-control"></td>
                              <td><input type="text" name="cover_page_addprice[]" value="<?=$val[cover_page_addprice]?>" class="form-control"></td>
                              <td class="col-md-2"><input type="text" name="cover_id[]" value="<?=$val[cover_id]?>" id="cover_id_<?=$key?>" onclick="make_cover_id('<?=$key?>');" class="form-control" readonly></td>
                           </tr>
                           <? } } ?>
                        </table>
                     </div>
                     
                     <label class="col-md-2 control-label"><?=_("커버혼용북 규격설정")?></label>
                     <div class="col-md-10">
                        <table class="table table-bordered table-hover">
                           <!--
                           <tr>
                              <td colspan="6">
                                 <span onclick="cover_ragne_opt()">추가</span>
                                 <span onclick="cover_ragne_opt_remove()">제거</span>
                              </td>
                           </tr>
                           -->
                           <tr>
                              <th><?=_("제어")?></th>
                              <th><?=_("커버규격ID")?></th>
                              <th><?=_("페이지수")?></th>
                              <th><?=_("가로규격(mm)")?></th>
                              <th><?=_("세로규격(mm)")?></th>
                              <th><?=_("책등(mm)")?></th>
                              <th><?=_("좌싸바리(mm)")?></th>
                              <th><?=_("우싸바리(mm)")?></th>
                              <th><?=_("상싸바리(mm)")?></th>
                              <th><?=_("하싸바리(mm)")?></th>
                           </tr>
                           <? if($cover_range_standard) { foreach($cover_range_standard as $key => $val) { ?>
                           <tr>
                              <td style="text-align: center;"><input type="radio" name="dummy_cover_ragne"></td>
                              <td><input type="text" name="cover_id_range[]" value="<?=$val[cover_id]?>" class="form-control"></td>
                              <td><input type="text" name="page[]"           value="<?=$val[page]?>"     class="form-control"></td>
                              <td><input type="text" name="width[]"          value="<?=$val[width]?>"    class="form-control"></td>
                              <td><input type="text" name="height[]"         value="<?=$val[height]?>"   class="form-control"></td>
                              <td><input type="text" name="back[]"           value="<?=$val[back]?>"     class="form-control"></td>
                              <td><input type="text" name="left[]"           value="<?=$val[left]?>"     class="form-control"></td>
                              <td><input type="text" name="right[]"          value="<?=$val[right]?>"    class="form-control"></td>
                              <td><input type="text" name="up[]"             value="<?=$val[up]?>"       class="form-control"></td>
                              <td><input type="text" name="down[]"           value="<?=$val[down]?>"     class="form-control"></td>
                           </tr>
                           <? } } ?>
                        </table>
                     </div>
                  </div>
                  <? } ?>
                  

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("간략설명")?></label>
                     <div class="col-md-10">
                     	<input type="text" class="form-control" name="summary"  value="<?=$data[summary]?>"/>
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
                        <input type="text" class="form-control" name="search_word"  value="<?=$data[search_word]?>"/>
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
                     <label class="col-md-2 control-label"><?=_("상품특이사항1")?></label>         
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="etc1"  value="<?=$data[etc1]?>"/>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품특이사항2")?></label>         
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="etc2"  value="<?=$data[etc2]?>"/>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품특이사항3")?></label>         
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="etc3"  value="<?=$data[etc3]?>"/>
                     </div>
                  </div>-->
                  
                  
                  
                  <script>
                  function click_pods_div(mode){
                     if (mode != "<?=$data[pods_use]?>") {
                        $j('#podsno').val('');
                        $j('#podskind').val('');
                        $j('#podskind_str').html('');
                        $j("#defaultpage").val('');
                        $j('#minpage').val('');
                        $j("#maxpage").val('');
                        $j("#templatesetID").val('');
                     }

                     $j("#defaultpage").attr("disabled",true).attr("readonly",true).removeAttr("required");
                     $j("#minpage").attr("disabled",true).attr("readonly",true).removeAttr("required");
                     $j("#maxpage").attr("disabled",true).attr("readonly",true).removeAttr("required");
                     $j("#templatesetID").attr("disabled",true).attr("readonly",true).removeAttr("required");
                  
                     if (mode=="0"){
                     	$j(".btn_get_productid").css("display","none");
                        $j('#pods_div').slideUp();
                     }
                     if (mode=="1"){
                        $j(".btn_get_productid").css("display","");
                        $j("#vidiobook_link_tr").css("display","");
                        $j("#podsno").attr("readonly",true);
                        $j("#podskind").attr("readonly",true);
                        $j('#pods_div').slideDown()
                     }
                     if (mode=="2" || mode=="3"){
                        $j(".btn_get_productid").css("display","none");
                        $j("#vidiobook_link_tr").css("display","none");
                        $j("#podsno").removeAttr("readonly");
                        $j("#podskind").removeAttr("readonly");
                        $j("#edit_type").css("display","none");
                        if (mode == "2"){
                           $j("#defaultpage").removeAttr("disabled").attr("required","required").removeAttr("readonly");
                           $j("#minpage").removeAttr("disabled").attr("required","required").removeAttr("readonly");
                           $j("#maxpage").removeAttr("disabled").attr("required","required").removeAttr("readonly");
                           $j("#templatesetID").removeAttr("disabled").removeAttr("readonly");
                           $j("#edit_type").css("display","");
                        }
                        $j('#pods_div').slideDown();
                     }

                     //wpod 명함 편집기 연동시 userDataUrl 연동 여부 by 2014.04.16 kdk
                     //유치원 시즌2(prtty) 메크로 데이터 연동 여부 / 15.06.15 / kjm
                     //달력편집기 로고 연동으로 인해 1.0 사용 / 15.09.15 / kjm
                     if(mode == "3" || mode == "2" || mode == "1") {
                        $j("#pods_userdataurl").show();
                        $j("#input_div").hide();
                     } else {
                        $j("#pods_userdataurl").hide();
                        $j("#input_div").show();
                     }
                  }
                  
                  <?if($data[pods_use] != "0"){?>
                    click_pods_div(<?=$data[pods_use]?>);
                  <?}?>

                  </script>

                  <script>

                  $j(function(){
                     $j("input[type=radio][name=pods_use]").click(function(){
                        if ($j(this).val()=="0"){
                           $j("#pods_div").slideUp();
                           $j("select","#pods_div").attr("disabled",true);
                           $j("input","#pods_div").attr("disabled",true);
                        } else {
                           $j("#pods_div").slideDown();
                           $j("select","#pods_div").attr("disabled",false);
                           $j("input","#pods_div").attr("disabled",false);
                        }
                     });

                     $j("input[type=radio][name=privatecid][checked]").trigger("click");
                     $j('#category2_tr').show();
                     $j("select[name=pods_useid]").val("mall");
                     $j("select[name=pods_useid]").hide();

                     //면적당 계산 여부 추가 / 16.11.03 / kdk
                     $j("input[type=checkbox][name=price_type_size_flag]").click(function(){
                        var extra_price_type = $j("input[type=checkbox][name=price_type_size_flag]").is(":checked") ? "SIZE" : "CNT";
                        $j("input:[name=extra_price_type]").val(extra_price_type);
                     });
                  });

                  var product_id_mode = 0;
                  var product_id_obj;
                  function _get_productid(mode,obj){
                     product_id_mode = mode;
                     product_id_obj = obj;

                     if ($j("select[name=pods_useid]").val()=="center"){
                        var siteid = "<?=$cfg_center[podsid]?>";
                        var userid = "<?=$cfg_center[podsid]?>";
                     } else {
                        var siteid = "<?=$mall_info[siteid]?>";
                        var userid = "<?=$mall_info[podsid]?>";
                     }
                     var actionurl = "http://<?=$_SERVER[HTTP_HOST]?>/_sync/get_productid.php";
                     var url = "http://<?=PODS10_DOMAIN?>/ProductInfo30/code_mapping.aspx?siteid="+siteid+"&userid="+userid+"&actionurl="+actionurl+"&addinfo=Y";

                     popupLayer(url,'',770,450);
                  }
                  </script>

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
                        <input type="checkbox" name="input_str" value="1" <?=$checked[input_str][1]?>/> <?=_("문구입력 사용")?><br>
                        <input type="checkbox" name="input_file" value="1" <?=$checked[input_file][1]?>/> <?=_("파일업로드 사용")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("세트수량")?></label>
                     <div class="col-md-2">
                     	<input type="text" class="form-control" name="goods_set_ea"  value="<?=$data[goods_set_ea]?>"/>
                     </div>
                  </div>

                  <!-- ==============================판매 정보============================== -->

                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("판매 정보")?></div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("판매여부")?></label>
                     <div class="col-md-2">
                        <? foreach ($r_goods_state as $k=>$v){ ?>
                        <input type="radio" name="state" class="form-contorl" value="<?=$k?>" <?=$checked[state][$k]?>/> <?=$v?>
                        <? } ?>
                        <!--<?=$r_goods_state[$data[state]]?>-->
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
                     <div class="col-md-8">
                     <input type="radio" name="shiptype" value="0" <?=$checked[shiptype][0]?>/> 일반배송
                     <input type="radio" name="shiptype" value="1" <?=$checked[shiptype][1]?>/> 무료배송
                     <input type="radio" name="shiptype" value="2" <?=$checked[shiptype][2]?>/> 개별배송
                    
                     <div class="desc">
                        <div id="shipping_0" style="display:none">제작사의 배송정책을 따릅니다.</div>
                        <div id="shipping_1" style="display:none">배송비가 <b>무료</b>입니다 (판매자부담)</div>
                        <div id="shipping_2" style="display:none">
                        개별배송비 <input type="text" name="shipprice" value="<?=$data[shipprice]?>" class="w80" pt="_pt_numplus"/> 원 
                        (배송비원가 <input type="text" name="oshipprice" value="<?=$data[oshipprice]?>" class="w80" pt="_pt_numplus"/> 원)
                        </div>
                     </div>
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
                        <input type="checkbox" name="nodp" value="1" <?=$checked[nodp][1]?>> <span class="desc">(<?=_("체크시 진열되지 않습니다.")?>)</span>
                     </div>
                  </div>



                  <!-- ==============================가격/재고 관리============================== -->

                  <div class="form-group">
                     <div class="col-md-12"> > > <?=_("가격/재고 관리")?></div>
                  </div>

                  <div class="form-group form-inline">
                     <label class="col-md-2 control-label"><?=_("원가")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="oprice" value="<?=$data[oprice]?>" pt="_pt_numplus"/>
                     </div>

                     <label class="col-md-2 control-label"><?=_("공급가")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="sprice" value="<?=$data[sprice]?>" pt="_pt_numplus"/>
                     </div>
                  </div>

                  <div class="form-group form-inline">
                     <label class="col-md-2 control-label"><?=_("권장소비자가")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="cprice" value="<?=$data[cprice]?>" pt="_pt_numplus"/>
                     </div>

                     <label class="col-md-2 control-label"><?=_("판매가")?></label>
                     <div class="col-md-2">
                        <input type="text" class="form-control" name="price" value="<?=$data[price]?>" pt="_pt_numplus"/>
                     </div>
                  </div>

                  <div class="form-group form-inline">
                     <label class="col-md-2 control-label"><?=_("페이지추가가격")?></label>
                     <div class="col-md-10">
                        <?=_("원가")?> : <input type="text" class="form-control" name="opageprice" value="<?=$data[opageprice]?>" pt="_pt_numplus" size="10"/>
                        <?=_("공급가")?> : <input type="text" class="form-control" name="spageprice" value="<?=$data[spageprice]?>" pt="_pt_numplus" size="10"/>
                        <?=_("판매가")?> : <input type="text" class="form-control" name="pageprice" value="<?=$data[pageprice]?>" pt="_pt_numplus" size="10"/>
                        <span class="desc"><?=_("정해진 페이지량을 초과하여 편집시에 추가되는 가격입니다.(ex : 가변포토북)")?></span>
                     </div>
                  </div>

                  <div class="form-group form-inline">
                     <label class="col-md-2 control-label"><?=_("총재고")?></label>
                     <div class="col-md-10">
                        <input type="text" class="form-control" name="totstock" value="<?=$data[totstock]?>" pt="_pt_num"/>
                        <span class="desc"><?=_("옵션을 사용하는 경우 임의 수정이 불가능해집니다.")?></span>
                     </div>
                  </div>

                  <!-- ==============================옵션별 가격/재고 관리============================== -->

                  <div class="form-group option-pro">
                     <div class="col-md-12"> > > <?=_("옵션별 가격/재고 관리")?> [<?=_("명함상품은 반드시 1차옵션에 추가금액을 넣어주셔야 편집기와 주문금액이 연동됩니다. / 옵션추가 > center관리자 에서 등록하세요")?>]</span></div>
                  </div>

                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"><?=_("옵션유무")?></label>
                     <div class="col-md-10">
                        <input type="radio" class="radio-inline" name="useopt" value="0" <?=$checked[useopt][0]?>/>
                        <?=_("옵션사용안함")?>
                        <input type="radio" class="radio-inline" name="useopt" value="1" <?=$checked[useopt][1]?>/>
                        <?=_("옵션사용")?>
                        <b class="desc hand" id="easy_setopt" id="mod_opt_btn">[<?=_("쉬운세팅")?>]</b>
                     </div>
                  </div>

                  <div class="form-group option-pro" id="setopt_div">
                     <label class="col-md-2 control-label"><?=_("옵션제어")?></label>
                     <div class="col-md-10 form-inline">
                        <b><?=_("1차 옵션명")?></b> : <input type="text" class="form-control" name="optnm1" value="<?=$data[optnm1]?>"/>
                        <span class="desc"><?=_("선택되어야할 옵션의 이름을 정해주세요. 공란일 경우 사용자에게는 '옵션1' 로 노출이 됩니다.")?></span><br><br>
                        <b><?=_("2차 옵션명")?></b> : <input type="text" class="form-control" name="optnm2" value="<?=$data[optnm2]?>"/>
                        <span class="desc"><?=_("선택되어야할 옵션의 이름을 정해주세요. 공란일 경우 사용자에게는 '옵션2' 로 노출이 됩니다.")?></span><br><br>
                        <span class="btn btn-warning btn-icon btn-circle" onclick="add_more_opt()"><i class="fa fa-plus"></i></span>
                        <span class="btn btn-warning btn-icon btn-circle" onclick="remove_opt()"><i class="fa fa-minus"></i></span>
                     </div>
                  </div>
                  
                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <table id="opt_tb" class="table table-hover table-bordered">
                           <thead>
                              <th><?=_("제어")?></th>
                              <th><?=_("1차옵션")?></th>
                              <th><?=_("2차옵션")?></th>
                              <th><?=_("편집코드")?></th>
                              <th><?=_("편집옵션코드")?></th>
                              <th><?=_("추가가격")?></th>
                              <th><?=_("옵션추가공급가")?></th>
                              <th><?=_("추가원가")?></th>
                              <th><?=_("권장소비자가")?></th>
                              <th><?=_("재고")?></th>
                              <th><?=_("노출여부")?></th>
                              <?if($_GET[goodsno]){?><th><?=_("편집기")?></th><?}?>
                           </thead>
                           
                           <tbody>
                              <? foreach ($r_opt as $k=>$v){ ?>
                              <tr align="center">
                                 <td><input type="radio" name="dummy"/></td>
                                 <td><input type="text" class="form-control" name="opt1[<?=$v[optno]?>]" value="<?=$v[opt1]?>" pt="_pt_txt"/></td>
                                 <td><input type="text" class="form-control" name="opt2[<?=$v[optno]?>]" value="<?=$v[opt2]?>" pt="_pt_txt"/></td>
                                 <td><input type="text" class="form-control" name="podsno[<?=$v[optno]?>]" value="<?=$v[podsno]?>" /><a class="btn btn-success btn-icon btn-circle btn_get_productid" onclick="_get_productid(0,$j(this).prev())" <?if($is_podskind20 || $data[pods_use]=="0" || $data[pods_use]=="3"){?>style="display:none"<?}?>><i class="fa fa-repeat"></i></a></td>
                                 <td><input type="text" class="form-control" name="podoptno[<?=$v[optno]?>]" value="<?=$v[podoptno]?>" /></td>
                                 <td><input type="text" class="form-control" name="aprice[<?=$v[optno]?>]" value="<?=$v[aprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="asprice[<?=$v[optno]?>]" value="<?=$v[asprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="aoprice[<?=$v[optno]?>]" value="<?=$v[aoprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="opt_cprice[<?=$v[optno]?>]" value="<?=$v[opt_cprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="stock[<?=$v[optno]?>]" value="<?=$v[stock]?>" pt="_pt_numplus"/></td>
                                 <td>
                                 <select name="opt_view[<?=$v[optno]?>]">
                                 <option value="0">Y
                                 <option value="1" <?if($v[opt_view]){?>selected<?}?>>N
                                 </select>
                                 </td>
                                 <?if($_GET[goodsno]){?><td><a href="javascript:void(0);" onclick="popupLayer('../../module/popup_calleditor.php?mode=edit&goodsno=<?=$data[goodsno]?>&productid=<?=$data[podsno]?>&optionid=<?=$v[podoptno]?>')"><?=_("호출")?></a></td><?}?>
                              </tr>
                              <? } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  
                  
                  
                  <!-- ==============================인화 옵션============================== -->
                  
                  <div class="form-group option-pro">
                     <div class="col-md-12"> > > <?=_("인화옵션")?></span></div>
                  </div>
                  
                  <div class="form-group option-pro" id="setopt_div">
                     <label class="col-md-2 control-label"><?=_("인화옵션설정")?></label>
                     <div class="col-md-10 form-inline">
                        <span class="desc"><?=_("인화상품의 경우 인화종류별 금액을 설정해주셔야 올바른 주문을 접수받으실 수 있습니다.")?></span><br><br>
                        <span class="desc"><?=_("인화옵션명은 PodStation에 등록된 인화옵션명과 동일하게 입력해주셔야 합니다.")?></span><br><br>
                        <span class="btn btn-warning btn-icon btn-circle" onclick="add_printopt()"><i class="fa fa-plus"></i></span>
                        <span class="btn btn-warning btn-icon btn-circle" onclick="remove_printopt()"><i class="fa fa-minus"></i></span>
                     </div>
                  </div>
                  
                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <table id="printopt_tb" class="table table-hover table-bordered" <? if (!$r_printopt) { ?>style="display:none"<? } ?>>
                           <thead>
                              <th><?=_("제어")?></th>
                              <th><?=_("옵션명")?></th>
                              <th><?=_("인화옵션크기")?></th>
                              <th><?=_("인화옵션판매가")?></th>
                              <th><?=_("인화옵션공급가")?></th>
                              <th><?=_("인화옵션원가")?></th>
                           </thead>
                           
                           <tbody>
                              <? foreach ($r_printopt as $k=>$v) { ?>
                              <tr align="center">
                                 <td><input type="radio" name="dummy_printopt"/></td>
                                 <td><input type="text" class="form-control" name="printoptnm[]" value="<?=$v[printoptnm]?>" pt="_pt_txt"/></td>
                                 <td><input type="text" class="form-control" name="print_size[]" value="<?=$v[print_size]?>" pt="_pt_txt"/></td>
                                 <td><input type="text" class="form-control" name="print_price[]" value="<?=$v[print_price]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="print_sprice[]" value="<?=$v[print_sprice]?>" pt="_pt_numplus"/></td>
                                 <td><input type="text" class="form-control" name="print_oprice[]" value="<?=$v[print_oprice]?>" pt="_pt_numplus"/></td>
                              </tr>
                              <? } ?>
                           </tbody>
                        </table>
                     </div>
                  </div>



                  <!-- ==============================추가 옵션============================== -->

                  <div class="form-group option-pro">
                     <div class="col-md-12"> > > <?=_("추가옵션")?></div>
                  </div>

                  <div class="form-group option-pro">
                     <label class="col-md-2 control-label"><?=_("추가옵션설정")?></label>
                     <div class="col-md-10">
                        <!--<img src="../img/bt_add_opt_add_s.png" align="absmiddle" class="hand" onclick="add_addoptbox()">-->
                        <span type="button" class="btn btn-primary" onclick="add_addoptbox()"><?=_("옵션추가")?></span>
                        <!--<span type="button" class="btn btn-primary" onclick="window.open('img_addopt_popup.php?goodsno=<?=$_GET[goodsno]?>','','scrollbars=1,toolbar=no,status=no,resizable=yes,menubar=no');"><?=_("옵션이미지추가")?></span><br><br>--></br>
                        
                        <div id="addoptbox_div">
                        
                        <? if ($r_addopt_bundle) foreach ($r_addopt_bundle as $k=>$v){ ?>
                        <div class="addoptbox_div" addoptbox_idx="<?=$k?>">
                        <table class="addoptbox" class="table table-striped table-bordered">
                        <tr>
                           <th><?=_("추가옵션묶음명")?></th>
                           <td class="form-inline">
                              <input type="text" name="addopt_bundle_name[<?=$k?>]" class="form-control" value="<?=$v[addopt_bundle_name]?>" pt="_pt_txt" required/>
                              <span><?=_("필수")?></span>
                              <input type="checkbox" name="addopt_bundle_required[<?=$k?>]" class="check-inline" style="width:11px;" value="1" <?if($v[addopt_bundle_required]){?>checked<?}?>>
                           </td>
                           <td width="105" align="center">
                              <select name="addopt_bundle_view[<?=$k?>]" class="form-control">
                                 <option value="0"><?=_("노출")?>
                                 <option value="1" <?if($v[addopt_bundle_view]){?>selected<?}?>><?=_("숨김")?></select>
                           </td>
                           <td width="124" align="center">
                              <img src="../img/bt_optadd_s.png" class="hand absmiddle" onclick="add_addopt(this)"><img src="../img/bt_del_group_s.png" class="hand absmiddle" onclick="remove_addoptbox(this)"/>
                           </td>
                        </tr>
                        </table><br>
                     
                        <table class="table table-striped table-bordered">
                        <tr>
                           <th><?=_("옵션명")?></th>
                           <th><?=_("옵션판매가")?></th>
                           <th><?=_("옵션공급가")?></th>
                           <th><?=_("옵션원가")?></th>
                           <th><?=_("권장소비자가")?></th>
                           <th><?=_("맵핑옵션")?></th>
                           <th><?=_("노출여부")?></th>
                           <th><?=_("삭제")?></th>
                        </tr>
                        <? foreach ($v[addopt] as $k2=>$v2){ ?>
                        <tr align="center">
                           <td><input type="text" class="form-control" name="addoptnm[<?=$k?>][<?=$k2?>]" value="<?=$v2[addoptnm]?>" pt="_pt_txt"/></td>
                           <td><input type="text" class="form-control" name="addopt_aprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_aprice]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="addopt_asprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_asprice]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="addopt_aoprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_aoprice]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="addopt_cprice[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_cprice]?>" pt="_pt_numplus"/></td>
                           <td><input type="text" class="form-control" name="addopt_mapping_no[<?=$k?>][<?=$k2?>]" value="<?=$v2[addopt_mapping_no]?>"/></td>
                           <td><select class="form-control" name="addopt_view[<?=$k?>][<?=$k2?>]"><option value="0"><?=_("노출")?><option value="1" <?if($v2[addopt_view]){?>selected<?}?>><?=_("숨김")?></select></td>
                           <td><button type="button" class="btn btn-sm btn-success" onclick="remove_addopt(this)"><?=_("삭제")?></button></td>
                        </tr>
                        <? } ?>
                        </table>
                     
                        </div>
                        <? } ?>
                        
                        </div>
                     </div>
                  </div>
                  
                  
                  
                  
                  
                  <!-- ==============================이미지설정============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("이미지 설정")?>
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
                        <div class="col-md-6 form-inline">
                           <input type="file" class="form-control" name="img[]">
                           <? if ($data[img][$i]){ ?>
                              <img src="../img/bt_preview.png" align="absmiddle" onclick="vLayer(this.nextSibling)" class="hand absmiddle"/><div style="display:none;"><img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/l/<?=$data[goodsno]?>/<?=$data[img][$i]?>"></div>
                           <? } ?>
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
                        <div class="col-md-6 form-inline">
                           <input type="file" class="form-control" name="listimg" id="listimg">
                           <? if ($data[listimg]){ ?>
                           <img src="../img/bt_preview.png" align="absmiddle" onclick="vLayer(this.nextSibling)" class="hand absmiddle"/><div style="display:none;"><img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/s/<?=$data[goodsno]?>/<?=$data[listimg]?>"></div>
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
                        <div class="col-md-6 form-inline">
                           <input type="file" class="form-control" name="listimg_w" id="listimg_w">
                           <? if ($data[listimg_w]){ ?>
                           <img src="../img/bt_preview.png" align="absmiddle" onclick="vLayer(this.nextSibling)" class="hand absmiddle"/><div style="display:none;"><img src="http://<?=$cfg_center[host]?>/data/goods/<?=$cid?>/s/<?=$data[goodsno]?>/<?=$data[listimg_w]?>"></div>
                           <? } ?>
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-2">
                           <input type="checkbox" name="dellistimg_w"><span><?=_("삭제")?></span>
                        </div>
                     </div>

                  </div>
                  
                  
                  <!-- ==============================상세설명(몰)============================== -->
                  
                  <div class="form-group">
                     <div class="col-md-12">
                        > > <?=_("상세설명(몰)")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <textarea name="desc" style="width:100%;height:400px" id="desc" type="editor"><?=stripslashes($data[desc])?></textarea><p>
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
                        <button type="submit" class="btn btn-sm btn-success"><?=_("수정")?></button>
                        <button type="button" class="btn btn-sm btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>


<!--
<script src="../assets/plugins/DataTables-1.9.4/js/jquery.dataTables.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
-->
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/smarteditor/editorStart.js" charset="utf-8"></script>
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
        	var desc = $j("#desc").val();
        	$j("#desc").val(Base64.encode(desc));
            return form_chk(formObj);
        } catch(e) {
        	return false;
        }
    }
    return false;
}
</script>

<script>
/* 인쇄 견적 정보 */
$j("#pack").hide();
$j("#extra").hide();
$j("#extra_print").hide();
$j("#product_etc").show();
$j("#pods_add").hide();
$j(".option-pro").show();

function click_group_div(mode) {
    if (mode=="30"){//인쇄견적
        $j("#extra_print").hide();
        $j("#pack").hide();
        $j("#extra").show();
        $j("#extra_print").hide();
        $j("#product_etc").show();
        $j("#pods_add").show();
        $j(".option-pro").show();
    }
    else if(mode=="40"){//결제전용상품
        $j("#pack").hide();
        $j("#extra").hide();
        $j("#extra_print").hide();
        $j("#product_etc").hide();
        $j("#pods_add").hide();
        $j(".option-pro").show();
    }   
    else if(mode=="50"){//패키지상품
        $j("#pack").show();
        $j("#extra").hide();
        $j("#extra_print").hide();
        $j("#product_etc").hide();
        $j("#pods_add").hide();
        $j(".option-pro").hide();
    }
    else if(mode=="60"){//견적상품
        $j("#pack").hide();
        $j("#extra").hide();
        $j("#extra_print").show();
        $j("#product_etc").hide();
        $j("#pods_add").hide();
        $j(".option-pro").show();
    }
    else {//일반상품
        $j("#pack").hide();
        $j("#extra").hide();
        $j("#extra_print").hide();
        $j("#product_etc").show();
        $j("#pods_add").hide();
        $j(".option-pro").show();
    }
}
<?if($data[goods_group_code] != ""){?>
  click_group_div('<?=$data[goods_group_code]?>');
<?}?>
/* 인쇄 견적 정보 */

/* 프리셋스타일선택 */
function click_preset_div(mode) {
	$j(".preset_div").hide();
	$j("#preset_"+ mode).show();
	//$j("#preset_"+ mode + " input:radio[name='extra_preset']").attr("checked",true);
	
	//신규일 경우 첫번째 프리셋 선택되도록
	<?if($_GET[mode] != "modify" && !$_GET[goodsno]){?>
		$j("#preset_"+ mode +" input:radio[name='extra_preset']").eq(0).attr("checked", true);
	<?}?>
}
<?if($extra_preset_div != ""){?>
  click_preset_div('<?=$extra_preset_div?>');
<?}?>
/* 프리셋스타일선택 */

/* 인쇄견적 옵션설정 자동견적결제 click_auto_pay_div() */
function click_auto_pay_div() {
	if($j("input:checkbox[name='extra_auto_pay_flag_']").is(':checked')) {
		$j("#msg_auto_pay").hide();
		//자동견적결제를 체크하면 견적가격노출은 체크된 상태에서 비활성화.
		$j("input:checkbox[name='extra_price_view_flag_']").attr("checked", true);
		$j("input:checkbox[name='extra_price_view_flag_']").attr("disabled", true);
		
		$j("#extra_auto_pay_flag").val("1");
		$j("#extra_price_view_flag").val("1");
	}
	else {
		$j("#msg_auto_pay").show();
		//자동견적결제를 체크해제하면 견적가격노출은 체크해제한 상태에서 활성화.
		$j("input:checkbox[name='extra_price_view_flag_']").attr("checked", false);
		$j("input:checkbox[name='extra_price_view_flag_']").attr("disabled", false);
		
		$j("#extra_auto_pay_flag").val("0");
		$j("#extra_price_view_flag").val("0");
	}
}
/* 인쇄견적 옵션설정 자동견적결제 click_auto_pay_div() */

/* 인쇄견적 옵션설정 견적가격노출 click_price_view() */
function click_price_view() {
	if($j("input:checkbox[name='extra_price_view_flag_']").is(':checked')) {
		$j("#extra_price_view_flag").val("1");
	}
	else {
		$j("#extra_price_view_flag").val("0");
	}
}
/* 인쇄견적 옵션설정 견적가격노출 click_price_view() */

/* 수정이면 인쇄견적/스튜디오견적 정보 수정 불가*/
<?if($_GET[mode] == "modify" && $_GET[goodsno]){?>
	var input = $j("#extra input:radio[name^='extra_']");
	if($j(input).length > 0) {
		$j(input).attr("disabled","disabled")
	}

	input = $j("#extra_studio input:radio[name^='extra_']");
	if($j(input).length > 0) {
		$j(input).attr("disabled","disabled")
	}
	
	//자동견적결제,견적가격노출 수정 불가.
	input = $j("#extra input:checkbox[name^='extra_']");
	if($j(input).length > 0) {
		$j(input).attr("disabled","disabled")
	}

	//견적가격(부가세 계산 방식) 예외.
	input = $j("#extra input:radio[name='extra_price_vat_flag']");
	if($j(input).length > 0) {
		$j(input).removeAttr("disabled")
	}
	
	//인쇄견적/스튜디오견적이면 상품 종류 수정 불가
	<?if($data[goods_group_code] == "20" || $data[goods_group_code] == "30"){?>
		input = $j("input:radio[name^='goods_group_code']");
		if($j(input).length > 0) {
			$j(input).attr("disabled","disabled")
		}
	<?}?>	
<?}?>
/* 수정이면 인쇄견적 정보 수정 불가*/

function make_cover_id(num, j){
   
   var arr = new Array();
   /*
   if($j("#cover_range1_"+num+" option:selected").val() && $j("#cover_range2_"+num+" option:selected").val()){
      var range = $j("#cover_range1_"+num+" option:selected").val()+"X"+$j("#cover_range2_"+num+" option:selected").val()
      arr.push(range);
   } else if($j("#cover_range1_"+num+" option:selected").val()){
      arr.push($j("#cover_range1_"+num+" option:selected").val());
   } else if($j("#cover_range2_"+num+" option:selected").val())
      arr.push($j("#cover_range2_"+num+" option:selected").val());
   */
   if($j("#cover_range_"+num).val())
      arr.push($j("#cover_range_"+num).val());

   if($j("#cover_type_"+num+" option:selected").val())
      arr.push($j("#cover_type_"+num+" option:selected").val());
      
   if($j("#cover_paper_"+num).val())
      arr.push($j("#cover_paper_"+num).val());

   var cover_id = arr.join("_");
   

   $j('#cover_id_'+num).val(cover_id);
}
</script>

<? include "goods_r.js.php"; ?>
<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>
