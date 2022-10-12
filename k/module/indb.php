<? 
	$printgroup_service_not_check_flag = "Y";			//printgroup 사용기간(여부) 체크 안하기.			20170104		chunter
	$login_check_flag = "N"; 							//k 관리자 체크 안하기.
?>
<?
include_once "../lib.php";
include_once dirname(__FILE__) . "/../../pretty/_module_util.php";
//printhome 업로드 주문 20140724   chunter

//추가는 남은 기간동안만 추가한다.  
  if ($_POST[storage_account_kind] == "1")
  {
    //현재 웹하드 사용하고 있을 경우만 1T부터 추가할수 있다.      //사용하고 있지 않을 경우 3t부터 선택해야 한다.
    if ($cfg[storage_date])
    {
      $upload_size = 0;
      $remain_month = (int)datediff($cfg[storage_date], date("Y-m-d"), "m");
    } 
    else
    { 
      $upload_size = "1";     //1 TB skip;
      $remain_month = "";
    }
  }
   
  //현재 사용량보다 적은 용량은 선택하지 못한다. 
  if ($_POST[storage_account_kind] == "2")
  {
  	$remain_month = "";
    //현재 웹하드 사용하고 있을 경우만 현재 용량 이후부터 추가할수 있다.    //사용하고 있지 않을 경우 3t부터 선택해야 한다.
    if ($cfg[storage_date])
    {
    	//사용기간이 지났을 경우 이전 월까지의 요금을 받는다.			20160408		chunter
    	//나중에 적용하다 하여 주석 처리로 진행(아래 2줄 처리 부분)		20160408		chunter
    	//if (date("Y-m-d") > $cfg[storage_date])			
    	//	$remain_month = (int)datediff(date("Y-m-d"), $cfg[storage_date],"m");
			
	    
	    $m_pretty = new M_pretty();
      $upload_size = $m_pretty->getUseFileTotalSize($cid);
            
      //$upload_size += getPodsStorageTotalSize($cfg[podsiteid]);
			$upload_size += $m_pretty->getUseEditFileTotalSize($cid);			//속도 개선. DB 에서 조회
			
      $upload_size = byteFormat($upload_size, "T", 2, false);
      
      if ($upload_size < 2) $upload_size = "1";     //기본 1T 부터
      //if ($upload_size < 0.5) $upload_size = "0.5";     //최소 단위를 0.5T 부터 할경우 이소스를 사용할것.			20151016		chunter
			
    } 
    else 
    {
      $upload_size = "1";     //1 TB skip;
    }    
    
  }
  
$result = "";
/***/
switch ($_POST[mode]) {

	//printgroup
	case 'service_price':
		$result = calcuServiceAccountPrice($cid, $_POST[service_month], $_POST[bDetailAccountInfo]);
		break;
	
	//printgroup	
	case 'service_month':
		$result = "<select name=\"storage_month\" onchange=\"calcu_price();\">";
		
		foreach ($r_printgroup_service_month as $key => $value) {
		  	if ($remain_month) {
		    	if ($remain_month == $key)
		    		$result .= "<option value='$key'>$key "._("개월")." </option>";
		  	}
		  	else {
		    	$result .= "<option value='$key'>$key "._("개월")." </option>";
			}
		}
		$result .=  "</select>";
		$result .= "<script>calcu_price();</script>";
		break;
		
		

	case 'storage_price':
		$result = calcuStorageAccountPrice($cid, $_POST[storage_size], $_POST[storage_month], $_POST[storage_account_kind], true);
		break;

  case 'storage_size':
    $result = "<select name=\"storage_size\" onchange=\"calcu_price();\">";
    foreach ($r_pretty_storage_price as $key => $value) {
       
      $showvalue = number_format($value);
      if ($upload_size > 0 && $key < 1) continue;     //연장은 1T 으로 표시

      if ($upload_size <= $key)
        $result .=  "<option value='$key'>$key "._("테라")."(TByte) - $showvalue "._("원")."</option>";
    }
    $result .= "</select>";
    $result .= "<script>calcu_price();</script>";

    break;

  case 'storage_month':
    $result = "<select name=\"storage_month\" onchange=\"calcu_price();\">";

    foreach ($r_pretty_storage_month as $key => $value) {
      if ($remain_month)
      {
        if ($remain_month == $key)
          $result .= "<option value='$key'>$key "._("개월")." </option>";
      }
      else
      {
        $result .= "<option value='$key'>$key "._("개월")." </option>";
      }
    }
    $result .=  "</select>";
    $result .= "<script>calcu_price();</script>";
    break;
	default:
		
		break;
}

echo $result;


?>
        