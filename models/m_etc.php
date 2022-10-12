<?php
/**
* ETC model
* 2015.02.13 by chunter
*/

class M_etc{
    var $db;
    function M_etc() {
        $this->db = $GLOBALS[db];
    }

    //모던->상품->추가옵션->이미지링크 정보 리스트
    function getAddOptImgList($cid, $goodsno) {
        $sql = "select * from mb_option_image_info where cid = '$cid' and goodsno='$goodsno'";
        //echo $sql;
        return $this -> db -> listArray($sql);
    }

    function setAddOptImgDelete($cid, $goodsno) {
        $sql = "delete from mb_option_image_info where cid = '$cid' and goodsno='$goodsno'";
        //echo $sql;
        return $this -> db -> listArray($sql);
    }

    //쿠폰관리>쿠폰등록리스트
    function getCouponRegistList($cid, $code, $name, $issue_code, $issue_yn, $regdt1, $regdt2, $usedt1, $usedt2, $limit, $bQuery = false)
    {
        $sql = "select * from 
        exm_coupon a
        inner join exm_coupon_issue b on a.cid = b.cid and a.coupon_code = b.coupon_code
        ";

        $where = " where a.cid = '$cid'";

        if ($code) $where .= " and a.coupon_code like '%$code%'";
        if ($name) $where .= " and coupon_name like '%$name%'";
        if ($issue_code) $where .= " and coupon_issue_code like '%$issue_code%'";

        if (is_numeric($coupon_issue_yn)) $where .= " and coupon_issue_yn = '$use'";

        if ($regdt1) $where .= " and b.coupon_regdt >= '{$regdt1}'";
        if ($regdt2) $where .= " and b.coupon_regdt <= adddate('{$regdt2}', interval 1 day)";
        if ($usedt1) $where .= " and b.coupon_issuedt >= '{$usedt1}'";
        if ($usedt2) $where .= " and b.coupon_issuedt <= adddate('{$usedt2}', interval 1 day)";

        $order = " order by b.coupon_regdt desc $limit";
        //debug($sql.$where.$order);

        if($bQuery) return $sql.$where.$order;
        else return $this->db->listArray($sql.$where.$order);
    }

    //쿠폰관리>회원별발행리스트
    function getCouponMemberList($cid, $kind, $code, $name, $use, $mid, $setdt1, $setdt2, $usedt1, $usedt2, $limit, $bQuery = false)
    {
        $sql = "select * from 
        exm_coupon a
        inner join exm_coupon_set b on a.cid = b.cid and a.coupon_code = b.coupon_code
        ";

        $where = " where a.cid = '$cid'";
        $where .= " and a.coupon_kind = '$kind'";

        if ($code) $where .= " and a.coupon_code like '%$code%'";
        if ($name) $where .= " and a.coupon_name like '%$name%'";

        if (is_numeric($use)) $where .= " and b.coupon_use = '$use'";

        if ($mid) $where .= " and b.mid like '%$mid%'";

        if ($setdt1) $where .= " and b.coupon_setdt >= '{$setdt1}'";
        if ($setdt2) $where .= " and b.coupon_setdt <= adddate('{$setdt2}', interval 1 day)";
        if ($usedt1) $where .= " and b.coupon_usedt >= '{$usedt1}'";
        if ($usedt2) $where .= " and b.coupon_usedt <= adddate('{$usedt2}', interval 1 day)";

        $order = " order by b.coupon_setdt desc $limit";
        //debug($sql.$where.$order);

        if($bQuery) return $sql.$where.$order;
        else return $this->db->listArray($sql.$where.$order);
    }

    function getAutoMsgInfo($cid, $type, $catnm)
    {
      $sql = "select * from exm_automsg where cid = '$cid' and catnm = '$catnm' and type = '$type'";
      return $this->db->fetch($sql);
    }

    function getAutoMsgList($cid, $catnm)
    {
    	$sql = "select * from exm_automsg where cid = '$cid' and catnm = '$catnm'";
    	return $this->db->listArray($sql);
    }


    /*function setCouponSetUpdate($coupon_no)
    {
        $sql = "update exm_coupon_set set
                coupon_use      = 0,
                payno           = null,
                ordno           = null,
                ordseq          = null,
                coupon_usedt    = null
            where no = '$coupon_no'";

        $this->db->query($sql);
    }*/


    function setEmailLogInsert($cid, $to, $subject, $contents, $cnt)
    {
      $sql = "insert into exm_log_email set 
        cid         = '$cid',
        `to`        = '$to',
        subject     = '$subject',
        contents    = '$contents',
        regdt       = now(),
        cnt         = '$cnt'";
      $this->db->query($sql);
			return $this->db->id;
    }

   function getReleaseInfo($rid)
   {
      $sql = "select * from exm_release where rid = '$rid'";
        return $this->db->fetch($sql);
   }

   function getReleaseList($cid)
   {
      $sql = "select * from exm_release where cid = '$cid'";
        return $this->db->listArray($sql);
   }

    //자체상품관리 추가 및 수정
    function setReleaseInfo($cid, $rid, $compnm, $nicknm, $name, $manager, $phone, $zipcode, $address, $address_sub, $shiptype, $shipprice, $shipconditional, $oshipprice, $podsid, $siteid) {
      $sql = "insert into exm_release set
            rid              = '$rid',
            compnm           = '$compnm',
            nicknm           = '$nicknm',
            name             = '$name',
            manager          = '$manager',
            phone            = '$phone',
            zipcode          = '$zipcode',
            address          = '$address',
            address_sub      = '$address_sub',
            shiptype         = '$shiptype',
            shipprice        = '$shipprice',
            shipconditional  = '$shipconditional',
            oshipprice       = '$oshipprice',
            mall_release     = 1,
            cid              = '$cid',
            podsid           = '$podsid',
            siteid           = '$siteid',
            regdt            = now()
        on duplicate key update
            compnm           = '$compnm',
            nicknm           = '$nicknm',
            name             = '$name',
            manager          = '$manager',
            phone            = '$phone',
            zipcode          = '$zipcode',
            address          = '$address',
            address_sub      = '$address_sub',
            shiptype         = '$shiptype',
            shipprice        = '$shipprice',
            shipconditional  = '$shipconditional',
            oshipprice       = '$oshipprice',
            mall_release     = 1,
            cid              = '$cid',
            podsid           = '$podsid',
            siteid           = '$siteid'";
      $this->db->query($sql);
    }

    //접속로그
    function getSiteLog($cid, $addWhere='') {
      $sql = "select * from exm_cnt $addWhere";
      return $this->db->listArray($sql);
    }

    //접속로그2
    function getSiteLog2($cid, $addColumn='', $addWhere='', $groupby='', $orderby='') {
      $sql = "select $addColumn from exm_cnt_log $addWhere $groupby $orderby";
      return $this->db->listArray($sql);
    }

    //일별판매통계
    function getSoldDay($cid, $month) {
      $sql = "select
            if (paydt is null or paydt<='0000-00-00',left(confirmdt,10),left(paydt,10)) paydt,
            count(*) paycnt,
            (select
                sum(x.ea) ea
            from
                exm_ord_item x
                inner join exm_pay y on x.payno=y.payno
            where
                y.cid='$cid'
                and (left(paydt,10)=left(a.paydt,10) or left(confirmdt,10)=left(a.confirmdt,10))
                and ((itemstep>=2 and itemstep<10) or itemstep=92)
            ) item_ea_cnt,
            sum(payprice) payprice,
            sum(shipprice) shipprice,
            (select
                sum(x.payprice) payprice
            from
                exm_ord_item x
                inner join exm_pay y on x.payno=y.payno
            where
                y.cid='$cid'
                and left(paydt,10)=left(a.paydt,10)
                and itemstep >= 11
                and itemstep <= 29) refund
        from
            exm_pay a
        where
            cid='$cid'
            and (left(paydt,7)='$month' or left(confirmdt,7)='$month')
            and ((paystep>=2 and paystep<10) or paystep=92)
        group by if (paydt is null or paydt<='0000-00-00',left(confirmdt,10),left(paydt,10))";
      return $this->db->listArray($sql);
    }

    //유입경로별 매출통계
    function getSoldRefer($cid, $month) {
      $sql = "select
            if (paydt is null or paydt<='0000-00-00',left(confirmdt,10),left(paydt,10)) paydt,
            payprice,referer
        from
            exm_pay
        where
            cid='$cid'
            and (left(paydt,7)='$month' or left(confirmdt,7)='$month')
            and ((paystep>=2 and paystep<10) or paystep=92)";
      return $this->db->listArray($sql);
    }

    //결제수단별 매출통계
    function getSoldPaymethod($cid, $startDate, $endDate) {
      $sql = "select
            if (paydt is null or paydt<='0000-00-00',left(confirmdt,10),left(paydt,10)) dt,
            paymethod,escrow,count(*) cnt,sum(payprice) payprice,sum(dc_emoney) emoney 
        from
            exm_pay
        where
            cid='$cid'
        and
            ((paystep>=2 and paystep<10) or paystep=92) and
            ((paydt>='{$startDate}' and paydt<adddate('{$endDate}',interval 1 day)) or
            (confirmdt>='{$startDate}' and confirmdt<adddate('{$endDate}',interval 1 day)))
        group by if (paydt is null or paydt<='0000-00-00',left(confirmdt,10),left(paydt,10)),paymethod,escrow";
      return $this->db->listArray($sql);
    }

    //카테고리별 매출통계
    function getSoldCategory($cid, $startDate, $endDate, $r_category=array()) {
      $sql = "select
            sum(b.payprice) payprice,left(b.catno,3) catno,left(a.paydt,10) dt
        from
            exm_pay a
            inner join exm_ord_item b on b.payno = a.payno
        where
            a.cid='$cid'
            and b.itemstep in (2,3,4,5,92)
            and a.paydt>'{$startDate}'
            and a.paydt<adddate('{$endDate}',interval 1 day)
            and left(b.catno,3) in ('".implode("','",$r_category)."')
        group by
            left(b.catno,3),left(a.paydt,10)";
      return $this->db->listArray($sql);
    }

    //주문자별 주문통계
    function getSoldOrder($cid, $addWhere='', $orderby='', $limit='') {
      $sql = "select
            left(a.paydt,10) paydt,a.payno,a.orderer_name,a.mid,
            b.ordno,b.ordseq,b.goodsnm,b.opt,b.addopt,b.addpage,b.ea,b.payprice,b.printopt
        from
            exm_pay a
            inner join exm_ord_item b on b.payno=a.payno
        $addWhere
        $orderby
        $limit";
      return $this->db->listArray($sql);
    }

    //상품별 주문통계
    function getSoldGoods($cid, $addWhere='') {
      $sql = "select
            #(select group_concat(catnm separator '>') from exm_category where cid=a.cid and (catno=left(b.catno,3) or catno=left(b.catno,6) or catno=left(b.catno,9) or catno=left(b.catno,12))) as catnm,
            if ( b.catno='', 
                (select group_concat(catnm separator '>') from exm_category where cid=a.cid and catno=(select catno from exm_goods_link where cid='service' and goodsno=b.goodsno limit 1)) 
                ,
                (select group_concat(catnm separator '>') from exm_category where cid=a.cid and (catno=left(b.catno,3) or catno=left(b.catno,6) or catno=left(b.catno,9) or catno=left(b.catno,12))) 
            ) as catnm,
            b.goodsnm,sum(b.ea) sumea,sum(b.payprice) sumprice
        from
            exm_pay a
            inner join exm_ord_item b on b.payno=a.payno
        $addWhere
        group by b.catno,b.goodsno";
      return $this->db->listArray($sql);
    }

    //인화상품별 주문통계
    function getSoldPrint($cid, $startDate, $endDate) {
      $sql = "select
            goodsno,goodsnm,printopt
        from
            exm_pay a
            inner join exm_ord_item b on b.payno=a.payno
        where
            a.cid='$cid'
            and b.itemstep in (2,3,4,5,92)
            and a.paydt>'{$startDate}'
            and a.paydt<adddate('{$endDate}',interval 1 day)
            and b.printopt!=''";
      return $this->db->listArray($sql);
    }

    //사용일기준 적립금통계
    function getSoldUseEmoney($cid, $startDate, $endDate) {
      $sql = "select 
            *,left(regdt,10) as regdt,SUM(ABS(emoney)) as emoney,count(*) as cnt 
        from 
            exm_log_emoney 
        where
            cid='$cid'
            and regdt>'{$startDate}'
            and regdt<adddate('{$endDate}',interval 1 day)
            and memo='"._("상품구입시 사용")."'
            and emoney<0
            and payno is not NULL
        group by left(regdt,10),mid
        order by regdt desc";
      return $this->db->listArray($sql);
    }

    //결제일기준 적립금통계
    function getSoldPayEmoney($cid, $startDate, $endDate) {
      $sql = "select 
            a.*,left(b.paydt,10) as paydt,SUM(ABS(a.emoney)) as emoney,count(*) as cnt 
        from 
            exm_log_emoney a
            inner join exm_pay b on b.payno=a.payno
        where
            a.cid='$cid'
            and b.paydt>'{$startDate}'
            and b.paydt<adddate('{$endDate}',interval 1 day)
            and a.memo='"._("상품구입시 사용")."'
            and a.emoney<0
            and a.payno is not NULL
        group by left(b.paydt,10),a.mid
        order by b.paydt desc";
      return $this->db->listArray($sql);
    }

    //쿠폰통계
    function getSoldCoupon($cid, $addWhere='', $orderby='', $limit='') {
      $sql = "select * from exm_coupon $addWhere $orderby $limit";
      return $this->db->listArray($sql);
    }

    //쿠폰통계(등록 및 사용수)
    function getSoldCouponCnt($cid, $addWhere='', $addSubWhere='') {
      list($m_cnt) = $this->db->fetch("select count(*) from exm_coupon_set $addWhere $addSubWhere", 1);
      return $m_cnt;
    }

    //이벤트리스트
    function getEventList($cid, $addWhere='', $orderby='', $limit='') {
      $sql = "select * from exm_event $addWhere $orderby $limit";
      return $this->db->listArray($sql);
    }

    //이벤트 추가 및 수정
    function setEventInfo($cid, $addColumn='', $addWhere='', $eventno='') {
      if ($eventno) {
        $sql = "update exm_event set $addColumn $addWhere";
      } else {
        $sql = "insert into exm_event set $addColumn";
      }
      $this->db->query($sql);
    }

    //이벤트 상품연결 삭제후 재등록
    function setEventLink($cid, $eventno, $r_goodsno=array()) {
      $sql = "delete from exm_event_link where cid = '$cid' and eventno = '$eventno'";
      $this->db->query($sql);

      if ($r_goodsno) {
        foreach ($r_goodsno as $grpno=>$v) {
            foreach ($v as $k=>$goodsno) {
                $sql2 = "
                    insert into exm_event_link set
                    cid     = '$cid',
                    goodsno = '$goodsno',
                    eventno = '$eventno',
                    grpno   = '$grpno', 
                    sort    = '$k'";
                    $this->db->query($sql2);
                }
            }
        }
    }

    //이벤트삭제
    function delEventInfo($cid, $eventno) {
      $sql = "delete from exm_event where eventno = '$eventno' and cid = '$cid'";
      $this->db->query($sql);
      $sql2 = "delete from exm_event_link where cid = '$cid' and eventno = '$eventno'";
      $this->db->query($sql2);
    }

    //이벤트정보
    function getEventInfo($cid, $eventno) {
      $sql = "select * from exm_event where cid='$cid' and eventno='$eventno'";
      return $this->db->fetch($sql);
    }

    //이벤트 코멘트리스트
    function getEventCommentList($cid, $addWhere='', $orderby='', $limit='') {
      $sql = "select a.*,b.title,(select name from exm_member where cid = '$cid' and mid = a.mid) as name 
            from exm_event_comment a 
            left join exm_event b on a.eventno = b.eventno $addWhere $orderby $limit";
      return $this->db->listArray($sql);
    }

    //적립금 지급할 코멘트리스트
    function getEventCommentEmoney($cid, $addWhere='') {
      $sql = "select a.* from exm_event_comment a 
            inner join exm_member b on a.cid = b.cid and a.mid = b.mid $addWhere";
      return $this->db->listArray($sql);
    }

    //코멘트 추가 및 수정
    function setEventComment($cid, $addColumn='', $addWhere='', $no='') {
      if ($no) {
        $sql = "update exm_event_comment $addColumn $addWhere";
      } else {
        $sql = "insert into exm_event_comment $addColumn";
      }
      $this->db->query($sql);
    }

    //이벤트 코멘트정보
    function getEventCommentInfo($cid, $no) {
      $sql = "select a.*,b.title,(select name from exm_member where cid = '$cid' and mid = a.mid) as name 
            from exm_event_comment a 
            left join exm_event b on a.eventno = b.eventno 
            where a.cid = '$cid' and a.no = '$no'";
      return $this->db->fetch($sql);
    }

    //이벤트 작성권한체크
    function getEventCommentCheck($cid, $mid, $eventno) {
      $m_chk = array();
      list($m_chk[eventno], $m_chk[emoney]) = $this->db->fetch("select eventno,emoney from exm_event_comment where cid='$cid' and mid='$mid' and eventno='$eventno'", 1);
      return $m_chk;
    }

    //이벤트 코멘트 삭제
    function delEventCommentInfo($cid, $mid, $eventno) {
      $sql = "delete from exm_event_comment where cid='$cid' and mid='$mid' and eventno='$eventno'";
      $this->db->query($sql);
    }

    //쿠폰 정보
    function getCouponInfo($cid, $coupon_code, $addWhere='') {
      $sql = "select * from exm_coupon where cid='$cid' and coupon_code='$coupon_code' $addWhere";
      return $this->db->fetch($sql);
    }

    //쿠폰발급 정보
    function getCouponSetInfo($cid, $addWhere='') {
      $sql = "select a.*,(select coupon_type from exm_coupon where cid=a.cid and coupon_code=a.coupon_code) as coupon_type from exm_coupon_set a $addWhere";
      return $this->db->fetch($sql);
    }

    //쿠폰발행 정보
    function getCouponIssueInfo($cid, $coupon_issue_code) {
      $sql = "select * from exm_coupon_issue where cid='$cid' and coupon_issue_code = '$coupon_issue_code'";
      return $this->db->fetch($sql);
    }

    //쿠폰 정보 수정
    function setCouponInfo($cid, $addColumn='', $addWhere='') {
      $sql = "update exm_coupon $addColumn $addWhere";
      $this->db->query($sql);
    }

    //쿠폰발급 정보 추가 및 수정
    function setCouponSetInfo($no, $addColumn='', $addWhere='') {
      if ($no) {
        $sql = "update exm_coupon_set $addColumn $addWhere";
      } else {
        $sql = "insert into exm_coupon_set $addColumn";
      }
      $this->db->query($sql);
    }

    //쿠폰발행 정보 수정
    function setCouponIssueInfo($cid, $addColumn='', $addWhere='') {
      $sql = "update exm_coupon_issue $addColumn $addWhere";
      $this->db->query($sql);
    }

    //쿠폰발급 정보 삭제
    function delCouponSetInfo($no) {
      $sql = "delete from exm_coupon_set where no = '$no'";
      $this->db->query($sql);
    }

    //쿠폰발행 정보 삭제
    function delCouponIssueInfo($coupon_issue_code) {
      $sql = "delete from exm_coupon_issue where coupon_issue_code = '$coupon_issue_code'";
      $this->db->query($sql);
    }

   function getGalleyData($cid, $flag = '', $addWhere = '' ,$orderby = '', $limit = ''){

      if($flag) $flagWhere = "and a.flag = '$flag'";

      $query = "select a.*, b.title, c.goodsnm, c.goodsno from md_gallery a
                inner join exm_edit b on a.storageid = b.storageid
                inner join exm_goods c on b.goodsno = c.goodsno
                where a.cid = '$cid' $flagWhere $addWhere $orderby $limit";
      $list = $this->db->listArray($query);

      return $list;
   }

   function getGalleyDataCnt($cid, $flag = '', $addWhere = '') {
   	  if ($flag) $flagWhere = "and a.flag = '$flag'";

	  $query = "select count(*) as cnt from md_gallery a
                inner join exm_edit b on a.storageid = b.storageid
                inner join exm_goods c on b.goodsno = c.goodsno
                where a.cid = '$cid' $flagWhere $addWhere";

   	  list($cnt) = $this->db->fetch($query, 1);

	  return $cnt;
   }

   function setGalleryViewCnt($storageid){
      $this->db->query("update md_gallery set view = view+1 where storageid = '$storageid'");
   }

   function getGalleryData($storageid){
      $sql = "select a.*, b.goodsnm, c.view, c.regdt as galley_regdt from exm_edit a
               inner join exm_goods b on a.goodsno = b.goodsno
               inner join md_gallery c on a.storageid = c.storageid
              where a.storageid = '$storageid'";
      $data = $this->db->fetch($sql);
      return $data;
   }

   function getGalleryComment($cid, $storageid){
      $sql = "select * from md_gallery_comment where cid = '$cid' and storageid = '$storageid'";
      $comment = $this->db->listArray($sql);
      return $comment;
   }

   function getJjimStorageid($cid, $mid, $storageid){
      $sql = "select storageid from md_jjim where storageid = '$storageid' and cid = '$cid' and mid = '$mid'";
      list($data) = $this->db->fetch($sql,1);
      return $data;
   }

   function setGalleryComment($cid, $mid, $storageid, $comment){
      $sql = "insert into md_gallery_comment set
               cid = '$cid',
               mid = '$sess[mid]',
               storageid = '$_POST[storageid]',
               regdt = now(),
               comment = '$_POST[comment]'
             ";
      $db->query($sql);

      $sql = "update md_gallery set comment = comment +1 where storageid = '$_POST[storageid]'";
      $db->query($sql);
   }

   //베스트 편집 정보
   function getGalleryBestInfo($cid) {
   	  $sql = "select a.*, b.title, c.goodsnm, c.goodsno from md_gallery a
                inner join exm_edit b on a.storageid = b.storageid
                inner join exm_goods c on b.goodsno = c.goodsno
                where a.cid = '$cid' and a.flag = 'best' and a.main_flag = 'Y'";

   	  return $this->db->fetch($sql);
   }

}
?>
