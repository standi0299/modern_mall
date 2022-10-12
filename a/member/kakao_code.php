<?
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__) . "/../../lib2/db_common.php";
include_once dirname(__FILE__) . "/../../models/m_common.php";
include_once dirname(__FILE__) . "/../../lib/class.kakao.alimtalk.aligo.php";

$kakao_aligo = new KakaoAlimtalkAligo();

$kakao_aligo_template = $kakao_aligo->alimtalk_aligo_get_template();
$kakao_aligo_template = $kakao_aligo_template['list'];

$alimtalk_mapping_data = $db->listArray("select code,k_code from exm_alimtalk_mapping where cid='$cid' and group_code='aligo'");
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=_("알림톡 코드관리")?></li>
   </ol>
   <h1 class="page-header"><?=_("알림톡 코드관리")?> <small><?=_("소비자에게 유형별 알림톡 코드를 관리할 수 있습니다.")?></small></h1>
   
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   	<input type="hidden" name="mode" value="kakao_code"/>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("알림톡 코드관리")?></h4>
         </div>

         <div class="panel-body panel-form"> 
           <?
            foreach($alimtalk_mapping_data as $a_k => $a_v){              
            ?>          
            <div class="form-group">
               <label class="col-md-2 control-label"><?=$a_v['code']?></label>
               <div class="col-md-10 form-inline">
                  <input type="text" name="code['<?=$a_v['code']?>']" class="form-control" value="<?=$a_v['k_code']?>" size="50"> 
               </div>
            </div>
            <? } ?>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("알림톡 <br/>Aligo 서비스 연결")?></label>
               <div class="col-md-10 form-inline">
               <?
               if($kakao_aligo_template){
                  foreach ($kakao_aligo_template as $k=>$v){
               ?>
                  <div class="col-md-6">
                     <br>
                     <div class="col-md-12 alert-info">
                        <label class="control-label">
                           <h6>
                              Aligo 템플릿 이름 : <b><?=$v['templtName']?></b>
                           </h6>
                           
                        </label>
                     </div>
                     <div class="col-md-12 alert-info">
                        <label class="control-label">
                           <h6>
                              Aligo 템플릿 코드 : <b><?=$v['templtCode']?></b>
                           </h6>
                           
                        </label>
                     </div>
                     <div class="col-md-12 alert-info">
                        <label class="control-label">
                           <h6>
                              Aligo 템플릿 승인상태 : <b><?=$v['inspStatus']?></b>
                           </h6>
                           
                        </label>
                     </div>                     
                  </div>
               <?
                  }
               }else{
                  echo "등록된 Aligo 템플릿이 없습니다.";
               }
               ?>
               </div>
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-md-11">
            <p class="pull-right">
               <button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("등록")?></button>
               <button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
            </p>
         </div>
      </div>
   </form>

   <div class="panel panel-inverse">
      <div class="panel-body panel-form">
         <div class="form-group">
            <div class="col-md-12">
               <br>
               <?=_("[확인] 알리고 템플릿 코드 변경이 된 경우 시스템에 반영이 되어야 합니다.")?><br><br> 
               - <?=_("템플릿 변경시 신규 승인 완료 후 등록해 주세요")?><br><br>
               <font color="red"><b>
                 - <?=_("승인상태 : REG(등록) REQ(심사요청)  APR(승인) REJ(반려)")?>
              </b></font>
               <br><br>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
function add(){
   var target = $j("#addmobile");
   var inner = $j("#origin_tb").html();
   var div = document.createElement("div");
   div.innerHTML = inner;
   $j(div).clone().appendTo(target);
}

function remove(obj){
   $j(obj).parent().remove();
}
</script>

<!-- 카피용 -->
<div id="origin_tb" style="display:none;padding-left:-10px">
<input type="text" name="mobile2[1][]" class="form-control" maxlength="3"> -
<input type="text" name="mobile2[2][]" class="form-control" maxlength="4"> -
<input type="text" name="mobile2[3][]" class="form-control" maxlength="4">
<span class="btn btn-danger btn-icon btn-circle btn-xs" onclick="remove(this)"><i class="fa fa-times"></i></span><br><br>
</div>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>