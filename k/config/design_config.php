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

$selected[dg_top][$data[top]] = " selected";
$selected[dg_footer][$data[bottom]] = "selected";
$selected[dg_goods_quick][$data[goods_quick]] = "selected";

$checked[dg_right_slide_menu][$cfg[dg_right_slide_menu]] = "checked";
$checked[dg_goods_detail_option_layout][$cfg[dg_goods_detail_option_layout]] = "checked";
$checked[dg_top_menu_fix][$cfg[dg_top_menu_fix]] = "checked";

$checked[main_display_board_left][$cfg[main_display_board_left]] = "selected";
$checked[main_display_board_right][$cfg[main_display_board_right]] = "selected";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data">
   <input type="hidden" name="mode" value="design_config" />

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title">디자인 설정</h4>
         </div>

         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label">상단 카테고리 영역</label>
         	 	<div class="col-md-2">
         	 		<select name="dg_top" class="form-control" required>
         	 			<? foreach ($_r_dg_top_block as $key=>$value) { ?>
         	 				<option value="<?=$key?>" <?=$selected[dg_top][$key]?>><?=$value?></option>
         	 			<? } ?>
         	 		</select>
         	 	</div>
         	 	<div><span class="notice">[설명]</span> 상단의 카테고리 영역의 디자인을 선택합니다.</div>
         	</div>

         	<div class="form-group">
         	 	<label class="col-md-2 control-label">하단 Footer 역역</label>
         	 	<div class="col-md-2">
         	 		<select name="dg_footer" class="form-control" required>
         	 			<? foreach ($_r_dg_footer_block as $key=>$value) { ?>
         	 				<option value="<?=$key?>" <?=$selected[dg_footer][$key]?>><?=$value?></option>
         	 			<? } ?>
         	 		</select>
         	 	</div>
         	 	<div><span class="notice">[설명]</span> 하단의 Copyright 영역의 디자인을 선택합니다.</div>
         	</div>

         	<div class="form-group">
         	 	<label class="col-md-2 control-label">하단 Footer 역역</label>
         	 	<div class="col-md-2">
         	 		<select name="dg_goods_quick" class="form-control" required>
         	 			<? foreach ($_r_dg_goods_quick_option_block as $key=>$value) { ?>
         	 				<option value="<?=$key?>" <?=$selected[dg_goods_quick][$key]?>><?=$value?></option>
         	 			<? } ?>
         	 		</select>
         	 	</div>
         	 	<div><span class="notice">[설명]</span> 상품 상세화면에서 스크롤시 나타나는 옵션창 영역의 디자인을 선택합니다.</div>
         	</div>

         	<div class="form-group">
         	 	<label class="col-md-2 control-label">오른쪽 슬라이드 퀵 메뉴</label>
         	 	<div class="col-md-10">
         	 		<input type="checkbox" data-render="switchery" data-theme="blue" name="dg_right_slide_menu" value="1" <?=$checked[dg_right_slide_menu][1]?>>
         	 		<div><span class="notice">[설명]</span> 오른쪽 슬라이드 퀵메뉴 사용여부를 설정합니다.</div>
         	 	</div>
         	</div>

         	<div class="form-group">
         	 	<label class="col-md-2 control-label">상품 상세화면 옵션 Layout</label>
         	 	<div class="col-md-10">
         	 		<input type="checkbox" data-render="switchery" data-theme="blue" name="dg_goods_detail_option_layout" value="1" <?=$checked[dg_goods_detail_option_layout][1]?>>      	 		
         	 		<div><span class="notice">[설명]</span> 상품 상세화면에서 스크롤시 나타나는 옵션창 사용여부를 설정합니다.</div>      	 		
         	 	</div>
         	</div>

         	<div class="form-group">
               <label class="col-md-2 control-label">상단 메뉴 스크롤시 메뉴 고정</label>
               <div class="col-md-10">
                  <input type="checkbox" data-render="switchery" data-theme="blue" name="dg_top_menu_fix" value="1" <?=$checked[dg_top_menu_fix][1]?>>               
                  <div><span class="notice">[설명]</span> 스크롤시 상단메뉴 고정으로 나타나는 화면을 설정합니다.</div>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label">상단 로고 설정</label>
               <div class="col-md-4">
                  <input type="radio" class="radio-inline" name="top_logo" value="img" <?=$checked[top_logo][img]?> onclick="divShow(this, 'img', 'txt');"> 이미지
                  <input type="radio" class="radio-inline" name="top_logo" value="txt" <?=$checked[top_logo][txt]?> onclick="divShow(this, 'txt', 'img');"> 텍스트<br>

                  <div class="notView" id="top_logo_img_div">
                     <input type="file" class="form-control" name="top_logo_img"><br>
                     <? if(is_file("../../data/top_logo.png")) { ?>
                        <img src="../../data/top_logo.png"><br>
                     <? } ?>
                     <span class="notice">[주의사항]</span> 이미지의 크기는 최대 152px X 57px 그리고 확장자는 png만 가능합니다.
                  </div>
                  
                  <div class="notView" id="top_logo_txt_div">
                     <input type="text" class="form-control" name="top_logo_txt" value="<?=$cfg[top_logo]?>">
                  </div>
               </div>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label">메인노출게시판</label>
               <div class="col-md-2">
                  왼쪽
                  <select class="form-control" name="main_display_board_left">
                  <option value="">선택
                  <? foreach ($board as $v) { ?>
                  <option value="<?=$v[board_id]?>" <?=$checked[main_display_board_left][$v[board_id]]?>><?=$v[board_name]?>
                  <? } ?>
                  </select>
               </div>
               <div class="col-md-2">
                  오른쪽
                  <select class="form-control" name="main_display_board_right">
                  <option value="">선택
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
});

function divShow(obj, type, type_dis) {
   $j("#" + obj.name + "_" + type + "_div").slideDown();
   $j('input','#' + obj.name + "_" + type +  '_div').attr('disabled', false);
   
   $j("#" + obj.name + "_" + type_dis + "_div").slideUp();
   $j('input','#' + obj.name  + "_" + type_dis + '_div').attr('disabled', true);
}
</script>

<? include "../_footer_app_exec.php"; ?>