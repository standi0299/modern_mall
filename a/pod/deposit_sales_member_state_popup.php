<?
/*
* @date : 20181119
* @author : kdk
* @brief : POD용 (알래스카) 회원매출입금현황.
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

if (!$_POST[sdt]) $_POST[sdt] = "orddt";

$addwhere = "";
if ($_GET[mid]) {
    $addwhere = "and mid = '$_GET[mid]'";
    $list = $m_member -> getList($cid, $addwhere);
    
    if($list) $data = $list[0]; 
}

if (array_notnull($_POST[sdate])){
   if ($_POST[sdate][0]) $addwhere .= " and $_POST[sdt]>='{$_POST[sdate][0]} 00:00:00'";
   if ($_POST[sdate][1]) $addwhere .= " and $_POST[sdt]<='{$_POST[sdate][1]} 23:59:59'";
}

//debug($data);
if($data) {
    //대표세금계산서담당자,전자세금계산서발행이메일
    $etc_name = "";
    $etc_email = "";
    if ($data[etc1]) {
        $data[etc1] = explode(",",$data[etc1]);
        if($data[etc1][3]) {
            $etc_name = $data[etc1][0];
            $etc_email = $data[etc1][1];
        }
    }        
    if ($data[etc2]) {
        $data[etc2] = explode(",",$data[etc2]);
        if($data[etc2][3]) {
            $etc_name = $data[etc2][0];
            $etc_email = $data[etc2][1];
        }
    }
    if ($data[etc3]) {
        $data[etc3] = explode(",",$data[etc3]);
        if($data[etc3][3]) {
            $etc_name = $data[etc3][0];
            $etc_email = $data[etc3][1];
        }
    }
    if ($data[etc4]) {
        $data[etc4] = explode(",",$data[etc4]);
        if($data[etc4][3]) {
            $etc_name = $data[etc4][0];
            $etc_email = $data[etc4][1];
        }
    }
    if ($data[etc5]) {
        $data[etc5] = explode(",",$data[etc5]);
        if($data[etc5][3]) {
            $etc_name = $data[etc5][0];
            $etc_email = $data[etc5][1];
        }
    }
}
//debug($addwhere);

//$sales_list = $m_pod->getPodPayList($cid, $_GET[mid], $addwhere);
$sales_list = $m_pod->getStatDepositSalesMember($cid, $_GET[mid], $_POST[sdt], $addwhere);
//debug($sales_list);
if ($sales_list) {
    //$totalCnt = count($sales_list);
    //debug($totalCnt);
        
    $list = array();
    foreach ($sales_list as $key => $val) {
        $list[substr($val[dt],0,7)][] = $val;
        
        //총합계.
        $sum[totpayprice] += $val[totpayprice];
        $sum[payprice] += $val[payprice];
        $sum[vat_price] += $val[vat_price];
        $sum[remain_price] += $val[remain_price];
        $sum[deposit_price] += $val[deposit_price];
        $sum[pre_deposit_price] += $val[pre_deposit_price];
        $sum[cash_money] += $val[cash_money];
        $sum[cashreceipt_money] += $val[cashreceipt_money];
        $sum[card_money] += $val[card_money];
    }
    //debug($list);
    $sum[dt] = "총합계";
    //debug($sum);

    foreach ($list as $key => $val) {
        //debug($key);
        $tot[totpayprice] = 0;
        $tot[payprice] = 0;
        $tot[vat_price] = 0;
        $tot[remain_price] = 0;
        $tot[deposit_price] = 0;
        $tot[pre_deposit_price] = 0;
        $tot[cash_money] = 0;
        $tot[cashreceipt_money] = 0;
        $tot[card_money] = 0;
        foreach ($val as $k => $v) {
    
            if (substr($v[dt],0,7) == $key) {
                //debug($v[dt]);
                $tot[totpayprice] += $v[totpayprice];
                $tot[payprice] += $v[payprice];
                $tot[vat_price] += $v[vat_price];
                $tot[remain_price] += $v[remain_price];
                $tot[deposit_price] += $v[deposit_price];
                $tot[pre_deposit_price] += $v[pre_deposit_price];
                $tot[cash_money] += $v[cash_money];
                $tot[cashreceipt_money] += $v[cashreceipt_money];
                $tot[card_money] += $v[card_money];
            }
            //debug($tot);
        }
        $list_tot[$key] = $tot;
        //debug(count($list[$key]));
        $tot[dt] = $key;
        $list[$key][count($list[$key])] = $tot; //합계 추가.
    }
    //debug($list_tot);
    
    $list['total'][] = $sum; //총합계 추가
}
//debug($list);

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
                    <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("회원매출입금현황")?></a>
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
                    <label class="col-md-2 control-label"><?=_("휴대전화")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[mobile]?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("전화번호")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[phone]?></label>
                    </div>                    
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("대표세금계산서담당자")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$etc_name?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("전자세금계산서발행이메일")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$etc_email?></label>
                    </div>                    
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("사업자번호")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[cust_no]?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("사업자명")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[cust_name]?></label>
                    </div>                    
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("대표자명")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[cust_ceo]?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("휴대전화")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[cust_ceo_phone]?></label>
                    </div>                    
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("업태")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[cust_type]?></label>
                    </div>
                    <label class="col-md-2 control-label"><?=_("종목")?></label>
                    <div class="col-md-2">
                        <label class="control-label"><?=$data[cust_class]?></label>
                    </div>                    
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?=_("사업장주소")?></label>
                    <div class="col-md-6">
                        <label class="control-label">[<?=$data[cust_zipcode]?>] <?=$data[cust_address]?> <?=$data[cust_address_sub]?></label>
                    </div>
                </div>
                </form>
            </div>
        </div>
    
        <ul class="nav nav-tabs">
            <li class="active"><a href="#default-tab-1" data-toggle="tab"><?=_("회원 매출입금 현황")?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="default-tab-1">
                <div class="panel-body">
                   <div class="table-responsive">
                      <table class="table table-bordered table-hover">
                         <thead>
                            <tr>

                               <th><?=_("주문일자")?>/<?=_("접수일자")?></th>                                                      
                               <th><?=_("공급가액")?></th>
                               <th><?=_("부가세")?></th>
                               <th><?=_("총주문금액")?></th>
                               <th><?=_("현금성입금")?></th>                               
                               <th><?=_("현금영수증발행금액")?></th>
                               <th><?=_("카드")?></th>
                               <th><?=_("선발행입금사용금액")?></th>
                               <th><?=_("총입금액")?></th>
                               <th><span class="red"><?=_("차액")?></span></th>
                               <th><span class="red"><?=_("미수(-선입) 잔액")?></span></th>
                            </tr>
                         </thead>
    
                         <tbody>
                         <?
                         foreach ($list as $key => $val) {
                            $style = "";
                            foreach ($val as $k => $value) {
                                if(strlen($value[dt])==7) $style = "background-color: #E8ECF1;";
                         ?>
                            <tr>
                               <td style="<?=$style?>"><?=$value[dt]?></td>
                               <td style="<?=$style?>"><?=number_format($value[payprice])?></td>
                               <td style="<?=$style?>"><?=number_format($value[vat_price])?></td>
                               <td style="<?=$style?>"><?=number_format($value[totpayprice])?></td>
                               <td style="<?=$style?>"><?=number_format($value[cash_money])?></td>
                               <td style="<?=$style?>"><?=number_format($value[cashreceipt_money])?></td>
                               <td style="<?=$style?>"><?=number_format($value[card_money])?></td>
                               <td style="<?=$style?>"><?=number_format($value[pre_deposit_price])?></td>
                               <td style="<?=$style?>"><?=number_format($value[deposit_price])?></td>
                               <td style="<?=$style?>"><?=number_format($value[totpayprice] - $value[deposit_price])?></td>
                               <td style="<?=$style?>"><?=number_format($value[deposit_price] - $value[totpayprice])?></td>
                            </tr>
                         <? 
                            }
                         } 
                         ?>
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