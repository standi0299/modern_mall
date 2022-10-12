<?

/*
* @date : 20190124
* @author : kdk
* @brief : 시안 파일 업로드 기능 추가.
* @request : 태웅.
* @desc : SaveFileDesign()
* @todo : 
*/

/*
* @date : 20181106
* @author : kdk
* @brief : pod관련 관리자용 함수.
* @desc : 알래스카
*/

//주문 생성.exm_pay,exm_ord,exm_ord_item
function SetPayOrdItem($pay, $ord, $item) 
{
    global $db;
    
    $db -> start_transaction();
    try {
        ## 결제데이터 insert
        $query = "insert into exm_pay set
        cid               = '$pay[cid]',
        payno             = '$pay[payno]',
        mid               = '$pay[mid]',
        paystep           = '$pay[step]',
        paymethod         = '$pay[paymethod]',
        payprice          = '$pay[payprice]',
        saleprice         = '$pay[saleprice]',
        shipprice         = '$pay[shipprice]',
        orddt             = now(),
        paydt             = now(),
        orderer_name      = '$pay[orderer_name]',
        payer_name        = '$pay[payer_name]',
        orderer_phone     = '$pay[orderer_phone]',
        orderer_mobile    = '$pay[orderer_mobile]',
        orderer_email     = '$pay[orderer_email]',
        receiver_name     = '$pay[receiver_name]',
        receiver_phone    = '$pay[receiver_phone]',
        receiver_mobile   = '$pay[receiver_mobile]',
        receiver_zipcode  = '$pay[receiver_zipcode]',
        receiver_addr     = '$pay[receiver_addr]',
        receiver_addr_sub = '$pay[receiver_addr_sub]',
        request2          = '$pay[request2]'
        ";
        //debug($query);//exit;
        
        //request           = '$_POST[request]',//배송시메모
        //return_msg        = '$_POST[return_msg]',//반려사유
        //memo              = '$_POST[memo]'//관리자메모
        $db->query($query);        

        ## 주문데이터 insert
        $query = "insert into exm_ord set
         payno          = '$ord[payno]',
         ordno          = '$ord[ordno]',
         rid            = '$ord[rid]',
         ordprice       = '$ord[ordprice]',         
         shipprice      = '$ord[shipprice]',
         order_shiptype = '$ord[order_shiptype]'
        ";
        //debug($query);//exit;
        $db->query($query);

        $query = "insert into exm_ord_item set
            payno                = '$item[payno]',
            ordno                = '$item[ordno]',
            ordseq               = '$item[ordseq]',
            goodsno              = '$item[goodsno]',
            goodsnm              = '$item[goodsnm]',
            saleprice            = '$item[saleprice]',
            payprice             = '$item[payprice]',
            ea                   = '$item[ea]',
            storageid            = '$item[storageid]',
            itemstep             = '$item[itemstep]',
            item_rid             = '$item[item_rid]',
            order_inspection     = '$item[order_inspection]',
            selfgoods            = '$item[selfgoods]',
            est_order_option_desc= '$item[est_order_option_desc]'
         ";
        //debug($query);//exit;
        $db->query($query);

        $debug_data .= debug_time($this_time);

        if ($pay[step] == 2) {
            $query = "select * from exm_ord_item where payno = '$pay[payno]'";
            $res = $db -> query($query);
            while ($data = $db -> fetch($res)) {
                //set_pod_pay($data[payno], $data[ordno], $data[ordseq]);
                //set_acc_desc($data[payno], $data[ordno], $data[ordseq], 2);
            }
            $debug_data .= debug_time($this_time);
            //order_sms($_POST[payno]);
        }

        $debug_data .= debug_time($this_time);

        $db -> query("commit");
    } catch(Exception $e) {
        $db -> query("rollback");
        return false;
    }
    $db -> end_transaction();

    return true;
}

//주문내역 파일처리.
function SaveFile($file, $storageid, $payno, $mid)
{
    global $db, $cfg_center, $cid;
    
    //debug($cfg_center[center_cid]);
    //debug($cid);
    //debug($storageid);
    //debug($file);

    if ($file[file][name]) {
        $result['upload_file_name'] = $file[file]['name'];

        $tmp_name = $file[file]['tmp_name'];
        $filename = $file[file]['name'];
        $filetype = $file[file]['type'];

        $data = '@'.$tmp_name. ';filename='.$filename.';type='.$filetype;
        $post = array('file' => $data,'storage_code' => $storageid,'center_id' => $cfg_center[center_cid],'mall_id' => $cid);

        $timeout = 30;
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'http://files.ilark.co.kr/portal_upload/estm/file/jqupload.aspx'); 
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

        $ret = curl_exec($curl); 

        //$ret = "{\"files\":[{\"name\":\"thumb-1f6fe105079c4de5848b1a6766ce7f07_1511495778_67_840x840.jpg\",\"size\":\"136369\", \"type\":\"\",\"url\":\"http://files.ilark.co.kr/es_storage/mcdev/mmdev/201807/20180704/20180704-351063/thumb-1f6fe105079c4de5848b1a6766ce7f07_1511495778_67_840x840.jpg\",\"thumbnailUrl\":\"\",\"deleteUrl\":\"http://files.ilark.co.kr/portal_upload/estm/file/jqdelete.aspx?storage_code=20180704-351063&center_id=mcdev&mall_id=mmdev&file_name=thumb-1f6fe105079c4de5848b1a6766ce7f07_1511495778_67_840x840.jpg\",\"deleteType\":\"GET\"}]}";
        
        //debug($ret);
        if ($ret) {
            $ret_data = json_decode($ret,1);
            //debug($ret_data[files][0]);

            $result['server_file_name'] = $ret_data[files][0]['name'];
            $result['file_size'] = $ret_data[files][0]['size'];
            $result['server_path'] = $ret_data[files][0]['url'];
        }

        curl_close ($curl);
    }
    
    //$result['server_file_name'] = "thumb-1f6fe105079c4de5848b1a6766ce7f07_1511495778_67_840x840.jpg";
    //$result['file_size'] = "1024";
    //$result['server_path'] = "http://files.ilark.co.kr/es_storage/mcdev/mmdev/201807/20180704/20180704-351063/thumb-1f6fe105079c4de5848b1a6766ce7f07_1511495778_67_840x840.jpg";
        
    //debug($result);
    
    //DB 저장.
    if ($result) {
        $m_print = new M_print();
        $m_pod = new M_pod();
        
        ### md_print_upload_file DB에 파일 정보 저장. 
        $fdata = $m_print->getPrintUploadFile($storageid);
        //debug($fdata);
        
        if($fdata) {
            $m_print->updatePrintUploadFile($storageid, $result[server_file_name], $result[file_size], $result[server_path], "", "", "");
        }
        else {
            $m_print->insertPrintUploadFile($storageid, $result[server_file_name], $result[file_size], $result[server_path], "", "", "");
        }
                        
        ### exm_ord_item DB에  est_order_type='', est_file_down_full_path='' 저장.
        $m_pod->setOrdItemFileUpdate($payno, $storageid, $result[server_path]);
        
        //파일 수정.order_files
        $m_pod->setPodPayFileUpdate($cid, $mid, $payno, $result[server_path], $storageid);
    }    

    return $result;
}

//결제번호 생성하기.
function CreatePayno() 
{
    $payno;

    $micro = explode(" ",microtime());
    $payno = $micro[1].sprintf("%03d",floor($micro[0]*1000));
    //debug($payno);

    return $payno;
}

//결제번호 확인하기.
function CheckPayno($payno)
{
    $result = true;

    $m_pod = new M_pod();
    
    list($chk) = $m_pod->checkPayno($payno);
    if ($chk) {
       $result = false;
    }
                
    return $result;
}

//보관함코드 생성하기
function CreateStorageId($bTemp = true)
{
    if ($bTemp) return "_temp_".date('Ymd')."_".time();
    else return date('Ymd')."_".time();
}

//시안 파일처리.
function SaveFileDesign($file, $storageid, $payno, $mid)
{
    global $db, $cfg_center, $cid;
    
    //debug($cfg_center[center_cid]);
    //debug($cid);
    //debug($storageid);
    //debug($payno);
    //debug($mid);
    //debug($file);

    if ($file[name]) {
        $result['upload_file_name'] = $file['name'];

        $tmp_name = $file['tmp_name'];
        $filename = $file['name'];
        $filetype = $file['type'];

        $data = '@'.$tmp_name. ';filename='.$filename.';type='.$filetype;
        $post = array('file' => $data,'storage_code' => $storageid,'center_id' => $cfg_center[center_cid],'mall_id' => $cid);

        
        $timeout = 30;
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'http://files.ilark.co.kr/portal_upload/estm/file/jqupload.aspx'); 
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

        $ret = curl_exec($curl);

        //$ret = @"{\"files\":[{\"name\":\"결과파일_1.jpg\",\"size\":\"262181\", \"type\":\"\",\"url\":\"http://files.ilark.co.kr/es_storage/mprione/lotteria/201901/20190123/20190123-170911/결과파일_1.jpg\",\"thumbnailUrl\":\"\",\"deleteUrl\":\"http://files.ilark.co.kr/portal_upload/estm/file/jqdelete.aspx?storage_code=20190123-170911&center_id=mprione&mall_id=lotteria&file_name=결과파일_1.jpg\",\"deleteType\":\"GET\"}]}";
        
        //debug($ret);
        if ($ret) {
            $ret_data = json_decode($ret,1);
            //debug($ret_data[files][0]);

            $result['server_file_name'] = $ret_data[files][0]['name'];
            $result['file_size'] = $ret_data[files][0]['size'];
            $result['server_path'] = $ret_data[files][0]['url'];
        }

        curl_close ($curl);
        
    }
    
    /*
    $result['server_file_name'] = "결과파일_1.jpg";
    $result['file_size'] = "262181";
    $result['server_path'] = "http://files.ilark.co.kr/es_storage/mprione/lotteria/201901/20190123/20190123-170911/결과파일_1.jpg";
    */
        
    //debug($result);
    
    //DB 저장.
    if ($result) {
        $m_print = new M_print();
        ### md_print_upload_file DB에 파일 정보 저장. 
        //$fdata = $m_print->getPrintUploadFileWithID($storageid,$id);
        //debug($fdata);
        //exit;
        //if($fdata) {
        //    $m_print->updatePrintUploadFile($storageid, $result[server_file_name], $result[file_size], $result[server_path], "", "", "");
        //}
        //else {
            $m_print->insertPrintUploadFile($storageid, $result[server_file_name], $result[file_size], $result[server_path], "", "", "");
        //}
    }    

    return $result;
}

?>