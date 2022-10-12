<?
//날짜 버튼 클릭 후 조회시 버튼 색상 변경
if (!$_POST[date_buton_type]) $_POST[date_buton_type] = "week";
$button_color = array("yesterday" => "inverse","today" => "inverse","tdays" => "inverse","week" => "inverse","month" => "inverse","all" => "inverse");
if ($_POST[date_buton_type]) {
   $button_color[$_POST[date_buton_type]] = "warning";
}

$selected[bid][$_POST[bid]] = "selected";
$selected[manager_inspection][$_POST[manager_inspection]] = "selected";

$m_mall = new M_mall();
$m_pretty = new M_pretty();
$mall_data = $m_mall -> getInfo($cid);

$count[0] = $m_pretty -> getListCount_modern($cid, "and paystep != 0", "", "Y");

$count[1] = $m_pretty -> getListCount_modern($cid, "and paystep = 1", "", "Y");
$count[2] = $m_pretty -> getListCount_modern($cid, "and itemstep = 2", "Y");

$count[91] = $m_pretty -> getListCount_modern($cid, "and paystep = 91 and paymethod = 't'");
$count[92] = $m_pretty -> getListCount_modern($cid, "and itemstep = 92 and paymethod = 't'", "Y");

$count[3] = $m_pretty -> getListCount_modern($cid, "and itemstep = 3", "Y");
$count[4] = $m_pretty -> getListCount_modern($cid, "and itemstep = 4", "Y");
$count[5] = $m_pretty -> getListCount_modern($cid, "and itemstep = 5", "Y");

if ($_GET[itemstep]) {
   if ($_GET[itemstep] == "1") {
      $pannel_color = "danger";
      $pannel_title = _("무통장-미입금");
      $pannel_sub_title = "<h1 class=\"page-header\">"._("무통장-미입금")." <small>"._("무통장 미입금 목록")."</small></h1>";
   } else if ($_GET[itemstep] == "2") {
      $pannel_color = "inverse";
      $pannel_title = _("무통장-입금완료");
      $pannel_sub_title = "<h1 class=\"page-header\">"._("무통장-입금완료")." <small>"._("무통장 입금완료 목록")."</small></h1>";
   } else if ($_GET[itemstep] == "91") {
      $pannel_color = "primary";
      $pannel_title = _("신용거래-승인요청");
      $pannel_sub_title = "<h1 class=\"page-header\">"._("신용거래-승인요청")." <small>"._("신용거래 승인요청 목록")."</small></h1>";
   } else if ($_GET[itemstep] == "92") {
      $pannel_color = "success";
      $pannel_title = _("신용거래-승인완료");
      $pannel_sub_title = "<h1 class=\"page-header\">"._("신용거래-승인완료")." <small>"._("신용거래 승인완료 목록")."</small></h1>";
   } else if ($_GET[itemstep] == "03") {
      $pannel_color = "info";
      $pannel_title = _("제작중");
      $pannel_sub_title = "<h1 class=\"page-header\">"._("제작중")." <small>"._("제작진행 목록")."</small></h1>";
   } else if ($_GET[itemstep] == "04") {
      $pannel_color = "purple";
      $pannel_title = _("발송대기");
      $pannel_sub_title = "<h1 class=\"page-header\">"._("발송대기")." <small>"._("발송대기 목록")."</small></h1>";
   } else if ($_GET[itemstep] == "05") {
      $pannel_color = "danger";
      $pannel_title = _("발송완료");
      $pannel_sub_title = "<h1 class=\"page-header\">"._("발송완료")." <small>"._("발송완료 목록")."</small></h1>";
   }
} else {
   $pannel_color = "danger";
   $pannel_title = _("통합목록");
   $pannel_sub_title = "<h1 class=\"page-header\">"._("통합목록")." <small>"._("주문 통합 목록")."</small></h1>";
}
?>


<ol class="breadcrumb pull-right">
   <li>
      <a href="javascript:;">Home</a>
   </li>
   <li class="active"><?=$pannel_title?></li>
</ol>
<?=$pannel_sub_title?>

<div class="row">
   <div class="col-md-12 col-sm-6">
      <div class="widget widget-state bg-aqua-darker">
         <div class="btn-group pull-right">
            <button type="button" class="btn btn-sm btn-info" onclick="location.href='/k/order/order_list.php?itemstep=2';"><?=_("무통장-입금완료")?></button>
            <button type="button" class="btn btn-sm btn-info" onclick="location.href='/k/order/order_list.php?itemstep=92';"><?=_("신용거래-승인완료")?></button>
         </div>
      </div>
   </div>
</div>

<? setAdminIncudeFile("order_list_header_status"); ?>