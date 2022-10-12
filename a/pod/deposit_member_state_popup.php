<?
/*
* @date : 20181114
* @author : kdk
* @brief : POD용 (알래스카) 회원입금현황.
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
    $list = $m_member -> getList($cid, "and mid = '$_GET[mid]'");

    if($list) $data = $list[0]; 
}
//debug($data);

$addwhere = "";

//입금일자.
if (array_notnull($_POST[sdate])){
   if ($_POST[sdate][0]) $addwhere .= " and deposit_date>='{$_POST[sdate][0]}'";
   if ($_POST[sdate][1]) $addwhere .= " and deposit_date<='{$_POST[sdate][1]}'";
}

//등록자.
if(isset($_POST[regid]) && $_POST[regid] != "") {
    $addwhere .= " and admin=$_POST[regid]";
}

$deposit_list = $m_pod->getDepositMoneyList($cid, $_GET[mid], $addwhere);
//debug($deposit_list);
if ($deposit_list) {
    $totalCnt = count($deposit_list);
    //debug($totalCnt);
}

$log_list = $m_pod->depositHistoryList($cid, $_GET[mid], "and status = '2'"); //사용내역.
//debug($log_list);
if ($log_list) {
    $logCnt = count($log_list);
}

$selected[regid][$_POST[regid]] = "selected";
?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
    <!-- begin #content -->
    <div id="content" class="content">
        
        <!-- begin #header -->
        <div id="header" class="header navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("회원 입금 현황")?></a>
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
                     <label class="col-md-1 control-label"><?=_("등록자")?></label>
                     <div class="col-md-2 form-inline">
                        <select name="regid" class="form-control">
                            <option value=""><?=_("선택")?>
                            <?foreach($r_manager as $k=>$v){?>
                                <option value="<?=$v[mid]?>" <?=$selected[regid][$v[mid]]?>><?=$v[name]?></option>
                            <?}?>
                        </select>                            
                     </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-1 control-label"><?=_("검색")?></label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-sm btn-inverse"><?=_("검색")?></button>
                        <!--<button type="button" class="btn btn-sm btn-default" onclick="dnExcel()"><?=_("엑셀저장")?></button>-->
                    </div>
                  </div>
                </form>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><?=_("회원 정보")?></h4>
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

                <div class="form-group">
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
                </div>
                
                </form>
            </div>
        </div>
    
        <ul class="nav nav-tabs">
            <li class="active"><a href="#default-tab-1" data-toggle="tab"><?=_("회원 입금 내역")?></a></li>
            <li class=""><a href="#default-tab-2" data-toggle="tab"><?=_("선입금 사용 내역")?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="default-tab-1">
                <div class="panel-body">
                   <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                         <thead>
                            <tr>
                               <th><?=_("번호")?></th>
                               <th><?=_("입금일자(수정)")?></th>
                               <th><?=_("입금방법")?></th>                                                      
                               <th><?=_("(선)입금액")?></th>
                               <th><?=_("선발행입금액(부가세포함)")?></th>
                               <th><?=_("현금영수증발행일자")?></th>
                               <th><?=_("계산서선발행일자")?></th>
                               <th><?=_("등록자")?></th>
                               <th><?=_("등록일자")?></th>
                               <th><?=_("비고")?></th>
                               <th><?=_("삭제")?></th>
                            </tr>
                         </thead>
    
                         <tbody>
                         <? foreach ($deposit_list as $k => $value) { ?>
                            <tr>
                               <td><?=$totalCnt--?></td> 
                               <td><a href="javascript:popup('deposit_date_form_popup.php?no=<?=$value[no]?>&mid=<?=$value[mid]?>',700,650);"><?=$value[deposit_date]?></a></td>
                               <td><?=$r_deposit_method[$value[deposit_method]]?></td>
                               <td><?=number_format($value[deposit_money])?></td>
                               <td><?=number_format($value[pre_deposit_money])?></td>
                               <td><?=$value[cashreceipt_date]?></td>
                               <td><?=$value[taxbill_date]?></td>
                               <td><?=$r_manager[$value[admin]][name]?></td>
                               <td><?=$value[regdt]?></td>
                               <td><?=$value[memo]?></td>
                               <td><a href="indb.php?mode=deposit_delete&no=<?=$value[no]?>" onclick="return confirm('삭제된 입금정보는 복구되지 않습니다.\n정말로 삭제하시겠습니까?')"><span class="btn btn-xs btn-danger"><?=_("삭제")?></span></a></td>
                            </tr>
                         <? } ?>
                         </tbody>
                      </table>
                   </div>
                </div>
            </div>
            <div class="tab-pane fade" id="default-tab-2">
                <div class="panel-body">
                   <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                         <thead>
                            <tr>
                               <th><?=_("번호")?></th>
                               <th><?=_("결제번호")?></th>
                               <th><?=_("내역")?></th>
                               <th><?=_("사용일")?></th>                                                      
                               <th><?=_("(선)입금액")?></th>
                               <th><?=_("선발행입금액(부가세포함)")?></th>
                            </tr>
                         </thead>
    
                         <tbody>
                         <? foreach ($log_list as $k => $value) { ?>
                            <tr>
                               <td><?=$logCnt--?></td>                            
                               <td><?=$value[payno]?></td>
                               <td><?=$value[memo]?></td>
                               <td><?=$value[regdt]?></td>                             
                               <td><?=number_format($value[deposit_money])?></td>
                               <td><?=number_format($value[pre_deposit_money])?></td>
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