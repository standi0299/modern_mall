<?
/*
* @date : 20181214
* @author : kdk
* @brief : POD용 (알래스카) 기존 회원 이관.
* @request :
* @desc : 엑셀파일 업로드 처리.
* @todo :
*/

include "../_header.php";
include "../_left_menu.php";

$m_pod = new M_pod();

## 회원가입항목
$cfg[fieldset] = getCfg("fieldset");
$cfg[fieldset] = unserialize($cfg[fieldset]);

if (is_array($cfg[fieldset])) {
    foreach ($cfg[fieldset] as $k=>$v) {
        if ($v[req] == 1) $required[$k] = "required";
        if ($v['use'] == 1) $used[$k] = "used";
    }
}

## 회원그룹 추출
$r_grp = getMemberGrp();
$r_bid = getBusiness();

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

if (!$data[state]) $checked[state][0] = "checked";
if (!$data[sex]) $checked[sex][m] = "checked";
if (!$data[apply_email]) $checked[apply_email][1] = "checked";
if (!$data[apply_sms]) $checked[apply_sms][1] = "checked";

### 결제방식:credit_member (선결제,후결제)
### 거래상태:rest_flag (승인,정지)
if (!$data[credit_member]) $checked[credit_member][0] = "checked";
if (!$data[rest_flag]) $checked[rest_flag][0] = "checked";
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->
<div id="content" class="content">
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active">
         <?=_("회원수정")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("회원등록")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("회원등록")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.member.php" enctype="multipart/form-data" onsubmit="return submitContents(this);">

                  <div class="form-group">
                     <label class="col-md-2 control-label"><?=_("엑셀파일")?></label>
                     <div class="col-md-4 form-inline">
                         <input type="file" name="file" />
                         <div class="desc"><span class="warning">[주의]</span>xlsx파일을 업로드 하셔야 합니다.</div>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-2 control-label"></label>
                     <div class="col-md-10">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("등록")?></button>
                        <button type="button" class="btn btn-sm btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<!--form 전송 취약점 개선 20160128 by kdk-->
<script src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
function submitContents(formObj) {
    try {
        return form_chk(formObj);
    } catch(e) {return false;}
}
</script>

<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>