<?php
class M_attend_event {
	var $db;
	var $thisMonthWhere = "";
	var $lastMonthWhere = "";

	function M_attend_event() {
		$this -> db = $GLOBALS[db];

		$this -> thisMonthWhere = " and regist_date>='" . date("Y-m") . "-01 00:00:00' and regist_date<='" . date("Y-m") . "-31 23:59:59'";
		$this -> lastMonthWhere = " and regist_date>='" . LAST_MONTH_Y_M_D . "-01 00:00:00' and regist_date<='" . date("Y-m-t", strtotime(LAST_MONTH_Y_M_D)) . " 23:59:59'";
	}

	function getInfo($cid) {
		$sql = "select * from md_attend_event where cid='$cid'";
		return $this -> db -> fetch($sql);
	}

	function getUserTakeList($cid, $mid) {
		$sql = "select * from md_addtend_event_user_log where cid='$cid' and mid='$mid' order by regist_date desc";
		return $this -> db -> listarray($sql);
	}

	function getUserTakeListThisMonth($cid, $mid) {
		$sql = "select * from md_addtend_event_user_log where cid='$cid' and mid='$mid' $this->thisMonthWhere order by regist_date desc";
		return $this -> db -> listarray($sql);
	}

	function getUserTakeListLastMonth($cid, $mid) {
		$sql = "select * from md_addtend_event_user_log where cid='$cid' and mid='$mid' $this->lastMonthWhere order by regist_date desc";
		return $this -> db -> listarray($sql);
	}

	function getUserTakeCntThisMonth($cid, $mid) {
		$sql = "select count(*) as cnt from md_addtend_event_user_log where cid='$cid' and mid='$mid' $this->thisMonthWhere";
		list($cnt) = $this -> db -> fetch($sql, 1);
		return $cnt;
	}

	function getIsertUserAttendLog($cid, $mid, $a_day) {
		$sql = "insert into md_addtend_event_user_log set cid = '$cid', mid	= '{$mid}',attend_day='{$a_day}', regist_date = now()";
		//debug($sql);
		$this -> db -> query($sql);
	}

	function getInsertUpdate($cid, $_POST) {
		$sql = "insert into md_attend_event set
				cid	= '$cid',
				etype	= '{$_POST[etype]}',
				title	= '{$_POST[title]}',
				sdate	= '{$_POST[sdate]}',
				edate	= '{$_POST[edate]}',
				emoney	= '{$_POST[emoney]}',
				count_tot	= '{$_POST[count_tot]}',
				count_seq	= '{$_POST[count_seq]}',
				add_emoney	= '{$_POST[add_emoney]}',
				emoney_expire_date	= '{$_POST[emoney_expire_date]}',
				msg1	= '{$_POST[msg1]}',
				msg2	= '{$_POST[msg2]}',
				regdt	= now()
			on duplicate key update
				cid	= '$cid',
				etype	= '{$_POST[etype]}',
				title	= '{$_POST[title]}',
				sdate	= '{$_POST[sdate]}',
				edate	= '{$_POST[edate]}',
				emoney	= '{$_POST[emoney]}',
				count_tot	= '{$_POST[count_tot]}',
				count_seq	= '{$_POST[count_seq]}',
				add_emoney	= '{$_POST[add_emoney]}',
				emoney_expire_date	= '{$_POST[emoney_expire_date]}',
				msg1	= '{$_POST[msg1]}',
				msg2	= '{$_POST[msg2]}',
				updatedt	= now()		";
		$this -> db -> query($sql);
	}

	//오늘 출석 체크 확인
	function getUserTakeToday($cid, $mid, $today) {
		$sql = "select * from md_addtend_event_user_log where cid='$cid' and mid='$mid' and DATE_FORMAT(regist_date,'%Y-%m-%d')='$today'";
		return $this -> db -> fetch($sql);
	}
	
	//이벤트 기간별 출석 체크 확인
	function getUserTakeListMonth($cid, $mid, $sdate, $edate) {
		$monthWhere = " and regist_date>='" . $sdate . " 00:00:00' and regist_date<='" . $edate . " 23:59:59'";
		$sql = "select * from md_addtend_event_user_log where cid='$cid' and mid='$mid' $monthWhere order by regist_date";
		return $this -> db -> listarray($sql);
	}

	//이벤트 기간별 출석 체크 확인
	function getUserTakeCntMonth($cid, $mid, $sdate, $edate) {
		$monthWhere = " and regist_date>='" . $sdate . " 00:00:00' and regist_date<='" . $edate . " 23:59:59'";
		$sql = "select count(*) as cnt from md_addtend_event_user_log where cid='$cid' and mid='$mid' $monthWhere";
		list($cnt) = $this -> db -> fetch($sql, 1);
		return $cnt;
	}
	
}
?>