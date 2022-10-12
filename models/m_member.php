<?php
/**
 * service_config
 * 2013.12.05 by chunter
 */

class M_member {
    var $db;
    function M_member() {
        $this -> db = $GLOBALS[db];
    }

    //미승인 회원수 구하기     20150403    chunter
    function getNotAcceptMemberCount($cid, $addWhere = '') {
        list($m_cnt) = $this->db->fetch("select count(mid) as cnt from exm_member where cid='$cid' and state='1' $addWhere", 1);
        return $m_cnt;
    }

		//회원 등급별 할인율 가져오기
    function getGrpDcInfo($grpno) {
        list($grpdc) = $this -> db -> fetch("select grpdc from exm_member_grp where grpno = '{$grpno}'", 1);

        return $grpdc;
    }

		//회원 등급별 적립률 가져오기
		function getGrpScInfo($grpno) {
        list($grpsc) = $this -> db -> fetch("select grpsc from exm_member_grp where grpno = '{$grpno}'", 1);

        return $grpsc;
    }

		// exm_member_grp 테이블에도 cid 조건 추가. 191212 jtkim
		function getGrpScInfoWithMember($cid, $mid) {
        list($grpsc) = $this -> db -> fetch("select b.grpsc from exm_member a, exm_member_grp b  where a.cid='{$cid}' and b.cid = '{$cid}' and a.mid='{$mid}' and a.grpno = b.grpno", 1);
        // echo "select b.grpsc from exm_member a, exm_member_grp b  where a.cid='{$cid}' and a.mid='{$mid}' and a.grpno = b.grpno";
        return $grpsc;
    }


    function getGrpInfo($grpno) {
      $sql = "select grpdc from exm_member_grp where grpno = '$grpno' and base = 1";
      return $this->db->fetch($sql);
    }

    //20150211  chunter
    function getGrpList($cid, $orderby='', $limit='') {
        $sql = "select * from exm_member_grp where cid = '$cid' $orderby $limit";
        //echo $sql;
        return $this -> db -> listarray($sql);
    }

    function getGrpGroupByCount($cid) {
        $sql = "select grpno, count(*) cnt from exm_member where cid = '$cid' group by grpno";
        //echo $sql;
		//return $this -> db -> fetch($sql);

		$res = $this -> db -> query($sql);
		while ($data = $this -> db -> fetch($res)) $cnt[$data[grpno]] = $data[cnt];
		return $cnt;
    }

    //20150211  chunter
    function getBusinessList($cid) {
        $sql = "select * from exm_business where cid = '$cid' order by bsort";
        //echo $sql;
        return $this -> db -> listarray($sql);
    }


    function getBusinessInfo($cid, $bid) {
        $sql = "select * from exm_business where cid = '$cid' and bid = '$bid'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }


    function getInfo($cid, $mid) {
        $sql = "select * from exm_member where mid='$mid' and cid='$cid'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }

    //20150211  chunter
    function getInfoWithGrp($cid, $mid) {
        $sql = "select a.*, b.grpnm, b.grplv from exm_member a left join exm_member_grp b on a.grpno = b.grpno and a.cid = b.cid where a.cid = '$cid' and a.mid='$mid';";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }


    function getSearchInfo($cid, $s_data) {
        $sql = "select * from exm_member where cid='$cid' and (mid='$s_data' or name='$s_data')";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }

    function getList($cid, $addWhere = '') {
        $sql = "select * from exm_member where cid='$cid' $addWhere";
        //echo $sql;
        return $this -> db -> listarray($sql);
    }

   function getListPage($cid, $addWhere = '', $start, $limit, $orderby = "") {
      if (!$orderby) $orderby = " order by name";
         $sql = "select * from exm_member where cid='$cid' $addWhere $orderby limit $start , $limit";
         //echo $sql;
         return $this -> db -> listarray($sql);
   }

   function getListPage_class_child($cid, $addWhere = '', $start, $limit, $orderby = "", $db_table, $service_year) {
      if (!$orderby) $orderby = " order by name asc";
         $sql = "select *, count(b.mid) as cnt from exm_member a, $db_table b where a.cid='$cid' and a.mid = b.mid and b.service_year = $service_year group by b.mid $addWhere $orderby limit $start , $limit";
         //echo $sql;
         return $this -> db -> listarray($sql);
   }

   /*
   function getListPage_child($cid, $addWhere = '', $start, $limit, $orderby = "") {
      if (!$orderby) $orderby = " order by name asc";
         $sql = "select *, count(b.mid) as cnt from exm_member a, tb_pretty_child b where a.cid='$cid' and a.mid = b.mid group by b.mid $addWhere $orderby limit $start , $limit";
         //echo $sql;
         return $this -> db -> listarray($sql);
   }
   */

   function getListPage_cnt($cid, $addWhere = '', $db_table) {
         $sql = "select * from exm_member a, $db_table b where a.cid='$cid' and a.mid = b.mid group by b.mid $addWhere";
         //echo $sql;
         return $this -> db -> listarray($sql);
   }

   function getManagerListPage($cid, $addWhere = '', $start, $limit) {
      //if (!$orderby) $orderby = " order by name asc";
        $sql = "select * from exm_manager where cid='$cid' $addWhere limit $start , $limit";
        //echo $sql;
        return $this -> db -> listarray($sql);
    }


    function getListTotalCnt($cid, $addWhere = '') {
        $sql = "select count(mid) from exm_member where cid='$cid' $addWhere";
        list($m_cnt) = $this->db->fetch($sql, 1);
        return $m_cnt;
    }

    function getTeacherList($cid, $addWhere = '') {
        $sql = "select * from tb_pretty_teacher where cid='$cid' $addWhere";
        //echo $sql;
        return $this -> db -> listarray($sql);
    }

    function getManagerList($cid, $addWhere = '', $limit = '') {
        $sql = "select * from exm_manager where cid='$cid' $addWhere order by manager_name $limit";
        return $this -> db -> listarray($sql);
    }

    function getManagerTotalCnt($cid) {
        $sql = "select count(cid) from exm_manager where cid='$cid'";
        list($m_cnt) = $this->db->fetch($sql, 1);
        return $m_cnt;
    }

    //계약 수량 사용 여부 가져오기 / 15.09.11 / kjm
    //함수 이름이 이상함. 해당 여부를 가져오도록 수정예정 / 15.11.12 / kjm
    function getContractEa($cid, $mid) {
        $sql = "select pretty_contract_ea from exm_member where cid='$cid' and mid = '$mid'";
        list($contract) = $this -> db -> fetch($sql, 1);
        return $contract;
    }

    /*
    function chkManagerBranchFlag($cid) {
        $sql = "select login_id from exm_manager where cid='$cid' and branch_flag = '0'";
        return $this -> db -> fetch($sql);
    }
    */

    function memberCntManager($cid, $manager_no) {
        $sql = "select count(mid) as cnt from exm_member where cid='$cid' and manager_no = '$manager_no'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }

    //정액 회원 기간 연장하기
    function update_fix_date($cid, $mid, $update_month, $update_day = 0) {
        if ($update_month == "")
            $update_month = 0;

        //정액 기간 date 가 null 일경우 오늘 날짜로 초기화.
        $data = $this -> getInfo($cid, $mid);
        if ($data[fix_member_expire_date] == null || $data[fix_member_expire_date] == '') {
            $sql = "update exm_member set fix_member_expire_date=now() where cid='$cid' and mid = '$mid'";
            //echo $sql;
            $this -> db -> fetch($sql);
        }

        //일반회원인 경우 오늘부터 정액기간 연장처리   20140106  chunter
        if ($data[member_type] != "FIX") {
            $sql = "update exm_member set fix_member_expire_date=now() where cid='$cid' and mid = '$mid'";
            //echo $sql;
            $this -> db -> fetch($sql);
        }

        //정액 회원중 기간 끝난경우 오늘 날짜로 초기화. 2015.03.26 by kdk
        if ($data[fix_member_expire_date] < date("Y-m-d h:i:s")) {
            $sql = "update exm_member set fix_member_expire_date=now() where cid='$cid' and mid = '$mid'";
            //echo $sql;
            $this -> db -> fetch($sql);
        }

        $sql = "update exm_member set member_type='FIX', fix_member_update_date=now(), 
        fix_member_expire_date=DATE_ADD(DATE_ADD(fix_member_expire_date,INTERVAL $update_month MONTH), INTERVAL $update_day DAY) 
        where cid='$cid' and mid = '$mid'";
        //echo $sql;
        $this -> db -> fetch($sql);
    }

    //무한정액 회원으로 기간 연장하기
    function update_unlimited_fix_date($cid, $mid) {
        if ($update_month == "")
            $update_month = 0;

        //정액 기간 date 가 null 일경우 오늘 날짜로 초기화.
        $data = $this -> getInfo($cid, $mid);
        if ($data[fix_member_expire_date] == null || $data[fix_member_expire_date] == '') {
            $sql = "update exm_member set fix_member_expire_date=now() where cid='$cid' and mid = '$mid'";
            //echo $sql;
            $this -> db -> fetch($sql);
        }

        //일반회원인 경우 오늘부터 정액기간 연장처리   20140106  chunter
        if ($data[member_type] != "FIX") {
            $sql = "update exm_member set fix_member_expire_date=now() where cid='$cid' and mid = '$mid'";
            //echo $sql;
            $this -> db -> fetch($sql);
        }

        //정액 회원중 기간 끝난경우 오늘 날짜로 초기화. 2015.03.26 by kdk
        if ($data[fix_member_expire_date] < date("Y-m-d h:i:s")) {
            $sql = "update exm_member set fix_member_expire_date=now() where cid='$cid' and mid = '$mid'";
            //echo $sql;
            $this -> db -> fetch($sql);
        }

        $sql = "update exm_member set member_type='FIX', fix_member_update_date=now(), 
        fix_member_expire_date='9999-12-31 23:59:59' 
        where cid='$cid' and mid = '$mid'";
        //echo $sql;
        $this -> db -> fetch($sql);
    }

    //정액 회원중 기간 끝난사람 일괄 변경하기.   20140402    chunter
    function update_member_nor_batch($cid) {
        $subSql = "select * from (select mid from exm_member where member_type='FIX' and fix_member_expire_date < now() and cid='$cid') as tbl1 ";
        $sql = "update exm_member set member_type='NOR' where cid='$cid' and mid in ($subSql)";
        //echo $sql;
        $this -> db -> fetch($sql);
        return true;
    }

    function update_member_nor($cid, $mid) {
        $data = $this -> getInfo($cid, $mid);
        if ($data[member_type] == "FIX") {
            //$sql = "update exm_member set member_type='NOR', fix_member_expire_date=now() where cid='$cid' and mid = '$mid'";
            $sql = "update exm_member set member_type='NOR' where cid='$cid' and mid = '$mid'";
            //echo $sql;
            $this -> db -> fetch($sql);
            return true;
        } else
            return false;
    }

    //회원 타입 넘겨주기    return : NOR, FIX
    function getMemberType($cid, $mid) {
        $member_type = "NOR";
        $data = $this -> getInfo($cid, $mid);
        if ($data[member_type]) {
            //정액회원 종료일시 체크한다.
            if ($data[member_type] == "FIX") {
                $day = dateDiff(substr($data[fix_member_expire_date], 0, 10), date("Y-m-d"), "d", true);

                if ($day > -1)
                    $member_type = $data[member_type];
                else {
                    //일반 회원으로 변경
                    $this -> updateMemberType($cid, $mid, $member_type);
                }
            }
        }
        return $member_type;
    }

    //회원 구분 변경
    function updateMemberType($cid, $mid, $member_type) {
        $sql = "update exm_member set member_type='$member_type' where cid='$cid' and mid = '$mid'";
        $this -> db -> fetch($sql);
    }

    //미수금 및 적립금 변경 처리     20141223  chunter
    function updateMemberAccountData($cid, $mid, $pgPayPrice) {
        //+= 연산자 오류가 발생해서 = 필드명 + 값 으로 변경 / 15.01.09 / kjm
        //$sql = "update exm_member set credit_member += $pgPayPrice, emoney += $addDepositiEmoney where cid='$cid' and mid='$mid'";
        $sql = "update exm_member set credit_member = credit_member + '$pgPayPrice' where cid='$cid' and mid='$mid'";
        $this->db->query($sql);
    }


    //emoney 변경 처리    20150213    chunter
    function setEmoneyUpdate($cid, $mid, $emoney) {
        $sql = "update exm_member set emoney = emoney + $emoney where cid = '$cid' and mid = '$mid'";
        $this->db->query($sql);
    }


    //btob 연동 회원 저장하기   20150213    chunter
    function setBtoBMemberInsert($cid, $mid, $bid, $name, $grpno)
    {
      $sql = "insert into exm_member set
        cid   = '$cid',
        mid   = '$mid',
        bid   = '$bid',
        name  = '$name',
        grpno = '$grpno',
        regdt = now(),
        sort  = -UNIX_TIMESTAMP()
      on duplicate key update
        bid   = '$bid',
        name  = '$name',
        grpno = '$grpno'
      ";
      $this->db->query($sql);
    }

    //로그인후 관련 필드 update     20150213    chunter
    function setLoginDataUpdate($cid, $mid)
    {
      $sql = "update exm_member set cntlogin=cntlogin+1, lastlogin=now(), rest_email_flag = 0 where cid = '$cid' and mid = '$mid'";
      $this->db->query($sql);
    }



    /*
    //미수금이 충전 예치금보다 클 때 처리(미수금이 있을 때 예치금 충전시 충전된 예치금과 추가 적립금으로 미수금 결제) / 15.01.09 / kjm
    function updateMemberAccountData_else($cid, $mid, $pgPayPrice, $addDepositiEmoney) {
        //충전 예치금 + 추가 적립금이 현재 미수금보다 크냐 작냐에 따라서 결제 방식과 업데이트 값이 달라진다 / 15.01.09 / kjm
        list($credit_member) = $db -> fetch("select credit_member from exm_member where cid = '$cid' and mid= '$mid'", 1);

        $finalCalc = $credit_member + $pgPayPrice + $addDepositiEmoney;

        if ($finalCalc >= 0) {
            $sql = "update exm_member set credit_member = 0, emoney = $finalCalc where cid='$cid' and mid='$mid'";
            $this -> db -> fetch($sql);
        } else {
            $sql = "update exm_member set credit_member = '$finalCalc', emoney = 0 where cid='$cid' and mid='$mid'";
            $this -> db -> fetch($sql);
        }
    }
    */
    function insertFixHistory($cid, $m_id, $payno, $account_flag, $account_price, $paymethod, $paydt, $h_content, $process_month_day, $referer) {
        try {
            $sql = "insert into tb_fix_member_history(
                    cid,
                    m_id,
                    payno,
                    account_flag,
                    account_price,
                    paymethod,
                    paydt,
                    h_content,
                    process_month_day,
                    referer,
                    regist_date)        
                    values(
                        '$cid',
                        '$m_id',
                        '$payno',
                        '$account_flag',
                        '$account_price',
                        '$paymethod',
                        now(),
                        '$h_content',
                        '$process_month_day',
                        '$referer',
                    now());";
            $this -> db -> query($sql);
            return $this -> db -> id;

        } catch (Exception $e) {
            return $e -> message;
        }
    }

    //PG 완료후 내역 저장하기      20141219  chunter
    function updateFixHistoryFromPG($payno, $pgcode, $pglog, $step, $bankinfo) {
        try {
            $sql = "update tb_fix_member_history set 
              paydt = now(),
              account_flag='Y',
              tid   = '$pgcode',
              pglog   = '$pglog',
              pgcode    = '$pgcode',
              paystep   = '$step',
              bankinfo  = '$bankinfo'
            where payno='$payno'";
            $this -> db -> query($sql);
            return "";
        } catch (Exception $e) {
            return $e -> message;
        }
    }

    //회원의 기업 회원 그룹정보 구하기        20141219    chunter
    function getMemberBusinessInfo($cid, $mid) {
        $sql = "select enterpriseadd from exm_member a inner join exm_business b on a.bid = b.bid
        where '$cid' = a.cid and '$mid' = a.mid and b.cid='$cid'";
        return $this -> db -> fetch($sql);
    }

    //회원의 기간별 주문 총 금액을 구한다      20141209    chunter
    function MemberBuyPriceSum($cid, $mid, $startDate, $endDate) {
        $sql = "select sum(y.payprice) totpayprice from exm_pay x inner join exm_ord_item y on y.payno = x.payno
        where '$cid' = x.cid and '$mid' = x.mid and y.itemstep>4 and y.itemstep<10";

        if ($startData)
            $sql .= " and orddt>='$startDate 00:00:00'";
        if ($endDate)
            $sql .= " and orddt<='$endDate 23:59:59'";
        list($totpayprice) = $this -> db -> fetch($sql, 1);

        return $totpayprice;
    }

    function BrooksMemberGradeProc($cid, $mid, $gradeTable, $currentGrade, $targetGrade) {
        //현재 자신의 등급에서 증가되는 등급에서 추가 포인트 구한다.
        $addPoint = 0;
        foreach ($gradeTable as $key => $value) {
            if ($gradeTable[0] == $currentGrade)
                $addPoint = 0;
        }
    }

    //미수금 및 예치금 내역 히스토리 / 15.02.13 / kjm
    function insertDepositHistory($cid, $mid, $payno, $price, $pay_flag, $admin_pay, $memo) {
        $query = "insert into tb_deposit_history set
                   cid               = '$cid',
                   mid               = '$mid',
                   payno             = '$payno',
                   credit2_payprice  = '$price',
                   pay_flag          = '$pay_flag',
                   credit2_admin_pay = '$admin_pay',
                   regdt             = now(),
                   memo              = '$memo'
                 ";
        $this -> db -> query($query);
    }

	//구매액 조회 / 16.06.02 / kdk
   	function getTotPayPrice($cid, $mid) {

      	$sql = "select sum(b.payprice) as totpayprice from 
	        exm_pay a
	        inner join exm_ord_item b on a.payno = b.payno
	    where 
	        cid = '$cid' and mid = '$mid' and itemstep in (2, 3, 4, 5, 92)";

		$result = $this -> db -> fetch($sql);
		$total = $result[totpayprice];
      	//$result = $this -> db -> query($sql);
      	//$total = $this -> db -> count;
    	return $total;
   	}

   	function getTotPayCount($cid, $mid) {

      	$sql = "select count(*) as totpaycount from 
	        exm_pay a
	        inner join exm_ord_item b on a.payno = b.payno
	    where 
	        cid = '$cid' and mid = '$mid' and itemstep in (2, 3, 4, 5, 92)";

		$result = $this -> db -> fetch($sql);
		$total = $result[totpaycount];
      	//$result = $this -> db -> query($sql);
      	//$total = $this -> db -> count;
    	return $total;
   	}

    //PG 완료후 개인정보 마케팅 활용에 동의 여부 저장하기 / 16.07.07 / kdk
    function updatePrivacyFlag($cid, $mid, $flag) {
        try {
            $sql = "update exm_member set privacy_flag = '$flag' where cid = '$cid' and mid = '$mid'";
            $this -> db -> query($sql);
            return "";
        } catch (Exception $e) {
            return $e -> message;
        }
    }

	//정액 회원 결제 내역을 조회하여 이용한도를 넘겨준다. / 16.09.20 / kdk
	function getFixHistory($cid, $mid) {
		global $r_fix_member_download;
        $member_fixdata = "";
        $sql = "select * from tb_fix_member_history where cid='$cid' and m_id='$mid' and account_flag = 'Y' order by id desc";
        //debug($sql);
        $data = $this -> db -> fetch($sql);
        //debug($data);

    	if ($data[paymethod] == "c") { //카드
    		//h_content 파싱 / 결제번호 : 1473234557031,이용기간 : 베이직(2개월)
    		if (strpos($data[h_content], _("이용기간")) !== 0 ) {
    			$h_content_arr = explode(':',$data[h_content]);
				if(trim($h_content_arr[2])) {
					//$r_fix_member_download 이용한도 조회.
					$member_fixdata = $r_fix_member_download[trim($h_content_arr[2])];
				}
    		}
		}
		else if($data[paymethod] == "cp") { //쿠폰
			$member_fixdata = "-1"; //이용한도 무제한에서 베이직(1개월)로 변경. / 16.10.14 / kdk
			$member_fixdata = $r_fix_member_download[_("베이직(1개월)")];
		}
		else if($data[paymethod] == "ad") { //관리자
			$member_fixdata = "-1"; //이용한도 무제한.
		}

        return $member_fixdata;
	}

    //정액 회원 다운로드 카운트 조회 / 16.09.20 / kdk
    function getFixTodayDownloadCount($cid, $mid) {
    	$count = 0;
    	$down_dt = date("Y-m-d");

        $sql = "select down_count from tb_fix_member_download_count where cid='$cid' and mid='$mid' and down_dt='$down_dt'";
      	$data = $this->db->fetch($sql);
		if($data) {
			$count = $data[down_count];
		}
		else {
	        $query = "insert into tb_fix_member_download_count set
	                   cid         = '$cid',
	                   mid         = '$mid',
	                   down_dt     = '$down_dt',
	                   regist_date = now()
	                 ";
	        $this -> db -> query($query);
		}

		return $count;
    }
    //정액 회원 다운로드 카운트 변경 처리 / 16.09.20 / kdk
    function updateFixTodayDownloadCount($cid, $mid) {
    	$down_dt = date("Y-m-d");
        $sql = "update tb_fix_member_download_count set down_count = down_count + 1, update_date = now() where cid='$cid' and mid='$mid' and down_dt='$down_dt'";
		//debug($sql);
        $this->db->query($sql);
    }
    //정액 회원 다운로드 내역 저장 / 16.09.20 / kdk
    function setFixDownloadHistory($cid, $mid, $storageid, $hidelog) {
        $query = "insert into tb_fix_member_download_history set
                   cid         = '$cid',
                   mid         = '$mid',
                   storageid   = '$storageid',
                   _hidelog    = '$hidelog',
                   regist_date = now()
                 ";
        $this -> db -> query($query);
    }

   //관리자 정보 조회 / 16.11.10 / kdk
   function getAdminList($cid, $addWhere){
      $query = "select * from exm_admin where cid = '$cid' $addWhere";

	  //debug($query);
      return $this->db->listArray($query);
   }

   //관리자 정보 카운트 조회 / 16.11.10 / kdk
   function getAdminListCount($cid, $addWhere){
      $query = "select count(*) as cnt from exm_admin where cid = '$cid' $addWhere";

      //debug($query);
      $totCnt = $this->db->fetch($query);
      return $totCnt[cnt];
   }

    //sns 연동 회원 조회하기 (sns_id) / 17.06.30 / kdk
    function getSearchWithSnsId($cid, $sns_id) {
        $sql = "select * from exm_member where cid='$cid' and sns_id='$sns_id'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }

    //sns 연동 회원 조회하기 (email) / 17.03.02 / kdk
    function getSearchWithEmail($cid, $email) {
        $sql = "select * from exm_member where cid='$cid' and email='$email'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }

    //sns 연동 회원 저장하기 / 17.03.02 / kdk
    function setSnsMemberInsert($bid, $cid, $mid, $name, $email)
    {
      	$sql = "insert into exm_member set
	        bid   = '$bid',
	        cid   = '$cid',
	        mid   = '$mid',
	        name  = '$name',
	        email = '$email',
	        regdt = now(),
	        sort  = -UNIX_TIMESTAMP()
      	on duplicate key update
	        bid   = '$bid',
	        cid   = '$cid',
	        mid   = '$mid'        
      	";
    	$this->db->query($sql);
	}

    //sns 연동 로그인 로그 저장하기 / 17.03.02 / kdk
	//sns 연동 정보 추가(sns_picture,sns_user_type) 210906 jtkim
    function setLogSnsLoginInsert($cid, $sns_type, $sns_id, $sns_name, $sns_email, $sns_nickname, $sns_picture , $sns_user_type)
    {
    	$strSql = "select * from exm_log_snslogin where cid = '$cid' and sns_type = '$sns_type' and sns_id = '$sns_id'";
		//echo $sql;
		$data = $this->db->fetch($strSql);

		if(!$data) {
			$sql = "
				INSERT INTO exm_log_snslogin (cid,sns_type,sns_id,sns_name,sns_email,sns_nickname,sns_picture,sns_user_type,sns_connect_date)
				VALUES ('$cid','$sns_type','$sns_id','$sns_name','$sns_email','$sns_nickname','$sns_picture','$sns_user_type',now());
			";
			$this->db->query($sql);
		}
	}

    //sns 연동 로그인 로그 관련 필드 update / 17.03.02 / kdk
    function setLogSnsLoginUpdate($cid, $sns_type, $sns_id)
    {
      	$sql = "
      	update exm_log_snslogin 
      		set cnt_login=cnt_login+1, sns_connect_date=now() 
      	where cid = '$cid' and sns_type = '$sns_type' and sns_id = '$sns_id'
      	";
      	$this->db->query($sql);

 		$sql = "update exm_member set cntlogin=cntlogin+1, lastlogin=now() where cid = '$cid' and sns_id = '$sns_id'";
      	$this->db->query($sql);
    }

    //기존 회원일 경우 sns이메일정보 확인 후 회원정보 갱신 필드 update / 20180323 / kdk
    function setMemberSnsIdUpdate($cid, $mid, $email, $sns_id)
    {
        $sql = "update exm_member set sns_id = '$sns_id' where cid = '$cid' and mid = '$mid' and email = '$email'";
        $this->db->query($sql);
    }

    //sns 연동 로그인 정보 조회하기 / 17.06.30 / kdk
    function getLogSnsLogin($cid, $sns_id)
    {
    	$sql = "select * from exm_log_snslogin where cid = '$cid' and sns_id = '$sns_id'";
		return $this->db->fetch($sql);
	}

    //미사용 적립금
    function getNotUseEmoney($cid) {
    	list($m_emoney) = $this->db->fetch("select sum(emoney) from exm_member where cid='$cid'", 1);
        return $m_emoney;
    }

	//회원정보 유효성 체크
	function getChkMemberInfo($cid, $tableName, $addWhere='') {
		$sql = "select * from $tableName $addWhere";
        return $this->db->fetch($sql);
	}

	//회원정보 추가 및 수정
	function setMemberInfo($mode, $addColumn='', $addWhere='') {
		if ($mode == "myinfo") {
			$sql = "update exm_member $addColumn $addWhere";
		} else if ($mode == "register") {
			$sql = "insert into exm_member $addColumn";
		}
		$this->db->query($sql);
	}

	//휴면계정 인증코드
	function setMemberRestoreCode($cid, $mid, $restore_num) {
		$sql = "insert into tb_member_restore_code set 
					cid='$cid',
					mid='$mid',
					restore_number='$restore_num',
					regist_date=now()
				on duplicate key update
					restore_number='$restore_num',
					regist_date=now()";
		$this->db->query($sql);
	}

	//휴면계정 인증코드 조회
	function getMemberRestoreCode($cid, $mid) {
		list($m_restore_num) = $this->db->fetch("select restore_number from tb_member_restore_code where cid='$cid' and mid='$mid'", 1);
		return $m_restore_num;
	}

	//휴면계정 인증코드 삭제
	function delMemberRestoreCode($cid, $mid) {
		$sql = "delete from tb_member_restore_code where cid='$cid' and mid='$mid'";
		$this->db->query($sql);
	}

	//회원정보 삭제
	function delMemberInfo($cid, $mid, $password) {

      $sql1 = "select * from exm_member where cid='$cid' and mid='$mid' and password='$password'";
      $data = $this->db->fetch($sql1);
      if($data) {
         $sql2 = "insert into exm_member_out set 
                     cid='$cid',
                     mid='$mid',
                     name='',
                     email='',
                     regdt='$data[regdt]',
                     outdt=now()";
         $this->db->query($sql2);

         //$sql2 = "insert into exm_member_out(cid, mid, regdt, outdt) select cid, mid, regdt, now() from exm_member where cid='$cid' and mid='$mid' and password='$password' limit 1";
		 //$this->db->query($sql2);

         $sql = "delete from exm_member where cid='$cid' and mid='$mid' and password='$password'";
         $this->db->query($sql);
      }
    }

    //적립금내역 삭제
	function delEmoneyInfo($cid, $mid) {

        $sql1 = "select * from exm_log_emoney where cid='$cid' and mid='$mid' limit 1";
        $data = $this->db->fetch($sql1);
        if($data) {
           $sql2 = "INSERT INTO `exm_log_emoney_out` SELECT * FROM exm_log_emoney WHERE cid='$cid' AND MID='$mid'";
           $this->db->query($sql2);

           $sql = "delete from exm_log_emoney where cid='$cid' and mid='$mid'";
           $this->db->query($sql);
        }
    }

	//다운로드 가능한 쿠폰
	function getDownloadCouponList($cid, $mid) {
		$sql = "select a.*,(select count(*) from exm_coupon_set x where x.cid = a.cid and x.coupon_code = a.coupon_code) issued 
			from exm_coupon a 
			left join exm_coupon_set b on a.cid = b.cid and a.coupon_code = b.coupon_code and mid = '$mid' 
			where a.cid = '$cid' 
			and b.coupon_code is null 
			and coupon_issue_system = 'download' 
			and coupon_kind = 'on' 
			and (coupon_issue_unlimit = 1 or (coupon_issue_sdate <= curdate() and coupon_issue_edate >= curdate())) 
			and ((coupon_issue_ea_limit = 1 and (select count(*) from exm_coupon_set x where x.cid = a.cid and x.coupon_code = a.coupon_code) < coupon_issue_ea) or coupon_issue_ea_limit = 0) 
			order by coupon_regdt desc";
		return $this->db->listArray($sql);
	}

	//사용 가능한 쿠폰
	function getMyCouponList($cid, $mid, $addwhere='') {
		$sql = "select a.*,b.coupon_setdt,b.no,b.coupon_able_money,if(coupon_period_system = 'date',coupon_period_edate,
				if(coupon_period_system = 'deadline',left(adddate(coupon_setdt,interval coupon_period_deadline-1 day),10),coupon_period_deadline_date)) usabledt 
			from exm_coupon a 
			inner join exm_coupon_set b on a.cid = b.cid and a.coupon_code = b.coupon_code and mid = '$mid' 
			where a.cid = '$cid' 
			and coupon_use = 0 
			and ((coupon_period_system = 'date' and coupon_period_sdate <= curdate() and coupon_period_edate >= curdate()) 
				or (coupon_period_system = 'deadline' and adddate(coupon_setdt,interval coupon_period_deadline day) >= adddate(curdate(),interval 1 day)) 
                or (coupon_period_system = 'deadline_date' and coupon_period_deadline_date >= curdate())) 
            $addwhere    
			order by coupon_setdt desc";
		return $this->db->listArray($sql);
	}

	//발급한 쿠폰
	function getAllMyCouponList($cid, $mid) {
		$sql = "select a.*,b.coupon_setdt,b.no,b.coupon_able_money,b.coupon_use,if(coupon_period_system = 'date',coupon_period_edate,
				if(coupon_period_system = 'deadline',left(adddate(coupon_setdt,interval coupon_period_deadline-1 day),10),coupon_period_deadline_date)) usabledt 
			from exm_coupon a 
			inner join exm_coupon_set b on a.cid = b.cid and a.coupon_code = b.coupon_code and mid = '$mid' 
			where a.cid = '$cid' 
			order by coupon_setdt desc";
		return $this->db->listArray($sql);
	}

	//주문시 사용한 쿠폰명
	function getUseCouponInfo($couponsetno) {
		list($couponname) = $this->db->fetch("select coupon_name from exm_coupon_set a inner join exm_coupon b on a.cid = b.cid and a.coupon_code = b.coupon_code where no = '$couponsetno'", 1);
		return $couponname;
	}

	//찜하기 유효성 검사
	function getWishChk($cid, $mid, $catno, $goodsno) {
		list($m_cnt) = $this->db->fetch("select count(*) from md_wish_list where cid='$cid' and mid='$mid' and catno='$catno' and goodsno='$goodsno'", 1);
		return $m_cnt;
	}

	//찜하기 추가
	function setWishInfo($cid, $mid, $catno, $goodsno) {
		$sql = "insert into md_wish_list set 
			cid='$cid',
			mid='$mid',
			catno='$catno',
			goodsno='$goodsno',
			regist_date=now()";
		$this->db->query($sql);
	}

	//찜하기 삭제
	function delWishInfo($cid, $mid, $no) {
		$sql = "delete from md_wish_list where cid='$cid' and mid='$mid' and no in ($no)";
		$this->db->query($sql);
	}

	//리뷰 조회
	function getReviewInfo($cid, $payno, $ordno, $ordseq) {
		$sql = "select * from exm_review where cid = '$cid' and payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'";
		return $this->db->fetch($sql);
	}

	//리뷰 추가 및 수정
	function setReviewInfo($cid, $payno, $ordno, $ordseq, $catno, $goodsno, $mid, $name, $subject, $content, $degree, $review_deny_user, $kind, $img) {
		$sql = "insert into exm_review set
			cid				  = '$cid',
			payno				  = '$payno',
			ordno				  = '$ordno',
			ordseq			  = '$ordseq',
			catno				  = '$catno',
			goodsno			  = '$goodsno',
			mid				  = '$mid',
			name				  = '$name',
			subject			  = '$subject',
			content			  = '$content',
			degree			  = '$degree',
			review_deny_user = '$review_deny_user',
			kind 				  = '$kind',
			img				  = '$img',
			regdt				  = now()
		on duplicate key update
			subject			  = '$subject',
			content			  = '$content',
			degree			  = '$degree',
			review_deny_user = '$review_deny_user',
			kind 				  = '$kind',
			img					= '$img'";
		$this->db->query($sql);
	}

	//모바일푸쉬알림 리스트
	function getMobilePushList($cid, $orderby='', $limit='') {
		$sql = "select a.*,(select count(*) from exm_log_mobile_push_resend where cid = a.cid and pushno = a.pushno) as resendCnt from exm_mobile_push a where a.cid = '$cid' $orderby $limit";

		return $this -> db -> listarray($sql);
	}

   function savePaymethodMember($cid, $mid, $paymethod){
      $query = "update exm_member set save_paymethod = '$paymethod' where cid = '$cid' and mid = '$mid'";
      $this->db->query($query);
   }

   function setDocumentInfo($cid, $mid, $document_type, $payno, $mobile, $email, $card_num, $licensee_num, $document_file, $no = "") {
   	  $column = "";

	  if ($document_type == "CRD") {
	  	 $column = "mobile = '$mobile',email = '$email',card_num = '$card_num',";
	  } else if ($document_type == "CRE") {
	  	 $column = "email = '$email',licensee_num = '$licensee_num',document_file = '$document_file',";
	  } else if ($document_type == "TI") {
	  	 $column = "email = '$email',licensee_num = '$licensee_num',document_file = '$document_file',";
	  }

      //내역 다시 조회하여 중복 등록 방지.
      if ($no == "") {
        $sql = "select no from md_document_info where cid='$cid' and mid='$mid' and payno = '$payno' and document_type = '$document_type'";
        list($no) = $this->db->fetch($sql, 1);
      }

	  if ($no) {
	  	$sql = "update md_document_info set 
   	  			$column
   	  			moddt = now()
   	  			where no = '$no'";
	  } else {
	  	$sql = "insert into md_document_info set 
   	  			cid = '$cid',
   	  			mid = '$mid',
   	  			document_type = '$document_type',
   	  			payno = '$payno',
   	  			$column
   	  			regdt = now()";
	  }

   	  $this->db->query($sql);
   }

   //베스트 후기 정보
   function getReviewBestInfo($cid) {
   	  $sql = "select a.*,b.goodsnm from exm_review a 
   	  	inner join exm_goods b on a.goodsno = b.goodsno 
   	  	where a.cid = '$cid' and a.review_deny_admin = '0' and a.review_deny_user = '0' and a.main_flag = 'Y'";

   	  return $this->db->fetch($sql);
   }

   function setBigorderRequestInfo($cid, $category, $goodsnm, $ea, $request_company, $request_name, $request_mobile, $request_email, $request_zipcode, $request_address, $request_address_sub, $content) {
   	  $sql = "insert into exm_request set 
   	  		  cid = '$cid',
   	  		  category = '$category',
   	  		  goodsnm = '$goodsnm',
   	  		  ea = '$ea',
   	  		  request_company = '$request_company',
   	  		  request_name = '$request_name',
   	  		  request_mobile = '$request_mobile',
   	  		  request_email = '$request_email',
   	  		  request_zipcode = '$request_zipcode',
   	  		  request_address = '$request_address',
   	  		  request_address_sub = '$request_address_sub',
   	  		  content = '$content',
   	  		  regdt = now()";

   	  $this->db->query($sql);
   }

    //후기 카운트 정보 / 20190215 / kdk
    function getReviewCnt($cid, $addWhere=''){
        $query = "select count(*) as cnt from exm_review where cid='$cid' $addWhere";
        $cnt = $this->db->fetch($query);
        return $cnt[cnt];
    }

	//OutLogin 연동 회원 저장하기   20191128 kkwon
    function setOutLoginBMemberInsert($cid, $mid, $grpno)
    {
      $ret = $this->getInfo($cid, $mid);

	  if(!$ret[mid]){
		  $sql = "insert into exm_member set
			cid   = '$cid',
			mid   = '$mid',
			grpno = '$grpno',
			regdt = now(),
			sort  = -UNIX_TIMESTAMP()      
		  ";
		  $this->db->query($sql);
	  }

	  $this->setLoginDataUpdate($cid, $mid);


	  $data['cid'] = $cid;
	  $data['mid'] = $mid;
	  _member_login($data);
	  setCookie('member_check', "1", 0, '/', '');
	  go('/');
    }
}
?>
