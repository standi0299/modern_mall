<?php
/**
* config model
* 2015.02.11 by chunter
*/
     
class M_board{
    var $db;
    function M_board() {
        $this->db = $GLOBALS[db];
    }
    
    function getBoardDataListNLimit($cid, $board_id, $limitCount)
    {      
      if ($board_id=="cs")
        $sql = "select * from exm_mycs where cid='$cid' and id = 'cs' order by no desc limit $limitCount";
      else if ($board_id=="qna")
        $sql = "select * from exm_mycs where cid='$cid' and id = 'qna' order by no desc limit $limitCount";
      else if ($board_id=="review")
        $sql = "select * from exm_review where cid='$cid' order by no desc limit $limitCount";
      else
        $sql = "select * from exm_board where cid='$cid' and board_id = '$board_id' order by no desc limit $limitCount";
      
      //debug($sql);
      return $this->db->listArray($sql);
    }
		
				
    function getBoardSetList($cid){
    	$query = "select *,(select count(*) from exm_board where cid = '$cid' and board_id=exm_board_set.board_id) article from exm_board_set where cid = '$cid'";
        //echo $query;
      	return $this->db->listArray($query);
	}

	function getBoardSetInfo($cid, $board_id){
		$query = "select * from exm_board_set where cid = '$cid' and board_id = '$board_id'";
		return $this->db->fetch($query);
	}		

	function getBoardSetCount($cid){
		$query = "select count(*) as cnt from exm_board where cid = '$cid'";
		$totCnt = $this->db->fetch($query);
		return $totCnt[cnt];
	}
		
		function getBoardInfo($cid, $board_id, $board_no){
      $query = "select * from exm_board where cid = '$cid' and board_id = '$board_id' and no=$board_no";
      return $this->db->fetch($query);
		}
		
		
		function getBoardList($cid, $board_id, $addWhere, $orderBy){
			if ($board_id)
				$boardWhere = "and board_id = '$board_id'";
    	$query = "select * from exm_board where cid = '$cid' $boardWhere $addWhere order by $orderBy";
      return $this->db->listArray($query);
		}
		
		
		function getBoardCount($cid, $board_id){
			if ($board_id)
				$boardWhere = "and board_id = '$board_id'";
    	$query = "select count(*) as cnt from exm_board where cid = '$cid' $boardWhere $addWhere";
      $totCnt = $this->db->fetch($query);
      return $totCnt[cnt];
		}
		
		
		function getBoardFileData($cid, $no, $board_id, $admin='N'){
      $r_file = array();
      $query = "select * from exm_board_file where pno = $no";
      $res = $this->db->query($query);
      while ($file_data = $this->db->fetch($res)){
         if ($admin == "Y") $file_data[size] = getImageSize("../../data/board/$cid/$board_id/$file_data[filesrc]");
		 else if ($admin == "N") $file_data[size] = getImageSize("../data/board/$cid/$board_id/$file_data[filesrc]");
         $r_file[] = $file_data;
      }
      return $r_file;
   }
   
   //고객센터 리스트
   function getCustomerService($cid, $tableName, $addWhere='', $orderby='', $limit='') {
   	  $sql = "select * from $tableName $addWhere $orderby $limit";
	  return $this->db->listArray($sql);
   }
   
   //고객센터 추가 및 수정
   function setCustomerService($mode, $tableName, $addColumn='', $addWhere='') {
   	  if ($mode == "update") {
   	  	  $sql = "update $tableName $addColumn $addWhere";
	  } else if ($mode == "insert") {
	  	  $sql = "insert into $tableName $addColumn";
	  }
	  $this->db->query($sql);
   }
   
   //고객센터 삭제
   function delCustomerService($cid, $tableName, $no, $storageid='') {
   	  if ($tableName == "exm_edking") {
	   	  $sql2 = "delete from exm_edking_copyfolder where cid='$cid' and storageid='$storageid'";
		  $this->db->query($sql2);
		  $sql3 = "delete from exm_edking_comment where cid='$cid' and pno='$no'";
		  $this->db->query($sql3);
	  }
	  
	  $sql = "delete from $tableName where cid='$cid' and no='$no'";
	  $this->db->query($sql);
   }
   
   //고객센터 정보
   function getCustomerServiceInfo($cid, $tableName, $addWhere='') {
   	  $sql = "select * from $tableName $addWhere";
	  return $this->db->fetch($sql);
   }
   
   //고객센터 파일 삭제
   function delCustomerServiceFile($fileno) {	  
	  $sql = "delete from exm_board_file where fileno='$fileno'";
	  $this->db->query($sql);
   }
   
   //편집왕 정보
   function getEdkingInfo($cid, $addWhere='') {
   	  $sql = "select * from exm_edking $addWhere";
   	  return $this->db->fetch($sql);
   }
   
   //편집왕 추천
   function setEdkingComment($no, $cid, $mid) {
   	  $sql = "insert into exm_edking_comment set 
   	  	pno     = '$no',
   	  	cid		= '$cid',
   	  	mid	    = '$mid',
   	  	chk_yn	= 'Y',
   	  	regdt	= now()";
   	  $this->db->query($sql);
   }
   
   //편집왕 추가 및 수정
   function setEdkingInfo($no, $addColumn='', $addWhere='') {
   	  if ($no) {
   	  	  $sql = "update exm_edking $addColumn $addWhere";
   	  } else {
   	  	  $sql = "insert into exm_edking $addColumn";
   	  }
	  $this->db->query($sql);
   }
   
   //베스트 편집왕 정보
   function getBestEdkingInfo($cid) {
   	  $sql = "select * from exm_edking where cid = '$cid' and chk_yn = 'Y' order by no desc limit 1";
   	  return $this->db->fetch($sql);
   }
   
   //편집왕 편집정보 미리보기파일 복사 정보
   function getEdkingCopyfolderInfo($cid, $storageid) {
   	  $sql = "select * from exm_edking_copyfolder where cid = '$cid' and storageid = '$storageid'";
	  return $this->db->fetch($sql);
   }
   
   //편집왕 편집정보 미리보기파일 복사 추가 및 수정
   function setEdkingCopyfolderInfo($no, $addColumn='', $addWhere='') {
   	  if ($no) {
   	  	  $sql = "update exm_edking_copyfolder $addColumn $addWhere";
   	  } else {
   	  	  $sql = "insert into exm_edking_copyfolder $addColumn";
   	  }
   	  $this->db->query($sql);
   }
   
   //문의 정보 조회
   function getMycsInfo($cid, $addWhere='') {
   	  $sql = "select * from exm_mycs $addWhere";
   	  return $this->db->fetch($sql);
   }
   
   function getMycsFileInfo($pno){
      $sql = "select * from exm_mycs_file where pno = '$pno'";
      return $this->db->listArray($sql);
   }
    
}
?>