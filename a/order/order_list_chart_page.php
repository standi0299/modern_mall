<?

/*
* @date : 20180316
* @author : kdk
* @brief : 주문리스트 총 결제금액 추가
* @request :
* @desc : getOrdItemSumPayPrice() 추가.
* @todo : 처음 쿼리에서 처리가 가능해 보임 추후 개선필요.
*/

include "../lib.php";
$this_time = get_time();

$m_pretty = new M_pretty();

$debug_data .= debug_time($this_time);


## 회원그룹 추출
$r_grp = getMemberGrp();
$r_bid = getBusiness();

### 출고처 추출
$r_rid["|self|"] = _("자체출고");
$r_rid_x = get_release();
$r_rid = array_merge($r_rid,$r_rid_x);

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

$getData = json_decode(base64_decode($_GET[postData]),1);
$debug_data .= debug_time($this_time);
//debug($getData);
if(!$getData[step]) {
    $addWhere = " and paystep != 0 and paystep != -1";
}

if($getData) {
   if($getData[s_search]){
      $getData[s_search] = trim($getData[s_search]);
	   //검색어에 수령자(a.receiver_name) 추가 210416 jtkim
      $addWhere .= " and concat(a.payno, c.goodsno, c.goodsnm, a.mid, a.orderer_name, a.payer_name, a.receiver_name) like '%$getData[s_search]%'";
   }

    if($getData[orddt_start]) $addWhere .= " and a.orddt > '{$getData[orddt_start]}'";
    if($getData[orddt_end]) $addWhere .= " and a.orddt < adddate('{$getData[orddt_end]}',interval 1 day)";

    if($getData[paydt_start]) $addWhere .= " and (a.paydt > '{$getData[paydt_start]}' || a.confirmdt > '{$getData[paydt_start]}')";
    if($getData[paydt_end]) $addWhere .= " and (a.paydt < adddate('{$getData[paydt_end]}',interval 1 day) || a.confirmdt < adddate('{$getData[paydt_end]}',interval 1 day))";

    if($getData[shipdt_start]) $addWhere .= " and (select ordseq from exm_ord_item where payno = a.payno and shipdt >= '{$getData[shipdt_start]}' limit 1) > 0";
    if($getData[shipdt_end]) $addWhere .= " and (select ordseq from exm_ord_item where payno = a.payno and shipdt < adddate('{$getData[shipdt_end]}',interval 1 day)+0  limit 1) > 0";

    if ($getData[paymethod]){
        $paymethod = implode("','",$getData[paymethod]);
        $addWhere .= " and (select ordseq from exm_ord_item where payno = a.payno and paymethod in ('$paymethod') limit 1) > 0";
    }

    if ($getData[step]){
        $step = implode(",",$getData[step]);
        $addWhere .= " and (select ordseq from exm_ord_item where payno = a.payno and itemstep in ($step) limit 1) > 0";
    }

    if($getData[itemstep]){
        if($getData[itemstep] == 91 || $getData[itemstep] == 92) {
            $addWhere .= " and a.paymethod = 't'";
        }
        $addWhere .= " and c.itemstep = '$getData[itemstep]'";
    }

    if (is_numeric($getData[pods_trans])){
        $addWhere .= " and (select ordseq from exm_ord_item where payno = a.payno and storageid != '' and pods_trans = '$getData[pods_trans]' limit 1) > 0";
    }
}
//debug($addWhere);
$debug_data .= debug_time($this_time);
/*
if(!$getData[itemstep] || $getData[itemstep] == 1 || $getData[itemstep] == 91){
   if(!$getData[itemstep]) $stepWhere = "and paystep != 0";
   else $stepWhere = "and paystep = $getData[itemstep]";

   $query = "select * from exm_pay a
            where a.cid = '$cid' $stepWhere $addWhere order by a.orddt desc limit $_POST[start], $_POST[length]";

   list($totalCnt) = $db->fetch("select count(a.payno) from exm_pay a where a.cid = '$cid' $stepWhere $addWhere",1);
} else {

   $query = "select * from exm_pay a
            inner join exm_ord b on a.payno = b.payno
            inner join exm_ord_item c on a.payno = c.payno and b.ordno = c.ordno
            where a.cid = '$cid' and paystep != 0 $addWhere $addItemstep group by a.payno order by orddt desc  limit $_POST[start], $_POST[length]";

   $cnt_query = $db->listArray("select a.payno from exm_pay a
                                 inner join exm_ord b on a.payno = b.payno
                                 inner join exm_ord_item c on a.payno = c.payno and b.ordno = c.ordno
                                 where a.cid = '$cid' and paystep != 0 $addWhere $addItemstep group by a.payno",1);
   $totalCnt = count($cnt_query);
}

$query = "select * from exm_pay a
        inner join exm_ord b on a.payno = b.payno
        inner join exm_ord_item c on a.payno = c.payno and b.ordno = c.ordno
        where a.cid = '$cid' and paystep != 0 $addWhere group by a.payno order by orddt desc  limit $_POST[start], $_POST[length]";
//debug($query);
$cnt_query = $db->listArray("select a.payno from exm_pay a
                             inner join exm_ord b on a.payno = b.payno
                             inner join exm_ord_item c on a.payno = c.payno and b.ordno = c.ordno
                             where a.cid = '$cid' and paystep != 0 $addWhere group by a.payno",1);
//$totalCnt = count($cnt_query);
//$data = $db->listArray($query);
*/

$limit = "limit $_POST[start], $_POST[length]";
$totalCnt = count($m_order->getOrderList($cid, $addWhere, '', TRUE));
$data = $m_order->getOrderList($cid, $addWhere, $limit);

$debug_data .= debug_time($this_time);
foreach($data as $k => $v){
   $data[$k][ordno] = $db->listArray("select * from exm_ord where payno = $v[payno]");
}
$debug_data .= debug_time($this_time);
foreach($data as $k => $v){
   foreach($v[ordno] as $kk => $vv){
      $orditem_data = $db->listArray("select * from exm_ord_item where payno = $v[payno] and ordno = $vv[ordno] $addItemstep");
      $data[$k][ordno][$kk][orditem] = $orditem_data;

      foreach($orditem_data as $kkk => $vvv){
         $query = "select podsno,podoptno from exm_goods_opt where goodsno = '$vvv[goodsno]' and optno = '$vvv[optno]'";
         list ($vv[podsno],$vv[podoptno]) = $db->fetch($query,1);

         $etc_key = $v[payno]."_".$vv[ordno]."_".$vvv[ordseq];
         $etc_array[$etc_key][podsno] = $vv[podsno];
         $etc_array[$etc_key][podoptno] = $vv[podoptno];

         if (!$etc_array[$etc_key][podsno]){
            $query = "select podsno,pods_use,podskind from exm_goods where goodsno = '$vvv[goodsno]'";
            list ($vv[podsno],$vv[pods_use],$vv[podskind]) = $db->fetch($query,1);

            $etc_array[$etc_key][podsno] = $vv[podsno];
            $etc_array[$etc_key][pods_use] = $vv[pods_use];
            $etc_array[$etc_key][podskind] = $vv[podskind];

         } else {
            $query = "select pods_use,podskind from exm_goods where goodsno = '$vvv[goodsno]'";
            list ($vv[pods_use],$vv[podskind]) = $db->fetch($query,1);

            $etc_array[$etc_key][pods_use] = $vv[pods_use];
            $etc_array[$etc_key][podskind] = $vv[podskind];
         }

         if (!$etc_array[$etc_key][podoptno]) $etc_array[$etc_key][podoptno] = 1;

         //주문제목 추가 2014.10.22 by kdk
         if($vvv[storageid]) {
            //아래에 같은 쿼리가 있어서 합침
            //$query = "select title from exm_edit where storageid = '$vv[storageid]'";
            //list ($vv[title]) = $db->fetch($query,1);

            //복수 편집기 처리 pods_use, podskind, podsno 장바구니 편집기 호출 함수 수정 2016.05.20 by kdk
            $query = "select pods_use,podsno,podskind,title from exm_edit where storageid = '$vvv[storageid]'";
            list ($temp[pods_use],$temp[podsno],$temp[podskind],$etc_array[$etc_key][title]) = $db->fetch($query,1);
            //debug($temp);

            if($temp[pods_use]) $etc_array[$etc_key][pods_use] = $temp[pods_use];
            if($temp[podsno]) $etc_array[$etc_key][podsno] = $temp[podsno];
            if($temp[podskind]) $etc_array[$etc_key][podskind] = $temp[podskind];
         }

         //$etc_array[$etc_key][listImg] = goodsListImg($vvv[goodsno],30,'',"border:1px solid #CCCCCC",$cid);
      }
   }
}
$debug_data .= debug_time($this_time);
//list($totalCnt) = $db->fetch("select count(a.payno) from exm_pay a where a.cid = '$cid' $addWhere and paystep != 0",1);
$plist = array();
$plist[recordsTotal] = $totalCnt;
$plist[recordsFiltered] = $totalCnt;

foreach ($data as $key => $value) {
   $debug_data .= debug_time($this_time);
   $pdata = array();

   $totalea = $m_order->getOrdItemSumEa($value[payno], $getData[itemstep]);
   //$totalprice = $m_order->getOrdItemSumPayPrice($value[payno], $getData[itemstep]);

   if($value[paymethod]=="t") {
       $confirm_date = substr($value[confirmdt],0,10);
       $confirm_title = "확정일";
   }
   else {
       $confirm_date = substr($value[paydt],0,10);
       $confirm_title = "결제일";
   }

   if($getData[itemstep] && $getData[itemstep] != 5)
      $pdata[] = "<input type=\"checkbox\" name=\"chk[]\" value=\"$value[payno]\"/>";

     if ($value[mid]) {
         $info_member = $value[mid];
     } else {
         $info_member = _("비회원");
     }

		//모바일 주문 구분. 스킨에 따라 모바일 주문을 구분한다.			181214		chunter
		if ($cfg[admin_order_web_app] == "Y")
		{
			$order_skin = " <font color='blue'><b>[Web]</b></font>";
			if ($value['order_skin'] == "m_default") $order_skin = " <font color='red'><b>[App]</b></font>";
		}

   $pdata[] = "
   <a href=\"javascript:;\" onclick=\"popup('order_detail_popup.php?payno=$value[payno]',1200,750)\"><b>$value[payno]</b></a>$order_skin<br>
   TOTAL : $totalea[totea]<br>".
   _("주문일")." : ".substr($value[orddt],0,10)."<br>".
   $confirm_title." : ".$confirm_date."<br>
   <span class=\"".$r_paymethod_color[$value[paymethod]]."\">".$r_paymethod[$value[paymethod]]."</span><br>".
   //_("결제금액")." : <b>".number_format($totalprice[totpayprice])."</b><br>".
   _("결제금액")." : <b>".number_format($value[totalprice])."</b><br>".
   _("사용적립금")." : ".number_format($value[dc_emoney])."<br>".
   _("주문자")." : $value[orderer_name](<b>$info_member</b>)<br>".
   _("수령자")." : $value[receiver_name]<br>".
   _("입금자")." : $value[payer_name]
   ";

   $data_table = "<table>";
            foreach($value[ordno] as $k => $v){
   $data_table .=  "<tr align=\"center\">
               <td width=\"100\">
                  <b>$value[payno]_$v[ordno]</b>
                  <div class=\"desc\">"._("출고처")." : ".$r_rid[$v[rid]]."</div>
                  <div class=\"desc\">
                     <span>"._("배송비")."</span> : <b class=\"gray\">";
                     if ($v[shipprice] != 0) {
                        $data_table .= "<br><span class=\"eng\">".number_format($v[shipprice])."</span>";
                     } else {
                        $data_table .= "<span class=\"stxt\">"._("무료")."</span>";
                        //주문 배송 정보 출력 추가      20141202    chunter
                        if ($v[order_shiptype] != "1" && $v[order_shiptype] != "") {
                           $data_table .= "<BR><span class=\"green\">".$r_order_shiptype[$v[order_shiptype]]."</span>";
                        }
                     }
    $ord_item_count = count($v[orditem]);
   $data_table .= "</b>
                  </div>
               </td>
               <td>
                  <table align = \"left\">";

                  foreach($v[orditem] as $kk => $vv){
                     $vv[addopt] = unserialize($vv[addopt]); $vv[printopt] = unserialize($vv[printopt]);
                     $orditem_key = $vv[payno]."_".$vv[ordno]."_".$vv[ordseq];

                     if($vv[ext_json_data])
                        $vv[ext_json_data] = json_decode($vv[ext_json_data],1);

                     if($etc_array[$orditem_key][podsno]) $vv[podsno] = $etc_array[$orditem_key][podsno];
                     if($etc_array[$orditem_key][podoptno]) $vv[podoptno] = $etc_array[$orditem_key][podoptno];
                     if($etc_array[$orditem_key][pods_use]) $vv[pods_use] = $etc_array[$orditem_key][pods_use];
                     if($etc_array[$orditem_key][podskind]) $vv[podskind] = $etc_array[$orditem_key][podskind];
                     if($etc_array[$orditem_key][title]) $vv[title] = $etc_array[$orditem_key][title];

                     if($vv[podskind] == 3055){
                        list($editor_return_json) = $db->fetch("select editor_return_json from tb_editor_ext_data where storage_id = '$vv[storageid]'",1);

                        $editor_return_json = json_decode($editor_return_json,1);
                        $vv[cover_range_id] = $editor_return_json[cover_range_id];
                     }

                     //debug($vv);
      $data_table .= "<tr>
                        <td width=\"30\"><a href=\"../../goods/view.php?goodsno=$vv[goodsno]\" target=\"_blank\">".$etc_array[$orditem_key][listImg]."</a></td>
                        <td width=\"230\">
                           <div>"._("상품번호")." : $vv[goodsno] / ".$r_rid[$vv[item_rid]]."</div>";

                            //패키지상품 여부. / 20180104 / kdk
                            $package_data = "";
                            if($vv[package_order_code]) {
                                $package_data = "<span class=\"orange\">["._("패키지")."]</span>";
                            }

                           $data_table .= "<div>$package_data $vv[goodsnm]</div>";

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

                           if ($vv[title]) {
                              $data_table .= "<div>"._("주문 제목")." - $vv[title]</div>";
                           }

                           if ($vv[storageid] && $vv[est_order_type]!="UPLOAD" && $vv[est_order_type]!="NOFILES" && strpos($vv[storageid], "_temp_") === FALSE) {
                            	$data_table .= "<u onclick=\"popup('../../module/preview.php?goodsno=$vv[goodsno]&storageid=$vv[storageid]',0,0)\" style=\"cursor:pointer\">"._("편집내용 미리보기")."</u>&nbsp;";
							}
                           else if ($vv[storageid] && $vv[est_order_type]=="UPLOAD" && strpos($vv[storageid], "_temp_") === FALSE) {
                               $data_table .= "<u onclick=\"popupLayer('download.est.php?storageid=$vv[storageid]','',600,400)\" style=\"cursor:pointer\">"._("업로드원본")."</u>&nbsp;";
                           }

                           if ($vv[storageid] && !in_array($vv[podskind],array(8,9)) && $vv[est_order_type]!="UPLOAD" && $vv[est_order_type]!="NOFILES" && $vv[pods_use] != '' && $vv[podsno] != '' && strpos($vv[storageid], "_temp_") === FALSE){
                              if ($vv[pods_use]=="3") {
                                 $data_table .= "<u onclick=\"call_wpod('PodsCallEditorUpdate.php?pods_use=$vv[pods_use]&podskind=$vv[podskind]&podsno=$vv[podsno]&goodsno=$vv[goodsno]&optno=$vv[optno]&storageid=$vv[storageid]&podoptno=$vv[podoptno]&addoptno=$vv[addoptno]&ea=$vv[ea]&mid=$value[mid]');\" style=\"cursor:pointer\">"._("편집하기")."</u>";
                              } else if ($vv[pods_use]=="2" && $vv[ext_json_data][editor_type] == "web") {
                                 $editor_type = $vv[ext_json_data][editor_type];
                                 //$data_table .= "<u onclick=\"popup('PodsCallEditorUpdate.php?pods_use=$vv[pods_use]&podskind=$vv[podskind]&podsno=$vv[podsno]&goodsno=$vv[goodsno]&optno=$vv[optno]&storageid=$vv[storageid]&podoptno=$vv[podoptno]&addoptno=$vv[addoptno]&ea=$vv[ea]&mid=$value[mid]&editor_type=$editor_type&cover_id=$vv[cover_range_id]');\" style=\"cursor:pointer\">"._("편집하기-web")."</u>";
                                 $data_table .= "<u onclick=\"PodsCallEditorUpdate('$vv[pods_use]','$vv[podskind]','$vv[podsno]','$vv[goodsno]','$vv[optno]','$vv[storageid]','$vv[podsno]','$vv[podoptno]','$vv[addoptno]','$vv[ea]','','','Y','','','','','','web');\" style=\"cursor:pointer\">"._("편집하기-web")."</u>";
                              } else {
                                 //$data_table .= "<u onclick=\"popupLayer('PodsCallEditorUpdate.php?pods_use=$vv[pods_use]&podskind=$vv[podskind]&podsno=$vv[podsno]&goodsno=$vv[goodsno]&optno=$vv[optno]&storageid=$vv[storageid]&podoptno=$vv[podoptno]&addoptno=$vv[addoptno]&ea=$vv[ea]&mid=$value[mid]','','',,1);\" style=\"cursor:pointer\">"._("편집하기")."</u>";
                                 $data_table .= "<u onclick=\"PodsCallEditorUpdate('$vv[pods_use]','$vv[podskind]','$vv[podsno]','$vv[goodsno]','$vv[optno]','$vv[storageid]','$vv[podsno]','$vv[podoptno]','$vv[addoptno]','$vv[ea]','','','Y','','','','','','','');\" style=\"cursor:pointer\">"._("편집하기")."</u>";
                              }
                           }

                           //orderitem 마지막 인덱스에서 사용쿠폰 내역 조회 210416 jtkim
                           if($vv[dc_coupon] != 0 && $kk >= $ord_item_count - 1){
                               $coupon_info = $m_order->getCouponInfo($value[payno],1);
                               if($coupon_info){
                                   $data_table .= "<div>사용쿠폰 : $coupon_info[coupon_name]</div>";
                               }
                           }

        $data_table .= "</td>
                        <td width=\"130\" align=\"right\">
                        (";
                           $data_table .= number_format($vv[goods_price]);
                           if ($vv[aprice]) $data_table .= "+ ".number_format($vv[aprice]);
                           if ($vv[addopt_aprice]) $data_table .= "+ ".number_format($vv[addopt_aprice]);
                           if ($vv[print_aprice]) $data_table .= "+ ".number_format($vv[print_aprice]);
                           if ($vv[addpage_aprice]) $data_table .= "+ ".number_format($vv[addpage_aprice]);
                           if ($vv[dc_member]) $data_table .= "- ".number_format($vv[dc_member]);
         $data_table .= ")
                        X $vv[ea]";

                        if ($vv[dc_coupon]) $data_table .= "- ".number_format($vv[dc_coupon]);
       $data_table .= "</td>
                
                        <td width=\"10\">=</td>
                        <td align=\"right\" width=\"50\">
                        <b>";
                        $data_table .= number_format($vv[payprice]);
        $data_table .= "</b>
                        </td>
                        <td width=\"150\" class=\"stxt\" align=\"center\">
                        ";
                        $data_table .= $r_step[$vv[itemstep]];
                        if($vv[itemstep] == '81' || $vv[itemstep] == '82' || $vv[itemstep] == '83') {
                            $data_table .= "<a href=\"javascript:;\" onclick=\"popup('order_design_popup.php?payno=$value[payno]',900,750)\">&nbsp;<u>"._("시안관리")."</u></a>";
                        }else if($cid='lotteria'/* || $cid='4483' */){
                           $data_table .= "<a href=\"javascript:;\" onclick=\"popup('order_design_popup.php?payno=$value[payno]',900,750)\">&nbsp;<u>"._("시안관리")."</u></a>";
                        }

        $data_table .= "<br>";
                        if($value[paymethod] == 'd') {
                           if($vv[itemstep] == '1' || $vv[itemstep] == '2' || $vv[itemstep] == '91' || $vv[itemstep] == '92') {
                              $data_table .= "<a href=\"javascript:ordCancle('$value[mid]', '$vv[payno]', '$vv[ordno]', '$vv[ordseq]');\">"._("주문취소")."</a>";
                           }
                        }

                        if ($vv[storageid]) {
                           $data_table .= "<div class=\"desc\">";
                           if($vv[pods_trans]=="1") {
                               $data_table .= "<b class='blue'>"._("전송")."</b>";
                           }
                           else {
                               $data_table .= "<b class='red'>"._("미전송")."</b>";
                           }
                           $data_table .= "</div>";

                           if($vv[est_goodsnm]=="FILEUPLOAD") {
                               //출력기능 확인.
                               $data_table .= "<div class=\"desc\">";
                               $data_table .= "<a href=\"javascript:void(0)\" onclick=\"window.open('upload_inspection_pop.php?orditem_key=$orditem_key&storageid=$vv[storageid]');\">"._("출력파일")."</a>";
                               //popup('../../module/preview.php?storageid=$vv[storageid]',1100,800)
                               $data_table .= "</div>";
                           }

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
