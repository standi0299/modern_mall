<?

include "../lib/library.php";

$m_print = new M_print();

switch ($_REQUEST[mode]) 
{
    ### 견적 정보 사용자 업로드 파일 삭제.
    case "ajax_file_delete":

        try 
        {
            $soapurl = "http://files.ilark.co.kr/portal_upload/estm/file/jqdelete.aspx?";
            //debug($soapurl);
            $param = "storage_code=". $_REQUEST['storage_code'] ."&center_id=". $cfg_center[center_cid] ."&mall_id=". $cid ."&file_name=". $_REQUEST['file_name'];
            //debug($soapurl.$param);
     
            $ret = readUrlWithcurl($soapurl.$param, false);
            //debug($ret);
            
            if ($ret) {
                if (strpos($ret, "true") !== false) {
                    echo("OK");
                }
                else {
                    echo("FAIL");
                }
            }
            else {
                echo("FAIL");
            }
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
    
    ### 견적 정보 사용자 업로드 파일 정보 저장 / 20180702 / kdk
    case "ajax_file_json":
    
        //debug($_POST[mode]);
        //debug($_POST[storageid]);
        //debug($_POST[upload_order_product_check]);
        //debug($_POST[goodsnm]);
        //debug($_POST[file_json]);
        
        //debug($cid);

        $mid = ($GLOBALS[sess][mid]) ? $GLOBALS[sess][mid] : $_COOKIE[cartkey];
        //debug($mid);
        
        try 
        {
            $m_print->setOrdUpload($_POST[storageid], $cid, $mid, 'C', $product_name);

            $filesData = json_decode($_POST[file_json],1);
            //debug($filesData);
            foreach ($filesData as $key => $val) {
                $m_print->setOrdUploadFile($_POST[storageid], $val['file_name'], $val['file_size'], $val['server_path']);
            }
            
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