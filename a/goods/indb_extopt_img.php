<?

/*
* @date : 20180117 (20180118)
* @author : kdk
* @brief : 견적 옵션 상품 제한설정 추가.
* @request : 
* @desc : $limit_flag = '0'
* @todo :
*/

include "../lib.php";
include "../../conf/conf.db.php";
$m_goods = new M_goods();
$m_extra_option = new M_extra_option();

### 이미지 폴더
$dirL = "../../data/tmp/l/";
$dirL_c = "/public_html/data/goods/$cid/l/$_POST[goodsno]/";

# FTP 접속
$ftp = ftp_connect($cfg_center[host]);
ftp_login($ftp, $db_user, $db_pass);

@ftp_mkdir($ftp, "/public_html/data/goods/$cid/");
@ftp_mkdir($ftp, "/public_html/data/goods/$cid/l/");
@ftp_mkdir($ftp, "/public_html/data/goods/$cid/l/$_POST[goodsno]/");
@ftp_chmod($ftp, 0707, "/public_html/data/goods/l/$_POST[goodsno]");

$item = array();
$img = array();

if ($_POST[mode] == "userOptionImg_delete") {
    //자동견적옵션 이미지 초기화 기능 추가 2017.10.17 by kdk.
    if (!$_POST[goodsno]) {
        echo "FAIL|"._("오류입니다.")."[param=goodsno]";
        exit ;
    }

    /* 견적상품 옵션/가격 복사 */
    $db -> start_transaction();
    try {
        //이미지 삭제.            
        $img_data = $m_extra_option->getOptImgList($cid, $cfg_center[center_cid], $_POST[goodsno]);
        //debug($img_data);
        
        //등록된 정보가 있을 경우 설정
        if ($img_data) {
            foreach ($img_data as $key => $val) {
                //삭제.
                @unlink($dirL . $val[option_img]);
            }
        }
        
        //자동견적 옵션 이미지 초기화(삭제)
        $m_extra_option->setAllDeleteOptImg($cid, $cfg_center[center_cid], $_POST[goodsno]);
        $db -> query("commit");
    } catch(Exception $e) {
        $db -> query("rollback");
    }

    echo "OK";
    exit;
}
else {
    foreach ($_POST as $ItemKey => $ItemValue) {
        //옵션명 설정
        if (strpos($ItemKey, "item") !== false) {
            $optionItemData[$ItemKey] = $ItemValue;
            
            foreach ($ItemValue as $k => $v) {
                $item[$k] = $v;
            }        
        }
    }
    
    //업로드 파일 갯수 제한(100개).
    if(count($optionItemData[item]) > 100) {
        msg(_("옵션 이미지 파일은 100개 이하만 업로드 됩니다."), -1);
    }
    
    //파일 임시 저장.
    foreach ($_FILES[img][tmp_name] as $k => $v) {
    
        if (!is_uploaded_file($v)) continue;
    
        if (!is_dir($dirL)) {
            mkdir($dirL, 0707);
        }
        $info = GetImageSize($v);
        switch($info[2]) {
            case "1" :
                $ext = ".gif";
                break;
            case "2" :
                $ext = ".jpg";
                break;
            case "3" :
                $ext = ".png";
                break;
        }
        
        if (!$ext) continue;
        
        $fsrc = 'opt_'. time() . '-'. $k . $ext;
    
        if (!move_uploaded_file($v, $dirL . $fsrc)) continue;
    
        $img[$k] = $fsrc;
    }

    //제한설정 초기화. $limit_flag = '0'
    $m_extra_option->setUpdateOptInitLimitFlag($cid, $cfg_center[center_cid], $_POST[goodsno]);
    if ($_POST[limitflag]) {
        foreach ($_POST[limitflag] as $k => $v) {
            $limit_flag = 1;
            //제한설정 업데이트.
            $m_extra_option->setUpdateOptLimitFlag($cid, $cfg_center[center_cid], $_POST[goodsno], $v, $limit_flag);
        }
    }    
    
    //파일삭제.
    if ($_POST[delimg]) {
        # FTP 디렉토리 변경
        @ftp_chdir($ftp, $dirL_c);
        
        foreach ($_POST[delimg] as $k => $v) {
            //DB 조회.
            $data = $m_extra_option->getOptImg($cid, $cfg_center[center_cid], $_POST[goodsno], $v);
            
            if($data) {
                //삭제.
                @ftp_delete($ftp, $data[option_img]);
                $m_extra_option->setDeleteOptImg($cid, $cfg_center[center_cid], $_POST[goodsno], $v);
            }    
            
        }
    }
    
    if ($img) {
        foreach ($img as $k => $v) {
            if($item[$k]) {
                # FTP 디렉토리 변경
                @ftp_chdir($ftp, $dirL_c);

                # FTP를 통한 이미지 수정
                @ftp_put($ftp, $v, $dirL . $v, FTP_BINARY);
            
                //DB 조회.
                $data = $m_extra_option->getOptImg($cid, $cfg_center[center_cid], $_POST[goodsno], $item[$k]);
                if($data) {
                    //수정.
                    $m_extra_option->setUpdateOptImg($cid, $cfg_center[center_cid], $_POST[goodsno], $item[$k], $v);
                }            
                else {
                    //등록.
                    $m_extra_option->setInsertOptImg($cid, $cfg_center[center_cid], $_POST[goodsno], $item[$k], $v);
                }
                
                //임시파일 삭제.    
                @unlink($dirL . $v);
            }
        } 
    }
    
    ftp_close($ftp);
    
    if (!$_POST[rurl])
        $_POST[rurl] = $_SERVER[HTTP_REFERER];
    
    go($_POST[rurl]);    
}
?>