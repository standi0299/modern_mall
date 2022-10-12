<?
include "../_header.php";
include "../_left_menu.php";

?>

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("기타기능설정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("기타기능설정")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("기타기능설정")?></h4>
            </div>
            
            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" onsubmit="return submitContents(this);">
                  <input type="hidden" name="mode" value="etc">
                 
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품리스트이미지너비")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" name="listimg_w" class="form-control" value="<?=$cfg[listimg_w]?>"/> px
                     </div>                     
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품상세 기본형 이미지너비")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" name="img_w" class="form-control" value="<?=$cfg[img_w]?>"/> px                        
                        <div><?=_("450 픽셀을 넘어갈경우 디자인이 틀어질수 있습니다. 변경후 사이트를 꼭 확인해 주세요.")?></div>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품상세 가로형 이미지너비")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" name="img_horizen_w" class="form-control" value="<?=$cfg[img_horizen_w]?>"/> px                        
                        <div><?=_("1000 픽셀을 넘어갈경우 디자인이 틀어질수 있습니다. 변경후 사이트를 꼭 확인해 주세요.")?></div>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상품리스트 출력방식")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" name="cells" class="form-control" value="<?=$cfg[cells]?>"/> × 
                        <input type="text" name="rows" class="form-control" value="<?=$cfg[rows]?>"/> (<?=_("열×행")?>)
                     </div>
                  </div>
         
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("상단분류메뉴 슬라이드 ON/OFF")?></label>
                     <div class="col-md-10">
                        <input type="radio" name="top_category_sub_off" value="0" <?if($cfg[top_category_sub_off]+0==0){?>checked<?}?> class="absmiddle"><span class="absmiddle">ON</span>
                        <input type="radio" name="top_category_sub_off" value="1" <?if($cfg[top_category_sub_off]+0==1){?>checked<?}?> class="absmiddle"><span class="absmiddle">OFF</span>
                        <div><span class="notice">[<?=_("설명")?>]</span> <?=_("스킨의 상단분류메뉴의 하위분류 슬라이드 여부를 결정합니다. - 지원가능한 스킨(basic) 에만 적용됩니다.")?></div>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("자동견적 사용여부")?></label>
                     <div class="col-md-6">
                        <input type="radio" name="est_option_flag" value="1" <?if($cfg[est_option_flag]+0==1){?>checked<?}?> class="absmiddle"><span class="absmiddle"><?=_("사용")?></span>
                        <input type="radio" name="est_option_flag" value="0" <?if($cfg[est_option_flag]+0==0){?>checked<?}?> class="absmiddle"><span class="absmiddle"><?=_("사용안함")?></span>
                        <div><span class="notice">[<?=_("설명")?>]</span> <?=_("자동견적 사용여부를 설정합니다.")?></div>
                     </div>
                  </div>                  
<!--
	#자동견적 메뉴 생성 후 이동 예정
                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("자동견적 업로드 URL")?></label>
                     <div class="col-md-6">
                        <input type="text" name="est_upload_url" class="form-control" value="<?=$cfg[est_upload_url]?>"/> http://포함해서 입력
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("자동견적 원본 파일정보 URL")?></label>
                     <div class="col-md-6">
                        <input type="text" name="est_fileinfo_url" class="form-control" value="<?=$cfg[est_fileinfo_url]?>"/> http://포함해서 입력
                     </div>
                  </div>
-->                  
                  <div class="form-group"> 
                    <label class="col-md-2 control-label"></label>
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("등록")?></button>
                    </div>
                </div>
               </form>
            </div>
            
            <div style="border:2px solid #DEDEDE;padding:10px;">
               <p><span>[<?=_("확인")?>]</span> <?=_("상품의 이미지사이즈는 상품등록시 설정된 사이즈로 자동으로 변경됩니다.")?></p>
               <p><span>[<?=_("확인")?>]</span> <?=_("상품리스트 출력방식은 상품분류의 출력방식을 지정하지 않았을때 나오는 기본형식입니다.")?></p>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>

<? include "goods_r.js.php"; ?>