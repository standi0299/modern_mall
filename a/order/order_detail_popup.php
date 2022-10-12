<?

/*
* @date : 20180320
* @author : kdk
* @brief : 주문환불 기능 추가.
* @request : 포토큐브.
* @desc : 센터  기능 그대로 적용. 
* @todo : 
*/

/*
* @date : 20180122 (20180122)
* @author : kdk
* @brief : 주문취소 기능 추가. (exm_pay.paystep=-9)
* @request : 김기웅 이사.
* @desc : 기존 무통장에서만 사용하던 취소기능을 다른 결제방식에도 사용.(리스트 페이지 명칭 변경 입금전 취소->취소리스트.) 
* @todo : 
*/

include_once "../_pheader.php";
$m_pretty = new M_pretty();

if (!$_GET[payno]) {
    msg(_("필수 정보가 넘어오지 못했습니다!"), "close");
}

$r_pods_order_trans = array(-1 => _("실패"), 0 => _("미전송"), 1 => _("완료"));
$r_step_selectBox = array(2=>_("제작대기"), 3=>_("제작중"), 4=>_("발송대기"), 5=>_("출고완료"));

### 배송업체정보추출
$r_shipcomp = get_shipcomp();

### 출고처 추출
$r_rid["|self|"] = _("자체출고");
$r_rid_x = get_release();
$r_rid = array_merge($r_rid, $r_rid_x);

### 결제정보추출
$pay = $db -> fetch("select * from exm_pay where payno = '$_GET[payno]'");
//$pay[receiver_zipcode] = explode("-", $pay[receiver_zipcode]);

$m_order = new M_order();

//상품 전체 개수 / 15.08.25 / kjm
$totalea = $m_order->getOrdItemSumEa($_GET[payno]);

$query = "
select * from
   exm_ord
where
   payno = '$pay[payno]'
";
$db -> query("set names utf8");
$res = $db -> query($query);

$cancelok = true;
while ($data = $db -> fetch($res)) {
   $loop[$data[ordno]] = $data;
   $query = "select * from exm_ord_item where payno = '$pay[payno]' and ordno = '$data[ordno]' order by ordseq";
   //debug($query);
   $res2 = $db -> query($query);
   while ($tmp = $db -> fetch($res2)) {
      if ($tmp[est_order_option_desc]) {
         $tmp[est_order_option_desc_str] = str_replace("<br>", ",", str_replace("::", ":", $tmp[est_order_option_desc]));
         if ($tmp[est_order_option_desc_str]) $tmp[est_order_option_desc_str] = substr($tmp[est_order_option_desc_str] , 0, -1); 
      }
      if ($tmp[addopt])
         $tmp[addopt] = unserialize($tmp[addopt]);
      if ($tmp[printopt])
         $tmp[printopt] = unserialize($tmp[printopt]);

      if ($tmp[ext_json_data]) {
         $tmp[ext_json_data] = json_decode($tmp[ext_json_data],1);
      }

      $loop[$data[ordno]][item][$tmp[ordseq]] = $tmp;
      if ($tmp[itemstep] != 1)
         $cancelok = false;
   }
}

//환불 총금액 조회
$pay[refund_total_price] = 0;
$addWhere = "and a.payno='$pay[payno]' and a.state='1'";
$refund_data = $m_order->getRefundData($cid, $addWhere);

if (count($refund_data) > 0) {
	foreach ($refund_data as $refund_k=>$refund_v) {
		$pay[refund_total_price] = $pay[refund_total_price] + $refund_v[cash] + $refund_v[pg] + $refund_v[emoney] + $refund_v[custom];
	}
}

//모바일 주문 구분. 스킨에 따라 모바일 주문을 구분한다.			181214		chunter 
if ($cfg[admin_order_web_app] == "Y")
{
	$order_skin = " <font color='blue'><b>[Web]</b></font>"; 
	if ($pay['order_skin'] == "m_default") $order_skin = " <font color='red'><b>[App]</b></font>";
}
// debug($loop);
?>

<style>
#ord_box {margin-left:5px;margin-right:5px}
</style>

<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("주문명세서")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <div class="panel-body">
               <div class="table-responsive">
                  <form name="fm_item" class="form-horizontal form-bordered" method="post" action="indb.php">
                  <input type="hidden" name="mode" value="stepback_change">
                  <input type="hidden" name="mid" value="<?=$pay[mid]?>">
                  
                  <table id="data-table" class="table table-bordered">
                     <thead>
                        <tr>
                           <th width="80"><?=_("주문번호")?></th>
                           <th><input type="checkbox" style="width:11px;" onclick="chkrev('chk[]');return false;"/></th>
                           <th colspan="2"><?=_("상품명")?></th>
                           <th><?=_("판매단가")?></th>
                           <th><?=_("수량")?></th>
                           <th><?=_("합계")?></th>
                           <th><?=_("적립액")?></th>
                           <th><?=_("상태")?></th>
                           <th><?=_("제작관리")?></th>
                           <th><?=_("배송정보")?></th>
                        </tr>
                     </thead>

                     <tbody>
                        <? foreach ($loop as $k=>$ord){
                           $idx=0;
                           foreach ($ord[item] as $k2=>$item) {

                           $pay[itemprice]+=$item[payprice];
                           $pay[reserve]+=$item[reserve];

                           $query = "select podsno,podoptno from exm_goods_opt where goodsno = '$item[goodsno]' and optno = '$item[optno]'";

                           list ($item[podsno],$item[podoptno]) = $db->fetch($query,1);

                           if (!$item[podsno]){
                              $query = "select podsno,pods_use,podskind from exm_goods where goodsno = '$item[goodsno]'";
                              list ($item[podsno],$item[pods_use],$item[podskind]) = $db->fetch($query,1);
                           }
                           if (!$item[podoptno]) $item[podoptno] = 1;

                           if($item[storageid]) {
                              //복수 편집기 처리 pods_use, podskind, podsno 장바구니 편집기 호출 함수 수정 2016.05.20 by kdk
                              $query = "select pods_use,podsno,podskind from exm_edit where storageid = '$item[storageid]'";
                                list ($temp[pods_use],$temp[podsno],$temp[podskind]) = $db->fetch($query,1);
                              //debug($temp);
                              if($temp[pods_use]) $item[pods_use] = $temp[pods_use];
                              if($temp[podsno]) $item[podsno] = $temp[podsno];
                              if($temp[podskind]) $item[podskind] = $temp[podskind];
                           }
                           // debug($item);
                        ?>

                        <tr align="center">
                        <? if (!$idx){ ?>
                        <td rowspan="<?=count($ord[item])?>">
                        <b><?=$ord[ordno]?><br/>
                        <?=$r_rid[$ord[rid]]?><br/>
                        <?=_("배송비")?>:<?=$ord[shipprice]?></b>

                        <? //주문 배송 정보 출력 추가      20141202    chunter
                        if ($ord[order_shiptype] != "1" && $ord[order_shiptype] != "") { ?>
                           <BR><span class="green"><?=$r_order_shiptype[$ord[order_shiptype]]?></span>
                        <? } ?>

                        </td>
                        <? } ?>
                        <td style="padding:1px;" width="13">
                        <!-- 상품별 itemstep 변경을 위해 value에 itemstep값을 추가 / 14.03.04 / kjm -->
                        <input type="checkbox" name="chk[]" style="width:11px;" value="<?=$item[payno]?>|<?=$item[ordno]?>|<?=$item[ordseq]?>|<?=$item[itemstep]?>" required label='<?=_("아이템")?>'/>
                        </td>
                        <td width="30"><?=goodsListImg($item[goodsno], 30, "border:1px solid #DEDEDE;")?></td>
                        <td class="c1">
                        <div><b><?=_("상품번호")?> : <?=$item[goodsno]?></b></div>
                            <? if($item[package_order_code]) {//패키지상품 여부. / 20180104 / kdk ?>    
                                <span class="orange">[<?=_("패키지")?>]</span>&nbsp;
                            <? } ?>
                        <?=$item[goodsnm]?>

                        <? if ($item[title]){ ?>
                        <div class="blue desc"><?=_("주문 제목")?>:<?=$item[title]?></div>
                        <? } ?>

                        <? if ($item[opt]){ ?>
                        <div class="blue desc"><?=$item[opt]?></div>
                        <? } ?>
                        
                        <? if ($item[addopt]) foreach ($item[addopt] as $v){ ?>
                        <div class="green desc"><?=$v[addopt_bundle_name]?>:<?=$v[addoptnm]?></div>
                        <? } ?>
                        
                        <? if ($item[printopt]) foreach ($item[printopt] as $v){ ?>
                        <div class="blue desc"><?=$v[printoptnm]?>:<?=$v[ea]?> (+<?=number_format($v[print_price])?>)</div>
                        <? } ?>
                        
                        <? if ($item[est_order_option_desc_str]){ ?>
                        <div class="blue desc"><?=$item[est_order_option_desc_str]?></div>
                        <? } ?>

                        <? if ($item[est_order_memo]){ ?>
                        <div class="blue desc"><?=_("주문 메모")?>:<?=$item[est_order_memo]?></div>
                        <? } ?>

                        <? if ($item[storageid] && $item[est_order_type]!="UPLOAD" && $item[est_order_type]!="NOFILES" && $item[podskind]!="99999" && strpos($item[storageid], "_temp_") === FALSE){ ?>
                        <br><u onclick="window.open('../../module/preview.php?goodsno=<?=$item[goodsno]?>&storageid=<?=$item[storageid]?>','','width=910,height=537,scrollbars=1');" style="cursor:pointer"><?=_("편집내용 미리보기")?></u>
                        <? } else if ($item[storageid] && $item[est_order_type]=="UPLOAD" && strpos($item[storageid], "_temp_") === FALSE){ ?>
                        <br><u onclick="popupLayer('download.est.php?storageid=<?=$item[storageid]?>','',600,400);" style="cursor:pointer"><?=_("업로드원본")?></u>
                        <? } ?>

                        <? if ($item[storageid] && !in_array($item[podskind],array(8,9)) && $item[goodsno]!="-1" && $item[est_order_type]!="UPLOAD" && $item[est_order_type]!="NOFILES" && $item[pods_use] && $item[podsno] && strpos($item[storageid], "_temp_") === FALSE){ ?>

                           <?
                              if ($item[pods_use]=="2" && $item[ext_json_data][editor_type] == "web") {
                                 $editor_type = $item[ext_json_data][editor_type];   
                              }
                           ?>   

                           <? if ($item[pods_use]=="3"){ ?>
                              <u onclick="call_wpod('PodsCallEditorUpdate.php?pods_use=<?=$item[pods_use]?>&podskind=<?=$item[podskind]?>&podsno=<?=$item[podsno]?>&goodsno=<?=$item[goodsno]?>&optno=<?=$item[optno]?>&storageid=<?=$item[storageid]?>&podoptno=<?=$item[podoptno]?>&addoptno=<?=$item[addoptno]?>&ea=<?=$item[ea]?>&mid=<?=$pay[mid]?>');" style="cursor:pointer"><?=_("편집하기")?></u>
                              
                           <?} else {?>                               
                           	  <u onclick="popupLayer('PodsCallEditorUpdate.php?pods_use=<?=$item[pods_use]?>&podskind=<?=$item[podskind]?>&podsno=<?=$item[podsno]?>&goodsno=<?=$item[goodsno]?>&optno=<?=$item[optno]?>&storageid=<?=$item[storageid]?>&podoptno=<?=$item[podoptno]?>&addoptno=<?=$item[addoptno]?>&ea=<?=$item[ea]?>&mid=<?=$pay[mid]?>&editor_type=<?=$editor_type?>');" style="cursor:pointer"><?=_("편집하기")?></u>
                           <? } ?>
                        <?} else if($item[podskind]=="99999") {?>
                                <u onclick="call_wpod('PodsCallEditorUpdate.php?pods_use=<?=$item[pods_use]?>&podskind=<?=$item[podskind]?>&podsno=<?=$item[podsno]?>&goodsno=<?=$item[goodsno]?>&optno=<?=$item[optno]?>&storageid=<?=$item[storageid]?>&podoptno=<?=$item[podoptno]?>&addoptno=<?=$item[addoptno]?>&ea=<?=$item[ea]?>&mid=<?=$pay[mid]?>');" style="cursor:pointer"><?=_("편집하기...")?></u>
                        <? } ?>
                        
                        </td>
                        <td align="right" nowrap>
                           <div><?=_("상품")?>:<?=number_format($item[goods_price] * $item[ea])?></div>
                           <? if ($item[aprice]){ ?>
                           <div><?=_("옵션")?>:+<?=number_format($item[aprice])?></div>
                           <? } ?>
                           <? if ($item[addopt_aprice]){?>
                           <div><?=_("추가")?>:+<?=number_format($item[addopt_aprice])?></div>
                           <? } ?>
                           <? if ($item[addpage_aprice]){?>
                           <div class="stxt"><?=_("추가페이지")?>:<span class="eng">+<?=number_format($item[addpage_aprice])?></span></div>
                           <? } ?>
                           <? if ($item[dc_member]){ ?>
                           <div><?=_("회원할인")?>:-<?=number_format($item[dc_member])?></div>
                           <? } ?>
                           <div style="border-top:1px solid #DEDEDE;margin-top:3px;padding-top:3px;"><b><?=number_format(($item[goods_price]+$item[aprice]+$item[addopt_aprice]+$item[addpage_aprice]+$item[print_aprice]-$item[dc_member]))?></b></div>
                        </td>
                        <td><?=$item[ea]?></td>
                        
                        <td align="right" nowrap>
                           <b><?=number_format(($item[goods_price] + $item[aprice] + $item[addopt_aprice] + $item[print_aprice] + $item[addpage_aprice] - $item[dc_member]) * $item[ea])?></b>
                           <div style="border-top:1px solid #DEDEDE;margin-top:3px;padding-top:3px;"><b><?=number_format($item[payprice])?></b></div>
                        </td>
                        
                        <td align="right" nowrap>
                           <div><?=_("상품")?>:<?=number_format($item[goods_reserve])?> × <?=$item[ea]?></div>
                           <? if ($item[areserve]){ ?>
                           <div><?=_("옵션")?>:<?=number_format($item[areserve])?> × <?=$item[ea]?></div>
                           <? } ?>
                           <? if ($item[addopt_areserve]){ ?>
                           <div><?=_("추가")?>:<?=number_format($item[addopt_areserve])?> × <?=$item[ea]?></div>
                           <? } ?>
                           <? if ($item[addpage_areserve]){ ?>
                           <div><span class="stxt"><?=_("추가페이지")?></span>:<?=number_format($item[addpage_areserve])?> × <?=$item[ea]?></div>
                           <? } ?>
                           <div style="border-top:1px solid #DEDEDE;margin-top:3px;padding-top:3px;"><b><?=number_format($item[reserve])?></b></div>
                        </td>
                        <td nowrap class="stxt">
                        <span onclick="popupLayer('step_log_popup.php?payno=<?=$item[payno]?>&ordno=<?=$item[ordno]?>&ordseq=<?=$item[ordseq]?>')" class="hand"><?=$r_step[$item[itemstep]]?></span>
                        <? if ($item[itemstep]=="5"){ ?>
                        <div style="margin:2px 0"><a href="indb.php?mode=stepback&from=5&to=4&payno=<?=$item[payno]?>&ordno=<?=$item[ordno]?>&ordseq=<?=$item[ordseq]?>" onclick="return confirm('<?=_("출고를 취소하고 발송대기상태로 전환하시겠습니까?")?>')"/><span class="red"><?=_("출고취소")?></span></a></div>
                        <? } ?>
                        <? if ($item[itemstep2]){ ?>
                        <div class="desc"><?=$item[itemstep2]?></div>
                        <? } ?>
                        <? if ($item[storageid] && $item[est_order_type] != "UPLOAD" && (($item[itemstep] >= 2 && $item[itemstep] <= 10) || in_array($item[itemstep],array(92)))){ ?>
                        <div class="stxt hand" onclick="$j(this).next().slideToggle()"><?=_("pod통신")?>: <?=$r_pods_order_trans[$item[pods_trans]]?></div><div class="stxt" style="white-space:normal;word-break:break-all;display:none;position:absolute;border:1px solid #000000;width:100px;padding:5px;background:#FFFFFF;" align="left"><?=($item[pods_trans_msg] == 1) ? _("성공") : $item[pods_trans_msg];?>
                        <? if ($item[pods_trans]!=1) { ?>
                        <div class="red desc"><a href="indb.php?mode=reset_pod&payno=<?=$item[payno]?>&ordno=<?=$item[ordno]?>&ordseq=<?=$item[ordseq]?>"><?=_("재전송하기")?></a></div>
                        <? } ?>
                        </div>
                        <? } ?>
                        <br>
                        <!-- 주문상태 변경을 위한 콤보박스 / 14.02.20 / kjm -->
                        <!-- 체크한 상품의 값만 넘김 / 14.04.09 / kjm -->
                        <!-- selectbox 변경, 현재 상태가 체크되도록 / 14.06.12 / kjm -->
                        <select name="changeBox[<?=$item[payno]?>|<?=$item[ordno]?>|<?=$item[ordseq]?>|<?=$item[itemstep]?>]">
                        <? foreach($r_step_selectBox as $k=>$v) {?>
                           <option name="step" value="<?=$k?>" <? if($item[itemstep] == $k){?> selected <?}?> ><?=$v?></option>
                        <?}?>
                        <? if ($item[itemstep] == "-9" && in_array($pay[paymethod],array("b","o","oe","v","ve"))) { ?>
                        	<option name="step" value="19" <? if ($item[itemstep] == "19") { ?>selected<? } ?>><?=$r_step[19]?></option>
                        <? } ?>
                        </select>
                        <!-- submit형태가 아닌 _refund_step을 호출하는 형태로 변경 / 14.03.04 / kjm -->
                        <input type="button" value='<?=_("전송")?>' onclick="_refund_step()">
                        <br>
                        </td>

                        <td nowrap>
                        <? if (strpos($item[storageid], "_temp_") === FALSE) { ?>
                            
                        <? if ($item[storageid] && $item[goodsno]!="-1" && $item[podskind]!="99999" && $item[est_order_type]!="UPLOAD" && $item[est_order_type]!="NOFILES"){ ?>
                        <div class="desc"><a href="indb.php?mode=oasis_remake&payno=<?=$item[payno]?>&ordno=<?=$item[ordno]?>&ordseq=<?=$item[ordseq]?>" target="hiddenIfrm" onclick="return confirm('<?=_("! 주의")?>\n<?=_("1. 재합성 요청은 반드시 제작담당자와 협의가 진행되어야 합니다.")?>\n<?=_("2. 협의되지 않은 재합성 요청 접수로 발생하는 장애 처리는 판매처에서 진행하셔야 합니다.")?>\n<?=_("재합성 요청을 진행하시겠습니까?")?>')">[<?=_("재합성 요청")?>]</a></div>
                        <? } ?>
                        
                        <? if ($item[storageid] && $item[est_order_type]=="UPLOAD") { ?>
                        <div class="desc"><span class="hand" onclick="popupLayer('download.est.php?storageid=<?=$item[storageid]?>','',600,400)">[<?=_("업로드원본")?>]</span></div>
                        <?} else if($item[storageid] && $item[podskind]=="99999") {?>
                            <div class="desc"><a class="hand" href="/miodio/download_figure.php?sid=<?=$item[storageid]?>">[<?=_("업로드원본...")?>]</a></div>
                        <? } else {?>
                        <div class="desc"><span class="hand" onclick="popupLayer('download_img.php?mode=filelist&storageid=<?=$item[storageid]?>&pods_use=<?=$item[pods_use]?>','',600,600)">[<?=_("업로드원본")?>]</span></div>
                        <? } ?>
                        
                        <? if ($item[storageid] && $item[goodsno]!="-1" && $item[podskind]!="99999" && $item[est_order_type]!="UPLOAD" && $item[est_order_type]!="NOFILES"){ ?>
                        <div class="desc"><span class="hand" onclick="popupLayer('download_img_imp.php?mode=filelist&payno=<?=$item[payno]?>&ordno=<?=$item[ordno]?>&ordseq=<?=$item[ordseq]?>&storageid=<?=$item[storageid]?>&item_rid=<?=$item[item_rid]?>','',600,600)">[<?=_("합성결과다운")?>]</span></div>
                        <? } ?>
                        
                        <? } ?>
                        </td>

                        <td nowrap>
                        <? if ($item[goodsno]=="-2"){ ?>
                           <div><?=get_studio_upload_detail($item[payno], $item[ordno], $item[ordseq], _("배송정보"), "03")?></div>
                        <? } else { ?>
                           <?=$r_shipcomp[$item[shipcomp]][compnm]?>
                           <div><a href="<?=$r_shipcomp[$item[shipcomp]][url] . $item[shipcode]?>" target="_blank"><?=$item[shipcode]?></a></div>
                           <div class="desc red"><span onclick="popup('order.shipcode.p.php?payno=<?=$item[payno]?>&ordno=<?=$item[ordno]?>&ordseq=<?=$item[ordseq]?>&page=order_detail_popup',630,325,1,'','updatewin')" class="hand"><?=_("송장/수정")?></span></div>
                        <? } ?>
                        
                        <? if ($item[shiptype]) echo $r_shiptype[$item[shiptype]]; ?> 
                        </td>
                     </tr>
                     <? $idx++; } } ?> 

                     <tr align="center">
                        <td><b><?=number_format($pay[shipprice])?></b></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td><b><?=$totalea[totea]?><b></td>
                        <td><b><?=number_format($pay[itemprice])?></b></td>
                        <td><b><?=number_format($pay[reserve])?></b></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>

                     </tbody>
                  </table>
                  </form>
                  <!-- 환불 추가 / 17.08.17 / kdk -->
                  <button type="button" class="btn btn-sm btn-danger" onclick="_refund()"><?=_("환불")?></button>                  
               </div>
               
               <table style="width:100%" height="100">
                  <tr align="center">
                     <td><b><?=_("상품금액")?></b><br/><b class="price1"><?=number_format($pay[saleprice])?></b><?=_("원")?></td>
                     <td><b class="price1" style="font-size:20pt">-</b></td>
                     <td><b><?=_("할인")?></b><br><b class="price1"><?=number_format($pay[dc_member] + $pay[dc_emoney] + $pay[dc_coupon])?></b><?=_("원")?></td>
                     <td width="20"><b class="price1" style="font-size:20pt">[</b></td>
                     <td width="180" align="left" style="padding-left:10px">
                     <table>
                        <tr>
                           <td width="120"><b><?=_("그룹할인")?></b></td>
                           <td align="right"><b class="price1"><?=number_format($pay[dc_member])?></b><?=_("원")?></td>
                        </tr>                        
                        <tr>
                           <td width="120"><b><?=_("쿠폰할인")?></b></td>
                           <td align="right"><b class="price1"><?=number_format($pay[dc_coupon])?></b><?=_("원")?></td>
                        </tr>                        
                        <tr>
                           <td width="120"><b><?=_("프로모션코드 할인")?></b></td>
                           <td align="right"><b class="price1"><?=number_format($pay[dc_sale_code_coupon])?></b><?=_("원")?></td>
                        </tr>
                        <tr>
                           <td><b><?=_("적립금사용")?></b></td>
                           <td align="right"><b class="price1"><?=number_format($pay[dc_emoney])?></b><?=_("원")?></td>
                        </tr>
                        <? if($cfg[pg][tmembership_use]){ ?>
                        <tr>
                           <td width="120"><b><?=_("멤버십 할인")?></b></td>
                           <td align="right"><b class="price1"><?=number_format($pay[dc_partnership])?></b><?=_("원")?></td>
                        </tr>
                        <? } ?>
                     </table>
                     <td width="30"><b class="price1" style="font-size:20pt">]</b></td>
                     <td><b class="price1" style="font-size:20pt">+</b></td>
                     <td><b><?=_("배송비")?></b><br><b class="price1"><?=number_format($pay[shipprice])?></b><?=_("원")?></td>
                     <td><b class="price1" style="font-size:20pt">=</b></td>
                     <td>
                     	<b><?=_("최종결제금액")?></b><br><b class="price2"><?=number_format($pay[payprice])?></b><?=_("원")?>
                     	<? if ($pay[refund_total_price] > 0) { ?>
                     		<br>(<?=_("환불금액")?> <b><?=number_format($pay[refund_total_price])?></b><?=_("원")?>)
                     	<? } ?>
                     </td>
                  </tr>
               </table>
               
               <form name="fm" class="form-horizontal form-bordered" method="post" action="indb.php" onsubmit="return confirm('<?=_("주문자/배송지 정보를 수정하시겠습니까?")?>')">
                  <input type="hidden" name="mode" value="order">
                  <input type="hidden" name="payno" value="<?=$pay[payno]?>">

                  <div class="panel panel-inverse">
                     <div class="panel-heading">
                        <h4 class="panel-title"><?=_("주문정보")?></h4>
                     </div>

                     <div class="panel-body">
                        <table class="table table-bordered">
                           <tr>
                              <th width="150"><?=_("결제번호")?></th>
                              <td><b style="color:#28a5f9"><?=$pay[payno]?></b><?=$order_skin?></td>
                           </tr>
                           <tr>
                              <th><?=_("주문일자")?></th>
                              <td><?=$pay[orddt]?></td>
                           </tr>
                           <tr>
                              <th><?=_("결제금액")?></th>
                              <td>
                              	<b><?=number_format($pay[payprice])?><?=_("원")?></b>
                              	<? if ($pay[refund_total_price] > 0) { ?>
                              		&nbsp;(<?=_("환불금액")?> <b><?=number_format($pay[refund_total_price])?><?=_("원")?></b>)
                              	<? } ?>
                              </td>
                           </tr>
                           <tr>
                              <th><?=_("적립예정액")?></th>
                              <td><?=number_format($pay[reserve])?><?=_("원")?></td>
                           </tr>
                           <tr>
                              <th><?=_("입금여부")?></th>
                              <td>
                              <? if($pay[paystep]>1 && $pay[paystep] < 90 ) { ?><?=_("입금")?>
                                 <?=$pay[paydt]?>
                                 <? if($pay[payadmin]){ ?>(<?=_("처리자")?> : <?=$pay[payadmin]?>)<? } ?>
                                 <? if (in_array($pay[paymethod],array("b","v"))){ ?>
                                 <span class="stxt red"><a href="indb.php?mode=step2to1&payno=<?=$pay[payno]?>" class="red bold" onclick="return confirm('<?=_("정말 입금을 취소하시겠습니까?")?>')"><?=_("입금취소하기")?></a></span>
                                 / <span class="stxt red"><a href="javascript:void0();" onclick="orderCancel('<?=$pay[payno]?>');" class="red bold">[<?=_("주문취소하기")?>]</a></span>
                                 <? } ?>
                              <? } else if($pay[paystep]==1) { ?><?=_("미입금")?>
                                 <span class="stxt red"><a href="javascript:void(0);" onclick="orderCancel('<?=$pay[payno]?>');" class="red bold">[<?=_("주문취소하기")?>]</a></span>
                              <? } else if($pay[paystep]==92) { ?><?=_("승인완료")?>
                                 <?=$pay[confirmdt]?>
                                 <? if ($pay[confirmadmin]){?>(<?=_("처리자")?> : <?=$pay[confirmadmin]?>)<? } ?>
                                 <? if($pay[paymethod] == 't' || $pay[paymethod] == 'd') { ?>
                                    <span class="stxt red"><a href="indb.php?mode=step92to91&payno=<?=$pay[payno]?>" class="red bold" onclick="return confirm('<?=_("정말 승인을 취소하시겠습니까?")?>')"><?=_("승인취소하기")?></a></span>
                                 <? } ?>
                                 / <span class="stxt red"><a href="javascript:void(0);" onclick="orderCancel('<?=$pay[payno]?>');" class="red bold">[<?=_("주문취소하기")?>]</a></span>
                              <? } else if($pay[paystep]==91) { ?><?=_("승인요청")?>
                                 <span class="stxt red"><a href="indb.php?mode=step91to-90&payno=<?=$pay[payno]?>" class="red bold" onclick="return confirm('<?=_("정말 승인을 반려하시겠습니까?")?>')"><?=_("승인반려하기")?></a></span>
                                 / <span class="stxt red"><a href="javascript:void(0);" onclick="orderCancel('<?=$pay[payno]?>');" class="red bold">[<?=_("주문취소하기")?>]</a></span>
                              <? } else { ?><?=$r_step[$pay[paystep]]?>
                              	 <? if ($pay[paystep]==-9 || $pay[paystep]==-90) { ?><?=$pay[canceldt]?><? } ?>
                              <? } ?>
                              <? if($cfg[skin] == 'kids') { ?>
                                 <span class="stxt red"><a href="indb.php?mode=step91tocart&payno=<?=$pay[payno]?>" class="blue bold" onclick="return confirm('<?=_("주문을 되돌리시겠습니까?")?>')"><?=_("주문 되돌리기")?></a></span>
                              <? } ?>
                              
                              </td>
                           </tr>
                           <tr>
                              <th><?=_("결제수단")?></th>
                              <td><?=$r_paymethod[$pay[paymethod]]?></td>
                           </tr>
                           <tr>
                              <th><?=_("입금계좌")?></th>
                              <td><?=($pay[paymethod] != 'c') ? $pay[bankinfo] : ""?></td>
                           </tr>
                           <tr>
                              <th><?=_("PG로그")?></th>
                              <td><?=nl2br($pay[pglog])?></td>
                           </tr>
                        </table>
                     </div>
                  </div>
   
                  <div class="panel panel-inverse">
                     <div class="panel-heading">
                        <h4 class="panel-title"><?=_("주문자정보")?></h4>
                     </div>
   
                     <?
                     if ($pay[mid]) {
                         $info_member = $pay[mid];
                     } else {
                         $info_member = _("비회원");
                     }
                     ?>
                     
                     <div class="panel-body">
                        <table class="table table-bordered form-inline">
                           <tr>
                              <th width="150"><?=_("아이디")?></th>
                              <td><?=$info_member?></td>
                           </tr>
                           <tr>
                              <th><?=_("주문자명")?></th>
                              <td><input type="text" class="form-control" name="orderer_name" value="<?=$pay[orderer_name]?>"></td>
                           </tr>
                           <tr>
                              <th><?=_("입금자명")?></th>
                              <td><input type="text" class="form-control" name="payer_name" value="<?=$pay[payer_name]?>"></td>
                           </tr>
                           <tr>
                              <th><?=_("모바일")?></th>
                              <td>
                              <input type="text" class="form-control" name="orderer_mobile" value="<?=$pay[orderer_mobile]?>">
                              <!--<a href="javascript:;" onclick="popup('../member/sms_popup.php?mobile=<?=$pay[orderer_mobile]?>',800,600)">문자전송</a>-->
                              <button type="button" class="btn btn-xs btn-inverse" onclick="popup('../member/sms_popup.php?mobile=<?=$pay[orderer_mobile]?>',800,600)"><?=_("문자전송")?></button>
                              </td>
                           </tr>
                           <tr>
                              <th><?=_("연락처")?></th>
                              <td><input type="text" class="form-control" name="orderer_phone" value="<?=$pay[orderer_phone]?>"></td>
                           </tr>
                           <tr>
                              <th><?=_("이메일")?></th>
                              <td>
                              <input type="text" class="form-control" name="orderer_email" value="<?=$pay[orderer_email]?>">
                              </td>
                           </tr>
                           <? if ($mem_etc) { ?>
                           <tr>
                              <th><?=_("회원추가정보")?></th>
                              <td>
                              <?=$mem_etc;?>
                              </td>
                           </tr>
                           <? } ?>
                        </table>
                     </div>
                  </div>

                  <div class="panel panel-inverse">
                     <div class="panel-heading">
                        <h4 class="panel-title"><?=_("배송지정보")?></h4>
                     </div>

                     <div class="panel-body">
                        <table class="table table-bordered form-inline">
                           <tr>
                              <th width="150"><?=_("받으시는 분")?></th>
                              <td><input type="text" class="form-control" name="receiver_name" value="<?=$pay[receiver_name]?>"></td>
                           </tr>
                           <tr>
                              <th><?=_("연락처")?></th>
                              <td>
                                 <input type="text" class="form-control" name="receiver_phone" value="<?=$pay[receiver_phone]?>"> 
                                 <input type="text" class="form-control" name="receiver_mobile" value="<?=$pay[receiver_mobile]?>">
                              </td>
                           </tr>
                           <tr>
                              <th><?=_("주소")?></th>
                              <td>
                                 <input type=text class="form-control" name="zipcode" id="zipcode" value="<?=$pay[receiver_zipcode]?>" size="5" readonly>
                                 <button type="button" class="btn btn-xs btn-inverse" onclick="javascript:popupZipcode<?=$language_locale?>()"><?=_("우편번호")?></button>
                                 <br>
                                 <input type="text" class="form-control" name="address" value="<?=$pay[receiver_addr]?>" size="100"><br>
                                 <input type="text" class="form-control" name="address_sub" value="<?=$pay[receiver_addr_sub]?>" size="100">
                              </td>
                           </tr>
                           <tr>
                              <th><?=_("추가메모")?></th>
                              <td><input type="text" class="form-control" name="request2" value="<?=$pay[request2]?>" size="100"></td>
                           </tr>
                           <tr>
                              <th><?=_("배송시메모")?></th>
                              <td><input type="text" class="form-control" name="request" value="<?=$pay[request]?>" size="100"></td>
                           </tr>
                           <tr>
                              <th><?=_("관리자메모")?></th>
                              <td><textarea class="form-control" name="memo" cols="100" rows="5"><?=$pay[memo]?></textarea></td>
                           </tr>
                           <tr>
                              <th><?=_("반려사유")?></th>
                              <td>
                                 <textarea class="form-control" name="return_msg" cols="100" rows="5"><?=$pay[return_msg]?></textarea>
                                 <div class="desc red"><?=_("신용거래의 반려상태가 되었을 시에 사용자에게 보여질 메세지 입니다.")?></div>
                              </td>
                           </tr>
                        </table>
                        <div>
                            <button type="submit" class="btn btn-xs btn-inverse"><?=_("주문자/배송지 정보 수정")?></button>
                        </div>
                     </div>
                  </div>
               </form>
               
              <div>
                 <!--<button type="button" class="btn btn-sm btn-danger" onclick="_refund_step()"><?=_("주문 넘기기")?></button>-->
                 <button type="button" class="btn btn-sm btn-default" onclick="popup('order_detail_popup_print.php?payno=<?=$pay[payno]?>',800,750,1,'','printwin')"><?=_("주문서 인쇄")?></button>
              </div>               
            </div>
         </div>
      </div>
   </div>
</div>

<script>
function _refund_step() {
   var fm = document.fm_item;
   var chk = false;
   var c = document.getElementsByName('chk[]');

   for (var i = 0; i < c.length; i++) {
      if (c[i].checked) {
         chk = true;
      }
   }
   if(chk){
      if(confirm('<?=_("이 기능은 블루팟의 주문상태만 변경하며")?>\r<?=_("PODstation, OASIS에는 어떠한 영향도 미치지 않습니다.")?>\r<?=_("계속 하시겠습니까?")?>') == true) {
         $j(fm).attr("action", "indb.php");
         //$j(fm).attr("target", "popupLayerFrame");
         fm.submit();
      }
   } else alert('<?=_("주문을 선택해주세요.")?>');
   //popupLayer('');
}

function _refund(){
    var fm = document.fm_item;
    if (!form_chk(fm)) return;
    popupLayer('');
    $j(fm).attr("action","order_refund_popup.php");
    $j(fm).attr("target","popupLayerFrame");
    fm.submit();
}

function orderCancel(payno) {
    if(confirm('<?=_("이 기능은 블루팟의 주문상태만 변경하며")?>\r<?=_("결제서비스(PG사), PODstation, OASIS에는 어떠한 영향도 미치지 않습니다.")?>\r\r<?=_("정말 주문을 취소하시겠습니까?")?>\r<?=_("취소된주문은 복구가 불가능합니다.")?>') == true) {
        location.href = 'indb.php?mode=step1to-9&payno='+payno;
    }
}
</script>

<? include_once "../_pfooter.php"; ?>