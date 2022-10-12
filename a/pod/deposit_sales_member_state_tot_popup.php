<?
/*
* @date : 20181115
* @author : kdk
* @brief : POD용 (알래스카) 회원전체거래.
* @request : 
* @desc :
* @todo : 
*/

include dirname(__FILE__) . "/../_pheader.php";

if (!$_GET[mid]) {
    msg(_("회원 코드가 넘어오지 못했습니다!"), "close");
}

$m_member = new M_member();
$m_pod = new M_pod();

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

if ($_GET[mid]) {
    $addwhere = "and mid = '$_GET[mid]'";
    $list = $m_member -> getList($cid, $addwhere);
    
    if($list) $data = $list[0]; 
}
//debug($data);

$addwhere = "";

//월매출현황 페이지에서 호출.
if ($_GET[sdt] && $_GET[sdate]) {
    $end_day = date("t", $_GET[sdate]);
    
    if ($_GET[sdt] == "orddt") {
        $_POST[orddt][0] = "{$_GET[sdate]}-01";
        $_POST[orddt][1] = "{$_GET[sdate]}-{$end_day}";
    }
    else {
        $_POST[receiptdt][0] = "{$_GET[sdate]}-01";
        $_POST[receiptdt][1] = "{$_GET[sdate]}-{$end_day}";
    }
}

if(isset($_POST[manager_no]) && $_POST[manager_no] != "") {
    $addwhere .= " and manager_no like '%$_POST[manager_no]%'";
}

if(isset($_POST[remain_yn]) && $_POST[remain_yn] != "") {
    $addwhere .= " and remain_price>0";
}

if(isset($_POST[deposit_yn]) && $_POST[deposit_yn] != "") {
    $addwhere .= " and pre_deposit_price>0";
}

if(isset($_POST[autoproc_flag]) && $_POST[autoproc_flag] != "") {
    $addwhere .= " and autoproc_flag=$_POST[autoproc_flag]";
}

if (array_notnull($_POST[orddt])){
   if ($_POST[orddt][0]) $addwhere .= " and orddt>='{$_POST[orddt][0]} 00:00:00'";
   if ($_POST[orddt][1]) $addwhere .= " and orddt<='{$_POST[orddt][1]} 23:59:59'";
}

if (array_notnull($_POST[receiptdt])){
   if ($_POST[receiptdt][0]) $addwhere .= " and receiptdt>='{$_POST[receiptdt][0]}'";
   if ($_POST[receiptdt][1]) $addwhere .= " and receiptdt<='{$_POST[receiptdt][1]}'";
}

if(isset($_POST[sword]) && $_POST[sword] != "") { //주문번호, 주문명, 주문사양을 입력해주세요.
    $addwhere .= " and concat(payno,order_title,order_data) like '%$_POST[sword]%'";
}

$selected[manager_no][$_POST[manager_no]] = "selected";
$checked[remain_yn][$_POST[remain_yn]] = "checked";
$checked[deposit_yn][$_POST[deposit_yn]] = "checked";
$checked[autoproc_flag][$_POST[autoproc_flag]] = "checked";

//debug($addwhere);
$sales_list = $m_pod->getPodPayList($cid, $_GET[mid], $addwhere);
//debug($sales_list);
if ($sales_list) {
    $totalCnt = count($sales_list);
    //debug($totalCnt);
    $totpayprice = 0;
    $totvatprice = 0;
    $totorderprice = 0;
    $totdepositprice = 0;
    
    foreach ($sales_list as $key => $val) {
        $totpayprice += $val[payprice];
        $totvatprice += $val[vat_price];
        $totdepositprice += $val[deposit_price];
    }
    
    $totorderprice = $totpayprice+$totvatprice;    
}

/*$log_list = $m_pod->depositHistoryList($cid, $_GET[mid], "and status = '2'"); //사용내역.
//debug($log_list);
if ($log_list) {
    $logCnt = count($log_list);
}*/

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
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("회원 전체거래")?></a>
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
                     <label class="col-md-1 control-label"><?=_("영업담당자")?></label>
                     <div class="col-md-2 form-inline">
                        <select name="manager_no" class="form-control">
                            <option value=""><?=_("선택")?>
                            <?foreach($r_manager as $k=>$v){?>
                                <option value="<?=$v[mid]?>" <?=$selected[manager_no][$v[mid]]?>><?=$v[name]?></option>
                            <?}?>
                        </select>                            
                     </div>
                     <label class="col-md-1 control-label"><?=_("미수금액 존재")?></label>
                     <div class="col-md-2 form-inline">
                        <input type="checkbox" name="remain_yn" value="Y" <?=$checked[remain_yn]["Y"]?> /> <?=_("존재")?>
                     </div>
                     <label class="col-md-1 control-label"><?=_("선발행입금사용액 존재")?></label>
                     <div class="col-md-2 form-inline">
                        <input type="checkbox" name="deposit_yn" value="Y" <?=$checked[deposit_yn]["Y"]?> /> <?=_("존재")?>
                     </div>                         
                     <label class="col-md-1 control-label"><?=_("자동입금처리")?></label>
                     <div class="col-md-2 form-inline">
                        <input type="radio" name="autoproc_flag" value="" <?=$checked[autoproc_flag][""]?> /> <?=_("전체")?>
                        <input type="radio" name="autoproc_flag" value="0" <?=$checked[autoproc_flag][0]?>/><?=_("자동")?>
                        <input type="radio" name="autoproc_flag" value="1" <?=$checked[autoproc_flag][1]?>/><?=_("제외")?>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("주문일")?></label>
                     <div class="col-md-3 form-inline">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="orddt[]" placeholder="Date Start" value="<?=$_POST[orddt][0]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="orddt[]" placeholder="Date End" value="<?=$_POST[orddt][1]?>" />
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("접수일")?></label>
                     <div class="col-md-3 form-inline">
                        <div class="input-group input-daterange">
                           <input type="text" class="form-control" name="receiptdt[]" placeholder="Date Start" value="<?=$_POST[receiptdt][0]?>" />
                           <span class="input-group-addon"> ~ </span>
                           <input type="text" class="form-control" name="receiptdt[]" placeholder="Date End" value="<?=$_POST[receiptdt][1]?>" />
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-md-1 control-label"><?=_("검색")?></label>
                     <div class="col-md-4 form-inline">
                        <input type="text" name="sword" class="form-control" value="<?=$_POST[sword]?>" size="40" placeholder='<?=_("주문번호, 주문명, 주문사양을 입력해주세요.")?>'>
                     </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-inverse"><?=_("검색")?></button>
                        <!--<button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>-->
                    </div>
                  </div>               
                </form>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><?=_("회원 전체거래")?></h4>
            </div>
            <div class="panel-body panel-form">
                <form class="form-horizontal form-bordered">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("회원성명")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[name]?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("회원")?>ID</label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[mid]?></label>
                    </div>                    
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("사업자번호")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[cust_no]?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("사업자명")?>ID</label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[cust_name]?></label>
                    </div>                    
                </div>

                <!--<div class="form-group">
                    <label class="col-md-2 control-label"><?=_("주문건수")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><?=number_format($m_member->getTotPayCount($cid, $data[mid]))?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("결제금액")?></label>
                    <div class="col-md-4">
                        <label class="control-label"><?=number_format($m_member->getTotPayPrice($cid, $data[mid]))?></label>
                    </div>                    
                </div>-->

                <!--<div class="form-group">
                    <label class="col-md-2 control-label"><?=_("총주문금액")?></label>
                    <div class="col-md-10">
                        <label class="control-label"><?=number_format($m_member->getTotPayPrice($cid, $data[mid]))?></label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("총입금액")?></label>
                    <div class="col-md-10">
                        <label class="control-label"><?=number_format($m_pod->getTotDepositMoney($cid, $data[mid]))?></label>
                    </div>
                </div>-->
                
                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("조회범위 총공급가액")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=number_format($totpayprice)?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("조회범위 총부가세액")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=number_format($totvatprice)?></label>
                    </div>                    
                </div>
                
                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("조회범위 총주문금액")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=number_format($totorderprice)?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("조회범위 총입금액")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=number_format($totdepositprice)?></label>
                    </div>                    
                </div>
                </form>
            </div>
        </div>
    
        <ul class="nav nav-tabs">
            <li class="active"><a href="#default-tab-1" data-toggle="tab"><?=_("회원 거래 내역")?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="default-tab-1">
                <div class="panel-body">
                   <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                         <thead>
                            <tr>
                               <th><?=_("번호")?></th>
                               <th><?=_("주문일자")?></th>
                               <th><?=_("접수일자")?></th>                                                      
                               <th><?=_("주문번호")?></th>
                               <th><?=_("주문명")?></th>
                               <th><?=_("주문사양")?></th>
                               <th><span class="red"><?=_("영업담당자")?></span></th>
                               <th><span class="red"><?=_("접수담당자")?></span></th>
                               <th><span class="red"><?=_("공급가액")?></span></th>
                               <th><span class="red"><?=_("부가세액")?></span></th>
                               <th><span class="red"><?=_("주문금액")?></span></th>
                               <th><span class="red"><?=_("입금액")?></span></th>
                               <th><span class="red"><?=_("선발행입금사용금액")?></span></th>
                               <th><span class="red"><?=_("미수금액")?></span></th>
                               <th><?=_("자동입금처리")?></th>
                            </tr>
                         </thead>
    
                         <tbody>
                         <? foreach ($sales_list as $k => $value) { ?>
                            <tr>
                               <td><?=$totalCnt--?></td> 
                               <td><?=substr($value[orddt],0,10)?></td>
                               <td><?=$value[receiptdt]?></td>
                               <td><?=$value[payno]?></td>
                               <td><?=$value[order_title]?></td>
                               <td><?=$value[order_data]?></td>
                               <td><?=$r_manager[$value[manager_no]][name]?>(<?=$r_manager[$value[manager_no]][mid]?>)</td>
                               <td><?=$r_manager[$value[receiptadmin]][name]?>(<?=$r_manager[$value[receiptadmin]][mid]?>)</td>
                               <td><?=number_format($value[payprice])?></td>
                               <td><?=number_format($value[vat_price])?></td>
                               <td><?=number_format($value[payprice]+$value[vat_price])?></td>
                               <td><?=number_format($value[deposit_price])?></td>
                               <td><?=number_format($value[pre_deposit_price])?></td>
                               <td><?=number_format($value[remain_price])?></td>
                               <td><?=($value[autoproc_flag] == "0")?"자동":"제외"?></td>
                            </tr>
                         <? } ?>
                         </tbody>
                      </table>
                   </div>
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