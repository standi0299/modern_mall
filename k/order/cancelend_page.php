<?
include "../lib.php";
$this_time = get_time();
$debug_data .= debug_time($this_time);
$m_pretty = new M_pretty();
## 회원그룹 추출
$r_grp = getMemberGrp();
$r_bid = getBusiness();

$r_rid = get_release();

$m_order = new M_order();

### 결제방법색상
$r_paymethod_color = array(
   'c' => 'red',
   'b' => 'blue',
   'e' => 'green',
   'v' => 'sky',
   'o' => 'orange',
   'h' => 'pink'
);

$postData = json_decode(base64_decode($_GET[postData]),1);

if($postData) {
   if($postData[start]) $addWhere .= " and orddt > '{$postData[start]}'";
   if($postData[end]) $addWhere .= " and orddt < adddate('{$postData[end]}',interval 1 day)";
}

$search_data = $_POST[search][value];
if ($search_data) {
   $addWhere .= " and (payno like '%$search_data%')";
}

list($totalCnt) = $db->fetch("select count(payno) from exm_pay where cid = '$cid' and paystep = '-9' $addWhere",1);

$data = $db->listArray("select * from exm_pay 
               where cid = '$cid' and paystep = '-9' $addWhere order by orddt desc limit $_POST[start], $_POST[length]");

foreach($data as $k => $v){
   $data[$k][ordno] = $db->listArray("select * from exm_ord where payno = $v[payno]");
}

foreach($data as $k => $v){
   foreach($v[ordno] as $kk => $vv){
      $orditem_data = $db->listArray("select * from exm_ord_item where payno = $v[payno] and ordno = $vv[ordno]");
      $data[$k][ordno][$kk][orditem] = $orditem_data;
      
      foreach($orditem_data as $kkk => $vvv){
         $etc_array[$etc_key][listImg] = goodsListImg($vvv[goodsno],30,'',"border:1px solid #CCCCCC",$cid);
      }
   }
}
$debug_data .= debug_time($this_time);
//list($totalCnt) = $db->fetch("select count(a.payno) from exm_pay a where a.cid = '$cid' $addWhere and paystep != 0",1);
$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($data as $key => $value) {
   $pdata = array();

   if($value[paymethod]=="t") $confirm_date = substr($value[confirmdt],0,10);
   else $confirm_date = substr($value[paydt],0,10);

   $pdata[] = "
   <a href=\"javascript:;\" onclick=\"popup('order_detail_popup.php?payno=$value[payno]',1200,750)\"><b>$value[payno]</b></a><br>".
   _("주문일")." : ".substr($value[orddt],0,10)."<br>".
   _("입금일")." : ".substr($value[paydt],0,10)."<br>
   <span class=\"".$r_paymethod_color[$value[paymethod]]."\">".$r_paymethod[$value[paymethod]]."</span><br>".
   _("결제금액")." : <b>".number_format($value[payprice])."</b><br>".
   _("적립금사용")." : ".number_format($value[dc_emoney])."<br>".
   _("주문자")." : $value[orderer_name](<b>$value[mid]</b>)<br>".
   _("수령자")." : $value[receiver_name]<br>".
   _("입금자")." : $value[payer_name]
   ";

   $data_table = "<table>";
            foreach($value[ordno] as $k => $v){
   $data_table .=  "<tr align=\"center\">
               <td>
                  <b>$value[payno]_$v[ordno]</b>
                  <div>"._("출고처")." : ".$r_rid[$v[rid]]."</div>
                  <div>
                     <span>"._("배송비")."</span> : <b class=\"gray\">";
                     if ($v[shipprice]) {
                        $data_table .= "<span>".number_format($v[shipprice])."</span>";
                     } else {
                        $data_table .= "<span>"._("무료")."</span>";
                        //주문 배송 정보 출력 추가      20141202    chunter
                        if ($v[order_shiptype] != "1" && $v[order_shiptype] != "") {
                           $data_table .= "<BR><span class=\"green\">".$r_order_shiptype[$v[order_shiptype]]."</span>";
                        }
                     }
   $data_table .= "</b>
                  </div>
               </td>
               <td>
                  <table>";

                  foreach($v[orditem] as $kk => $vv){
                     $vv[addopt] = unserialize($vv[addopt]); $vv[printopt] = unserialize($vv[printopt]);
                     $orditem_key = $vv[payno]."_".$vv[ordno]."_".$vv[ordseq];
      $data_table .= "<tr>
                        <td><a href=\"../../goods/view.php?goodsno=$vv[goodsno]\" target=\"_blank\">".$etc_array[$orditem_key][listImg]."</a></td>
                        <td>
                           ";
                           $data_table .= "<div>$vv[goodsnm]</div>";

                           if ($vv[opt]) {
                              $data_table .= "<div class=\"blue\">$vv[opt] <span>(+".number_format($vv[aprice]).")</span></div>";
                           }

                           if ($vv[addopt]) foreach ($vv[addopt] as $v) {
                              $data_table .= "<div>$v[addopt_bundle_name]:$v[addoptnm] <span>(+".number_format($v[addopt_aprice]).")</span></div>";
                           }

                           if ($vv[printopt]) foreach ($vv[printopt] as $v) {
                              $data_table .= "<div>$v[printoptnm]:$v[ea] <span>(+".number_format($v[print_price]).")</span></div>";
                           }

                           if ($vv[addpage_aprice]) {
                              $data_table .= "<div>"._("추가페이지").":$vv[addpage] <span>(+".number_format($vv[addpage_aprice]).")</span></div>";
                           }

                           if ($vv[dc_member]) {
                              $data_table .= "<div>"._("회원할인")." : (-".number_format($vv[dc_member]).")</div>";
                           }

        $data_table .= "</td>
                        <td align=\"right\">
                        (";
                           $data_table .= number_format($vv[goods_price]);
                           if ($vv[aprice]) $data_table .= " + ".number_format($vv[aprice]);
                           if ($vv[addopt_aprice]) $data_table .= " + ".number_format($vv[addopt_aprice]);
                           if ($vv[print_aprice]) $data_table .= " + ".number_format($vv[print_aprice]);
                           if ($vv[addpage_aprice]) $data_table .= " + ".number_format($vv[addpage_aprice]);
                           if ($vv[dc_member]) $data_table .= " - ".number_format($vv[dc_member]);
         $data_table .= ")
                        X $vv[ea]";

                        if ($vv[dc_coupon]) $data_table .= "- ".number_format($vv[dc_coupon]);
       $data_table .= "</td>

                        <td>=</td>
                        <td align=\"right\">
                        <b>".
                        number_format($vv[payprice]);
        $data_table .= "</b>
                        </td>
                        <td align=\"center\">
                        ";
                        $r_step[$vv[itemstep]];
                        if($value[paymethod] == 'd') {
                           if($vv[itemstep] == '1' || $vv[itemstep] == '2' || $vv[itemstep] == '91' || $vv[itemstep] == '92') {
                              "<a href=\"javascript:ordCancle('$value[mid]', '$vv[payno]', '$vv[ordno]', '$vv[ordseq]');\">"._("주문취소")."</a>";
                           }
                        }

                        if ($vv[storageid]) {
                           $data_table .= $r_step[$vv[itemstep]];
                        }
        $data_table .= "</td>
                    </tr>
                    ";
                    }
  $data_table .= "</table>
               </td>
            </tr>";
            }
   $data_table .= "</table>";

   $pdata[] = $data_table;
   $psublist[] = $pdata;
   $debug_data .= debug_time($this_time);
}
//echo"$debug_data";
$plist[data] = $psublist;
echo json_encode($plist)
?>