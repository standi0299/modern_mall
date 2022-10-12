<?
include "../lib.php";

$m_order = new M_order();

$order_column_arr = array("regist_date","account_flag", "account_point", "account_point", "mall_point", "m_name", "payno");
$order_data = $_POST[order][0];
$orderby = " order by " .$order_column_arr[$order_data[column]] . " " .$order_data[dir];

$addWhere = "";

$postData = json_decode(base64_decode($_GET[postData]),1);

if($postData) {
   if($postData[start]) $addWhere .= " and a.updatedt > '{$postData[start]}'";
   if($postData[end]) $addWhere .= " and a.updatedt < adddate('{$postData[end]}',interval 1 day)";
   if($postData[state] != null) $addWhere .= " and a.state = '$postData[state]'";
   
   if($postData[catno]){
      if (is_numeric($postData[catno])){
         $addWhere .= " and e.catno like '$postData[catno]%'";
      }
   }
}

$search_data = $_POST[search][value];
if ($search_data) {
   $addWhere .= " and (storageid like '%$search_data%' or goodsnm like '%$search_data%' or concat(a.mid,b.name) like '%$search_data%')";
}
       
$list = $m_order->getEditList($cid, $addWhere);

$totalCnt = $m_order->getEditListCnt($cid, $addWhere); 

$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($list as $key => $value) {

   if ($value[goodsno]=="-1"){
      $value[goodsnm]  = "자동견적상품 : ".$value[est_goodsnm];
   }

   $value[addopt] = array();
   if ($value[addoptno]){
      $value[addoptno] = explode(",",$value[addoptno]);
      foreach ($value[addoptno] as $k=>$v){

         $query = "
         select
            a.*,
            if(b.addopt_aprice is null,a.addopt_aprice,b.addopt_aprice) addopt_aprice,
            addopt_areserve,
            c.*
         from
            exm_goods_addopt a
            left join exm_goods_addopt_price b on b.cid = '$cid' and a.addoptno = b.addoptno
            inner join exm_goods_addopt_bundle c on a.goodsno = c.goodsno and a.addopt_bundle_no = c.addopt_bundle_no
         where
            a.addoptno = '$v'
         ";
         $tmp = $db->fetch($query);

         if (!$tmp[goodsno]) $value[error] = 4;
         else $value[addopt][] = $tmp;
      }
   }
   $value[_hidelog] = explode("/",$value[_hidelog]);
   $value[_hidelog] = array_map("trim",$value[_hidelog]);
   $value[_hidelog] = implode("/",$value[_hidelog]);

   $pdata = array();

   $pdata[] = ($value[goodsno] >= 0) ? $value[goodsno] : "자동견적";
   
   $goodsno_field = "
      <table>
      <tr>
      <td>
      <a href=\"../../goods/view.php?goodsno=$value[goodsno]\" target=\"_blank\">".goodsListImg($value[goodsno],50,"border:1px solid #CCCCCC",$cid)."</a>
      </td>
      <td>
      <div>".$value[goodsnm]." <span class=\"gray small\">".$value[title]."</span></div>
      ";
      if ($value[est_order_option_desc]) { 
         $goodsno_field.= "<div>견적옵션 : ".$value[est_order_option_desc]."</div>";
      }
      
      if ($value[opt1]) {
         $goodsno_field.= "<div>옵션 : ".$value[opt1];
         if($value[opt2]) {
             $goodsno_field.= "/". $value[opt2];
         }
         $goodsno_field.= "</div>";
      }

      foreach ($value[addopt] as $k=>$v) {
         $goodsno_field.= "<div>추가옵션 - ".$v[addopt_bundle_name]." : ".$v[addoptnm]."</div>";
      }

      $goodsno_field.= "
         <div><u onclick=\"popup('../../module/preview.php?goodsno=$value[goodsno]&storageid=$value[storageid]',600,400)\">편집내용 미리보기</u>
         <u onclick=\"popupLayer('../../module/popup_calleditor.php?mode=edit&goodsno=$value[goodsno]&productid=$value[podsno]&optionid=$value[podoptno]&addopt=$value[addoptno]&storageid=$value[storageid]');\">편집하기</u>
         </div>
         </td>
         </tr>
         </table>
      ";

   $pdata[] = $goodsno_field;

   if ($value[mid] && $value[mid]!="admin") {
      $pdata[] = "<a href=\"javascript:;\" onclick=\"popup('../member/popup_member_detail.php?mid=$value[mid]',800,700)\"><b>".$value[name]."</b><br>(".$value[mid].")</a>";
   } else {
      $member_field= $value[orderer_name];

      if($value[mid] == "admin") {
         $member_field .= $value[mid];
      }
      
      if(!$value[mid]) {
         $member_field .= "비회원";
      }
      $pdata[] = $member_field;
   }

   $pdata[] = $r_state[$value[state]]."<div class=\"stxt\">".$value[storageid]."</div>";

   $pdata[] = $value[updatedt];

   $log_field = "<div style=\"position:relative\">";


   if($value[_hide]) {
      $log_field .= "삭제<div><span>삭제로그</span></div><div>".$value[_hidelog]."</div>";
   } else {
      $log_field .= "보존";
   }
   
   $log_field .= "</div>";

   $pdata[] = $log_field;


   if($value[_hide]) {
      $delete_field = "<a href=\"indb.php?mode=set_hide&hide=0&storageid=$value[storageid]\" action=\"restore\">복구하기</a>";
   } else {
      $delete_field = "<a href=\"indb.php?mode=set_hide&hide=1&storageid=$value[storageid]\" action=\"del\">삭제하기</a>";
   }

   $pdata[] = $delete_field;
   $psublist[] = $pdata;
}

$plist[data] = $psublist;

echo json_encode($plist)
?>