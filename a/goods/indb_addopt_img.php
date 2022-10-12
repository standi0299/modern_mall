<?

include "../lib.php";
include "../../conf/conf.db.php";
$m_goods = new M_goods();

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

//파일삭제.
if ($_POST[delimg]) {
    # FTP 디렉토리 변경
    @ftp_chdir($ftp, $dirL_c);
    
    foreach ($_POST[delimg] as $k => $v) {        
        //DB 조회.
        $data = $m_goods->getOptionImg($cid, $_POST[goodsno], $v);

        if($data) {
            //삭제.
            @ftp_delete($ftp, $data[option_img]);
            $m_goods->setOptionImgDelete($cid, $_POST[goodsno], $v);
        }    
    }
}

//파일 저장.
if ($img) {
    foreach ($img as $k => $v) {
        if($item[$k]) {
            # FTP 디렉토리 변경
            @ftp_chdir($ftp, $dirL_c);

            # FTP를 통한 이미지 수정
            @ftp_put($ftp, $v, $dirL . $v, FTP_BINARY);
            
            //DB 조회.
            $data = $m_goods->getOptionImg($cid, $_POST[goodsno], $item[$k]);
            
            if($data) {
                //수정.
                $m_goods->setOptionImgUpdate($cid, $_POST[goodsno], $item[$k], $v);
            }            
            else {
                //등록.
                $m_goods->setOptionImgInsert($cid, $_POST[goodsno], $item[$k], $v);
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

?>