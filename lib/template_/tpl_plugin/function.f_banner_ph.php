<?
function f_banner_ph($code, $text_cnt = 1, $banner_type = '', $add_type = 'Y', $image_type = 'Y') {
    global $db, $cfg, $cid, $sess;

    $dir = "/skin/$cfg[skin]/img/_banner/$code/";

    //header.php 에서 조회해혼 공통 배너정보가 존재하는 체크한다.    20140403    chunter
    if (is_array($r_user_banner[$code]))
        $data = $r_user_banner[$code];
    else {
        $query = "select * from exm_banner where cid = '$cid' and skin = '$cfg[skin]' and code = '$code'";
        $data = $db -> fetch($query);
    }

    if ($data[spc_desc]) {
        $data[spc_desc] = explode("||", $data[spc_desc]);
        $foreach_data = $data[spc_desc];
    }

    if ($data[img]) {
        $data[img] = explode("||", $data[img]);
        $foreach_data = $data[img];
    }

    $data[url] = explode("||", $data[url]);
    $data[target] = explode("||", $data[target]);

    //배너 경로가 저장된 경우.    20140623  chunter
    if ($data[file_path])
        $dir = $data[file_path];

    if (is_array($foreach_data)) {
        foreach ($foreach_data as $k => $v) {

            unset($img);

            $textIndex = $k * $text_cnt;
            if ($data[spc_desc][$textIndex]) {
                //debug($data[spc_desc][$k]);
                $img[logo_desc] = $data[spc_desc][$textIndex];
                $img[spc_desc] = str_replace("\r\n", "<BR />", $data[spc_desc][$textIndex]);
                $img[spc_desc2] = str_replace("\r\n", "<BR />", $data[spc_desc][$textIndex + 1]);
            }

            if ($data[url][$k]) {
                $data[url][$k] = str_replace("{mid}", $sess[mid], $data[url][$k]);
                // {mid} 치환 whenji 20120926
                $img[url] = $data[url][$k];
                $img[target] = $data[target][$k];
            }

            if ($data[img][$k]) {
                $img[img] = $dir . $data[img][$k];
            }

            if ($img)
                $loop[] = $img;
        }
    }

    if (!$loop && $GLOBALS[ici_admin]) {
        $loop[0][spc_desc] = "<div class='_banner$banner_type' add_type='$add_type' image_type='$image_type' text_cnt='$text_cnt' add style='border:1px dotted #dedede;' code='$code'>"._("배너")."($code)"._("영역")."<br/>"._("최대너비")." : <b class='banner_width_span'></b></div>";
        $loop[0][logo_desc] = "";
    }
    //debug($loop);
    return $loop;
}
?>