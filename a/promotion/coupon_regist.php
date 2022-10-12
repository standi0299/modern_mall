<?
include "../_header.php";
include "../_left_menu.php";


if ($_GET[coupon_code]) $_POST[coupon_code] = $_GET[coupon_code];

## 쿠폰등록리스트
$postData = base64_encode(json_encode($_POST));

$_POST[coupon_regdt][0] = str_replace("-","",$_POST[coupon_regdt][0]);
$_POST[coupon_regdt][1] = str_replace("-","",$_POST[coupon_regdt][1]);
$_POST[coupon_issuedt][0] = str_replace("-","",$_POST[coupon_issuedt][0]);
$_POST[coupon_issuedt][1] = str_replace("-","",$_POST[coupon_issuedt][1]);

if ($_POST[coupon_code]) $code = $_POST[coupon_code];
if ($_POST[coupon_name]) $name = $_POST[coupon_name];
if ($_POST[coupon_issue_code]) $issue_code = $_POST[coupon_issue_code];
if (is_numeric($_POST[coupon_issue_yn])) $use = $_POST[coupon_issue_yn];

if ($_POST[coupon_regdt][0]) $regdt1 = $_POST[coupon_regdt][0];
if ($_POST[coupon_regdt][1]) $regdt2 = $_POST[coupon_regdt][1];
if ($_POST[coupon_issuedt][0]) $usedt1 = $_POST[coupon_issuedt][0];
if ($_POST[coupon_issuedt][1]) $usedt2 = $_POST[coupon_issuedt][1];

$limit = "";
$m_etc = new M_etc();
$query = $m_etc -> getCouponRegistList($cid, $code, $name, $issue_code, $issue_yn, $regdt1, $regdt2, $usedt1, $usedt2, $limit, TRUE);

$checked[coupon_issue_yn][$_POST[coupon_issue_yn]] = "checked";

### form 전송 취약점 개선 20160128 by kdk
$_query = base64_encode(urlencode($query));
$url_query = "/a/promotion/indb.php?query=".$_query;
//debug($_query);
//debug($url_query);
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
//debug($pod_signed);
//debug($pod_expired);
### form 전송 취약점 개선 20160128 by kdk
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
         <?=_("쿠폰등록리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("쿠폰등록리스트")?> <small><?=_("등록된 쿠폰 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <div class="panel-heading-btn">
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                  <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
               </div>
               <h4 class="panel-title"><?=_("쿠폰 목록")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" method="post">
	               <div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("쿠폰코드")?></label>
	                  <div class="col-md-3">
	                     <input type="text" class="form-control" name="coupon_code" value="<?=$_POST[coupon_code]?>" placeholder='<?=_("쿠폰코드를 입력하세요.")?>' />
	                  </div>
	                  <label class="col-md-2 control-label"><?=_("쿠폰명")?></label>	                  
	                  <div class="col-md-3">
	                     <input type="text" class="form-control" name="coupon_name" value="<?=$_POST[coupon_name]?>" placeholder='<?=_("쿠폰명을 입력하세요.")?>' />
	                  </div>
	                  <div class="col-md-2">

	                  </div>
	               </div>
	               <div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("발행코드")?></label>
	                  <div class="col-md-3">
	                     <input type="text" class="form-control" name="coupon_issue_code" value="<?=$_POST[coupon_issue_code]?>" placeholder='<?=_("발행코드를 입력하세요.")?>' />
	                  </div>
	                  <label class="col-md-2 control-label"><?=_("등록여부")?></label>	                  
	                  <div class="col-md-3">
						<input type="radio" name="coupon_issue_yn" value="" checked/> <?=_("전체")?>
						<input type="radio" name="coupon_issue_yn" value="0" <?=$checked[coupon_issue_yn][0]?>/> <?=_("미등록")?>
						<input type="radio" name="coupon_issue_yn" value="1" <?=$checked[coupon_issue_yn][1]?>/> <?=_("등록")?>
	                  </div>
	                  <div class="col-md-2">
	                     
	                  </div>
	               </div>
	               <div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("발행일")?></label>
	                  <div class="col-md-3">
	                     <div class="input-group input-daterange">
	                        <input type="text" class="form-control" name="coupon_regdt[]" placeholder="Date Start" value="<?=toDate($_POST[coupon_regdt][0])?>" />
	                        <span class="input-group-addon"> ~ </span>
	                        <input type="text" class="form-control" name="coupon_regdt[]" placeholder="Date End" value="<?=toDate($_POST[coupon_regdt][1])?>" />
	                     </div>
	                  </div>
	                  <label class="col-md-2 control-label"><?=_("사용일")?></label>	                  
	                  <div class="col-md-3">
	                     <div class="input-group input-daterange">
	                        <input type="text" class="form-control" name="coupon_issuedt[]" placeholder="Date Start" value="<?=toDate($_POST[coupon_issuedt][0])?>" />
	                        <span class="input-group-addon"> ~ </span>
	                        <input type="text" class="form-control" name="coupon_issuedt[]" placeholder="Date End" value="<?=toDate($_POST[coupon_issuedt][1])?>" />
	                     </div>
	                  </div>
	                  <div class="col-md-2">	                     
	                     <button type="submit" class="btn btn-sm btn-success"><?=_("조 회")?></button>
	                     <button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>
	                  </div>
	               </div>
	               	               
                  <div class="panel-body">
                    <div class="table-responsive">
                       <table id="data-table" class="table table-striped table-bordered">
                          <thead>
                             <tr>
                                 <th><?=_("쿠폰코드")?></th>
                                 <th><?=_("쿠폰명")?></th>
                                 <th><?=_("발행코드")?></th>
                                 <th><?=_("생성일")?></th>
                                 <th><?=_("등록일")?></th>
                                 <th><?=_("등록여부")?></th>
                                 <th><?=_("등록회원")?></th>
                                 <th><?=_("삭제")?></th>
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
</div>
<!-- end #content -->

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<!-- ### 엑셀데이타추출용쿼리문 -->
<input type="hidden" name="_query" value="<?=$_query?>&pod_signed=<?=$pod_signed?>&pod_expired=<?=$pod_expired?>">

<script>
function dnExcel(){
	var query = document.getElementsByName("_query")[0].value;
	hidden_frm.location.href = "indb.php?mode=dnExcel&kind=coupon_regist&query="+query;
}

function coupon_delete(no) 
{
	if (confirm('<?=_("정말로 삭제하시겠습니까?")?>'))
	{
		location.href='indb.php?mode=del_coupon_member&no=' + no;
	}
}

function coupon_update_confirm(no, update_code)
{
	if (confirm('<?=_("변경처리 하시겠습니까?")?>'))
	{
		location.href='indb.php?mode=coupon_use_update&update_code=' + update_code + '&no=' + no;
	}
}
</script>
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
			"sPaginationType" : "bootstrap",
			"aaSorting" : [[3, "desc"]],
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
         { "bSortable": false },
         ],
		 "processing": false,
         "serverSide": true,
         "bAutoWidth": false,
			"ajax": $.fn.dataTable.pipeline({
            url: './coupon_regist_page.php?postData=<?=$postData?>',
            pages: 10 // number of pages to cache
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