<?php
/*
 * @date : 20181105
 * @author : kdk
 * @brief : pod견적 관련 model.(알래스카)
 * @request :
 * @desc : 알래스카
 * @todo :
 */

class M_pod {
    var $db;
    var $this;
    function M_pod() {
        $this -> db = $GLOBALS[db];
    }

    ### 현재 선입금액 정보 조회.
    function getDepositMoney($cid, $mid) {
        //select sum(deposit_money) from pod_log_deposit_money where cid = 'print' and mid = 'ilarkTest';
        $sql = "select ifnull(sum(deposit_money), 0) as money from pod_log_deposit_money where cid='$cid' and mid='$mid'";
        list($data) = $this->db->fetch($sql, 1);
        return $data;
    }

    ### 누적 선입금액 정보 조회.
    function getTotDepositMoney($cid, $mid, $addWhere='') {
        $sql = "select ifnull(sum(tot_deposit_money), 0) as money 
            from pod_deposit_money 
            where cid='$cid' 
            and mid='$mid'
            $addWhere
        ";
        $result = $this->db-> fetch($sql);
        $total = $result[money];
        return $total;
    }

    ### 현재 선발행입금액 정보 조회.
    function getPreDepositMoney($cid, $mid) {
        //select sum(pre_deposit_money) from pod_log_deposit_money where cid = 'print' and mid = 'ilarkTest';
        $sql = "select ifnull(sum(pre_deposit_money), 0) as money from pod_log_deposit_money where cid='$cid' and mid='$mid'";
        list($data) = $this->db->fetch($sql, 1);
        return $data;
    }

    ### 누적 선발행입금액 정보 조회.
    function getTotPreDepositMoney($cid, $mid) {
        $sql = "select ifnull(sum(pre_deposit_money), 0) as money from pod_deposit_money where cid='$cid' and mid='$mid'";
        $result = $this->db-> fetch($sql);
        $total = $result[money];
        return $total;
    }
    
    ### 미수금 정보 조회.
    function getRemainMoney($cid, $mid) {
        $sql = "select ifnull(sum(remain_price), 0) as money from pod_pay where cid='$cid' and mid='$mid'";        
        //주문상태에 따른 추가 조건이 필요함.
        list($data) = $this->db->fetch($sql, 1);
        return $data;
    }    
    
    ### 주문정보.
    function getOrderList($cid, $addWhere='', $orderby='', $limit='', $bQuery=false){
        $query = "select a.*,b.* from pod_pay a left join exm_member b on a.cid = b.cid and a.mid = b.mid where a.cid='$cid' $addWhere $orderby $limit";

        if ($bQuery) return $query;
        else return $this->db->listArray($query);
    }
   
    function getOrderListCnt($cid, $addWhere='', $orderby='', $limit=''){
        $query = "select count(a.mid) as cnt from pod_pay a left join exm_member b on a.cid = b.cid and a.mid = b.mid where a.cid='$cid' $addWhere $orderby $limit";
        $cnt = $this->db->fetch($query);
        return $cnt[cnt];
    }
    
    ### 영업사원정보. (관리자 super=3)
    function getSalesList($cid) {
        //$sql = "select mid,name,super from exm_admin where cid='$cid' and super=3 order by regdt";
        $sql = "select mid,name,super from exm_admin where cid='$cid' order by regdt";
        //echo $sql;
        
        $res = $this->db->listarray($sql);
        foreach ($res as $key => $data) {
          $ret[$data[mid]] = $data;
        }
        
        return $ret;
    }
    
    ### 회원정보.
    function getMemberList($cid, $addWhere='', $orderby='', $limit='', $bQuery=false){
        //,(select sum(deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid) deposit_money
        //,(select sum(pre_deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid) pre_deposit_money
        
        $query = "select *
        ,(select sum(deposit_money) from pod_log_deposit_money where cid = a.cid and mid = a.mid) deposit_money
        ,(select sum(pre_deposit_money) from pod_log_deposit_money where cid = a.cid and mid = a.mid) pre_deposit_money
        ,(select sum(remain_price) from pod_pay where cid = a.cid and mid = a.mid) remain_money    
        from exm_member a where cid='$cid' and sort <= 0 $addWhere $orderby $limit";

        if ($bQuery) return $query;
        else return $this->db->listArray($query);
    }
   
    function getMemberListCnt($cid, $addWhere='', $orderby='', $limit=''){
        $query = "select count(mid) as cnt from exm_member a where cid='$cid' and sort <= 0 $addWhere $orderby $limit";
        $cnt = $this->db->fetch($query);
        return $cnt[cnt];
    }

    ### 회원미수금정보.
    function getMemberRemainList($cid, $addWhere='', $orderby='', $limit='', $bQuery=false){
        //,(select sum(deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid) deposit_money
        //,(select sum(pre_deposit_money) from pod_deposit_money where cid = a.cid and mid = a.mid) pre_deposit_money
        
        $query = "select a.*
        ,(select sum(deposit_money) from pod_log_deposit_money where cid = a.cid and mid = a.mid) deposit_money
        ,(select sum(pre_deposit_money) from pod_log_deposit_money where cid = a.cid and mid = a.mid) pre_deposit_money
        ,(select sum(remain_price) from pod_pay where cid = a.cid and mid = a.mid) remain_money
        ,(select regdt from pod_deposit_money where cid = a.cid and mid = a.mid order by regdt desc limit 1) final_date
        ,b.start_date,b.promise_date,b.promise_money,b.remainadmin,b.memo as bigo
        from exm_member a left join pod_member_remain_status b on b.cid=a.cid and a.mid=b.mid where a.cid='$cid' and sort <= 0 $addWhere $orderby $limit";

        if ($bQuery) return $query;
        else return $this->db->listArray($query);
    }
   
    function getMemberRemainListCnt($cid, $addWhere='', $orderby='', $limit=''){
        $query = "select count(a.mid) as cnt from exm_member a left join pod_member_remain_status b on b.cid=a.cid and a.mid=b.mid  where a.cid='$cid' and sort <= 0 $addWhere $orderby $limit";
        $cnt = $this->db->fetch($query);
        return $cnt[cnt];
    }
    
    ### 회원 미수금현황 관리.
    function getMemberRemainStatus($cid, $mid)
    {
        return $this->db->fetch("select * from pod_member_remain_status where cid = '$cid' and mid = '$mid'");
    }    
    
    function setMemberRemainStatusInsert($data) {
        //pod_member_remain_status
        $sql = "INSERT INTO pod_member_remain_status SET
            cid = '$data[cid]',
            mid = '$data[mid]',
            regdt = now(),
            start_date = '$data[start_date]',
            promise_date = '$data[promise_date]',
            promise_money = '$data[promise_money]',
            remainadmin = '$data[remainadmin]',
            memo = '$data[memo]'
        ";
        //debug($sql);
        $this->db->query($sql);
    }

    function setMemberRemainStatusUpdate($data) {
        //pod_member_remain_status
        $sql = "UPDATE pod_member_remain_status SET
            start_date = '$data[start_date]',
            promise_date = '$data[promise_date]',
            promise_money = '$data[promise_money]',
            remainadmin = '$data[remainadmin]',
            memo = '$data[memo]',
            update_date = now(),
            update_mid = '$data[update_mid]'
            WHERE cid = '$data[cid]' AND mid = '$data[mid]';
        ";
        //debug($sql);
        $this->db->query($sql);
    }

    function setMemberRemainStatusDelete($cid, $mid) {
        //pod_member_remain_status
        $sql = "DELETE FROM pod_member_remain_status WHERE cid = '$cid' and mid = '$mid'";
        //debug($sql);
        //$this->db->query($sql);
    }
    

    ### pod_deposit_money 입금 정보.
    function getDepositMoneyList($cid, $mid, $addWhere = '', $orderby='order by no desc', $limit='', $bQuery=false) {
        //pod_deposit_money
        $sql = "select * from pod_deposit_money 
            where cid = '$cid' and mid = '$mid' 
            $addWhere $orderby $limit";
        //debug($sql);
        if ($bQuery) return $sql;
        else return $this->db->listArray($sql);
    }    

    function setDepositMoneyInsert($data) {
        //pod_deposit_money
        $sql = "INSERT INTO pod_deposit_money SET
            cid = '$data[cid]',
            mid = '$data[mid]',
            admin = '$data[admin]',
            memo = '$data[memo]',
            regdt = now(),
            deposit_date = '$data[deposit_date]',
            deposit_method = '$data[deposit_method]',
            tot_deposit_money = '$data[tot_deposit_money]',
            deposit_money = '$data[deposit_money]',
            pre_deposit_money = '$data[pre_deposit_money]',
            cashreceipt_date = '$data[cashreceipt_date]',
            taxbill_date = '$data[taxbill_date]'
        ";
        //debug($sql);
        $this->db->query($sql);
    }

    function setDepositMoneyUpdate($data) {
        //pod_deposit_money
        $sql = "UPDATE pod_deposit_money SET
            memo = '$data[memo]',
            deposit_method = '$data[deposit_method]',
            deposit_money = '$data[deposit_money]',
            pre_deposit_money = '$data[pre_deposit_money]',
            cashreceipt_date = '$data[cashreceipt_date]',
            taxbill_date = '$data[taxbill_date]',
            update_date = now(),
            update_mid = '$data[update_mid]'
            WHERE no = '$data[no]' AND cid = '$data[cid]' AND mid = '$data[mid]';
        ";
        //debug($sql);
        $this->db->query($sql);
    }

    function setDepositMoneyDelete($no) {
        //pod_deposit_money
        //주문에 사용된 내역 삭제 처리 절차필요함.
        $sql = "DELETE FROM pod_deposit_money WHERE no = '$no'";
        //debug($sql);
        $this->db->query($sql);
    }

    //입금일자 수정.
    function setDepositMoneyDateUpdate($data) {
        //pod_deposit_money
        $sql = "UPDATE pod_deposit_money SET
            deposit_date = '$data[deposit_date]',
            deposit_method = '$data[deposit_method]',
            tot_deposit_money = '$data[tot_deposit_money]',
            deposit_money = '$data[deposit_money]',
            pre_deposit_money = '$data[pre_deposit_money]',
            cashreceipt_date = '$data[cashreceipt_date]',
            taxbill_date = '$data[taxbill_date]',
            memo = '$data[memo]',            
            update_date = now(),
            update_mid = '$data[update_mid]'
            WHERE no = '$data[no]' AND cid = '$data[cid]' AND mid = '$data[mid]';
        ";
        //debug($sql);
        $this->db->query($sql);
    }
   
    ### pod_log_deposit_money 입금 내역 로그 정보.getPodLogDepositMoneyList
    function depositHistoryList($cid, $mid, $addWhere = '', $orderby='', $limit='', $bQuery=false) {
        //pod_log_deposit_money
        $sql = "select * from pod_log_deposit_money 
            where cid = '$cid' and mid = '$mid' 
            $addWhere $orderby $limit";
        if ($bQuery) return $sql;
        else return $this->db->listArray($sql);
    }    

    //setPodLogDepositMoneyInsert    
    function depositHistoryInsert($data) {
        //pod_log_deposit_money
        //status = 1:입금, 2:사용, 3:수정
        $sql = "INSERT INTO pod_log_deposit_money SET
            cid = '$data[cid]',
            mid = '$data[mid]',
            admin = '$data[admin]',
            memo = '$data[memo]',
            regdt = now(),
            deposit_money = '$data[deposit_money]',
            pre_deposit_money = '$data[pre_deposit_money]',
            payno = '$data[payno]',
            status = '$data[status]'
        ";
        //debug($sql);
        $this->db->query($sql);
    }

    function depositHistoryUpdate($data) {
        //pod_log_deposit_money
        $sql = "UPDATE pod_log_deposit_money SET
            admin = '$data[admin]',
            memo = '$data[memo]',
            deposit_money = '$data[deposit_money]',
            pre_deposit_money = '$data[pre_deposit_money]',
            payno = '$data[payno]',
            update_date = now(),
            update_mid = '$data[update_mid]',
            status = '$data[status]'
            WHERE no = '$data[no]' AND cid = '$data[cid]' AND mid = '$data[mid]';
        ";
        //debug($sql);
        $this->db->query($sql);
    }

    function depositHistoryDelete($no) {
        //pod_log_deposit_money
        //주문에 사용된 내역 삭제 처리 절차필요함.
        $sql = "DELETE FROM pod_log_deposit_money WHERE no = '$no'";
        //debug($sql);
        //$this->db->query($sql);
    }
    
    ### pod_pay 주문 정보.
    function getPodPayList($cid, $mid, $addWhere = '', $orderby='', $limit='', $bQuery=false) {
        //pod_pay
        $sql = "select * from pod_pay 
            where cid = '$cid' and mid = '$mid' 
            $addWhere $orderby $limit";
        //debug($sql);
        if ($bQuery) return $sql;
        else return $this->db->listArray($sql);
    }    

    function setPodPayInsert($data) {
        //pod_pay
        $sql = "INSERT INTO pod_pay SET
            cid = '$data[cid]',
            mid = '$data[mid]',
            payno = '$data[payno]',
            admin = '$data[admin]',            
            payprice = '$data[payprice]',
            deposit_price = '$data[deposit_price]',
            pre_deposit_price = '$data[pre_deposit_price]',
            vat_price = '$data[vat_price]',
            ship_price = '$data[ship_price]',
            remain_price = '$data[remain_price]',
            manager_no = '$data[manager_no]',
            orddt = now(),
            autoproc_flag = '$data[autoproc_flag]',
            memo = '$data[memo]',
            status = '$data[status]',
            order_title = '$data[order_title]',
            order_data = '$data[order_data]',
            goodsno = '$data[goodsno]',
            goodsnm = '$data[goodsnm]',
            extopt_flag = '$data[extopt_flag]',
            order_type = '$data[order_type]'            
        ";
        //debug($sql);
        
        $this->db->query($sql);
    }

    function setPodPayUpdate($data) {
        //pod_pay
        $sql = "UPDATE pod_pay SET
            payprice = '$data[payprice]',
            deposit_price = '$data[deposit_price]',
            pre_deposit_price = '$data[pre_deposit_price]',
            vat_price = '$data[vat_price]',
            ship_price = '$data[ship_price]',
            remain_price = '$data[remain_price]',
            manager_no = '$data[manager_no]',
            receiptdt = '$data[receiptdt]',
            receiptadmin = '$data[receiptadmin]',
            deliverydt = '$data[deliverydt]',
            deliveryadmin = '$data[deliveryadmin]',
            autoproc_flag = '$data[autoproc_flag]',
            memo = '$data[memo]',
            update_date = now(),
            update_mid = '$data[update_mid]',
            order_title = '$data[order_title]',
            order_data = '$data[order_data]',
            extopt_flag = '$data[extopt_flag]',
            order_type = '$data[order_type]'
            WHERE cid = '$data[cid]' AND mid = '$data[mid]' AND payno = '$data[payno]';
        ";
        //debug($sql);
        $this->db->query($sql);
    }

    //접수완료 변경.
    function setPodPayReceiptUpdate($cid,$payno,$status,$receiptdt,$receiptadmin,$update_mid) {
        //pod_pay
        $sql = "UPDATE pod_pay SET
            status = '$status',
            status_date = now(),
            receiptdt = '$receiptdt',
            receiptadmin = '$receiptadmin',
            update_mid = '$update_mid'
            WHERE cid = '$cid' AND payno = '$payno';
        ";
        //debug($sql);
        $this->db->query($sql);
    }    
    
    //주문상태 변경.
    function setPodPayStatusUpdate($cid,$mid,$payno,$status) {
        //pod_pay
        $sql = "UPDATE pod_pay SET
            status = '$status',
            status_date = now()
            WHERE cid = '$cid' AND mid = '$mid' AND payno = '$payno';
        ";
        //debug($sql);
        $this->db->query($sql);
    }
    
    //취소.    
    function setPodPayCancel($cid,$mid,$payno,$status,$update_mid) {
        //pod_pay
        //주문에 사용된 내역 삭제 처리 절차필요함.
        $sql = "UPDATE pod_pay SET
            canceldt = now(),
            status = '$status',
            update_mid = '$update_mid'
            WHERE cid = '$cid' AND mid = '$mid' AND payno = '$payno';
        ";
        //debug($sql);
        $this->db->query($sql);
    }
    
    //파일 수정.order_files
    function setPodPayFileUpdate($cid,$mid,$payno,$order_files,$storageid) {
        //pod_pay
        $sql = "UPDATE pod_pay SET
            order_files = '$order_files',
            storageid = '$storageid'
            WHERE cid = '$cid' AND mid = '$mid' AND payno = '$payno';
        ";
        //debug($sql);
        $this->db->query($sql);
    }

    //exm_ord_item DB에  est_order_type='', est_file_down_full_path='' 저장.
    function setOrdItemFileUpdate($payno,$storageid,$file) {
        //exm_ord_item
        $sql = "UPDATE exm_ord_item SET
            est_order_type = 'UPLOAD',
            est_file_down_full_path = '$file',
            storageid = '$storageid'
            WHERE payno = '$payno';
        ";
        //debug($sql);
        $this->db->query($sql);
    }    
    
    //입금처리.
    function setPodPayDepositProc($data) {
        //pod_pay
        $sql = "UPDATE pod_pay SET
            deposit_price = '$data[deposit_price]',
            remain_price = '$data[remain_price]'
            WHERE cid = '$data[cid]' AND mid = '$data[mid]' AND payno = '$data[payno]';
        ";
        //debug($sql);
        $this->db->query($sql);
    }    
    
    //결제번호 확인하기.
    function checkPayno($payno)
    {
        return $this->db->fetch("select payno from exm_pay where payno = '$payno'", 1);
    }    

    ### 매출현황.
    function getStatSalesList($cid, $addWhere='', $addWhere2='', $orderby='', $limit='', $bQuery=false){
        $query = "select *
        ,(select ifnull(sum(payprice), 0) + ifnull(sum(vat_price), 0) + ifnull(sum(ship_price), 0) from pod_pay where cid = a.cid and mid = a.mid $addWhere2) payprice
        ,(select ifnull(sum(deposit_price), 0) from pod_pay where cid = a.cid and mid = a.mid $addWhere2) deposit_price
        ,(select ifnull(sum(remain_price), 0) from pod_pay where cid = a.cid and mid = a.mid) remain_price
        ,(select ifnull(sum(deposit_money), 0) from pod_deposit_money where cid = a.cid and mid = a.mid) deposit_money
        ,(select ifnull(sum(pre_deposit_money), 0) as money from pod_log_deposit_money where cid = a.cid and mid = a.mid) pre_deposit_money
        from exm_member a where cid='$cid' and sort <= 0 $addWhere $orderby $limit";

        if ($bQuery) return $query;
        else return $this->db->listArray($query);
    }
   
    function getStatSalesListCnt($cid, $addWhere='', $orderby='', $limit=''){
        $query = "select count(mid) as cnt from exm_member a where cid='$cid' and sort <= 0 $addWhere $orderby $limit";
        $cnt = $this->db->fetch($query);
        return $cnt[cnt];
    }

    ### 매출현황 상세보기.
    function getStatSalesDetailList($cid, $addWhere='', $addWhere2='', $addwhere3='', $bQuery=false){
        $query = "select *
        ,(select ifnull(sum(payprice), 0) + ifnull(sum(vat_price), 0) + ifnull(sum(ship_price), 0) from pod_pay where cid = a.cid and mid = a.mid $addWhere2) as payprice
        ,(select ifnull(sum(deposit_price), 0) from pod_pay where cid = a.cid and mid = a.mid $addWhere2) as deposit_price
        ,(select ifnull(sum(remain_price), 0) from pod_pay where cid = a.cid and mid = a.mid) as remain_price        
        ,(select ifnull(sum(pre_deposit_price), 0) from pod_pay where cid = a.cid and mid = a.mid $addWhere2) as pre_deposit_price
        ,(select ifnull(sum(deposit_money), 0) from pod_deposit_money where cid = a.cid and mid = a.mid $addWhere3) as deposit_money
        ,(
            select ifnull(sum(deposit_money), 0) from pod_deposit_money where cid = a.cid and mid = a.mid
            and deposit_method not in (6,8,9)
            $addWhere3
        ) as cash_money
        ,(
            select ifnull(sum(deposit_money), 0) from pod_deposit_money where cid = a.cid and mid = a.mid
            and deposit_method not in (6,8,9)
            $addWhere3
        ) as cashreceipt_money
        ,(
            select ifnull(sum(deposit_money), 0) from pod_deposit_money where cid = a.cid and mid = a.mid
            and deposit_method in (6)
            $addWhere3
        ) as card_money
        ,(
            select ifnull(sum(deposit_money), 0) from pod_deposit_money where cid = a.cid and mid = a.mid
            and deposit_method in (8,9)
            $addWhere3
        ) as etc_money
        from exm_member a where cid='$cid' and sort <= 0 $addWhere";

        if ($bQuery) return $query;
        else return $this->db->listArray($query);
    }    
        
    ### 일매출현황.
    function getStatDay($cid, $sdt, $addWhere='', $bQuery=false){
        $query = "
            select
            cast($sdt as char(10)) as dt
            ,ifnull(sum(payprice)+sum(vat_price)+sum(ship_price), 0) as totpayprice 
            ,ifnull(sum(payprice), 0) as payprice 
            ,ifnull(sum(vat_price), 0) as vat_price 
            ,ifnull(sum(remain_price), 0) as remain_price 
            ,ifnull(sum(deposit_price), 0) as deposit_price
            ,ifnull(sum(pre_deposit_price), 0) as pre_deposit_price 
            ,sum(ea) as totea
            ,(
                select ifnull(sum(ea), 0)
                from pod_pay where cid='$cid'
                and $sdt is not null and left($sdt,10)<>'0000-00-00'
                and left($sdt,10)=left(a.$sdt,10)
                $addWhere
                and status in (1,2)
            ) as ea
            from pod_pay a where cid='$cid'
            and $sdt is not null and left($sdt,10)<>'0000-00-00'
            $addWhere
            group by left($sdt,10);
        ";

        if ($bQuery) return $query;
        else return $this->db->listArray($query);
    }
    
    ### 월매출현황
    function getStatMonth($cid, $sdt, $addWhere='', $bQuery=false) {
        //select * from pod_pay where left(orddt,7)='2018-11';
        $query = "
            select
            ifnull(sum(payprice)+sum(vat_price)+sum(ship_price), 0) as totpayprice 
            from pod_pay a where cid='$cid'
            $addWhere
            and $sdt is not null and left($sdt,7)<>'0000-00';            
        ";
        //debug($query);
        if ($bQuery) return $query;
        else {
            $result = $this->db->fetch($query);
            $total = $result[totpayprice]; 
            return $total;
        }
    }
    
    ### 일일입금현황
    function getStatDepositDay($cid, $addWhere='', $bQuery=false){
        $query = "
            select
            cast(deposit_date as char(10)) as dt
            ,ifnull(sum(tot_deposit_money), 0) as tot_deposit_money 
            ,ifnull(sum(deposit_money), 0) as deposit_money 
            ,ifnull(sum(pre_deposit_money), 0) as pre_deposit_money 
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='1'
                $addWhere
            ) as method1
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='2'
                $addWhere
            ) as method2
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='3'
                $addWhere
            ) as method3
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='4'
                $addWhere
            ) as method4
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='5'
                $addWhere
            ) as method5
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='6'
                $addWhere
            ) as method6
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='print'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='7'
                $addWhere
            ) as method7
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='8'
                $addWhere
            ) as method8
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='9'
                $addWhere
            ) as method9
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='10'
                $addWhere
            ) as method10
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='11'
                $addWhere
            ) as method11
            ,(
                select ifnull(sum(tot_deposit_money), 0)
                from pod_deposit_money where cid='$cid'
                and left(deposit_date,10)=left(a.deposit_date,10) and deposit_method='12'
                $addWhere
            ) as method12
            from pod_deposit_money a where cid='$cid'
            $addWhere
            group by left(deposit_date,10);
        ";

        if ($bQuery) return $query;
        else return $this->db->listArray($query);
    }

    ### 일일입금현황 입금방법.
    function getStatDepositDayPopup($cid, $addWhere = '', $bQuery=false) {
        //pod_deposit_money
        $sql = "select * from pod_deposit_money 
            where cid = '$cid' 
            $addWhere";
        //debug($sql);
        if ($bQuery) return $sql;
        else return $this->db->listArray($sql);
    }    
    
        
    ### 회원매출입금현황.
    function getStatDepositSalesMember($cid, $mid, $sdt, $addWhere='', $bQuery=false){
        $query = "
            select
            cast($sdt as char(10)) as dt
            ,ifnull(sum(payprice)+sum(vat_price)+sum(ship_price), 0) as totpayprice 
            ,ifnull(sum(payprice), 0) as payprice 
            ,ifnull(sum(vat_price), 0) as vat_price 
            ,ifnull(sum(remain_price), 0) as remain_price 
            ,ifnull(sum(deposit_price), 0) as deposit_price
            ,ifnull(sum(pre_deposit_price), 0) as pre_deposit_price 
            ,(
                select ifnull(sum(deposit_money), 0) as money from pod_deposit_money where cid='$cid' and mid='$mid'
                and left(deposit_date,10)=left(a.$sdt,10)
                and deposit_method not in (6,8,9)
            ) as cash_money
            ,(
                select ifnull(sum(deposit_money), 0) as money from pod_deposit_money where cid='$cid' and mid='$mid'
                and left(cashreceipt_date,10)=left(a.$sdt,10)
                and deposit_method not in (6,8,9)
            ) as cashreceipt_money
            ,(
                select ifnull(sum(deposit_money), 0) as money from pod_deposit_money where cid='$cid' and mid='$mid'
                and left(deposit_date,10)=left(a.$sdt,10)
                and deposit_method in (6)
            ) as card_money
            from pod_pay a where cid='$cid'
            and $sdt is not null and left($sdt,10)<>'0000-00-00'
            $addWhere
            group by left($sdt,10);
        ";

        if ($bQuery) return $query;
        else return $this->db->listArray($query);
    }    

    ### 미수금 정보 조회.
    function getRemainPrice($cid, $mid, $addWhere = '') {
        $sql = "select ifnull(sum(remain_price), 0) as money from pod_pay where cid='$cid' and mid='$mid' 
            $addWhere
        ";

        list($data) = $this->db->fetch($sql, 1);
        return $data;
    }    

    ### 입금 정보 조회.
    function getDepositPrice($cid, $mid, $addWhere = '') {
        $sql = "select ifnull(sum(deposit_money), 0) as money from pod_deposit_money where cid='$cid' and mid='$mid' 
            $addWhere
        ";

        list($data) = $this->db->fetch($sql, 1);
        return $data;
    }
    
}
?>