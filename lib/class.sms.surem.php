<?
class SmsSurem
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
	var $nationCode;				///국가코드
	var $usercode;				//사용자 ID
	var $deptcode;				//업체 코드
	
	var $chargeUrl;
	var $logUrl;
	
	function SmsSurem()
	{
  	global $cfg_center, $cfg, $cid, $db, $g_nation_code;
		
		$this->db = $db;
		$this->cid = $cfg_center[center_cid]."|".$cid;
		$this->connect();
		$this->getLimit(); //콜수			
		
		$this->nationCode = $g_nation_code;
		$this->usercode = $cfg[sms_code1];
		$this->deptcode = $cfg[sms_code2];
		
		$this->chargeUrl = "http://www.surem.co.kr/main.asp";
		$this->logUrl = "http://www.surem.co.kr/main.asp";
		
		
	}

	function getLimit() 
	{  	
		$this->limit = 0;
	}

	function connect() {
		
	}

	function disconnect() {
		
	}

	function send($msg)
	{
  	global $languages;
  	//if (@preg_match('/.+/u', $msg)) $msg = iconv("UTF-8", "EUC-KR", $msg);
    if (!$this->cid) return false;

    if (!is_array($msg))
    {
    	$tmp = array(); 
    	$tmp[0] = $msg; 
    	$msg = $tmp; 
		}
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

    /*
    if ($this->call>$this->limit){
    	$this->error(_("보유 SMS 잔여량이 발송예정건수보다 부족합니다.")." ("._("보유 SMS 잔여량")." : ".number_format($this->limit).", "._("발송예정건수")." : ".number_format($this->call).")");			
    	return;
    }
	 */

 		foreach ($msg as $_msg)
 		{
 			$_msg = addslashes($_msg);
 			$this->log($_msg);
 			$_parse_msg = (strpos($_msg,"{name}")!==false && $this->name) ? 1 : 0;

 			$tmpTo = array();
 			foreach ($this->to as $k=>$v)
 			{
 				$_msgX = (!$_parse_msg) ? $_msg : str_replace("{name}",$this->name[$k],$_msg);
 				$v = str_replace("-","",$v);

				if (! $this->nationCode || $this->nationCode == "82")
				{
					$smsUrl = "https://toll.surem.com:440/message/direct_call_sms_return_post.asp";
					$addedParam = "&encoding=UNICODE";
					$addedNumber = "";
				}
				else
				{
					$smsUrl = "https://toll.surem.com:440/message/direct_INTL_return_post.asp";
					$addedParam = "&encoding=UNICODE";
					
					$addedNumber = $this->nationCode ."-";
				}
				
				$ucode = mb_convert_encoding($_msgX, "UTF-16", "UTF-8");
				$decode_msgX = strtoupper(bin2hex($ucode)); //유니코드
				
				//$smsParam = "usercode=$this->usercode&deptcode=$this->deptcode&group_name=$this->nationCode-$v&from_num=$this->from&member=$this->cno&rurl=http://" .USER_HOST. "/_sync/sms_surem_response.php&to_message=$_msgX".$addedParam;				
				$smsParam = "usercode=$this->usercode&deptcode=$this->deptcode&group_name=$addedNumber"."$v&from_num=$this->from&member=$this->cno&rurl=http://" .USER_HOST. "/_sync/sms_surem_response.php&to_message=$decode_msgX".$addedParam;				
				
				//$this->url_debug($this->cno, $smsUrl ."?". $smsParam);
				$this->url_debug($this->cno, $smsParam);
				readurl($smsUrl ."?". $smsParam, 440, "POST");
								
				//debug($smsUrl ."?". $smsParam);
				//exit;
 			}
 		}
		 		
		$this->msg = $this->call._("개의 SMS를 발송하였습니다");
    //$this->limit -= $this->call;

    $this->disconnect();
		
	}

	function error($msg) {
		$this->error = true;
		$this->msg = $msg;
		$this->disconnect();
	}
	
	
	function url_debug($no, $log)
	{
		$query = "update exm_log_sms set sender_result = '$log' where no = $no";
		$this->db->query($query);
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