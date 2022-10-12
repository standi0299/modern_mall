<?
/*
* @date : 201803231
* @author : chunter
* @brief : 회원 가입시 쿠폰 자동 발행 처리. 회원가입 발행 쿠폰이 신규 출시.
* @desc : memberRegistCouponSend()
*/
?>
<?php
  
   //DB를 사용하는 함수들 모음. (일반 스크립트 함수는 포함되지 않는다)

   //일반 회원 로그인 처리.(member/indb.php 에서 옮겨옴)			20160824		chunter
   function memberLoginProc($mid, $password, $mobile_type, $burl, $passwdCheckFlag = 'Y', $bEncodeFlag = 'N')
   {
      global $cid;
		$m_member =  new M_member();
		//그룹이 선택되지 않은 회원이 로그인시 해당 그룹 데이터가 없기때문에
		//cid값이 null로 처리되어서 select 값을 지정해줌 / 14.11.27 / kjm
		
		if($bEncodeFlag == "Y") {
   		$mid = base64_decode($mid);
         $password = base64_decode($password);
      }
		$data = $m_member->getInfoWithGrp($cid, $mid);

		### 아이디, 비번 유효성 체크
		if ($mobile_type == "Y") {
			if (!$data) { echo _("아이디 또는 비밀번호가 일치하지 않습니다."); exit; }
			
			if ($passwdCheckFlag == "Y")
			{
			   $getEncodePassword = passwordCommonEncode($password);
				if ($data[password]!=$getEncodePassword) { echo _("아이디 또는 비밀번호가 일치하지 않습니다."); exit; }
			}
			if ($data[state]==1) { echo _("미승인 회원아이디 입니다."); exit; }
			if ($data[state]==2) { echo _("차단된 회원아이디 입니다."); exit; }
		} else {
			if (!$data) msg(_("존재하지 않는 아이디입니다."),$burl);
			
			if ($passwdCheckFlag == "Y") {
			   $getEncodePassword = passwordCommonEncode($password);
				if ($data[password]!=$getEncodePassword) msg(_("아이디 또는 비밀번호가 일치하지 않습니다."),$burl);
			}
			if ($data[state]==1) msg(_("미승인 회원아이디 입니다."),$burl);
			if ($data[state]==2) msg(_("차단된 회원아이디 입니다."),$burl);
		}
		$data[sub_mid] = "";
		$data[sub_mid_name] = "";
		
		return $data;
	}



   function getAllGoodsList()
   {
      global $db,$cfg,$cid,$sess;
      $loop = array();
      
      $where = ($sess[bid]) ? "(bid.bid is null or bid.bid = '$sess[bid]')":"bid.bid is null";  
      $where .= " and b.nodp = 0";
      $where .= " and state < 2";
  
      $query = "select a.*, c.* from
                  exm_goods a
                  inner join exm_goods_cid b on b.cid = '$cid' and b.goodsno = a.goodsno
                  inner join exm_category_link c on c.cid = '$cid' and c.goodsno = a.goodsno
                  left join exm_goods_bid bid on bid.cid = '$cid' and bid.goodsno = a.goodsno
               where
                  $where
               order by c.catno";
      //debug($query);
      //exit;
      $loop = $db->listArray($query);
      /*
      while ($data=$db->fetch($res)){
    
      if(strlen($data[goodsnm]) > 20){      
         $data[goodsnm] = mb_substr($data[goodsnm], 0, 13, "UTF-8");
      }
      else{
         $data[goodsnm] = $data[goodsnm];
      }
      
         $loop[] = $data;
      }
      */
      return $loop;
   }
  
  /* 카테고리명 가져오기 */
  function getCatnm($catno,$arr=0){

    global $db,$cid;
    $r_catnm = array();
    for ($i=0;$i<strlen($catno);$i=$i+3){
        $r_catno = substr($catno,0,$i+3);
        $data = $db->fetch("select catnm from exm_category where cid = '$cid' and catno = '$r_catno'");
        $r_catnm[] = $data[catnm];
    }
    if (!$arr) $r_catnm = implode(" > ",$r_catnm);
    return $r_catnm;
  }

  /* 환경변수 가져오기 */
  function getCfg($cfg="", $cfg_group=""){

    global $db,$cid;
    if ($cfg){
        list($ret) = $db->fetch("select value from exm_config where cid = '$cid' and config = '$cfg'",1);
    } else {
    	if ($cfg_group)
        $query = "select * from exm_config where cid = '$cid' and config_group = '$cfg_group'";
			else
				//$query = "select * from exm_config where cid = '$cid' and (config_group is NULL or config_group = '')";	
				$query = "select * from exm_config where cid = '$cid' and config <> 'agreement' and config <> 'agreement2' and config <> 'policy' and config <> 'nonmember_agreement' and (config_group is NULL or config_group = '')";
        $res = $db->query($query);
        while ($data = $db->fetch($res)){
            if ($data[config]=="pg") $data[value] = unserialize($data[value]);
            if ($data[config]=="paymethod_info"){
                $data[value] = unserialize($data[value]);
                $data[value] = array_map("base64_decode",$data[value]);
                $data[value] = array_map("stripslashes",$data[value]);
            }
				if ($data[config]=="layout") $data[value] = unserialize($data[value]);

            if ($data[config]=="shipconfig") $data[value] = unserialize($data[value]);
            if ($data[config]=="member_system") $data[value] = unserialize($data[value]);
            if ($data[config]=="cart_system") $data[value] = unserialize($data[value]);
            if ($data[config]=="estimate") $data[value] = unserialize($data[value]);
            if ($data[config]=="design") $data[value] = unserialize($data[value]);
            if ($data[config]=="banner_hidden") $data[value] = unserialize($data[value]);
            if ($data[config]=="service_menu") $data[value] = unserialize($data[value]);
            $ret[$data[config]] =  $data[value];
        }
    }
    return $ret;
  }

  /* 센터 환경변수 가져오기 */
  function getCfgCenter($cfg=""){

    global $db,$cid;
    list($center_cid) = $db->fetch("select value from exm_config where config = 'center_cid'",1);
    
    if ($cfg){
        list($ret) = $db->fetch("select value from exm_config where cid = '$center_cid' and config = '$cfg'",1);
    } else {
        $query = "select * from exm_config where cid = '$center_cid'";

        $res = $db->query($query);
        while ($data = $db->fetch($res)){
            $ret[$data[config]] =  $data[value];
        }
    }
    $ret[cid] = $center_cid;
    
    return $ret;
   }
        
   function getAuthMember($admin=0)
   {
      if ($admin) $sess = md5_decode($_COOKIE[sess_admin]);
      else $sess = md5_decode($_COOKIE[sess]);
      if ($sess){
        list($sess[bid],$sess[grpno]) = $GLOBALS[db]->fetch("select bid,grpno from exm_member where cid = '$GLOBALS[cid]' and mid = '$sess[mid]'",1);
        
        $tmp = md5_decode($_COOKIE[member]);
        if ($tmp) $sess = array_merge($tmp,$sess);
      }
      //debug($sess);
      return $sess;
   }
   
   ### 관리자 접속로그
   function _log_admin($state, $mode, $cid) 
   {
      $password = passwordCommonEncode($_POST[password]);
      $query = "insert into $mode set
          cid      = '$cid',
          mid      = '$_POST[mid]',
          password = '$password',
          conndt   = now(),
          ip       = INET_ATON('$_SERVER[REMOTE_ADDR]'),
          state    = '$state'
        ";
      $GLOBALS[db]->query($query);
   }
    
    
    
    //2014.04.01 / minks / 주문 상태 개수 가져오기와 링크 걸기
    //20141125 / minks / 정산관리자 일련번호 가져오기 추가
    function get_order_state_count($itemstep, $mid)
    {
      global $cid, $db, $sess_admin;
      
      if($itemstep == "3")
      {
        $sql = "select count(*) as count from exm_pay a 
            inner join exm_ord_item b on b.payno = a.payno
            inner join exm_member c on c.cid = a.cid and c.mid = a.mid where a.cid = '$cid' and (b.itemstep = '$itemstep' or b.itemstep = '92' or b.itemstep = '2' or b.itemstep = '4')";
      }
      else if($itemstep != "3")
      {
        $sql = "select count(*) as count from exm_pay a 
            inner join exm_ord_item b on b.payno = a.payno
            inner join exm_member c on c.cid = a.cid and c.mid = a.mid where a.cid = '$cid' and b.itemstep = '$itemstep'";
      }

      if ($mid)
      {
         $sql .= " and a.mid='$mid' and (b.visible_flag = 'Y' or b.visible_flag = '')";
         $order_state = $db->fetch($sql);
         $result = "$order_state[count]";
      } 
      else 
      {
         if($sess_admin[mngno])
      {
         $sql .= " and c.manager_no='$sess_admin[mngno]'";
      }
         $order_state = $db->fetch($sql);
         $result = "$order_state[count]";
      }
      //debug($sql);
      return $result;
   }
    
    
    //2014.05.26 / minks / 유치원 메인 페이지 상단에 뿌릴 데이터  
    function get_name_memo($type, $mid) {
      global $cid, $db;
      $sql = "select name, etc1 from exm_member where cid='$cid' and mid='$mid'";
      
      $name_memo = $db -> fetch($sql);
      if($type == "name")
      {
        $result = "$name_memo[name]";
      }
      else
      {
        $result = "$name_memo[etc1]"; 
      }
      
      return $result;
    }
    
    
    //회원 가입 쿠폰 발행			20180323		chunter
    function memberRegistCouponSend($mid) 
    {
      global $cid;
						
			$whereArr = array("cid" => "$cid", "coupon_issue_system" => "mem_register");
			$cList = SelectListTable("exm_coupon", "*", $whereArr);
			//debug($cList);
			if (is_array($cList))
			{
				$m_etc = new M_etc();
				foreach ($cList as $key => $value) 
				{						
					//유효성 체크	
					if ($value[coupon_issue_unlimit] == 0 && ($value[coupon_issue_sdate] > date("Y-m-d") || $value[coupon_issue_edate] < date("Y-m-d"))) 
					{
						continue;			//일자 지났음
					}
			
					if ($value[coupon_issue_ea_limit] == 1) 
					{
						$addWhere2 = "where cid = '$cid' and coupon_code = '{$value[coupon_code]}'";
						$chk2 = $m_etc->getSoldCouponCnt($cid, $addWhere2);				
						if ($chk2 >= $value[coupon_issue_ea]) {
							continue;			//모두 소진
						}
					}
					
					$cData = $m_etc->getCouponSetInfo($cid, "where cid='$cid' and coupon_code = '{$value[coupon_code]}' and mid='$mid'");					
					if (!$cData) 
					{
						$addColumn = "set cid='$cid', coupon_code='{$value[coupon_code]}', mid='$mid', coupon_use=0, coupon_setdt=NOW()";
						$m_etc->setCouponSetInfo("", $addColumn);
					}
				}
			}
    }
  
?>