<?php

class M_modern {
   var $db;
   function M_modern() {
      $this -> db = $GLOBALS[db];
   }
   
   function getListCount_modern($cid, $addWhere = '', $inner_join = '', $group_by = '') {

      if($inner_join){
         $inner_join = "inner join exm_ord b on b.payno = a.payno
         inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno";
      }
      
      if($group_by) $groupby = "group by a.payno";
      
      $sql = "select a.payno from exm_pay a
               $inner_join
              where a.cid = '$cid' $addWhere $groupby
            ";
      $result = $this -> db -> listArray($sql);
      
      $total = count($result);
      return $total;
   }
   
    //시안요청 조회.md_design_draft.
    function getDesignPayno($payno) {
        $sql = "select * from md_design_draft where payno='$payno'";
        //debug($sql);
        
        return $this -> db -> fetch($sql);
    }

    //시안요청 조회.md_design_draft. / 20190122 / kdk
    function getDesignCartno($cartno) {
        $sql = "select * from md_design_draft where cartno='$cartno'";
        //debug($sql);
        
        $this -> db -> fetch($sql);

        if($this->db->count == 0) { $result = 0; } 
        else { $result = 1; }
        
        return $result;
    }
       
    //시안요청 저장.md_design_draft. / 20190116 / kdk
    function setDesignDraftInsert($data) {
        $sql = "INSERT INTO md_design_draft SET
            cartno = '$data[cartno]',
            cid = '$data[cid]',
            mid = '$data[mid]',
            state = '81',
            regist_flag = 'Y',
            regist_date = now(),
            storageid = '$data[storageid]',
            est_order_data = '$data[est_order_data]',
            est_order_option_desc = '$data[est_order_option_desc]',
            est_file_upload_json = '$data[est_file_upload_json]',
            ext_json_data = '$data[ext_json_data]'
        ";
        //debug($sql);
        
        $this->db->query($sql);
        
        if(mysql_affected_rows() == 0) { $result = 0; } 
        else { $result = 1; }
        
        return $result;
    }

    //시안요청 수정. / 20190121 / kdk
    function setDesignDraftUpdate($payno, $data) {
        $flds = "";
        
        if($data) {
            foreach ($data as $key => $val) {
                $flds .= " $key='$val', ";
            }
        }

        $sql = "UPDATE md_design_draft SET
            $flds
            update_date = now()
            WHERE payno = '$payno';
        ";
        //debug($sql);
        $this->db->query($sql);
    }

    //시안요청 payno수정. / 20190121 / kdk        
    function setDesignDraftPaynoUpdate($cartno, $payno) {
        $sql = "UPDATE md_design_draft SET
                payno = '$payno'
            WHERE cartno = '$cartno';
        ";
        //debug($sql);
        $this->db->query($sql);
    }    

    //시안요청 state수정. / 20190121 / kdk
    function setDesignDraftStateUpdate($payno, $state) {
        $sql = "UPDATE md_design_draft SET
                state = '$state',
                update_date = now()
            WHERE payno = '$payno';
        ";
        //debug($sql);
        $this->db->query($sql);
    }
    
    //시안요청 시안선택 수정. / 20190121 / kdk
    function setDesignDraftFixUpdate($payno, $fixid) {
        $sql = "UPDATE md_design_draft SET
                design_fix = '$fixid',
                design_fix_date = now()
            WHERE payno = '$payno';
        ";
        //debug($sql);
        $this->db->query($sql);
    }    
    
    //시안요청 댓글 조회. / 20190130 / kdk
    function getDesignComment($payno) {
        $sql = "select * from md_design_comment where payno='$payno'";
        //debug($sql);
        
        return $this->db->listArray($sql);
    }
    
    //시안요청 댓글 등록. / 20190130 / kdk
    function setDesignCommentInsert($data) {
        $sql = "INSERT INTO md_design_comment SET
            cid = '$data[cid]',
            mid = '$data[mid]',
            name = '$data[name]',
            payno = '$data[payno]',
            comment = '$data[comment]',
            admin = '$data[admin]',
            regdt = now()
        ";
        //debug($sql);
        
        $this->db->query($sql);
        
        if(mysql_affected_rows() == 0) { $result = 0; } 
        else { $result = 1; }
        
        return $result;
    }    
    
    //시안요청 댓글 수정. / 20190130 / kdk
    function setDesignCommentUpdate($no, $comment) {
        $sql = "UPDATE md_design_comment SET
                comment = '$comment'
            WHERE no = '$no';
        ";
        //debug($sql);
        $this->db->query($sql);
    }    
    
    //시안요청 댓글 삭제. / 20190130 / kdk
    function setDesignCommentDelete($no) {
        $sql = "DELETE FROM md_design_comment WHERE no = '$no';
        ";
        //debug($sql);
        $this->db->query($sql);
    }
    
}
?>