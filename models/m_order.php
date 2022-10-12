<?php

/*
* @date : 20180316
* @author : kdk
* @brief : 주문리스트 총 결제금액 추가
* @request :
* @desc : getOrdItemSumPayPrice() 추가.
* @todo : 처음 쿼리에서 처리가 가능해 보임 추후 개선필요.
*/


/**
* order model
* 2015.02.11 by chunter
*/

class M_order{
    var $db;
    function M_order() {
        $this->db = $GLOBALS[db];
    }

    function getPayInfo($payno) {
        $sql = "select * from exm_pay where payno = '$payno'";
        return $this->db->fetch($sql);
    }

    function getOrdInfo($payno, $ordno) {
        $sql = "select * from exm_ord where payno = '$payno' and ordno = '$ordno'";
        return $this->db->fetch($sql);
    }

    function getOrdItemInfo($payno, $ordno, $ordseq) {
        $sql = "select * from exm_ord_item where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'";
        return $this->db->fetch($sql);
    }

    function getOrdItemInfo_v2($payno, $ordno, $ordseq) {
        $sql = "select * from exm_ord_item where payno = '$payno' and ordno = '$ordno' and ordseq in ($ordseq)";
        return $this->db->listArray($sql);
    }

    function getOrdGroupConcatInfo($payno) {
        $sql = "select group_concat(rid) as rid from exm_ord where payno = '$payno'";
        return $this->db->fetch($sql);
    }

    function getOrdItemCnt($payno, $ordno) {
        $sql = "select count(ordseq) as chkItem from exm_ord_item where payno = '$payno' and ordno = '$ordno'";
        return $this->db->fetch($sql);
    }

    function getOrdItemList($payno, $ordno = '', $ordseq = '') {
       if($ordno && $ordseq) $addWhere = " and ordno = '$ordno' and ordseq = '$ordseq'";
        $sql = "select * from exm_ord_item where payno = '$payno' $addWhere";
        return $this->db->listArray($sql);
    }

    function getOrdItemSumEa($payno, $itemstep= ''){
       if($itemstep) $addWhere = "and itemstep = '$itemstep'";
       $sql = "select sum(ea) as totea from exm_ord_item where payno = $payno $addWhere";
       return $this -> db -> fetch($sql);
    }

    function getOrdItemSumPayPrice($payno, $itemstep= ''){
       if($itemstep) $addWhere = "and itemstep = '$itemstep'";
       $sql = "select sum(payprice) as totpayprice from exm_ord_item where payno = $payno $addWhere";
       return $this -> db -> fetch($sql);
    }

    function getOrdUploadItemInfo($payno, $ordno, $ordseq) {
        $sql = "select * from exm_ord_upload where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'";
        return $this->db->fetch($sql);
    }

    function getAccDescInfo($cid, $payno, $ordno, $item_rid, $kind) {
        $sql = "select * from exm_acc_desc where cid = '$cid' and rid = '$item_rid' and kind = '$kind' and payno = '$payno' and ordno = '$ordno'";
        return $this->db->fetch($sql);
    }


    function setPayStepUpdate($payno, $paystep) {
        $sql = "update exm_pay set paystep = '$paystep' where payno = '$payno'";
        $this->db->query($sql);
    }


    //ord_item_step 변경 (상품 전체를 하거나 개발 상품만 하거나)    20150213    chunter
    function setOrdItemStepUpdate($payno, $ordno, $ordseq, $itemstep, $addqr = '')
    {
      if ($ordno && $ordseq)
        $sql = "update exm_ord_item set itemstep = '$itemstep' $addqr where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'";
      else
        $sql = "update exm_ord_item set itemstep = '$itemstep' $addqr where payno = '$payno'";
      $this->db->query($sql);
    }

   //ord_item_step 변경 (상품 전체를 하거나 개발 상품만 하거나)    20150213    chunter
   function setOrdItemStepUpdate_v2($payno, $ordno, $ordseq, $itemstep, $addqr = '')
   {
      if ($ordno && $ordseq){
         $ordseq = substr($ordseq, 0, -1);

         $sql = "update exm_ord_item set itemstep = '$itemstep' $addqr where payno = '$payno' and ordno = '$ordno' and ordseq in ($ordseq)";

      }
      else
         $sql = "update exm_ord_item set itemstep = '$itemstep' $addqr where payno = '$payno'";
      //$this->db->query($sql);
   }

    function setOrdItemPodsStateUpdate($payno, $ordno, $ordseq, $pods_trans, $pods_trans_msg)
    {
      if ($ordno && $ordseq)
        $sql = "update exm_ord_item set pods_trans='{$pods_trans}',pods_trans_msg='$pods_trans_msg' where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'";
      else
        $sql = "update exm_ord_item set pods_trans='{$pods_trans}',pods_trans_msg='$pods_trans_msg' where payno = '$payno'";
      $this->db->query($sql);
    }


    function setEditStateUpdate($storageid, $state)
    {
      $sql = "update exm_edit set state = '$state' where storageid = '$storageid'";
      $this->db->query($sql);
    }


    function setEditStateUpdateWithStorageids($storageids, $state)
    {
      $sql = "update exm_edit set state = '$state' where storageid in ( $storageids )";
      $this->db->query($sql);
    }


    function setEditStateNPodsTransUpdateWithStorageids($storageids, $state, $pods_trans, $pods_trans_msg)
    {
      $sql = "update exm_edit set state = '$state', pods_trans='{$pods_trans}',pods_trans_msg='$pods_trans_msg' where storageid in ( $storageids )";
      $this->db->query($sql);
    }

    function setPayDateUpdateFromAdmin($payno, $payadmin)
    {
      $sql = "update exm_pay set paydt = now(),payadmin='$payadmin' where payno = '$payno'";
      $this->db->query($sql);
    }


    function setConfirmDateUpdateFromAdmin($payno, $confirmadmin)
    {
      $sql = "update exm_pay set confirmdt = now(),confirmadmin='$confirmadmin' where payno = '$payno'";
      $this->db->query($sql);
    }


    function setPayDataInsert($payno, $cid, $mid, $sale_price, $paymethod, $pay_kinds, $pay_emoney, $pay_credit1, $pay_credit2_credit, $pay_credit2_deposit, $pay_price)
    {
      $sql = "insert into tb_pay_data set
                 payno                = '$payno',
                 cid                  = '$cid',
                 mid                  = '$mid',
                 sale_price           = '$sale_price',
                 paymethod            = '$paymethod',
                 pay_kinds            = '$pay_kinds',
                 pay_emoney           = '$pay_emoney',
                 pay_credit1          = '$pay_credit1',
                 pay_credit2_credit   = '$pay_credit2_credit',
                 pay_credit2_deposit  = '$pay_credit2_deposit',
                 pay_price            = '$pay_price',
                 account_flag         = 'N'
                on duplicate key update
                 sale_price           = sale_price + '$sale_price',
                 paymethod            = '$paymethod',
                 pay_kinds            = '$pay_kinds',
                 pay_emoney           = pay_emoney + '$pay_emoney',
                 pay_credit1          = pay_credit1 + '$pay_credit1',
                 pay_credit2_credit   = pay_credit2_credit + '$pay_credit2_credit',
                 pay_credit2_deposit  = pay_credit2_deposit + '$pay_credit2_deposit',
                 pay_price            = pay_price + '$pay_price',
                 account_flag         = 'N'";
      $this->db->query($sql);
    }


    function GetSumPayData($cid, $mid, $regdt_s, $regdt_e){
        $sql = "select sum(y.payprice) as payprice, sum(x.shipprice) as shipprice from exm_pay x
                 inner join exm_ord_item y on y.payno = x.payno
                 where cid = '$cid' and mid = '$mid' and itemstep in (2, 3, 4, 5, 92) and orddt >= '$regdt_s' and orddt < '$regdt_e'";

        return $this->db->fetch($sql);
    }


    function getPayData($payno) {
        $sql = "select * from tb_pay_data where payno = '$payno'";
        return $this -> db -> fetch($sql);
    }


    //스튜디오 업로드 주문 재결제
    function updatePayData($saleprice, $payprice, $ord_shipprice, $payno) {
        $sql = "update exm_pay set saleprice = '$saleprice', payprice = '$payprice', shipprice = shipprice - '$ord_shipprice' where payno = '$payno'";
        return $this -> db -> query($sql);
    }

    //스튜디오 업로드 주문 재결제
    function updateOrdData($saleprice, $payno, $ordno) {
        $sql = "update exm_ord set ordprice = '$saleprice' where payno = '$payno' and ordno = '$ordno'";
        return $this -> db -> query($sql);
    }

    function setEmoneyLogInsert($cid, $mid, $memo, $emoney, $_mid, $status = '', $payno = '', $ordno = '', $ordseq = '')
    {
      if ($payno)
        $addqr = ",payno = '$payno', ordno = '$ordno', ordseq = '$ordseq'";

      $sql = "insert into exm_log_emoney set 
        cid     = '$cid',
        mid     = '$mid',
        memo    = '$memo',
        regdt   = now(),
        emoney  = '$emoney',
        admin   = '$_mid',
        status	= '$status'
        $addqr";
      $this->db->query($sql);
    }

    function setStepLogInsert($payno, $ordno, $ordseq, $from, $to, $admin, $memo)
    {
      $sql = "insert into exm_log_step set
        payno   = '$payno',
        ordno   = '$ordno',
        ordseq  = '$ordseq',
        regdt   = now(),
        `from`  = '$from',
        `to`    = '$to',
        admin   = '$admin',
        memo    = '$memo'";
      $this->db->query($sql);
    }


    function setAccHistoryInsert($cid, $admin_mid, $data, $month = '', $flag = '')
    {
      $sql = "insert into exm_acc_history set
        cid                     = '$data[cid]',
        rid                     = '$data[item_rid]',
        payno                   = '$data[payno]',
        ordno                   = '$data[ordno]',
        ordseq                  = '$data[ordseq]',
        paydt                   = '$data[paydt]',
        goodsno                 = '$data[goodsno]',
        goodsnm                 = '$data[goodsnm]',
        ea                      = '$data[ea]',
        supplyprice_goods       = '$data[supplyprice_goods]',
        supplyprice_opt         = '$data[supplyprice_opt]',
        supplyprice_addopt      = '$data[supplyprice_addopt]',
        supplyprice_printopt    = '$data[supplyprice_printopt]',
        price_goods             = '$data[goods_price]',
        price_opt               = '$data[aprice]',
        price_addopt            = '$data[addopt_aprice]',
        price_printopt          = '$data[print_aprice]',
        regdt                   = now(),
        reg_cid                 = '$cid',
        reg_admin               = '$admin_mid',
        month                   = '$month',
        flag                    = '$flag',
        paymethod               = '$data[paymethod]'";
      $this->db->query($sql);
    }


    function setAccDpriceInsert($cid, $admin_mid, $data, $ord_data, $month = '')
    {
      $sql = "insert into exm_acc_dprice set
            cid         = '$data[cid]',
            rid         = '$data[item_rid]',
            month       = '$month',
            goodsnm     = '$data[goodsnm]',
            regdt       = now(),
            reg_cid     = '$cid',
            reg_admin   = '$admin_mid',
            payno       = '$data[payno]',
            ordno       = '$data[ordno]',
            dprice      = '$ord_data[shipprice]',
            dprice_acc  = '$ord_data[acc_shipprice]',
            dprice_add  = '0',
            paymethod   = '$data[paymethod]',
            paydt       = '$data[paydt]'
        on duplicate key update no = no";
      $this->db->query($sql);
    }


    function setAccDescInsertFullData($cid, $admin_mid, $flag, $kind, $comment, $data)
    {
      $sql = "insert into exm_acc_desc set
        cid                     = '$data[cid]',
        rid                     = '$data[item_rid]',
        flag                    = '$flag',
        kind                    = '$kind',
        dt                      = now(),
        regdt                   = now(),
        payno                   = '$data[payno]',
        ordno                   = '$data[ordno]',
        ordseq                  = '$data[ordseq]',
        mid                     = '$data[mid]',
        paymethod               = '$data[paymethod]',
        goodsno                 = '$data[goodsno]',
        goodsnm                 = '$data[goodsnm]',
        optno                   = '$data[optno]',
        opt                     = '$data[opt]',
        addopt                  = '$data[addopt]',
        printopt                = '$data[printopt]',
        addpage                 = '$data[addpage]',
        ea                      = '$data[ea]',
        supplyprice_goods       = '$data[supplyprice_goods]',
        supplyprice_opt         = '$data[supplyprice_opt]',
        supplyprice_addopt      = '$data[supplyprice_addopt]',
        supplyprice_printopt    = '$data[supplyprice_printopt]',
        supplyprice_addpage     = '$data[supplyprice_addpage]',
        cost_goods              = '$data[cost_goods]',
        cost_opt                = '$data[cost_opt]',
        cost_addopt             = '$data[cost_addopt]',
        cost_printopt           = '$data[cost_printopt]',
        cost_addpage            = '$data[cost_addpage]',
        price_goods             = '$data[goods_price]',
        price_opt               = '$data[aprice]',
        price_addopt            = '$data[addopt_aprice]',
        price_printopt          = '$data[print_aprice]',
        price_addpage           = '$data[addpage_aprice]',
        reg_cid                 = '$cid',
        reg_admin               = '$admin_mid',
        comment                 = '$comment'";
      $this->db->query($sql);
    }


    function setAccDescInsert($cid, $admin_mid, $flag, $kind, $comment, $data, $ord_data)
    {
      $sql = "insert into exm_acc_desc set
            cid                     = '$data[cid]',
            rid                     = '$data[item_rid]',
            flag                    = '$flag',
            kind                    = '$kind',
            dt                      = now(),
            regdt                   = now(),
            payno                   = '$data[payno]',
            ordno                   = '$data[ordno]',
            mid                     = '$data[mid]',
            paymethod               = '$data[paymethod]',
            supplyprice_ship        = '$ord_data[shipprice]',
            cost_ship               = '$ord_data[acc_shipprice]',
            price_ship              = '$ord_data[shipprice]',
            reg_cid                 = '$cid',
            reg_admin               = '$admin_mid',
            comment                 = '$comment'";
      $this->db->query($sql);
    }



    //주문 검수 완료 처리.    20150615    chunter
    //승인완료 확인 처리 flag 추가 / 15.11.19 / kjm
    function setOrderInspectionUpdate($payno, $ordno, $ordseq)
    {
      $sql = "update exm_ord_item set order_inspection = 'Y', order_inspection_date = now(), order_allow_chk = '1' where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'";
      $this->db->query($sql);
    }

    //PG 정보들 저장.      20150729    chunter
    function setPGPaymentDataUpdate($payno, $escrow, $pglog, $pgcode, $step, $bankinfo, $qr)
    {
      $sql = "update exm_pay set $qr
              escrow    = '$escrow',
              pglog   = '$pglog',
              pgcode    = '$pgcode',
              paystep   = '$step',
              bankinfo  = '$bankinfo'
            where payno='$payno'";
      $this->db->query($sql);
    }

    function setPGCodeUpdate($payno, $pgcode)
    {
      $sql = "update exm_pay set pgcode = '$pgcode' where payno='$payno'";
      $this->db->query($sql);
    }

    //쿠폰 사용완료 처리      20150729    chunter
    function setPGPaymentUpdate($couponsetNo, $payno, $ordno, $ordseq)
    {
      $sql = "update exm_coupon_set set
                  coupon_use    = 1,
                  payno     = $payno,
                  ordno     = $ordno,
                  ordseq      = $ordseq,
                  coupon_usedt  = now()
                where no = '$couponsetNo'";
      $this->db->query($sql);
    }

    //쿠폰 정보 가져오기    210416 jtkim
    function getCouponInfo($payNo, $used = 0) // 1 : 사용 , 0 : 미사용
    {
    	$sql = "select 
       		a.coupon_usedt,a.payno,b.coupon_code,b.cid,coupon_name,b.coupon_type,b.coupon_way,b.coupon_price,b.coupon_rate,b.coupon_price_limit
       		from exm_coupon_set a
			left join exm_coupon b on a.coupon_code = b.coupon_code		
			where a.payno= '$payNo' and a.coupon_use = $used";

    	return $this->db->fetch($sql);
    }

    //장바구니 번호로 결제수단 가져오기 / 15.11.24 / kjm
    function getPayMethodWithCartno($cartno){
       $sql = "select a.paymethod from exm_pay a inner join exm_ord_item b on a.payno = b.payno where cartno = '$cartno'";
       return $this->db->fetch($sql);
    }

   //장바구니 번호로 ord_item 정보 가져오기 / 15.11.25 / kjm
   function getOrdItemInfoWidhCartno($cartno) {
      $sql = "select * from exm_ord_item where cartno = '$cartno'";
      return $this->db->fetch($sql);
   }

   //장바구니 번호로 ord_item itemstep 업데이트 하기 정보 가져오기 / 15.11.25 / kjm
   function setOrdItemStepWithCartno($cartno, $itemstep) {
      $sql = "update exm_ord_item set itemstep = '$itemstep' where cartno = '$cartno' and itemstep not in (0, -91)";
      $this->db->query($sql);
   }


   //P관리자에서 사용. 2016.06.01 by kdk
	function getList($cid, $step = '', $addWhere = '') {
	if ($step == "01") {//1.무통장입금
	   	$stepWhere = " and (select ordseq from exm_ord_item where payno = a.payno and itemstep = '1' limit 1) > 0";
	} else if ($step == "91") {//2.주문승인(승인대기)
	   	$stepWhere = " and a.paystep in ('2', '91', '92') 
	   		and (a.paymethod != 'd')
			and (select ordseq from exm_ord_item where payno = a.payno and (order_inspection is null or order_inspection='' or order_allow_chk = '0') limit 1) > 0 
	   		and (select ordseq from exm_ord_item where payno = a.payno and itemstep not in ('91','-9') limit 1) > 0 ";
	} else if ($step == "92") {//3.제작진행(승인완료,제작중)
		$stepWhere = " and a.paystep in ('2', '92', '3') 
			and (select ordseq from exm_ord_item where payno = a.payno and order_allow_chk = '1' limit 1) > 0
			and (select ordseq from exm_ord_item where payno = a.payno and order_inspection = 'Y' limit 1) > 0";
	} else if ($step == "05") {//4.발송완료
		$stepWhere = " and (select ordseq from exm_ord_item where payno = a.payno and itemstep = '5' limit 1) > 0";
	} else if ($step == "C") {//5.주문취소
		$stepWhere = " and (select ordseq from exm_ord_item where payno = a.payno and itemstep = '-9' limit 1) > 0";
	} else {//조건이 쿼리로 넘어오면..
		$stepWhere = $step;
	}

	$sql = "select * from exm_pay a 
		where a.cid = '$cid' $stepWhere $addWhere ";

	//debug($sql);
	return $this -> db -> listArray($sql);
	}
	function getListCount($cid, $step = '', $addWhere = '') {
	if ($step == "01") {//1.무통장입금
	   	$stepWhere = " and (select ordseq from exm_ord_item where payno = a.payno and itemstep = '1' limit 1) > 0";
	} else if ($step == "91") {//2.주문승인(승인대기)
	   	$stepWhere = " and a.paystep in ('2', '91', '92') 
	   		and (a.paymethod != 'd')
			and (select ordseq from exm_ord_item where payno = a.payno and (order_inspection is null or order_inspection='' or order_allow_chk = '0') limit 1) > 0 
	   		and (select ordseq from exm_ord_item where payno = a.payno and itemstep not in ('91','-9') limit 1) > 0 ";
	} else if ($step == "92") {//3.제작진행(승인완료,제작중)
		$stepWhere = " and a.paystep in ('2', '92', '3') 
			and (select ordseq from exm_ord_item where payno = a.payno and order_allow_chk = '1' limit 1) > 0
			and (select ordseq from exm_ord_item where payno = a.payno and order_inspection = 'Y' limit 1) > 0";
	} else if ($step == "05") {//4.발송완료
		$stepWhere = " and (select ordseq from exm_ord_item where payno = a.payno and itemstep = '5' limit 1) > 0";
	} else if ($step == "C") {//5.주문취소
		$stepWhere = " and (select ordseq from exm_ord_item where payno = a.payno and itemstep = '-9' limit 1) > 0";
	}

	$sql = "select * from exm_pay a 
		where a.cid = '$cid' $stepWhere $addWhere ";

	//debug($sql);
	$result = $this -> db -> query($sql);
	$cnt = $this -> db -> count;
 	return $cnt;
	}



	/*
	function getList($cid, $addWhere = '') {
	$sql = "select * from exm_pay a
		inner join exm_ord b on b.payno = a.payno
		inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno
		inner join exm_goods d on d.goodsno = c.goodsno
		where a.cid = '$cid' $addWhere
		";

	//debug($sql);
	return $this -> db -> listArray($sql);
	}
	function getListCount($cid, $addWhere = '') {
	$sql = "select * from exm_pay a
		inner join exm_ord b on b.payno = a.payno
		inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno
		inner join exm_goods d on d.goodsno = c.goodsno
		where a.cid = '$cid' $addWhere ";

	//debug($sql);
	$result = $this -> db -> query($sql);
	$cnt = $this -> db -> count;
 	return $cnt;
	}
	*/

	//P관리자에서 사용 (견적). 2016.06.01 by kdk
	function getExtraList($cid, $addWhere = '') {
	  $sql = "select 
		          *, (select goodsnm from exm_goods where goodsno=tb_extra_cart.goodsno) as 'goodsnm' 
	          from tb_extra_cart where cid = '$cid' 
		       and (order_status is null or (order_status in ('c','p'))) 
      		and goodsno in (select goodsno from exm_goods where extra_auto_pay_flag=0)
      		$addWhere
      ";

      //debug($sql);
      return $this -> db -> listArray($sql);
   }

   function getExtraListCount($cid, $addWhere = '') {
      $sql = "select * from tb_extra_cart where cid = '$cid' 
			and (order_status is null or (order_status in ('c','p'))) 
			and goodsno in (select goodsno from exm_goods where extra_auto_pay_flag=0)
			$addWhere
			";

		//debug($sql);
		$result = $this -> db -> query($sql);
		$cnt = $this -> db -> count;
    	return $cnt;
   	}
   	function getEditInfoWithStorageid($storageid, $mid='') {
   		$addwhere = ($mid) ? "and mid = '{$mid}'" : "";
      $sql = "select * from exm_edit where storageid = '$storageid' $addwhere";
      return $this->db->fetch($sql);
	}

   function getOrdItemInfoForOrdno($payno, $ordno) {
      $sql = "select * from exm_ord_item where payno = '$payno' and ordno = '$ordno'";
		//debug($sql);
      return $this->db->listArray($sql);
   }

   function getOrdItemInfoForPayno($payno) {
      $sql = "select * from exm_ord_item where payno = '$payno'";
		//debug($sql);
      return $this->db->listArray($sql);
   }

	//pod_group에서 다운로드 카운트를 해야함.est_fullpost 필드 활용 / 16.09.20 / kdk
   function setEditEstfullpostUpdate($storageid) {
      $sql = "update exm_edit set est_fullpost = '' where storageid = '$storageid'";
      $this->db->query($sql);
   }

   function getNonmemberConnect($cid, $addWhere = '', $mode = "", $orderby = '', $start = '', $limit = '') {
      if($start && $limit) $addLimit = "limit $start, $limit";
      $query = "select * from exm_pay where cid = '$cid' and mid = '' and paystep >= 1 $addWhere $orderby $addLimit";

      if($mode){
         $res = $this->db->query($query);
         return $this->db->count_($res);
      } else
         return $this->db->listarray($query);
   }

   function getRefundData($cid, $addWhere = '', $limit = ''){
      $query = "select b.*,a.*  from exm_refund a
                  inner join exm_pay b on a.payno = b.payno
                  where b.cid = '$cid' $addWhere $limit";

      return $this->db->listArray($query);
   }

   function getEditList($cid, $addWhere = '', $limit = ''){
      $query = "select b.name,c.goodsnm,d.*,
         #if(d.podsno < 1 or d.podsno = '' or d.podsno is null,c.podsno,d.podsno) podsno,a.* 
            storageid,a.goodsno,a.optno,addoptno,a.cid,a.mid,a.editkey,a.updatedt,a.state,a.title,a.est_order_data,a.est_order_option_desc,a.est_file_down_full_path,
            a.est_order_type,a.est_cost,a.est_supply,a.est_price,a.est_rid,a.est_goodsnm,a.est_fullpost,a.est_pods_version,a._hide,a._hidelog,
            ifnull(a.pods_use,c.pods_use) pods_use,
            ifnull(a.podsno,c.podsno) podsno,
            ifnull(a.podskind,c.podskind) podskind,
            a.catno         
         from exm_edit a
         left join exm_member b on a.cid = b.cid and b.mid = a.mid
         left join exm_goods c on c.goodsno = a.goodsno
         left join exm_goods_opt d on d.goodsno = a.goodsno and d.optno = a.optno
         left join exm_goods_link e on e.cid = a.cid and e.goodsno = a.goodsno
         where a.cid = '$cid' and length(storageid)=22 $addWhere group by storageid order by a.updatedt desc $limit
       ";
      return $this->db->listArray($query);
   }

   function getEditListCnt($cid, $addWhere){
      list($cnt) = $this->db->fetch("select count(c.cid) as cnt
                  from (
                  select (a.cid) from exm_edit a
                           left join exm_member b on a.cid = b.cid and b.mid = a.mid
                           left join exm_goods c on c.goodsno = a.goodsno
                           left join exm_goods_opt d on d.goodsno = a.goodsno and d.optno = a.optno
                           left join exm_goods_link e on e.cid = a.cid and e.goodsno = a.goodsno
                           where a.cid = '$cid' and length(storageid)=22 $addWhere group by storageid order by a.updatedt desc
                ) as c",1);

      return $cnt;
   }

   //비회원 주문조회
   function getNomemberOrder($cid, $payno, $email) {
   	  list($m_payno) = $this->db->fetch("select payno from exm_pay where cid='$cid' and payno='$payno' and orderer_email='$email'", 1);
   	  return $m_payno;
   }

   //비회원 견적의뢰 주문조회
   function getNomemberExtraOrder($cid, $order_name, $order_mobile) {
   	  $sql = "select * from tb_extra_cart where cid='$cid' and order_name='$order_name' and order_mobile='$order_mobile'";
   	  return $this->db->listArray($sql);
   }

   //1:1문의에 출력할 주문번호조회
   function getMycsPaynoList($cid, $mid) {
   	  $sql = "select a.payno from exm_pay a
   	  		 inner join exm_ord b on b.payno = a.payno
   	  		 inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno
   	  		 where a.cid = '$cid' and a.mid = '$mid' and (c.visible_flag = 'Y' or c.visible_flag = '') and c.itemstep not in (-1,0)
			 group by a.payno
   	  		 order by a.payno desc";
   	  return $this->db->listArray($sql);
   }

   //미리보기 경로 추가 및 수정
   function setEditorPreviewLink($storageid, $preview_link) {
   	  $sql = "insert into tb_editor_ext_data set
   	  			storage_id = '$storageid',
   	  			preview_link = '$preview_link'
   	  		on duplicate key update
   	  			preview_link = '$preview_link'";
	  $this->db->query($sql);
   }

   //재주문할 주문조회
   function getReOrderList($payno, $storageid) {
   	  $sql = "select storageid,b.podskind,a.*,b.* from exm_ord_item a 
   	  		left join exm_goods b on a.goodsno = b.goodsno 
   	  		where payno = '$payno' and storageid = '$storageid'";
	  return $this->db->listArray($sql);
   }

   //편집기에서 전달받은 json데이터 조회
   function getEditorJsonData($storageid) {
   	  list($editor_return_json) = $this->db->fetch("select editor_return_json from tb_editor_ext_data where storage_id = '$storageid'", 1);
   	  return $editor_return_json;
   }

   //주문관련 데이터 조회
   function getOrderInfo($cid, $tableName, $addWhere='') {
   	  $sql = "select * from $tableName $addWhere";
   	  return $this->db->fetch($sql);
   }

   //주문상세페이지 데이터 리스트
   function getOrderViewList($payno, $ordno) {
   	  $sql = "select a.*, b.paymethod, c.goods_group_code from exm_ord_item a
   	  		inner join exm_pay b on a.payno = b.payno
   	  		left join exm_goods c on a.goodsno = c.goodsno
   	  		where a.payno = '$payno' and a.ordno = '$ordno'";
	  return $this->db->listArray($sql);
   }

   //편집기에서 전달받은 미리보기 경로 조회
   function getEditorPreviewLink($storageid) {
   	  list($preview_link) = $this->db->fetch("select preview_link from tb_editor_ext_data where storage_id = '$storageid'", 1);
   	  return $preview_link;
   }

   //후기에 출력할 주문데이터 조회
   function getOrderDataInfo($cid, $mid, $payno, $ordno, $ordseq) {
   	  $sql = "select a.*,b.ordno,c.ordseq,c.goodsno,c.goodsnm,c.catno,(select group_concat(catnm separator '>') from exm_category where cid=a.cid and (catno=left(c.catno,3) or catno=left(c.catno,6) or catno=left(c.catno,9) or catno=left(c.catno,12))) as catnm 
   	  	from exm_pay a 
		inner join exm_ord b on b.payno = a.payno 
		inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno 
		where a.cid = '$cid' and a.mid = '$mid' and a.payno = '$payno' and b.ordno = '$ordno' and c.ordseq = '$ordseq'";
	  return $this->db->fetch($sql);
   }

   //보관함코드로 주문정보 조회
   function getOrdItemForStorageid($storageid) {
   	  $sql = "select * from exm_ord_item where storageid = '$storageid'";
	  return $this->db->fetch($sql);
   }

   //주문리스트
   function getOrdItemInfoList($cid, $addWhere='', $orderby='', $limit='', $bQuery=false) {
   	  $sql = "select * from exm_pay a
   	  	inner join exm_ord b on a.payno=b.payno
   	  	inner join exm_ord_item c on a.payno=c.payno and b.ordno=c.ordno 
   	  	$addWhere $orderby $limit";

   	  if ($bQuery) return $sql;
	  else return $this->db->listArray($sql);
   }

   //A관리자 주문리스트 / 20180208 / kdk / 배송비 금액 exm_pay필드에서 가지고 오기.20220414 standi
   function getOrderList($cid, $addWhere='', $limit='', $bCount=false, $bQuery=false) {
        if($bCount) {
            $sql = "select a.payno from exm_pay a
                inner join exm_ord b on a.payno = b.payno
                inner join exm_ord_item c on a.payno = c.payno and b.ordno = c.ordno
                where a.cid = '$cid' $addWhere group by a.payno";
        }
        else {
            $sql = "select *,a.payprice as totalprice,a.shipprice as payshipprice from exm_pay a
                inner join exm_ord b on a.payno = b.payno
                inner join exm_ord_item c on a.payno = c.payno and b.ordno = c.ordno
                where a.cid = '$cid' $addWhere group by a.payno order by orddt desc $limit";
        }

        //debug($sql);
        if ($bQuery) return $sql;
        else {
            if ($bCount) return $this->db->listArray($sql,1);
            else  return $this->db->listArray($sql);
        }
   }


   function getBasicAddress($cid, $mid){
      $sql = "select * from exm_address where cid = '$cid' and mid = '$mid' and use_check = 'Y'";
      return $this->db->fetch($sql);
   }

   function getOrdList($payno) {
   		$sql = "select * from exm_ord where payno = '$payno'";
		return $this->db->listArray($sql);
   }

   //주문 취소일자 저장			20180904		chunter
   function setUpdateCancelDate($payno)
   {
   		$sql = "update exm_pay set canceldt = now() where payno = '$payno'";
			$this->db->query($sql);
   }

   //배송정보만 수정
   function setPayDeliveryInfo($payno, $receiver_name, $receiver_mobile, $receiver_zipcode, $receiver_addr, $receiver_addr_sub, $request) {
   	  $sql = "update exm_pay set 
   	  			receiver_name = '$receiver_name',
   	  			receiver_mobile = '$receiver_mobile',
   	  			receiver_zipcode = '$receiver_zipcode',
   	  			receiver_addr = '$receiver_addr',
   	  			receiver_addr_sub = '$receiver_addr_sub',
   	  			request = '$request'
   	  		  where payno = '$payno'";

   	  $this->db->query($sql);
   }

   function getChkPayno($cid, $mid, $payno) {
   	  list($chkVal) = $this->db->fetch("select payno from exm_pay where cid = '$cid' and mid = '$mid' and payno = '$payno'", 1);

   	  return $chkVal;
   }

   //20181114 / minks / 가상계좌, 계좌이체, 무통장으로 주문한 주문번호 조회
   //20210419 / jtkim / not in 조건 추가
   function getDocumentPayno($cid, $mid, $notInPayno = '') {
   	  $sql = "select payno from exm_pay 
			where cid = '$cid' 
		  	and mid = '$mid' 
		  	and paystep > 0 
	    	and paymethod in ('v','o','b') 
	    	$notInPayno
			order by payno desc";
	  return $this->db->listArray($sql);
   }

   //20181114 / minks / 서류발급신청 리스트
   function getDocumentList($cid, $addWhere = '', $addQuery = '', $bQuery=false) {
   	  $sql = "select a.*,b.name from md_document_info a 
   	  		left join exm_member b on a.cid = b.cid and a.mid = b.mid 
   	  		where a.cid = '$cid' $addWhere $addQuery";

   	  if ($bQuery) return $sql;
   	  return $this->db->listArray($sql);
   }

   //20181114 / minks / 서류발급신청 리스트 총개수
   function getDocumentListCnt($cid, $addWhere = '') {
   	  list($totCnt) = $this->db->fetch("select count(*) as cnt from md_document_info a 
   	  		left join exm_member b on a.cid = b.cid and a.mid = b.mid 
   	  		where a.cid = '$cid' $addWhere", 1);

   	  return $totCnt;
   }

   //20191217 / kkwon / 서류발급 완료 처리
   function setUpdateDocumentInfo($cid, $addWhere = '') {
		if(!$addWhere||!$cid) return false;

		$sql = "update md_document_info set state='1', updatedt=now() where cid='$cid' and state='0' and no in($addWhere)";
		$this->db->query($sql);
		return true;
   }

   //20181119 / minks / 대량구매문의 리스트
   function getBigorderList($cid, $addWhere = '', $addQuery = '') {
   	  $sql = "select a.* from exm_request a where a.cid = '$cid' $addWhere $addQuery";

	  return $this->db->listArray($sql);
   }

   //20181119 / minks / 대량구매문의 리스트 총개수
   function getBigorderListCnt($cid, $addWhere = '') {
   	  list($totCnt) = $this->db->fetch("select count(*) as cnt from exm_request a where a.cid = '$cid' $addWhere", 1);

	  return $totCnt;
   }

   //20181119 / minks / 대량구매문의 정보
   function getBigorderInfo($cid, $no) {
   	  $sql = "select * from exm_request where cid = '$cid' and no = '$no'";

   	  return $this->db->fetch($sql);
   }

   //20190125 / minks / 주문취소시 환불정보 관리자메모에 저장
   function setPayRefundInfo($cid, $payno, $memo) {
   	  $sql = "update exm_pay set memo = CONCAT_WS('\\n',memo,'$memo') where cid = '$cid' and payno = '$payno'";

   	  $this->db->query($sql);
   }

   //20190128 / minks / 주문번호로 itemstep 조회 (출력 우선순위 : 1,91>2,92>3>4>5>-9,-90>11>19)
   function getPayItemstep($cid, $payno) {
   	  $itemstep = "";

   	  $sql = "select itemstep from exm_ord_item where payno = '$payno' and itemstep not in ('0','-1') group by itemstep";
	  $itemstepArr = $this->db->listArray($sql);

	  foreach ($itemstepArr as $k=>$v) {
	  	 switch ($v[itemstep]) {
		 	case "1":
			case "91":
				$itemstep = $v[itemstep];
				break;
			case "2":
			case "92":
				if ($itemstep == "" || in_array($itemstep, array("3","4","5","-9","-90","11","19"))) $itemstep = $v[itemstep];
				break;
			case "3":
				if ($itemstep == "" || in_array($itemstep, array("4","5","-9","-90","11","19"))) $itemstep = $v[itemstep];
				break;
			case "4":
				if ($itemstep == "" || in_array($itemstep, array("5","-9","-90","11","19"))) $itemstep = $v[itemstep];
				break;
			case "5":
				if ($itemstep == "" || in_array($itemstep, array("-9","-90","11","19"))) $itemstep = $v[itemstep];
				break;
			case "-9":
			case "-90":
				if ($itemstep == "" || in_array($itemstep, array("11","19"))) $itemstep = $v[itemstep];
				break;
			case "11":
				if ($itemstep == "" || in_array($itemstep, array("19"))) $itemstep = $v[itemstep];
				break;
			case "19":
				if ($itemstep == "") $itemstep = $v[itemstep];
				break;
	  	 }
	  }

	  return $itemstep;
   }
}
?>
