<?
include_once dirname(__FILE__)."/class.sms.ilark.php";
include_once dirname(__FILE__)."/class.sms.exm.php";
include_once dirname(__FILE__)."/class.sms.surem.php";
class Sms
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

	var $sms;     //sms ilark 객체
	
	var $chargeUrl;
	var $logUrl;

   function Sms() {
      global $cfg_center, $cid, $cfg;

		if ($cfg[sms_service] == "exm") {
         $this->sms = new SmsExm();

			//call 이 없는 경우 ilark 으로 전환된다.
			if (!$this->sms->limit || $this->sms->limit < 0) 
				$this->sms = new SmsIlark();
      } else if ($cfg[sms_service] == "surem") {
         $this->sms = new SmsSurem();
		} else {
         $this->sms = new SmsIlark();
		}
      $this->cid = $this->sms->cid;
		
		$this->chargeUrl = $this->sms->chargeUrl;
		$this->logUrl = $this->sms->logUrl;
		
		$this->getLimit(); //콜수
   }

	function getLimit() {      
      $this->limit = $this->sms->limit;
		if (!$this->limit) $this->limit = 0;
	}

	function connect() {
		$this->sms->connect();
	}

	function disconnect() {
		$this->sms->disconnect();
	}

	function send($msg){
      $this->sms->to = $this->to;
      $this->sms->from = $this->from;
		$this->sms->name = $this->name;
		$this->sms->trandt = $this->trandt;

  	   $this->sms->send($msg);

  	   $this->msg = $this->sms->msg;
  	   $this->limit = $this->sms->limit;
	
		$this->error = $this->sms->error;
		$this->msg = $this->sms->msg;
	}

	function error($msg) {
		$this->sms->error($msg);
	}

	function sendmsgArr($tmp)
	{
		$this->sms->sendmsgArr($tmp);
	}

	function sendmsg($from,$to,$msg) {
		$this->sms->sendmsg($from,$to,$msg);
	}

	function log($msg) {
		$this->sms->log($msg);
		$this->cno = $this->sms->cno;
	}
}

?>