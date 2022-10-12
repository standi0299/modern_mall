<?php

/*
 * @date : 20180629
 * @author : kdk
 * @brief : 자동견적 업로드 파일 관련 추가. (exm_ord_upload,exm_ord_upload_file) 테이블사용.
 * @desc : (파일업로드주문,다이렉트주문 파일업로드)에서 사용.
 */

/*
 * @date : 20180405
 * @author : kdk
 * @brief : 자동견적 관련 model.
 * @request :
 * @desc : 인터프로(ipro)
 * @todo :
 */

class M_print {
    var $db;
    var $this;
    function M_print() {
        $this -> db = $GLOBALS[db];
    }

    //사이즈 리스트.
    function getOptionSizeList($addWhere = '') {
        $sql = "select * from md_print_option_items 
            where opt_prefix in ('A','B','C') 
                and opt_group = 'SIZE' 
                and opt_use = 'Y' $addWhere
            order by opt_order;";
        //echo $sql;
        return $this -> db -> listArray($sql);
    }    
    
    function getOptionItemsList($addWhere = "", $orderby = "") {
        if (!$orderby) $orderby = " order by opt_order";
        $sql = "select * from md_print_option_items where opt_use = 'Y' $addWhere $orderby";
        //echo $sql;
        //debug($sql);
        return $this -> db -> listArray($sql);
    }

    function getPrintOptionPrice($cid, $price_type) {
        $sql = "select * from md_print_option_price where cid='$cid' and price_type='$price_type'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }

    function setPrintOptionPrice($cid, $price_type, $price_data) {
        //ipro_size_price
        $sql = "insert into md_print_option_price set
            cid = '$cid',
            price_type = '$price_type',
            price_data = '$price_data',
            regist_date = now()
        on duplicate key update
            cid = '$cid',
            price_data = '$price_data',
            update_date = now()
           ";
        //debug($sql);
        $this -> db -> query($sql);
    }

    ###옵션 info
    function setOptionInfoInsert($cid, $opt_key, $opt_name, $opt_desc = '', $opt_img = '', $opt_tip = '') {
        $val = "";
        if ($opt_desc) $val .= "opt_desc = '$opt_desc',";
        if ($opt_img) $val .= "opt_img = '$opt_img',";
        if ($opt_tip) $val .= "opt_tip = '$opt_tip',";

        $sql = "insert into md_print_option_info set
            cid = '$cid',
            opt_key = '$opt_key',
            opt_name = '$opt_name',
            $val
            opt_use = 'Y',
            regist_date = now()
           ";
        //debug($sql);
        $this -> db -> query($sql);
    }    

    function setOptionInfoUpdate($id, $opt_name, $opt_desc = '', $opt_img = '', $opt_tip = '') {
        $val = "";
        if ($opt_desc) $val .= "opt_desc = '$opt_desc',";
        if ($opt_img) $val .= "opt_img = '$opt_img',";
        if ($opt_tip) $val .= "opt_tip = '$opt_tip',";
        
        $sql = "update md_print_option_info set
            opt_name = '$opt_name',
            $val
            update_date = now()
        where ID = '$id'";
        //debug($sql);
        $this -> db -> query($sql);
    }    
    
    function getOptionInfoList($cid, $addWhere = "", $orderby = "") {
        if (!$orderby) $orderby = " order by ID";
        $sql = "select * from md_print_option_info where cid='$cid' and opt_use = 'Y' $addWhere $orderby";
        //echo $sql;
        //debug($sql);
        return $this -> db -> listArray($sql);
    }

    function getOptionInfo($cid, $opt_key = "", $opt_name = "") {
        $addWhere = "";
        if ($opt_key) $addWhere .= " and opt_key = '$opt_key'";
        if ($opt_name) $addWhere .= " and opt_name = '$opt_name'";
        
        $sql = "select * from md_print_option_info where cid='$cid' and opt_use = 'Y' $addWhere ";
        //echo $sql;
        //debug($sql);
        return $this -> db -> fetch($sql);
    }

    function setOptionInfoValidInsert($cid, $opt_key, $opt_name, $opt_desc) {
        $sql = "insert into md_print_option_info set
            cid = '$cid',
            opt_key = '$opt_key',
            opt_name = '$opt_name',
            opt_desc = '$opt_desc',
            opt_use = 'Y',
            regist_date = now()
           ";
        //debug($sql);
        $this -> db -> query($sql);
    }    

    function setOptionInfoValidUpdate($opt_key, $opt_name, $opt_desc) {
        $sql = "update md_print_option_info set
            opt_desc = '$opt_desc',
            update_date = now()
        where opt_key = '$opt_key' and opt_name = '$opt_name'";
        //debug($sql);
        $this -> db -> query($sql);
    }
    
    function setOptionInfoDelete($id) {
        $sql = "update md_print_option_info set opt_use = 'N', update_date = now() where ID='$id'";
        //debug($sql);
        $this -> db -> query($sql);
    }

    #항목이미지
    function getOptionInfoImg($id) {
        $sql = "select * from md_print_option_info where ID='$id'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }
        
    function setOptionInfoImgInsert($cid, $opt_key, $opt_name, $opt_img) {
        $sql = "insert into md_print_option_info set
            cid = '$cid',
            opt_key = '$opt_key',
            opt_name = '$opt_name',
            opt_img = '$opt_img',
            opt_use = 'Y',
            regist_date = now()
           ";
        //debug($sql);
        $this -> db -> query($sql);
    }    

    function setOptionInfoImgUpdate($id, $opt_img) {
        $sql = "update md_print_option_info set
            opt_img = '$opt_img',
            update_date = now()
        where ID = '$id'";
        //debug($sql);
        $this -> db -> query($sql);
    }
        
    function setOptionInfoImgDelete($id) {
        $sql = "update md_print_option_info set opt_img = '', update_date = now() where ID='$id'";
        //debug($sql);
        $this -> db -> query($sql);
    }

    #항목도움말
    function getOptionInfoDesc($id) {
        $sql = "select * from md_print_option_info where ID='$id'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }
        
    function setOptionInfoDescInsert($cid, $opt_key, $opt_name, $opt_desc) {
        $sql = "insert into md_print_option_info set
            cid = '$cid',
            opt_key = '$opt_key',
            opt_name = '$opt_name',
            opt_desc = '$opt_desc',
            opt_use = 'Y',
            regist_date = now()
           ";
        //debug($sql);
        $this -> db -> query($sql);
    }    

    function setOptionInfoDescUpdate($id, $opt_desc) {
        $sql = "update md_print_option_info set
            opt_desc = '$opt_desc',
            update_date = now()
        where ID = '$id'";
        //debug($sql);
        $this -> db -> query($sql);
    }
        
    function setOptionInfoDescDelete($id) {
        $sql = "update md_print_option_info set opt_desc = '', update_date = now() where ID='$id'";
        //debug($sql);
        $this -> db -> query($sql);
    }

    #항목설명팁
    function getOptionInfoTip($id) {
        $sql = "select * from md_print_option_info where ID='$id'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }
        
    function setOptionInfoTipInsert($cid, $opt_key, $opt_name, $opt_tip) {
        $sql = "insert into md_print_option_info set
            cid = '$cid',
            opt_key = '$opt_key',
            opt_name = '$opt_name',
            opt_tip = '$opt_tip',
            opt_use = 'Y',
            regist_date = now()
           ";
        //debug($sql);
        $this -> db -> query($sql);
    }    

    function setOptionInfoTipUpdate($id, $opt_key, $opt_name, $opt_tip) {
        $sql = "update md_print_option_info set
            opt_key = '$opt_key',
            opt_name = '$opt_name',
            opt_tip = '$opt_tip',
            update_date = now()
        where ID = '$id'";
        //debug($sql);
        $this -> db -> query($sql);
    }
        
    function setOptionInfoTipDelete($id) {
        //$sql = "update md_print_option_info set opt_tip = '', update_date = now() where ID='$id'";
        $sql = "delete from md_print_option_info where ID='$id'";
        //debug($sql);
        $this -> db -> query($sql);
    }    
        
    //일반 인쇄 설정 가격으로 처리할 상품조회.(DG01 디지털 일반-명함,DG02 디지털 일반-스티커)
    function getNormalGoodsList($cid) {
        $sql = "select * from exm_goods where privatecid='$cid' and extra_option in ('DG01','DG02')";
        //echo $sql;
        //debug($sql);
        return $this -> db -> listArray($sql);
    }    

    //관리자 출력 파일 관련.
    function setPrintUploadFile($storageid, $upload_file_name, $file_size, $server_path, $file_type, $machine_id, $uniquekey)
    {
        $sql = "insert into md_print_upload_file set
            storageid   = '$storageid',
            upload_file_name            = '$upload_file_name',
            server_file_name            = '$upload_file_name',
            file_size                   = '$file_size',
            server_path                 = '$server_path',
            file_type                   = '$file_type',
            machine_id                  = '$machine_id',
            uniquekey                   = '$uniquekey',
            regist_flag                 = 'Y',
            regist_date                 = now()
        ";

        $this -> db -> query($sql);
    }

    function insertPrintUploadFile($storageid, $upload_file_name, $file_size, $server_path, $file_type, $machine_id, $uniquekey)
    {
        $sql = "insert into md_print_upload_file set
            storageid   = '$storageid',
            upload_file_name            = '$upload_file_name',
            server_file_name            = '$upload_file_name',
            file_size                   = '$file_size',
            server_path                 = '$server_path',
            file_type                   = '$file_type',
            machine_id                  = '$machine_id',
            uniquekey                   = '$uniquekey',
            regist_flag                 = 'Y',
            regist_date                 = now()
        ";

        $this -> db -> query($sql);
    }
        
    function updatePrintUploadFile($storageid, $upload_file_name, $file_size, $server_path, $file_type, $machine_id, $uniquekey)
    {
        $sql = "update md_print_upload_file set
            upload_file_name            = '$upload_file_name',
            server_file_name            = '$upload_file_name',
            file_size                   = '$file_size',
            server_path                 = '$server_path',            
            machine_id                  = '$machine_id',
            uniquekey                   = '$uniquekey',
            update_date                 = now()
            where storageid = '$storageid' 
            and file_type = '$file_type'
        ";

        $this -> db -> query($sql);
    }    

    function getPrintUploadFileWithFileType($storageid, $file_type)
    {
        $sql = "select * from md_print_upload_file where storageid = '$storageid' and file_type = '$file_type'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }    
    
    function getPrintUploadFile($storageid)
    {
        $sql = "select * from md_print_upload_file where storageid = '$storageid' order by id";
        //echo $sql;
        return $this -> db -> listArray($sql);
    }

    function getPrintUploadFileWithID($storageid, $id)
    {
        $sql = "select * from md_print_upload_file where storageid = '$storageid' and id = '$id'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }    
    
    function deletePrintUploadFile($id)
    {
        $sql = "delete from md_print_upload_file where id='$id'";
        $this -> db -> query($sql);
    }
    
    //사용자 파일 관련 (파일업로드주문,다이렉트주문 파일업로드).
    function setOrdUpload($upload_order_code, $cid, $mid, $upload_order_status, $product_name)
    {
        $sql = "
        insert into exm_ord_upload set
            upload_order_code               = '$upload_order_code',
            cid                             = '$cid',
            mid                             = '$mid',
            upload_order_status             = '$upload_order_status',
            product_name                    = '$product_name'
        on duplicate key update
            cid                             = '$cid',
            mid                             = '$mid',
            upload_order_status             = '$upload_order_status',
            product_name                    = '$product_name'
        ";
        
        $this -> db -> query($sql);
    }
    
    function setOrdUploadFile($upload_order_code, $upload_file_name, $file_size, $server_path)
    {
        $sql = "insert into exm_ord_upload_file set
            upload_order_product_code   = '$upload_order_code',
            upload_file_name            = '$upload_file_name',
            server_file_name            = '$upload_file_name',
            file_size                   = '$file_size',
            server_path                 = '$server_path',
            regist_flag                 = 'Y',
            regist_date                 = now()";
        $this -> db -> query($sql);
        
        if(mysql_affected_rows() == 0) { $result = 0; } 
        else { $result = 1; }
    }

    function setUpdateOrdUploadFile($id, $upload_file_name, $file_size, $server_path)
    {
        $sql = "update exm_ord_upload_file set
            upload_file_name            = '$upload_file_name',
            server_file_name            = '$upload_file_name',
            file_size                   = '$file_size',
            server_path                 = '$server_path'
        where id = '$id'
        ";
        $this -> db -> query($sql);
        
        if(mysql_affected_rows() == 0) { $result = 0; } 
        else { $result = 1; }
    }    
    
    function getOrdUpload($upload_order_code)
    {
        $sql = "select * from exm_ord_upload_file where upload_order_code = '$upload_order_code'";
        //echo $sql;
        return $this -> db -> fetch($sql);
    }
    
    function getOrdUploadFile($upload_order_code)
    {
        $sql = "select * from exm_ord_upload_file where upload_order_product_code = '$upload_order_code'";
        //echo $sql;
        return $this -> db -> listArray($sql);
    }    


    //오아시스 라우터  관련.
    function setOasisRouterInsert($cid, $router_user_id, $router_user_name, $machine_id, $router_info, $send_type)
    {
        $sql = "insert into md_oasis_router set
            cid             = '$cid',
            router_user_id  = '$router_user_id',
            router_user_name= '$router_user_name',
            machine_id      = '$machine_id',
            router_info     = '$router_info',
            send_type       = '$send_type',
            regist_flag     = 'Y',
            regist_date     = now()
        ";

        $this -> db -> query($sql);
    }

    function setOasisRouterUpdate($cid, $router_user_id, $router_user_name, $machine_id, $router_info, $send_type)
    {
        $sql = "update md_oasis_router set
            router_user_name= '$router_user_name',
            router_info     = '$router_info',
            send_type       = '$send_type',
            regist_date     = now()
        where cid = '$cid'
            and router_user_id  = '$router_user_id'
            and machine_id      = '$machine_id'
        ";

        $this -> db -> query($sql);
    }

    function setOasisRouterDelete($cid, $router_user_id, $machine_id)
    {
        $sql = "delete from md_oasis_router where cid='$cid' and router_user_id='$router_user_id' and machine_id='$machine_id'";

        $this -> db -> query($sql);
    }    
    
    function getOasisRouter($cid)
    {
        $sql = "select * from md_oasis_router where cid = '$cid'";
        //echo $sql;
        return $this -> db -> listArray($sql);
    }
    
    //-exm_ord_item.proc_admin_id,exm_ord_item.oasis_router_print_direction.
    function setOrdItemEtcInfoWithPrint($cartno, $proc_admin_id, $oasis_router_print_direction) {
        $sql = "update exm_ord_item set proc_admin_id='$proc_admin_id',oasis_router_print_direction='$oasis_router_print_direction' where cartno = '$cartno'";
        //debug($sql);
        $this -> db -> query($sql);
    }

    //-exm_pay.request2.payno
    function setPayRequest2WithPrint($payno, $request2) {
        $sql = "update exm_pay set request2='$request2' where payno = '$payno'";
        //debug($sql);
        $this -> db -> query($sql);
    }

    
    ### 미오디오용 필름 스캔이미지 업로드 파일 정보 저장 / 20180829 / kdk
    function getEtcUploadFileList($cid, $mid)
    {
        $sql = "select *,date_format(regist_date,'%Y-%m-%d') as regist_dt from md_etc_upload_file 
            where cid = '$cid'
            and mid = '$mid'
            and regist_flag = 'Y' order by date_format(regist_date,'%Y-%m-%d') desc";
        return $this -> db -> listArray($sql);
    }
        
    function getEtcUploadFile($cid, $mid, $storageid, $upload_file_name, $file_size, $server_path)
    {
        $sql = "select * from md_etc_upload_file 
            where cid = '$cid'
            and mid = '$mid'
            and storageid = '$storageid'
            and upload_file_name = '$upload_file_name'
            and file_size = '$file_size'
            and server_path = '$server_path'
            and regist_flag = 'Y'";
        return $this -> db -> fetch($sql);
    }
        
    function setEtcUploadFileInsert($cid, $mid, $storageid, $upload_file_name, $file_size, $server_path, $file_width, $file_height, $thumb_path, $thumb_size, $thumb_width, $thumb_height, $exif_info)
    {
        $sql = "insert into md_etc_upload_file set
            cid = '$cid',
            mid = '$mid',
            storageid = '$storageid',
            upload_file_name = '$upload_file_name',
            server_file_name = '$upload_file_name',
            file_size = '$file_size',
            file_width = '$file_width',
            file_height = '$file_height',
            server_path = '$server_path',
            thumb_path = '$thumb_path',
            thumb_size = '$thumb_size',
            thumb_width = '$thumb_width',
            thumb_height = '$thumb_height',
            exif_info = '$exif_info',
            regist_flag = 'Y',
            regist_date = now()";
        $this -> db -> query($sql);
    }

    function setEtcUploadFileUpdate($cid, $mid, $storageid, $upload_file_name, $file_size, $server_path, $file_width, $file_height, $thumb_path, $thumb_size, $thumb_width, $thumb_height, $exif_info)
    {
        $sql = "update md_etc_upload_file set
            file_size = '$file_size',
            file_width = '$file_width',
            file_height = '$file_height',
            thumb_path = '$thumb_path',
            thumb_size = '$thumb_size',
            thumb_width = '$thumb_width',
            thumb_height = '$thumb_height',
            exif_info = '$exif_info',
            regist_date = now()
            where cid = '$cid' 
            and mid = '$mid'
            and storageid = '$storageid'
            and upload_file_name = '$upload_file_name'
            and server_path = '$server_path'";
        $this -> db -> query($sql);
    }
    
}
?>