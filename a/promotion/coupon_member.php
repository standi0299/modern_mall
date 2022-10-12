<?
include "../_header.php";
include "../_left_menu.php";

if ($_GET[coupon_code]) $_POST[coupon_code] = $_GET[coupon_code];

## 회원별발행리스트
$r_kind = array("on" => _("온라인"), "off" => _("오프라인"));

$postData = base64_encode(json_encode($_POST));

$_POST[coupon_setdt][0] = str_replace("-","",$_POST[coupon_setdt][0]);
$_POST[coupon_setdt][1] = str_replace("-","",$_POST[coupon_setdt][1]);
$_POST[coupon_usedt][0] = str_replace("-","",$_POST[coupon_usedt][0]);
$_POST[coupon_usedt][1] = str_replace("-","",$_POST[coupon_usedt][1]);

if ($_POST[coupon_code]) $code = $_POST[coupon_code];
if ($_POST[coupon_name]) $name = $_POST[coupon_name];
if (is_numeric($_POST[coupon_use])) $use = $_POST[coupon_use];
if ($_POST[mid]) $mid = $_POST[mid];

if ($_POST[coupon_setdt][0]) $setdt1 = $_POST[coupon_setdt][0];
if ($_POST[coupon_setdt][1]) $setdt2 = $_POST[coupon_setdt][1];
if ($_POST[coupon_usedt][0]) $usedt1 = $_POST[coupon_usedt][0];
if ($_POST[coupon_usedt][1]) $usedt2 = $_POST[coupon_usedt][1];

$limit = "";
$m_etc = new M_etc();
$query = $m_etc -> getCouponMemberList($cid, $_GET[kind], $code, $name, $use, $mid, $setdt1, $setdt2, $usedt1, $usedt2, $limit, TRUE);

$checked[coupon_use][$_POST[coupon_use]] = "checked";

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
         <?=$r_kind[$_GET[kind]]?> <?=_("회원별발행리스트")?>
      </li>
   </ol>

   <h1 class="page-header"><?=$r_kind[$_GET[kind]]?> <?=_("회원별발행리스트")?> <small><?=_("회원별 발행 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
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
	                     <input type="text" class="form-control" name="coupon_name" value="<?=$_POST[coupon_name]?>" placeholder='<?=_("쿠폰명명을 입력하세요.")?>' />
	                  </div>
	                  <div class="col-md-2">

	                  </div>
	               </div>
	               <div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("회원아이디")?></label>
	                  <div class="col-md-3">
	                     <input type="text" class="form-control" name="mid" value="<?=$_POST[mid]?>" placeholder='<?=_("회원아이디를 입력하세요.")?>' />
	                  </div>
	                  <label class="col-md-2 control-label"><?=_("사용여부")?></label>	                  
	                  <div class="col-md-3">
	                    <input type="radio" name="coupon_use" value="" checked/> <?=_("전체")?>
						<input type="radio" name="coupon_use" value="0" <?=$checked[coupon_use][0]?>/> <?=_("미사용")?>
						<input type="radio" name="coupon_use" value="1" <?=$checked[coupon_use][1]?>/> <?=_("사용")?>
	                  </div>
	                  <div class="col-md-2">
	                     
	                  </div>
	               </div>
	               <div class="form-group">
	                  <label class="col-md-2 control-label"><?=_("발행일")?></label>
	                  <div class="col-md-3">
	                     <div class="input-group input-daterange">
	                        <input type="text" class="form-control" name="coupon_setdt[]" placeholder="Date Start" value="<?=toDate($_POST[coupon_setdt][0])?>" />
	                        <span class="input-group-addon"> ~ </span>
	                        <input type="text" class="form-control" name="coupon_setdt[]" placeholder="Date End" value="<?=toDate($_POST[coupon_setdt][1])?>" />
	                     </div>
	                  </div>
	                  <label class="col-md-2 control-label"><?=_("사용일")?></label>	                  
	                  <div class="col-md-3">
	                     <div class="input-group input-daterange">
	                        <input type="text" class="form-control" name="coupon_usedt[]" placeholder="Date Start" value="<?=toDate($_POST[coupon_usedt][0])?>" />
	                        <span class="input-group-addon"> ~ </span>
	                        <input type="text" class="form-control" name="coupon_usedt[]" placeholder="Date End" value="<?=toDate($_POST[coupon_usedt][1])?>" />
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
                                 <th><?=_("쿠폰명/발행코드")?></th>
                                 <th><?=_("발행일")?></th>
                                 <th><?=_("발행회원")?></th>
                                 <th><?=_("사용일")?></th>
                                 <th><?=_("사용여부")?></th>
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
	hidden_frm.location.href = "indb.php?mode=dnExcel&kind=coupon_member&query="+query;
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
			"aaSorting" : [[2, "desc"]],
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
            url: './coupon_member_page.php?kind=<?=$_GET[kind]?>&postData=<?=$postData?>',
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