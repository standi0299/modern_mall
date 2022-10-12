<?php
/**
 * service_config
 * 2015.02.11 by chunter
 */

class M_manager {
    var $db;
    function M_manager() {
        $this -> db = $GLOBALS[db];
    }

    function getList($cid, $valid="") 
    {
      if ($valid) $valid_where = " and valid = '$valid'";
      $sql = "select * from exm_manager where cid = '$cid' $valid_where order by branch_flag" ;
      //echo $sql;
      return $this->db->listarray($sql);
    }
    
    //회원의 manager 데이터 가져오기 / 15.09.01 / kjm
    //필드 데이타형식이 int에서 문자열로 변경하여 ","구분자를 넣어 복수값을 입력하도록함 / 20181031 / kdk
    /*function getInfo($cid, $mid){
       $sql = "select * from exm_manager where manager_no = (select manager_no from exm_member where cid = '$cid' and mid = '$mid')";
       $sql = "select a.* from exm_manager a inner join exm_member b on a.manager_no = b.manager_no where a.cid = '$cid' and b.mid = '$mid'";
       return $this->db->fetch($sql);
    }*/
    function getInfo($cid, $mno){
       $sql = "select * from exm_manager where cid = '$cid' and manager_no = '$mno'";
       return $this->db->fetch($sql);
    }    
}
?>