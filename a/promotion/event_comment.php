<?

include "../_header.php";
include "../_left_menu.php";

$m_etc = new M_etc();

$addWhere = "where cid='$cid'";

### 이벤트리스트추출
$r_event = $m_etc->getEventList($cid, $addWhere);
$r_bottommenu = array("event_emoney"=>_("적립금발급"));

$selected[eventno][$_POST[eventno]+0] = "selected";
$checked[emoney][$_POST[emoney]+0] = $checked[hidden][$_POST[hidden]+0] = "checked";

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
         <?=_("코멘트관리")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("코멘트관리")?> <small><?=_("이벤트의 코멘트를 관리하실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
		   <div class="panel panel-inverse"> 
		      <div class="panel-heading">
		         <h4 class="panel-title"><?=_("코멘트관리")?></h4>
		      </div>
		      
		      <div class="panel-body panel-form">
		         <form class="form-horizontal form-bordered" method="post" action="event_comment.php">
			         <div class="form-group">
			         	<label class="col-md-2 control-label"><?=_("이벤트")?></label>
			         	<div class="col-md-10 form-inline">
			         		<select name="eventno" class="form-control">
			               	   <option value=""><?=_("이벤트 선택")?></option>
			               	   <? foreach ($r_event as $k=>$v) { ?>
			               	   	  <option value="<?=$v[eventno]?>" <?=$selected[eventno][$v[eventno]]?>><?=$v[title]?></option>
			               	   <? } ?>
			                </select>
			         	</div>
			         </div>
			         
			         <div class="form-group">
		          	 	 <label class="col-md-2 control-label"><?=_("작성일자")?></label>
		          	 	 <div class="col-md-4">
		          	 		 <div class="input-group input-daterange">
		          	 			<input type="text" class="form-control" name="start" placeholder="Date Start" value="<?=$_POST[start]?>">
		          	 			<span class="input-group-addon"> ~ </span>
		          	 			<input type="text" class="form-control" name="end" placeholder="Date End" value="<?=$_POST['end']?>">
		          	 		 </div>
		          	 	 </div>
		          	 	
		          	 	 <div class="col-md-6">
		          	 	 	 <button type="button" class="btn btn-sm btn-<?=$button_color[yesterday]?>" onclick="regdtOnlyOne('yesterday','start', 'yesterday'); regdtOnlyOne('today','end');"><?=_("어제")?></button>
		          	 		 <button type="button" class="btn btn-sm btn-<?=$button_color[today]?>" onclick="regdtOnlyOne('today','start', 'today'); regdtOnlyOne('today','end');"><?=_("오늘")?></button>
		          	 		 <button type="button" class="btn btn-sm btn-<?=$button_color[tdays]?>" onclick="regdtOnlyOne('tdays','start', 'tdays'); regdtOnlyOne('today','end');"><?=_("3일")?></button>
		          	 		 <button type="button" class="btn btn-sm btn-<?=$button_color[week]?>" onclick="regdtOnlyOne('week','start', 'week'); regdtOnlyOne('today','end');"><?=_("1주일")?></button>
		          	 	 </div>
		          	 </div>
	          	 
	          	 	 <div class="form-group">
			         	<label class="col-md-2 control-label"><?=_("검색")?></label>
			         	<div class="col-md-10 form-inline">
			         		<input type="text" class="form-control" name="sword" value="<?=$_POST[sword]?>" size="40">
			         		<div><span class="notice">[<?=_("설명")?>]</span> <?=_("아이디 또는 코멘트를 검색합니다.")?></div>
			         	</div>
			         </div>
			         
			         <div class="form-group">
			            <label class="col-md-2 control-label"><?=_("적립금지급여부")?></label>
			            <div class="col-md-10">
			               <input type="radio" class="radio-inline" name="emoney" value="0" <?=$checked[emoney][0]?>> <?=_("전체")?>
			      	 	   <input type="radio" class="radio-inline" name="emoney" value="1" <?=$checked[emoney][1]?>> <?=_("미지급")?>
			      	 	   <input type="radio" class="radio-inline" name="emoney" value="2" <?=$checked[emoney][2]?>> <?=_("지급")?>
			            </div>
			         </div>
			         
			         <div class="form-group">
			            <label class="col-md-2 control-label"><?=_("노출여부")?></label>
			            <div class="col-md-8">
			               <input type="radio" class="radio-inline" name="hidden" value="0" <?=$checked[hidden][0]?>> <?=_("전체")?>
			      	 	   <input type="radio" class="radio-inline" name="hidden" value="2" <?=$checked[hidden][2]?>> <?=_("미노출")?>
			      	 	   <input type="radio" class="radio-inline" name="hidden" value="1" <?=$checked[hidden][1]?>> <?=_("노출")?>
			            </div>
			            <div class="col-md-2">
			         		<button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
			         	</div>
			         </div>
			         
			         <div class="panel-body">
			         	<div class="table-responsive">
			         		<table id="data-table" class="table table-striped table-bordered">
			         			<thead>
			         				<tr>
			         					<th><a href="javascript:chkBox('chk[]','rev')"><?=_("선택")?></a></th>
			         					<th><?=_("코멘트내용")?></th>
			         					<th><?=_("작성자")?></th>
			         					<th><?=_("작성일자")?></th>
			         					<th><?=_("이벤트명 (번호)")?></th>
			         					<th><?=_("적립금")?></th>
			         					<th><?=_("노출여부")?></th>
			         				</tr>
			         			</thead>
			         		</table>
			         	</div>
		         	</div>
		         </form>
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
   	  	  	 		<!--<input type="radio" class="radio-inline" name="range"> 검색항목-->
   	  	  	 		<p><div><span class="warning">[<?=_("주의")?>]</span> <?=_("변경시 복구가 불가능하므로 주의하시기 바랍니다.")?></div>
   	  	  	 	</div>
   	  	  	 	
   	  	  	 	<? foreach ($r_bottommenu as $k=>$v) { ?>
   	  	  	 		<div class="tab-pane" id="default-tab-<?=$k?>">
   	  	  	 			<form method="post" action="indb.php" onsubmit="return submitContents(this);">
   	  	  	 				<input type="hidden" name="mode" value="bottom_<?=$k?>">
   	  	  	 				<input type="hidden" name="range">
   	  	  	 				<input type="hidden" name="bno">
   	  	  	 				
        					<p><div><?=_("적립금액")?> <input type="text" class="form-control" name="emoney" required size="8" maxlength="7" type2="number"><?=_("원을 적립합니다.")?></div>
        					<div><span class="notice">[<?=_("설명")?>]</span> <?=_("적립금을 삭감하실때는 적립금액에 - 를 넣으시면 됩니다.")?></div>
   	  	  	 				
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

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/js/smarteditor/js/HuskyEZCreator.js" charset="utf-8"></script>

<script type="text/javascript">
/* Table initialisation */
$(document).ready(function() {
	$('#data-table').dataTable({
		"sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
		"sPaginationType" : "bootstrap",
		"bFilter" : false,
		"oLanguage" : {
			"sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
		},
		"aoColumns": [
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		{ "bSortable": false },
		],
		"processing": false,
		"serverSide": true,
		"bAutoWidth": false,
		"ajax": $.fn.dataTable.pipeline({
			url: 'event_comment_page.php?postData=<?=$postData?>',
			pages: 5 //number of pages to cache
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

$j(function() {
	$j(".nav-tabs>li:first").addClass("active");
	$j(".tab-pane:first").addClass("active in");
	
	$j('input[type2=number]').css('ime-mode', 'disabled').keypress(function(event) {
		if (event.which && event.which != 45 && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});

function chg_hidden(obj, no) {
	var hidden = (obj.src.indexOf("btn_on.gif") > 0) ? '0' : '1';
	$j.post("indb.php", {mode:'event_comment_hidden', no:no, hidden:hidden},
		function(chg_hidden) {
			obj.src = (chg_hidden == "0") ? obj.src.replace("\_off.gif", "\_on.gif") : obj.src.replace("\_on.gif", "\_off.gif");
	});
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
	
	if (!confirm('<?=_("수정 및 삭제된 내용은 복구가 불가능합니다.")?>\n<?=_("계속하시겠습니까?")?>')) {
		return false;
	}
}
</script>

<script src="../js/datatable_page.js"></script>

<? include "../_footer_app_exec.php"; ?>