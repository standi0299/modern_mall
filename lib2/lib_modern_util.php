<?
/*
* @date : 20180913
* @author : chunter
* @brief : 적립금 지급 처리 기능 개발
* @request : getDeliveryEndEmoney() 처리 함수를 어디서 호출해야 하는지 생각중..		센터에서 사용하는 파일 존재.
  
  
* @date : 20180904
* @author : chunter
* @brief : 사용자 주문 취소처리 기능 개발
* @request : 현재 개발중. 개발 완료시 코멘트 추가
*/
?>
<?php
	
	//사용자 주문 취소 처리.			20180904		chunter
	//여기부터 신규 개발완료
	function orderCancel($payno)
	{
		$m_order = new M_order();
		$pay = $m_order->getPayInfo($payno);

		if ($pay[paystep]>1 && $pay[paystep]<90) {
			if ($pay[paymethod] == "c" || $pay[paymethod] == "o" || $pay[paymethod] == "oe") {
				orderCalcelWithPG($pay[payno], $pay[pgcode]);
			} else {
				orderCalcelWithPG($pay[payno], "");
			}
		} else if($pay[paystep]==1) {	//미입금
			orderCalcelWithPG($pay[payno], "");
		} else if($pay[paystep]==92) {	//승인완료
			orderCalcelWithPG($pay[payno], $pay[pgcode]);
		} else if($pay[paystep]==91) { 	//승인요청
			orderCalcelWithPG($pay[payno], $pay[pgcode]);
		}
	}


	function orderCalcelWithPG($payno, $tid)
	{
		global $cfg;
		$pgResult = true;
		
		if ($tid)
		{
			if ($cfg[pg][module] == "inipaystdweb")
			$pgResult = orderCalcelINIPayStd($tid, "사용자 주문 취소[$payno]");
		}
		
		if ($pgResult)
		{
			chg_paystep($payno,1,-9,"사용자 주문 취소[$payno]");
			$m_order = new M_order();
			$m_order->setUpdateCancelDate($payno);
			
			//PODS 연동해서 주문상태를 편집중이나 취소로 변경해야 한다.
			$pods = new PODStation('20');			
			$orderList = $m_order->getOrdItemList($payno);
			foreach ($orderList as $key => $value) {
				if ($value[storageid]) {
					$pods->SetOrderCancel($value[storageid]);
				}
			}
		}
	}
	
	//주문 취소시 사용된 적립금을 다시 반환. 적립금 유효기간이 있기에 유효기간을 그대로 넣어준다.
	function orderCancelEmoney($mid,$payno,$memo)
	{		
		global $cid, $cfg;
		
		//적립금 정책을 가져온다.			
		if ($cfg['emoney']) {
			$data = $cfg['emoney'];
		} else {
			$cfg['emoney'] = getCfg("", "emoney");
			$data = $cfg['emoney'];
		}
	
		$m_emoney = new M_emoney();
		$mList = $m_emoney->getEmoneyLogListUseOrder($cid, $mid, $payno);
		foreach ($mList as $key => $value) {
			if ($value[remain_emoney] > 0) {
				//일부 사용한 금액은 사용한 금액만큼만 넣어준다.
				$addEmony = $value[emoney] - $value[remain_emoney];
			} else {
				//전체 사용한 금액은 다 넣어준다.
				$addEmony = $value[emoney];
			}
			
			//개발하다 중단되어서 적립반환처리를 어떻게 하는지 몰라서 주문취소시점으로 적립금 만료일 설정
			if (!$value[expire_date]) {
				if ($data['emoney_expire_day'])
					$value[expire_date] = date("Y-m-d", strtotime(TODAY_Y_M_D."+".$data['emoney_expire_day']." days"));
				else
					$value[expire_date] = date("Y-m-d", strtotime(TODAY_Y_M_D."+15 years"));
			}
			
			//구매시 사용한 적립금 다시 넣어줌.		유효기간은 원래 적립금의 유효기간을 그대로 사용한다.
			$m_emoney->setEmoneyLogInsert($cid, $mid, $memo, $addEmony, "1", "", $value[expire_date]);
		}

		//유효기간 만기한 적립금 정리
		setExpireEmoney($cid, $mid);

   	//회원 총 적립금 올리고
  	$m_emoney->setEmoneyUpdate($cid, $mid);
	}
	
	//이니시스 StandartWeb 취소 처리.
	function orderCalcelINIPayStd($tid, $msg)
	{
		global $cfg;
		require_once($_SERVER["DOCUMENT_ROOT"].'/pg/INIPayStdWeb/libs/INILib.php');
		$mid = $cfg[pg][mid];  			// 가맹점 ID(가맹점 수정후 고정)
		$adminKey = $cfg[pg][admin];
	
		$inipay = new INIpay50();
		$inipay->SetField("inipayhome", $_SERVER["DOCUMENT_ROOT"]."/pg/INIpay50"); // 이니페이 홈디렉터리(상점수정 필요)
		$inipay->SetField("type", "cancel");                            // 고정 (절대 수정 불가)
		$inipay->SetField("debug", "true");                             // 로그모드("true"로 설정하면 상세로그가 생성됨.)
		$inipay->SetField("mid", $mid);                                 // 상점아이디
		$inipay->SetField("admin", $adminKey);
		$inipay->SetField("tid", $tid);                                 // 취소할 거래의 거래아이디
		$inipay->SetField("cancelmsg", $msg);                           // 취소사유

		$inipay->startAction();
		//debug($inipay);
		
		//20190115 픽스토리 테스트 결과 resultcode 가 120000 로 넘어오는데 정상취소 처리가 된것으로 확인됨				20190116		chunter
		if ($inipay->getResult('ResultCode') == "0000" || $inipay->getResult('ResultCode') == "120000")
		{
			//$inipay->getResult('CancelDate'); 
			//$inipay->getResult('CancelTime');
			//$inipay->getResult('ResultMsg');			
			return true;
		}	else
			return false;
	}

//여기끼지 신규 개발중


	//배송완료 적립금 지급처리			20180913		chunter
	function getDeliveryEndEmoney($mid)
	{
		global $cid, $cfg;
		
		if (!$mid) return;
		
		//적립금 정책을 가져온다.	
		if ($cfg['emoney'])
			$data = $cfg['emoney'];
		else {
			$cfg['emoney'] = getCfg("", "emoney");
			$data = $cfg['emoney'];
		}
		
		if ($data[emoney_use_flag] == "Y")
		{
			$m_order = new M_order();
			$m_emoney = new M_emoney();
			$list = $m_emoney->getOrderEndNotEmoney($cid, $mid);
			//debug($list);
			foreach ($list as $key => $value) 
			{
				$item_list = $m_order->getOrdItemInfoForPayno($value[payno]);
				foreach ($item_list as $ikey => $ivalue) 
				{
					$bAddEmoneyFlag = true;		//발송후  적립금 지금.
					if ($ivalue[shipdt])
					{
						if ($data[emoney_send_day])
						{
							$t = strtotime($ivalue[shipdt]);
                     $end_datetime = strtotime("+" . $data[emoney_send_day] . " day", $t);
							if (TODAY_Y_M_D < date('Y-m-d', $end_datetime))
							{
                        //지정일 만큼 지나지 않았으면 처리하지 않음.
								$bAddEmoneyFlag = false;
								break;
							}
						} 
					} else {
						//발송이 안된건이 한개라도 있다면 지급처리 하지 않는다.
						$bAddEmoneyFlag = false;
						break;
					}
				}

				if ($bAddEmoneyFlag) {
					setPayAfterAddEmoney($cid, $mid, $value[payno], "{$value[payno]} 주문 적립");
				}
			}
		}
	}


	//소멸 적립금이 얼마인지 조회
	function getExpireEmoneyTotal($cid, $mem_id, $searchDays = 30)
	{
		$m_emoney = new M_emoney();

		$whereDate = date("Y-m-d 01:01:01", strtotime(TODAY_Y_M_D." +".$searchDays." days"));
		$result = ($searchDays > 0) ? $m_emoney->getSumEmoneyWithDate($cid, $mem_id, $whereDate) : 0;

		return $result;
	}
	
	//유효기간 지난 적립금 사용완료 처리
	function setExpireEmoney($cid, $mem_id)
	{
		//어제 날짜의 적립금을 사용처리한다.		로그인시 한번 호출하도록.
		$m_emoney = new M_emoney();
		$m_emoney->setEmoneyUpdateExpire($cid, $mem_id);
	}

	//회원가입시 적립금 지급
	function setAddNewMember($cid, $mem_id)
	{
		//적립금 정책을 가져온다.
		if ($cfg['emoney'])
			$data = $cfg['emoney'];
		else {
			$cfg['emoney'] = getCfg("", "emoney");
			$data = $cfg['emoney'];
		}

		if ($data['emoney_new_member'] != '' && is_numeric($data['emoney_new_member'])  && $data['emoney_new_member'] > 0)
		{
			setAddEmoney($cid, $mem_id, $data['emoney_new_member'], _('회원가입'));
		}
	}

	//적립금 지급 처리
	function setAddEmoney($cid, $mem_id, $add_emoney, $addMemo = '', $admin_mid = '', $payNo = '', $ordNo = '', $ordSeq = '', $emoney_expire_day = '')
	{
		global $cfg;
		//적립금 정책을 가져온다.			
		if ($cfg['emoney'])
			$data = $cfg['emoney'];
		else {
			$cfg['emoney'] = getCfg("", "emoney");
			$data = $cfg['emoney'];
		}
			

		//유효기간 없을경우 20년으로 고정
		if ($data['emoney_expire_day'])
			$expire_date = date("Y-m-d", strtotime(TODAY_Y_M_D."+".$data['emoney_expire_day']." days"));
		else
			$expire_date = date("Y-m-d", strtotime(TODAY_Y_M_D."+15 years"));

		//넘어온 만료일이 있을 경우 (이벤트) 2017.07.11 / kdk
		if ($emoney_expire_day)
			$expire_date = date("Y-m-d", strtotime(TODAY_Y_M_D."+".$emoney_expire_day." days"));

		//적립금 지급 처리
		$m_emoney = new M_emoney();

		//적립금 로그 넣어줌.
		$m_emoney->setEmoneyLogInsert($cid, $mem_id, $addMemo, $add_emoney, "1", $admin_mid, $expire_date, $payNo, $ordNo, $ordSeq);
      
      //회원 총 적립금 올리고
      $m_emoney->setEmoneyUpdate($cid, $mem_id);
		$m_emoney->setPayEmoenyReceive($cid, $payNo);
	}

	//주문 완료후 적립금 지급 처리.
	function setPayAfterAddEmoney($cid, $mem_id, $payNo, $addMemo = '')
	{
		global $cfg;
			
		//적립금 정책을 가져온다.	
		if ($cfg['emoney'])
			$data = $cfg['emoney'];
		else {
			$cfg['emoney'] = getCfg("", "emoney");
			$data = $cfg['emoney'];
		}

		$m_order = new M_order();
		$p_data = $m_order->getPayInfo($payNo);
		
		//적립금 지급 안한 경우만. 처리.
		if ($p_data[emoney_receive_flag] == "9")
		{
			//기본값이 G		//G : 상품 할인 판매가격, A : 실제 결제금액
			if ($data['emoney_send_type'] == "A")
				$proc_price = $p_data['payprice'];
			else
				$proc_price = $p_data['saleprice'];

			if ($data['emoney_send_ratio'])
				$add_emoney = $proc_price * $data['emoney_send_ratio'] / 100;

			//절사, 1:1자리, 10:10자리	기본값 0
			if ($data['emoney_send_type'] == "1")
				$add_emoney =  floor($add_emoney / 10) * 10;
			if ($data['emoney_send_type'] == "10")
				$add_emoney =  floor($add_emoney / 100) * 100;

			//적립금 지급전에 체크할께 있을경우 여기에 추가 코드를 넣으면 된다.
			$bAddEmoneyFlag = true;

			if ($bAddEmoneyFlag)
			{
				setAddEmoney($cid, $mem_id, $add_emoney, $addMemo, '', $payNo);

				//회원 등급별 적립 추가 지급. 
				$m_member = new M_member();
				$grpsc = $m_member->getGrpScInfoWithMember($cid, $mem_id);
				if ($grpsc > 0)
				{
					$add_emoney = $proc_price * $grpsc / 100;

					//절사, 1:1자리, 10:10자리	기본값 0
					if ($data['emoney_send_type'] == "1")
						$add_emoney =  floor($add_emoney / 10) * 10;
					if ($data['emoney_send_type'] == "10")
						$add_emoney =  floor($add_emoney / 100) * 100;

					setAddEmoney($cid, $mem_id, $add_emoney, _("등급에 따른 주문 추가 적립"), '', $payNo);
				}
			}
		}
	}


   function calcuOrderTotalEmonay($payrice, $saleprice){
      global $cfg, $sess, $cid;

      //적립금 정책을 가져온다.	
			if ($cfg['emoney'])
				$data = $cfg['emoney'];
			else {
				$cfg['emoney'] = getCfg("", "emoney");
				$data = $cfg['emoney'];
			}
		  
      $mem_id = $sess[mid];
      //기본값이 G    //G : 상품 할인 판매가격, A : 실제 결제금액
      if ($data['emoney_send_type'] == "A")
         $proc_price = $payrice;
      else
         $proc_price = $saleprice;

      if ($data['emoney_send_ratio'])
         $add_emoney = $proc_price * $data['emoney_send_ratio'] / 100;

      //절사, 1:1자리, 10:10자리 기본값 0
      if ($data['emoney_send_type'] == "1")
         $add_emoney =  floor($add_emoney / 10) * 10;
      if ($data['emoney_send_type'] == "10")
         $add_emoney =  floor($add_emoney / 100) * 100;
      
      //회원 등급별 적립 추가 지급. 
      $m_member = new M_member();
      $grpsc = $m_member->getGrpScInfoWithMember($cid, $mem_id);
      if ($grpsc > 0)
      {
         $add_grp_emoney = $proc_price * $grpsc / 100;

         //절사, 1:1자리, 10:10자리 기본값 0
         if ($data['emoney_send_type'] == "1")
            $add_grp_emoney = floor($add_emoney / 10) * 10;
         if ($data['emoney_send_type'] == "10")
            $add_grp_emoney = floor($add_emoney / 100) * 100;
      }

      //debug($add_emoney);
      //debug($add_grp_emoney);
      $totemoney = $add_emoney+$add_grp_emoney;
      return $totemoney;
   }

	//주문시 적립금 사용처리.		$bUsePossibleCheck가 false 인경우 DB 처리한다.		사용가능여부를 체크하기 위해서는 true로 넘겨준다.
	function setPayNUseEmoney($cid, $mem_id, $use_emoney, $salePrice, $payno = '', $bUsePossibleCheck = false)
	{
		global $cfg;
		$result = array("code" => "00", "msg" => "");

		setExpireEmoney($cid, $mem_id);			//유효기간 지난건 사용완료 처리
		
		//적립금 정책을 가져온다.
		if ($cfg['emoney'])
			$data = $cfg['emoney'];
		else {
			$cfg['emoney'] = getCfg("", "emoney");
			$data = $cfg['emoney'];
		}

		$proc_price = $salePrice;
		if ($data['emoney_min_orderprice'])
		{
			if ($proc_price < $data['emoney_min_orderprice'])
			{
				$result['code'] = "08";
				$result['msg'] = _("적립금 사용 최소 주문금액보다 적습니다.");
			}
		}

		if ($data['emoney_use_min'])
		{
			if ($use_emoney < $data['emoney_use_min'])
			{
				$result['code'] = "07";
				$result['msg'] = _("적립금 최소 사용액보다 적습니다.");
			}
		}

		if ($data['emoney_use_max'])
		{
			if ($use_emoney > $data['emoney_use_max'])
			{
				$result['code'] = "06";
				$result['msg'] = _("적립금 최대 사용액보다 많습니다.");
			}
		}

		if ($result['code'] == "00")
      {
			$m_emoney = new M_emoney();
			$total_remain_emoney = $m_emoney->getSumEmoneyLog($cid, $mem_id);
			//$total_remain_emoney = 1000;

			if ($total_remain_emoney >= $use_emoney)
			{
				$emoney_list = $m_emoney->getEmoneyLogList($cid, $mem_id);;		//DB 조회로 유효기간내 리스트를 가져온다.

				$used_emoney = array();
				$remain_sub_total = 0;
				foreach ($emoney_list as $key => $value) {
					$remain_sub_total += $value[remain_emoney];

					if ($remain_sub_total < $use_emoney)
					{
						$used_emoney[] = $value[no];
					} else {
						$diff_remain_emoney = $remain_sub_total - $use_emoney;
						//마지막 로그중 부분 사용 처리 진행.
						if ($bUsePossibleCheck == false)
							$m_emoney->setEmoneyUpdatePartUsed($cid, $mem_id, $diff_remain_emoney, $value[no]);
						break;
					}
				}

				if ($bUsePossibleCheck == false)
				{
					if($used_emoney) {
   					//전체  사용처리. 잔액 0
   					$whereNO = implode(',', $used_emoney);
   					$m_emoney->setEmoneyUpdateUsed($cid, $mem_id, $whereNO);
               }
					//사용 이력 추가.
					$m_emoney->setEmoneyLogInsert($cid, $mem_id, _("상품구입시 사용"), $use_emoney, "2", "", "", $payno);
         	  $m_emoney->setEmoneyUpdate($cid, $mem_id);
				}
			} else {
				$result['code'] = "09";
				$result['msg'] = _("적립금 잔액이 부족합니다.");
			}
		}

		return $result;
	}

   function MakeSEOTagFile($cid, $seo_kind, $title, $author, $description, $keyword)
	{
      switch ($seo_kind) 
		{
         case 'main':
            $file_name = "main_searching_keyword.php";
				break;

			case 'cate':
				$file_name = "cate_searching_keyword.php";
				break;

			case 'item':
				$file_name = "item_searching_keyword.php";
				break;
		}

		//펄더 생성
      $dir = DOCUMENT_ROOT . "/data/user_config/$cid/";
      if (!is_dir($dir)) {
         mkdir($dir, 0757);
         chmod($dir, 0757);
      }

		$myfile = fopen($dir.$file_name, "w");
		$txt = "<?php\n";
		$txt .= "\$usr_searching_title = \"" .$title. "\";\n";
		$txt .= "\$usr_searching_author = \"" .$author. "\";\n";
		$txt .= "\$usr_searching_description = \"" .$description. "\";\n";
		$txt .= "\$usr_searching_keyword = \"" .$keyword. "\";\n";
		$txt .= "?>\n";

		fwrite($myfile, $txt);
		fclose($myfile);
	}


	//$rowData -> 카테고리  DB row 또는 상품 item DB row
	function GetSEOTag($cid, $rowData = "")
	{
		global $cfg;

		if (CURRENT_FILE_PATH_NAME == "/goods/list.php")
		{
			$seo_role = "CATEGORY";
			$file_name = dirname(__FILE__) ."/../data/user_config/" .$cid. "/cate_searching_keyword.php";
			if (file_exists($file_name))
			{
				include_once $file_name;
				$usr_searching_title = str_replace("{{"._("상품분류명")."}}", $rowData[catnm], $usr_searching_title);
				$usr_searching_author = str_replace("{{"._("상품분류명")."}}", $rowData[catnm], $usr_searching_author);
				$usr_searching_description = str_replace("{{"._("상품분류명")."}}", $rowData[catnm], $usr_searching_description);
				$usr_searching_keyword = str_replace("{{"._("상품분류명")."}}", $rowData[catnm], $usr_searching_keyword);
			}

		} else if (CURRENT_FILE_PATH_NAME == "/goods/view.php") {
			$seo_role = "ITEM";
			$file_name = dirname(__FILE__) ."/../data/user_config/" .$cid. "/item_searching_keyword.php";
			if (file_exists($file_name))
			{
				include_once $file_name;

				$usr_searching_title = str_replace("{{"._("상품명")."}}", $rowData[goodsnm], $usr_searching_title);
				$usr_searching_author = str_replace("{{"._("상품명")."}}", $rowData[goodsnm], $usr_searching_author);
				$usr_searching_description = str_replace("{{"._("상품명")."}}", $rowData[goodsnm], $usr_searching_description);
				$usr_searching_keyword = str_replace("{{"._("상품명")."}}", $rowData[goodsnm], $usr_searching_keyword);

				$usr_searching_title = str_replace("{{"._("상품검색어")."}}", $rowData[search_word], $usr_searching_title);
				$usr_searching_author = str_replace("{{"._("상품검색어")."}}", $rowData[search_word], $usr_searching_author);
				$usr_searching_description = str_replace("{{"._("상품검색어")."}}", $rowData[search_word], $usr_searching_description);
				$usr_searching_keyword = str_replace("{{"._("상품검색어")."}}", $rowData[search_word], $usr_searching_keyword);

				$usr_searching_title = str_replace("{{"._("상품판매가격")."}}", $rowData[price], $usr_searching_title);
				$usr_searching_author = str_replace("{{"._("상품판매가격")."}}", $rowData[price], $usr_searching_author);
				$usr_searching_description = str_replace("{{"._("상품판매가격")."}}", $rowData[price], $usr_searching_description);
				$usr_searching_keyword = str_replace("{{"._("상품판매가격")."}}", $rowData[price], $usr_searching_keyword);
			}

		} else {
			$seo_role = "MAIN";
			$file_name = dirname(__FILE__) ."/../data/user_config/" .$cid. "/main_searching_keyword.php";
			if (file_exists($file_name))
				include_once $file_name;
		}
		if ($usr_searching_title) $cfg['seo_title'] = $usr_searching_title;
		else $cfg['seo_title'] = $cfg['titleDoc'];

		$cfg['seo_role'] = $seo_role;
		$cfg['seo_title'] = $usr_searching_title;
		$cfg['seo_author'] = $usr_searching_author;
		$cfg['seo_description'] = $usr_searching_description;
		$cfg['seo_keywords'] = $usr_searching_keyword;
	}

	function editListPodsProgress($data) {
		global $r_podskind20;
		$podsApi = new PODStation();

		$result = "";

		$ret_result = "";
		$progress = "";

		if ($data[storageid] && $data[goodsno]!="-1") {
			//2013.12.27 / minks / podskind값을 $data에서 가져옴
			//debug($data[storageid]);
			if (in_array($data[podskind],$r_podskind20) || $data[pods_use]==3) { /* 2.0 상품 */
				$podsApi->setVersion('20');
				$ret_result = $podsApi->GetMultiOrderInfoResult($data[storageid]);
				//debug($ret_result);
				list($flag) = explode("|",substr($ret_result,0,8));
				$ret_result = substr($ret_result,strpos($ret_result,"STORAGE_ID"));
				$ret_result = explode("|^|",$ret_result);

				if ($ret_result) foreach ($ret_result as $v){
					$v = _ilark_vars(substr($v,8));
					$progress = $v[PROGRESS];
				}

				//debug($progress);

			} else {
				$ret_result = $podsApi->GetMultiOrderInfoResult($data[storageid], true);
				//debug($ret_result);
				if ($ret_result)
				{
					foreach ($ret_result as $v) {
						$v = _ilark_vars(substr($v,8));
						$progress = $v[PROGRESS];
					}
				}
			}

			//debug($_PROGRESS);
			$_PROGRESS = explode("/",$progress);
         //2013.12.31 / minks / 에러메시지 수정( 0/1 -> 50% )
         //20140123 by kdk
         if (in_array($data[podskind],array(3030,3040,3041,3042,3043,3050,3051,3052,3060,3110,3112,3053,3054))) {
            //2013.12.26 / minks / 편집중일 때만 몇 퍼센트 편집됬는지 진행률을 나타냄
            if($_PROGRESS[0]==0){
               $result = "0%";
            }
            else{
               $result = round($_PROGRESS[0] / $_PROGRESS[1] * 100)."%";
            }
         }
         else {
            $result = "";
         }
		}
		
		//debug($result);
		return $result;
	}
	
	//후기작성시 적립금 지급
	function setAddReviewWrite($cid, $mem_id, $payno, $goodsnm)
	{
		//적립금 정책을 가져온다.
		if ($cfg['emoney']) {
			$data = $cfg['emoney'];
		} else {
			$cfg['emoney'] = getCfg("", "emoney");
			$data = $cfg['emoney'];
		}

		if ($data['emoney_review_write'] != '' && is_numeric($data['emoney_review_write']) && $data['emoney_review_write'] > 0)
		{
			setAddEmoney($cid, $mem_id, $data['emoney_review_write'], $payno." ".$goodsnm." "._('후기작성'));
		}
	}
	
	//갤러리공개시 적립금 지급
	function setAddOpenGallery($cid, $mem_id, $goodsnm)
	{
		//적립금 정책을 가져온다.
		if ($cfg['emoney']) {
			$data = $cfg['emoney'];
		} else {
			$cfg['emoney'] = getCfg("", "emoney");
			$data = $cfg['emoney'];
		}

		if ($data['emoney_open_gallery'] != '' && is_numeric($data['emoney_open_gallery']) && $data['emoney_open_gallery'] > 0)
		{
			setAddEmoney($cid, $mem_id, $data['emoney_open_gallery'], $goodsnm." "._('갤러리공개'));
		}
	}
?>
