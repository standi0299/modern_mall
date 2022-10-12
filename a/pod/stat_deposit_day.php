<?
/*
* @date : 20181116
* @author : kdk
* @brief : POD용 (알래스카) 일일입금현황.
* @request : 
* @desc :
* @todo :
*/
?>
<?
include "../_header.php";
include "../_left_menu.php";

$m_pod = new M_pod();

if (!$_POST[deposit_date][0]) $_POST[deposit_date][0] = date("Y-m-d", strtotime($day." -30 day")); //date("Y-m-d");
//if (!$_POST[deposit_date][1]) $_POST[deposit_date][1] = date("Y-m-d");

$addwhere = "";
if (array_notnull($_POST[deposit_date])){
   if ($_POST[deposit_date][0]) $addwhere .= " and deposit_date>='{$_POST[orddt][0]}'";
   if ($_POST[deposit_date][1]) $addwhere .= " and deposit_date<='{$_POST[orddt][1]}'";
}

$query = $m_pod->getStatDepositDay($cid, $addwhere, true);

$postData = base64_encode(json_encode($_POST));

### form 전송 취약점 개선 20160128 by kdk
$_query = base64_encode(urlencode($query));
$url_query = "/a/order/indb.php?query=".$_query;
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
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
         <?=_("일일입금현황")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("일일입금현황")?> <small><?=_("일일입금현황 정보를 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("일일입금현황")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="post" action="stat_deposit_day.php">
                  <div class="panel-body">
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("입금일자")?></label>
                         <div class="col-md-3 form-inline">
                            <div class="input-group input-daterange">
                               <input type="text" class="form-control" name="deposit_date[]" placeholder="Date Start" value="<?=$_POST[deposit_date][0]?>" />
                               <span class="input-group-addon"> ~ </span>
                               <input type="text" class="form-control" name="deposit_date[]" placeholder="Date End" value="<?=$_POST[deposit_date][1]?>" />
                            </div>
                         </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-1 control-label"><?=_("")?></label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-inverse"><?=_("검색")?></button>
                            <button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>
                        </div>
                      </div>
                     <br>
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("일자")?></th>
                                 <th><?=$r_deposit_method[1]?></th>
                                 <th><?=$r_deposit_method[2]?></th> 
                                 <th><?=$r_deposit_method[3]?></th>
                                 <th><?=$r_deposit_method[4]?></th>                                 
                                 <th><?=$r_deposit_method[5]?></th>
                                 <th><?=$r_deposit_method[6]?></th>                                 
                                 <th><?=$r_deposit_method[7]?></th>
                                 <th><?=$r_deposit_method[8]?></th>
                                 <th><?=$r_deposit_method[9]?></th>
                                 <th><?=$r_deposit_method[10]?></th>
                                 <th><?=$r_deposit_method[11]?></th>
                                 <th><?=$r_deposit_method[12]?></th>
                                 <th><?=_("합계")?></th>
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
function dnExcel() {
    var query = document.getElementsByName("_query")[0].value;
    hidden_frm.location.href = "../order/indb.php?mode=dnExcel&kind=stat_deposit_day_pod&query=" + query;
}
</script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
            "sPaginationType" : "bootstrap",
            "aaSorting" : [[0, "desc"]],
            "bFilter" : false,
            "pageLength": 10,
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
            url: './stat_deposit_day_page.php?postData=<?=$postData?>',
            pages: 1 // number of pages to cache
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