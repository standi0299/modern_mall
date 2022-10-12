<?

include "../_header.php";
include "../_left_menu.php";

if (!isset($cfg[bill_yn])) $checked[bill_yn][0] = "checked";
if (!isset($cfg[bill_vat_yn])) $checked[bill_vat_yn][1] = "checked";

$checked[bill_yn][$cfg[bill_yn]] = "checked";
$checked[bill_vat_yn][$cfg[bill_vat_yn]] = "checked";

if ($cfg[bill_nameComp] == "") $cfg[bill_nameComp] = $cfg[nameComp];
if ($cfg[bill_typeBiz] == "") $cfg[bill_typeBiz] = $cfg[typeBiz];
if ($cfg[bill_itemBiz] == "") $cfg[bill_itemBiz] = $cfg[itemBiz];
if ($cfg[bill_regnumBiz] == "") $cfg[bill_regnumBiz] = $cfg[regnumBiz];
if ($cfg[bill_regnumOnline] == "") $cfg[bill_regnumOnline] = $cfg[regnumOnline];
if ($cfg[bill_nameCeo] == "") $cfg[bill_nameCeo] = $cfg[nameCeo];
if ($cfg[bill_managerInfo] == "") $cfg[bill_managerInfo] = $cfg[managerInfo];
if ($cfg[bill_address] == "") $cfg[bill_address] = $cfg[address];
if ($cfg[bill_phoneComp] == "") $cfg[bill_phoneComp] = $cfg[phoneComp];
if ($cfg[bill_faxComp] == "") $cfg[bill_faxComp] = $cfg[faxComp];

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php" enctype="multipart/form-data">
   <input type="hidden" name="mode" value="bill_info" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("견적서정보관리")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("견적서 사용여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="bill_yn" value="1" <?=$checked[bill_yn][1]?>>
            </div>
            <label class="col-md-2 control-label"><?=_("부가세 포함여부")?></label>
            <div class="col-md-3">
               <input type="checkbox" data-render="switchery" data-theme="blue" name="bill_vat_yn" value="1" <?=$checked[bill_vat_yn][1]?>>
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("결제조건")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_payOpt" value="<?=$cfg[bill_payOpt]?>">
               <div><?=_("예)납품후 7일이내 현금")?></div>
            </div>
            <label class="col-md-2 control-label"><?=_("유효기간")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_expDt" value="<?=$cfg[bill_expDt]?>">
               <div><?=_("예)견적일로부터 1개월")?></div>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("상호명")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_nameComp" value="<?=$cfg[bill_nameComp]?>">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("업태")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_typeBiz" value="<?=$cfg[bill_typeBiz]?>">
            </div>
            <label class="col-md-2 control-label"><?=_("종목")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_itemBiz" value="<?=$cfg[bill_itemBiz]?>">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("사업자번호")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_regnumBiz" value="<?=$cfg[bill_regnumBiz]?>">
            </div>
            <label class="col-md-2 control-label"><?=_("통신판매 신고번호")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_regnumOnline" value="<?=$cfg[bill_regnumOnline]?>">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("대표자명")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_nameCeo" value="<?=$cfg[bill_nameCeo]?>">
            </div>
            <label class="col-md-2 control-label"><?=_("담당자")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_managerInfo" value="<?=$cfg[bill_managerInfo]?>">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("주소")?></label>
            <div class="col-md-10 form-inline">
               <input type="text" class="form-control" name="zipcode" id="zipcode" value="<?=$cfg[bill_zipcode]?>" readonly>
               <i class="fa fa-search cursorPointer" onclick="javascript:popupZipcode<?=$language_locale?>()"></i><br><br>
               <input type="text" class="form-control" name="address" value="<?=$cfg[bill_address]?>" size="100">
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("전화번호")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_phoneComp" value="<?=$cfg[bill_phoneComp]?>">
            </div>
            <label class="col-md-2 control-label"><?=_("팩스번호")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_faxComp" value="<?=$cfg[bill_faxComp]?>">
            </div>
         </div>

        <!--청구서에서 사용-->
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("입금계좌정보")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_bank_name" value="<?=$cfg[bill_bank_name]?>" placeholder="은행명">
            </div>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_bank_no" value="<?=$cfg[bill_bank_no]?>" placeholder="계좌번호">
            </div>
            <div class="col-md-3">
               <input type="text" class="form-control" name="bill_bank_owner" value="<?=$cfg[bill_bank_owner]?>" placeholder="예금주명">
            </div>
         </div>

         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("기타 특이사항")?></label>
            <div class="col-md-10">
               <textarea id="bill_Etc" class="form-control" name="bill_Etc"><?=$cfg[bill_Etc]?></textarea>
            </div>
         </div>
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("직인이미지")?></label>
            <div class="col-md-10 form-inline">
               <? if (is_file("../../data/bill/$cid/bill_seal.png")) { ?><img src="../../data/bill/<?=$cid?>/bill_seal.png" /><? } ?>
               <input type="file" class="form-control" name="bill_seal" size="40">
			   <? if (is_file("../../data/bill/$cid/bill_seal.png")) { ?><input type="checkbox" class="form-control" name="d[bill_seal]/"> <?=_("삭제")?><? } ?>
			   <div><span class="warning">[<?=_("주의")?>]</span> <b>53px * 53px</b> <?=_("이하 사이즈의")?> <b>png</b> <?=_("파일로 업로드해주세요.")?></div>
            </div>
         </div>
         
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("사업자등록증 이미지")?></label>
            <div class="col-md-10 form-inline">
               <? if (is_file("../../data/bill/$cid/tax_seal.jpg")) { ?><img src="../../data/bill/<?=$cid?>/tax_seal.jpg" width="100" /><? } ?>
               <input type="file" class="form-control" name="tax_seal" size="40">
			   <? if (is_file("../../data/bill/$cid/tax_seal.jpg")) { ?><input type="checkbox" class="form-control" name="d[tax_seal]/"> <?=_("삭제")?><? } ?>
			   <div><span class="warning">[<?=_("주의")?>]</span> <b>3M</b> <?=_("이하 사이즈의")?> <b>jpg</b> <?=_("파일로 업로드해주세요.")?></div>
            </div>
         </div>
         
         
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("통장 이미지")?></label>
            <div class="col-md-10 form-inline">
               <? if (is_file("../../data/bill/$cid/bank_seal.jpg")) { ?><img src="../../data/bill/<?=$cid?>/bank_seal.jpg" width="100" /><? } ?>
               <input type="file" class="form-control" name="bank_seal" size="40">
			   <? if (is_file("../../data/bill/$cid/bank_seal.jpg")) { ?><input type="checkbox" class="form-control" name="d[bank_seal]/"> <?=_("삭제")?><? } ?>
			   <div><span class="warning">[<?=_("주의")?>]</span> <b>3M</b> <?=_("이하 사이즈의")?> <b>jpg</b> <?=_("파일로 업로드해주세요.")?></div>
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
});
</script>

<? include "../_footer_app_exec.php"; ?>