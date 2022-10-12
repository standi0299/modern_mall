<?
class SmsExm
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

	var $bSmsIlarkSend;    //sms ilark 으로 처리 여부
	var $smsIlark;     //sms ilark 객체
	
	
	var $chargeUrl;
	var $logUrl;

   function SmsExm(){
      global $cfg_center, $cid, $db;
		$this->db = $db;
		$this->cid = $cfg_center[center_cid]."|".$cid;
		$this->connect();
		$this->getLimit(); //콜수
		$this->bSmsIlarkSend = false;

		$this->chargeUrl = "http://podmanage.bluepod.kr/sms/sms.php?cid=".$this->cid;
		$this->logUrl = "http://podmanage.bluepod.kr/sms/sms_list.php?cid=".$this->cid;
		
      //sms 건수가 없을 경우 sms_ilark 으로 처리한다.    20150421    chunter
		if ($this->limit < 1) {
         //업체 계약 문제로 sms ilark 발송 모듈을 주석처리     20150429    chunter
         //계약 완료     20150706    chunter  
         $this->smsIlark = new SmsIlark();
         $this->bSmsIlarkSend = true;
         $this->limit = $this->smsIlark->limit;

			$this->chargeUrl = "http://podmanage.bluepod.kr/sms/sms.php?cid=".$this->cid;
			$this->logUrl = "http://podmanage.bluepod.kr/sms/sms_list.php?cid=".$this->cid;
  	   }
	}

	function getLimit() {
      $query = "select `call` from mini_sms where cid='$this->cid'";
		list ($this->limit) = mysql_fetch_array(mysql_query($query,$this->conn));
	}

	function connect() {
		$this->conn = mysql_connect("exm.co.kr","npro","npro@exm11");
		mysql_select_db("npro",$this->conn);
	}

	function disconnect() {
		mysql_close($this->conn);
	}

   function send($msg){
      if ($this->bSmsIlarkSend){
         $this->smsIlark->to = $this->to;
         $this->smsIlark->from = $this->from;
         $this->smsIlark->send($msg);
      } else {
         //if (@preg_match('/.+/u', $msg)) $msg = iconv("UTF-8", "EUC-KR", $msg);
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

     		if (!trim($msg)){
     			$this->error(_("메세지를 입력해주세요")); return;
     		}

     		$this->call = count($this->to) * $this->multiple;
     		$this->getLimit();

     		if ($this->call>$this->limit){
     			$this->error(_("보유 SMS 잔여량이 발송예정건수보다 부족합니다.")." ("._("보유 SMS 잔여량")." : ".number_format($this->limit).", "._("발송예정건수")." : ".number_format($this->call).")");			
     			return;
     		}

     		### 업체 보유 SMS 업데이트
     		$query = "update mini_sms set `call`=`call`-$this->call where cid='$this->cid'";
     		mysql_query($query,$this->conn);

     		foreach ($msg as $_msg){
     			$_msg = addslashes($_msg);

     			$this->log($_msg);
     			$_parse_msg = (strpos($_msg,"{name}")!==false && $this->name) ? 1 : 0;

     			$tmpTo = array();
     			foreach ($this->to as $k=>$v){
     				$_msgX = (!$_parse_msg) ? $_msg : str_replace("{name}",$this->name[$k],$_msg);
     				$v = str_replace("-","",$v);
     				$tmpTo[] = (!$this->trandt) ? "('{$this->cid}', '$this->cno', 0, '$v','$this->from', '$_msgX', 4, null)" : "('{$this->cid}', '$this->cno', 0, '$v','$this->from', '$_msgX', 4, '$this->trandt')";
     				if ($k%50==49){
     					$this->sendmsgArr($tmpTo);
     					$tmpTo = array();
     				}
     				//$this->sendmsg($this->from,$v,$_msg);
     			}
     			if (count($tmpTo)) $this->sendmsgArr($tmpTo);
     		}
     		
     		$this->msg = $this->call._("개의 SMS를 발송하였습니다");
     		$this->limit -= $this->call;

     		$this->disconnect();
		}
	}

	function error($msg) {
		$this->error = true;
		$this->msg = $msg;
		$this->disconnect();
	}

	function sendmsgArr($tmp) {
		$query = "
		insert into MSG_DATA (CID, CNO, CUR_STATE, CALL_TO, CALL_FROM, SMS_TXT, MSG_TYPE, REQ_DATE)
		values ".implode(",",$tmp);
		mysql_query($query,$this->conn);
		//debug($query);
	}

	function sendmsg($from,$to,$msg) {
		return;	// SMS 실발송 제한
		mysql_query("
		insert into MSG_DATA set
			CID				= '{$this->cid}',
			CNO				= '$this->cno',
			CUR_STATE		= 0,
			CALL_TO			= '$to',
			CALL_FROM		= '$from',
			SMS_TXT			= '$msg',
			MSG_TYPE		= 4
		", $this->conn);
	}

	function log($msg) {
   //$msg = iconv("EUC-KR", "UTF-8", $msg);
      $cnt = $this->call;

		$r_to = implode(",",$this->to);

		### 전송로그기록
		global $cid;
		$query = "
		insert into exm_log_sms
		set
			cid		= '$cid',
			number	= '$r_to',
			msg		= '$msg',
			`call`	= '$cnt',
			regdt	= now()
		";
		$this->db->query($query);
		$this->cno = $this->db->id;
	}
}

?>