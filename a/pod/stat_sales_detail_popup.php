<?
/*
* @date : 20181121
* @author : kdk
* @brief : POD용 (알래스카) 매출현황 상세보기.
* @request : 
* @desc :
* @todo : 
*/

include dirname(__FILE__) . "/../_pheader.php";

if (!$_GET[postData]) {
    msg(_("필수 정보가 넘어오지 못했습니다!"), "close");
}

$m_member = new M_member();
$m_pod = new M_pod();

$addwhere = "";
$addwhere2 = "";
$addwhere3 = "";

$postData = json_decode(base64_decode($_GET[postData]), 1);
if ($postData) {
    if (array_notnull($postData[receiptdt])){
       //접수일시
       if ($postData[receiptdt][0]) $addwhere2 .= " and receiptdt>='{$postData[receiptdt][0]}'";
       if ($postData[receiptdt][1]) $addwhere2 .= " and receiptdt<='{$postData[receiptdt][1]}'";
       
       //입금일자 deposit_date
       if ($postData[receiptdt][0]) $addwhere3 .= " and deposit_date>='{$postData[receiptdt][0]}'";
       if ($postData[receiptdt][1]) $addwhere3 .= " and deposit_date<='{$postData[receiptdt][1]}'";
    }
    
    if(isset($postData[sword]) && $postData[sword] != "") { //아이디,회원명,사업자명
        $addwhere .= " and concat(mid,name,cust_name) like '%$postData[sword]%'";
    }
   
    if(isset($postData[credit_member]) && $postData[credit_member] != "") {
        $addwhere .= " and credit_member = '$postData[credit_member]'";
    }
    
    if(isset($postData[rest_flag]) && $postData[rest_flag] != "") {
        $addwhere .= " and rest_flag = '$postData[rest_flag]'";
    }
}
//debug($addwhere);
//debug($addwhere2);
//debug($addwhere3);

$list = $m_pod -> getStatSalesDetailList($cid, $addwhere, $addwhere2, $addwhere3);
$totalCnt = count($list);
//debug($list);
//debug($totalCnt);
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
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("매출현황 상세보기")?></a>
                </div>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><?=_("매출현황 상세보기")?></h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                         <thead>
                            <tr>
                               <th><?=_("번호")?></th>
                               <th><?=_("아이디")?></th>                                                      
                               <th><?=_("회원명")?></th>
                               <th><?=_("사업자명")?></th>
                               <th><?=_("전화번호")?></th>
                               <th><?=_("총주문금액")?></th>
                               <th><?=_("현금성입금")?></th>                               
                               <th><?=_("현금영수증발행금액")?></th>
                               <th><?=_("카드")?></th>
                               <th><?=_("선발행입금사용금액")?></th>
                               <th><?=_("기타")?></th>
                               <th><?=_("입금총금액")?></th>
                               <th><span class="red"><?=_("총미수금액")?></span></th>
                            </tr>
                         </thead>
    
                         <tbody>
                         <?
                         foreach ($list as $key => $value) {
                         ?>
                            <tr>
                               <td><?=$totalCnt--?></td>
                               <td><?=$value[mid]?></td>
                               <td><?=$value[name]?></td>
                               <td><?=$value[cust_name]?></td>
                               <td><?=$value[phone]?></td>
                               <td><?=number_format($value[payprice])?></td>
                               <td><?=number_format($value[cash_money])?></td>
                               <td><?=number_format($value[cashreceipt_money])?></td>
                               <td><?=number_format($value[card_money])?></td>
                               <td><?=number_format($value[pre_deposit_price])?></td>
                               <td><?=number_format($value[etc_money])?></td>
                               <td><?=number_format($value[deposit_price])?></td>
                               <td><?=number_format($value[remain_price])?></td>
                            </tr>
                         <? 
                         } 
                         ?>
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