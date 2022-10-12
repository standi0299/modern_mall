<?

include_once "../_pheader.php";

if (!is_array($_POST[chk]) || !count($_POST[chk])){
	msg(_("환불할 아이템을 선택해주세요."));
	echo "<script>parent.closeLayer();</script>";
	exit;
}

$r_pods_order_trans = array(-1=>_("실패"),0=>_("미전송"),1=>_("완료"));

$r_rid = get_release();

foreach ($_POST[chk] as $v){

	list($payno,$ordno,$ordseq) = explode("|",$v);

	if (!$loop[$ordno]){
		$data = $db->fetch("select * from exm_ord where payno = '$payno' and ordno = '$ordno'");	
		$loop[$data[ordno]] = $data;
		unset($data);
	}

	$data = $db->fetch("select * from exm_ord_item where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'");

	if ($data[addopt]) $data[addopt] = unserialize($data[addopt]);
	if ($data[printopt]) $data[printopt] = unserialize($data[printopt]);
	
	if (!in_array($data[itemstep],array(2,3,4))){
		$msg[] = "["._("주문번호").":".$data[ordno]." "._("아이템번호").":".$data[ordseq]."]".$r_step[$data[itemstep]]." "._("상태인 아이템은 환불이 불가능합니다.");
	}
	$loop[$data[ordno]][item][$data[ordseq]] = $data;

}

$pay = $db->fetch("select * from exm_pay where payno = '$payno'");

if ($msg){
	$msg = implode("\\r\\n",$msg);
	msg($msg);
	echo "<script>parent.closeLayer()</script>";
}

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<link href="/css/style.css" rel="stylesheet">
<link href="/css/table.css" rel="stylesheet">

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("환불처리하기")?></a>
            </div>
         </div>
      </div>
      
      <form class="form-horizontal form-bordered" method="post" action="indb.php" onsubmit="return confirm('<?=_("필요한 정보를 정확히 입력하셨습니까?")?>')">
      	<input type="hidden" name="mode" value="refund_create" />
      	<input type="hidden" name="payno" value="<?=$payno?>" />
      	
      	<div class="panel panel-inverse">
          <div class="panel-body panel-form">
         	<div class="panel-body">
         		<div class="table-responsive">


<table class="tb22 eng">
<tr>
	<th width="90"><?=_("주문번호")?></th>
	<th colspan="2"><?=_("상품명")?></th>
	<th><?=_("판매단가")?></th>
	<th><?=_("수량")?></th>
	<th><?=_("환불수량")?></th>
	<th><?=_("합계")?></th>
	<th><?=_("상태")?></th>
</tr>
<?
foreach ($loop as $k=>$ord){ $idx=0; foreach ($ord[item] as $k2=>$item){
	$pay[itemprice]+=$item[payprice];
	$pay[reserve]+=$item[reserve];

	$query = "select podsno,podoptno from exm_goods_opt where goodsno = '$item[goodsno]' and optno = '$item[optno]'";
	list ($item[podsno],$item[podoptno]) = $db->fetch($query,1);
	
	if (!$item[podsno]){
		$query = "select podsno from exm_goods where goodsno = '$item[goodsno]'";
		list ($item[podsno]) = $db->fetch($query,1);
	}
	if (!$item[podoptno]) $item[podoptno] = 1;

	if ($item[dc_couponsetno]){
		$query = "select coupon_name from exm_coupon_set a inner join exm_coupon b on a.cid = b.cid and a.coupon_code = b.coupon_code where no = '$item[dc_couponsetno]'";
		list($item[dc_coupon_name]) = $db->fetch($query,1);
	}
	if ($item[coupon_areservesetno]){
		$query = "select coupon_name from exm_coupon_set a inner join exm_coupon b on a.cid = b.cid and a.coupon_code = b.coupon_code where no = '$item[coupon_areservesetno]'";
		list($item[reserve_coupon_name]) = $db->fetch($query,1);
	}

	$item[pcs_price] = $item[goods_price]+$item[aprice]+$item[addopt_aprice]+$item[print_aprice]-$item[dc_member];

?>
<tr align="center">
	<? if (!$idx){ ?>
	<td rowspan="<?=count($ord[item])?>">
	<b><?=$ord[ordno]?><br/>
	<?=$r_rid[$ord[rid]]?><br/>
	<?=_("배송비")?>:<?=$ord[shipprice]?></b>
	</td>
	<? } ?>
	<td width="30"><?=goodsListImg($item[goodsno],30,"border:1px solid #DEDEDE;")?></td>
	<td class="c1">
	<div><?=_("상품번호")?> : <?=$item[goodsno]?></div>
	<?=$item[goodsnm]?>
	<? if ($item[opt]){ ?>
	<div class="blue"><?=$item[opt]?></div>
	<? } ?>
	<? if ($item[addopt]) foreach ($item[addopt] as $v){ ?>
	<div class="green"><?=$v[addopt_bundle_name]?>:<?=$v[addoptnm]?></div>
	<? } ?>
	<? if ($item[printopt]) foreach ($item[printopt] as $v){ ?>
	<div class="green"><?=$v[printoptnm]?>:<?=$v[ea]?></div>
	<? } ?>
	</td>
	<td align="right">
		<div><?=_("상품")?>:<?=number_format($item[goods_price])?></div>
		<? if ($item[aprice]){ ?>
		<div><?=_("옵션")?>:+<?=number_format($item[aprice])?></div>
		<? } ?>
		<? if ($item[addopt_aprice]){ ?>
		<div><?=_("추가")?>:+<?=number_format($item[addopt_aprice])?></div>
		<? } ?>
		<? if ($item[print_aprice]){ ?>
		<div><?=_("인화")?>:+<?=number_format($item[print_aprice])?></div>
		<? } ?>
		<? if ($item[dc_member]){ ?>
		<div><?=_("회원할인")?>:-<?=number_format($item[dc_member])?></div>
		<? } ?>
		<div style="border-top:1px solid #DEDEDE;margin-top:3px;padding-top:3px;"><b><?=number_format(($item[goods_price]+$item[aprice]+$item[addopt_aprice]+$item[print_aprice]-$item[dc_member]))?></b></div>
	</td>
	<td><?=$item[ea]?></td>
	<td><input type="text" name="ea[<?=$item[ordno]?>|<?=$item[ordseq]?>]" value="<?=$item[ea]?>" size="2" pt="_pt_numplus" class="_num refund_ea" maxea="<?=$item[ea]?>" pcs_price="<?=$item[pcs_price]?>"/></td>
	<td align="right">
		<b><?=number_format(($item[goods_price]+$item[aprice]+$item[addopt_aprice]+$item[print_aprice]-$item[dc_member])*$item[ea])?></b>
		<? if ($item[dc_coupon]){ ?>
		<div style="margin-top:2px;" class="hand" onclick="$j(this).next().slideToggle()"><?=_("쿠폰할인")?>:-<?=number_format($item[dc_coupon])?></div><div style="position:absolute;background:#FFFFFF;width:200px;font:8pt 돋움;border:1px solid #000000;display:none;padding:2px;" align="left"><?=_("사용쿠폰")?>:<?=$item[dc_coupon_name]?></div>
		<? } ?>
		<div style="border-top:1px solid #DEDEDE;margin-top:3px;padding-top:3px;"><b><?=number_format($item[payprice])?></b></div>
	</td>
	<td>
	<?=$r_step[$item[itemstep]]?>
	<? if ($item[storageid] && $item[itemstep] >= 2){ ?>
	<div class="stxt hand" onclick="$j(this).next().slideToggle()"><?=_("pod통신")?>: <?=$r_pods_order_trans[$item[pods_trans]]?></div><div class="stxt" style="display:none;position:absolute;border:1px solid #000000;right:0;width:100px;padding:5px;background:#FFFFFF;word-break:break-all" align="left"><?=($item[pods_trans_msg]==1)?_("성공"):$item[pods_trans_msg];?></div>
	<? } ?>
	</td>
</tr>
<? $idx++; }} ?>
</table>

<table class="tb1">
<tr>
	<th><?=_("결제정보")?></th>
	<td>
	<? if ($pay[paymethod]){ ?>
	<div><?=$r_paymethod[$pay[paymethod]]?> : <b><?=number_format($pay[payprice])?></b></div>
	<? } ?>
	<? if ($pay[dc_emoney]){ ?>
	<div><?=_("적립금사용")?> : <b><?=number_format($pay[dc_emoney])?></b></div>
	<? } ?>
	<? if ($pay[dc_coupon]){ ?>
	<div><?=_("쿠폰사용")?> : <b><?=number_format($pay[dc_coupon])?></b></div>
	<? } ?>
	</td>
</tr>
<tr>
	<th><?=_("환불예상액")?></th>
	<td>
	<b id="refund_price_str" class="red" style="font-size:13pt;">.</b>
	<span class="stxt"><?=_("상품의 수량과 단가를 자동으로 계산해낸 참고용 금액입니다.")?></span>
	</td>
</tr>
<tr>
	<th><?=_("환불액입력")?></th>
	<td style="padding:1px;">
	
	<table class="tb1">
	<tr>
		<th><?=_("현금환불")?></th>
		<td>
		<input type="text" name="cash" pt="_pt_numplus" size="10" class="_num refund_kind"/>
		</td>
		<th><?=_("PG취소")?></th>
		<td>
		<input type="text" name="pg" pt="_pt_numplus" size="10" class="_num refund_kind"/>
		</td>
	</tr>
	<tr>
		<th><?=_("적립금환불")?></th>
		<td>
		<input type="text" name="emoney" pt="_pt_numplus" size="10" class="_num refund_kind"/>
		</td>
		<th><?=_("고객부담")?></th>
		<td>
		<input type="text" name="custom" pt="_pt_numplus" size="10" class="_num refund_kind"/>
		</td>
	</tr>
	</table>

	</td>
</tr>
<tr>
	<th><?=_("입력된 환불액총액")?></th>
	<td>
	<b id="refund_totalprice" class="red" style="font-size:13pt;">0</b>
	</td>
</tr>
<tr>
	<th><?=_("관리자메모")?></th>
	<td>
	<textarea name="memo" style="width:100%;height:100px;"></textarea>
	</td>
</tr>
</table>

<div class="btn">
<div class="stxt"><?=_("확인버튼을 누르기전, 입력된 내용을 정확히 확인해보시기 바랍니다.")?></div>
<!--<input type="image" src="../img/bt_submit_l.png"/>
<img src="../img/bt_cancel_l.png" class="hand" onclick="parent.closeLayer();"/>-->
</div>
         			
         			
         		</div>
         	</div>
         	
         	<div class="row">
         		<div class="col-md-12">
         			<p class="pull-right">
         				<button type="submit" class="btn btn-sm btn-primary"><?=_("저장")?></button>
         				<button type="button" class="btn btn-sm btn-default"onclick="parent.closeLayer();"><?=_("취소")?></button>
         			</p>
         		</div>
         	</div>
          </div>
        </div>
      </form>
   </div>
</div>

<script>
$j(function(){
	$j(".refund_ea").blur(function(){
		if (parseInt($j(this).val()) > parseInt($j(this).attr("maxea"))){
			$j(this).val($j(this).attr("maxea"));
		}
		if (!parseInt($j(this).val())){
			$j(this).val(1);
		}
		calcueprice();
	});
	calcueprice();

	$j(".refund_kind").keyup(function(){
		calcuerefund();
	});

});

function calcueprice(){	
	var refund_price = 0;
	$j(".refund_ea").each(function(){
		refund_price += parseInt($j(this).val()) * parseInt($j(this).attr("pcs_price"));
	});
	$j("#refund_price_str").html(comma(refund_price));
}

function calcuerefund(){
	var refund_price = 0;
	if ($j("input[name=cash]").val()){
		refund_price += parseInt($j("input[name=cash]").val());
	}
	if ($j("input[name=pg]").val()){
		refund_price += parseInt($j("input[name=pg]").val());
	}
	if ($j("input[name=emoney]").val()){
		refund_price += parseInt($j("input[name=emoney]").val());
	}
	if ($j("input[name=custom]").val()){
		refund_price += parseInt($j("input[name=custom]").val());
	}
	$j("#refund_totalprice").html(comma(refund_price));
}
</script>

<? include "../_footer_app_init.php"; ?>

<script type="text/javascript" src="../assets/plugins/switchery/switchery.min.js"></script>
<script type="text/javascript" src="../assets/js/form-slider-switcher.demo.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	FormSliderSwitcher.init();
});
</script>

<? include_once "../_pfooter.php"; ?>