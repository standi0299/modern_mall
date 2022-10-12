<?
/*
 * @date : 20190328 (20190328)
 * @author : kdk
 * @brief : 현수막 실사출력 추가.
 * @request :
 * @desc :
 * @todo :
 */

/*
 * @date : 20190121
 * @author : kdk
 * @brief : 시안요청 정보가 있을경우 장바구니가 아닌 결제페이지로 바로이동.
 * @brief : 시안요청 정보 입력.
 * @request : 태웅
 * @desc :
 * @todo :
 */

/*
 * @date : 20180118 (20180118)
 * @author : kdk
 * @brief : 편집기 상품에 사용자 파일 업로드 처리.
 * @request :
 * @desc : 사용자 파일 업로드 처리로 인해  $_REQUEST[storageid][uploaded_list] 값이 없음
 * @todo :
 */

/*
 * @date : 20171220 (20180108)
 * @author : kdk
 * @brief : 패키지상품 관련 처리.
 * @request :
 * @desc :
 * @todo : 충분한 테스트를 진행해야 하며 결제 모듈의 이상유무 면밀히 검토해야함.
 */

$podspage = true;
$cartpage = true;

include "../_header.php";
include "../lib/class.cart.php";

if (($cfg[member_system][order_system] == "close" || $cfg[member_system][order_system] == "edit_close") && !$sess[mid]) {
   //msgNlocationReplace("비회원은 주문을 할 수 없습니다.로그인을 해주세요.", "../member/login.php", "Y");
   //exit;
   // kkwon 2020.08.10, 46~47 구문 자체가 원인으로 앱에서 처리가 안됨
   $msg = "비회원은 주문을 할 수 없습니다.로그인을 해주세요.";
   $location = "../member/login.php";
   echo "<script type='text/javascript'>";
   echo "alert('$msg');";
   echo "parent.location.replace('$location');";
   echo "</script>";
}

$goods = new Goods();
$goods->getMain();

//메인 블럭 컨텐츠 정보 조회.
$mcdata = $goods->mainBlockContentData;
//debug($mcdata);

//메인 블럭 정보 조회.
$data_block = $goods->mainBlockData;
//debug($data_block);

if ($data_block) {
   foreach ($data_block as $key => $val) {
      if ($val[block_code] == "main_block_11") {
         $blockDataArr[$val[block_code]] = $val;
      }
   }
}


//옵션이 있으면 옵션의 편집코드 값을 넣어준다 / 16.12.30 / kjm
if ($_REQUEST[productid]) $_REQUEST[podsno] = $_REQUEST[productid];

//20150915 / minks / 인스턴스가 달라 쿠키가 다르게 설정되므로 기존의 쿠키와 똑같이 설정하기 위해 추가
if ($_REQUEST[userkey]) $_COOKIE[cartkey] = $_REQUEST[userkey];
if ($_REQUEST[mid]) $sess[mid] = $_REQUEST[mid];

$cart = new Cart();
$r_rid = get_release();

switch ($_REQUEST[mode]) {

   //20141023 / minks / 편집타이틀 수정 추가
   case "edittitle":
      $_GET[title] = addslashes($_GET[title]);

      $query = "update exm_cart set title='$_GET[title]' where storageid='$_GET[storageid]'";
      $db->query($query);

	  //편집리스트 편집타이틀 수정 추가 / 20170914 / kdk
      $query = "update exm_edit set title='$_GET[title]' where storageid='$_GET[storageid]'";
      $db->query($query);

      go($_SERVER[HTTP_REFERER]);
      exit;
      break;

   //20141023s / minks / 편집정보 이관 추가
   case "transfer_edit":
      if ($_GET[cartno]) {
         foreach ($_GET[cartno] as $k => $v) {
            list($storageid) = $db->fetch("select storageid from exm_cart where cartno='$v'", 1);

            $query = "update exm_edit set mid='$_GET[mid]', updatedt=now() where storageid='$storageid'";
            $query2 = "update exm_cart set mid='$_GET[mid]', updatedt=now() where storageid='$storageid'";

            $db->query($query);
            $db->query($query2);
         }
      }
      ?>
   <script> window.opener.location.reload(); window.close(); </script>
   <?
   exit;
   break;

case "del_edit":

   foreach ($_REQUEST[cartno] as $k => $v) {
      $hidelog = "user_del_edit_all $sess[mid] $_COOKIE[cartkey] / $cfg[source_save_days]/" . date("y-m-d H:i:s");
      if ($sess[mid]) {
         $hidelog = "user_del_edit_all(member) $sess[mid] $_COOKIE[cartkey] / " . date("y-m-d H:i:s");
         $db->query("update exm_edit set _hide = '1', _hidelog = '$hidelog' where cid = '$cid' and storageid = '$v' and mid = '$sess[mid]' and _hide != 1");
      } else if ($_COOKIE[cartkey]) {
         $hidelog = "user_del_edit_all(non-member) $_COOKIE[cartkey] / " . date("y-m-d H:i:s");
         $db->query("update exm_edit set _hide = '1', _hidelog = '$hidelog' where cid = '$cid' and storageid = '$v' and editkey = '$_COOKIE[cartkey]' and _hide != 1");
      }
   }
   go($_SERVER[HTTP_REFERER]);

   exit;

   go("editlist.php");

   exit;
   break;

case "cart_edit":

   $idx = 1;
   if ($_REQUEST[cartno]) {
      foreach ($_REQUEST[cartno] as $k => $v) {
         if (!$v) continue;
         $tmp = $db->fetch("select * from exm_edit where cid = '$cid' and storageid = '$v'");
         $data[$idx][storageid] = $v;
         $data[$idx][goodsno] = $tmp[goodsno];
         $data[$idx][optno] = $tmp[optno];
         $data[$idx][addopt] = explode(",", $tmp[addoptno]);
         $data[$idx][ea] = 1;
         $data[$idx][est_order_data] = $tmp[est_order_data];
         $data[$idx][est_order_option_desc] = $tmp[est_order_option_desc];
         $data[$idx][est_file_down_full_path] = $tmp[est_file_down_full_path];
         $data[$idx][est_order_type] = $tmp[est_order_type];
         $data[$idx][est_cost] = $tmp[est_cost];
         $data[$idx][est_supply] = $tmp[est_supply];
         $data[$idx][est_price] = $tmp[est_price];
         $data[$idx][est_rid] = $tmp[est_rid];
         $data[$idx][est_goodsnm] = $tmp[est_goodsnm];
         $data[$idx][est_fullpost] = $tmp[est_fullpost];
         $data[$idx][est_pods_version] = $tmp[pods_version];
         $data[$idx][title] = $tmp[title]; //20140929추가  / minks
         $data[$idx][ext_json_data] = $tmp[ext_json_data];
         $loop[] = $data;
         $idx++;
      }
   }
   $cart->add($data);

   //btob 연동안 추가 (편집 리스트에서 선택한 주문건만 연동으로 넘긴다.)       20140328    chunter
   if ($cfg[cart_system][cart_system] == "submit" && $cfg[cart_system][submit][url]) {
      $btobCartNo = array_merge($cart->addid, $cart->editlistCardNo);
      //debug($btobCartNo);
      //exit;
      include "b2b.cart_add_{$cfg[cart_system][submit][language]}.php";
      exit;
   }

   go("cart.php");
   exit;
   break;

   //견적 시스템 옵셤만 수정 처리    20140205    chunter
case "extra_option_update":
   if ($_REQUEST[cartno]) {
      $data[cartno] = $_REQUEST[cartno];
      if ($_REQUEST[option_json]) {
            //$extraOptionData = orderJsonParse2($_REQUEST[option_json]);
         $orderOptionData = str_replace("\r\n", "", $_REQUEST[option_json]);
         $extraOptionData = orderJsonParse2($orderOptionData);
         $data[est_order_data] = $extraOptionData[est_order_data];
         $data[est_order_option_desc] = $extraOptionData[est_order_option_desc];
         $data[est_cost] = '0';
         $data[est_supply] = '0';
         $data[est_price] = $extraOptionData[est_price];
         $data[est_order_type] = $_REQUEST[est_order_type];
      }
      $data[title] = $_REQUEST[est_title];
      $data[est_order_memo] = $_REQUEST[est_order_memo];
   }

   $cart->extraOptionUpdate($data);
      //go("cart.php");
   popupMsgNLocation(_("수정되었습니다"), 'cart.php');
   exit;
   break;

   //미오디오 피규어 편집정보만 수정 처리. / 20181001 / kdk
case "cart_edit_figure":
   if ($_REQUEST[storageid] && $_REQUEST[ext_json_data]) {
      $data[storageid] = $_REQUEST[storageid];
      $data[ext_json_data] = $_REQUEST[ext_json_data];
   }

   $cart->extraJsonUpdate($data);
   msg(_("수정되었습니다"), 'cart.php');
   exit;
   break;

case "cart":

   unset($data);

   if ($_REQUEST[goodsno]) {
         //list($podskind) = $db->fetch("select podskind from exm_goods where goodsno = '$_REQUEST[goodsno]'",1);
      list($podskind, $pods_use) = $db->fetch("select podskind,pods_use from exm_goods where goodsno = '$_REQUEST[goodsno]'", 1);
   }

      #복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.03.16 by kdk
   if ($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
      $pods_use = $_REQUEST[pods_use];
      $podskind = $_REQUEST[podskind];
      $podsno = $_REQUEST[podsno];
   }

   if ($_REQUEST[ea] < 1 || strpos($_REQUEST[ea], ".") !== false) {
      if ($_REQUEST[storageid]) {
         $_REQUEST[ea] = 1;
      } else {
         msg(_("수량이 올바르지 않습니다."), -1);
         exit;
      }
   }

   $log_dir = "../data/tmp/pods_return/";
   if (!is_dir($log_dir)) {
      mkdir($log_dir, 0707);
      chmod($log_dir, 0707);
   }
   $log_file = date("YmdHis") . "_" . uniqid();
   $fp = fopen($log_dir . $log_file, "w");
   fwrite($fp, $_REQUEST[storageid]);

      //테라메모리 exe 실행 시 사용 / 16.05.12 / kjm
   if ($_REQUEST[exe_mode] == 'Y')
      $_REQUEST[storageid] = urldecode($_REQUEST[storageid]);

   if (in_array($podskind, array("3010", "3011", "3020", "3030", "3040", "3041", "3042", "3043", "3050", "3051", "3052", "3060", "3053", "3054", "3055")) && $pods_use != 3 && $_REQUEST[mode2] != "reorder") {
      //20141016 / minks / 재주문일 경우 제외
      // pods 2.0 사용여부를 조건으로 변경처리     20140106    chunter
      //if ($pods_use == 2){

      if (strpos($_REQUEST[storageid], "\\\"uploaded_list\\\"") !== false) $ret = stripslashes($_REQUEST[storageid]); //exe편집기일 경우
      else $ret = $_REQUEST[storageid]; //activeX편집기일 경우

      $ret = json_decode($ret, 1);

      if ($ret[uploaded_list]) {
         foreach ($ret[uploaded_list] as $k => $v) {
            parse_str($v[session_param], $retdata);

            $retdata[sessionparam] = explode(",", urldecode($retdata[sessionparam]));
            if ($retdata[sessionparam][1]) {
               $retdata[sessionparam][1] = str_replace("param:", "", $retdata[sessionparam][1]);
            } else if ($retdata[sessionparam][0]) {
               $retdata[sessionparam][1] = str_replace("param:", "", $retdata[sessionparam][0]);
            }

            $indata = json_decode(base64_decode($retdata[sessionparam][1]), 1);

            if (!is_array($indata[addopt])) $indata[addopt] = explode(",", $indata[addopt]);
            $data[$k][goodsno] = $indata[goodsno];
            $data[$k][storageid] = $v[rsid];
            $data[$k][optno] = $indata[optno];
            $data[$k][ea] = $v[order_count];
            if (!$data[$k][ea]) $data[$k][ea] = 1;
            $data[$k][addopt] = $indata[addopt];
            $data[$k][title] = $v[title];

                #복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.03.16 by kdk
            if ($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
               $data[$k][pods_use] = $_REQUEST[pods_use];
               $data[$k][podskind] = $_REQUEST[podskind];
               $data[$k][podsno] = $_REQUEST[podsno];
            }

            if ($v[goods_options]) {
               $m_cart = new M_cart();
               $m_cart->setGoodsOptions($ret[storageId], $v[goods_options]);
            }
         }
      }

   } else if (in_array($podskind, array("3130", "3110", "3112")) && $_REQUEST[mode2] != "reorder") {

      $_REQUEST[storageid] = stripslashes($_REQUEST[storageid]);
      $_REQUEST[storageid] = json_decode($_REQUEST[storageid], 1);
         //debug($_REQUEST[storageid][uploaded_list]);
      if ($_REQUEST[storageid][uploaded_list]) {
         foreach ($_REQUEST[storageid][uploaded_list] as $k => $v) {
            $_data = explode("&", $v[session_param]);

            foreach ($_data as $k2 => $v2) {
               $v2 = explode("=", $v2);
               $retdata[$v2[0]] = $v2[1];
            }
            $retdata[sessionparam] = explode(",", $retdata[sessionparam]);
            if ($retdata[sessionparam][1]) {
               $retdata[sessionparam][1] = str_replace("param:", "", $retdata[sessionparam][1]);
            } else if ($retdata[sessionparam][0]) {
               $retdata[sessionparam][1] = str_replace("param:", "", $retdata[sessionparam][0]);
            }
            $indata = json_decode(base64_decode($retdata[sessionparam][1]), 1);

            if (!is_array($indata[addopt])) $indata[addopt] = explode(",", $indata[addopt]);
            $data[$k][goodsno] = $indata[goodsno];
            $data[$k][storageid] = $v[rsid];
            $data[$k][optno] = $indata[optno];
            $data[$k][ea] = $v[order_count];
            if (!$data[$k][ea]) $data[$k][ea] = 1;
            $data[$k][addopt] = $indata[addopt];
            $data[$k][title] = $v[title];

                #복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.03.16 by kdk
            if ($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
               $data[$k][pods_use] = $_REQUEST[pods_use];
               $data[$k][podskind] = $_REQUEST[podskind];
               $data[$k][podsno] = $_REQUEST[podsno];
            }
         }
      }

   } else if (in_array($podskind, array("3240")) && $_REQUEST[mode2] != "reorder") {

         // 초간단포토북 처리 2014.09.16 by kdk
      $ret = stripslashes($_REQUEST[storageid]);
      $ret = json_decode($ret, 1);

      if ($ret[uploaded_list]) {
         foreach ($ret[uploaded_list] as $k => $v) {
            parse_str($v[session_param], $retdata);

            $retdata[sessionparam] = explode(",", urldecode($retdata[sessionparam]));
            if ($retdata[sessionparam][1]) {
               $retdata[sessionparam][1] = str_replace("param:", "", $retdata[sessionparam][1]);
            } else if ($retdata[sessionparam][0]) {
               $retdata[sessionparam][1] = str_replace("param:", "", $retdata[sessionparam][0]);
            }

            $indata = json_decode(base64_decode($retdata[sessionparam][1]), 1);

                //if (!is_array($indata[addopt])) $indata[addopt] = explode(",",$indata[addopt]);
                //$data[$k][addopt]     = $indata[addopt];
            $data[$k][goodsno] = $indata[goodsno];
            $data[$k][storageid] = $v[rsid];
            $data[$k][optno] = $indata[optno];
            $data[$k][ea] = $v[order_count];
            if (!$data[$k][ea]) $data[$k][ea] = 1;
            $data[$k][addopt] = $v[al_page_size] . "," . $v[al_page_count];
            $data[$k][title] = $v[title];

                #복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.03.16 by kdk
            if ($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
               $data[$k][pods_use] = $_REQUEST[pods_use];
               $data[$k][podskind] = $_REQUEST[podskind];
               $data[$k][podsno] = $_REQUEST[podsno];
            }
         }
      }
   } else if (in_array($podskind, array("3180", "28")) && $_REQUEST[mode2] != "reorder") {

      if ($podskind == "28") {
         list($_REQUEST[storageid]) = array_slice(explode(";", $_REQUEST[storageid]), -1);
         $_REQUEST[storageid] = str_replace("sId=", "", $_REQUEST[storageid]);
      }

      $_REQUEST[storageid] = explode("&", $_REQUEST[storageid]);
      if (!is_array($_REQUEST[addopt])) $_REQUEST[addopt] = explode(",", $_REQUEST[addopt]);

      foreach ($_REQUEST[storageid] as $k => $v) {
         $data[$k][goodsno] = $_REQUEST[goodsno];
         $data[$k][storageid] = $v;
         $data[$k][optno] = $_REQUEST[optno];
         $data[$k][ea] = $_REQUEST[ea];
         $data[$k][addopt] = $_REQUEST[addopt];

            #복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.03.16 by kdk
         if ($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
            $data[$k][pods_use] = $_REQUEST[pods_use];
            $data[$k][podskind] = $_REQUEST[podskind];
            $data[$k][podsno] = $_REQUEST[podsno];
         }
      }

   } else if (in_array($podskind, array("3231")) && $_REQUEST[mode2] != "reorder") {

      $_REQUEST[storageid] = stripslashes($_REQUEST[storageid]);
      $_REQUEST[storageid] = json_decode($_REQUEST[storageid], 1);

         //debug($_REQUEST[storageid][uploaded_list]);
      if ($_REQUEST[storageid][uploaded_list]) {
         foreach ($_REQUEST[storageid][uploaded_list] as $k => $v) {
            $_data = explode("&", $v[session_param]);

            foreach ($_data as $k2 => $v2) {
               $v2 = explode("=", $v2);
               $retdata[$v2[0]] = $v2[1];
            }
            $retdata[sessionparam] = explode(",", $retdata[sessionparam]);
            if ($retdata[sessionparam][1]) {
               $retdata[sessionparam][1] = str_replace("param:", "", $retdata[sessionparam][1]);
            } else if ($retdata[sessionparam][0]) {
               $retdata[sessionparam][1] = str_replace("param:", "", $retdata[sessionparam][0]);
            }
            $indata = json_decode(base64_decode($retdata[sessionparam][1]), 1);

            if (!is_array($indata[addopt])) $indata[addopt] = explode(",", $indata[addopt]);
            $data[$k][goodsno] = $_REQUEST[goodsno];
            $data[$k][storageid] = $v[rsid];
            $data[$k][optno] = $indata[optno];
            $data[$k][ea] = $v[order_count];
            if (!$data[$k][ea]) $data[$k][ea] = 1;
            $data[$k][addopt] = $indata[addopt];
            $data[$k][title] = $v[title];

                #복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.03.16 by kdk
            if ($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
               $data[$k][pods_use] = $_REQUEST[pods_use];
               $data[$k][podskind] = $_REQUEST[podskind];
               $data[$k][podsno] = $_REQUEST[podsno];
            }
         }
      }

   } else {

      $data[0][goodsno] = $_REQUEST[goodsno];
      if ($_REQUEST[optno]) {
         if ($_REQUEST[mode2] != "reorder") {
            if (is_array($_REQUEST[optno])) {
               $_REQUEST[optno] = array_slice($_REQUEST[optno], -1);
               $data[0][optno] = $_REQUEST[optno][0];
            } else {
               $data[0][optno] = $_REQUEST[optno];
            }
         } else {
            $data[0][optno] = $_REQUEST[optno];
            $_REQUEST[addopt] = explode(",", $_REQUEST[addopt]);
         }
      }
      if (!$_REQUEST[ea] || !is_numeric($_REQUEST[ea])) $_REQUEST[ea] = 1;
      list($_REQUEST[storageid]) = explode("&", $_REQUEST[storageid]);

      $data[0][ea] = $_REQUEST[ea];
      $data[0][storageid] = $_REQUEST[storageid];
      $data[0][addopt] = $_REQUEST[addopt];
      $data[0][title] = $_REQUEST[title];

         //일반상품에 사용자 파일 업로드 처리로 인해  UPLOAD 값 추가. / 20180118 / kdk
      if ($_REQUEST[est_order_type])
         $data[0][est_order_type] = $_REQUEST[est_order_type];

         #복수 편집기 처리 pods_use, podskind, podsno pods_editor.js에서 form에 추가하여 넘기 2016.03.16 by kdk
      if ($_REQUEST[pods_use] && $_REQUEST[podskind] && $_REQUEST[podsno]) {
         $data[0][pods_use] = $_REQUEST[pods_use];
         $data[0][podskind] = $_REQUEST[podskind];
         $data[0][podsno] = $_REQUEST[podsno];
      }
   }



      //사용자 파일 업로드 처리로 인해  $_REQUEST[storageid][uploaded_list] 값이 없음. / 20180118 / kdk
   if ($data[0][goodsno] == "") {
      $data[0][goodsno] = $_REQUEST[goodsno];
      if ($_REQUEST[optno]) {
         $data[0][optno] = $_REQUEST[optno];
         $_REQUEST[addopt] = explode(",", $_REQUEST[addopt]);
      }
      $data[0][addopt] = $_REQUEST[addopt];
      $data[0][title] = $_REQUEST[title];
   }

   if ($_REQUEST[cartno]) {
      foreach ($_REQUEST[cartno] as $k => $v) {
         if (!$v) continue;
         list($goodsno, $optno, $addoptno) = $db->fetch("select goodsno, optno, addoptno from exm_edit where cid = '$cid' and storageid = '$k'", 1);
         $data[0][storageid] = $k;
         $data[0][goodsno] = $goodsno;
         $data[0][optno] = $optno;
         $data[0][addopt] = (!is_array($_REQUEST[addopt])) ? explode(",", $addoptno) : $addoptno;

            #복수 편집기 처리 pods_use, podskind, podsno exm_edit에서 복사 2016.03.16 by kdk
         list($pods_use, $podsno, $podskind) = $db->fetch("select pods_use, podsno, podskind from exm_edit where cid = '$cid' and storageid = '$k'", 1);
         $data[0][pods_use] = $pods_use;
         $data[0][podskind] = $podskind;
         $data[0][podsno] = $podsno;
      }
   }

      //20160607 / minks / 인스턴스가 달라 쿠키가 다르게 설정되므로 기존의 쿠키와 똑같이 설정하기 위해 추가
   if ($_REQUEST[sessdata]) $data[0][sessdata] = makeDecryptData($_REQUEST[sessdata], true);

      ### 복수 편집기 견적 정보 임시 저장을 20160325 by kdk
   if ($_REQUEST[pod_signed] && $_REQUEST[pod_signed] != "") {
      list($option_json, $order_title, $order_memo) = $db->fetch("select option_json, order_title, order_memo from tb_extra_option_json_temp where pod_signed = '$_REQUEST[pod_signed]'", 1);
      if ($option_json != "") {
         $option_json = str_replace('"', '\"', $option_json);
         $_REQUEST[option_json] = $option_json;
      }
      if ($order_title != "") $_REQUEST[est_title] = $order_title;
      if ($order_memo != "") $_REQUEST[est_order_memo] = $order_memo;

      $db->query("delete from tb_extra_option_json_temp where pod_signed = '$_REQUEST[pod_signed]'");
         //debug($_REQUEST[option_json]);
   }

       //옵션 자동견적이 있을경우     20140128    chunter
       //부가세 포함 수정 2015.05.13 by kdk
   if ($_REQUEST[option_json]) {
      $orderOptionData = str_replace("\r\n", "", $_REQUEST[option_json]);

      $extraOptionData = orderJsonParse2($orderOptionData);

      $data[0][est_order_data] = $extraOptionData[est_order_data];
      $data[0][est_order_option_desc] = $extraOptionData[est_order_option_desc];
      $data[0][est_supply_price] = $extraOptionData[est_supply_price];

      $data[0][est_price] = $extraOptionData[est_price];

         //$data[0][est_price] = $extraOptionData[est_price] + ($extraOptionData[est_price] * 0.1);    //부가세 포함    20140410 //포함안함 2015.05.13

      $data[0][est_order_type] = $_REQUEST[est_order_type];
      $data[0][est_order_memo] = $_REQUEST[est_order_memo];     //자동견적 주문 메모    20140326

      $data[0][est_goodsnm] = $_REQUEST[est_goodsnm]; //인터프로견적 상품구분(파일업로드,다이렉트파일업로드) / 20180703 / kdk

         //주문 수량은 자동견적 주문 수량으로 변경해 준다.       //20140326    chunter
         //if ($extraOptionData[est_order_cnt] > 0)
         //$data[0][ea] = $extraOptionData[est_order_cnt];

      $data[0][title] = $_REQUEST[est_title];     //자동견적 주문 제목    20140410

         //상품 카테고리 저장.
      if (!$_REQUEST[catno] || $_REQUEST[catno] == "") {
         if ($extraOptionData[catno])
            $data[0][catno] = $extraOptionData[catno];
      }

         //보관함코드가 없을 경우에만 처리 2016.03.29 by kdk
      if ($data[0][storageid] == "") {
         if ($_REQUEST[storageid]) {
            if (is_array($_REQUEST[storageid])) $data[0][storageid] = $_REQUEST[storageid][0];
            else $data[0][storageid] = $_REQUEST[storageid];
         } else
            $data[0][storageid] = "_temp_" . date('Ymd') . "_" . time(); //중복으로 장바구니에 담기 위해 임시 storageid 부여함
      }

         //견적 상품이 있을 경우
      $cart->add_extra($data);
   }
      //debug($extraOptionData);
      //debug($_COOKIE[cartkey]);

      //debug($podskind);
      ### 주문 정보(wpodorderjsondata) 가져오기 2014.04.17 by kdk
   if (in_array($podskind, array(1001, 1002, 1003, 1005, 1006, 1007, 1008))) {
         /*
       * //속도 저하 현상으로  soap 제거  20140513    chunter
         $client = new soapclient("http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/StationWebService.asmx?WSDL",true);
         $client->xml_encoding = "UTF-8";
         $client->soap_defencoding = "UTF-8";
         $client->decode_utf8 = false;
         $wpod_ret = $client->call('GetMultiOrderInfoResult',array('storageids'=>$data[0][storageid]));
         $wpod_ret[GetMultiOrderInfoResultResult] = explode("|^|",$wpod_ret[GetMultiOrderInfoResultResult]);
       */

      $wpod_ret = readUrlWithcurl('http://' . PODS20_DOMAIN . '/CommonRef/StationWebService/GetMultiOrderInfoResult.aspx?storageids=' . $data[0][storageid], false);
         //$wpod_ret[GetMultiOrderInfoResultResult] = explode("|^|",$wpod_ret);
         //$wpod_ret = _ilark_vars(substr($wpod_ret[GetMultiOrderInfoResultResult][2],8));
         //$data[0][vdp_edit_data] = $wpod_ret[WDATA];

      $wdata = explode("|^|", $wpod_ret);
      $wpod_ret = _ilark_vars(substr($wdata[2], 8));
      $data[0][vdp_edit_data] = $wpod_ret[WDATA];
   }

   if ($_REQUEST[catno] && $_REQUEST[catno] != "")
      $data[0][catno] = $_REQUEST[catno];

      //스튜디오북에만 추가한 기능 - #9127
      //편집리스트의 타이틀을 재주문을 하면 장바구니에도 넣어준다 / 17.02.01 / kjm
   if ($_GET[origin_storageid]) {
      list($editlist_title) = $db->fetch("select title from exm_edit where storageid = '$_GET[origin_storageid]'", 1);
      $data[0][title] = $editlist_title;
   }

   if ($podskind == "99999") { //미오디오 피규어 상품관련 편집기 결과값을 저장한다. / 2018.09.10 / kdk
      $data[0][pods_use] = $_REQUEST[pods_use];
      $data[0][podskind] = $_REQUEST[podskind];
      $data[0][ext_json_data] = $_REQUEST[ext_json_data];
   } else {
      if ($_REQUEST[editor_type]) {
         $editor_type = array();

         $editor_type[editor_type] = $_REQUEST[editor_type];

         $data[0][ext_json_data] = json_encode($editor_type);
      }
   }

   $cart->add($data);

      ###wpod vdp 복수 주문일 경우 by 2014.04.18 kdk
   if ($_REQUEST[storageids]) {
      $storageid_arr = explode(',', $_REQUEST[storageids]);
      foreach ($storageid_arr as $key => $value) {
         if ($value) {
            $data[0][storageid] = $value;

               ### 주문 정보(wpodorderjsondata) 가져오기 2014.04.17 by kdk
            if (in_array($podskind, array(1001, 1002, 1003, 1005, 1006, 1007, 1008))) {
                  /*
                  //속도 저하 현상으로  soap 제거  20140513    chunter
                  $client = new soapclient("http://" .PODS20_DOMAIN. "/CommonRef/StationWebService/StationWebService.asmx?WSDL",true);
                  $client->xml_encoding = "UTF-8";
                  $client->soap_defencoding = "UTF-8";
                  $client->decode_utf8 = false;
                  $wpod_ret = $client->call('GetMultiOrderInfoResult',array('storageids'=>$data[0][storageid]));
                  $wpod_ret[GetMultiOrderInfoResultResult] = explode("|^|",$wpod_ret[GetMultiOrderInfoResultResult]);
                  $wpod_ret = _ilark_vars(substr($wpod_ret[GetMultiOrderInfoResultResult][2],8));
                  $data[0][vdp_edit_data] = $wpod_ret[WDATA];
                */

               $wpod_ret = readUrlWithcurl('http://' . PODS20_DOMAIN . '/CommonRef/StationWebService/GetMultiOrderInfoResult.aspx?storageids=' . $data[0][storageid], false);
                  //$wpod_ret[GetMultiOrderInfoResultResult] = explode("|^|",$wpod_ret);
                  //$wpod_ret = _ilark_vars(substr($wpod_ret[GetMultiOrderInfoResultResult][2],8));
                  //$data[0][vdp_edit_data] = $wpod_ret[WDATA];

               $wdata = explode("|^|", $wpod_ret);

               $wpod_ret = _ilark_vars(substr($wdata[2], 8));
               $data[0][vdp_edit_data] = $wpod_ret[WDATA];
            }
               //debug($data);
               //exit;
            $cart->add($data);
         }
      }
   }
      //exit;

      //패키지상품 관련 처리 / 2017.12.20 / kdk
   if ($_REQUEST[package_mode] == "Y" && $_REQUEST[c_cartno]) { //대표 패키지상품 및 하위 상품 편집완료
          //대표 패키지상품 등록 및 편집완료.
      $db->query("update exm_cart set est_goodsnm='$_REQUEST[est_goodsnm]',package_flag='2' where cartno='" . $cart->addid_direct . "'");

          //하위 패키지상품 편집완료 및 대표 패키지상품 cartno 등록 .
      $db->query("update exm_cart set est_goodsnm='$_REQUEST[est_goodsnm]',package_flag='2',package_parent_cartno='$cart->addid_direct' where cartno in (" . $_REQUEST[c_cartno] . ")");

          //일반상품 보관함코드 생성 및 장바구니 업데이트.
      if (!$_REQUEST[storageid]) {
         $ret = $cart->CreateStorageId($r_site_product_code_pack["1"]);
         if ($ret[0] == "success") {
            $db->query("update exm_cart set storageid='" . $ret[1] . "',podskind='9999',est_pods_version='" . $r_site_product_code_pack["1"] . "' where cartno='" . $cart->addid_direct . "'");
         } else {
                //보관함코드 생성 실패 시 처리.
            msg($ret[1]);
            exit;
         }
      }

      echo $cart->addid_direct;
      exit;
   } else if ($_REQUEST[package_mode] == "Y") { //하위 패키지상품 편집중.
      if ($_REQUEST[cartno] == "") $_REQUEST[cartno] = $cart->addid_direct;
      $db->query("update exm_cart set package_flag='1' where cartno='" . $_REQUEST[cartno] . "'");

      if ($_REQUEST[ajax_mode] == "Y") {
              //일반상품 보관함코드 생성 및 장바구니 업데이트.
         if (!$_REQUEST[storageid]) {
            $ret = $cart->CreateStorageId($r_site_product_code_pack["1"]);
            if ($ret[0] == "success") {
               $db->query("update exm_cart set storageid='" . $ret[1] . "',podskind='9999',est_pods_version='" . $r_site_product_code_pack["1"] . "' where cartno='" . $_REQUEST[cartno] . "'");
            } else {
                      //보관함코드 생성 실패 시 처리.
               msg($ret[1]);
               exit;
            }
         }

         echo $cart->addid_direct;
      }

      // 몰 설정에서 장바구니 담기 완료 스크립트가 있을경우 수행
      if(trim($cfg[in_cart_script])) setCookie('in_cart_script', $cfg[in_cart_script], time() + 60 * 10, '/');

      if ($_REQUEST[wpod_mode] == "Y") { //장바구니가 아닌 이전 페이지로 이동.
              //$_REQUEST[pod_signed]
         if ($_REQUEST[rurl]) {
                  //base64_encode(urlencode($_GET[rurl]))
            $_REQUEST[rurl] = urldecode(base64_decode($_REQUEST[rurl]));
                  //debug($_REQUEST[rurl]);
            go($_REQUEST[rurl]);
         }
      } else { //패키지상품이면서 ActiveX인경우
         if ($_REQUEST[rurl]) go($_REQUEST[rurl]);
      }
      exit;
   }

   // 몰 설정에서 장바구니 담기 완료 스크립트가 있을경우 수행
   if(trim($cfg[in_cart_script])) setCookie('in_cart_script', $cfg[in_cart_script], time() + 60 * 10, '/');

      ### 장바구니이동여부 변수가 설정되어있는경우
   if (is_numeric($_REQUEST[goCart])) {
      if ($_REQUEST[goCart] == 1) { ### 장바구니이동일때
         echo "<script>parent.location.href='cart.php'</script>";
      } else {
         echo "<script>parent.$('cart_confirm').style.display='none';</script>";
      }
      exit;
   }

   if ($cfg[cart_system][cart_system] == "submit" && $cfg[cart_system][submit][url]) {
      $btobCartNo = $cart->addid;
      include "b2b.cart_add_{$cfg[cart_system][submit][language]}.php";
      exit;
   }

   if ($_REQUEST[mobile_type] == "Y") {
         //20160810 / minks / pc편집인지 모바일편집인지 구분
      $db->query("update exm_cart set mobile_edit_flag='1' where storageid='" . $data[0][storageid] . "'");
      echo "ok";
   } else go("cart.php");

   exit;
   break;

// wishlist multi cart add 210709 jtkim
case "wish_list_multi_cart" :

  forEach($_REQUEST['no'] as $k){
	  $no = $db->fetch("select goodsno from md_wish_list where cid='$cid' and no = '$k'", 1);
    $data[0][goodsno] = $no[0];
    $data[0][ea] = 1;
    $cart->add($data);
  }

  go("cart.php");
  exit;
	break;

case "del":

   $cart->del($_REQUEST[cartno]);
   go("cart.php");

   exit;
   break;

   //견적의뢰
case "cart_extra":

   unset($data);

   if ($_REQUEST[goodsno]) {
         //list($podskind) = $db->fetch("select podskind from exm_goods where goodsno = '$_REQUEST[goodsno]'",1);
      list($podskind, $pods_use) = $db->fetch("select podskind,pods_use from exm_goods where goodsno = '$_REQUEST[goodsno]'", 1);
   }

   if ($_REQUEST[ea] < 1 || strpos($_REQUEST[ea], ".") !== false) {
      if ($_REQUEST[storageid]) {
         $_REQUEST[ea] = 1;
      } else {
         msg(_("수량이 올바르지 않습니다."), -1);
         exit;
      }
   }

      //편집기(pods 1.0, 2.0) 사용에 따른 조건 처리는 무시한다.
   $data[0][goodsno] = $_REQUEST[goodsno];
   if ($_REQUEST[optno]) {
      if ($_REQUEST[mode2] != "reorder") {
         $_REQUEST[optno] = array_slice($_REQUEST[optno], -1);
         $data[0][optno] = $_REQUEST[optno][0];
      } else {
         $data[0][optno] = $_REQUEST[optno];
         $_REQUEST[addopt] = explode(",", $_REQUEST[addopt]);
      }
   }
   if (!$_REQUEST[ea] || !is_numeric($_REQUEST[ea])) $_REQUEST[ea] = 1;
   list($_REQUEST[storageid]) = explode("&", $_REQUEST[storageid]);

   $data[0][ea] = $_REQUEST[ea];
   $data[0][storageid] = $_REQUEST[storageid];
   $data[0][addopt] = $_REQUEST[addopt];

   if ($_REQUEST[cartno]) {
      foreach ($_REQUEST[cartno] as $k => $v) {
         if (!$v) continue;
         list($goodsno, $optno, $addoptno) = $db->fetch("select goodsno, optno, addoptno from exm_edit where cid = '$cid' and storageid = '$k'", 1);
         $data[0][storageid] = $k;
         $data[0][goodsno] = $goodsno;
         $data[0][optno] = $optno;
         $data[0][addopt] = (!is_array($_REQUEST[addopt])) ? explode(",", $addoptno) : $addoptno;
      }
   }

   if ($_REQUEST[option_json]) {
      $orderOptionData = str_replace("\r\n", "", $_REQUEST[option_json]);

      $extraOptionData = orderJsonParse2($orderOptionData);
      $data[0][est_order_data] = $extraOptionData[est_order_data];
      $data[0][est_order_option_desc] = $extraOptionData[est_order_option_desc];
      $data[0][est_supply_price] = "0"; //$extraOptionData[est_supply_price];
      $data[0][est_price] = "0"; //$extraOptionData[est_price];
         //$data[0][est_price] = $extraOptionData[est_price] + ($extraOptionData[est_price] * 0.1);    //부가세 포함    20140410 //포함안함 2015.05.13

      $data[0][est_order_type] = $_REQUEST[est_order_type];

      $data[0][est_order_memo] = $_REQUEST[est_order_memo];     //자동견적 주문 메모    20140326

         //주문 수량은 자동견적 주문 수량으로 변경해 준다.       //20140326    chunter
         //if ($extraOptionData[est_order_cnt] > 0)
           //$data[0][ea] = $extraOptionData[est_order_cnt];

      $data[0][title] = $_REQUEST[est_title];     //자동견적 주문 제목    20140410

      if ($_REQUEST[storageid]) {
         if (is_array($_REQUEST[storageid])) $data[0][storageid] = $_REQUEST[storageid][0];
         else $data[0][storageid] = $_REQUEST[storageid];
      } else
         $data[0][storageid] = "_temp_" . date('Ymd') . "_" . time(); //중복으로 장바구니에 담기 위해 storageid 부여함
   }
      //debug($extraOptionData);
      //debug($_COOKIE[cartkey]);

      //의뢰인정보
   if ($_REQUEST[est_order_info]) {
      $orderInfo = str_replace('\"', '"', $_REQUEST[est_order_info]);
      $orderInfo = str_replace('\\\\', '\\', $orderInfo);

      $orderData = json_decode($orderInfo, true);
      $data[0]["order_name"] = $orderData[order_name];
      $data[0]["order_cname"] = $orderData[order_cname];
      $data[0]["order_phone"] = $orderData[order_phone];
      $data[0]["order_mobile"] = $orderData[order_mobile];
      $data[0]["order_email"] = $orderData[order_email];
      $data[0]["order_sns"] = $orderData[order_sns];
   }

   $cart->add_extra($data);

      //자동 SMS 발송 $cfg[nameComp]
   autoSms(_("견적신청"), $data[0]["order_mobile"]);

      //견적내역조회 페이지로 이동
   go("extra_cart_list.php");

   exit;
   break;

   //견적의뢰
case "del_extra":
   foreach ($_REQUEST[cartno] as $k => $v) {
      if ($v)
         $cart->del_extra($v);
   }

      //$cart->del_extra($_REQUEST[cartno]);

      //견적내역조회 페이지로 이동
   go("extra_cart_list.php");

   exit;
   break;

   //견적의뢰에서 장바구니에 담기
case "add_extra_cart":

   foreach ($_REQUEST[cartno] as $k => $v) {
      if ($v) {
         $sql = "select * from tb_extra_cart where cartno='$v'";
         $sql .= ($sess[mid]) ? " and mid = '{$sess[mid]}'" : " and cartkey = '$_COOKIE[cartkey]'";

         $ret = $db->fetch($sql);

         $data[0] = $ret;
         $cart->add($data);

            //견적의뢰 완료 처리
         $cart->order_extra($v);
      }
   }

      //장바구니 페이지로 이동
   go("cart.php");

   exit;
   break;

case "mod":

   if (!is_numeric($_REQUEST[ea]) || $_REQUEST[ea] < 1) {
      msg(_("수량은 0보다 큰 숫자형이여야 합니다."), -1);
      exit;
   }

   $cart->mod($_REQUEST[cartno], $_REQUEST[ea]);


   go("cart.php");

   exit;
   break;

case "buy":

   $data[0][goodsno] = $_REQUEST[goodsno];
   if ($_REQUEST[optno]) {
      $_REQUEST[optno] = array_slice($_REQUEST[optno], -1);
      $data[0][optno] = $_REQUEST[optno][0];
   }
   $data[0][addopt] = $_REQUEST[addopt];
   if (!$_REQUEST[ea]) $_REQUEST[ea] = 1;
   $data[0][ea] = $_REQUEST[ea];

   $data[0][catno] = $_REQUEST[catno];

   if ($_REQUEST[title]) $data[0][title] = $_REQUEST[title]; //by kkwon 20.07.22

        //시안요청일 경우. / 20190121 / kdk
   if ($_REQUEST[design_draft_flag] == "Y") {
      $data[0][storageid] = $_REQUEST[storageid];
      if ($_REQUEST[option_json]) {
         $orderOptionData = str_replace("\r\n", "", $_REQUEST[option_json]);

         $extraOptionData = orderJsonParse2($orderOptionData);

         $data[0][est_order_data] = $extraOptionData[est_order_data];
         $data[0][est_order_option_desc] = $extraOptionData[est_order_option_desc];
         $data[0][est_supply_price] = $extraOptionData[est_supply_price];

         $data[0][est_price] = $extraOptionData[est_price];

                //$data[0][est_price] = $extraOptionData[est_price] + ($extraOptionData[est_price] * 0.1);    //부가세 포함    20140410 //포함안함 2015.05.13

         $data[0][est_order_type] = $_REQUEST[est_order_type];
         $data[0][est_order_memo] = $_REQUEST[est_order_memo];     //자동견적 주문 메모    20140326
         $data[0][est_goodsnm] = $_REQUEST[est_goodsnm]; //인터프로견적 상품구분(파일업로드,다이렉트파일업로드) / 20180703 / kdk
         $data[0][title] = $_REQUEST[est_title];     //자동견적 주문 제목    20140410

                //견적 상품이 있을 경우 / 저장안함.
                //$cart->add_extra($data);
      }

      if ($_REQUEST[ext_json_data]) {
         $data[0][ext_json_data] = $_REQUEST[ext_json_data];
      }

      if ($_REQUEST[est_file_upload_json]) {
         $data[0][est_file_upload_json] = $_REQUEST[est_file_upload_json];
      }

   }

   $cart->add($data);

        //시안요청일 경우. / 20190121 / kdk
   if ($_REQUEST[design_draft_flag] == "Y") {
            //시안요청 등록.md_design_draft
      if (!$cart->setDesignInsert($data[0])) {
         msg(_("시안요청 정보 등록에 오류가 발생했습니다.\n다시 시도해 주세요."), -1);
         exit;
      }

      go("orderpayment.php?cartno[]=$cart->addid_direct&design_draft_flag=Y");
      exit;
   }
        //exit;

   if ($cfg[cart_system][cart_system] == "submit" && $cfg[cart_system][submit][url]) {
      $btobCartNo = $cart->addid;
      include "b2b.cart_add_{$cfg[cart_system][submit][language]}.php";
      exit;
   }

   if ($cfg[skin_theme] == "M2" || $cfg[skin_theme] == "P1" ){
      if($cfg[ssl_use] == "Y"){
         if( trim($cfg[urlService]) && $_SERVER[HTTP_HOST] == $cfg[urlService] ){
            go("https://".$_SERVER[HTTP_HOST]."/order/orderpayment.php?cartno[]=$cart->addid_direct");
         }else{
            go($cfg[ssl_url]."/order/orderpayment.php?cartno[]=$cart->addid_direct");
         }
      } else {
         go("orderpayment.php?cartno[]=$cart->addid_direct");
      }
   }else{
      go("order.php?cartno[]=$cart->addid_direct");
   }

   exit;
   break;

case "truncate":

   if (!is_array($_REQUEST[cartno])) {
      msg(_("상품을 선택해주세요"), -1);
      exit;
   }

   $_REQUEST[cartno] = implode(",", $_REQUEST[cartno]);

   $query = "delete from exm_cart where cartno in ($_REQUEST[cartno])";
   $db->query($query);

   go("cart.php");

   exit;
   break;
}

function _ilark_vars($vars, $flag = ";")
{
   $r = array();
   $div = explode($flag, $vars);
   foreach ($div as $tmp) {
      $pos = strpos($tmp, "=");
      list($k, $v) = array(substr($tmp, 0, $pos), substr($tmp, $pos + 1));
      $r[$k] = $v;
   }
   return $r;
}

$history_back = "../main/index.php";
//debug($cart->item);

if ($cfg[skin_theme] == "B1") {

    include "../print/lib_const_print.php";     //옵션 항목 설정 및 가격설정

    //시안요청 주문건 장바구니에 노출안함. / 20190118 / kdk
    //debug($cart->item);
    $cart_item = array();
    if (!empty($cart->item)) {
        foreach ($cart->item as $key => $value) {
            foreach ($value as $k => $v) {
                $est_order_option_desc = "";
                //debug($v[extra_option]);

                if ($r_est_print_product[$v[extra_option]]) { //견적상품일 경우.
                    //옵션변경 안함.
                    $v[option_mod_enabled] = "N";

                    //매,건.
                    if ($v[est_order_data]) {
                        $est_arr = json_decode($v[est_order_data], true);
                        //debug($est_arr);
                        $v[order_cnt_select] = $est_arr[opt_page];
                        $v[unit_order_cnt] = $est_arr[cnt];

                        if ($est_arr[opt_size] == "USER") {
                            $est_order_option_desc .= "사이즈:user (". $est_arr[cut_width] ."x". $est_arr[cut_height] . ")<br>";
                        } else {
                            //$est_order_option_desc .= "사이즈:". $est_arr[work_width] ."x". $est_arr[work_height] . "<br>";
                            $est_order_option_desc .= "사이즈:{$r_ipro_pr_standard_size[$est_arr[opt_size]][name]} ({$r_ipro_pr_standard_size[$est_arr[opt_size]][size_x]} x {$r_ipro_pr_standard_size[$est_arr[opt_size]][size_y]})<br>";
                        }

                        /*
                        if ($est_arr[cut_width] && $est_arr[cut_height]) {
                            $est_order_option_desc .= "사이즈:". $est_arr[cut_width] ."x". $est_arr[cut_height] . "<br>";
                        } else {
                            $est_order_option_desc .= "사이즈:". $est_arr[work_width] ."x". $est_arr[work_height] . "<br>";
                        }
                        */

                        //debug($v[est_order_option_desc]);
                        //제목,규격,수량,메모 항목 제외.
                        if ($v[est_order_option_desc]) {
                            $v[est_order_option_desc] = str_replace("[", "", $v[est_order_option_desc]);
                            $desc_arr = explode("]", $v[est_order_option_desc]);
                            //debug($desc_arr);
                            if ($desc_arr) {
                                foreach ($desc_arr as $kk => $vv) {
                                    if ($vv && strpos($vv, "내지") !== false)
                                        $est_order_option_desc .= str_replace("내지::/", "용지:", $vv) . "<br>";
                                    if ($vv && strpos($vv, "후가공") !== false)
                                        $est_order_option_desc .= str_replace(";", "<br>", str_replace("후가공::", "", $vv));
                                    //if ($vv && strpos($vv, "메모") !== false)
                                        //$est_order_option_desc .= str_replace(";", "<br>", str_replace("메모:", "요청사항:", $vv));
                                }
                                //debug($est_order_option_desc);
                            }
                            $v[est_order_option_desc] = $est_order_option_desc;
                        }
                    }
                } else {
                    //매,건.
                    if ($v[est_order_data]) {
                        $est_arr = json_decode($v[est_order_data], true);
                        $v[order_cnt_select] = $est_arr[order_cnt_select];
                        $v[unit_order_cnt] = $est_arr[unit_order_cnt];
                    }

                    //수량: 항목 제외.
                    if ($v[est_order_option_desc]) {
                        $desc_arr = explode("<br>", $v[est_order_option_desc]);
                        if ($desc_arr) {
                            foreach ($desc_arr as $kk => $vv) {
                                if ($vv && strpos($vv, "수량") === false)
                                $est_order_option_desc .= $vv . "<br>";
                            }
                        }
                        $v[est_order_option_desc] = $est_order_option_desc;
                    }
                }

                if ($v[ext_json_data]) {
                    $ext_data = json_decode($v[ext_json_data], 1);

                    //시안요청 주문건 장바구니 노출을 안하기 위해 검수플래그(order_inspection)를 사용함. / 20190118 / kdk
                    if (!array_key_exists('order_inspection', $ext_data)) { //order_inspection 가 없으면 장바구니 노출
                        $cart_item[$key][$k] = $v;
                    }
                }
                else {
                    $cart_item[$key][$k] = $v;
                }
            }
        }
    }

    $cart->item = $cart_item;
    //debug($cart->item);
}

// 옵션변경 체크 210714 jtkim
if(count($cart->item) > 0){
  forEach($cart->item as $k => $v){
    forEach($v as $_k => $_v){
	    $cart->item[$k][$_k]['change_add_options'] = (count(getGoodsAddOption($_v[goodsno])) > 0) ? true : false;
    }
  }
}

// if ($cfg[skin_theme] == "M2" && $cfg[ssl_use] == 'Y'){
//    $ssl_action = "https://".$_SERVER['HTTP_HOST']."/order/orderpayment.php";
//    $tpl->assign('ssl_action', $ssl_action);
// }

if($cfg[ssl_use] == "Y" && ( $cfg[skin_theme]== "M2" || $cfg[skin_theme]== "P1" ))  {
	// 서비스도메인과 현재 접속한 도메인이 같을경우 ssl은 접속한 도메인으로 변경
	if(trim($cfg[urlService]) && $_SERVER[HTTP_HOST] == $cfg[urlService])
		$ssl_action = "https://".$_SERVER[HTTP_HOST]."/order/orderpayment.php";
	// 서비스도메인과 현재 접속한 도메인이 다른경우 ssl은 ssl설정url로 처리
	else
		$ssl_action = $cfg[ssl_url]."/order/orderpayment.php";

   $tpl->assign('ssl_action', $ssl_action);
}

//절사 표시문구 190530 jtkim
$cutmoney_res = $cart->setCuttingMoneyText();
//절사 설정 190610 jtkim
$cutmoney_cfg = $cart->setCuttingCfg();

// M2스킨의 경우 최종수정날짜 시간 삭제(Y-m-d) 표시
if($cfg[skin_theme] == "M2"){
   if($cart->item){
      foreach($cart->item as $idx => $v){
         foreach($v as $idx2 => $vv){
            if($vv['updatedt']){ $cart->item[$idx][$idx2]['updatedt'] = date("Y-m-d", strtotime($vv['updatedt']));
            }
         }
      }
   }
}


if ($cfg[sns_login]) $cfg[sns_login] = unserialize($cfg[sns_login]);
//debug($cfg[sns_login]);

if ($cfg[skin] == "m_default") $tpl->define('itembox', "module/itembox.htm");

if($_COOKIE['in_cart_script']){
   // 쿠키값 생성시 자동 백슬러시 설정이 있을시 백슬러시 제거.
   if(get_magic_quotes_gpc()){
      $tpl->assign('in_cart_script', stripslashes($_COOKIE['in_cart_script']));
   }else{
      $tpl->assign('in_cart_script', $_COOKIE['in_cart_script']);
   }

   setCookie('in_cart_script', '', time() - 1, '/', $_SERVER[SERVER_NAME]);
   // unset($_COOKIE['in_cart_script']);
}

// kidsnote의 경우 장바구니 페이지 에서 센터 리스트 선택하는 selectbox 필요 211113 jtkim&standi
if ($cid == "kidsnote") {
    $kn_centerList = array();
    if ($_COOKIE['kidsnote_access_token']) {
        $kidsnote_client_url = $cfg[sns_login][kidsnote_client_url] . "o/token/";
        $kidsnote_client_id = $cfg[sns_login][kidsnote_client_id];
        $kidsnote_client_secret = $cfg[sns_login][kidsnote_client_secret];
        $kidsnote_redirect_uri = "http://" . $_SERVER['SERVER_NAME'] . "/_oauth/callback_kidsnote.php";
        $kidsnote_code = $_GET['code'];

        $kidsnote_me_url = $cfg[sns_login][kidsnote_client_url] . "v1/me/";
        $kidsnote_me_center_url = $cfg[sns_login][kidsnote_client_url] . "v1/me/center";
        $kidsnote_me_employments_url = $cfg[sns_login][kidsnote_client_url] . "v1/me/employments";
        $header_data = array(
            'Content-Type' => 'application/x-www-form-urlencoded'
        );

        $header_data[] = 'Content-Type: application/json';
        $header_data[] = 'Authorization: ' . $_COOKIE['kidsnote_token_type'] . " " . $_COOKIE['kidsnote_access_token'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $kidsnote_me_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $me_type = curl_exec($ch);
        curl_close($ch);
        $me_type_arr = json_decode($me_type, true);
        //print_r($me_type_arr);
        if ($me_type_arr['error']) {
            //echo "회원정보 가져오기 실패";
        }
        //admin 원장 / teacher 교사 / parent 부모
        if ($me_type_arr['type'] == "teacher") {
            $ch_ = curl_init();
            curl_setopt($ch_, CURLOPT_URL, $kidsnote_me_employments_url);
            curl_setopt($ch_, CURLOPT_HTTPHEADER, $header_data);
            curl_setopt($ch_, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch_, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
            $me_teacher = curl_exec($ch_);
            // $info = curl_getinfo($ch_);
            //print_r($info);
            curl_close($ch_);
            $me_teacher_arr = json_decode($me_teacher, true);
            // print_r($me_teacher_arr);
            // 센터리스트 파싱 이후 $kn_centerList array push
            // api 응답이 오지 않아 확인 필요함
            if(isset($me_teacher_arr)){
                if(count($me_teacher_arr) > 0){
                    // center id 중복 제거
                    forEach($me_teacher_arr as $k){
                        $center_id_duplicate = false;
                        forEach($kn_centerList as $k_){
                            if($k['center_id'] == $k_['center_id']){
                                $center_id_duplicate = true;
                            }
                        }
                        if($center_id_duplicate === false){
                            array_push($kn_centerList, $k);
                        }
                    }
                }
            }

        } else if ($me_type_arr['type'] == "admin") {
            $ch_ = curl_init();
            curl_setopt($ch_, CURLOPT_URL, $kidsnote_me_center_url);
            curl_setopt($ch_, CURLOPT_HTTPHEADER, $header_data);
            curl_setopt($ch_, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch_, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch_, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_, CURLOPT_VERBOSE, true);
            $me_admin = curl_exec($ch_);
            //$info = curl_getinfo($ch_);
            //print_r($info);
            curl_close($ch_);
            $me_admin_arr = json_decode($me_admin, true);
            // print_r($me_admin_arr);
            if(isset($me_admin_arr)){
                array_push($kn_centerList, array('center_id' => $me_admin_arr['id'] , 'center_name' => $me_admin_arr['name']));
            }
        }

    }
    if($_COOKIE['kidsnote_access_token'] && $_COOKIE['kidsnote_token_type']) $tpl->assign("kn_token",$_COOKIE['kidsnote_token_type']." ".$_COOKIE['kidsnote_access_token']);
//        $tpl->assign("kn_centerList",$kn_centerList);

    if($cart->item && count($kn_centerList) > 0){
        foreach($cart->item as $idx => $v){
            foreach($v as $idx2 => $vv){

                $cart->item[$idx][$idx2]['kn_centerList'] = $kn_centerList;

            }
        }
    }
}
$tpl->assign('cart', $cart);
$tpl->assign(array(
   'cutmoney_flag' => $cutmoney_res[flag][value],     //절사 안내문구 표시 여부
   'cutmoney_text' => $cutmoney_res[text][value],     //절사 안내문구 텍스트
   'cutmoney_money' => $cutmoney_res[money][value],   //절사 금액 표시 여부
   'cutmoney_use' => $cutmoney_cfg[c_use][value],     //절사 여부 (1:사용 2:미사용)
   'cutmoney_type' => $cutmoney_cfg[c_type][value],   //절사금액 (1:1의자리 ,2:10의자리, 3:100의자리)
   'cutmoney_op' => $cutmoney_cfg[c_op][value],       //절사방식 (F:버림처리,C:올림처리,R:반올림처리)
   'cutmoney_mod' => $cutmoney_cfg[c_mod][value],     //나머지
));

$tpl->print_('tpl');
?>
