<?
function f_banner($code, $image_type = '', $width = 0, $height = 0) {
    global $db, $cfg, $cid, $sess, $r_sys_banner, $r_user_banner, $r_printhome_skin, $language_locale;
//debug($code);
    //$dir = "/skin/$cfg[skin]/img/_banner/$code/";
    $dir = "/data/banner/$cid/$code/";

    //header.php 에서 조회해혼 공통 배너정보가 존재하는 체크한다.    20140403    chunter
    if (is_array($r_user_banner[$code]))
        $data = $r_user_banner[$code];
    else {
        $query = "select * from exm_banner where cid = '$cid' and skin = '$cfg[skin]' and code = '$code'";
        $data = $db -> fetch($query);
    }
//debug($data);

	if ($data[spc] == "text") 
	{
	   	$data[spc_desc] = explode("||",$data[spc_desc]);

	   	foreach ($data[spc_desc] as $k=>$v)
	   	{      
            //배너에 텍스트 출력 추가 .
        	if ($v)
        	{
            	//debug($data[spc_desc][$k]);
            	$img[spc_desc] .= $v;
            	$textBanner = str_replace("\r\n", "<BR>", $v); 
            	$img[banner] = "<div class='_banner' code='$code'>" .$textBanner. "</div>";
    		}        

    		if ($img[banner])
        		$loop[] = $img;
    	}
	   	//debug($loop);
	}
	else if ($data[spc] == "edit") 
	{
    	$img[spc_desc] = $data[spc_desc];
    	$img[banner] = "<div class='_banner' code='$code'>" .$data[spc_desc]. "</div>";
		if ($img[banner])
			$loop[] = $img;
	}
	else if ($data[spc] == "sys") 
	{
        $src = $dir . $data[img][0];
        $src2 = $dir . $data[img_on][0];
        $sys_src2 = $r_sys_banner[$code]['src2'];
        if (!$data[img][0]) {
            $src = "/_sys_banner/" . $cfg[skin] . "/" . $r_sys_banner[$code]['src'];
        }
        $loop[0][banner] = str_replace('--banner--', $src, $r_sys_banner[$code]['shape']);
        if (!$data[img_on][0]) {
            $src2 = "";
        }
        //20140703 / minks / 추가한 배너 오버이미지가 없고 기본 배너 오버이미지가 있을 경우 이벤트 적용되도록 수정
        if (!$src2 && $sys_src2) {
            $sys_src2 = "/_sys_banner/" . $cfg[skin] . "/" . $r_sys_banner[$code]['src2'];
            $loop[0][banner] = str_replace('--event--', "onmouseover='\$j(this).attr(\"src\",\"$sys_src2\")' onmouseout='\$j(this).attr(\"src\",\"$src\")'" . $sys_src2, $loop[0][banner]);
        }
        if ($src2) {
            $loop[0][banner] = str_replace('--event--', "onmouseover='\$j(this).attr(\"src\",\"$src2\")' onmouseout='\$j(this).attr(\"src\",\"$src\")'" . $src2, $loop[0][banner]);
        }
        if ($data[spc_desc] && $_map) {
            $r_sys_banner[$code]['map'] = "<map name='$code'>" . $data['spc_desc'] . "</map>";
        }
        if ($r_sys_banner[$code]['map']) {
            if (!is_file($src)) {
                $loop[0][banner] .= $r_sys_banner[$code]['map'];
            }
        }
    }	
	else 
	{
	    $data[img] = explode("||", $data[img]);
	    $data[img_on] = explode("||", $data[img_on]);
	
	    $_map = false;
	    if ($data[spc] == "map") {
	        $mapname = "bannermap_$code";
	        $addmap = "usemap='#$mapname'";
	        $data[img] = array_slice($data[img], 0, 1);
	        $_map = true;
	    }
	
	    $data[url] = explode("||", $data[url]);
	    $data[target] = explode("||", $data[target]);
	
	    if ($r_sys_banner[$code]) {
	        $data[img] = array_slice($data[img], 0, 1);
	        $data[spc] = "sys";
	        unset($mapname);
	        unset($addmap);
	    }
	
	    if ($data[spc] != "map" && $data[spc] != "sys")
	        $data[spc_desc] = explode("||", $data[spc_desc]);
	
	    //배너 경로가 저장된 경우.    20140623  chunter
	    if ($data[file_path])
	        $dir = $data[file_path];
	
	    foreach ($data[img] as $k => $v) {
	        unset($img);
	
	        //배너에 텍스트 출력 추가 . 이미지가 출력 우선순위가 높기때문에 text 를 먼저 불러온다.    20140626  chunter
	        if ($data[spc_desc][$k] && $data[spc] != "sys") {
	            //debug($data[spc_desc][$k]);
	            $img[spc_desc] .= $data[spc_desc][$k];
	            $img[banner] = "<div class='_banner' code='$code'>" . $data[spc_desc][$k] . "</div>";
	
	            if ($data[url][$k]) {
	                $data[url][$k] = str_replace("{mid}", $sess[mid], $data[url][$k]);
	                // {mid} 치환 whenji 20120926
	                $img[url] = $data[url][$k];
	                $img[target] = $data[target][$k];
	            }
	        }
	
	        if ($v && $data[spc] != "sys") {
	
	            $size = @getImageSize(dirname(__FILE__) . "../../../.." . $dir . $v);
	
	            if (!$size[2])
	                continue;
	            if (in_array($size[2], array(4, 13))) {
	                $img[banner] = "
					<embed src='$dir{$v}' quality='high' bgcolor='#FFFFFF' wmode='transparent' allowScriptAccess='always' width='$size[0]' height='$size[1]' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' class='_banner' code='$code'></embed>	
					";
	
	            } else {
	
	                //백그라운드 이미지 배너 처리.
	                if ($image_type == 'background') {
	                    $img[banner] = "width:" . $width . "px; height:" . $height . "px; background:url('$dir{$v}') no-repeat 0 0;\" class='_banner' code='$code'";
	                } else {
	                    if ($data[img_on][$k]) {
	                        $_event = "onmouseover='\$j(this).attr(\"src\",\"$dir{$data[img_on][$k]}\")' onmouseout='\$j(this).attr(\"src\",\"$dir{$v}\")'";
	                    } else {
	                        $_event = "";
	                    }
	                    if (!$data[slide_speed])
	                        $data[slide_speed] = 5;
	                    $data[slide_speed] = $data[slide_speed] * 1000;
	                    $img[banner] = "<img src='$dir{$v}' onerror='this.style.display=\"none\"' class='_banner' id='$code' code='$code' $addmap $_event slide_speed='" . $data[slide_speed] . "'/>";
	                    if ($data[url][$k]) {
	                        $data[url][$k] = str_replace("{mid}", $sess[mid], $data[url][$k]);
	                        // {mid} 치환 whenji 20120926
	                        //$img[banner] = "<a href='{$data[url][$k]}' target='{$data[target][$k]}'>$img[banner]</a>";
							
	    					//pod_group iphotobook 배너에서 자바스크립트를 사용하기 위한 임시코드 by kdk
	    					//스킨별 임시코드 같은건 없음.. 그냥 정식 기능으로 변경. 			20160608		chunter					    
		    				if (strpos($data[url][$k], "http://javascript:") !== false) {
		        			$src = str_replace("http://javascript:", "", $data[url][$k]);
									$img[banner] = "<a href=\"javascript:{$src}\">$img[banner]</a>";
		    				}
		    				else {
		        			$img[banner] = "<a href='{$data[url][$k]}' target='{$data[target][$k]}'>$img[banner]</a>";
		    				}
							
	                    }
	                }
	            }
	
	            if ($data[spc] == "map") {
	            	$img[banner] = "<div class='_banner_img_map'>" .$img[banner].
	                "
					<map name=\"$mapname\">
					$data[spc_desc]
					</map></div>
					";
	
	            }
	            //$loop[] = $img;
	        }
	
	        if ($img[banner])
	            $loop[] = $img;
	    }		
	}

    if (!$loop && $GLOBALS[ici_admin]) {
    	if ($language_locale == "")
        $loop[0][banner] = "<div class='_banner stxt' style='border:1px dotted #dedede;' code='$code'>"._("배너")."($code)"._("영역")."<br/>"._("최대너비")." : <b class='banner_width_span'></b></div>";
			else 
				$loop[0][banner] = "<div class='_banner stxt' style='border:1px dotted #dedede;' code='$code'>Banner($code) Area<br/>Max Width : <b class='banner_width_span'></b></div>";
			
    }
    
	//debug($loop);
    return $loop;
}
?>