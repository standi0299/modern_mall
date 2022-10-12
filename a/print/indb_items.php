<?
/*
 * @date : 20180515
 * @author : kdk
 * @brief : 인터프로 자동견적 관련 항목 이미지,도움말,설명 기능 처리.
 * @desc :
 */
?>
<?
include "../lib.php";
include "../../conf/conf.db.php";

$m_print = new M_print();

switch($_REQUEST[mode]) {

    # 항목 제한설정.
    case "option_items_valid" :
        //debug($_POST);

        $optionItemData = array();
        if (is_array($_POST[gram])) {
            foreach ($_POST[gram] as $key => $value) {
                $optionItemData[$key][gram] = $_POST[gram][$key]; 
                $optionItemData[$key][page] = $_POST[page][$key];
            }
        }
        
        //debug($optionItemData);
        $opt_desc = json_encode($optionItemData);
        
        //제약사항 정보.
        $optionData = $m_print -> getOptionInfoList($cid, "and opt_key = '$_POST[opt_key]' and opt_name = '$_POST[opt_name]'");
    
        if ($optionData) {
            //update.
            $m_print->setOptionInfoValidUpdate($_POST[opt_key], $_POST[opt_name], $opt_desc);
        }
        else {
            //insert.
            $m_print->setOptionInfoValidInsert($cid, $_POST[opt_key], $_POST[opt_name], $opt_desc);
        }
        
        //exit;
        break;    
    
    
    # 항목 이미지.
    case "option_items_img" :
        //debug($_POST);
        
        ### 이미지 폴더
        $uploaddir = "../../data/print/goods_items_img/$cid/";
        //debug($uploaddir);
        
        if (!is_dir($uploaddir))
            mkdir($uploaddir, 0707);
        else
            @chmod($uploaddir, 0707);

        if (!is_dir($uploaddir)) {
            msg(_("폴더 생성에 실패했습니다.") . "\\n " . _("관리자에게 문의주세요."), -1);
            exit ;
        }

        //debug($_POST[delimg]);
        //파일삭제.
        if ($_POST[delimg]) {
            foreach ($_POST[delimg] as $k => $v) {        
                //DB 조회.
                $data = $m_print->getOptionInfoImg($v);
                //debug($data);
                if($data) {
                    //삭제.
                    @unlink($uploaddir . $data[opt_img]);
                    
                    $m_print->setOptionInfoImgDelete($v);
                }    
            }
        }
        
        $optionItemData = array();
        foreach ($_POST as $ItemKey => $ItemValue) {
            //옵션명 설정
            if (strpos($ItemKey, "opt_") !== false) {
                $optionItemData[$ItemKey] = $ItemValue;
                /*foreach ($ItemValue as $k => $v) {
                    $item[$k] = $v;
                }*/        
            }
        }        
        //debug($optionItemData);
        
        //업로드 파일 갯수 제한(500개).
        if(count($optionItemData[item]) > 500) {
            //msg(_("옵션 이미지 파일은 500개 이하만 업로드 됩니다."), -1);
        }
        
        //파일 저장.
        $img = array();
        foreach ($_FILES[img][tmp_name] as $k => $v) {
            if (!is_uploaded_file($v)) continue;

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
            
            //$fsrc = 'opt_'. time() . '-'. $k . $ext;
            //$fsrc = $optionItemData[opt_key][$k].'_'. time() . '-'. $k . $ext;
            $fsrc = $optionItemData[opt_key][$k].$ext;
           
            if (!move_uploaded_file($v, $uploaddir . $fsrc)) continue;
            $img[$k] = $fsrc;
        }
        //debug($img);
                
        //파일 저장.
        if ($img) {
            foreach ($img as $k => $v) {
                //DB 조회.
                $data = $m_print->getOptionInfo($cid, $optionItemData[opt_key][$k], $optionItemData[opt_name][$k]);
                //debug($data);
                if($data) {
                    //수정.
                    $m_print->setOptionInfoImgUpdate($data[ID], $img[$k]);
                }            
                else {
                    //등록.
                    $m_print->setOptionInfoImgInsert($cid, $optionItemData[opt_key][$k], $optionItemData[opt_name][$k], $img[$k]);
                }
            }
        }
        
        //exit;
        break;
        
    # 항목 도움말.
    case "option_items_desc" :

        if ($_POST[contents])
            $_POST[opt_desc] = addslashes(base64_decode($_POST[contents]));

        if ($_POST[id]) {
            //수정.
            $m_print->setOptionInfoDescUpdate($_POST[id], $_POST[opt_desc]);
        }
        else {
            //등록.
            $m_print->setOptionInfoDescInsert($cid, $_POST[opt_key], $_POST[opt_name], $_POST[opt_desc]);
        }

        break;
    
    # 항목 설명.
    case "option_items_tip" :
        //debug($_POST);

        $optionItemData = array();
        foreach ($_POST[ID] as $ItemKey => $ItemValue) {
            //옵션명 설정
            $optionItemData[$ItemKey][ID] = $ItemValue;
            $optionItemData[$ItemKey][opt_name] = $_POST[opt_name][$ItemKey];
            $optionItemData[$ItemKey][opt_tip] = $_POST[opt_tip][$ItemKey];
        }        
        //debug($optionItemData);
        
        foreach ($optionItemData as $ItemKey => $ItemValue) {
            if ($ItemValue[ID]) {
                //수정.
                $m_print->setOptionInfoTipUpdate($ItemValue[ID], $ItemValue[opt_name], $ItemValue[opt_name], $ItemValue[opt_tip]);
            }
            else {
                //등록.
                $m_print->setOptionInfoTipInsert($cid, $ItemValue[opt_name], $ItemValue[opt_name], $ItemValue[opt_tip]);
            }
        }

        break;

    case "option_items_tip_del" :
        //debug($_REQUEST);

        if ($_REQUEST[id]) {
            //삭제.
            $m_print->setOptionInfoTipDelete($_REQUEST[id]);
        }
        //exit;
        break;

}

if (!$_POST[rurl])
    $_POST[rurl] = $_SERVER[HTTP_REFERER];
go($_POST[rurl]);
?>