<?
include "../library.php"; 
//chkMember();
include "./extra_option_price_proc.php";
$db->query("set names utf8");
  
  $return_data = ""; 
    
  //센터와 몰을 구분하여 쿼리를 가져온다.
  $classExtraOption = new M_extra_option();  
  
  $same_item_name = $classExtraOption->getAdminSamePriceOptionItem($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[item_value], $_GET[option_kind_code]);
  if ($same_item_name)
    $_GET[item_value] = $same_item_name;
  
  $data = $classExtraOption->getAdminOptionChildItem($cid, $cfg_center[center_cid], $_GET[goodsno], $_GET[item_value], $_GET[option_kind_code]);
  //debug($data);
  $return_data = "";
  if (sizeof($data) > 0)
  {
    foreach ($data as $key => $value) {
      $item_name = strDelEntTab($value[item_name]);
      $return_data .= "<option value='$item_name' option_kind_code='$value[option_kind_code]' gap='3'>$item_name</option>\r\n";
    }
  }
  
  if ($return_data)
  {
    echo $return_data;
  } 
  
 
  
?>




