<?

function f_banner_main_cnt($code, $image_type = '', $width = 0, $height = 0) {
    global $db, $cfg, $cid, $sess, $r_sys_banner, $r_user_banner;
    
    $resultCnt = 0;

    $dir = "/skin/$cfg[skin]/img/_banner/$code/";
    
    //header.php 에서 조회해혼 공통 배너정보가 존재하는 체크한다.    20140403    chunter
    if (is_array($r_user_banner[$code]))
        $data = $r_user_banner[$code];
    else {
        $query = "select * from exm_banner where cid = '$cid' and skin = '$cfg[skin]' and code = '$code'";
        $data = $db -> fetch($query);
    }

    $data[img] = explode("||", $data[img]);
    $data[img_on] = explode("||", $data[img_on]);

    $data[url] = explode("||", $data[url]);
    $data[target] = explode("||", $data[target]);
    $data[spc_desc] = explode("||", $data[spc_desc]);

    //배너 경로가 저장된 경우.    20140623  chunter
    if ($data[file_path])
        $dir = $data[file_path];

    foreach ($data[img] as $k => $v) {
        $resultCnt++;
    }

    return $resultCnt;

}
?>