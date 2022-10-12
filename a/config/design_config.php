<?
include "../_header.php";
include "../_left_menu.php";

$query = "select * from exm_board_set where cid = '$cid' order by board_name";
$res = $db->query($query);
$board = array();
while ($data = $db->fetch($res)) {
   $board[] = $data;
}

$data = $cfg[layout];
if (!$data[top]) $data[top] = "default";
if (!$data[bottom]) $data[bottom] = "default";
if (!$data[goods_quick]) $data[goods_quick] = "default";
if (!$cfg[top_header_color]) $cfg[top_header_color] = "#7ECAFD";
//debug($data);
$selected[dg_top][$data[top]] = " selected";
$selected[dg_footer][$data[bottom]] = "selected";
$selected[dg_goods_quick][$data[goods_quick]] = "selected";

$checked[dg_right_slide_menu][$cfg[dg_right_slide_menu]] = "checked";
$checked[dg_goods_detail_option_layout][$cfg[dg_goods_detail_option_layout]] = "checked";
$checked[dg_top_menu_fix][$cfg[dg_top_menu_fix]] = "checked";

$checked[main_display_board_left][$cfg[main_display_board_left]] = "selected";
$checked[main_display_board_right][$cfg[main_display_board_right]] = "selected";

$checked[top_logo][$cfg[top_logo_type]] = "checked";
if (!$cfg[top_logo_type]) $checked[top_logo][img] = "checked";

$checked[list_catnav][$cfg[list_catnav]] = "checked";
$checked[list_orderby][$cfg[list_orderby]] = "checked";
$checked[dg_top_slide_banner][$cfg[dg_top_slide_banner]] = "checked";
$checked[sns_goods][$cfg[sns_goods]] = "checked";
$checked[zoom_goods][$cfg[zoom_goods]] = "checked";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<link href="/a/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data">
   <input type="hidden" name="mode" value="design_config" />

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("디자인 설정")?></h4>
         </div>

         <div class="panel-body panel-form">
       	

         	<div class="form-group">
         	 	<label class="col-md-2 control-label"><?=_("오른쪽 슬라이드 퀵 메뉴")?></label>
         	 	<div class="col-md-10">
         	 		<input type="checkbox" data-render="switchery" data-theme="blue" name="dg_right_slide_menu" value="1" <?=$checked[dg_right_slide_menu][1]?>>
         	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("오른쪽 슬라이드 퀵메뉴 사용여부를 설정합니다.")?></div>
         	 	</div>
         	</div>

         	<div class="form-group">
         	 	<label class="col-md-2 control-label"><?=_("상품 상세화면 옵션 Layout")?></label>
         	 	<div class="col-md-10">
         	 		<input type="checkbox" data-render="switchery" data-theme="blue" name="dg_goods_detail_option_layout" value="1" <?=$checked[dg_goods_detail_option_layout][1]?>>      	 		
         	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("상품 상세화면에서 스크롤시 나타나는 옵션창 사용여부를 설정합니다.")?></div>      	 		
         	 	</div>
         	</div>
         	
         	<div class="form-group">
         	 	<label class="col-md-2 control-label"><?=_("상품리스트(상세) 상단분류 숨기기")?></label>
         	 	<div class="col-md-10">
         	 		<input type="checkbox" data-render="switchery" data-theme="orange" name="list_catnav" value="1" <?=$checked[list_catnav][1]?>>      	 		
         	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("상품 리스트(상세)화면에서 분류 표시를")?> <span class="red"><?=_("나타나지 않게")?></span> <?=_("설정합니다.")?></div>      	 		
         	 	</div>
         	</div>
         	
         	<div class="form-group">
         	 	<label class="col-md-2 control-label"><?=_("상품리스트 정렬방법 숨기기")?></label>
         	 	<div class="col-md-10">
         	 		<input type="checkbox" data-render="switchery" data-theme="orange" name="list_orderby" value="1" <?=$checked[list_orderby][1]?>>      	 		
         	 		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("상품 리스트화면에서 정렬방법(인기상품순, 최신순, 높은가격순, 낮은가격순 ) 표시를")?> <span class="red"><?=_("나타나지 않게")?></span> <?=_("설정합니다.")?></div>      	 		
         	 	</div>
         	</div>         	
         	
         	<div class="form-group">
             <label class="col-md-2 control-label"><?=_("최상단 SKY 배너 숨기기")?></label>
             <div class="col-md-10">
                <input type="checkbox" data-render="switchery" data-theme="orange" name="dg_top_slide_banner" value="1" <?=$checked[dg_top_slide_banner][1]?>>               
                <div><span class="notice">[<?=_("설명")?>]</span> <?=_("최상단 SKY 배너가")?> <span class="red"><?=_("나타나지 않게")?></span> <?=_("설정합니다.")?></div>
             </div>
          </div>

         	<div class="form-group">
             <label class="col-md-2 control-label"><?=_("상단 메뉴 스크롤시 메뉴 고정")?></label>
             <div class="col-md-10">
                <input type="checkbox" data-render="switchery" data-theme="blue" name="dg_top_menu_fix" value="1" <?=$checked[dg_top_menu_fix][1]?>>               
                <div><span class="notice">[<?=_("설명")?>]</span> <?=_("스크롤시 상단메뉴 고정으로 나타나는 화면을 설정합니다.")?></div>
             </div>
          </div>          
          
          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("SNS 상품 공유")?></label>
             <div class="col-md-10">
                <input type="checkbox" data-render="switchery" data-theme="blue" name="sns_goods" value="1" <?=$checked[sns_goods][1]?>>               
                <div><span class="notice">[<?=_("설명")?>]</span> <?=_("상품 상세 화면에서 SNS 상품 공유 기능 사용을 설정합니다.")?></div>
             </div>
          </div>
          
          
          <div class="form-group">
             <label class="col-md-2 control-label"><?=_("상품 이미지 Zoom 사용")?></label>
             <div class="col-md-10">
                <input type="checkbox" data-render="switchery" data-theme="blue" name="zoom_goods" value="1" <?=$checked[zoom_goods][1]?>>               
                <div><span class="notice">[<?=_("설명")?>]</span> <?=_("상품 상세 화면에서 상품 이미지 Zoom 기능 사용을 설정합니다.")?></div>
             </div>
          </div>
          

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("상단 로고 설정")?></label>
               <div class="col-md-4">
                  <input type="radio" class="radio-inline" name="top_logo" value="img" <?=$checked[top_logo][img]?> onclick="divShow(this, 'img', 'txt');"> <?=_("이미지")?>
                  <input type="radio" class="radio-inline" name="top_logo" value="txt" <?=$checked[top_logo][txt]?> onclick="divShow(this, 'txt', 'img');"> <?=_("텍스트")?><br>

                  <div class="notView" id="top_logo_img_div">
                     <input type="file" class="form-control" name="top_logo_img"><br>
                     <? if(is_file("../../data/top_logo/$cid/top_logo.png")) { ?>
                        <img src="../../data/top_logo/<?=$cid?>/top_logo.png"><br>
                     <? } ?>
                  </div>
                  
                  <div class="notView" id="top_logo_txt_div">
                     <input type="text" class="form-control" name="top_logo_txt" value="<?=$cfg[top_logo]?>">
                  </div>
               </div>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("메인노출게시판")?></label>
               <div class="col-md-2">
                  <?=_("왼쪽")?>
                  <select class="form-control" name="main_display_board_left">
                  <option value=""><?=_("선택")?>
                  <? foreach ($board as $v) { ?>
                  <option value="<?=$v[board_id]?>" <?=$checked[main_display_board_left][$v[board_id]]?>><?=$v[board_name]?>
                  <? } ?>
                  </select>
               </div>
               <div class="col-md-2">
                  <?=_("오른쪽")?>
                  <select class="form-control" name="main_display_board_right">
                  <option value=""><?=_("선택")?>
                  <? foreach ($board as $v) { ?>
                  <option value="<?=$v[board_id]?>" <?=$checked[main_display_board_right][$v[board_id]]?>><?=$v[board_name]?>
                  <? } ?>
                  </select>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"></label>
         	 	<div class="col-md-10">
         	 		<button type="submit" class="btn btn-sm btn-success"><?=_("저장")?></button>
               </div>
            </div>
         </div>
      </div>   
   </form>
</div>

<? include "../_footer_app_init.php"; ?>

<script type="text/javascript" src="../assets/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="../assets/js/form-slider-switcher.demo.js"></script>
<script src="../assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	FormSliderSwitcher.init();
	
   $('input:radio[name="top_logo"]').each(function() {
      if(this.value == "img" && this.checked == true){
         divShow(this, 'img', 'txt');
      } else if(this.value == "txt" && this.checked == true){
         divShow(this, 'txt', 'img');
      }
   });
   
   $('#colorpicker').colorpicker({format: 'hex'});
   header_pick();
});

function divShow(obj, type, type_dis) {
   $j("#" + obj.name + "_" + type + "_div").slideDown();
   $j('input','#' + obj.name + "_" + type +  '_div').attr('disabled', false);
   
   $j("#" + obj.name + "_" + type_dis + "_div").slideUp();
   $j('input','#' + obj.name  + "_" + type_dis + '_div').attr('disabled', true);
}

function header_pick()
{		
	if ($( "#dg_top" ).val() == "default")	
		$("#header_color_pick").show(500);
	else
		$("#header_color_pick").hide(500);
	
}

</script>

<? include "../_footer_app_exec.php"; ?>