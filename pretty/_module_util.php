<?
//전체 분류 파일 크기
function getStorageSize($cid, $mid = '') {
   $m_pretty = new M_pretty();
   return $m_pretty -> getUseFileTotalSize($cid, $mid);
}

//pods 편집 스토리지 사용량. DB에서 조회		20151207		chunter
function getEditStorageSize($cid, $mid = '') {
   $m_pretty = new M_pretty();
   return $m_pretty->getUseEditFileTotalSize($cid, $mid);
}

//스토리지 사용량, 날짜 체크하기
function checkStorageAccess($cid) {
   if (checkStorageDate($cid) && checkStorageSize($cid))
      return true;
   else
      return false;
}

//스토리지 사용량 체크하기
function checkStorageSize($cid, $addSparePercent = 0) {
   global $cfg;
   $result = false;

   //웹하드 용량이 없다면 연장 결제건이 있는지 체크한다.
   if (!$cfg[storage_size]) {
      $result = getStorageHistoryToCfg($cid);
   }

   //연장 결제건이 있는경우만 용량 체크한다.
   if ($cfg[storage_size]) {
      //업로드 전체 용량 + 편집데이타 전체 용량
      $totalSize = getStorageSize($cid);
      //byte
      //$podsSize = getPodsStorageTotalSize($cfg[podsiteid]);
		$podsSize = getEditStorageSize($cid);				//속도 개선. DB에서 조회		20151207	chunter

      //byte
      $totalSize += $podsSize;

      $diffStorageSize = $cfg[storage_size];

      //여유분을 추가해서 용량 체크하기   20150420    chunter
      if ($addSparePercent > 0)
         $diffStorageSize = $diffStorageSize + ($diffStorageSize * $addSparePercent / 100);

      if (TerabyteToByte($diffStorageSize) >= $totalSize)
         $result = true;
      else
         $result = false;

      if ($result == false) {
         //$cfg[storage_size] = "0";
         //$m_config = new M_config();
         //$m_config->setConfigInfo($cid, 'storage_size', 0);
      }
   }

   return $result;
}

//스토리지 날짜 체크하기
function checkStorageDate($cid) {
   global $cfg;
   $result = false;

   if ($cfg[storage_date] >= TODAY_Y_M_D)
      $result = true;
   else {
      //연장 결제건이 있는지 체크한다.
      $result = getStorageHistoryToCfg($cid);
   }

   //연장 결제건이 없다면 용량도 같이 초기화 한다.      20150428    chunter
   if ($result == false) {
      $cfg[storage_date] = "";
      $cfg[storage_size] = "0";
      $m_config = new M_config();
      $m_config -> setConfigInfo($cid, 'storage_date', "");
      $m_config -> setConfigInfo($cid, 'storage_size', 0);
   }
   return $result;
}

//연장 결제건이 있는지 체크한다.
function getStorageHistoryToCfg($cid) {
   global $cfg;
   $result = false;
   //연장 결제건이 있는지 체크한다.
   $m_pretty = new M_pretty();
   $list = $m_pretty -> getStorageHistoryWithDate($cid, TODAY_Y_M_D);

   $m_config = new M_config();
   foreach ($list as $key => $value) {
      $m_config -> setConfigInfo($cid, 'storage_size', $value[storage_size]);
      $m_config -> setConfigInfo($cid, 'storage_date', $value[storage_edate]);

      //환경변수 즉시 반영
      $cfg[storage_size] = $value[storage_size];
      $cfg[storage_date] = $value[storage_edate];

      $result = true;
      break;
   }
   return $result;
}

//스토리지 결제 가격 계산하기
function calcuStorageAccountPrice($cid, $size, $use_month, $storage_account_kind, $bDetailAccountInfo = false) {
   global $r_pretty_storage_price, $cfg;
   $result = 0;
   $result_info = "";
   //스토리지 추가인 경우.
   if ($storage_account_kind == "1") {
      //이번날 말까지는 1일 계산.
      $end_day = date("t");
      $now_day = date("d");
    
			//사용기간이 지났을 경우 이전 월까지의 요금을 합산하여 계산한다.					20160408		chunter
			//나중에 적용하다 하여 주석 처리로 진행(아래 3줄 처리 부분)		20160408		chunter
			//이전 월까지 정산하도록 변경				20170201		chunter
			//if ($cfg[storage_date] && $cfg[storage_date] < date("Y-m-d"))			
			//	$use_month = datediff(date("Y-m-d"), $cfg[storage_date], "m") + $use_month - 1;			
			//else
			//	$use_month = datediff($cfg[storage_date], date("Y-m-d"), "m");
						
			//if문추가 / 16.07.15 / kjm
			//if문이 있으면 월 계산이 무조건 1월로 되서 제거, 제거 하여도 계산에는 문제가 없다 / 17.03.07 / kjm
			//if($cfg[storage_date])
      	  //$use_month = datediff($cfg[storage_date], date("Y-m-d"), "m");
						
         //이번달 남은 일수 * 일일금액
         //$result = ($end_day - $now_day) * floor($r_pretty_storage_price[$size] / $end_day / 10) * 10;
         //$result = floor($result / 10) * 10;
			
			//남은일수로 하면 손해보는 날이 있어서 한달에서 지난날을 차감철.		지난날짜에 오늘은 포함하지 않는다.			20170201		chunter
			$now_day = $now_day - 1;
			
			//if ($now_day > 0)
			//	$result = $r_pretty_storage_price[$size] - floor($r_pretty_storage_price[$size] / $now_day);
			//else
			//	$result = $r_pretty_storage_price[$size];				
      					
			//1일 이후 결제할 경우.. 1일부터 결제일까지 저장공간이 있다면 한달치를 결제한다.			20170208	chunter
			if ($now_day > 0)
			{
				$m_pretty = new M_pretty();
				$fileTotalSize = $m_pretty->getUseAllFileTotalSize($cid);
				//1G 이상 사용량에 대해서만 과금처리한다. 1G 미만은 일할 계산 처리.
				if ($fileTotalSize > 1000000000)
					$result = $r_pretty_storage_price[$size];
				else 
					$result = $r_pretty_storage_price[$size] - floor($r_pretty_storage_price[$size] / $now_day);
			} else {
				$result = $r_pretty_storage_price[$size];
			}

			$result = floor($result / 10) * 10;


      //1의 자리 버림
      //debug($result);
      
      //기존에 사용 중이다가 기간이 만료되고 다시 결제 하려는 경우 금액 결제에 개월만 나온다 / 17.03.07 / kjm
      if($bDetailAccountInfo && $fileTotalSize > 1000000000)
         $result_info = ($use_month) . _('개월')." (" . ($use_month) . _('개월')." * " . number_format($r_pretty_storage_price[$size]) . _('원').") : " . number_format($r_pretty_storage_price[$size] * ($use_month));
      else {
         if ($bDetailAccountInfo)
            $result_info = ($end_day - $now_day) . _('일')." (" . ($end_day - $now_day) . _('일')." * " . number_format(floor($r_pretty_storage_price[$size] / $end_day / 10) * 10) . _('원').") : " . number_format($result);
   
         $result += $r_pretty_storage_price[$size] * ($use_month - 1);
         if ($bDetailAccountInfo)
            $result_info .= " + " . ($use_month -1) . _('개월')." (" . ($use_month -1) . _('개월')." * " . number_format($r_pretty_storage_price[$size]) . _('원').") : " . number_format($r_pretty_storage_price[$size] * ($use_month -1));
      }

      /*
      //종료일 이후 연장이 가능한 경우 가격 계산 방식
      $remain_month = datediff($cfg[storage_date], date("Y-m-d"), "m");

      //계약 종료일까지는 추가 용량에 대한 금액만 추가
      $add_month = ($use_month - 1) - ($remain_month - 1);
      $result += $r_pretty_storage_price[$size] * $add_month;

      //계약 종료 이후까지 추가경우는 기본 + 추가금액.
      $account_month = $use_month - $add_month;
      $result += (DEFAULT_STORAGE_PRICE * $account_month) + ($r_pretty_storage_price[$size] * $account_month);
      */
   }
   //스토리지 연장인 경우.
   else if ($storage_account_kind == "2") {
      //현재 웹하드를 이용하는 고객인경우 만 연장이 가능하다.    없을 경우는 추가로 재 계산한다.
      if ($cfg[storage_date] && $cfg[storage_date] >= date("Y-m-d")) {
         $result = $r_pretty_storage_price[$size] * $use_month;

         if ($bDetailAccountInfo)
            $result_info = $use_month . _('개월'). " (" . $use_month . _('개월'). " * " . number_format($r_pretty_storage_price[$size]) ._('원'). ") : " . number_format($result);
      } else {
         return calcuStorageAccountPrice($cid, $size, $use_month, "1", $bDetailAccountInfo);
         exit ;
      }
   }

   if ($bDetailAccountInfo)
      $result_info .= "<BR> = " . number_format($result) ."("._('부가세 포함').")";

   if ($bDetailAccountInfo)
      return $result_info;
   else
      return $result;

}

//스토리지 사용량 및 기간 변경, 내역 저장.    $storage_account_kind - 1:추가, 2:연장
function updateStorageData($cid, $size, $use_month, $storage_account_kind, $account_price, $tno, $ordr_payno, $account_pay_method = '', $pg_log = '', $account_detail_info = '') 
{
  global $cfg;
  $m_config = new M_config();
  $m_pretty = new M_pretty();

  //스토리지 추가인 경우.
  if ($storage_account_kind == "1") 
  {

    if (!$cfg[storage_date] || $cfg[storage_date] < date("Y-m-d"))
    {
      //오늘부터 기간과 용량을 바로 연장한다.   //31일 버그 때문에 문제다..하하.. 해당월의 첫날로 계산
      $t = strtotime(date('Y-m-') . "01");
      $end_datetime = strtotime("+" . ($use_month - 1) . " month", $t);
      $end_day = date("t", $end_datetime);
      $end_date = date('Y-m', $end_datetime) . "-" . $end_day;

      //신규 신청인경우만 환경설정에 저장.     20150715    chunter
      $m_config -> setConfigInfo($cid, 'storage_date', $end_date);
      $cfg[storage_date] = $end_date;
    } else {
      $end_date = $cfg[storage_date];
    }

    $m_config -> setConfigInfo($cid, 'storage_size', $cfg[storage_size] + $size);

    //환경변수 갱신
    $cfg[storage_size] += $size;

    //내역 저장
    $m_pretty -> insertStorageHistory($cid, $size, TODAY_Y_M_D, $end_date, $storage_account_kind, $use_month, $account_price, $tno, $ordr_payno, $account_pay_method, $pg_log, $account_detail_info);
   }
  //스토리지 연장인 경우.
  else if ($storage_account_kind == "2")
  {
      //추가 연장 결제가 있는지 확인. 추가 연장 결제가 있는데 또 연장한 경우 시작일을 연장 끝나는 날부터 해야함
      //스토리지 날짜가 현재 날짜보다 이후인 경우만 계산
      if ($cfg[storage_date] && $cfg[storage_date] >= date("Y-m-d")) {
         $list = $m_pretty -> getStorageHistoryWithDate($cid, $cfg[storage_date], true);
         foreach ($list as $key => $value) {
            $storage_edate = $value[storage_edate];
            //마지막 종료일을 찾는다.
         }

         if ($storage_edate)
            //$t = strtotime($storage_edate);
            $t = strtotime(substr($storage_edate, 0, 8) . "01");
         //31일 버그 때문에 문제다..하하.. 해당월의 첫날로 계산
         else
            //$t = strtotime($cfg[storage_date]);     //31일 버그 때문에 문제다..하하.. 해당월의 첫날로 계산 
            $t = strtotime(substr($cfg[storage_date], 0, 8) . "01");
      } else {
         //스토리지 일자가 넘어갔을 경우 연장아닌 추가로 처리한다.   //연장으로 하면 날짜 계산쪽에 문제가 생긴다.
         $cfg[storage_size] = 0;
         updateStorageData($cid, $size, $use_month, "1", $account_price, $tno, $ordr_payno, $account_pay_method, $pg_log, $account_detail_info);
         return;
      }

      //끝나는 날부터 결제한 개월수 만큼 기간 연장, 용량 연장. 현재 설정에 즉시 반영하지 않는다.
      $start_datetime = strtotime("+1 month", $t);
      $start_date = date('Y-m', $start_datetime) . "-01";
      //종료일 다음달 1일부터

      $end_datetime = strtotime("+" . $use_month . " month", $t);
      $end_day = date("t", $end_datetime);
      $end_date = date('Y-m', $end_datetime) . "-" . $end_day;
      //개월수 마지막날까지
      
      //계약 날짜 즉시 반영하도록 수정 / 16.01.20 / kjm
      //일단 주석처리
      //$m_config -> setConfigInfo($cid, 'storage_date', $end_date);

      //내역 저장
      $m_pretty -> insertStorageHistory($cid, $size, $start_date, $end_date, $storage_account_kind, $use_month, $account_price, $tno, $ordr_payno, $account_pay_method, $pg_log, $account_detail_info);
   }
}

//승인처리 전에 결제번호에 묶인 모든 주문에 대해 보유 포인트보다 적고 많은지 확인한다.      20150403    chunter
function checkOrderPayPoint($cid, $payno_arr) {

   $m_order = new M_order();
   
   if (!is_array($payno_arr))
      $payno_arr[] = $payno_arr;

   foreach ($payno_arr as $key => $payno) {
      $data = $m_order -> getOrdItemList($payno);
      $check_goods = array();
      foreach ($data as $key => $value) {
         $check_goods[$value[goodsno]]++;
      }

      $check_point = getGoodsTotalPoint($cid, $check_goods);
   }

   //debug($check_goods);
   $result = checkMallExpensePoint($cid, $check_point);
   return $result;
}


function checkOrderPayPointForItem($cid, $payno, $ordno, $ordseq) {

  $m_order = new M_order();
  $data = $m_order -> getOrdItemInfo($payno,$ordno, $ordseq);
  $check_goods = array();
  $check_goods[$data[goodsno]]++;

  $check_point = getGoodsTotalPoint($cid, $check_goods);
  //debug($check_point);
  $result = checkMallExpensePoint($cid, $check_point);
  return $result;
}


// 상품들의 모든 차감 포인트 조회하기      20150626    chunter
//$ea 수량추가 / 15.07.02 / kjm
//$ea 수량제거 / 15.07.15 / kjm
function getGoodsTotalPoint($cid, $check_goods) {
   $check_point = 0;

   if (!is_array($check_goods))
      $check_goods_arr[$check_goods] = 1;
   else
      $check_goods_arr = $check_goods;

   if (is_array($check_goods_arr)) {
      $m_goods = new M_goods();
      foreach ($check_goods_arr as $key => $value) {
         if ($key > 0) {
            //몰별 포인트 차감형태로 변경. 몰별 설정이 있는경우 몰별 포인트 차감이 우선.     20160625
            $proc_point = 0;
            $g_pretty_point = $m_goods->getPrettyPointInfoWithCid($key, $cid);
            if ($g_pretty_point[pretty_point] && $g_pretty_point[pretty_point] > 0)
               $proc_point = $g_pretty_point[pretty_point];
            else {
               $g_data = $m_goods -> getInfo($key);
               if ($g_data[pretty_point] && $g_data[pretty_point] > 0)
                  $proc_point = $g_data[pretty_point];
            }

            //$g_data = $m_goods -> getInfo($key);
            //포인트를 수량만큼 곱해준다 / 15.07.02 / kjm
            $check_point += $proc_point * $value;
         }
      }
   }
   return $check_point;
}


//몰의 가용 포인트 체크하기. 기본 0 포인트 상품이 있을경우     20150626    chunter
function checkMallExpensePoint($cid, $check_point)
{
  $result = true;
  if ($check_point > 0) 
  {
    $m_mall = new M_mall();

    $mall_data = $m_mall -> getInfo($cid);
    if (($mall_data[pretty_point] - $check_point) >= 0) {
       $result = true;
    } else {
       $result = false;
    }
  }

  return $result;
}

//몰의 가용 포인트 체크하기. 포인트가 -1일 경우 바로 승인 처리하도록 한다. / 16.07.19 / kdk
function checkMallMinusPoint($cid)
{
	$result = false;

    $m_mall = new M_mall();

    $mall_data = $m_mall -> getInfo($cid);
    if ($mall_data[pretty_point] == -1) {
       $result = true;
    }  

  	return $result;
}

//결제번호에 대한 주문건들에 대한 실체 포인트 차감 처리. 내역도 같이 저장   return false -> 포인트 부족등으로 실패.
function setOrderPayPointProc($cid, $payno) {
   $m_order = new M_order();
   $m_goods = new M_goods();

   $data = $m_order -> getOrdItemList($payno);
   $check_goods = array();
   foreach ($data as $key => $value) {
      $check_goods[$value[goodsno]]++;
   }

   $check_point = getGoodsTotalPoint($cid, $check_goods);   
   //debug($check_goods);
   //debug($check_point);
   //exit;
   return setOrderAgreementPointProc($cid, $payno, $check_point);
}



//결제 상품별 승인건에 대한 포인트 차감 처리. 내역도 같이 저장   return false -> 포인트 부족등으로 실패.   20150611    elgar
function setOrderPayPointProcForItem($cid, $payno, $ordno, $ordseq) {
  $m_order = new M_order();
  $m_goods = new M_goods();

  $data = $m_order->getOrdItemInfo($payno, $ordno, $ordseq);
     
  //몰별 포인트 차감형태로 변경. 몰별 설정이 있는경우 몰별 포인트 차감이 우선.     20160625
  //$data[ea] 수량추가 / 15.07.02 / kjm
  $check_point = getGoodsTotalPoint($cid, $data[goodsno], $data[ea]);
  return setOrderAgreementPointProc($cid, $payno, $check_point);
}


//포인트 차감처리를 실제 처리 함수        20150611      chunter
function setOrderAgreementPointProc($cid, $payno, $check_point)
{
  $result = true;
  if ($check_point > 0) {
    $m_order = new M_order();
    $m_mall = new M_mall();
    $m_pretty = new M_pretty();
    $mall_data = $m_mall -> getInfo($cid);
    if (($mall_data[pretty_point] - $check_point) >= 0)
    {
       $mall_point = $mall_data[pretty_point] - $check_point;
       $pay = $m_order -> getPayInfo($payno);
       $m_mall -> mallPrettyPointUpdate($cid, $mall_point);
       //$m_pretty -> insertPointHistory($cid, $g_data[pretty_point], '11', $mall_point, $pay[mid], $pay[orderer_name], '', $payno);
       $m_pretty -> insertPointHistory($cid, $check_point, '11', $mall_point, $pay[mid], $pay[orderer_name], '', $payno);

       $result = true;
    } else {
       $result = false;
    }
  }
  return $result;
}

//포인트 잔액 체크하기..
function checkPoint($cid, $account_point) {
   $m_mall = new M_mall();
   $data = $m_mall -> getInfo($cid);

   if ($data) {
      if ($data[pretty_point] >= $account_point)
         return true;
      else {
         msg(_('합성처리 포인트가 부족합니다. 몰 관리자에게 문의주세요.'), -1);
      }
   } else {
      msg(_('몰정보가 올바르지 않습니다.'), -1);
   }
}

//유치원서비스 몰의 point 차감및 내역 저장.
function setPointProcess($cid, $account_point, $account_flag, $member_id, $member_name, $memo = '', $payno = '', $tno = '', $account_pay_method = '', $account_price = '0') {
   $account_point_unit = $account_point;
   //포인트 사용인경우 -(마이너스) 처리
   if ($account_flag < 20) {
      $account_point_unit = -1 * $account_point;
      $mid = $member_id;
      $m_name = $member_name;
   }

   $m_mall = new M_mall();
   $data = $m_mall -> getInfo($cid);

   if ($data) {
      $mall_point = $data[pretty_point] + $account_point_unit;

      if ($account_flag < 20) {
         if ($data[pretty_point] < $account_point)
            msg(_('합성처리 포인트가 부족합니다. 몰 관리자에게 문의주세요.'), -1);
      }

      $m_mall -> mallPrettyPointUpdate($cid, $mall_point);

      $m_pretty = new M_pretty();
      $m_pretty -> insertPointHistory($cid, $account_point, $account_flag, $mall_point, $mid, $m_name, $memo, $payno, $tno, $account_pay_method, $account_price);
   } else {
      msg(_('몰정보가 올바르지 않습니다.'), -1);
   }
}

//폴더 및 파일 삭제 (원아 분류 파일 제외)
function deleteFolder($cid, $mid, $folder_ID) {
   $m_pretty = new M_pretty();

   $m_pretty->deleteFolderFiles($cid, $mid, $folder_ID);
   //moveFileToNotGroupWithFolder($cid, $mid, $folder_ID);
   $m_pretty -> deleteFolder($cid, $mid, $folder_ID);
}

//반 삭제 및 파일 삭제처리
function deleteClass($cid, $mid, $class_ID) {
   $m_pretty = new M_pretty();

   $m_pretty->deleteClassFiles($cid, $mid, $class_ID);
   //moveFileToNotGroupWithClass($cid, $mid, $class_ID);
   $m_pretty -> deleteClass($cid, $mid, $class_ID);
}

//원아 삭제시 분류 파일 삭제 처리
function deleteChild($cid, $mid, $child_ID) {
   $m_pretty = new M_pretty();

   $m_pretty->deleteChildFiles($cid, $mid, $child_ID);
   //moveFileToNotGroupWithChild($cid, $mid, $child_ID);
   $m_pretty -> deleteChild($cid, $mid, $child_ID);
}

//폴더내 파일 삭제
function deleteChildFile($cid, $mid, $child_ID, $file_id_arr) {
   moveFileToNotGroupWithChildFile($cid, $mid, $child_ID, $file_id_arr);
}

//원아분류내 파일 삭제
function deleteFolderFile($cid, $mid, $folder_ID, $file_id_arr) {
   moveFileToNotGroupWithFolderFile($cid, $mid, $folder_ID, $file_id_arr);
}

//원아 삭제시 맵핑 파일들을 모두 미분류 상태로 변경하기      20150402  chunter
function moveFileToNotGroupWithChild($cid, $mid, $child_ID) {
   $m_pretty = new M_pretty();
   $m_pretty -> moveChildImageToNotGroupFolder($cid, $mid, $child_ID);
}

//반 삭제시 원아에 연결되 모든 맵핑 파일들을 모두 미분류 상태로 변경하기        20150402  chunter
function moveFileToNotGroupWithClass($cid, $mid, $class_ID) {
   $m_pretty = new M_pretty();
   $data = $m_pretty -> getChildList($cid, $mid, $class_ID);
   foreach ($data as $key => $value) {
      moveFileToNotGroupWithChild($cid, $mid, $value[ID]);
   }
}

//폴더내 파일 맵핑을 모두 미분류로 폴더로  이동시키기.        20150402  chunter
function moveFileToNotGroupWithFolder($cid, $mid, $folder_ID) {
   $m_pretty = new M_pretty();
   $m_pretty -> moveFolderImageToNotGroupFolder($cid, $mid, $folder_ID);
}

//속한 폴더내 파일의 맵핑 정보를 미분류 폴더로 이동한다        20150402  chunter
function moveFileToNotGroupWithFolderFile($cid, $mid, $folder_ID, $file_id_arr) {
   $m_pretty = new M_pretty();
   foreach ($file_id_arr as $key => $value) {
      $file_ids[] = "'" . $value . "'";
      //sql 조건을 위해 '' 를 붙여준다
   }
   $m_pretty -> moveFolderFilesImageToNotGroupFolder($cid, $mid, $folder_ID, $file_ids);
}

//원아내 속한 파일의 맵핑 정보를 미분류 폴더로 이동한다        20150402  chunter
function moveFileToNotGroupWithChildFile($cid, $mid, $child_ID, $file_id_arr) {
   $m_pretty = new M_pretty();
   foreach ($file_id_arr as $key => $value) {
      $file_ids[] = "'" . $value . "'";
      //sql 조건을 위해 '' 를 붙여준다
   }
   $m_pretty -> moveChildFilesImageToNotGroupFolder($cid, $mid, $child_ID, $file_ids);
}

//유치원 삭제시 관련 데이타 모두 삭제 (파일, 원아, 행사분류, 원아분류)
function deleteAllData($cid, $mid) {
   //파일 삭제전 삭제 대상 목으로 테이블로 저장
   //$sql = "insert into tb_pretty_file_delete(file_id, file_full_path, thum1_file_full_path, thum2_file_full_path) select file_id, server_name,server_thum1_name,server_thum2_name from tb_pretty_file where cid = '$cid' and mid = '$mid'";
   $sql = "insert into tb_pretty_file_delete(file_id, regist_flag, regist_date) select file_id, 'Y', now() from tb_pretty_file where cid = '$cid' and mid = '$mid'";

   $sql = "delete from tb_pretty_cart_mapping where class_ID in (select ID from tb_pretty_class where cid = '$cid' and mid = '$mid')";

   $sql = "delete from tb_pretty_file_child_mapping where child_ID in (select ID from tb_pretty_child where cid = '$cid' and mid = '$mid')";

   $sql = "delete from tb_pretty_file_folder_mapping where folder_ID in (select ID from tb_pretty_folder where cid = '$cid' and mid = '$mid')";

   $sql = "delete from tb_pretty_order_mapping where class_ID in (select ID from tb_pretty_class where cid = '$cid' and mid = '$mid')";

   $sql = "delete from tb_pretty_class where cid = '$cid' and mid = '$mid'";
   $sql = "delete from tb_pretty_child where cid = '$cid' and mid = '$mid'";

   $sql = "delete from tb_pretty_file where cid = '$cid' and mid = '$mid'";

   $sql = "delete from tb_pretty_folder where cid = '$cid' and mid = '$mid'";
   $sql = "delete from tb_pretty_teacher where cid = '$cid' and mid = '$mid'";

}

//서버 파일명으로 파일 삭제 유무를 판단한다.
function checkDeleteFileWithFilename($server_file_name) {

   $bDeleteFlag = true;

   $sql = "select * from tb_pretty_file where server_name = '$server_file_name'";
   $list = $db -> listArray($sql);
   foreach ($list as $key => $value) {
      if ($value[ID]) {
         if (checkDeleteFile($value[ID]) == FALSE) {
            $bDeleteFlag = false;
            break;
         }
      }
   }
   return $bDeleteFlag;
}

//원아분류도 없고 폴더 분류도 없는 파일인지 체크
function checkDeleteFile($file_id) {
   //분류 정보가 없는 파일은 삭제가 기본값.
   $bDeleteFlag = true;

   //폴더를 지운경우 삭제 파일로 간주.
   $sql = "select a.*, b.*, b.ID as folder_tbl_ID from tb_pretty_file_folder_mapping a left join tb_pretty_folder b on B.ID = a.folder_ID
            where a.file_id = '$file_id'";
   $list = $db -> listArray($sql);
   foreach ($list as $key => $value) {
      if ($value[folder_tbl_ID] || $value[folder_name]) {
         $bDeleteFlag = FALSE;
         break;
      }
   }

   if ($bDeleteFlag == FALSE) {
      //원아를 지운경우 삭제 파일로 간주.
      $sql = "select a.*, b.*, b.ID as child_tbl_ID from tb_pretty_file_child_mapping a left join tb_pretty_child b on B.ID = a.child_ID
                where a.file_id = '$file_id'";
      $list = $db -> listArray($sql);
      foreach ($list as $key => $value) {
         if ($value[child_tbl_ID] || $value[child_name]) {
            $bDeleteFlag = FALSE;
            break;
         }
      }
   }

   return $bDeleteFlag;
}

//기본 폴더 만들기
//매년 추가된다 / 15.04.14 / kjm
function makeBasicFolder($service_year, $cid, $mid) {
   global $db;
/*
   $sql = "insert into tb_pretty_folder (service_year, folder_name, folder_kind, regist_date, orderby, mid, cid)
            values
            ('$service_year', '"._('5월')."', 'D', now(), '1', '$mid', '$cid'),
            ('$service_year', '"._('6~7월')."', 'D', now(), '2', '$mid', '$cid'),
            ('$service_year', '"._('8~9월')."', 'D', now(), '3', '$mid', '$cid'),
            ('$service_year', '"._('10월')."', 'D', now(), '4', '$mid', '$cid'),
            ('$service_year', '"._('11월')."', 'D', now(), '5', '$mid', '$cid'),
            ('$service_year', '"._('12월')."', 'D', now(), '6', '$mid', '$cid'),
            ('$service_year', '"._('1월')."', 'D', now(), '7', '$mid', '$cid'),
            ('$service_year', '"._('2월')."', 'D', now(), '8', '$mid', '$cid'),

            ('$service_year', '"._('생일잔치')."', 'F', now(), '1', '$mid', '$cid'),
            ('$service_year', '"._('어린이날')."', 'F', now(), '2', '$mid', '$cid'),
            ('$service_year', '"._('소풍가는날')."', 'F', now(), '3', '$mid', '$cid'),
            ('$service_year', '"._('학예회발표')."', 'F', now(), '4', '$mid', '$cid'),
            ('$service_year', '"._('재롱잔치')."', 'F', now(), '5', '$mid', '$cid'),
            ('$service_year', '"._('공부하기')."', 'F', now(), '6', '$mid', '$cid'),
            ('$service_year', '"._('크리스마스')."', 'F', now(), '7', '$mid', '$cid'),
            ('$service_year', '"._('기념일')."', 'F', now(), '8', '$mid', '$cid'),
            ('$service_year', '"._('기타')."', 'F', now(), '9', '$mid', '$cid')
            ";
   */
   //* 3뎁스 기본 폴더
   $sql = "insert into tb_pretty_folder (service_year, folder_name, folder_kind, regist_date, orderby, mid, cid, mapping_kind_type, season_code)
            values
            ('$service_year', '"._('생일잔치')."', 'D', now(), '0', '$mid', '$cid', 'N', '$_COOKIE[season_code]'),
            ('$service_year', '"._('어린이날')."', 'D', now(), '1', '$mid', '$cid', 'N', '$_COOKIE[season_code]'),
            ('$service_year', '"._('기타')."', 'D', now(), '2', '$mid', '$cid', 'N', '$_COOKIE[season_code]')
            ";
   
   $db -> query($sql);

   makeFolderKindOrder($service_year, $cid, $mid);
}

//휴지통폴더
//회원 가입(or 승인)시 한번만 추가된다 / 15.04.14 / kjm
function makeTrashFolder($service_year, $cid, $mid) {
   global $db;

   $sql = "insert into tb_pretty_folder (folder_name, folder_kind, regist_date, mid, cid)
            values
            ('"._('휴지통')."', 'U', now(), '$mid', '$cid')
            ";
   $db -> query($sql);
}

function makeFolderKindOrder($service_year, $cid, $mid) {
   global $db;

   $sql = "insert into tb_pretty_folder_kind_order set
             service_year = '$service_year',
             orderby = 'D|F',
             cid = '$cid',
             mid = '$mid'
           on duplicate key update
            orderby = 'D|F'
           ";
   $db -> query($sql);
}

function makeWebhardPieGraphData($cid,$webhardTotalSize = 0,  $podsSizeArr = '') {
   global $cfg;
   $m_pretty = new M_pretty();
   //파일 업로드 전체용량
   $total_size = $cfg[storage_size] * 1024;
   //설정은 TByte => GByte 로 변환

   //파일 업로드 전체사용용량
   if ($webhardTotalSize > 0)
    $upload_size = $webhardTotalSize;
   else
    $upload_size = $m_pretty -> getUseFileTotalSize($cid);
   //$upload_size = byteFormat($upload_size, "G", 2, false);
   $upload_size = byteFormat_1000($upload_size, "G", 2, false);
   //테스트 화면의 원할한 차트를 위해 300을 더한다. 실제 서비스때는 지워야함.
   //편집주문 전체용량     //pods 연동안으로 처리(2 )

   /*
   if (!$podsSizeArr)
      $podsSizeArr = getPodsStorageSize($cfg[podsiteid]);
   //$edit_size1 = getPodsStorageSize($cfg[podsiteid]);

   $edit_size1 = 0;
   if (is_array($podsSizeArr)) {
      foreach ($podsSizeArr as $key => $value) {
         $edit_size1 += $value;
      }
   }
	 */
   $edit_size1 = $m_pretty->getUseEditFileTotalSize($cid);		//속도개선. DB에서 조회		20151207

   $edit_size1 = byteFormat_1000($edit_size1, "G", 2, false);
   //GByte 로 변환.     //임시로 300 더하기.

   //$edit_size2 = 200000000000;
   $edit_size = $edit_size1 + $edit_size2;
   //$edit_size = byteFormat($edit_size, "G", 2, false);

   //남은 용량
   $remide_size = $total_size - $upload_size - $edit_size;

   //chart 에서 사용할 자바스크립트형 변수로 만들기
   $pie_graph = "interactivePieChartData[0] = { label: \""._('전체사용량')." : " . $total_size . " G\", data: 0, color: '#4572A7' };";
   $pie_graph .= "interactivePieChartData[1] = { label: \""._('사진업로드')." : " . $upload_size . " G\", data: $upload_size, color: '#80699B' };";
   $pie_graph .= "interactivePieChartData[2] = { label: \""._('상품편집보관')." : " . $edit_size . " G\", data: $edit_size, color: '#AA4643' };";
   $pie_graph .= "interactivePieChartData[3] = { label: \""._('사용가능용량')." : " . $remide_size . " G\", data: $remide_size, color: '#3D96AE' };";
   //$pie_graph = "data[0] = { label: \"전체사용량\", data: 0, color: #3D96AE}";

   return $pie_graph;
}



  function inspectionRejectProcPayData($cid, $mid, $payno, $ordno, $ordseq, $emoney_ratio)
  {
    global $db;
    $m_studio = new M_studio();
    list($cntChk) = $db->fetch("select count(payno) as cnt from exm_ord_item where payno = '$payno'",1);


    $payData = $m_studio->getPayData($payno);
    $m_studio->payBackPayData($cid,$mid,$payData[pay_credit2_deposit],$payData[pay_credit2_credit]);

    $depositHistory[cid] = $cid;
    $depositHistory[mid] = $mid;
    $depositHistory[payno] = $payno;
    $depositHistory[credit2_payprice] = $payData[sale_price] - $payData[pay_emoney];
    $depositHistory[pay_flag] = 5;
    $depositHistory[memo] = _('관리자 주문반려');
    $m_studio -> depositHistory($depositHistory);
    
    //적립금 사용했다면
    if ($payData[pay_emoney] > 0)
      set_emoney($mid, _('관리자 주문반려'), $payData[pay_emoney], $payno);        


    //한주문에 1건이상 상품인경우 주문 반려 처리
    if($cntChk >  1)
    {
      $db->query("delete from tb_pay_data where payno = '$payno'");     //이전 주문 관련 데이타 삭제.
      //반려한 상품건을 제외한 나머지 상품들로 결제금액 재조정.
      $payprice = $m_studio->paybackOrd($payno, $ordno, $ordseq);

      //반려 1건 제외한 나머지 상품들로 재결제
      $data_c = $m_studio -> order_calc($cid, $mid, $payprice, $emoney_ratio);
      $paybackCreditMember = $data_c[member_credit2];

      $depositHistory[cid] = $cid;
      $depositHistory[mid] = $mid;
      $depositHistory[payno] = $payno;
      $depositHistory[credit2_payprice] = -($data_c[pay_credit2_credit] + $data_c[pay_credit2_deposit]);
      $depositHistory[pay_flag] = 6;
      $depositHistory[memo] = _('관리자 주문반려 재결제');
      $m_studio->depositHistory($depositHistory);

      if ($data_c[pay_emoney] > 0)
        set_emoney($mid, _('관리자 주문반려 재결제'), -$data_c[pay_emoney], $payno);

      //신용거래2 결제 방식 거래금액 history 저장.
      if($data_c[pay_emoney]) $loop[emoney] = 'e';
      if($data_c[pay_credit2_credit]) $loop[credit] = 'de';
      if($data_c[pay_credit2_deposit]) $loop[deposit] = 'da';
      $pay_kinds = implode(",", $loop);
      $paydata[payno] = $payno;
      $paydata[cid] = $cid;
      $paydata[mid] = $mid;
      $paydata[sale_price] = $payprice;
      $paydata[paymethod] = 'd';
      $paydata[pay_kinds] = $pay_kinds;
      $paydata[pay_emoney] = $data_c[pay_emoney];
      $paydata[pay_credit1] = $data_c[pay_credit1];
      $paydata[pay_credit2_credit] = $data_c[pay_credit2_credit];
      $paydata[pay_credit2_deposit] = $data_c[pay_credit2_deposit];
      $paydata[pay_price] = $pay_price;
      $m_studio->payData($paydata);

      //예치금 재조정 처리..
      $db->query("update exm_member set credit_member = '$paybackCreditMember' where cid = '$cid' and mid = '$mid'");
    }
  }


//printgroup 서비스 결제 가격 계산하기 2016.06.17 by kdk
function calcuServiceAccountPrice($cid, $use_month, $bDetailAccountInfo = false) {
   	global $r_printgroup_service_month;
   	$result = 0;
   	$result_info = "";
	
	$result = $r_printgroup_service_month[$use_month];
   	$result_info = $use_month . _('개월'). " (" . $use_month . _('개월'). " * " . number_format($result) ._('원'). ") : " . number_format($result);
   
   if ($bDetailAccountInfo)
      $result_info .= "<BR> = " . number_format($result) ." ("._('부가세 포함').")";

   if ($bDetailAccountInfo)
      return $result_info;
   else
      return $result;
}

//printgroup 서비스 기간 변경, 내역 저장.    $storage_account_kind - 1:추가, 2:연장
function updateServiceData($cid, $use_month, $storage_account_kind, $account_price, $tno, $ordr_payno, $account_pay_method = '', $pg_log = '', $account_detail_info = '') 
{
  	$m_mall = new M_mall();
  	$m_pretty = new M_pretty();

	$mall_data = $m_mall -> getInfo($cid);
	$mall_data[printgroup_expire_date];

	if ($mall_data[printgroup_expire_date]) {
		$start_date = $mall_data[printgroup_expire_date];
		$end_date = date("Y-m-d", strtotime($start_date." +" . $use_month . " month"));
		//debug($start_date);
		//debug($end_date);
	}
	else {
		$start_date = date("Y-m-d");
		$end_date = date("Y-m-d", strtotime(date("Y-m-d")." +" . $use_month . " month"));
		//debug($start_date);
		//debug($end_date);
	}
  	
  	//서비스 기간 저장	
	$m_mall -> mallPrintgroupExpireDateUpdate($cid, $end_date);
  	
  	//내역 저장
  	$m_pretty -> insertStorageHistory($cid, "", $start_date, $end_date, $storage_account_kind, $use_month, $account_price, $tno, $ordr_payno, $account_pay_method, $pg_log, $account_detail_info);
}
?>