<?php
/**
* config model
* 2015.02.11 by chunter
*/
     
class M_config{
    var $db;
    function M_config() {
        $this->db = $GLOBALS[db];
    }
    
    
    function getConfigInfo($cid, $config_code = '')
    {
      if ($config_code)
      {
        $sql = "select * from exm_config where cid = '$cid' and config = '$config_code'";
        return $this->db->fetch($sql);
      } else {
        $sql = "select * from exm_config where cid = '$cid'";
        return $this->db->listArray($sql);
      }
    }
    
    //config정보 추가 및 수정
    function setConfigInfo($cid, $config_code, $config_value, $config_group = "")
    {
      $sql = "insert into exm_config set cid='$cid', config = '$config_code', value='$config_value', config_group = '$config_group' on duplicate key update value='$config_value'";
      $this->db->query($sql);
    }
    
    
    function getCenterConfigInfo($config_code)
    {
      $sql = "select * from exm_config where config = '$config_code'";
      return $this->db->fetch($sql);
    }

    function getShipCompListWithUser($cid)
    {
      $sql = "select a.*, b.isuse from exm_shipcomp a inner join exm_shipcomp_useid b on a.shipno = b.shipno where b.useid = '$cid' and b.isuse = 1";                    
      return $this->db->listArray($sql);
    }
    
    
    function getShipCompList($cid)
    {
      $sql = "select * from exm_shipcomp where cid = '$cid' and isuse = 1";                    
      return $this->db->listArray($sql);
    }
    
    
    function getBankList($cid)
    {
      $sql = "select * from exm_bank where cid = '$cid'";                    
      return $this->db->listArray($sql);
    }
    
    function getBrandList()
    {
      $sql = "select * from exm_brand order by trim(brandnm)";                    
      return $this->db->listArray($sql);
    }
    
    
    //20150224    chunter
    function getBannerInfo($cid, $banner_code, $skin_name)
    {      
      $sql = "select * from exm_banner where cid = '$cid' and skin = '$skin_name' and code = '$banner_code'";
      return $this->db->fetch($sql);      
    }
    
    //계좌정보
    function getBankInfo($cid, $bankno='', $addWhere='', $orderby='', $limit='')
    {
      if ($bankno) {
      	$sql = "select * from exm_bank $addWhere";                    
      	return $this->db->fetch($sql);
      } else {
      	$sql = "select * from exm_bank $addWhere $orderby $limit";                    
      	return $this->db->listArray($sql);
      }
    }
    
	//계좌정보 추가 및 수정
	function setBankInfo($cid, $bankinfo, $bankno='') {
	  if ($bankno) {
	  	$sql = "update exm_bank set bankinfo='$bankinfo' where cid='$cid' and bankno='$bankno'";
	  } else {
	  	$sql = "insert into exm_bank set cid='$cid', bankinfo='$bankinfo'";
	  }
      $this->db->query($sql);
	}
	
	//계좌정보 삭제
	function delBankInfo($cid, $bankno) {
	  $sql = "delete from exm_bank where cid='$cid' and bankno='$bankno'";
	  $this->db->query($sql);
	}
	
	//택배정보
	function getShipCompInfo($cid, $shipno='', $addWhere='', $orderby='', $limit='') {
	  if ($shipno) {
	  	$sql = "select * from exm_shipcomp $addWhere";
      	return $this->db->fetch($sql);  
	  } else {
	  	$sql = "select * from exm_shipcomp $addWhere $orderby $limit"; 
		return $this->db->listArray($sql);
	  }
	}
	
	//택배정보 추가 및 수정
	function setShipCompInfo($cid, $compnm, $url, $isuse, $shipno='') {
	  if ($shipno) {
	  	$sql = "update exm_shipcomp set compnm='$compnm', url='$url', isuse='$isuse' where cid='$cid' and shipno='$shipno'";
	  } else {
	  	$sql = "insert into exm_shipcomp set cid='$cid', compnm='$compnm', url='$url', isuse='$isuse'";
	  }
	  $this->db->query($sql);
	}
	
	//택배정보 삭제
	function delShipCompInfo($cid, $shipno) {
	  $sql = "delete from exm_shipcomp where cid='$cid' and shipno='$shipno'";
	  $this->db->query($sql);
	}
	
	//팝업정보
	function getPopupInfo($cid, $popupno='', $addWhere='', $orderby='', $limit='') {
	  if ($popupno) {
	  	$sql = "select * from exm_popup $addWhere";                    
      	return $this->db->fetch($sql);
	  } else {
	  	$sql = "select * from exm_popup $addWhere $orderby $limit";              
      	return $this->db->listArray($sql);
	  }
	}
	
	//팝업번호
	function getPopupNo($cid) {
	  list($m_popupno) = $this->db->fetch("select max(popupno) from exm_popup where cid = '$cid'", 1);                    
      return $m_popupno;
	}
	
	//팝업 추가 및 수정
	function setPopupInfo($cid, $popupno, $title, $state, $sdt, $edt, $width, $height, $top, $left, $content, $skintype) {
	  $sql = "insert into exm_popup set
	  		cid	  	 = '$cid',
       		popupno  = '$popupno',
       		title	 = '$title',
       		state	 = '$state',
       		sdt	  	 = '$sdt',
       		edt	  	 = '$edt',
       		width	 = '$width',
       	 	height   = '$height',
       		`top`	 = '$top',
       		`left`   = '$left',
       		content  = '$content',
       		skintype = '$skintype'
       on duplicate key update
       		title	 = '$title',
       		state	 = '$state',
       		sdt	  	 = '$sdt',
       		edt	  	 = '$edt',
       		width	 = '$width',
       		height   = '$height',
       		`top`	 = '$top',
       		`left`   = '$left',
       		content  = '$content',
       		skintype = '$skintype'";
	   $this->db->query($sql);
	}
	
	//팝업정보 삭제
	function delPopupInfo($cid, $popupno) {
	  $sql = "delete from exm_popup where cid='$cid' and popupno='$popupno'";
	  $this->db->query($sql);
	}
	
	//관리자정보
	function getAdminInfo($cid, $mid='', $addWhere='', $orderby='', $limit='') {
	  if ($mid) {
	  	$sql = "select * from exm_admin $addWhere";
      	return $this->db->fetch($sql);
	  } else {
	  	$sql = "select * from exm_admin $addWhere $orderby $limit";
      	return $this->db->listArray($sql);
	  }	  
	}
	  
	//관리자 추가 및 수정
	function setAdminInfo($mode, $addColumn='', $addWhere='') {
	  if ($mode == "modify") {
	  	$sql = "update exm_admin $addColumn $addWhere";
	  } else if ($mode == "write") {
	  	$sql = "insert into exm_admin $addColumn";
	  }
	  $this->db->query($sql);
	}
	  
	//관리자정보 삭제
	function delAdminInfo($cid, $mid) {
	  $sql = "delete from exm_admin where cid='$cid' and mid='$mid'";
	  $this->db->query($sql);
	}
	
	//관리자로그
	function getAdminLog($cid, $addWhere='', $orderby='', $limit='') {
	  $sql = "select *,INET_NTOA(ip) as ip from exm_log_admin a inner join exm_admin b on a.cid=b.cid and a.mid=b.mid $addWhere $orderby $limit";
	  return $this->db->listArray($sql);
	}


    
    //추가페이지정보
    function getAddpageInfo($cid, $type='', $addWhere='', $limit='') {
      if ($type) {
        $sql = "select * from tb_kids_skin $addWhere";
        return $this->db->fetch($sql);
      } else {
        $sql = "select * from tb_kids_skin $addWhere $limit";
        return $this->db->listArray($sql);
      }
    }

    //추가페이지 추가 및 수정
    function setAddpageInfo($cid, $type, $memo, $msg1) {
      $sql = "insert into tb_kids_skin set
            cid     = '$cid',
            type    = '$type',
            memo    = '$memo',
            msg1    = '$msg1'
       on duplicate key update
            type    = '$type',
            memo    = '$memo',
            msg1    = '$msg1'";
       $this->db->query($sql);
    }
    
    //추가페이지 삭제
    function delAddpageInfo($cid, $type) {
      $sql = "delete from tb_kids_skin where cid='$cid' and type='$type'";
      $this->db->query($sql);
    }
    
	
	//추가 배송비 리스트				20171228		chunter
	function getShippingExtraInfo($rid, $zipcode='', $addWhere='', $orderby='', $limit='') {
	  if ($zipcode) {
	  	$sql = "select * from tb_release_extra_shipping where rid='$rid' and zipcode='$zipcode' $addWhere";
    	return $this->db->fetch($sql);  
	  } else {
	  	$sql = "select * from tb_release_extra_shipping where rid='$rid' $addWhere $orderby $limit"; 
		return $this->db->listArray($sql);
	  }
	}
	
	//추가 배송비 등록				20171228		chunter
	function setShippingExtraInfo($rid, $zipcode, $address, $add_price) {
		$sql = "insert into tb_release_extra_shipping set rid='$rid', zipcode='$zipcode', address='$address', add_price='$add_price' on duplicate key update address='$address', add_price='$add_price'";
		//debug($sql);
		//exit;
	  $this->db->query($sql);
	}
	
	//추가 배송비 삭제				20171228		chunter
	function delShippingExtraInfo($rid, $zipcode) {
	  $sql = "delete from tb_release_extra_shipping where rid='$rid' and zipcode='$zipcode'";
	  $this->db->query($sql);
	}
	
	        
}
?>