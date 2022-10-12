<?
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__) . "/../../lib2/db_common.php";
include_once dirname(__FILE__) . "/../../models/m_common.php";
include_once dirname(__FILE__) . "/../../lib/class.kakao.alimtalk.aligo.php";

$kakao_aligo = new KakaoAlimtalkAligo();

$kakao_aligo_template = $kakao_aligo->alimtalk_aligo_get_template();
// debug($kakao_aligo_template);
$kakao_aligo_template = $kakao_aligo_template['list'];
// debug($kakao_aligo_template);

// 발송 테스트
// $send_res = $kakao_aligo->auto_alimtalk_aligo("010-2602-6390","TB_1442");
// debug($send_res);


$alimtalk_mapping_data = $db->listArray("select code,k_code from exm_alimtalk_mapping
where cid='$cid'
and group_code='aligo'");

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
      <li class="active"><?=_("알림톡 서비스")?></li>
   </ol>
   <h1 class="page-header"><?=_("알림톡 서비스")?> <small><?=_("소비자에게 유형별 알림톡을 자동으로 보내실 수 있습니다.")?></small></h1>
   
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
   	<input type="hidden" name="mode" value="autokakao"/>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("알림톡 서비스")?></h4>
         </div>

         <div class="panel-body panel-form">            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("알림톡 <br/>(API KEY)")?></label>
               <div class="col-md-10 form-inline">
                  <input type="text" name="alimtalk_api_key" class="form-control" value="<?=$cfg[alimtalk_api_key]?>" size="50"> 
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("알림톡 <br/>(API ID)")?></label>
               <div class="col-md-10 form-inline">
               <input type="text" name="alimtalk_api_id" class="form-control" value="<?=$cfg[alimtalk_api_id]?>" size="50"> 
               </div>
            </div>
                  
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("알림톡 채널 <br/>(Sender KEY) ")?></label>
               <div class="col-md-10 form-inline">
                  <input type="text" name="alimtalk_sender_key" class="form-control" value="<?=$cfg[alimtalk_sender_key]?>" size="50"> 
               </div>
            </div>
            
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("알림톡 핸드폰 번호 ")?></label>
               <div class="col-md-10 form-inline">
                  <input type="text" name="alimtalk_sender_number" class="form-control" value="<?=$cfg[alimtalk_sender_number]?>" size="50"> 
               </div>
            </div>

            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("알림톡 <br/>Aligo 서비스 연결")?></label>
               <div class="col-md-10 form-inline">
               <?
               if($kakao_aligo_template){
                  foreach ($kakao_aligo_template as $k=>$v){

                     foreach($alimtalk_mapping_data as $a_k => $a_v){
                        if($v['templtCode'] == $a_v['k_code']){
                           $selected_code = $a_v['code'];
                           break;
                        }
                     }
               ?>
                  <div class="col-md-6">
                     <br>
                     <div class="col-md-12 alert-info">
                        <label class="control-label">
                           <h6>
                              발송 형태 : 
                                 <select name=alimtalk_mapping[<?=$v['templtCode']?>]>
                                    <? foreach($r_title as $r_k => $r_v) {?>
                                       <option value="<?=$r_v?>" <? if($selected_code == $r_v) echo "selected" ?> ><?=$r_v?></option>
                                    <? } ?>
                                 </select>
                           </h6>                              
                        </label>
                     </div>
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
                     <br><br>

                     <div class="col-md-12 alert-info">
                        <textarea  rows="12" cols="45" class="form-control" disabled><?=$v['templtContent'];?></textarea><br>

                        <!-- <input type="checkbox" class="control-label" name="send[<?=$k?>][]" value="1" <?=$checked[send][$k][1]?>/> <?=_("고객에게 자동발송")?><br><br><br> -->
                        
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
               <?=_("[확인] 알림톡 Aligo 보내기 서비스 이용 시 일정 포인트를 소모합니다. 또한 충전 포인트 잔액이 부족할 경우 전송이 안될 수 있습니다.")?><br><br> 
               - <?=_("포인트 잔액을 확인 후 이용해주세요.")?>
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