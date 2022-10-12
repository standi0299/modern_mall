<?
/*
* @date : 20181121
* @author : kdk
* @brief : POD용 (알래스카) 매출현황 입금액 상세보기.
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
       //if ($postData[receiptdt][0]) $addwhere .= " and deposit_date>='{$postData[receiptdt][0]}'";
       //if ($postData[receiptdt][1]) $addwhere .= " and deposit_date<='{$postData[receiptdt][1]}'";
    }
}
//debug($addwhere);

//입금일자.
if (array_notnull($_POST[sdate])){
   if ($_POST[sdate][0]) $addwhere .= " and deposit_date>='{$_POST[sdate][0]}'";
   if ($_POST[sdate][1]) $addwhere .= " and deposit_date<='{$_POST[sdate][1]}'";
}


$list = $m_pod -> getDepositMoneyList($cid, $_GET[mid], $addwhere);
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
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("매출현황 입금액")?></a>
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
                     <label class="col-md-1 control-label"><?=_("입금일자")?></label>
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
                <h4 class="panel-title"><?=_("매출현황 입금액 상세보기")?></h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                         <thead>
                            <tr>
                               <th><?=_("번호")?></th>
                               <th><b class="red"><?=_("입금일자(수정)")?></b></th>                                                      
                               <th><b class="red"><?=_("입금방법")?></b></th>
                               <th><b class="red"><?=_("(선)입금액")?></b></th>
                               <th><?=_("선발행입금액(부가세포함)")?></th>
                               <th><?=_("현금영수증발행일자")?></th>
                               <th><?=_("계산서발행일자")?></th>                               
                               <th><?=_("등록자")?></th>
                               <th><b class="red"><?=_("등록일시")?></b></th>
                               <th><b class="red"><?=_("비고")?></b></th>
                            </tr>
                         </thead>
    
                         <tbody>
                         <?
                         $deposit_money = 0;
                         $pre_deposit_money = 0;
                         foreach ($list as $key => $value) {
                         ?>
                            <tr>
                               <td><?=$totalCnt--?></td>
                               <td><?=$value[deposit_date]?></td>
                               <td><?=$r_deposit_method[$value[deposit_method]]?></td>
                               <td><?=number_format($value[deposit_money])?></td>
                               <td><?=number_format($value[pre_deposit_money])?></td>
                               <td><?=$value[cashreceipt_date]?></td>
                               <td><?=$value[taxbill_date]?></td>
                               <td><?=$r_manager[$value[admin]][name]?></td>
                               <td><?=$value[regdt]?></td>
                               <td><?=$value[memo]?></td>
                            </tr>
                         <?
                            $deposit_money += $value[deposit_money];
                            $pre_deposit_money += $value[pre_deposit_money];
                         } 
                         ?>
                            <tr style="background-color: #E8ECF1;">
                               <td><?=_("합계")?></td>
                               <td></td>
                               <td></td>
                               <td><?=number_format($deposit_money)?></td>
                               <td><?=number_format($pre_deposit_money)?></td>
                               <td></td>
                               <td></td>
                               <td></td>
                               <td></td>
                               <td></td>
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