<?
include_once "../_header.php";
include_once "../_left_menu.php";
?>

<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("회원 일괄 업로드")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("회원 일괄 업로드")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("회원 일괄 업로드")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data">
                  <input type="hidden" name="mode" value="xls_upload">

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("엑셀 파일")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="file" class="form-control" name="file" style="width: 300px;">
                        <button type="button" class="btn btn-sm btn-warning" onclick="location.href='../_sample/memberForm.xlsx';"><?=_("양식 다운로드")?></button>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-md-1 control-label"></label>
                     <div class="col-md-10 form-inline">
                        <button type="submit" class="btn btn-sm btn-primary"><?=_("전송")?></button>
                     </div>
                  </div>

                  <div class="form-group">
                     <div class="col-md-12 form-inline">
                        <span class="desc" style="font-weight: bold; font-size: 30px; color: red">※ <?=_("주의사항 - 꼭 읽어주시기 바랍니다.")?> ※</span>

                        <br><br>
                        - <?=_("파일의 확장자는")?> <span style="color: red">xlsx</span><?=_("파일만 가능합니다.")?><br>
                        - <span style="color: red"><?=_("반드시 양식에 맞춰서")?></span> <?=_("데이터 작성 후 등록하시기 바랍니다.")?><br/>
                        - <?=_("해당몰에 동일한 아이디가 있을 시 모든 데이터는")?> <span style="color: red"><?=_("업로드한 데이터로 업데이트")?></span> <?=_("됩니다.")?><br>
                        - <span style="color: red"><?=_("첫번째 라인(mid, name 등등이 쓰여져 있는)은 절대 수정을 금지합니다.")?></span><br>
                        - <?=_("양식은 첫번째 라인을 제외한 모든 라인이 등록되므로")?> <span style="color: red"><?=_("설명이 있는 2번째 라인은 꼭 삭제")?></span><?=_("하신 후 등록하셔야 합니다.")?><br>
                        - <?=_("회원아이디(mid)값이 없을 시 나머지 데이터는 등록되지 않습니다.")?>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>