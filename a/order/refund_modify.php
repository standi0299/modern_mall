<?
include "../_header.php";
include "../_left_menu.php";

$r_refundstate = array(_("접수"),_("완료"));
$r_pods_order_trans = array(-1 => _("실패"), 0 => _("미전송"), 1 => _("완료"));
$refund = $db->fetch("select * from exm_refund where refundno = '$_GET[refundno]'");

$pay = $db->fetch("select * from exm_pay where payno = '$refund[payno]'");

$query = "
select * from
   exm_ord a
   inner join exm_ord_item b on a.payno = b.payno and a.ordno = b.ordno
where
   a.payno = '$pay[payno]'
   and b.refundno = '$refund[refundno]'
";

$res = $db->query($query);
$loop = array();
while ($data = $db->fetch($res)){

   if ($data[addopt]) $data[addopt] = unserialize($data[addopt]);
   if ($data[printopt]) $data[printopt] = unserialize($data[printopt]);
   if (!$loop[$data[ordno]]) $loop[$data[ordno]] = $data;
   $loop[$data[ordno]][item][$data[ordseq]] = $data;
}

?>

<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
<link href="../assets/plugins/DataTables-1.9.4/css/data-table.css" rel="stylesheet" />
<!-- ================== END PAGE LEVEL STYLE ================== -->

<div id="content" class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="col-md-12">

            <div class="tab-content">
               <div class="panel-body panel-form">
                  <form class="form-horizontal form-bordered" name="fm" method="POST" action="indb.php">
                     <input type="hidden" name="mode" value="refund_modify"/>
                     <input type="hidden" name="refundno" value="<?=$refund[refundno]?>"/>

                     <div class="panel-body">
                        <div class="table-responsive">
                           <table id="data-table" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                    <th><?=_("주문번호")?></th>
                                    <th colspan="2"><?=_("상품명")?></th>
                                    <th><?=_("판매단가")?></th>
                                    <th><?=_("수량")?></th>
                                    <th><?=_("합계")?></th>
                                    <th><?=_("상태")?></th>
                                 </tr>
                              </thead>
                              <tbody>
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
                                    <td align="left">
                                    <div><?=_("상품번호")?> : <?=$item[goodsno]?></div>
                                    <?=$item[goodsnm]?>
                                    <? if ($item[opt]){ ?>
                                    <div class="blue"><?=$item[opt]?></div>
                                    <? } ?>
                                    <? if (is_array($item[addopt])) foreach ($item[addopt] as $v){ ?>
                                    <div class="green"><?=$v[addopt_bundle_name]?>:<?=$v[addoptnm]?></div>
                                    <? } ?>
                                    <? if (is_array($item[printopt])) foreach ($item[printopt] as $v){ ?>
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
                                    <td align="right">
                                       <b><?=number_format(($item[goods_price]+$item[aprice]+$item[addopt_aprice]+$item[print_aprice]-$item[dc_member])*$item[ea])?></b>
                                       <? if ($item[dc_coupon]){ ?>
                                       <div style="margin-top:2px;" class="hand" onclick="$j(this).next().slideToggle()"><?=_("쿠폰할인")?>:-<?=number_format($item[dc_coupon])?></div><div style="position:absolute;background:#FFFFFF;width:200px;font:8pt 돋움;border:1px solid #000000;display:none;padding:2px;" align="left"><?=_("사용쿠폰")?>:<?=$item[dc_coupon_name]?></div>
                                       <? } ?>
                                       <div style="border-top:1px solid #DEDEDE;margin-top:3px;padding-top:3px;"><b><?=number_format($item[payprice])?></b></div>
                                    </td>
                                    <td>
                                    <?=$r_step[$item[itemstep]]?>
                                    <? if ($item[storageid] && $item[itemstep] >= 2) { ?>
                                    <div class="stxt hand" onclick="$j(this).next().slideToggle()"><?=_("pod통신")?>: <?=$r_pods_order_trans[$item[pods_trans]]?></div>
                                    <div class="stxt" style="display:none;position:absolute;border:1px solid #000000;right:0;width:100px;padding:5px;background:#FFFFFF;word-break:break-all" align="left"><?=($item[pods_trans_msg]==1)?_("성공"):$item[pods_trans_msg];?></div>
                                    <? } ?>
                                    </td>
                                 </tr>
                                 <? $idx++; }} ?>
                              </tbody>
                           </table>
                        </div>

                        <table id="data-table" class="table table-striped table-bordered">
                           <tr>
                              <th><?=_("환불상태")?></th>
                              <td>
                                 <?=$r_refundstate[$refund[state]]?>
                              </td>
                           </tr>
                           <tr>
                              <th><?=_("환불접수일")?></th>
                              <td>
                                 <?=$refund[requestdt]?> by <?=$refund[request_admin]?>
                              </td>
                           </tr>
                           <? if ($refund[state]){ ?>
                           <tr>
                              <th><?=_("환불완료일")?></th>
                              <td>
                                 <div><?=$refund[completedt]?> by <?=$refund[complete_admin]?></div>
                              </td>
                           </tr>
                           <? } ?>
                           <tr>
                              <th><?=_("결제번호")?></th>
                              <td><a href="javascript:;" onclick="popup('order_detail_popup.php?payno=<?=$pay[payno]?>',1000,750)"><b><?=$pay[payno]?></b></a></td>
                           </tr>
                           <tr>
                              <th><?=_("환불정보")?></th>
                              <td style="padding:1px;">
                                 <table id="data-table" class="table table-hover table-bordered">
                                    <tr>
                                       <th><?=_("현금환불")?></th>
                                       <td>
                                          <input type="text" name="cash" pt="_pt_numplus" size="10" value="<?=$refund[cash]?>" class="form-control"/>
                                       </td>
                                       <th><?=_("PG취소")?></th>
                                       <td>
                                          <input type="text" name="pg" pt="_pt_numplus" size="10" value="<?=$refund[pg]?>" class="form-control"/>
                                       </td>
                                    </tr>
                                    <tr>
                                       <th><?=_("적립금환불")?></th>
                                       <td>
                                          <input type="text" name="emoney" pt="_pt_numplus" size="10" value="<?=$refund[emoney]?>" class="form-control"/>
                                       </td>
                                       <th><?=_("고객부담")?></th>
                                       <td>
                                          <input type="text" name="custom" pt="_pt_numplus" size="10" value="<?=$refund[custom]?>" class="form-control"/>
                                       </td>
                                    </tr>
                                 </table>
                              </td>
                           </tr>
                           <tr>
                              <th><?=_("환불총액")?></th>
                              <td><b class="red" id="refund_totalprice"><?=number_format($refund[cash] + $refund[pg] + $refund[emoney] + $refund[custom])?></b><?=_("원")?></td>
                           </tr>
                           <tr>
                              <th><?=_("관리자메모")?></th>
                              <td>
                              <?=nl2br($refund[memo])?>
                              <textarea name="memo" class="form-control" style="margin-top:10px;width:100%;height:100px;"></textarea>
                              </td>
                           </tr>
                        </table>
                     </div>

                     <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-10">
                           <button type="submit" class="btn btn-sm btn-success">
                              <?=_("수정")?>
                           </button>
                           <button type="button" class="btn btn-sm btn-default" onclick="javascript:history.back()"><?=_("취소")?></button>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<? include "../_footer_app_init.php"; ?>

<script src="../assets/plugins/DataTables-1.9.4/js/jquery.dataTables.js"></script>
<script src="../assets/plugins/DataTables-1.9.4/js/data-table.js"></script>


<? include "../_footer_app_exec.php"; ?>