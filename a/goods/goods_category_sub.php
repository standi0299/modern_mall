<?

include "../_pheader.php";

$m_goods = new M_goods();
$r_addtion_goods_kind = array("hot");

if ($_GET[catno]) $data = $db->fetch("select * from exm_category where cid = '$cid' and catno = '$_GET[catno]'");
else $data[catnm] = _("전체분류");

if (!is_numeric($data[cells])) {
   $data[cells] = $cfg[cells];
   $data[rows] = $cfg[rows];
}
list($data[cnt]) = $db->fetch("select count(*) from exm_goods_link where cid = '$cid' and catno like '$data[catno]%'",1);

//20150803 / minks / 모바일전용 카테고리 사용여부 추가
if (!$data[is_mobile]) $checked[is_mobile][0] = "checked";
$checked[is_mobile][$data[is_mobile]] = "checked";

foreach ($_r_mdn_goodslist_extra_kind as $_r_k=>$_r_v) {
	if (in_array($_r_k, $r_addtion_goods_kind)) {
		$addWhere = "where cid='$cid' and addtion_key_kind='C' and addtion_key='$data[catno]' and addtion_goods_kind='$_r_k'";
		$data2 = $m_goods->getAddtionGoodsItem($cid, $addWhere);
		if (!$data2[regist_flag]) $data2[regist_flag] = "N";
		
		$r_addtion_goods_data[$_r_k] = $_r_v;
		$r_addtion_goods_data[$_r_k][flag] = $data2[regist_flag];
		$checked[$_r_k."_flag"][$data2[regist_flag]] = "checked";
	}
}

//20150803 / minks / 모바일전용 카테고리 사용여부 추가
if (!$data[is_mobile]) $checked[is_mobile][0] = "checked";
$checked[catmain][$data[catmain]] = $checked[is_url][$data[is_url]] = $checked[is_intro][$data[is_intro]] = $checked[is_mobile][$data[is_mobile]] = "checked";
$selected[url_target][$data[url_target]] = "selected";

$selected[goods_list][$data[goods_list]] = "selected";
$selected[goods_view][$data[goods_view]] = "selected";

$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

//상품진열시 사용자정의 이미지가로사이즈(width) x 이미지세로사이즈(height) 2016.03.08 by kdk
	
	//테마별 리스트와 뷰페이지 사용형태가 다르다.			20180808		chunter
	if ($cfg[skin_theme])
	{
		$r_goods_list = $r_goods_design_list[$cfg[skin_theme]];
		$r_goods_view = $r_goods_design_view[$cfg[skin_theme]];
	}
	else
	{
		$r_goods_list = $r_goods_design_list[M1];
		$r_goods_view = $r_goods_design_view[M1];
	}


?>


<style type="text/css">
<!--
body {background-color:transparent}
-->
</style>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<? if ($data[catno]) { ?>
<form name="fm" method="post" action="indb.php" target="hiddenIfrm" enctype="multipart/form-data" onsubmit="return submitContents(this);">
<input type="hidden" name="mode" value="category_mod">
<input type="hidden" name="catno" value="<?=$data[catno]?>">
<? } else { ?>
<form name="fm" method="post" action="indb.php" target="hiddenIfrm" onsubmit="return submitContents(this);">
<input type="hidden" name="mode" value="category_all">
<? } ?>

<table class="table table-bordered" bgcolor="red">
   <colgroup>
      <col width="200" />
      <col />
   </colgroup>
   <? if ($_GET[catno]) { ?>
   <tr>
      <th><?=_("카테고리번호")?></th>
      <td><b><?=$data[catno]?></b></td>
   </tr>
   <? } ?>
   <tr>
      <th><?=_("카테고리명")?></th>
      <td>
      <? if ($data[catno]) { ?>
      <input type="text" class="form-control" name="catnm" value="<?=$data[catnm]?>" required/>
      <? } else { ?>
      <b><?=$data[catnm]?></b>
      <? } ?>
      </td>
   </tr>
   <tr>
      <th><?=_("상품진열")?></th>
      <td class="form-inline">
      <input type="text" class="form-control" name="cells" value="<?=$data[cells]?>" size="3"/> ×
      <input type="text" class="form-control" name="rows" value="<?=$data[rows]?>" size="3"/>
      <div style="margin-top:5px"><input type="checkbox" name="chk_dp"/> <?=_("상품개수와 정렬양식을 하위분류에 동일하게 적용시 체크")?></div>
      </td>
   </tr>

   <? if ($data[catno]) { ?>

   <tr>
      <th><?=_("상품 리스트&뷰 진열방식")?></th>
      <td class="form-inline">
         <?=_("리스트")?>
         <select class="form-control" name="goods_list" onchange="listOnchange();">
            <? foreach($r_goods_list as $k => $v) { 
            	
								//몰별 자신의 디자인만 나타나기			20180828		chunter	(lib_const.php 파일 설정을 따른다)
								if (strpos($v, "[") !== false)
								{
									//자신의  센터.cid가 등록된 리스트만 나타난다.
									if (strpos($v, $cfg_center[center_cid].".".$cid) === false) {
										continue;
									} else {
										//[ cid ] 값은 삭제한다.
										$v = substr($v, 0, strpos($v, "["));
									}
								}
            ?>
               <option value="<?=$k?>" <?=$selected[goods_list][$k]?>><?=_("$v")?>
            <? } ?>
         </select>
         <span id="goods_view_span">
         &nbsp;&nbsp;&nbsp;
         <?=_("뷰")?>
         <select class="form-control" name="goods_view">
            <? foreach($r_goods_view as $k => $v) { ?>
               <option value="<?=$k?>" <?=$selected[goods_view][$k]?>><?=_("$v")?>
            <? } ?>
         </select>
         </span>
        
        <span id="is_set_span">&nbsp;&nbsp;<input type="checkbox" name="is_set" value="1" <?if($data[is_set]){?>checked<?}?> /><?=_("템플릿셋")?></span>
        
         <div style="padding-top:5px; display: none;" id="params_box" class="form-inline">
	         	<!--자동견적으로 설정시에는 자동견적의 <b class="red">상품의 id를 입력해주세요.</b><br>-공란일 경우 자동견적 상품 리스트에 접속합니다.-->
            	-<?=_("템플릿(셋) 리스트로 설정시에는")?> <b class="red"><?=_("상품의 id를 입력해주세요.")?></b> (<?=_("공란일 경우 리스트에 오류가 발생합니다.")?>)
            	<br>
           		-<?=_("상품 id")?> :
           		<input type="text" class="form-control" name="params" value="<?=$data[params]?>"/><br>
            	-<?=_("샘플 이미지 사이즈")?> : 
            	<input type="text" class="form-control" name="listimg_w" value="<?=$data[listimg_w]?>" size="3"/> × 
            	<input type="text" class="form-control" name="listimg_h" value="<?=$data[listimg_h]?>" size="3"/><br>
                -알래스카 템플릿 리스트 샘플이미지 사이즈는 3개 중 하나를 입력해야합니다. (type1:694x125 / type2:142x454 / type3:326x231)
                <br>
            	<input type="checkbox" name="chk_dp_size"/> <?=_("이미지 사이즈를 하위분류에 동일하게 적용시 체크")?>
         </div>
      </td>
   </tr>

   <tr>
      <th><?=_("매치상품")?></th>
      <td><b><?=number_format($data[cnt])?></b><?=_("개의 상품이 매칭되어 있습니다.")?></td>
   </tr>
   <!--<tr>
      <th><?=_("메인사용여부")?></th>
      <td>
      <input type="radio" name="catmain" value="0" <?=$checked[catmain][0]?> onclick="$j('#catmain_link').hide();"/> <?=_("사용안함")?> 
      <input type="radio" name="catmain" value="1" <?=$checked[catmain][1]?> onclick="$j('#catmain_link').show();"/> <?=_("사용")?>&nbsp;
      <a href="goods_category_main_edit.php?catno=<?=$data[catno]?>" target="blank" id="catmain_link">
         <span type="button" class="btn btn-xs btn-primary"><?=_("메인편집")?></span>
      </a>
      <div class="desc gray"><?=_("상품리스트 대신 표시되는 페이지입니다.")?></div>
      </td>
   </tr>-->
   <tr>
      <th><?=_("URL 바로가기")?></th>
      <td>
         <input type="radio" name="is_url" value="0" <?=$checked[is_url][0]?> onclick="$j('#url_box').hide();"/><?=_("사용안함")?>
         <input type="radio" name="is_url" value="1" <?=$checked[is_url][1]?> onclick="$j('#url_box').show();"/><?=_("사용")?>
         <div style="padding-top:5px;" id="url_box" class="form-inline">
	         http://<input type="text" class="form-control" name="url" value="<?=$data[url]?>" size="50">
	         <select class="form-control" name="url_target">
	         	<option value="_self" <?=$selected[url_target][_self]?>><?=_("현재창")?>
	         	<option value="_blank" <?=$selected[url_target][_blank]?>><?=_("새창")?>   
	         </select>
         </div>         
      </td>
   </tr>
   <tr>
      <th><?=_("기본이미지")?></th>
      <td>
         <input type="file" class="form-control form-inline" name="img" />
         <input type="checkbox" class="checkbox-inline" name="imgdel"/> <?=_("삭제")?><p>
         <? if($data[img] && is_file("../..".$data[file_path].$data[img])) { ?>
            <img src="<?=$data[file_path].$data[img]?>"/>
         <? } else { ?>
            <? if($data[img] && is_file("../../data/category/$cid/$cfg[skin]/$data[img]")) { ?>
               <img id="img" src="../../data/category/<?=$cid?>/<?=$cfg[skin]?>/<?=$data[img]?>"/>
            <? } ?>
            <? if ($data[img] && is_file("../../data/category/$cid/m_default/$data[img]")) { ?>
               <img id="m_img" src="../../data/category/<?=$cid?>/m_default/<?=$data[img]?>"/>
            <? } ?>
         <? } ?>
      </td>
   </tr>
   <tr>
      <th><?=_("마우스오버이미지")?></th>
      <td>
         <input type="file" class="form-control" name="oimg"/>
         <input type="checkbox" class="checkbox-inline" name="oimgdel"/> <?=_("삭제")?><p>
         <? if($data[oimg] && is_file("../..".$data[file_path].$data[oimg])) { ?>
            <img src="<?=$data[file_path].$data[oimg]?>"/>
         <? } else { ?>
            <? if($data[oimg] && is_file("../../data/category/$cid/$cfg[skin]/$data[oimg]")) { ?>
               <img id="oimg" src="../../data/category/<?=$cid?>/<?=$cfg[skin]?>/<?=$data[oimg]?>"/>
            <? } ?>
            <? if($data[oimg] && is_file("../../data/category/$cid/m_default/$data[oimg]")) { ?>
               <img id="m_oimg" src="../../data/category/<?=$cid?>/m_default/<?=$data[oimg]?>"/>
            <? } ?>
         <? } ?>
      </td>
   </tr>
   <tr>
      <th><?=_("좌측기본이미지")?></th>
      <td>
      <input type="file"class="form-control"  name="left_img" size="50"/>
      <input type="checkbox" class="checkbox-inline" name="left_imgdel"/> <?=_("삭제")?><p>
      <? if($data[left_img] && is_file("../..".$data[file_path].$data[left_img])) { ?>
         <img src="<?=$data[file_path].$data[left_img]?>"/>
      <? } else { ?>
         <? if($data[left_img] && is_file("../../data/category/$cid/$cfg[skin]/$data[left_img]")) { ?>
            <img id="left_img" src="../../data/category/<?=$cid?>/<?=$cfg[skin]?>/<?=$data[left_img]?>"/>
         <? } ?>
         <? if($data[left_img] && is_file("../../data/category/$cid/m_default/$data[left_img]")) { ?>
            <img id="m_left_img" src="../../data/category/<?=$cid?>/m_default/<?=$data[left_img]?>"/>
         <? } ?>
      <? } ?>
      </td>
   </tr>
   <tr>
      <th><?=_("좌측마우스오버이미지")?></th>
      <td>
      <input type="file" class="form-control" name="left_oimg" size="50"/>
      <input type="checkbox" class="checkbox-inline" name="left_oimgdel"/> <?=_("삭제")?><p>
      <? if($data[left_oimg] && is_file("../..".$data[file_path].$data[left_oimg])) { ?>
         <img src="<?=$data[file_path].$data[left_oimg]?>"/>
      <? } else { ?>
         <? if($data[left_oimg] && is_file("../../data/category/$cid/$cfg[skin]/$data[left_oimg]")) { ?>
            <img id="left_oimg" src="../../data/category/<?=$cid?>/<?=$cfg[skin]?>/<?=$data[left_oimg]?>"/>
         <? } ?>
         <? if($data[left_oimg] && is_file("../../data/category/$cid/m_default/$data[left_oimg]")) { ?>
            <img id="m_left_oimg" src="../../data/category/<?=$cid?>/m_default/<?=$data[left_oimg]?>"/>
         <? } ?>
      <? } ?>
      </td>
   </tr>

   <tr>
      <th><?=_("상품분류 상단디자인")?></th>
      <td>
      <textarea class="form-control" name="header" style="width:99.8%;height:400px" id="header"><?=$data[header]?></textarea><p>
      <div><input type="checkbox" class="checkbox-inline" name="chk_header"/> <?=_("상품개수와 정렬양식을 하위분류에 동일하게 적용시 체크")?></div>
      </td>
   </tr>
   <? //2014.01.13 / minks / 인트로 사용여부와 인트로 html 코드 추가 ?>
   <tr>
      <th><?=_("POD모듈 INTRO관리 사용여부")?></th>
      <td>
      <input type="radio" name="is_intro" value="0" <?=$checked[is_intro][0]?>/><?=_("사용안함")?>
      <input type="radio" name="is_intro" value="1" <?=$checked[is_intro][1]?>/><?=_("사용")?>
      </td>
   </tr>
   <tr>
      <th><?=_("POD모듈 INTRO관리")?></th>
      <td>
      <textarea class="form-control" name="introhtml" style="width:99.8%;height:400px" id="introhtml"><?=$data[introhtml]?></textarea>
      <div><span class="notice">[<?=_("설명")?>]</span> <?=_("카테고리 INTRO를 사용할 경우 기본적으로 출력되는 INTRO대신 해당 카테고리에 설정한 INTRO가 출력됩니다.")?></div>
      </td>
   </tr>
   <tr>
      <th><?=_("상품분류 유의사항관리")?></th>
      <td>
      <textarea class="form-control" name="infohtml" style="width:99.8%;height:400px" id="infohtml"><?=$data[infohtml]?></textarea>
      <div><span class="notice">[<?=_("설명")?>]</span> <?=_("상품상세 페이지에 안내되는 유의사항 정보입니다.")?></div>
      </td>
   </tr>
   <tr>
      <th><?=_("모바일전용 카테고리")?></th>
      <td>
      <input type="radio" name="is_mobile" value="0" <?=$checked[is_mobile][0]?>/><?=_("사용안함")?>
      <input type="radio" name="is_mobile" value="1" <?=$checked[is_mobile][1]?>/><?=_("사용")?>
      </td>
   </tr>
   <? foreach ($r_addtion_goods_data as $r_k=>$r_v) { ?>
   	<tr>
      <th><?=$r_v[display]?> <?=_("사용여부")?></th>
      <td>
      	<input type="radio" class="radio-inline" name="<?=$r_k?>_flag" value="N" <?=$checked[$r_k."_flag"][N]?>> <?=_("사용안함")?>
	  	<input type="radio" class="radio-inline" name="<?=$r_k?>_flag" value="Y" <?=$checked[$r_k."_flag"][Y]?>> <?=_("사용")?>
      </td>
    </tr>
    <tr class="notView" id="<?=$r_k?>_div">
      <th><?=$r_v[display]?> <?=_("관리")?></th>
      <td style="padding:0;">
      	<div class="col-md-10 form-inline" style="width:100%;">
      	  <section class="srel">
      	    <div class="compare_wrap" style="margin:0;">
              <section class="compare_left">
      		    <h3><?=_("등록된 전체상품 목록")?></h3>
      		    <span class="srel_pad">
      		  	  <label for="<?=$r_k?>_catno" class="sound_only"><?=_("상품분류")?></label>
      		  	  <select id="<?=$r_k?>_catno" class="form-control" name="<?=$r_k?>_catno" style="width:30%;display:inline;">
      		  	    <option value="">+ <?=_("분류 선택")?></option>
      		  	    <?=conv_selected_option($ca_list, "")?>
      		  	  </select>
      		  	  <label for="<?=$r_k?>_name" class="sound_only"><?=_("상품명")?></label>
      		  	  <input type="text" id="<?=$r_k?>_name" class="form-control" name="<?=$r_k?>_name" style="width:35%;display:inline;">
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
          <div style="margin-bottom:10px;"><span class="notice">[<?=_("설명")?>]</span> <?=_("선택된 상품 목록의 순서를 변경하시려면 드래그해주시면 됩니다.")?></div>
        </div>
      </td>
    </tr>
   <? }} ?>
</table>

<div class="btn">
<button type="submit" class="btn btn-warning"><?=_("확인")?></button>
</div>

</form>

<script type="text/javascript" src="../../js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/smarteditor/editorStart.js" charset="utf-8"></script>

<!--form 전송 취약점 개선 20160128 by kdk-->
<script src="../../js/webtoolkit.base64.js"></script>
<script src="../../js/jquery-ui.js"></script>
<script src="../../js/jquery.ui.touch.js"></script>

<script type="text/javascript">
$j(function() {
	<? foreach ($r_addtion_goods_data as $r_k=>$r_v) { ?>
		$j("[name=<?=$r_k?>_flag][value=<?=$r_v[flag]?>]").trigger("click");
			
		$j("#reg_<?=$r_k?>").load(
			"indb.php",
			{ mode:"addtion_goods_select", kind: "<?=$r_k?>", kind2: "C", addtion_key : "<?=$data[catno]?>" },
			function(response, status, xhr) {
				$j("#<?=$r_k?>_sortable").sortable();
			}
		);
	<? } ?>
	
	$j("[name=is_mobile][value=<?=$data[is_mobile]?>]").trigger("click");
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

$j("[name=is_mobile]").click(function() {
	if ($(this).val() == 1) {
		$j("#img").hide();
		$j("#oimg").hide();
		$j("#left_img").hide();
		$j("#left_oimg").hide();
		
		if ($j("#m_img").attr("src") != undefined) $j("#m_img").show();
		else $j("#m_img").hide();
		if ($j("#m_oimg").attr("src") != undefined) $j("#m_oimg").show();
		else $j("#m_oimg").hide();
		if ($j("#m_left_img").attr("src") != undefined) $j("#m_left_img").show();
		else $j("#m_left_img").hide();
		if ($j("#m_left_oimg").attr("src") != undefined) $j("#m_left_oimg").show();
		else $j("#m_left_oimg").hide();
	} else {
		$j("#m_img").hide();
		$j("#m_oimg").hide();
		$j("#m_left_img").hide();
		$j("#m_left_oimg").hide();
		
		if ($j("#img").attr("src") != undefined) $j("#img").show();
		else $j("#img").hide();
		if ($j("#oimg").attr("src") != undefined) $j("#oimg").show();
		else $j("#oimg").hide();
		if ($j("#left_img").attr("src") != undefined) $j("#left_img").show();
		else $j("#left_img").hide();
		if ($j("#left_oimg").attr("src") != undefined) $j("#left_oimg").show();
		else $j("#left_oimg").hide();
	}
});

var oEditors = [];
smartEditorInit("header", true, "goods", true);
smartEditorInit("introhtml", true, "goods", true);
smartEditorInit("infohtml", true, "goods", true);

function submitContents(formObj) {
   if (sendContents("header", false) && sendContents("introhtml", false) && sendContents("infohtml", false)) {
      try {
         formObj.header.value = Base64.encode(formObj.header.value);
         formObj.introhtml.value = Base64.encode(formObj.introhtml.value);
         formObj.infohtml.value = Base64.encode(formObj.infohtml.value);
         return form_chk(formObj);
      } catch(e) {return false;}
   }
   return false;
}
</script>

<script>
$j(function(){
   	if("<?=$data[catmain]?>"==0) {
      	$j('#catmain_link').hide();
   	} else {
      	$j('#catmain_link').show();
   	}

   	if("<?=$data[is_url]?>"==0) {
      	$j('#url_box').hide();
   	} else {
      	$j('#url_box').show();
   	}

	if ($j("select[name='goods_list']").val().indexOf('list_interpro') > -1 || $j("select[name='goods_list']").val().indexOf('list_view_interpro') > -1) {
		$j('#goods_view_span').hide();
		$j('#is_set_span').show();
	}
	else {
		$j('#goods_view_span').show();
		$j('#is_set_span').hide();
	}
	
	if ($j("select[name='goods_list']").val().indexOf('_template') > -1 || $j("select[name='goods_list']").val().indexOf('list_view_interpro') > -1) {
		$j('#params_box').show();
	}
	else {
		$j('#params_box').hide();
	}
});

function form_submit(){
   	document.fm.submit();
}

function listOnchange() {
	if ($j("select[name='goods_list']").val().indexOf('list_interpro') > -1 || $j("select[name='goods_list']").val().indexOf('list_view_interpro') > -1) {
		$j('#goods_view_span').hide();
		$j('#is_set_span').show();
	}
	else {
		$j('#goods_view_span').show();
		$j('#is_set_span').hide();
	}
	
	if ($j("select[name='goods_list']").val().indexOf('_template') > -1 || $j("select[name='goods_list']").val().indexOf('list_view_interpro') > -1) {
		$j('#params_box').show();
	}
	else {
		$j('#params_box').hide();
	}
}
</script>

<? include "../_pfooter.php"; ?>