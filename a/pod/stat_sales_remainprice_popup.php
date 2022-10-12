<?
/*
* @date : 20181122
* @author : kdk
* @brief : POD용 (알래스카) 매출현황 미수금액 상세보기.
* @request : 
* @desc :
* @todo : 
*/

include dirname(__FILE__) . "/../_pheader.php";

if (!$_GET[mid]) {
    msg(_("회원 정보가 넘어오지 못했습니다!"), "close");
}
if (!$_GET[postData]) {
    //msg(_("필수 정보가 넘어오지 못했습니다!"), "close");
}

$m_member = new M_member();
$m_pod = new M_pod();

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

$addwhere = "";

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {
    if (array_notnull($postData[receiptdt])){
       //접수일시
       if ($postData[receiptdt][0]) $addwhere .= " and receiptdt>='{$postData[receiptdt][0]}'";
       if ($postData[receiptdt][1]) $addwhere .= " and receiptdt<='{$postData[receiptdt][1]}'";
    }
}
//debug($addwhere);

//접수일시.
if (array_notnull($_POST[sdate])){
   if ($_POST[sdate][0]) $addwhere .= " and receiptdt>='{$_POST[sdate][0]}'";
   if ($_POST[sdate][1]) $addwhere .= " and receiptdt<='{$_POST[sdate][1]}'";
}

$list = $m_pod -> getPodPayList($cid, $_GET[mid], $addwhere);
$totalCnt = count($list);
//debug($list);
//debug($totalCnt);
//debug($r_manager);
?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="https://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="page-container" class="page-without-sidebar page-header-fixed">
    <!-- begin #content -->
    <div id="content" class="content">
        
        <!-- begin #header -->
        <div id="header" class="header navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("매출현황 미수금액")?></a>
                </div>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><?=_("검색조건")?></h4>
            </div>
            <div class="panel-body panel-form">
                <form class="form-horizontal form-bordered" name="fm" method="post" action="?mid=<?=$_GET[mid]?>">
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("접수일시")?></label>
                     <div class="col-md-3 form-inline">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="sdate[]" placeholder="Date Start" value="<?=$_POST[sdate][0]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="sdate[]" placeholder="Date End" value="<?=$_POST[sdate][1]?>" />
                        </div>
                     </div>
                     <label class="col-md-1 control-label"></label>
                     <div class="col-md-2 form-inline">
                        <button type="submit" class="btn btn-sm btn-inverse"><?=_("검색")?></button>
                     </div>
                  </div>
                </form>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><?=_("매출현황 미수금액 상세보기")?></h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                         <thead>
                            <tr>
                               <th><?=_("번호")?></th>
                               <th><?=_("주문번호")?></th>                                                      
                               <th><?=_("주문명")?></th>
                               <th><?=_("상품명")?></th>
                               <th style="width: 180px;"><?=_("주문사양")?></th>
                               <th><?=_("영업담당자")?></th>
                               <th><?=_("접수자")?></th>                               
                               <th><?=_("공급가액")?></th>
                               <th><?=_("부가세")?></th>
                               <th><?=_("총주문금액")?></th>
                               <th><?=_("미수금액")?></th>
                            </tr>
                         </thead>
    
                         <tbody>
                         <?
                         $payprice = 0;
                         $vat_price = 0;
                         $totpayprice = 0;
                         $remainprice = 0;
                         foreach ($list as $key => $value) {
                         ?>
                            <tr>
                               <td><?=$totalCnt--?></td>
                               <td><?=$value[payno]?></td>
                               <td><?=$value[order_title]?></td>
                               <td><?=$value[goodsnm]?></td>
                               <td><?=$value[order_data]?></td>
                               <td><?=$r_manager[$value[manager_no]][name]?></td>
                               <td><?=$r_manager[$value[receiptadmin]][name]?></td>
                               <td><?=number_format($value[payprice])?></td>
                               <td><?=number_format($value[vat_price])?></td>
                               <td><?=number_format($value[payprice]+$value[vat_price]+$value[ship_price])?></td>
                               <td><?=number_format($value[remain_price])?></td>
                            </tr>
                         <?
                            $payprice += $value[payprice];
                            $vat_price += $value[vat_price];
                            $totpayprice += $value[payprice]+$value[vat_price]+$value[ship_price];
                            $remainprice += $value[remain_price];
                         } 
                         ?>
                            <tr style="background-color: #E8ECF1;">
                               <td><?=_("합계")?></td>
                               <td></td>
                               <td></td>
                               <td></td>
                               <td></td>
                               <td></td>
                               <td></td>
                               <td><?=number_format($payprice)?></td>
                               <td><?=number_format($vat_price)?></td>
                               <td><?=number_format($totpayprice)?></td>
                               <td><?=number_format($remainprice)?></td>
                            </tr>                         
                         </tbody>
                      </table>
                   </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label"></label>
            <div class="col-md-9">
                <button type="button" style="margin-bottom: 15px;" class="btn btn-sm btn-default"onclick="window.close();"><?=_("닫  기")?></button>
            </div>
        </div>
    
    </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>
<script src="../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

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

<?
include dirname(__FILE__) . "/../_pfooter.php";
?>