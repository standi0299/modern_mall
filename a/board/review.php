<?

include "../_header.php";
include "../_left_menu.php";
include_once dirname(__FILE__)."/../../lib2/db_common.php";
include_once dirname(__FILE__)."/../../models/m_common.php";

$m_goods = new M_goods();
$r_bottommenu = array("review_emoney"=>_("적립금발급"),"review_delete"=>_("일괄삭제"));

//몰 카테고리 분류
$cate_data = $m_goods->getCategoryList($cid);
$ca_list = makeCategorySelectOptionTag($cate_data);

if($_POST[sword] && !$_POST[stype]) unset($_POST[sword]);

$addQuery = '';
if ($_POST[stype] && $_POST[sword]) $addQuery .= " and $_POST[stype] like '%$_POST[sword]%'";
if ($_POST[start]) $addQuery .= " and regdt >= '$_POST[start] 00:00:00'";
if ($_POST[end]) $addQuery .= " and regdt <= '$_POST[end] 23:59:59'";
if (is_array($_POST[catno])) {
	list($_POST[catno]) = array_slice(array_notnull($_POST[catno]), -1);
	$addQuery .= " and catno like '$_POST[catno]%'";
}
if ($_POST[kind]) $addQuery .= " and kind = '$_POST[kind]'";
if ($_POST[emoney][0]) $addQuery .= " and emoney >= '{$_POST[emoney][0]}'";
if ($_POST[emoney][1]) $addQuery .= " and emoney <= '{$_POST[emoney][1]}'";

$orderby = " order by no desc ";
$addQuery .= $orderby;

$limit = " limit 10";

$tableName = "exm_review a";
$bRegistFlag = false;
$selectArr = "a.*,(select goodsnm from exm_goods where goodsno=a.goodsno) goodsnm";
$whereArr = array("cid" => "$cid");

$list = SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $addQuery, $limit);
$totalCnt = count(SelectListTable($tableName, $selectArr, $whereArr, $bRegistFlag, $addQuery));

$selected[stype][$_POST[stype]]  = "selected";
$selected[kind][$_POST[kind]] = "selected";

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
         <?=_("상품후기")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("상품후기")?> <small><?=_("상품후기 관리")?>.</small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("상품후기")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post" action="review.php">
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("검색")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="stype" class="form-control">
                        <option value=""><?=_("선택")?>
                        <option value="name" <?=$selected[stype][name]?>><?=_("작성자")?>
                        <option value="subject" <?=$selected[stype][subject]?>><?=_("제목")?>
                        <option value="content" <?=$selected[stype][content]?>><?=_("내용")?>
                        </select>
                        <input type="text" class="form-control" name="sword" value="<?=$_POST[sword]?>">
                     </div>
                  </div>

                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("작성일")?></label>
                     <div class="col-md-5">
                       <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_POST[start]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_POST[end]?>" />
                        </div>
                     </div>
                     <div class="col-md-5">
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');"><?=_("오늘")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('tdays','start', 'tdays'); regdtOnlyOne('today','end');"><?=_("3일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');"><?=_("1주일")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('month','start', 'month'); regdtOnlyOne('today','end');"><?=_("1달")?>
                        </button>
                        <button type="button" class="btn btn-sm btn-inverse" onclick="regdtOnlyOne('all','start', 'all'); regdtOnlyOne('today','end');"><?=_("전체")?>
                        </button>
                     </div>
                  </div>
                  
                  <div class="form-group">
                  	<label class="col-md-1 control-label"><?=_("카테고리")?></label>
                  	<div class="col-md-10 form-inline">
                  		<select name="catno[]" class="form-control">
			               <option value="">+ <?=_("분류 선택")?></option><?=conv_selected_option($ca_list, $_POST[catno])?>
			            </select>
	         		</div>
	         	  </div>
                  
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("구분")?></label>
                     <div class="col-md-10 form-inline">
                        <select name="kind" class="form-control">
							<option value=""><?=_("전체")?></option>
							<? foreach ($r_kind as $k => $v) { ?>
							<option value="<?=$k?>" <?=$selected[kind][$k]?>><?=$v?></option>		
							<? } ?>
						</select>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("적립금")?></label>
                     <div class="col-md-10 form-inline">
                        <input type="text" class="form-control" name="emoney[]" size="8" type2="number" value="<?=$_POST[emoney][0]?>"> <?=_("원")?> ~ 
                        <input type="text" class="form-control" name="emoney[]" size="8" type2="number" value="<?=$_POST[emoney][1]?>"> <?=_("원")?>
                     </div>
                     <div class="col-md-1">
                        <button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
                     </div>
                  </div>
               </form>

               <div class="panel-body">
                  <form name="form1" method="post" action="indb.php">
                     <input type="hidden" name="mode">
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                              	 <th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>
                                 <th><?=_("번호")?></th>
                                 <th><?=_("이미지")?></th>
                                 <th><?=_("주문번호")?></th>
                                 <th><?=_("작성자")?></th>
                                 <th><?=_("상품평")?></th>
                                 <th><?=_("적립금")?></th>
                                 <th><?=_("작성일자")?></th>
                                 <th><?=_("노출")?></th>
                                 <th><?=_("수정")?></th>
                                 <th><?=_("삭제")?></th>
                                 <th><?=_("메인노출")?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <? if($list) {
                              		foreach ($list as $key => $value) { ?>
                                 <tr>
                                 	<td><input type="checkbox" name="chk[]" value="<?=$value[no]?>"></td>
                                    <td>
                                    	<input type="hidden" name="no" value="<?=$value[no]?>">
                                    	<?=$key+1?>
                                    </td>
                                    <td><?=goodsListImg($value[goodsno],50,50)?></td>
                                    <td>
                                    	<div><?=_("주문번호")?> : <?=$value[payno]?>_<?=$value[ordno]?>_<?=$value[ordseq]?></div>
										<div><?=_("상품명")?> : <b class="red">[<?=$value[goodsno]?>]</b> <?=$value[goodsnm]?></div>
										<div><?=_("제목")?> : <?=$value[subject]?></div>
                                    </td>
                                    <td>
                                    	<? if ($value[mid]) { ?>
	         				 			<div><a href="javascript:;" onclick="popup('../member/member_detail_popup.php?mode=member_modify&mid=<?=$value[mid]?>',1100,800)"><?=$value[name]?></a></div>(<?=$value[mid]?>)
	         				 			<? } else { ?>
	         				 			<div><?=$value[name]?></div>(<?=_("비회원")?>)
	         				 			<? } ?>
                                    </td>
                                    <td><?=$r_degree[$value[degree]]?></td>
									<td><?=number_format($value[emoney])?></td>
									<td><?=substr($value[regdt],0,10)?></td>
                                    <td>
                                    	<div><?=_("사용자")?>:<?=($value[review_deny_user]) ? "<b class='red'>N</b>" : "<b class='green'>Y</b>" ?></div>
										<div><?=_("관리자")?>:<?=($value[review_deny_admin]) ? "<b class='red'>N</b>" : "<b class='green'>Y</b>" ?></div>
                                    </td>
                                    <td>
                                       <button type="button" class="btn btn-xs btn-primary" onclick=location.href='review.w.php?no=<?=$value[no]?>'>
                                          <?=_("수정")?>
                                       </button>
                                    </td>
                                    <td>
                                       <button type="button" class="btn btn-xs btn-danger" onclick="review_delete('<?=$value[no]?>','<?=$value[img]?>');">
                                          <?=_("삭제")?>
                                       </button>
                                    </td>
                                    <td>
                                    	<?=($value[main_flag] == "Y") ? _("노출") : _("노출안함") ?>
                                    </td>
                                 </tr>
                              <? }} ?>
                           </tbody>
                        </table>
                     </div>
                  </form>
                  
                <div class="form-group">
                   <div class="col-md-12">
                      <button type="button" class="btn btn-sm btn-success" onclick="location.href='review_write.php';"><?=_("후기등록")?></button>
                      <button type="button" class="btn btn-sm btn-default" onclick="selectMainFlag();"><?=_("메인노출 변경")?></button>
                   </div>
                </div>                  
                  
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <div class="panel-body panel-form">
   	  <div class="form-group">
   	  	  <div class="col-md-12 form-inline">
   	  	  	 <ul class="nav nav-tabs">
   	  	  	 	<? foreach ($r_bottommenu as $k=>$v) { ?>
   	  	  	 		<li class=""><a href="#default-tab-<?=$k?>" data-toggle="tab"><?=$v?></a></li>
   	  	  	 	<? } ?>
   	  	  	 </ul>
   	  	  	 <div class="tab-content">
   	  	  	 	<div class="m-t-10">
   	  	  	 		<input type="radio" class="radio-inline" name="range" checked> <?=_("선택항목")?>
   	  	  	 		<input type="radio" class="radio-inline" name="range"> <?=_("검색항목")?>
   	  	  	 		<p><div><span class="warning">[<?=_("주의")?>]</span> <?=_("변경시 복구가 불가능하므로 주의하시기 바랍니다.")?></div>
   	  	  	 	</div>
   	  	  	 	
   	  	  	 	<? foreach ($r_bottommenu as $k=>$v) { ?>
   	  	  	 		<div class="tab-pane" id="default-tab-<?=$k?>">
   	  	  	 			<form method="post" action="indb.php" onsubmit="return submitContents(this);">
   	  	  	 				<input type="hidden" name="mode" value="bottom_<?=$k?>">
   	  	  	 				<input type="hidden" name="range">
   	  	  	 				<input type="hidden" name="bno">
   	  	  	 				
   	  	  	 				<? if ($k == "review_emoney") { ?>
        					<p><div><?=_("적립금액")?> <input type="text" class="form-control" name="emoney" required size="8" maxlength="7" type2="number"><?=_("원을 적립합니다.")?></div>
        					<div><span class="notice">[<?=_("설명")?>]</span> <?=_("적립금을 삭감하실때는 적립금액에 - 를 넣으시면 됩니다.")?></div>
        					<? } ?>
   	  	  	 				
	   	  	  	 			<div class="text-right m-b-0">
	   	  	  	 				<button type="submit" class="btn btn-sm btn-success"><?=_("확인")?></button>
	   	  	  	 			</div>
   	  	  	 			</form>
   	  	  	 		</div>
   	  	  	 	<? } ?>
   	  	  	 </div>
		 </div>
      </div>
   </div>
</div>

<form name="fm" method="post" action="indb.php">
	<input type="hidden" name="mode" value="change_review_main_flag">
	<input type="hidden" name="review_no">
</form>
<!-- end #content -->

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<!-- ================== END PAGE LEVEL JS ================== -->

<script type="text/javascript">
$j(function() {	
	$j(".nav-tabs>li:first").addClass("active");
	$j(".tab-pane:first").addClass("active in");
	
	$j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
		if (event.which && event.which != 45 && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});

/* Table initialisation */
$(document).ready(function() {
   $('#data-table').dataTable({
      "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
      "sPaginationType" : "bootstrap",
      "aaSorting" : [[1, "desc"]],
      "bFilter" : false,
      "aLengthMenu": [10, 25, 50],
      "oLanguage" : {
          "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
      },
      "aoColumns": [
      { "bSortable": false },
      { "bSortable": true },
      { "bSortable": false },
      { "bSortable": false },
      { "bSortable": false },
      { "bSortable": true },
      { "bSortable": true },
      { "bSortable": true },
      { "bSortable": false },
      { "bSortable": false },
      { "bSortable": false },
      { "bSortable": false },
      ],
      "processing": true,
      "serverSide": true,
      "deferLoading": <?=$totalCnt?>,
      "bAutoWidth": false,
      "ajax": $.fn.dataTable.pipeline( {
         url: 'review_page.php?postData=<?=$postData?>',
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

function review_delete(no, img) {
	if (confirm('<?=_("정말 삭제하시겠습니까?")?>')) location.href = "indb.php?mode=delReview&no=" + no + "&img=" + img;
}

function submitContents(f) {
	if (!form_chk(f)) {
        return false;
    }

	var tmp = [];
	f.range.value = (document.getElementsByName('range')[0].checked) ? 'selmember' : 'allmember';

	if (f.range.value == 'allmember') {
		var no = document.getElementsByName('no');
		
		for (var i=0; i<no.length; i++) {
			tmp[i] = no[i].value;
		}
		
		if (tmp[0]) {
			f.bno.value = tmp;
		} else {
			alert('<?=_("검색된 항목이 없습니다.")?>', -1);
			return false;
		}
	} else if (f.range.value == 'selmember') {
		var c = document.getElementsByName('chk[]');
		
		for (var i=0;i<c.length;i++) {
			if (c[i].checked) tmp[tmp.length] = c[i].value;
		}
		
		if (tmp[0]) {
			f.bno.value = tmp;
		} else {
			alert('<?=_("선택된 항목이 없습니다.")?>', -1);
			return false;
		}
	}
	
	if (!confirm('<?=_("수정 및 삭제된 내용은 복구가 불가능합니다.")?>'+"\n"+'<?=_("계속하시겠습니까?")?>')) {
		return false;
	}
}

function selectMainFlag() {
	var fm = document.fm;
	var c = document.getElementsByName('chk[]');
	var chk = false;
	var cnt = 0;
	
	for (var i=0; i<c.length; i++) {
		if (c[i].checked) {
			fm.review_no.value = c[i].value;
			chk = true;
			cnt++;
		}
	}
	
	if (chk) {
		if (cnt == 1) {
			fm.submit();
		} else {
			alert('<?=_("한 항목만 선택해주시기 바랍니다.")?>');
			return false;
		}
	} else {
		alert('<?=_("메인에 노출할 후기를 선택해주시기 바랍니다.")?>');
		return false;
	}
}
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>