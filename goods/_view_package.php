<?
//패키지 상품
//debug($data);
//debug($data[addtionitem][package]);
//if(count($data[addtionitem][package]) > 3) $css_other = "cont_other";
//debug($css_other);

$cartpage = true;

include "../lib/class.cart.php";

//20150915 / minks / 인스턴스가 달라 쿠키가 다르게 설정되므로 기존의 쿠키와 똑같이 설정하기 위해 추가
if ($_REQUEST[userkey]) $_COOKIE[cartkey] = $_REQUEST[userkey];
if($_REQUEST[mid]) $sess[mid] = $_REQUEST[mid];

$podsApi = new PODStation('20');

$m_cart = new M_cart();
$cart_list = $m_cart->getCartListWithPODKind($cid, '', $addwhere = ' and package_flag=1');

//debug($cart_list);
$package_goodsno = array();
$cart_package_goods_data = array();
if($data[addtionitem][package]) {
   foreach ($data[addtionitem][package] as $key => $val) {
      $package_goodsno[] = $val[goodsno];
   }
}
//debug($package_goodsno);

if($cart_list) {
   foreach ($cart_list as $k => $v) {
      if($data[goodsno] == $v[goodsno]) continue;
      
      //debug("cartno:".$v[cartno].",goodsno:".$v[goodsno].",package_flag:".$v[package_flag].",package_parent_cartno:".$v[package_parent_cartno]);        
      if(in_array($v[goodsno],$package_goodsno)) {
         if($v[storageid]) {
            # 편집 상태 가져오기 20140122 by kdk
            if (in_array($v[podskind], array(3030, 3040, 3041, 3042, 3043, 3050, 3051, 3052, 3060, 3110, 3112, 3053, 3054))) {
               $podsApi -> setVersion('20');
               //pods2 버젼으로 변경
               $ret = $podsApi -> GetMultiOrderInfoResultAllData($v[storageid]);
               $pod_ret2[$ret[ID]] = $ret;

               //debug($pod_ret2);
               //debug($pod_ret2[$data[storageid]][STATE]);
               switch ($pod_ret2[$v[storageid]][STATE]) {
                  case "10" :
                  case "20" :
                     $pod_prog[PROGRESS] = explode("/", $pod_ret2[$v[storageid]][PROGRESS]);
                     if (is_array($pod_prog[PROGRESS])) {
                        if ($pod_prog[PROGRESS][0] != $pod_prog[PROGRESS][1]) {
                           if ($pod_prog[PROGRESS][0] == 0) {
                              $add_errmsg = "0%";
                              $v[edit_progress] = "0%";
                           } else {
                              $add_errmsg = round($pod_prog[PROGRESS][0] / $pod_prog[PROGRESS][1] * 100) . "%";
                              $v[edit_progress] = round($pod_prog[PROGRESS][0] / $pod_prog[PROGRESS][1] * 100) . "%";
                           }
                        }
                     }

                     $v[error] = "7";
                     $m_cart -> updateEditOneField("state", "0", $v[storageid]);
                  break;
                  case "30" :
                  case "40" :
                  case "60" :
                  case "70" :
                  case "90" :
                     $v[error] = "11";
                     $v[edit_progress] = "100%";
                  break;
               }
            }
         }
         $cart_package_goods_data[$v[goodsno]] = $v;
      }
   }
}

//debug($cart_package_goods_data);

//debug(date("Y-m-d H:i:s",time()));  
### 복수 편집기 견적 정보 임시 저장을 20160325 by kdk
$pod_signed = signatureData($cid, $_SERVER[REQUEST_URI]."&date=".date("Y-m-d H:i:s",time()));
//debug($pod_signed);

$tpl -> define('tpl', "goods/view_package.htm");
$tpl -> assign("templateSetIdx",$_GET[templateSetIdx]);
$tpl -> assign($data);
$tpl -> print_('tpl');
?>