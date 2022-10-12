<?
include "../library.php"; 
//chkMember();
//$db->query("set names utf8");
  
  $return_data = "";  


  $classExtraOption = new M_extra_option();
  $data = $classExtraOption->getAdminSameOptionItemList($cid, $cfg_center[center_cid], $_POST[goodsno], $_POST[option_kind_index]);
//debug($data);

  foreach ($data as $key => $val) {
	 $return_data .= "<option value='$val[item_name]'>$val[item_name]</option>\r\n";
  }
 
  echo $return_data;
?>