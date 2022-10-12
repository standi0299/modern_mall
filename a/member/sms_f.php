<?   
include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__) . "/../../lib2/db_common.php";
include_once dirname(__FILE__) . "/../../models/m_common.php";

### 회원그룹 추출
$r_grp = getMemberGrp();
?>

<div id="content" class="content">
   <!-- begin #header -->
   <ol class="breadcrumb pull-right">
      <li>
         <a href="javascript:;">Home</a>
      </li>
      <li class="active"><?=_("SMS발송")?></li>
   </ol>
   <h1 class="page-header"><?=_("SMS발송")?> <small><?=_("회원 그룹별 SMS를 발송할 수 있습니다.")?></small></h1>
   
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data" onsubmit="return (form_chk(this) && _submit(this))">
   	<input type="hidden" name="mode" value="sendsms2"/>

      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title"><?=_("SMS발송")?></h4>
         </div>

         <div class="panel-body panel-form">
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("보내는 사람")?></label>
               <div class="col-md-3">
				  <input type="text" name="from" style="width:50%" value="<?=$cfg[smsAdmin]?>" label="보내는사람" required>
               </div>
            </div>
			<div class="form-group">
               <label class="col-md-2 control-label"><?=_("받는사람")?></label>
               <div class="col-md-2">
                  <select name="vtype" class="form-control" onchange="on_vtype(this.value)">
					<option value="to">번호직접입력</option>
					<option value="member">회원에게 보내기</option>
					<option value="excel">엑셀파일등록</option>
                  </select>
               </div>
            </div>
			<div class="form-group" id="z_to">
               <label class="col-md-2 control-label"><?=_("번호직접입력")?></label>
               <div class="col-md-8">
                  <input type="text" name="to" style="width:30%">
               </div>
            </div>			
			<div class="form-group" style="display:none;" id="z_member">
               <label class="col-md-2 control-label"><?=_("그룹선택")?></label>
               <div class="col-md-2">
                  <select name="member" class="form-control">
					<option value="">전체회원
					<? foreach($r_grp as $k=>$v) { ?>
					<option value="<?=$k?>"><?=$v?></option>
					<? } ?>
                  </select>
               </div>
            </div>
			<div class="form-group" style="display:none;" id="z_excel">
               <label class="col-md-2 control-label"><?=_("엑셀선택")?></label>
               <div class="col-md-8">
                  <input type="file" name="file" size="20" style="margin-top:3px">
					<a href="javascript:listup()"><img src="../../admin/img/bt_exlup_s.png"></a>
					<a href="../../admin/_sample/sms.xls"><img src="../../admin/img/bt_exldown_s.png"></a><br/>
					<iframe name="ifrmSms" style="width:400px;height:100px"></iframe>
               </div>
			   
            </div>
            <div class="form-group">
               <label class="col-md-2 control-label"><?=_("내용")?></label>
               <div class="col-md-10">
                  <textarea name="sms_msg[]" id="sms_msg" style="width:20%;height:120px" cols="8" rows="4" label="내용" onkeyup="chkSmsByte(this)" required></textarea>
				  <span id="byte">0 </span> byte
               </div>
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-md-11">
            <p class="pull-right">
   	    	   <button type="submit" class="btn btn-md btn-primary m-r-15"><?=_("전송")?></button>
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
               - <?=_("[확인]</span> SMS는 콜을 충전하여야 전송됩니다.")?><br><br>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
function on_vtype(v)
{	console.log(v);
	_ID('z_to').style.display = _ID('z_excel').style.display = _ID('z_member').style.display = "none";
	_ID('z_'+v).style.display = "block";

	if (v=='to') {
		$j("input:text[name=to]").attr("required","required");
	} else {
		$j("input:text[name=to]").removeAttr("required");
	}
}
function listup()
{
	var fm = document.fm;
	
	fm.target = "ifrmSms";
	fm.mode.value = "smslistup";
	fm.submit();
}
function _submit(fm)
{
	var fm = document.fm;
	if(!fm.from.value) {
		alert('보내는사람을 확인하세요');
		return false;
	}

	fm.mode.value = 'sendsms2';

	return true;
}
</script>


<? include "../_footer_app_init.php"; ?>
<? include "../_footer_app_exec.php"; ?>