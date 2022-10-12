<?php

class M_emoney {
    var $db;
    function M_emoney() {
        $this -> db = $GLOBALS[db];
    }
    
    //유효기간내 적립금 합 구하기
    function getSumEmoneyLog($cid, $mid) {
    	list($m_total) = $this->db->fetch("select sum(remain_emoney) as total from exm_log_emoney where cid='$cid' and mid='$mid' and status='1' and expire_date >= now()", 1);
    	return ($m_total) ? $m_total : 0;
    }
		
	function getSumEmoneyWithDate($cid, $mid, $whereDate) {
		list($m_total) = $this->db->fetch("select sum(remain_emoney) as total from exm_log_emoney where cid='$cid' and mid='$mid' and status='1' and expire_date < '$whereDate'", 1);
    	return $m_total;
    }
            
    function getEmoneyLogList($cid, $mid) {
    	$sql = "select * from exm_log_emoney where cid='$cid' and mid='$mid' and status='1' and expire_date >= now() order by expire_date asc";
    	return $this->db->listarray($sql);
    }
		
		//배송은 끝나고 적립금 지급이 되지 않은 내역 가져오기
		function getOrderEndNotEmoney($cid, $mid)
		{
			//$sql = "select * from exm_pay where cid='$cid' and mid !='' and emoney_receive_flag='9' and shipdt != '' and date_add(shipdt, INTERVAL $emoney_send_day day) <= now()";
			$sql = "select * from exm_pay where cid='$cid' and mid ='$mid' and paystep='2' and emoney_receive_flag='9'";
			//debug($sql);
			return $this->db->listarray($sql);
		}
		
		//미지급 적립금 주문 조회			20180914		chunter
		function getAllOrderEndNotEmoney($cid, $emoney_send_day = 0)
		{
			if ($emoney_send_day && $emoney_send_day > 0)
				$addWhere = "and date_add(orddt, INTERVAL $emoney_send_day day) <= now()";		//exm_pay에서는 배송일이 없으니 주문일을 기준으로 일단 쿼리를 만든다. 그래야 결과 row가 작다.			
			$sql = "select distinct mid from exm_pay where cid='$cid' and paystep='2' and emoney_receive_flag='9' $addWhere";
			//debug($sql);
			return $this->db->listarray($sql);
		}
		  
    function setEmoneyUpdate($cid, $mid) {
      $total_emoeny = $this->getSumEmoneyLog($cid, $mid); 
    	$sql = "update exm_member set emoney = $total_emoeny where cid = '$cid' and mid = '$mid'";
    	$this -> db -> query($sql);
    }
		
		//주문 테이블의 적립금 지급 완료 처리.
		function setPayEmoenyReceive($cid, $payno)
		{
			$sql = "update exm_pay set emoney_receive_flag = '1' where cid = '$cid' and payno = '$payno'";
    	$this -> db -> query($sql);
		}
    
    //유효기간 지난 적립금 사용처리.
	function setEmoneyUpdateExpire($cid, $mid) {
        $sql = "update exm_log_emoney set remain_emoney = 0, memo = memo + ' ("._("유효기간 만료").")', status = '3' where cid = '$cid' and mid = '$mid' and status = '1' and expire_date < now()";
        $this->db->query($sql);
    }
    
    //적립금 일부만 사용여부 처리
	function setEmoneyUpdatePartUsed($cid, $mid, $remain_emoney, $emoneyNo) {
		$sql = "update exm_log_emoney set remain_emoney=$remain_emoney where cid = '$cid' and mid = '$mid' and no = {$emoneyNo}";
     	//$sql = "update exm_log_emoney set remain_emoney = 0 where cid = '$cid' and mid = '$mid' and  no in ({$emoneyNos})";
     	$this->db->query($sql);
    }
    
    //사용 처리
	function setEmoneyUpdateUsed($cid, $mid, $emoneyNos) {
		//$sql = "update exm_log_emoney set remain_emoney=0 where no in ({$whereNO});";
     	$sql = "update exm_log_emoney set remain_emoney = 0 where cid = '$cid' and mid = '$mid' and  no in ({$emoneyNos})";
     	$this->db->query($sql);
    }
    
    function setEmoneyLogInsert($cid, $mid, $memo, $emoney, $status, $admin_mid, $expire_date = '', $payno = '', $ordno = '', $ordseq = '') {
      $addqr = "";
      if ($payno)
         $addqr .= ",payno = '$payno', ordno = '$ordno', ordseq = '$ordseq'";

      if ($expire_date)
         $addqr .= ",remain_emoney = '$emoney', expire_date  = '$expire_date' ";

      //status 1:적립, 2:사용

      $sql = "insert into exm_log_emoney set 
               cid    = '$cid',
               mid    = '$mid',
               memo   = '$memo',
               regdt  = now(),
               emoney = '$emoney',
               status = '$status',
               admin  = '$admin_mid'
               $addqr
             ";
      $this -> db -> query($sql);
   }
   
   function getEmoneyLogListUseOrder($cid, $mid, $payno) {
   	  $sql = "select * from exm_log_emoney where cid = '$cid' and mid = '$mid' and payno = '$payno' and status = '2'";
   	  
   	  return $this->db->listArray($sql);
   }
}
?>