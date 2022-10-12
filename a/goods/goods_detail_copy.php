<?

include "../_header.php";
include "../_left_menu.php";


//몰 카테고리 분류
$m_goods = new M_goods();
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

$auth_code = sprintf('%03d', mt_rand(0, 999)).date("Y-m-d H:i:s").sprintf('%03d', mt_rand(0, 999));
$auth_code = md5_encode($auth_code, 1);
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <!-- begin #header -->
   <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb_other.php">
   <input type="hidden" name="mode" value="goods_detail_copy" />
   <input type="hidden" name="mode" value="goods_detail_copy" />
   
   <div class="panel panel-inverse"> 
      <div class="panel-heading">
         <h4 class="panel-title"><?=_("상품 상세 설명 복사")?></h4>
      </div>

      <div class="panel-body panel-form">
         <div class="form-group">
            <label class="col-md-2 control-label"><?=_("원본 상품코드")?></label>
            <div class="col-md-3">
               <input type="text" class="form-control" name="o_goods_code" value="">
            </div>
         </div>         
      
      
      
      
	      <div class="form-group">
	         <label class="col-md-2 control-label"><?=_("복사 대상") ?></label>
	         <div class="col-md-10">
	              <input type="radio" name="copy_range" value="c_cate" onclick="showGoodsCate(false, false);" checked/> <?=_("원본상품이 등록된 카테고리 등록된 전체 상품")?>	              
	              <input type="radio" name="copy_range" value="category" onclick="showGoodsCate(false, true);"/> <?=_("특정 카테고리 등록된 전체상품")?>
	              <input type="radio" name="copy_range" value="goodscode" onclick="showGoodsCate(true, false);"/> <?=_("특정상품")?>
	              
	              <div><span class="warning">[<?=_("주의")?>]</span> <?=_("다중카테고리 등록상품의 경우 모든 카테고리 상세설명이 변경됩니다. 상품설명 변경시 복구가 불가능하므로 주의하시기 바랍니다.")?></div>
	         </div>         
	      </div>
	      
	      <div class="form-group" id="t_goods" style="display: none;">
	         <label class="col-md-2 control-label"></label>
	         <div class="col-md-10">
	         		<input type="text" class="form-control" name="t_goods_code" value="" placeholder="상품코드를 입력하세요.">
	         		<div><span class="warning">[<?=_("주의")?>]</span> <?=_("여러개의 상품코드는 콤마(,)로 구분해 주세요.")?> (ex: 123,345,2435,3456)</div>        		
	         </div>
	      </div>          
	      
	      <div class="form-group" id="t_cate" style="display: none;">
	         <label class="col-md-2 control-label"></label>
	         <div class="col-md-6">
	         		<select name="t_catno" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list)?></select>	         		        		
	         </div>
	         <div class="col-md-4">
	         		<input type="checkbox" name="t_catno_sub" value="Y">하위 카테고리 포함 전체 적용	         		        		
	         </div>
	      </div>
	      
	      <div class="form-group">
	         <label class="col-md-2 control-label">보안코드</label>
	         <div class="col-md-10">
	         		<input type="text" class="form-control" name="auth_code" value="<?=$auth_code?>" readonly>	   
	         		<div><span class="warning">[<?=_("주의")?>]</span> <?=_("보안코드는 1회용이며, 보안을 위해 해당화면에서 3분내 처리하지 않을경우 보안코드는 만료됩니다.")?></div>           		        		
	         </div>	         
	      </div>
	    </div>
   </div>
      
   
   
   <div class="row">
   	  <div class="col-md-12">
   	  	 <p class="pull-right">
   	  	 	<button type="button" class="btn btn-md btn-primary m-r-15" onclick="goSubmit();"><?=_("처리")?></button>
   	  	 	<button type="button" class="btn btn-md btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
   	  	 </p>
   	  </div>
   </div>
   </form>
</div>

<? include "../_footer_app_init.php"; ?>

<script type="text/javascript" src="../assets/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="../assets/js/form-slider-switcher.demo.js"></script>
<script type="text/javascript" src="../../js/webtoolkit.base64.js"></script>

<script type="text/javascript">
function showGoodsCate(vGoodsFlag, vCateFlag) {
	if (vGoodsFlag)
		$("#t_goods").show();
	else
		$("#t_goods").hide();
		
	if (vCateFlag)
		$("#t_cate").show();
	else
		$("#t_cate").hide();
}


function goSubmit()
{
	if (document.fm.o_goods_code.value == '') {
		alert("필수 정보가 없습니다.");
		document.fm.o_goods_code.focus();
		return false;
	} else {		
		if (confirm("변경된 설명은 복구되지 않습니다.\r\n상품설명을 복사하시겠습니까? "))
		{
			document.fm.submit();		
		}
	}
}

</script>

<? include "../_footer_app_exec.php"; ?>