<?
/*
* @date : 20181116
* @author : kdk
* @brief : POD용 (알래스카) 일매출현황.
* @request : 
* @desc :
* @todo :
*/
?>
<?
include "../_header.php";
include "../_left_menu.php";

$m_pod = new M_pod();

if (!$_POST[sdt]) $_POST[sdt] = "orddt";

$addwhere = "";
if (array_notnull($_POST[sdate])){
   if ($_POST[sdate][0]) $addwhere .= " and $_POST[sdt]>='{$_POST[sdate][0]} 00:00:00'";
   if ($_POST[sdate][1]) $addwhere .= " and $_POST[sdt]<='{$_POST[sdate][1]} 23:59:59'";
}

$data = $m_pod->getStatDay($cid, $_POST[sdt], $addwhere);

$query = $m_pod->getStatDay($cid, $_POST[sdt], $addwhere, true);

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
         <?=_("일매출현황")?>
      </li>
   </ol>

   <h1 class="page-header"><?=_("일매출현황")?> <small><?=_("일매출현황을 보실 수 있습니다.")?></small></h1>

   <div class="row">
      <div class="col-md-12">
         <div class="panel panel-inverse">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("일매출현황")?></h4>
            </div>

            <div class="panel-body panel-form">
               <form class="form-horizontal form-bordered" name="fm" method="post" action="stat_day.php">
                  <div class="panel-body">
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("검색일")?></label>
                         <div class="col-md-2 form-inline">
                            <input type="radio" name="sdt" value="orddt" <?if($_POST[sdt]=="orddt"){?>checked<?}?>/> <?=_("주문일자")?>
                            <input type="radio" name="sdt" value="receiptdt" <?if($_POST[sdt]=="receiptdt"){?>checked<?}?>/> <?=_("접수일자")?>                            
                         </div>
                      </div>
                      <div class="form-group">
                         <label class="col-md-1 control-label"><?=_("검색일자")?></label>
                         <div class="col-md-3 form-inline">
                            <div class="input-group input-daterange">
                               <input type="text" class="form-control" name="sdate[]" placeholder="Date Start" value="<?=$_POST[sdate][0]?>" />
                               <span class="input-group-addon"> ~ </span>
                               <input type="text" class="form-control" name="sdate[]" placeholder="Date End" value="<?=$_POST[sdate][1]?>" />
                            </div>
                         </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-1 control-label"><?=_("검색")?></label>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-inverse"><?=_("검색")?></button>
                            <!--<button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>-->
                        </div>
                      </div>
                     <br>
                     <div class="table-responsive">
                        <table class="table table-bordered table-hover">    
                           <thead>
                              <tr>
                                 <th><?=_("주문일자(접수일자)")?></th> 
                                 <th><?=_("공급가액")?></th>
                                 <th><?=_("부가세")?></th>                                                                  
                                 <th><b class="red"><?=_("총주문금액")?></b></th>
                                 <th><b class="red"><?=_("미수금액")?></b></th>
                                 <th><b class="red"><?=_("선발행사용금액")?></b></th>
                                 <th><?=_("총주문건수")?></th>
                                 <th><?=_("제작미접수건수")?></th>
                              </tr>
                           </thead>
                            <tbody>
                            <? foreach ($data as $k => $value) { ?>
                            <tr>
                               <td><?=$value[dt]?></td>                               
                               <td><?=number_format($value[payprice])?></td>
                               <td><?=number_format($value[vat_price])?></td>
                               <td><?=number_format($value[totpayprice])?></td>
                               <td><?=number_format($value[remain_price])?></td>
                               <td><?=number_format($value[pre_deposit_price])?></td>                               
                               <td><?=$value[totea]?></td>
                               <td><?=$value[ea]?></td>
                            </tr>
                            <? } ?>
                            </tbody>                           
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
    hidden_frm.location.href = "../order/indb.php?mode=dnExcel&kind=stat_sales_pod&query=" + query;
}
</script>

<!-- ================== END PAGE LEVEL JS ================== -->
<script>
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

<? include "../_footer_app_exec.php"; ?>