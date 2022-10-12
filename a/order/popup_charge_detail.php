<?
include dirname(__FILE__)."/../_pheader.php";
$m_pretty = new M_pretty();

$data = $m_pretty->getPointChargeHistory($cid, $_GET[mid], $_GET[tno]);
//debug($data);

if (!$data) msg(_("결제정보가 존재하지 않습니다."), "close");
?>

<script type="text/javascript">
  /* 신용카드 영수증 */ 
  /* 실결제시 : "https://admin8.kcp.co.kr/assist/bill.BillAction.do?cmd=card_bill&tno=" */
  /* 테스트시 : "https://testadmin8.kcp.co.kr/assist/bill.BillAction.do?cmd=card_bill&tno=" */
  function receiptView( tno, ordr_idxx, amount )
  {
    receiptWin = "https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno=";
    receiptWin += tno + "&";
    receiptWin += "order_no=" + ordr_idxx + "&"; 
    receiptWin += "trade_mony=" + amount ;
    window.open(receiptWin, "", "width=455, height=815"); 
  }
</script>
    
<div id="page-container" class="page-without-sidebar page-header-fixed">
    <!-- begin #content -->
    <div id="content" class="content">
        <!-- begin #header -->
        <div class="panel panel-inverse">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title"><?=_("포인트 충전 내역")?></h4>
                </div>
            </div>
            
            <div class="panel-body panel-form" style="margin:0 10px;">
                <table>
                    <tr>
                        <td><?=_("주문일자")?> : <?=$data[regist_date]?></td>
                    </tr>
                    <tr>
                        <td><?=_("결제방법")?> : <?=$r_pay_kind[$data[account_pay_method]]?></td>
                    </tr>
                    <tr>
                        <td><?=_("결제금액")?> : <?=number_format($data[account_price])?></td>
                    </tr>
                    <tr>
                        <td><?=_("충전포인트")?> : <?=number_format($data[account_point])?></td>
                    </tr>
                    <tr>
                        <td>
                        	<div style="margin-bottom:20px;">
	                            <? if ($data[account_pay_method] == 'v') { ?>
	                                <input type="button" class="btn btn-sm btn-primary" value='<?=_("세금계산서 발행 요청")?>' onclick="location.href='indb.php?mode=tax_paper_point&account_price=<?=$data[account_price]?>&account_date=<?=$data[regist_date]?>';">
	                            <? } else if ($data[account_pay_method] == 'c') { ?>
	                                <input type="button" class="btn btn-sm btn-primary" value='<?=_("거래 명세서 출력")?>' onclick="receiptView('<?=$data[tno]?>','<?=$data[payno]?>','<?=$data[account_price]?>');">
	                            <? } ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<? include dirname(__FILE__) . "/../_pfooter.php"; ?>