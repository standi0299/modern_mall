<?
//text 배너만을 처리하기 위한 함수    20140708    chunter
function f_banner_text($code){

	global $db,$cfg,$cid,$sess,$r_sys_banner,$r_user_banner;

	$dir = "/skin/$cfg[skin]/img/_banner/$code/";
  
    //header.php 에서 조회해혼 공통 배너정보가 존재하는 체크한다.    20140403    chunter
    if (is_array($r_user_banner[$code]))
        $data = $r_user_banner[$code];
        else 
        {    
            $query = "select * from exm_banner where cid = '$cid' and skin = '$cfg[skin]' and code = '$code'";
            $data = $db->fetch($query);
        }
	
        $data[spc_desc] = explode("||",$data[spc_desc]);


	   foreach ($data[spc_desc] as $k=>$v)
	   {      
            //배너에 텍스트 출력 추가 . 이미지가 출력 우선순위가 높기때문에 text 를 먼저 불러온다.    20140626  chunter
        if ($v && $data[spc]!="sys")
        {
            //debug($data[spc_desc][$k]);
            $img[spc_desc] .= $v;
            $textBanner = str_replace("\r\n", "<BR>", $v); 
            $img[banner] = "<div class='_banner_text' code='$code'>" .$textBanner. "</div>";

            if ($data[url][$k]){
            $data[url][$k] = str_replace("{mid}",$sess[mid],$data[url][$k]); // {mid} 치환 whenji 20120926
            $img[url] = $data[url][$k];
            $img[target] = $data[target][$k];
        }            
    }        

    if ($img[banner])
        $loop[] = $img;
    }

	if (!$loop && $GLOBALS[ici_admin]){
		$loop[0][banner] = "<div class='_banner_text' style='border:1px dotted #dedede;' code='$code'>"._("배너")."($code)"._("영역")."<br/>"._("최대너비")." : <b class='banner_width_span'></b></div>";
	}

	return $loop;
}

//About/Contact Us/Deposit Account 하단 정보 클릭시 관리자에서 변경하라는 메세지 (이메일, 예금주 등등) 2015.02.24 by kdk
function f_banner_text_msg($code, $contents) {
	global $db,$cfg,$cid,$sess;

	if ($GLOBALS[ici_admin]){
		
	    if ($contents) {
	        $textBanner = str_replace("\r\n", "<BR>", $contents); 
	        $loop[0][banner] = "<span class='_banner_text_msg' code='$code'>" .$textBanner. "</span>";
	    }
		else {
			$loop[0][banner] = "<span class='_banner_text_msg' style='border:1px dotted #dedede;' code='$code'>"._("영역 최대너비")." : <b class='banner_width_span'></b></span>";
		}
		
	}
	else {
		$loop[0][banner] = $contents;
	}

	return $loop;
}
?>