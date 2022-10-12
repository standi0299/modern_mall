<?
function f_banner_pretty_main_backgroud($code, $text_cnt = 1, $add_type = 'Y', $image_type = 'Y') {
    global $db, $cfg, $cid, $sess, $language_locale;

    $dir = "/skin/$cfg[skin]/img/_banner/$code/";

    //header.php 에서 조회해혼 공통 배너정보가 존재하는 체크한다.    20140403    chunter
    if (is_array($r_user_banner[$code]))
        $data = $r_user_banner[$code];
    else {
        $query = "select * from exm_banner where cid = '$cid' and skin = '$cfg[skin]' and code = '$code'";
        $data = $db -> fetch($query);
    }
          

    if ($data[img]) {
        $data[img] = explode("||", $data[img]);
        $foreach_data = $data[img];
    }
    
    if ($data[spc_desc]) {
      $data[spc_desc] = explode("||", $data[spc_desc]);    
    }
    
        
    //배너 경로가 저장된 경우.    20140623  chunter
    if ($data[file_path])
        $dir = $data[file_path];
    
    if (is_array($foreach_data)) 
    {
      foreach ($foreach_data as $k => $v) 
      {
        $link_tag = "";

        unset($img);
            
        if ($data[img][$k]) {
          $img[img] = $dir . $data[img][$k];
        }
              
        $textIndex = $k * $text_cnt;
        if ($data[spc_desc][$textIndex]) {
          //debug($data[spc_desc][$k]);                
          $img[spc_desc] = str_replace("\r\n", "<BR />", $data[spc_desc][$textIndex]);
          $img[spc_desc2] = str_replace("\r\n", "<BR />", $data[spc_desc][$textIndex + 1]);
        }

              
        $back_image = $img[img];
        $back_height = $img[spc_desc];
        $back_sub_height = $back_height - 58;
      }            
    }        

    
  
?>

  <style type="text/css">    
  <?  if ($back_image)  { ?>  
    #wrap {background:url("<?=$back_image?>") no-repeat center 0;}
    #wrap .header-back {height:<?=$back_height?>px}          
  <?  } else {  ?>  
    #wrap {background:url(/skin/pretty/img/comm/top_visual.jpg) no-repeat center 0;}
    #wrap .header-back {height:197px}    
  <?  } ?>
  </style>

<?
  
    
  if ($GLOBALS[ici_admin])
	{
		if ($language_locale == "")
    	echo "<div class='_banner_text' banner_type='s2' image_type='Y' text_cnt='0' add_type='N' style='position:absolute;left:0;top:0;border:1px dotted #dedede;height:100px;' code='_sys_main_backgroud'>"._("배너 - 배경 이미지")."($code)"._("영역")."<br/>"._("최대너비")." : <b>"._("IE 창 전체")."</b></div>";       
		else 
			echo "<div class='_banner_text' banner_type='s2' image_type='Y' text_cnt='0' add_type='N' style='position:absolute;left:0;top:0;border:1px dotted #dedede;height:100px;' code='_sys_main_backgroud'>Banner - Backgroud Image($code) Area<br/>Max Width : <b>IE Full Size</b></div>";		
	}
  else 
    echo "";
}
?>