<?

include "../../lib/library.php";
include_once dirname(__FILE__)."/../../models/m_extra_option.php";
set_time_limit(0); //120 //테스트용 타임아웃 제한 풀기 2015.04.16 by kdk
ini_set('memory_limit','-1'); //테스트용 임시 메모리 제한 풀기 2015.04.16 by kdk

try {
	//$db->start_transaction();

$addWhere = " and option_group_type = '".$_REQUEST[mode2]."OPTION' ";
if($_REQUEST[mode2] == "AFTER") {
	//후가공 코드가 있으면...
	if($_REQUEST[kind]) { 
		$addWhere .= " and option_kind_code = '$_REQUEST[kind]' ";
	}
}
else {	
	if($_REQUEST[goodskind] == "BOOK") {
		//내지옵션이면...
		if($_REQUEST[mode2] == "F-FIX") {
			$addWhere = " and option_group_type = 'FIXOPTION' ";
		}
		elseif($_REQUEST[mode2] == "F-SEL") {
			$addWhere = " and option_group_type = 'SELOPTION' ";
		}
	}
}	

switch ($_REQUEST[mode]){
	
case "price_delete_s2":
	
	if($_REQUEST[mode2] == "AFTER") {
		$optionGroupType = $_REQUEST[kind]; 
	}
	else {
		$optionGroupType = $_REQUEST[mode2];
	}
	
	//전체 가격 DB를 삭제한다.
  	$classExtraOption = new M_extra_option();
	$classExtraOption->DeleteExtraOptionS2group($cid, $cfg_center[center_cid], $_REQUEST[goodsno], $optionGroupType);

	break;	
	
case "price_delete":
	
	//전체 가격 DB를 삭제한다.
  	$classExtraOption = new M_extra_option();
	$classExtraOption->DeleteExtraOptionS2($cid, $cfg_center[center_cid], $_REQUEST[goodsno]);
	
	break;

case "optionjson_delete":
	
    //속도 저하 문제로 저장된 옵션 데이타를 json 삭제 처리한다. 2014.10.08 by kdk
  	$file_name = "../../data/goods_option/{$_REQUEST[goodsno]}_option.json";
  	if(file_exists($file_name)) {
  		unlink($file_name);
  	}
	
	break;
	
case "price_update":
  
  $optionPriceData = array();
  

  foreach ($_POST as $ItemKey => $ItemValue) 
  {
	if(!$ItemValue || $ItemValue == "") $ItemValue = "0";
	
    //supply_price_15_100     
    //sale_price_15_100
    //원가 (공급가) 설정
    if (strpos($ItemKey, "supply_price_") !== false )
    {
      $postArr = split('_', $ItemKey);      

	  //print_cnt_0~1000~1 / supply_price_163_0~1000~1 시작~종료~구간이 있을 경우 구간을 삭제함. 2014.07.01 by kdk
	  $postArrCnt = split('~', $postArr[3]);
	
	  if (sizeof($postArrCnt) > 2) {
	  	$postArr[3] = $postArrCnt[0]."~". $postArrCnt[1];
	  }
	  
      $optionPriceData[$postArr[2]][$postArr[3]]['supply'] = $ItemValue;     //공급가
      $optionPriceData[$postArr[2]][$postArr[3]]['print_cnt'] = $postArr[3];     //출력수량
      
      //$optionPriceData[$postArr[2]]['supply'] = $ItemValue;     //공급가
      //$optionPriceData[$postArr[2]]['print_cnt'] = $postArr[3];     //출력수량   
    }
    
    //권장 판매가 (소비자 판매가) 설정
    if (strpos($ItemKey, "sale_price_") !== false )
    {
      $postArr = split('_', $ItemKey);      

	  //print_cnt_0~1000~1 / supply_price_163_0~1000~1 시작~종료~구간이 있을 경우 구간을 삭제함. 2014.07.01 by kdk
	  $postArrCnt = split('~', $postArr[3]);
	  if (sizeof($postArrCnt) > 2) {
	  	$postArr[3] = $postArrCnt[0]."~". $postArrCnt[1];
	  }
	        
      $optionPriceData[$postArr[2]][$postArr[3]]['sale'] = $ItemValue;     //판매가
      $optionPriceData[$postArr[2]][$postArr[3]]['print_cnt'] = $postArr[3];
      
      //$optionPriceData[$postArr[2]]['sale'] = $ItemValue;     //판매가
      //$optionPriceData[$postArr[2]]['print_cnt'] = $postArr[3];
    }
  }
  
  //debug($optionPriceData);    

  //전체 DB 에 저장한다.
  $classExtraOption = new M_extra_option();  
  foreach ($optionPriceData as $ItemKey => $ItemValue) 
  {
    $optionPriceText = "";
    foreach ($ItemValue as $priceKey => $priceValue) 
    {
      //가격설정 데이타 만들기.
      $optionPriceText .= "$priceValue[print_cnt]~$priceValue[supply]~$priceValue[sale];";        
    }
    
    //echo $ItemKey." - ".$optionPriceText."<BR>";
    //exit;
    
    //테스트
    //$cid = 'bpc';
    //$cfg_center[center_cid] = '';

    if($_REQUEST[goodskind] == "CARD" && $_REQUEST[mode2] == "FIX") {
    	$addWhere = "";
    }    
        
    $classExtraOption->setOptionItemPrice($cid, $cfg_center[center_cid], $_POST[goodsno], $ItemKey, $optionPriceText, $addWhere);
  }
  
  break;
  
  
  
case "price_update_s2":
  $optionPriceData = array();
  foreach ($_POST as $ItemKey => $ItemValue) 
  {
    if(!$ItemValue || $ItemValue == "") $ItemValue = "0";
  
    //supply_price_15_100     
    //sale_price_15_100
    //원가 (공급가) 설정
    if (strpos($ItemKey, "supply_price_") !== false )
    {
      $postArr = split('_', $ItemKey);      

      //print_cnt_0~1000~1 / supply_price_163_0~1000~1 시작~종료~구간이 있을 경우 구간을 삭제함. 2014.07.01 by kdk
      $postArrCnt = split('~', $postArr[3]);
  
      if (sizeof($postArrCnt) > 2) {
        $postArr[3] = $postArrCnt[0]."~". $postArrCnt[1];
      }
    
      $optionPriceData[$postArr[2]][$postArr[3]]['supply'] = $ItemValue;     //공급가
      $optionPriceData[$postArr[2]][$postArr[3]]['print_cnt'] = $postArr[3];     //출력수량
      
      //$optionPriceData[$postArr[2]]['supply'] = $ItemValue;     //공급가
      //$optionPriceData[$postArr[2]]['print_cnt'] = $postArr[3];     //출력수량   
    }
    
    //권장 판매가 (소비자 판매가) 설정
    if (strpos($ItemKey, "sale_price_") !== false )
    {
      $postArr = split('_', $ItemKey);      

      //print_cnt_0~1000~1 / supply_price_163_0~1000~1 시작~종료~구간이 있을 경우 구간을 삭제함. 2014.07.01 by kdk
      $postArrCnt = split('~', $postArr[3]);
      if (sizeof($postArrCnt) > 2) 
      {
        $postArr[3] = $postArrCnt[0]."~". $postArrCnt[1];
      }
          
      $optionPriceData[$postArr[2]][$postArr[3]]['sale'] = $ItemValue;     //판매가
      $optionPriceData[$postArr[2]][$postArr[3]]['print_cnt'] = $postArr[3];
      
      //$optionPriceData[$postArr[2]]['sale'] = $ItemValue;     //판매가
      //$optionPriceData[$postArr[2]]['print_cnt'] = $postArr[3];
    }
    
    if (strpos($ItemKey, "option_item_") !== false ) 
    {
      $optionKey = str_replace("option_item_", "", $ItemKey);
      $optionItemData[$optionKey] = $ItemValue;
    }
  }
  
  //debug($optionPriceData);
  
  $optionGroupType = $_POST[mode2];
  //debug($optionGroupType);
  if($optionGroupType == "AFTER") $optionGroupType = $_POST[kind]; //후가공이면 option_kind 를 사용한다.
  
  if ($_POST[mode3] == "update" && $_POST[all_delete] == "Y")
  {
    $sql = "delete from tb_extra_option_price_info where cid = '$cid' and bid = '$cfg_center[center_cid]' and goodsno='$_POST[goodsno]' and option_group_type='$optionGroupType'";
    //echo $sql . "<BR>";
    $db->query($sql);
  }
    

  //전체 DB 에 저장한다.
  $classExtraOption = new M_extra_option();  
  $order_index = 0;
  foreach ($optionPriceData as $ItemKey => $ItemValue) 
  {
    $optionPriceText = "";
    foreach ($ItemValue as $priceKey => $priceValue) 
    {
      //가격설정 데이타 만들기.
      $optionPriceText .= "$priceValue[print_cnt]~$priceValue[supply]~$priceValue[sale];";        
    }
    
    //echo $ItemKey." - ".$optionPriceText."<BR>";
    //exit;
    
    //테스트
    //$cid = 'bpc';
    //$cfg_center[center_cid] = '';

    if($_REQUEST[goodskind] == "CARD" && $_REQUEST[mode2] == "FIX") {
      $addWhere = "";
    }
    
    if ($_POST[mode3] == "new" || $_POST[all_delete] == "Y")
    {
      $OptionItemData = $optionItemData[$ItemKey];    
      //$classExtraOption->setOptionItemPrice($cid, $cfg_center[center_cid], $_POST[goodsno], $ItemKey, $optionPriceText, $addWhere);
      $sql = "insert into tb_extra_option_price_info set cid = '$cid', bid = '$cfg_center[center_cid]', option_item='$OptionItemData', option_price='$optionPriceText', goodsno='$_POST[goodsno]', order_index='$order_index', option_group_type='$optionGroupType', regist_date=now(), update_date=now()";
      //echo $sql . "<BR>";
      $db->query($sql);
      $order_index++;
    }
    else
    {
      $sql = "update tb_extra_option_price_info set option_price='$optionPriceText', update_date=now() where 
      cid = '$cid' and bid = '$cfg_center[center_cid]' and goodsno='$_POST[goodsno]' and option_group_type='$optionGroupType' and ID=$ItemKey";
      //echo $sql . "<BR>";
      $db->query($sql);
      
      //$classExtraOption->setOptionItemPrice($cid, $cfg_center[center_cid], $_POST[goodsno], $ItemKey, $optionPriceText, $addWhere);
    }
  }
  
  $_SERVER[HTTP_REFERER] = str_replace("&all_delete=Y","",$_SERVER[HTTP_REFERER]);
  //echo($_SERVER[HTTP_REFERER]);
  msg(_("완료되었습니다."),$_SERVER[HTTP_REFERER]);
  break;
  exit;

}

	//$db->end_transaction();
}
catch(Exception $ex){			
	msg("$ex->getMessage()",-1);
	exit;
}
		
//echo($_SERVER[HTTP_REFERER]);
go($_SERVER[HTTP_REFERER]);

?>