<?
include "../_header.php";
include "../_left_menu.php";

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <div class="panel panel-inverse">
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("고객센터 메뉴 관리")?></h4>
      </div>

      <div class="panel-body panel-form">
         <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
            <input type="hidden" name="mode" value="service_menu_setting">

            <!-- 몰 별로 회원가입 여부 설정 / 15.07.16 / kjm -->
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("공지사항")?></label>
               <div class="col-md-10">
                  <label class="radio-inline">
                     <input type="radio" name="menu_admin" value="Y" <? if(!$cfg[service_menu][menu_admin] || $cfg[service_menu][menu_admin] == 'Y') { ?> checked <? } ?>><?=_("사용")?>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="menu_admin" value="N" <? if($cfg[service_menu][menu_admin] == 'N') { ?>checked<? } ?>><?=_("사용안함")?>
                  </label>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("자주하는 질문")?></label>
               <div class="col-md-10">
                  <label class="radio-inline">
                     <input type="radio" name="menu_faq" value="Y" <? if(!$cfg[service_menu][menu_faq] || $cfg[service_menu][menu_faq] == 'Y') { ?> checked <? } ?>><?=_("사용")?>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="menu_faq" value="N" <? if($cfg[service_menu][menu_faq] == 'N') { ?>checked<? } ?>><?=_("사용안함")?>
                  </label>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("회원정보수정")?></label>
               <div class="col-md-10">
                  <label class="radio-inline">
                     <input type="radio" name="menu_myinfo" value="Y" <? if(!$cfg[service_menu][menu_myinfo] || $cfg[service_menu][menu_myinfo] == 'Y') { ?> checked <? } ?>> <?=_("사용")?>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="menu_myinfo" value="N" <? if($cfg[service_menu][menu_myinfo] == 'N') { ?>checked<? } ?>><?=_("사용안함")?>
                  </label>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("자료실")?></label>
               <div class="col-md-10">
                  <label class="radio-inline">
                     <input type="radio" name="menu_psd" value="Y" <? if(!$cfg[service_menu][menu_psd] || $cfg[service_menu][menu_psd] == 'Y') { ?> checked <? } ?>> <?=_("사용")?>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="menu_psd" value="N" <? if($cfg[service_menu][menu_psd] == 'N') { ?>checked<? } ?>><?=_("사용안함")?>
                  </label>
               </div>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("1:1 문의")?></label>
               <div class="col-md-10">
                  <label class="radio-inline">
                     <input type="radio" name="menu_mycs" value="Y" <? if(!$cfg[service_menu][menu_mycs] || $cfg[service_menu][menu_mycs] == 'Y') { ?> checked <? } ?>> <?=_("사용")?>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="menu_mycs" value="N" <? if($cfg[service_menu][menu_mycs] == 'N') { ?>checked<? } ?>><?=_("사용안함")?>
                  </label>
               </div>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("원격지원 안내")?></label>
               <div class="col-md-10">
                  <label class="radio-inline">
                     <input type="radio" name="menu_remote_service" value="Y" <? if(!$cfg[service_menu][menu_remote_service] || $cfg[service_menu][menu_remote_service] == 'Y') { ?> checked <? } ?>> <?=_("사용")?>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="menu_remote_service" value="N" <? if($cfg[service_menu][menu_remote_service] == 'N') { ?>checked<? } ?>><?=_("사용안함")?>
                  </label>
               </div>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("도움말")?></label>
               <div class="col-md-10">
                  <label class="radio-inline">
                     <input type="radio" name="menu_help" value="Y" <? if(!$cfg[service_menu][menu_help] || $cfg[service_menu][menu_help] == 'Y') { ?> checked <? } ?>> <?=_("사용")?>
                  </label>
                  <label class="radio-inline">
                     <input type="radio" name="menu_help" value="N" <? if($cfg[service_menu][menu_help] == 'N') { ?>checked<? } ?>><?=_("사용안함")?>
                  </label>
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"></label>
               <div class="col-md-10">
                  <button type="submit" class="btn btn-sm btn-success">
                     <?=_("저 장")?>
                  </button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>