<?

class SmsIlark
{
	var $db;
	var $cid;		// SMS업체아이디
	var $from;
	var $to;
	var $name;
	var $call;		// 보낸 SMS수
	var $limit;		// 업체보유 SMS수
	var $error;		// 에러유무
	var $trandt;	// SMS예약발송일시
	var $conn2;
	
	var $chargeUrl;
	var $logUrl;
	
	//sms 발송 방법 설정			20160928		chunter
	var $sms_send_type;
	var $sms_send_point;
	

	function SmsIlark(){
		global $cfg_center, $cid, $db, $cfg;
		
		$this->sms_send_type = $cfg[sms_send_type];		
		if ($cfg[sms_send_point])
			$this->sms_send_point = $cfg[sms_send_point];
		else
			$this->sms_send_point = 30;			//기본 30point;		기본 포인트 변경하려면 center 몰 설정 부분도 수정해야함.
		
		$this->db = $db;
		$this->connect();
		
		if ($cfg[sms_send_type] == "P")
		{
			$m_mall = new M_mall();
         $mall_data = $m_mall -> getInfo($cid);

			$this->cid = $cid;
			$this->limit = $mall_data[pretty_point]." Point (".$this->sms_send_point ."p/1)";

			$this->chargeUrl = "/a/module/point_account.php";
			$this->logUrl = "/a/order/sms_list.php";
			$this->logUrl = "/a/order/point_list.php";
		} else {
			$this->cid = $cfg_center[center_cid]."|".$cid;
			$this->getLimit(); //콜수			
			$this->chargeUrl = "javascript:popup('http://podmanage.bluepod.kr/sms/sms.php?cid=".$this->cid."',400,500);";
			$this->logUrl = "http://podmanage.bluepod.kr/sms/sms_list.php?cid=".$this->cid;
		}	
	}

	function getLimit()
	{
		$query = "select `call` from tb_sms where cid='$this->cid'";
		list ($this->limit) = mysql_fetch_array(mysql_query($query,$this->conn2));
	}

	function connect()
	{
		$this->conn2 = mysql_connect("bpm.bluepod.kr","podmanage","podmanage!@#$", true);
		//$this->conn2 = mysql_connect("192.168.1.199","root","dkdlfkr12!@", true);
		mysql_select_db("podmanage",$this->conn2);
		mysql_query("set names utf8",$this->conn2);
	}

	function disconnect()
	{
		if ($this->conn2)
			mysql_close($this->conn2);
	}

	function send($msg)
	{	   
//		if (@preg_match('/.+/u', $msg)) $msg = iconv("UTF-8", "EUC-KR", $msg);

		if (!$this->cid) return false;

		if (!is_array($msg)){ $tmp = array(); $tmp[0] = $msg; $msg = $tmp; }
		$this->multiple = count($msg);

		if (!$this->multiple || $this->multiple>2){
			$this->error(_("메세지에 문제가 있습니다. 관리자에게 문의하세요")); return;
		}
		if (!is_array($this->to)) $this->to = array($this->to);

		$this->from = str_replace("-","",$this->from);
		if (!$this->from || !array_notnull($this->to)){
			$msg = (!$this->from) ? _("보내는 번호가 존재하지 않습니다") : _("받는 번호가 존재하지 않습니다");
			$this->error($msg); return;
		}

		if (!trim($msg[0])){
			$this->error(_("메세지를 입력해주세요")); return;
		}

		//point로 sms 발송 처리			20160928		chunter
		if ($this->sms_send_type == "P")
		{
			$this->sendWithPoint($msg);			
		} else {
			$this->call = count($this->to) * $this->multiple;
			$this->getLimit();
	
			if ($this->call > $this->limit){
				$this->error(_("보유 SMS 잔여량이 발송예정건수보다 부족합니다.")." ("._("보유 SMS 잔여량")." : ".number_format($this->limit).", "._("발송예정건수")." : ".number_format($this->call).")");
				return;
			}
	
			### 업체 보유 SMS 업데이트
			$query = "update tb_sms set `call`=`call`-$this->call where cid='$this->cid'";
			mysql_query($query,$this->conn2);
	
			$this->sendProc($msg);
			
			$this->msg = $this->call._("개의 SMS를 발송하였습니다");
			$this->limit -= $this->call;
	
			$this->disconnect();
		}
	}

	function sendWithPoint($msg)
	{		
		$m_mall = new M_mall();
    
      $mall_data = $m_mall -> getInfo($this->cid);
      //$mall_data[pretty_point];

		$this->call = count($this->to) * $this->multiple;
		
		$check_point = $this->call * $this->sms_send_point;

		if ($check_point > $mall_data[pretty_point]){
			$this->error(_("Point가 부족합니다.")." ("._("Point")." : ".number_format($mall_data[pretty_point]).", "._("발송예정건수")." : ".number_format($this->call).")");
			return;
		}
		
		$m_pretty = new M_pretty();
		
		$mall_point = $mall_data[pretty_point] - $check_point;
		$m_mall -> mallPrettyPointUpdate($this->cid, $mall_point);
      $m_pretty -> insertPointHistory($this->cid, $check_point, '14', $mall_point, '', '', '[' .$this->call. ' call]');			//14 -> sms 발송
			 
		$this->sendProc($msg);
		$this->msg = $this->call._("개의 SMS를 발송하였습니다");
	}


	function sendProc($msg)	{
		foreach ($msg as $_msg) {
			$_msg = addslashes($_msg);

			$this->log($_msg);
			$_parse_msg = (strpos($_msg,"{name}")!==false && $this->name) ? 1 : 0;

			$tmpTo = array();
			foreach ($this->to as $k=>$v) {
				$_msgX = (!$_parse_msg) ? $_msg : str_replace("{name}",$this->name[$k],$_msg);
				$v = str_replace("-","",$v);
				$tmpTo[] = (!$this->trandt) ? "('{$this->cid}', '$this->cno', 0, '$v','$this->from', '$_msgX', Now(), Now(), Now(), 4)" : "('{$this->cid}', '$this->cno', 0, '$v','$this->from', '$_msgX', '$this->trandt', Now(), '$this->trandt', 4)";
				if ($k%50==49) {
					$this->sendmsgArr($tmpTo);
					$tmpTo = array();
				}
			}
			if (count($tmpTo)) $this->sendmsgArr($tmpTo);
		}
	}

	function error($msg) {
      $this->error = true;
		$this->msg = $msg;
		$this->disconnect();
	}

	function sendmsgArr($tmp) {
      $query = "Insert Into Oneshot_Tran(cid, cno, Status, Phone_No, Callback_No, Sms_Msg,Send_Time, Save_Time, Tran_Time, Msg_Type)
      values ".implode(",",$tmp);

		mysql_query($query,$this->conn2);
	}


	function log($msg) {
      //	$msg = iconv("EUC-KR", "UTF-8", $msg);
		$cnt = $this->call;
      $r_to = implode(",",$this->to);
      ### 전송로그기록
      global $cid;
      $query = "insert into exm_log_sms
                  set
                  cid    = '$cid',
                  number = '$r_to',
                  msg    = '$msg',
                  `call` = '$cnt',
                  regdt  = now()
               ";
      $this->db->query($query);
      $this->cno = $this->db->id;
   }
}

?>