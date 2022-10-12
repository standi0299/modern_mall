<?
// pods 연동 인터페이스 처리  클래스      //인터페이스와 실제 연동Api 부분을 나누어 처리한다.  
include_once "class.pods.api.php";

class PODStation
{
  var $podsApi;
  var $version;
  
  function podstation($version = '10')
  {    
    $this->version = $version;    
    $this->podsApi = new PODStationApi($version);    
  }  
  
  function setVersion($version = '10')
  {    
    $this->version = $version;		
		$this->podsApi->setVersion($version);
  }
  
  // PODStation 연동 인터페이스 선언
  
  //##################################
  //실제 적용 인터페이스 시작  
  //##################################
  
  //custom 편집기의 버젼정보를 가져온다.     20150909    chunter
  function GetCustomMuduleVersion($site_id, $ediotr_no)
  {
    $result = "";
    if ($this->version == "20")
    {
      $rtn = $this->podsApi->GetCustomMuduleInfo_V20($site_id, $ediotr_no);
      
      if (is_array($rtn))
      {
        if ($rtn[0] == "success")
        {
          // success|CUSTOM_DLL_FLAG=커스텀편집기사용여부;CUSTOM_DLL_NAME=커스텀편집기모듈명;CUSTOM_DLL_PATH=커스텀편집기모듈경로;
          $rtnData = $this->podsApi->_ilark_vars($rtn[1]);
          if ($rtnData[CUSTOM_DLL_FLAG] == "Y")
          {
            //4.0 편집기인지
            if ($rtnData[CUSTOM_DLL_NAME] == "xpodeditor40")
              $result = "40";

            //else if ($rtnData[CUSTOM_DLL_NAME] == "xpodeditor40")
            //  $result = "35";
          }
          
        }
      }
    }
    return $result;
  }
  
  
  //cart 169Line 사용.
  function GetMultiOrderInfoResultPageData($storageid)
  {
    $result = "";
    
    if ($this->version == "20")
    {
      $wdata = $this->podsApi->GetMultiOrderInfoResult($storageid);

      $result[DATA] = $wdata[DATA];;
      
      //$pageData= $wdata[5];            
      //$result[DATA] = str_replace("DATA=", "", $pageData);
    }
    return $result;        
  }  
  
  
  //미리보기 이미지 리스트 가져오기 
  function GetPreViewImg($storageid)
  {
    return $this->podsApi->GetPreViewImg($storageid);
  }
	
	
	function GetPreViewImgToJson($storageid)
  {
    return $this->podsApi->GetPreViewImgToJson($storageid);
  }
  
  //오아이스 주문정보 연동
  function SetMakingOption($storageid, $optionArr)
  {    
    return $this->podsApi->SetMakingOption($storageid, $optionArr);        
  }
	
	
	function GetMultiOrderInfoResult($storageid, $isMultiple = false)
  {    
    if ($this->version == "10")
      return $this->podsApi->GetMultiOrderInfoResult_V10($storageid, $isMultiple);
    else if ($this->version == "20")
      return $this->podsApi->GetMultiOrderInfoResult_V20($storageid);
  }
  
	
	function GetMultiOrderInfoResultAllData($storageid)
  {    
    if ($this->version == "20")
      return $this->podsApi->GetMultiOrderInfoResult($storageid);
  }
	
	function SetOrderCountInfo($storageid, $ea)
  {    
    if ($this->version == "10")
      return $this->podsApi->SetOrderCountInfo_V10($storageid, $ea);
  }
	
	
	function GetSourceFileList($storageid, $outDecoding = 'utf-8')
  {
    if ($this->version == "10")
      return $this->podsApi->GetSourceFileList_V10($storageid, $outDecoding);
    else if ($this->version == "20")
      return $this->podsApi->GetSourceFileList_V20($storageid, $outDecoding);
  }
	
	
	function GetSiteUserStorageSize($siteCode, $orderUserId)
  {    
    if ($this->version == "20")
      return $this->podsApi->GetSiteUserStorageSize_V20($siteCode, $orderUserId);
  }
	
	//20160903		적용
	function SetOrderInfo($pod_data)
  {
    if ($this->version == "10")
      return $this->podsApi->SetOrderInfo_V10($pod_data);
    else if ($this->version == "20")
      return $this->podsApi->SetOrderInfo_V20($pod_data);    
  }
  
  
  function SetOrderInfo2($pod_data)
  {
    if ($this->version == "10")
      return $this->podsApi->SetOrderInfo2_V10($pod_data);        
  }
  
  
  function SetOrderInfoOption($pod_data)
  {
    if ($this->version == "20")
      return $this->podsApi->SetOrderInfoOption_V20($pod_data);    
  }
  
  
  function SetOrderInfo2Option($pod_data)
  {
    if ($this->version == "10")
      return $this->podsApi->SetOrderInfo2Option_V10($pod_data);        
  }
	
	
	function SetBPOrderinfo($pod_data)
  {
    if ($this->version == "20")
      return $this->podsApi->SetBPOrderinfo_V20($pod_data);
  }
	
	//선정산 데이타 전달			20161124		chunter
	function SetOrderInfoCalculationData($pod_data)
  {
    if ($this->version == "20")
      return $this->podsApi->SetOrderInfoCalculationData_V20($pod_data);
  }
	
	
	function GetPrintCountResult($storageid)
  {
    if ($this->version == "10")
      return $this->podsApi->GetPrintCountResult_V10($storageid);        
    else if ($this->version == "20")
      return $this->podsApi->GetPrintCountResult_V20($storageid);
  }
	
	//주문 취소 처리		20181214
	function SetOrderCancel($storageid)
  {
    if ($this->version == "20")
      return $this->podsApi->SetOrderCancel_V20($storageid);
  }
	
	
  //##################################
  //실제 미적용 인터페이스 시작  
  //##################################
  
  
  
  function SetReOrder($storageid)
  {
    if ($this->version == "10")
      return $this->podsApi->SetReOrder_V10($storageid);
    else if ($this->version == "20")
      return $this->podsApi->SetReOrder_V20($storageid);    
  }
  
  
  function UpdateStorageDate($storageid)
  {
    if ($this->version == "10")
      return $this->podsApi->UpdateStorageDate_V10($storageid);
    else if ($this->version == "20")
      return $this->podsApi->UpdateStorageDate_V20($storageid);    
  }
	
	
	function UpdateMultiStorageDate($storageids)
  {
    if ($this->version == "10")
      return $this->podsApi->UpdateMultiStorageDate_V10($storageids);
    else if ($this->version == "20")
      return $this->podsApi->UpdateMultiStorageDate_V20($storageids);    
  }
  
  //책등, 종이 타입 정보 가져오기
  function GetCoverOptionInfo($storageid, $pod_data = array())
  {
    if ($this->version == "20")
      return $this->podsApi->GetCoverOptionInfo_V20($storageid, $pod_data);    
  }
  
  
  function GetCartCompleteResult($storageid)
  {
    if ($this->version == "20")
      return $this->podsApi->GetCartCompleteResult_V20($storageid);    
  }

   //일반상품 유효성 체크 및 보관함코드 생성.
  function SetCreateStorageIdData($pod_data)
  {
    if ($this->version == "20")
      return $this->podsApi->SetCreateStorageId($pod_data);
  }

   function SetImpositionOption($param){
      if ($this->version == "20")
      return $this->podsApi->SetImpositionOption_V20($param);
   }
   
   //20181012 / minks / 배송정보만 수정
   function SetOrderInfoDelivery($pod_data) {
   	  if ($this->version == "20")
	  	return $this->podsApi->SetOrderInfoDelivery($pod_data);
   }
}

?>
