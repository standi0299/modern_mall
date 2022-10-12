<?php
/**
* service_config
* 2013.12.12 by chunter
*/

class M_mall {
    var $db;
    function M_mall() {
        $this->db = $GLOBALS[db];
    }


    function getInfo($cid) {
      $sql = "select * from exm_mall where cid='$cid'";
      return $this->db->fetch($sql);
    }


    function getConfig($cid) {
      $sql = "select * from tb_mall_config where cid='$cid'";
      return $this->db->fetch($sql);
    }


    function getRequestInfo($webhard_id) {
      $sql = "select * from tb_ph_service_request where webhard_id='$webhard_id'";
      return $this->db->fetch($sql);
    }


    function getWebHardInfo($c_id) {
      $sql = "select * from tb_ph_service_request where cid='$c_id'";
      return $this->db->fetch($sql);
    }


    function checkServiceDomain($domain) {
      //$sql = "select * from bluepod_service.tb_mall_info where service_domain like '%$domain%'";
      $sql = "select * from exm_mall where service_domain like '%$domain%'";
      //echo $sql;
      return $this->db->fetch($sql);
    }


    function mallStateUpdate($cid, $state) {
      $sql = "update exm_mall set state = '$state' where cid = '$cid'";
      //echo $sql; 
      $this->db->query($sql);
    }


    //유치원 서비스 포인트 update      20150326   chunter
    function mallPrettyPointUpdate($cid, $mall_point) {
      $sql = "update exm_mall set pretty_point = '$mall_point' where cid = '$cid'";
      $this->db->query($sql);      
     // debug($sql); exit;
    }


    function phRequestStateUpdate($webhard_id, $state, $withdraw_msg = '') {
      if ($state == "2")
        $sql = "update tb_ph_service_request set state = '$state', withdraw_date = now(), withdraw_msg ='$withdraw_msg' where webhard_id = '$webhard_id'";
      else
        $sql = "update tb_ph_service_request set state = '$state' where webhard_id = '$webhard_id'";
      //echo $sql;
      $this->db->query($sql);
    }


    function setDaumMapData($cid, $lng, $lat)
    {
      $sql = "insert into exm_config set
          cid   = '$cid',
          config  = 'daum_map',
          value = '$lng|$lat'
        on duplicate key update
          value = '$lng|$lat'";
      //echo $sql;
      $this->db->query($sql);
    }
       
   //printgroup 서비스 기간 update / 16.06.17 / kdk
   function mallPrintgroupExpireDateUpdate($cid, $expire_date) {      
      $sql = "update exm_mall set printgroup_expire_date = '$expire_date' where cid = '$cid'";
      $this->db->query($sql);
      //debug($sql);
    }
   
   //service_domain 자체 도메인 update / 16.07.12 / kdk
   function mallServiceDomainUpdate($cid, $domain) {
      $sql = "update exm_mall set service_domain = '$domain' where cid = '$cid'";
      //echo $sql;
      $this->db->query($sql);
    }
   
   //몰정보 수정
   function mallInfoUpdate($cid, $self_podsiteid, $self_podsid, $manager, $phone) {
   	  $sql= "update exm_mall set self_podsiteid='$self_podsiteid',self_podsid='$self_podsid',manager='$manager',phone='$phone' where cid='$cid'";
	  $this->db->query($sql);
   }
       
	//podsiteid 조회. /goods/view.php에서 사용.
	function getSelfPodSiteId($cid) {
		$sql = "select self_podsiteid from exm_mall where cid = '$cid'";
		return $this -> db -> fetch($sql,1);
	}
}
?>