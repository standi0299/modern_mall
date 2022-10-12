<?
include_once "nusoap/lib/nusoap.php";

// pods 연동 관련 처리 클래스   개발은 하였으나 실제 bluepod 전체에 적용되지 않고 bluephoto 서비스 기능에 일부 적용되었음				20150113		chunter 

class PODStationApi {
   var $client;
   var $version;
   var $WebPageUrl;
  
   function PODStationApi($version) {
  	   $this->setVersion($version);
   }

	function setVersion($version = '') {
  	   if ($version == '') $version = '10';
  	   $this->version = $version;
      if ($version == "10") {
         $this->client = new soapclient("http://".PODS10_DOMAIN."/StationWebService/StationWebService.asmx?WSDL",true);
         $this->WebPageUrl = "http://".PODS10_DOMAIN."/StationWebService/";
      } else if ($version == "20") {
         $this->client = new soapclient("http://".PODS20_DOMAIN."/CommonRef/StationWebService/StationWebService.asmx?WSDL",true);
         $this->WebPageUrl = "http://".PODS20_DOMAIN."/CommonRef/StationWebService/";
      }

		$this->client->xml_encoding = "UTF-8";
      $this->client->soap_defencoding = "UTF-8";
      $this->client->decode_utf8 = false;
   }

   //통합 연동 처리
   function GetMultiOrderInfoResult($storageid) {
      //success|^|3|^|STORAGE_ID=DE1042012021400007;STATE=30;COUNT=21;PAGE=;PROGRESS=;DATA=[size=D3,count=10|size=3x5,count=6|size=4x5,count=5]|^|STORAGE_ID=DE1042012021400008;STATE=30;COUNT=3;PAGE=;PROGRESS=2/2;DATA=[skinId=p1,skinName=41010001,count=1|skinId=p2,skinName=41010002,count=2]|^|STORAGE_ID=DE1042012021400009;STATE=30;COUNT=1;PAGE=34;PROGRESS=34/34;DATA=[editorbase=4,per=2,sc=2,inc=4,pagecount=28,totalcount=34,skinName=book001.xml|book002.xml.....|]     
      $ret = readUrlWithcurl($this->WebPageUrl. 'GetMultiOrderInfoResult.aspx?storageids=' . $storageid, false);

      $retArray = explode("|^|",$ret);

      //$result = $this->_ilark_vars(substr($retArray[2],8));

      $result = $this->_ilark_vars($retArray[2]);
      $result[ID] = $result[STORAGE_ID];      //앞에서 8문자 자르지 않고 ID 키로 값을 한번더 넣어준다.

      return $result;
   }

   function GetPreViewImg($storageid) {  
      $ret = readUrlWithcurl($this->WebPageUrl.'GetPreViewImg.aspx?storageid=' . $storageid);
      $retArray = explode("|",$ret);
      $result = array_notnull($retArray);
      return $result;
   }
	 
	 
   function GetPreViewImgToJson($storageid) {  
      $ret = readUrlWithcurl($this->WebPageUrl.'GetPreViewImgToJson.aspx?storageid=' . $storageid);
      $retArray = json_decode($ret, TRUE);
      $result = array_notnull($retArray);
      return $result;
   }

   //오아시스 주문옵션 정보 설정.
   function SetMakingOption($storageid, $makeOptionArr) {
      $pod_data = array();
      $pod_data[storageid] = $storageid;

      $makeOption = "";
      foreach ($makeOptionArr as $key => $value) {
         if ($this->version =='10')
            $value = iconv('utf-8', 'euc-kr', $value);

         $makeOption .= $key . "$". urlencode($value) ."^";
      }
      //$pod_data[option] = "kids_cor_name$$kids_cor_name^kids_class_name$$kids_class_name^kids_stu_name$$kids_stu_name";
      $pod_data[option] = $makeOption;

      //$ret = $this->client->call('SetMakingOption',$pod_data);
      //$result = explode("|",$ret[SetMakingOptionResult]);    
      $ret = readUrlWithcurl($this->WebPageUrl.'SetMakingOption.aspx?storageid=' . $storageid. '&option='. $makeOption);
      $result = explode("|",$ret);
      return $result;
   }

   function SetOrderInfo($pod_data = array()) {
      $ret = $this->client->call('SetOrderInfo',$pod_data);
      $result = explode("|",$ret[SetOrderInfoResult]);
      return $result;
   }

   function GetPrintCountResult($storageid) {
      $ret = $this->client->call('GetPrintCountResult',array('storageid'=>$storageid));
      $result = explode("|",$ret[GetPrintCountResultResult]);
      return $result;
   }

   //*************** pods 1.0 전용 연동 메소드
   //*********************************

   //*************** pods 2.0 전용 연동 메소드
   //*********************************

   //책등, 종이 타입 정보 가져오기   
   function GetCoverOptionInfo_V20($storageid = '', $pod_data = array()) {
      $pod_data[storageid] = $storageid;
      $ret = $this->client->call('GetCoverOptionInfo',$pod_data);
      $result = explode("|",$ret[GetCoverOptionInfoResult]);
      //print_r($ret);
      return $result;
   }

   function GetCustomMuduleInfo_V20($site_id, $editor_no) {
      // success|CUSTOM_DLL_FLAG=커스텀편집기사용여부;CUSTOM_DLL_NAME=커스텀편집기모듈명;CUSTOM_DLL_PATH=커스텀편집기모듈경로;
      $ret = readUrlWithcurl($this->WebPageUrl.'GetCustomDllResult.aspx?siteCode=' . $site_id . "&editorNo=" .$editor_no);
      $result = explode("|",$ret);
      //$result = $this->_ilark_vars($wdata[1]);    
      return $result;
   }

	function GetSiteUserStorageSize_V20($siteCode, $orderUserId) {
  	   $ret = readUrlWithcurl($this->WebPageUrl.'/GetSiteUserStorageSize.aspx?siteCode=' . $siteCode . "&orderUserId=" .$orderUserId);
		$result = explode("|",$ret);
      return $result;
   }
	
	//주문 취소 연동			20181214		chunter
	function SetOrderCancel_V20($storageid) {
  	$ret = readUrlWithcurl($this->WebPageUrl.'/SetOrderCancel.aspx?storageid=' . $storageid);
		$result = explode("|",$ret);
    return $result;
  }
	
	
	

   //*********************************
   //*************** pods 연동중 확인 되지 않거나 실제 적용되지 않은 내용들 시작임
   //*********************************    

   //*************** pods 1.0 연동 메소드
   //*********************************  

   function GetMultiOrderInfoResult_V10($storageid, $isMultiple) {
      $ret = $this->client->call('GetMultiOrderInfoResult',array('storageids'=>$storageid));
      list($flag) = explode("|",substr($ret[GetMultiOrderInfoResultResult],0,8));
      $ret_result = explode("|^|", $ret);
		if ($isMultiple) {
			return $ret_result;
		} else {
    	 $result = $this->_ilark_vars(substr($ret_result[2], 8));
    	 return $result;
		}
   }

   function SetOrderCountInfo_V10($storageid, $ea) {
      //pods에 넘겨줄 데이터
      $pod_data[orderId] = $storageid;
      $pod_data[count] = $ea;

      $ret = $this->client->call('SetOrderCountInfo', $pod_data);
      $result = explode("|", $ret[SetOrderCountInfoResult]);
      return $result;
   }

   function GetSourceFileList_V10($storageid, $outDecoding = 'utf-8') {
      $ret = readUrlWithcurl($this->WebPageUrl.'GetSourceFileList.aspx?storageid=' . $storageid);
		if ($outDecoding == 'utf-8')
         $ret = iconv("EUC-KR", "UTF-8", $ret);
      $result = explode("|", $ret);

      return $result;
   }

   function SetOrderInfo_V10($pod_data = array()) {
      $ret = $this->client->call('SetOrderInfo',$pod_data);
      $result = explode("|",$ret[SetOrderInfoResult]);
      return $result;
   }

   function SetOrderInfo2_V10($pod_data = array()) {
      $ret = $this->client->call('SetOrderInfo2',$pod_data);
      $result = explode("|",$ret[SetOrderInfo2Result]);
      return $result;
   }
  
   function SetOrderInfo2Option_V10($pod_data = array()) {
      $ret = $this->client->call('SetOrderInfo2Option',$pod_data);
      $result = explode("|",$ret[SetOrderInfo2OptionResult]);
      return $result;
   }  

   function GetPrintCountResult_V10($storageid) {
      $ret = $this->client->call('GetPrintCountResult',array('storageid'=>$storageid));
      $result = explode("|",$ret[GetPrintCountResultResult]);
      return $result;
   }

   function GetPreViewImg_V10($storageid) {
      $ret = $this->client->call('GetPreViewImg',array('storageid'=>$storageid));
      $ret = explode("|",$ret[GetPreViewImgResult]);
      $result = array_notnull($ret);
      return $result;
   }

  //재주문 처리
  function SetReOrder_V10($storageid)
  {
    $ret = $this->client->call('SetReOrder',array('storageid'=>$storageid));
    $result = explode("|",$ret[SetReOrderResult]);
    return $result;
  }

  function UpdateStorageDate_V10($storageid)
  {
    $ret = $this->client->call('UpdateStorageDate',array('storageid'=>$storageid));
    $result = explode("|",$ret[UpdateStorageDateResult]);
    return $result;
  }

   function UpdateMultiStorageDate_V10($storageids) {
  	   if ($storageids) {
         $ret = $this->client->call('UpdateMultiStorageDate',array('storageids'=>$storageids));
         $result = explode("|",$ret[UpdateMultiStorageDateResult]);
		} else {
			$result = "FAIL";
		}
      return $result;
   }

   //*************** pods 2.0 연동 메소드
   //*********************************
   function GetCartCompleteResult_V20($storageid) {
      $ret = $this->client->call('GetCartCompleteResult',array('storageid'=>$storageid));
      list($flag,$ret) = explode("|",$ret[GetCartCompleteResultResult]);
      $result = $ret;
      return $result;
   }

   function GetMultiOrderInfoResult_V20($storageid) {
      //success|^|3|^|STORAGE_ID=DE1042012021400007;STATE=30;COUNT=21;PAGE=;PROGRESS=;DATA=[size=D3,count=10|size=3x5,count=6|size=4x5,count=5]|^|STORAGE_ID=DE1042012021400008;STATE=30;COUNT=3;PAGE=;PROGRESS=2/2;DATA=[skinId=p1,skinName=41010001,count=1|skinId=p2,skinName=41010002,count=2]|^|STORAGE_ID=DE1042012021400009;STATE=30;COUNT=1;PAGE=34;PROGRESS=34/34;DATA=[editorbase=4,per=2,sc=2,inc=4,pagecount=28,totalcount=34,skinName=book001.xml|book002.xml.....|]
      //PODS에서 utf-8로 넘기기 때문에 인코딩을 하지 않도록 2번째 파라미터를 false로 넘김 / 19.01.23 / kjm     
      $ret = readUrlWithcurl($this->WebPageUrl. 'GetMultiOrderInfoResult.aspx?storageids=' . $storageid, false);
      return $ret;
   }

   function SetOrderInfo_V20($pod_data = array()) {
      $ret = $this->client->call('SetOrderInfo3',$pod_data);
      $result = explode("|",$ret[SetOrderInfo3Result]);
      return $result;
   }

   function SetOrderInfoOption_V20($pod_data = array()) {
      $ret = $this->client->call('SetOrderInfoOption',$pod_data);
      $result = explode("|",$ret[SetOrderInfoOptionResult]);
      return $result;
   }

   function GetPreViewImg_V20($storageid) {
      //$ret = $this->client->call('GetPreViewImg',array('storageid'=>$storageid));
      //$ret = explode("|",$ret[GetPreViewImgResult]);

      $ret = readUrlWithcurl($this->WebPageUrl.'GetPreViewImg.aspx?storageid=' . $storageid);
      $retArray = explode("|",$ret);
      $result = array_notnull($retArray);
      return $result;
   }

   //재주문 처리
   function SetReOrder_V20($storageid) {
      $ret = $this->client->call('SetReOrder',array('storageid'=>$storageid));
      $result = explode("|",$ret[SetReOrderResult]);
      return $result;
   }

   function UpdateStorageDate_V20($storageid) {
      $ret = $this->client->call('UpdateStorageDate',array('storageid'=>$storageid));
      $result = explode("|",$ret[UpdateStorageDateResult]);
      return $result;
   }

	function UpdateMultiStorageDate_V20($storageids) {
      if ($storageids) {    
	     $ret = $this->client->call('UpdateMultiStorageDate',array('storageids'=>$storageids));
	     $result = explode("|",$ret[UpdateMultiStorageDateResult]);
	 	} else {
	 		$result = "FAIL";
	 	}
      return $result;
   }

   function GetPrintCountResult_V20($storageid) {
      $ret = $this->client->call('GetPrintCountResult',array('storageid'=>$storageid));
      $result = explode("|",$ret[GetPrintCountResultResult]);
      return $result;
   }

 	function GetSourceFileList_V20($storageid, $outDecoding = 'utf-8') {
  	   $ret = readUrlWithcurl($this->WebPageUrl.'GetSourceFileList.aspx?storageid=' . $storageid);
		if ($outDecoding == 'utf-8')
			$ret = iconv("EUC-KR", "UTF-8", $ret);
      $result = explode("|", $ret);
      return $result;
   }

	function SetBPOrderinfo_V20($pod_data) {
      //$ret = readUrlWithcurl($this->WebPageUrl.'bp_setorderinfo.aspx?data=' . $pod_data);
      //20161118 / mink / get방식으로 호출시 파라미터 잘림현상이 발생하므로 post방식으로 호출
	  $ret = readurl($this->WebPageUrl.'bp_setorderinfo.aspx?data=' . $pod_data,80,"POST",'', true);
      $result = explode("|",$ret);
      return $result;
   }

	function GetTempProductList_V20($pod_data) {
      $result = readUrlWithcurl($this->WebPageUrl.'temp_product_list.aspx?' . $pod_data);
      return $result;
   }

	function GetTempProductList2_V20($pod_data) {
      $result = readUrlWithcurl($this->WebPageUrl.'temp_product_list2.aspx?' . $pod_data);
      return $result;
   }

	//pods 으로 선정산 데이타 전송.			20161123		chunter
	function SetOrderInfoCalculationData_V20($pod_data) {
      $result = sendPostData($this->WebPageUrl.'SetOrderInfoCalculationData.aspx', $pod_data);
      return $result;
   }

   //일반상품 유효성 체크 및 보관함코드 생성.
   function SetCreateStorageId($pod_data = array()) {
      $ret = $this->client->call('SetCreateStorageId',$pod_data);
      $result = explode("|",$ret[SetCreateStorageIdResult]);
      return $result;
   }

  //*************** 공통 함수 모음   

   function _ilark_vars($vars, $flag = ";") {
      $r = array();
      $div = explode($flag, $vars);
      foreach ($div as $tmp) {
         $pos = strpos($tmp, "=");
         list($k, $v) = array(substr($tmp, 0, $pos), substr($tmp, $pos + 1));
         $r[$k] = $v;
      }

      return $r;
   }

   function PODS2Parse($pod_kind, $pod_version, $storageid) {
      //3.5 편집기 리턴시 처리
      if (in_array($pod_kind,array("3030", "3040", "3041", "3050", "3230", "3130", "3110", "3112"))) {
         $ret = stripslashes($storageid);
         $ret = json_decode($ret,1);
         
         foreach ($ret[uploaded_list] as $k=>$v) {
            parse_str($v[session_param],$retdata);

            $retdata[sessionparam] = explode(",",urldecode($retdata[sessionparam]));
            if ($retdata[sessionparam][1]){
               $retdata[sessionparam][1] = str_replace("param:","",$retdata[sessionparam][1]);
            } else if ($retdata[sessionparam][0]){
               $retdata[sessionparam][1] = str_replace("param:","",$retdata[sessionparam][0]);
            }

            $indata = json_decode(base64_decode($retdata[sessionparam][1]),1);

            if (!is_array($indata[addopt])) $indata[addopt] = explode(",",$indata[addopt]);
            $data[$k][goodsno]   = $indata[goodsno];
            $data[$k][storageid] = $v[rsid];
            $data[$k][optno]     = $indata[optno];
            $data[$k][ea]        = $v[order_count];
            if (!$data[$k][ea]) $data[$k][ea] = 1;
            $data[$k][addopt]    = $indata[addopt];
            $data[$k][title]     = $v[title];
         }
      }
   }

   //3055편집기의 내지 사이즈 전달
   function SetImpositionOption_V20($pod_data) {
      $param = "storageid=".$pod_data[storageid];
      
      $param .= "&option=";
      if($pod_data[imposition_inside_width])
         $param .= "imposition_inside_width$".$pod_data[imposition_inside_width];
      
      if($pod_data[imposition_inside_height])
         $param .= "^imposition_inside_height$".$pod_data[imposition_inside_height];
      
      if($pod_data[imposition_cover_width])
         $param .= "^imposition_cover_width$".$pod_data[imposition_cover_width];
      
      if($pod_data[imposition_cover_height])
         $param .= "^imposition_cover_height$".$pod_data[imposition_cover_height];

      $result = sendPostData($this->WebPageUrl.'SetImpositionOption.aspx', $param);
      return $result;
   }
   
   //20181012 / minks / 배송정보만 수정
   function SetOrderInfoDelivery($pod_data) {
   	  $result = sendPostData($this->WebPageUrl.'SetOrderInfoDelivery.aspx', $pod_data);
      return $result;
   }
}

?>