<?

include "../lib/library.php";

$m_print = new M_print();

switch ($_REQUEST[mode]) 
{
    ### 미오디오용 필름 스캔이미지 업로드 파일 정보 저장 / 20180829 / kdk
    case "miodio_ajax_file_json":
    
        //debug($_POST[mode]);
        //debug($_POST[mid]);
        //debug($_POST[storageid]);
        //debug($_POST[file_json]);
        
        try 
        {
            $_POST[file_json] = str_replace('\"', '"', $_POST[file_json]);
            $filesData = json_decode($_POST[file_json],1);
//, $val['file_width'], $val['file_height'], $val['thumb_path'], $val['thumb_size'], $val['thumb_width'], $val['thumb_height']

            foreach ($filesData as $key => $val) {
                //exif 정보.    
                if($val['exif_orientation']) {
                    $exif_info = json_encode(array('orientation' => $val['exif_orientation']));
                }                
                
                //이미지 정보.
                $data = $m_print->getEtcUploadFile($cid, $_POST[mid], $_POST[storageid], $val['file_name'], $val['file_size'], $val['server_path']);

                if ($data) {
                    //update.
                    $m_print->setEtcUploadFileUpdate($cid, $_POST[mid], $_POST[storageid], $val['file_name'], $val['file_size'], $val['server_path'], $val['file_width'], $val['file_height'], $val['thumb_path'], $val['thumb_size'], $val['thumb_width'], $val['thumb_height'], $exif_info);
                }
                else {
                    //insert.
                    $m_print->setEtcUploadFileInsert($cid, $_POST[mid], $_POST[storageid], $val['file_name'], $val['file_size'], $val['server_path'], $val['file_width'], $val['file_height'], $val['thumb_path'], $val['thumb_size'], $val['thumb_width'], $val['thumb_height'], $exif_info);
                }
            }
            
            //썸네일 파일 생성하기.
            /*$url = "http://storage.wecard.co.kr/zipfile_down/thumbnailImage.ashx";
            $returl = $url."?center_id=$cfg_center[center_cid]&mall_id=$cid&mid=$_POST[mid]&storage_code=$_POST[storageid]";

            $ret = sendQueryData($returl);

            if ($ret=="FAIL"){
                echo("FAIL");
                exit;
                break;
            }*/
            
            echo("OK");
            exit;
            break;
        } 
        catch(Exception $e) 
        {
            echo("FAIL");
            exit;
            break;
        }
        
        exit;break;
}
?>