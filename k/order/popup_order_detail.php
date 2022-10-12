<?
include_once "../_pheader.php";
$m_pretty = new M_pretty();
$m_member= new M_member();
$m_order= new M_order();

$data = $m_pretty -> getOrderDataDetail($cid, $_GET[mid], $_GET[payno]);

$memberData = $m_member -> getInfo($cid, $_GET[mid]);
$payData = $m_order -> getPayInfo($_GET[payno]);

//debug($memberData);
if($data) {
   foreach($data as $key => $value){
      $list[$value[payno]][$value[goodsnm]][] = $value;
      $point += $value[pretty_point];
   }
} else {
   msg(_("주문정보가 존재하지 않습니다."), "close");
}
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
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("주문정보")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel panel-info">
            <div class="panel-heading">
               <h4 class="panel-title"><?=_("주문정보")?></h4>
            </div>
         </div>
         
         <div class="panel-body panel-form">
            <div id="ord_box">
               <dl class="dl-horizontal">
                  <dt><?=_("유치원(단체)이름")?></dt>
                  <dd><?=$memberData[name]?></dd>
                  <dt><?=_("대표관리자 아이디")?></dt>
                  <dd><?=$memberData[mid]?></dd>
                  <dt><?=_("전화번호")?></dt>
                  <dd><?=$payData[receiver_phone]?></dd>
                  <dt><?=_("핸드폰번호")?></dt>
                  <dd><?=$payData[receiver_mobile]?></dd>
                  <dt><?=_("우편번호")?></dt>
                  <dd><?=$payData[receiver_zipcode]?></dd>
                  <dt><?=_("배송주소")?></dt>
                  <dd><?=$payData[receiver_addr]?><?=$payData[receiver_addr_sub]?></dd>
                  <dt><?=_("추가메모")?></dt>
                  <dd><?=$payData[request2]?></dd>
                  <dt><?=_("배송메모")?></dt>
                  <dd><?=$payData[request]?></dd>
               </dl>
                 
               <table class="table table-striped table-bordered">
                  <thead>
                     <tr class="info">
                        <th><?=_("주문번호")?><br><br><br></th>
                        <th><?=_("주문일")?><br><?=_("상품명")?><br><?=_("반(그룹)")?></th>
                        <th><?=_("마스터템플릿")?><br><?=_("원아(멤버)")?></th>
                        <th><?=_("주문정보")?><br><?=_("단가")?></th>
                        <th><?=_("수량")?></th>
                        <th><?=_("합계")?></th>
                        <th><?=_("미리보기")?></th>
                        <th><?=_("제작상태")?></th>
                        <th><?=_("포인트")?></th>
                     </tr>
                  </thead>
                  <tbody>
                     <? foreach($list as $key => $value) { ?>
                     <tr class="active">
                        <th><?=$key?></th>
                        <th colspan="2"><?=$m_pretty->getPrettyOrderDetailData($key, 'orddt')?></th>
                        <th colspan="6">
                           <span style="color: red"><?=_("상품수")?> : <?=$m_pretty->getPrettyOrderDetailData($key,'cnt')?><?=_("개")?> / <?=_("상품금액")?> : <?=number_format($m_pretty->getPrettyOrderDetailData($key,'saleprice'))?><?=_("원")?> / <?=_("배송비")?> : <?=number_format($m_pretty->getPrettyOrderDetailData($key,'shipprice'))?><?=_("원")?> / <?=_("합계 금액")?> : <?=number_format($m_pretty->getPrettyOrderDetailData($key,'payprice'))?><?=_("원")?></span>
                        </th>
                     </tr>
                     <? foreach($value as $gKey => $gValue) { ?>
                     <tr class="success">
                        <th></th>
                        <th><?=$gKey?></th>
                        <th><?=$m_pretty->getPrettyOrderDetailData($gKey, 'title')?></th>
                        <th colspan="6"></th>
                     </tr>
                        <? foreach($gValue as $cKey => $cValue) { ?>
                        <tr class="warning">
                           <td></td>
                           <td><?=$cValue[class_name]?></td>
                           <td><?=$cValue[child_name]?></td>
                           <td><?=number_format($cValue[saleprice]/$cValue[ea])?><?=_("원")?></td>
                           <td><?=number_format($cValue[ea])?><?=_("개")?></td>
                           <td><?=number_format($cValue[saleprice])?><?=_("원")?></td>
                           <td style="text-align: center;"><button type="button" onclick="window.open('../../module/preview.php?goodsno=<?=$cValue[goodsno]?>&storageid=<?=$cValue[storageid]?>','','width=910,height=537,scrollbars=1');" class="btn btn-success btn-xs"><i class="fa fa-search-plus"></i></button></td>
                           <td><?=$r_step[$cValue[itemstep]]?></td>
                           <td><?=number_format($cValue[pretty_point])?></td>
                        </tr>
                     <? } } } ?>
                     <tr>
                        <td colspan="9" style="text-align: right">
                           <?=_("포인트")?> : <?=number_format($point)?> Point
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>  

<? include_once "../_pfooter.php"; ?>