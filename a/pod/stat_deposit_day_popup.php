<?
/*
* @date : 20181130
* @author : kdk
* @brief : POD용 (알래스카) 일일입금현황 입금내역.
* @request : 
* @desc :
* @todo : 
*/

include dirname(__FILE__) . "/../_pheader.php";

if (!$_GET[sdate]) {
    msg(_("필수 정보가 넘어오지 못했습니다!"), "close");
}

$m_member = new M_member();
$m_pod = new M_pod();

### 영업사원정보 추출
$r_manager = $m_pod->getSalesList($cid);

$addwhere = "";

//입금종류    
if(isset($_GET[method]) && $_GET[method] != "") {
    $addwhere .= " and deposit_method = '$_GET[method]'";
}
//입금일자 deposit_date
if ($_GET[sdate]) $addwhere .= " and deposit_date>='{$_GET[sdate]}'";
if ($_GET[sdate]) $addwhere .= " and deposit_date<='{$_GET[sdate]}'";
//debug($addwhere);

$list = $m_pod -> getStatDepositDayPopup($cid, $addwhere);
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
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=$r_deposit_method[$_GET[method]]?><?=_(" 입금내역")?></a>
                </div>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><?=$_GET[sdate]?> <?=$r_deposit_method[$_GET[method]]?><?=_(" 입금내역")?></h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                         <thead>
                            <tr>
                               <th><?=_("번호")?></th>
                               <th><?=_("입금방법")?></th>
                               <th><?=_("아이디")?></th>                                                      
                               <th><?=_("회원명")?></th>
                               <th><?=_("사업자명")?></th>
                               <th><?=_("입금액")?></th>
                               <th><?=_("현금영수증발행일자")?></th>
                               <th><?=_("계산서발행일자")?></th>
                               <th><?=_("등록자")?></th>
                            </tr>
                         </thead>
    
                         <tbody>
                         <?
                         $tot_deposit_money = 0;
                         foreach ($list as $key => $value) {
                            //회원정보.
                            $memberlist = $m_member -> getList($cid, "and mid = '$value[mid]'");
                            if($memberlist) $data = $memberlist[0];
                            //debug($data); 
                         ?>
                            <tr>
                               <td><?=$totalCnt--?></td>
                               <td><?=$r_deposit_method[$value[deposit_method]]?></td>
                               <td><?=$value[mid]?></td>
                               <td><?=$data[name]?></td>
                               <td><?=$data[cust_name]?></td>                               
                               <td><?=number_format($value[tot_deposit_money])?></td>
                               <td><span class="red"><?=$value[cashreceipt_date]?></span></td>
                               <td><span class="red"><?=$value[taxbill_date]?></span></td>
                               <td><?=$r_manager[$value[admin]][name]?></td>
                            </tr>
                         <?
                            $tot_deposit_money += $value[tot_deposit_money];
                         } 
                         ?>
                            <tr style="background-color: #E8ECF1;">
                               <td><?=_("합계")?></td>
                               <td></td>
                               <td></td>
                               <td></td>
                               <td></td>
                               <td><?=number_format($tot_deposit_money)?></td>
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