<?
/*
* @date : 20181116
* @author : kdk
* @brief : POD용 (알래스카) 월매출현황.
* @request : 
* @desc :
* @todo :
*/
?>
<?
include "../_header.php";
include "../_left_menu.php";

$m_pod = new M_pod();

### 결제방식:credit_member (선결제,후결제)
### 거래상태:rest_flag (승인,정지)

if (!$_POST[sdt]) $_POST[sdt] = "orddt";
if (!$_POST[sdate]) $_POST[sdate] = date("Y-m-d");

$addwhere = "";
if ($_POST[sdate]){
   //if ($_POST[sdate]) $addwhere .= " and left($_POST[sdt],7)='". substr(date($_POST[sdate]),0,7) ."'";
}

if(isset($_POST[credit_member]) && $_POST[credit_member] != "") {
    $addwhere .= " and credit_member = '$_POST[credit_member]'";
}

if(isset($_POST[rest_flag]) && $_POST[rest_flag] != "") {
    $addwhere .= " and rest_flag = '$_POST[rest_flag]'";
}

if(isset($_POST[sword]) && $_POST[sword] != "") { //아이디,회원명,사업자명
    $addwhere .= " and concat(mid,name,cust_name) like '%$_POST[sword]%'";
}

$orderby = "order by regdt desc";
//$limit = "limit $_POST[start], $_POST[length]";
$query = $m_pod->getStatSalesList($cid, $addwhere, "", $orderby, "", true);

$postData = base64_encode(json_encode($_POST));

### form 전송 취약점 개선 20160128 by kdk
$_query = base64_encode(urlencode($query));
$url_query = "/a/order/indb.php?query=".$_query;
$pod_signed = signatureData($cid, $url_query);
$pod_expired = expiresData("20");
### form 전송 취약점 개선 20160128 by kdk

$checked[credit_member][$_POST[credit_member]] = "checked";
$checked[rest_flag][$_POST[rest_flag]] = "checked";
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
         <?=_("월매출현황")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("월매출현황")?> <small><?=_("월매출현황을 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("월매출현황")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="post" action="stat_month.php">
                  <div class="panel-body">
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("결제방식")?></label>
                         <div class="col-md-2 form-inline">
                            <input type="radio" name="credit_member" value="" <?=$checked[credit_member][""]?> /> <?=_("전체")?>
                            <input type="radio" name="credit_member" value="0" <?=$checked[credit_member][0]?>/><?=_("선결제")?>
                            <input type="radio" name="credit_member" value="1" <?=$checked[credit_member][1]?>/><?=_("후결제")?>
                         </div>
                         <label class="col-md-1 control-label"><?=_("거래상태")?></label>
                         <div class="col-md-2 form-inline">
                            <input type="radio" name="rest_flag" value="" <?=$checked[rest_flag][""]?> /> <?=_("전체")?>
                            <input type="radio" name="rest_flag" value="0" <?=$checked[rest_flag][0]?>/> <?=_("승인")?>
                            <input type="radio" name="rest_flag" value="1" <?=$checked[rest_flag][1]?>/> <?=_("정지")?>                            
                         </div>
                      </div>
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("기준일")?></label>
                         <div class="col-md-2 form-inline">
                            <input type="radio" name="sdt" value="orddt" <?if($_POST[sdt]=="orddt"){?>checked<?}?>/> <?=_("주문일자")?>
                            <input type="radio" name="sdt" value="receiptdt" <?if($_POST[sdt]=="receiptdt"){?>checked<?}?>/> <?=_("접수일자")?>                            
                         </div>
                      </div>
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("기준일자")?></label>
                         <div class="col-md-3 form-inline">
                            <div class="input-group input-daterange">
                               <input type="text" class="form-control" name="sdate" placeholder="기준일자" value="<?=$_POST[sdate]?>" />
                            </div>
                         </div>
                      </div>
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("검색")?></label>
                         <div class="col-md-6 form-inline">
                            <input type="text" name="sword" class="form-control" value="<?=$_POST[sword]?>" size="100" placeholder='<?=_("아이디,회원명,사업자명을 입력해주세요.")?>'>
                         </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-inverse"><?=_("검색")?></button>
                            <!--<button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>-->
                        </div>
                      </div>
                     <br>
                     <div class="table-responsive">
                        <table id="data-table" class="table table-striped table-bordered">
                           <thead>
                              <tr>
                                 <th><?=_("번호")?></th> 
                                 <th><?=_("아이디")?></th>
                                 <th><?=_("회원명")?></th>
                                 <th><?=_("사업자명")?></th>
                                 <th><?=_("결제방식")?></th>
                                 <th><?=_("거래상태")?></th>                                                                  
                                 <th><b class="red"><?=_("미수금액")?></b></th>
                                 <th><b class="red"><?=_("선입금액")?></b></th>
                                 <th><b class="red"><?=_("선발행사용가능입금액")?></b></th>
                                 <th><b class="red"><?=date("m",strtotime("$_POST[sdate] -5 month"))?><?=_("월")?></b></th>
                                 <th><b class="red"><?=date("m",strtotime("$_POST[sdate] -4 month"))?><?=_("월")?></b></th>
                                 <th><b class="red"><?=date("m",strtotime("$_POST[sdate] -3 month"))?><?=_("월")?></b></th>
                                 <th><b class="red"><?=date("m",strtotime("$_POST[sdate] -2 month"))?><?=_("월")?></b></th>
                                 <th><b class="red"><?=date("m",strtotime("$_POST[sdate] -1 month"))?><?=_("월")?></b></th>
                                 <th><b class="red"><?=date("m",strtotime("$_POST[sdate] -0 month"))?><?=_("월")?></b></th>
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
    hidden_frm.location.href = "../order/indb.php?mode=dnExcel&kind=stat_month_pod&query=" + query;
}
</script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
   /* Table initialisation */
   $(document).ready(function() {
      $('#data-table').dataTable({
         "sDom" : "<'row'<'col-md-6 col-sm-6'l><'col-md-6 col-sm-6'f>r>t<'row'<'col-md-6 col-sm-6'i><'col-md-6 col-sm-6'p>>",
            "sPaginationType" : "bootstrap",
            "aaSorting" : [[1, "desc"]],
            "bFilter" : false,
            "pageLength": 10,
            "oLanguage" : {
            "sLengthMenu" : "_MENU_ " + '<?=_("개씩 보기")?>'
            },
            "aoColumns": [
         { "bSortable": true },
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
            url: './stat_month_page.php?postData=<?=$postData?>',
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