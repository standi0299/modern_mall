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
		  	} else {
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
   
   case "exec_editor" :
      $m_goods = new M_goods();
      //debug($_POST);
      $_POST[pods_use] = 2;
      $data = $m_goods->getInfo($_POST[goodsno]);

      if($_POST[pods_use] == "3"){
         //PodsCallWpodEditor(pods_use, podskind, podsno, '', tsid, tid, '');
      } else {
         //$url = "../module/popup_calleditor_v2.php?pods_use=".$data[pods_use]."&podskind=".$data[podskind]."&podsno=".$data[podsno]."&mode=view&goodsno=".$data[goodsno]."&optno=".$data[optno]."&addopt=".$data[addopt]."&productid=".$_POST[productid]."&optionid=".$_POST[optionid]."&ea=".$data[ea]."&templatesetid=".$_POST[templateSetIdx]."&templateid=".$_POST[templateIdx];
         //debug($url);
         //$return = "<a href=\"javascript:popupLayer('$url','','','',1);\">"._("편집 시작하기")."</a>";         

         $return = "<a href=\"javascript:call_exec_editor('$data[pods_use]','$data[podskind]','$data[podsno]','$data[goodsno]','$data[optno]','$data[addopt]','$_POST[productid]','$_POST[optionid]','$_POST[templateSetIdx]','$_POST[templateIdx]');\">"._("편집 시작하기")."</a>";
      }
      //debug($return);
      echo $return;
      exit;
   break;
   
   case "goods_link" :
      $m_goods = new M_goods();
      $data = $m_goods->getInfo($_POST[goodsno]);

      if($_POST[type_link] == "goods_name") {
         $return = "<a href='/goods/view.php?goodsno=".$data[goodsno]."&catno=".$_POST[catno]."'>".$data[goodsnm]."</a>";
      } else if ($_POST[type_link] == "goods_list_img") {
         
         $link = "http://".$cfg_center[host]."/data/goods/".$cid."/s/".$data[goodsno]."/".$data[listimg];
         $return = "<a href='/goods/view.php?goodsno=".$data[goodsno]."&catno=".$_POST[catno]."'><img src='".$link."'></a>";
         
      } else if($_POST[type_link] == "goods_detail_img_link") {
         
         $link = "http://".$cfg_center[host]."/data/goods/".$cid."/l/".$data[goodsno]."/".$_POST[img];
         $return = "<a href='/goods/view.php?goodsno=".$data[goodsno]."&catno=".$_POST[catno]."'><img src='".$link."' width='".$_POST[width]."' height='".$_POST[height]."'></a>";
         
      }

      echo $return;
      exit;
   break;
   
   case "goods_name" :
      $m_goods = new M_goods();

      $data = $m_goods->getInfo($_POST[goodsno]);
      $goodsnm = $data[goodsnm];

      $return = "<span>".$goodsnm."</span>";
      echo $return;
      exit;
   break;
   
   case "goods_img" :
      $m_goods = new M_goods();
      $data = $m_goods->getInfo($_POST[goodsno]);
      
      if ($_POST[type_print] == "goods_list_img") {
         //debug($cfg_center[host]);
         $link = "http://".$cfg_center[host]."/data/goods/".$cid."/s/".$data[goodsno]."/".$data[listimg];
         $return = "<img src='".$link."'>";
         
      } else if($_POST[type_print] == "goods_detail_img_print") {
         $link = "http://".$cfg_center[host]."/data/goods/".$cid."/l/".$data[goodsno]."/".$_POST[img];
         $return = "<img src='".$link."' width='".$_POST[width]."' height='".$_POST[height]."'>";
      }

      echo $return;
   break;
   
   case "goods_price" :
      $m_goods = new M_goods();

      $data = $m_goods->getInfo($_POST[goodsno]);
      $price = $data[cprice];
      
      if($_POST[type] == "origin")
         $return = $return = "<span>".$price."</span>";
      else
         $return = $return = "<span>".number_format($price)."</span>";
      echo $return;
      exit;
   break;
   
   case "goods_dc_price" :
      $m_goods = new M_goods();

      $data = $m_goods->getInfo($_POST[goodsno]);
      
      $price = $data[price];
      
      if($_POST[type] == "origin")
         $return = $return = "<span>".$price."</span>";
      else
         $return = $return = "<span>".number_format($price)."</span>";
      echo $return;
      exit;
   break;
   
   case "goods_detail" :
      $m_goods = new M_goods();

      $data = $m_goods->getInfo($_POST[goodsno]);
      
      $return = $data[desc];
      echo $return;
      exit;
   break;
   
   case "get_detail_img" :
      $m_goods = new M_goods();

      $data = $m_goods->getInfo($_POST[goodsno]);
      $tag = "";
      if($data[img]){
         $img = explode("||", $data[img]);
         foreach($img as $key => $val){
            if($val){
               $link = "http://".$cfg_center[host]."/data/goods/".$cid."/l/".$data[goodsno]."/".$val;
               $tag .= "<input type='radio' name='img' value='".$val."'>&nbsp;<img src='".$link."' width='200px'>&nbsp;";
            }
         }
      }

      echo $tag;
      exit;
   break;
   
   default:
		
   break;
}

echo $result;


?>
        