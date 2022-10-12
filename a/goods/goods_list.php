<?
include "../_header.php";
include "../_left_menu.php";

//몰 카테고리 분류
$m_goods = new M_goods();
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

$r_nodp = array(_("진열"), _("미진열"));
$r_brand = get_brand();
$r_rid = get_release();
$r_bid = getBusiness();

$checked[shiptype][$_POST[shiptype]] = "checked";
$checked[isdp][$_POST[isdp]] = "checked";
$checked[state][$_POST[state]] = "checked";
$selected[release][$_POST[release]] = "selected";
$selected[rid][$_POST[rid]] = "selected";
$selected[brandno][$_POST[brandno]] = "selected";
if ($_POST[category_like_off]){
   $checked[category_like_off] = "checked";
}
if ($_POST[iscat]){
   $checked[iscat] = "checked";
}

$postData = base64_encode(json_encode($_POST));
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
         <?=_("상품리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("상품리스트")?></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("상품리스트")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post" action="">
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("상품분류")?></label>
                     <div class="col-md-5 form-inline">
                        <select class="form-control" name="catno">
                           <option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, $_POST[catno])?>
                        </select>
                        <input type="checkbox" name="iscat" <?=$checked[iscat]?>/> <?=_("카테고리 미매칭 상품")?>
                        <input type="checkbox" name="category_like_off" <?=$checked[category_like_off]?>/> <?=_("하위카테고리제외")?>
                     </div>
                     
                     <label class="col-md-1 control-label"><?=_("공급사/브랜드")?></label>
                     <div class="col-md-5 form-inline">
                        <select class="form-control" name="rid" onchange="getBrand_rid(this.value,'brandno')">
                        <option value=""><?=_("공급사선택")?></option>
                        <? foreach ($r_rid as $k=>$v){ ?>
                        <option value="<?=$k?>" <?=$selected[rid][$k]?>><?=$v?></option>
                        <? } ?>
                        </select>
                        <select class="form-control" name="brandno" id="brandno">
                        <option value=""><?=_("브랜드선택")?></option>
                        <? foreach ($r_brand as $k=>$v){ ?>
                        <option value="<?=$k?>" <?=$selected[brandno][$k]?>><?=$v."(".$k.")"?></option>
                        <? } ?>
                        </select>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("등록일")?></label>
                     <div class="col-md-5">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start_date" placeholder="Date Start" value="<?=$_POST[start_date]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end_date" placeholder="Date End" value="<?=$_POST[end_date]?>" />
                        </div>
                     </div>
                     
                     <label class="col-md-1 control-label"><?=_("상품가격")?></label>
                     <div class="col-md-5 form-inline">
                        <input type="text" class="form-control" name="price[]" value="<?=$_POST[price][0]?>" onkeypress="onlynumber()"/> <?=_("원")?> ~
                        <input type="text" class="form-control" name="price[]" value="<?=$_POST[price][1]?>" onkeypress="onlynumber()"/> <?=_("원")?>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("판매여부")?></label>
                     <div class="col-md-5">
                        <input type="radio" name="state" value="" <?=$checked[state][""]?>/> <?=_("전체")?>
                        <?foreach($r_goods_state as $k=>$v){?>
                        <input type="radio" name="state" value="<?=$k?>" <?=$checked[state][$k]?>/> <?=$v?>
                        <?}?>
                     </div>
                     
                     <label class="col-md-1 control-label"><?=_("진열여부")?></label>
                     <div class="col-md-5">
                        <input type="radio" name="isdp" value="" <?=$checked[isdp][""]?>/> <?=_("전체")?>
                        <input type="radio" name="isdp" value="0" <?=$checked[isdp][0]?>/> <?=_("진열")?>
                        <input type="radio" name="isdp" value="1" <?=$checked[isdp][1]?>/> <?=_("미진열")?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("재고수량")?></label>
                     <div class="col-md-5 form-inline">
                        <input type="text" class="form-control" name="totstock[]" pt="_pt_numplus" value="<?=$_POST[totstock][0]?>"/> ~ <input type="text" class="form-control" name="totstock[]" pt="_pt_numplus" value="<?=$_POST[totstock][1]?>"/>
                     </div>

                     <label class="col-md-1 control-label"><?=_("배송비")?></label>
                     <div class="col-md-5">
                        <input type="radio" name="shiptype" value="" <?=$checked[shiptype][""]?>/> <?=_("전체")?>
                        <?=makeShiptypeRadioTag("shiptype", $checked[shiptype], "3")?>
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("검색")?></label>
                     <div class="col-md-5">
                        <input type="text" class="form-control" name="sword" value="<?=$_POST[sword]?>" class="w300"/>
                     </div>
                  </div>

                  <div class="col-md-1">
                     <button type="submit" class="btn btn-sm btn-inverse">
                        <?=_("검색")?>
                     </button>
                  </div>
               </form>
            </div>

            <div class="panel-body">
               <div class="table-responsive">
                  <table id="data-table" class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>
                           <th><?=_("번호")?></th>
                           <th><?=_("이미지")?></th>
                           <th><?=_("상품명")?></th>
                           <th><?=_("공급/브랜드")?></th>
                           <th><?=_("가격")?></th>
                           <!--<th><?=_("재고")?></th>-->
                           <th><?=_("적립금")?></th>
                           <th><?=_("배송비")?></th>
                           <th><?=_("등록일")?></th>
                           <!--<th><?=_("진열")?></th>-->
                           <!--<th><?=_("가격수정")?></th>-->
                           <!--<th><?=_("옵션 이미지 등록")?></th>-->
                           <th><?=_("수정")?></th>
                           <th><?=_("삭제")?></th>
                        </tr>
                     </thead>
                  </table>
               </div>
            </div>
         </div>
         
   		<div class="panel panel-inverse"> 
       		<div class="panel-heading">
           		<h4 class="panel-title"><?=_("추가 설정")?></h4>
      		</div>
         	<div class="panel-body">
	            <div class="form-group">
	               <label class="col-md-2 control-label"><?=_("적용 상품") ?></label>
	               <div class="col-md-4">
						<input type="radio" name="range" checked/> <?=_("선택상품")?>
						<input type="radio" name="range" /> <?=_("검색상품전체")?>
	               </div>
	               <label class="col-md-6 control-label">
	                  <span class="desc gray" style="margin:10px"><span class="warning">[<?=_("주의")?>]</span> <?=_("적용시 실제 판매에 적용되므로 주의하시기 바랍니다.")?></span>
				   </label>
	            </div>
      		</div>
   		</div>

         <div class="panel panel-inverse">
   			<ul class="nav nav-tabs">
   				<li class="active"><a href="#default-tab-1" data-toggle="tab"><?=_("가격수정")?></a></li>
   				<li class=""><a href="#default-tab-2" data-toggle="tab"><?=_("적립금수정")?></a></li>
   				<li class=""><a href="#default-tab-3" data-toggle="tab"><?=_("분류연결/이동")?></a></li>
   				<li class=""><a href="#default-tab-4" data-toggle="tab"><?=_("판매상태수정")?></a></li>
   				<li class=""><a href="#default-tab-5" data-toggle="tab"><?=_("일괄삭제")?></a></li>
   			</ul>
   			<div class="tab-content">
   				<div class="tab-pane fade active in" id="default-tab-1">

   				    <div id="bottom_price">
   						<form method="POST" action="indb.php" onsubmit="return get_param(this)">
   						<input type="hidden" name="mode" value="bottom_price"/>
   						<input type="hidden" name="range"/>
   						<input type="hidden" name="mquery"/>

   						<div>

      						<select name="rate_cutprice_criterion">
         						<option value="price"><?=_("판매가")?></option>
         						<option value="mall_cprice"><?=_("소비자가(몰)")?></option>
      						</select> <?=_("를 기준으로")?>

      						<select name="pricekind" style="font: 9pt '돋움';color:red">
         						<option value="price"><?=_("판매가")?></option>
         						<option value="mall_cprice"><?=_("소비자가(몰)")?></option>
      						</select> <?=_("를")?>

      						<input type="text" name="price" size="17"/>
      						<select name="unit">
         						<option value="per">%</option>
         						<option value="won"><?=_("원")?></option>
      						</select>

      						<select name="change">
         						<option value="dc"><?=_("할인")?></option>
         						<option value="add"><?=_("할증")?></option>
      						</select>
      						<?=_("된 가격으로 일괄수정합니다.")?>

      						<div class="desc"><?=_("기준가의 값이 없거나 0 일 경우 업데이트 되지 않습니다.")?></div>
      						</p>

      						<?=_("새로운 가격은")?>
      						<select name="digit">
         						<option value="10">1
         						<option value="100">10
         						<option value="1000">100
         						<option value="10000">1000
      						</select> <?=_("원 단위에서")?>
   
      						<select name="math">
         						<option value="floor"><?=_("내림")?>
         						<option value="ceil"><?=_("올림")?>
         						<option value="round"><?=_("반올림")?>
      						</select>
      						<?=_("합니다.")?>

   						</div>

   						<div class="btn">
   						   <button type="submit" class="btn btn-primary m-r-5 m-b-5"><?=_("확인")?></button>
   						</div>

   						</form>
   				    </div>

   				</div>
   				<div class="tab-pane fade" id="default-tab-2">
   				    <div id="bottom_reserve">
   						<form method="POST" action="indb.php" onsubmit="return get_param(this)">
   						<input type="hidden" name="mode" value="bottom_reserve"/>
   						<input type="hidden" name="range"/>
   						<input type="hidden" name="mquery"/>

   						<?=_("적립금을 가격의")?> <input type="text" name="price" size="3" pt="_pt_numdot"/>%
   						<!--
   						<select name="unit">
   						<option value="per">%</option>
   						<option value="won">원</option>
   						</select>
   						<select name="change">
   						<option value="dc">할인</option>
   						<option value="add">할증</option>
   						</select>
   						-->
   						<?=_("된 가격으로 일괄수정합니다.")?></p>

   						<div class="btn">
   						<button type="submit" class="btn btn-primary m-r-5 m-b-5"><?=_("확인")?></button>
   						</div>	
   				
   						</form>
   				    </div>
   				</div>
   				<div class="tab-pane fade" id="default-tab-3">
   					<div id="bottom_cat">
   						<form method="POST" action="indb.php" onsubmit="return get_param(this)">
   						<input type="hidden" name="mode" value="bottom_cat"/>
   						<input type="hidden" name="range"/>
   						<input type="hidden" name="mquery"/>
   
                     <div class="col-md-10 form-inline" id="category2">
                        <select name="catno2[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("1차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[0][catno])?></select>
                        <select name="catno2[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("2차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[1][catno])?></select>
                        <select name="catno2[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("3차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[2][catno])?></select>
                        <select name="catno2[]" class="form-control"><option value="" style="background:#656263;color:#FFFFFF;">+ <?=_("4차 분류 선택")?></option><?=conv_selected_option($ca_list, $catLinkNo[3][catno])?></select>
                     </div>

   						<div class="btn">
   						<button type="submit" class="btn btn-primary m-r-5 m-b-5"><?=_("확인")?></button>
   						</div>

   						</form>
   				    </div>
   				</div>
   				<div class="tab-pane fade" id="default-tab-4">
   					<div id="bottom_option">
   						<form method="POST" action="indb.php" onsubmit="return get_param(this)">
   						<input type="hidden" name="mode" value="bottom_option"/>
   						<input type="hidden" name="range"/>
   						<input type="hidden" name="mquery"/>

   						<table class="tb1">
   						<tr>
   							<th><?=_("진열여부")?></th>
   							<td>
   							<input type="radio" name="isdp" value="0" checked/> <?=_("진열")?>
   							<input type="radio" name="isdp" value="1"/> <?=_("미진열")?>
   							</td>
   						</tr>
   						</table>

   						<div class="btn">
   						<button type="submit" class="btn btn-primary m-r-5 m-b-5"><?=_("확인")?></button>
   						</div>

   						</form>
   				    </div>
   				</div>
   				<div class="tab-pane fade" id="default-tab-5">
   					<div id="bottom_delete">
   						<form method="POST" action="indb.php" onsubmit="return get_param(this)">
   						<input type="hidden" name="mode" value="bottom_delete"/>
   						<input type="hidden" name="range"/>
   						<input type="hidden" name="mquery"/>

   						<div class="btn">
   						<button type="submit" class="btn btn-primary m-r-5 m-b-5"><?=_("삭제")?></button>
   						</div>

   						</form>
   				    </div>
   				</div>
   			</div>
         </div>
      </div>
   </div>
</div>
<!-- end #content -->

<script src="../../js/webtoolkit.base64.js"></script>
<script>
function get_param(f){
	if (!confirm('<?=_("변경된 가격이나 정보는 몰에 바로 반영되며 복구되지 않습니다.")?>' + "\n" + '<?=_("반드시 확인하시기 바랍니다.")?>' + "\n" + '<?=_("수정하시겠습니까?")?>')){
		return false;
	}

	var tmp = [];
	var cnt_member = 0;
	f.range.value = (document.getElementsByName('range')[0].checked) ? 'selmember' : 'allmember';

	if (f.range.value=='allmember'){

        //alert(document.getElementsByName('query[]')[0].value);
        //f.mquery.value = Base64.encode(document.getElementsByName('query[]')[0].value);
        f.mquery.value = document.getElementsByName('query[]')[0].value;
        //alert(f.mquery.value);
        if (f.mquery.value == ""){
            alert('<?=_("선택된 상품이 없습니다.")?>',-1);
            return false;
        }
        
		//var goodsno = document.getElementsByName('goodsno');
		//for (var i=0; i<goodsno.length; i++){
		//	tmp[i] = goodsno[i].value;
		//}
		//f.mquery.value = $j("[name=query]").val();

	} else if (f.range.value=='selmember'){

		var c = document.getElementsByName('chk[]');
		for (var i=0;i<c.length;i++){
			if (c[i].checked) tmp[tmp.length] = c[i].value;
		}
		if (tmp[0]){
			f.mquery.value = Base64.encode("select * from exm_goods_cid where cid = '<?=$cid?>' and goodsno in ('" + tmp.join("','") + "')");
			//alert(f.mquery.value);
			cnt_member = tmp.length;
		}else{
			alert('<?=_("선택된 상품이 없습니다.")?>',-1);
			return false;
		}
	}
}
</script>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
         "sPaginationType" : "bootstrap",
         "aaSorting" : [[1, "desc"]],
         "bFilter" : false,
         "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
         },
         "aoColumns": [
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": true },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         { "bSortable": true },
         //{ "bSortable": false },
         //{ "bSortable": false },
         { "bSortable": false },
         { "bSortable": false },
         ],
         "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
         //"scrollX" : true,
         "ajax": $.fn.dataTable.pipeline({
            url: './goods_list_page.php?postData=<?=$postData?>',
            pages: 5 // number of pages to cache
         })
      });
   });
   
   var handleDatepicker = function() {
      $('.input-daterange').datepicker({
         language : 'kor',
         todayHighlight : true,
         autoclose : true,
         todayBtn : true,
         format : 'yyyy-mm-dd',
      });
   };

   handleDatepicker();
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>