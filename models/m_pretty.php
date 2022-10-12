<?php
/**
 * service_config
 * 2013.12.05 by chunter
 */

class M_pretty {
   var $db;
   function M_pretty() {
      $this -> db = $GLOBALS[db];
   }
   
   function getPrettyMappingInfo($cid, $cartno) {
      $sql = "select * from tb_pretty_cart_mapping where cid = '$cid' and cartno = '$cartno'";
      return $this->db->fetch($sql);
    }

   //로고, 직인 데이터 가져오기
   function getLogoSealData($cid, $mid, $file_flag) {
      $sql = "select * from tb_pretty_etc_file where cid = '$cid' and mid = '$mid' and file_flag = '$file_flag'";
      return $this -> db -> fetch($sql);
   }

   function getMemberGoods($cid) {
      $sql = "select a.mid, b.name from tb_member_goods_mapping a, exm_member b where a.cid = '$cid' and a.mid = b.mid and a.cid = b.cid";
      $data = $this -> db -> listArray($sql);
      foreach ($data as $val) {
         $ret[$val[name]] = $val[mid];
      }
      return $ret;
   }

   //이전에 사용하던거
   function getMemberGoodsMapping_old($cid, $mid) {
      list($data) = $this -> db -> fetch("select goodsnos from tb_goods_member_mapping where cid = '$cid' and mid = '$mid'", 1);
      return $data;
   }

   //새로 만든거
   //시즌 조건 추가 / 17.03.14 / kjm
   function getMemberGoodsMapping($cid, $mid, $season_code = "") {
      if($season_code) $addWhere = "and season_code = '$season_code'";
      $sql = "select goodsno from tb_member_goods_mapping where cid = '$cid' and mid = '$mid' $addWhere";
      return $this -> db -> listArray($sql);
   }

   function getOrderCntData($cid, $mid, $itemstep, $order_where, $season_code = '') {
      if($season_code) $addWhere = "and c.season_code = '$season_code'";
      $sql = "select count(a.payno) from exm_ord_item a
               inner join exm_pay b on a.payno = b.payno
               inner join tb_pretty_cart_mapping c on a.cartno = c.cartno
               inner join tb_pretty_class d on c.class_ID = d.ID
               inner join tb_pretty_child e on c.child_ID = e.ID
              where b.cid = '$cid' and b.mid = '$mid' and c.cart_state = 'O' and a.itemstep in ($itemstep) $addWhere $order_where";
      list($cnt) = $this -> db -> fetch($sql, 1);
      return $cnt;
   }

   function getManagerNo($cid, $sess_admin) {//삭제예정
      $sql = "select manager_no from exm_manager where cid = '$cid' and login_id = '$sess_admin'";
      list($manager_no) = $this -> db -> fetch($sql, 1);

      return $manager_no;
   }

   //스토리지 연장 결제건이 있는지 체크한다.
   function getStorageHistoryWithDate($cid, $storage_date, $bStartDataOnly = false) {
      if ($bStartDataOnly)
         $addWhere = " and storage_sdate >= '$storage_date'";
      else
         $addWhere = " and storage_sdate <= '$storage_date' and storage_edate >= '$storage_date'";

      $sql = "select * from tb_pretty_storage_history where cid = '$cid' $addWhere order by storage_edate asc";
      return $this -> db -> listArray($sql);
   }

   //스토리지 결제 내역 저장     20150408    chunter
   function insertStorageHistory($cid, $storage_size, $storage_sdate, $storage_edate, $storage_account_kind, $storage_month, $account_price, $tno, $ordr_payno, $account_pay_method = '', $pg_log = '', $account_detail_info = '') {
      $sql = "insert into tb_pretty_storage_history set
         cid = '$cid',
         storage_size = '$storage_size',
         storage_sdate = '$storage_sdate',
         storage_edate = '$storage_edate',
         storage_account_kind = '$storage_account_kind',
         storage_month = '$storage_month',
         account_price = '$account_price',
         tno = '$tno',
         payno = '$ordr_payno',
         account_pay_method = '$account_pay_method',
         pg_log = '$pg_log',
         account_detail_info = '$account_detail_info',
         regist_date = now()";
      //debug($sql);
      $this -> db -> query($sql);
   }

   //포인트 내역 저장
   function insertPointHistory($cid, $account_point, $account_flag, $mall_point, $mid, $m_name, $memo = '', $payno = '', $tno = '', $account_pay_method = '', $account_price = '0') {
      $sql = "insert into tb_pretty_account_point_history set
         cid = '$cid', 
         account_point = '$account_point',
         account_flag = '$account_flag',
         memo = '$memo',
         payno = '$payno',
         regist_date = now(),
         mall_point = $mall_point,
         mid = '$mid',
         m_name = '$m_name',
         tno = '$tno',
         account_pay_method = '$account_pay_method',
         account_price = '$account_price'
      ";
      //debug($sql);
      $this -> db -> query($sql);
   }

   function getUseFileTotalSize($cid, $mid = '') {
      if ($mid)
         $addWhere = " and mid='$mid'";
      //$sql = "select ifnull(sum(file_size), 0) as size from tb_pretty_file where cid='$cid' $addWhere";

      //속도 개선     20150819    chunter
      $sql = "select ifnull(sum(use_webhard_size), 0) as size from exm_member where cid='$cid' $addWhere";
      list($total_size) = $this -> db -> fetch($sql, 1);
      return $total_size;
   }
    
   //pods 편집 스토리지 사용량 (pods 편집 스토리지 용량도 DB 에 저장한다)          20151207    chunter
   function getUseEditFileTotalSize($cid, $mid = '') {
      if ($mid) $addWhere = " and mid='$mid'";
      $sql = "select ifnull(sum(use_edit_webhard_size), 0) as size from exm_member where cid='$cid' $addWhere";
      list($total_size) = $this->db->fetch($sql, 1);
      return $total_size;
   }
   
   //분류 용량 + pods 편집 스토리지 사용량          20170208    chunter
   function getUseAllFileTotalSize($cid, $mid = '') {
      if ($mid) $addWhere = " and mid='$mid'";
      $sql = "select ifnull(sum(use_webhard_size), 0) as size1, ifnull(sum(use_edit_webhard_size), 0) as size2 from exm_member where cid='$cid' $addWhere";
      list($size1, $size2) = $this->db->fetch($sql, 1);
         $total_size = $size1 + $size2;
      return $total_size;
   }

   //회원별 웹하드 사용량 조회.
   function getUseFileTotalSizeGroupByMember($cid) {
      $sql = "select mid, sum(file_size) as size from tb_pretty_file where cid='$cid' group by mid";
      return $this -> db -> listArray($sql);
   }

   function insertFolder($cid, $mid, $service_year, $folder_name, $folder_kind) {

      $folderData = $this -> getFolderData($cid, $mid, $service_year, $folder_kind, '');
      //$orderby = $folderData[orderby] + 1;

      $sql = "insert into tb_pretty_folder set
                cid = '$cid',
                mid = '$mid',
                service_year = '$service_year',
                folder_name = '$folder_name',
                folder_kind = '$folder_kind',
                orderby = '$folderData[orderby]' + 1,
                regist_date = now(),
                update_date = now(),
                file_cnt = 0";
      $this -> db -> query($sql);
   }

   function updateFolder($folder_ID, $cid, $mid, $service_year, $folder_name, $folder_kind) {
      $sql = "update tb_pretty_folder set
                folder_name = '$folder_name',
                folder_kind = '$folder_kind',
                update_date = now()
              where cid = '$cid' and mid = '$mid' and ID=$folder_ID";
      $this -> db -> query($sql);
   }

   function insertClass($cid, $mid, $class_name, $teacher_name, $service_year) {
      $data = $this -> getClassDataOrderby($cid, $mid);
      //debug($data);
      $sql = "insert into tb_pretty_class set
                cid = '$cid',
                mid = '$mid',
                class_name = '$class_name',
                teacher_id = '$teacher_name',
                orderby = '$data[orderby]'+1,
                service_year = '$service_year',
                regist_date = now()";
      //debug($sql);
      $this -> db -> query($sql);
   }
   
   //가장 큰 반 정렬 데이터 가져오기
   function getClassDataOrderby($cid, $mid) {
      $sql = "select * from tb_pretty_class where cid = '$cid' and mid = '$mid' order by orderby desc";
      //debug($sql);
      $data = $this -> db -> fetch($sql);
      return $data;
   }

   function updateClass($class_ID, $cid, $mid, $class_name, $teacher_name) {
      $sql = "update into tb_pretty_class set
                 class_name = '$class_name'
                 teacher_id = '$teacher_name'
                where cid = '$cid' and mid = '$mid' and ID=$class_ID";
      $this -> db -> query($sql);
   }

   function insertChild($cid, $mid, $class_ID, $child_name, $service_year, $child_birth = '') {
      $data = $this -> getChildDataOrderby($cid, $mid, $class_ID);

      $sql = "insert into tb_pretty_child set
                 cid = '$cid',
                 mid = '$mid',
                 class_ID = '$class_ID',
                 child_name = '$child_name',
                 child_birth = '$child_birth',
                 file_cnt = '0',
                 service_year = '$service_year',
                 orderby = '$data[orderby]'+1,
                 regist_date = now()";
      $this -> db -> query($sql);
   }

   function getChildDataOrderby($cid, $mid, $class_ID) {
      $sql = "select * from tb_pretty_child where cid = '$cid' and mid = '$mid' and class_ID = '$class_ID' order by orderby desc";
      //debug($sql);
      $data = $this -> db -> fetch($sql);
      return $data;
   }

   function updateChild($child_ID, $cid, $mid, $class_ID, $child_name, $child_birth = '') {
      $sql = "update tb_pretty_child set
                 class_ID = '$class_ID',
                 child_name = '$child_name',
                 child_birth = '$child_birth'
                where cid = '$cid' and mid = '$mid' and ID=$child_ID";
      $this -> db -> query($sql);
   }

   //원아의 분류된 파일 수량 Update      20150403    chunter
   //일반, 졸업 분류로 나눔 / 16.07.15 / kjm
   function updateChildImageCount($cid, $mid, $child_ID, $mapping_kind) {
      if($mapping_kind == 'N'){
         $column = "file_cnt";
         $mapping_table = "tb_pretty_file_child_mapping";
      } else if($mapping_kind == 'G'){
         $column = "file_cnt_graduation";
         $mapping_table = "tb_pretty_file_child_graduation_mapping";
      }
      
      $sql = "update tb_pretty_child set
                $column = (select count(ID) from $mapping_table where child_ID='$child_ID')
              where cid = '$cid' and mid = '$mid' and ID=$child_ID";
      $this -> db -> query($sql);
   }
   
   //원아의 분류된 파일 수량 Update      20150403    chunter
   function updateChildImageCount_s2($cid, $mid, $child_ID) {
      $sql = "update tb_pretty_child set
                file_cnt = (select count(ID) from tb_pretty_file_child_mapping where child_ID='$child_ID')
              where cid = '$cid' and mid = '$mid' and ID=$child_ID";
      $this -> db -> query($sql);
   }

   //폴더, 원아에 맵핑된 사진 갯수 가져오기 / 15.08.20 / kjm
   function getFileCnt($ID, $flag) {
      if ($flag == "folder")
         $sql = "select count(ID) as cnt from tb_pretty_file_folder_mapping where folder_ID = '$ID'";
      else if ($flag == "child")
         $sql = "select count(ID) as cnt from tb_pretty_file_child_mapping where child_ID = '$ID'";
      return $this -> db -> fetch($sql);
   }
   
   //폴더, 원아에 맵핑된 사진 갯수 가져오기 / 15.08.20 / kjm
   function getFileCnt_s2($ID, $flag, $mapping_kind = '') {
      if ($flag == "folder")
         $sql = "select count(ID) as cnt from tb_pretty_file_folder_mapping where folder_ID = '$ID'";
      else if ($flag == "child" && $mapping_kind == 'N')
         $sql = "select count(ID) as cnt from tb_pretty_file_child_mapping where child_ID = '$ID'";
      else if ($flag == "child" && $mapping_kind == 'G'){         
         $sql = "select count(ID) as cnt from tb_pretty_file_child_graduation_mapping where child_ID = '$ID'";
      }
      return $this -> db -> fetch($sql);
   }

   //폴더, 원아의 file_cnt 값 업데이트 / 15.08.20 / kjm
   function updateFileCnt($file_cnt, $ID, $flag, $mapping_kind = '') {
      if ($flag == "folder") {
         $sql = "update tb_pretty_folder set
               file_cnt = '$file_cnt'
               where ID = '$ID'";
      } else if ($flag == "child" && ($mapping_kind == '' || $mapping_kind == 'N')) {
         $sql = "update tb_pretty_child set
               file_cnt = '$file_cnt'
               where ID = '$ID'";
      } else if ($flag == "child" && $mapping_kind == 'G') {
         $sql = "update tb_pretty_child set
               file_cnt_graduation = '$file_cnt'
               where ID = '$ID'";
      }
      $this -> db -> query($sql);
   }

   //폴더별 미분류 사진 수량 업데이트 / 15.08.20 / kjm
   function updateFolderNotGroupImageCount($cid, $mid, $folder_ID, $file_cnt) {
      $sql = "update tb_pretty_folder set
               not_group_file_cnt = '$file_cnt'
              where cid = '$cid' and mid = '$mid' and ID = '$folder_ID'";
      $this -> db -> query($sql);
   }

   function insertImageChildMapping($file_id, $child_ID) {
      $sql = "insert into tb_pretty_file_child_mapping set
                file_id = '$file_id',
                child_ID = '$child_ID',
                regist_date = now()
                on duplicate key update
                file_id = '$file_id'";
      $this -> db -> query($sql);
   }

   //원아 파일 복사, 이동, 삭제시 원아별 이미지 숫자 업데이트 / 15.05.28 / kjm
   function updateChildFileCnt($child_ID) {
      list($fileCnt) = $this -> db -> fetch("select count(ID) as file_cnt from tb_pretty_file_child_mapping where child_ID = $child_ID", 1);
      $this -> db -> query("update tb_pretty_child set file_cnt = $fileCnt where ID = $child_ID");
   }

   function insertImageFolderMapping($file_id, $folder_ID) {
      $sql = "insert into tb_pretty_file_folder_mapping set
                file_id = '$file_id',
                folder_ID = '$folder_ID',
                regist_date = now()
              on duplicate key update
                file_id = '$file_id'";
      //debug($sql); exit;
      $this -> db -> query($sql);
   }

   //원아분류 맵핑 정보를 미분류 폴더로 모두 이동하기.   20150402  chunter
   function moveChildImageToNotGroupFolder($cid, $mid, $child_ID) {
      $data = $this -> getTrashFolderData($cid, $mid);
      if ($data) {
         $this -> moveChildImageToFolder($child_ID, $data[ID]);
      }
   }

   //기존 폴더 이미지를 미분류 폴더로 모두 이동하기.   20150402  chunter
   function moveFolderImageToNotGroupFolder($cid, $mid, $folder_ID) {
      $data = $this -> getTrashFolderData($cid, $mid);
      if ($data) {
         $this -> moveFolderImageToFolder($folder_ID, $data[ID]);
      }
   }

   //파일 삭제시 맵핑 정보를 미분류 폴더로 모두 이동하기.   20150402  chunter
   function moveFolderFilesImageToNotGroupFolder($cid, $mid, $source_folder_ID, $source_file_ids) {
      $data = $this -> getTrashFolderData($cid, $mid);

      if ($data) {
         $file_ids = implode(",", $source_file_ids);
         $this -> moveFolderFileImageToFolder($source_folder_ID, $file_ids, $data[ID]);
      }
   }

   function moveChildFilesImageToNotGroupFolder($cid, $mid, $child_ID, $source_file_ids) {
      $data = $this -> getTrashFolderData($cid, $mid);
      if ($data) {
         $file_ids = implode(",", $source_file_ids);
         $this -> moveChildFileImageToFolder($child_ID, $file_ids, $data[ID]);
      }
   }

   //폴더의 맵핑 이미지를 모두 다른 폴더로 이동시키기   20150402  chunter
   function moveFolderImageToFolder($source_folder_ID, $target_folder_ID) {
      //중복 맵핑 정보는 제거한다. 중복 파일중 삭제폴더에 속해 있는 파일들을 지운다.
      $sql = "delete from tb_pretty_file_folder_mapping  where ID in (
        select tbl1.ID from 
        (select * from tb_pretty_file_folder_mapping  where folder_ID = '$source_folder_ID') as tbl1,
        (select * from tb_pretty_file_folder_mapping  where folder_ID = '$target_folder_ID') as tbl2
        where tbl1.file_id = tbl2.file_id
        )";
      //$this -> db -> query($sql);

      //속도 개선 쿼리      20150824    chunter
      $this -> deleteDuplicateFolderImage($source_folder_ID, $target_folder_ID);

      //맵핑 정보 이동.
      $sql = "update tb_pretty_file_folder_mapping set
                folder_ID = '$target_folder_ID'
              where ID in (select * from (SELECT ID FROM tb_pretty_file_folder_mapping WHERE folder_ID='$source_folder_ID') as temp)";

      //속도 개선 쿼리      20150824    chunter
      $sql = "update tb_pretty_file_folder_mapping set folder_ID = '$target_folder_ID' where folder_ID='$source_folder_ID'";
      $this -> db -> query($sql);
   }

   //선택 삭제된 파일들을 휴지통으로 이동 / 15.04.15 / kjm
   function moveSelectFolderImageData($file_ID, $source_folder_ID, $target_folder_ID, $cid, $mid) {
      //$data = $this -> getTrashFolderData($cid, $mid);
      //$trashID = $data[0][ID];

      //중복 맵핑 정보는 제거한다. 중복 파일중 삭제폴더에 속해 있는 파일들을 지운다.
      $sql = "delete from tb_pretty_file_folder_mapping where ID in (
                select tbl1.ID from 
                (select * from tb_pretty_file_folder_mapping  where folder_ID = '$target_folder_ID') as tbl1,
                (select * from tb_pretty_file_folder_mapping  where folder_ID = '$source_folder_ID') as tbl2
                where tbl1.file_id = tbl2.file_id
             )";
      //$this -> db -> query($sql);
      //속도 개선 쿼리      20150824    chunter
      //$this->deleteDuplicateFolderImage($source_folder_ID, $target_folder_ID);

      list($file_check) = $this -> db -> fetch("select ID from tb_pretty_file_folder_mapping  where file_id = '$file_ID' and folder_ID = '$target_folder_ID'", 1);

      if ($file_check) {
         $this -> db -> query("delete from tb_pretty_file_folder_mapping where file_id = '$file_ID' and folder_ID = '$source_folder_ID'");
      } else {
         //맵핑 정보 이동.
         $sql = "update tb_pretty_file_folder_mapping set
                folder_ID = '$target_folder_ID'
                where file_id = '$file_ID' and folder_ID = '$source_folder_ID'";
         //debug($sql);
         $this -> db -> query($sql);
      }
   }

   //중복 맵핑 정보는 제거한다. 중복 파일중 삭제폴더에 속해 있는 파일들을 지운다.
   function deleteDuplicateFolderImage($source_folder_ID, $target_folder_ID) {
      $sql = "delete a.* from tb_pretty_file_folder_mapping a 
        inner join (select * from tb_pretty_file_folder_mapping  where folder_ID = '$source_folder_ID') as tbl1 on a.ID=tbl1.ID
        inner join (select * from tb_pretty_file_folder_mapping  where folder_ID = '$target_folder_ID') as tbl2 on tbl1.file_id = tbl2.file_id";
      $this -> db -> query($sql);
   }

   //중복 맵핑 정보는 제거한다. 중복 파일중 삭제폴더에 속해 있는 파일들을 지운다.
   function deleteDuplicateChildImage($child_ID, $target_folder_ID) {
      $sql = "delete a.* from tb_pretty_file_child_mapping a 
        inner join (select * from tb_pretty_file_child_mapping  where child_ID = '$child_ID') as tbl1 on a.ID=tbl1.ID
        inner join (select * from tb_pretty_file_folder_mapping  where folder_ID = '$target_folder_ID') as tbl2 on tbl1.file_id = tbl2.file_id";
      $this -> db -> query($sql);
   }

   function deleteSelectFolderImageData($file_ID, $source_folder_ID) {
      //파일 이동시 source_folder에 있는 선택된 파일 삭제
      $sql = "delete from tb_pretty_file_folder_mapping
                where file_id = '$file_ID' and folder_id = '$source_folder_ID'";
      //debug($sql) ;
      $this -> db -> query($sql);
   }

   //앱 이미지 삭제 / 15.08.27 / kjm
   function prettyAppImgDelete($filelist_ids, $source_folder_ID, $target_folder_IDs, $table_flag) {

      if ($table_flag == "folder") {
         $table = "tb_pretty_file_folder_mapping";
         $ID = "folder_ID";
      } else if ($table_flag = "child") {
         $table = "tb_pretty_file_child_mapping";
         $ID = "child_ID";
      }

      foreach ($filelist_ids as $file_ID) {
         $where .= "'$file_ID', ";
         $insert .= " ('$file_ID', '$target_folder_IDs', now()),";

         //$m_pretty -> moveSelectFolderImageData($file_ID, $source_folder_ID, $target_folder_IDs, $get_cid, $get_mid);
      }
      $where = substr($where, 0, -2);
      $insert = substr($insert, 0, -1);

      $sql = "delete from $table where $ID = '$source_folder_ID' and file_id in ($where)";
      $this -> db -> query($sql);

      if ($target_folder_IDs) {
         $sql = "delete from tb_pretty_file_folder_mapping where folder_ID = '$target_folder_IDs' and file_id in ($where)";
         $this -> db -> query($sql);

         $sql = "insert into tb_pretty_file_folder_mapping(file_id, folder_ID, regist_date) value $insert";
         $this -> db -> query($sql);
      }
   }

   //앱 이미지 삭제 s2 / 16.06.22 / kjm
   function prettyAppImgDelete_s2($filelist_ids, $source_folder_ID, $target_folder_IDs, $table_flag, $mapping_kind_type) {

      if ($table_flag == "folder") {
         $table = "tb_pretty_file_folder_mapping";
         $ID = "folder_ID";
      } else if ($table_flag = "child") {
         $ID = "child_ID";
         if($mapping_kind_type == 'N') $table = "tb_pretty_file_child_mapping";
         else if($mapping_kind_type == 'G') $table = "tb_pretty_file_child_graduation_mapping";
      }

      foreach ($filelist_ids as $file_ID) {
         $where .= "'$file_ID', ";
         $insert .= " ('$file_ID', '$target_folder_IDs', now()),";

         //$m_pretty -> moveSelectFolderImageData($file_ID, $source_folder_ID, $target_folder_IDs, $get_cid, $get_mid);
      }
      $where = substr($where, 0, -2);
      $insert = substr($insert, 0, -1);

      $sql = "delete from $table where $ID = '$source_folder_ID' and file_id in ($where)";
      $this -> db -> query($sql);

      if ($target_folder_IDs) {
         $sql = "delete from tb_pretty_file_folder_mapping where folder_ID = '$target_folder_IDs' and file_id in ($where)";
         $this -> db -> query($sql);

         $sql = "insert into tb_pretty_file_folder_mapping(file_id, folder_ID, regist_date) value $insert";
         $this -> db -> query($sql);
      }
   }

   //앱 이미지 이동 / 15.08.27 / kjm
   function prettyAppImgMove($filelist_ids, $source_folder_ID, $target_folder_IDs, $table_flag) {
      if ($table_flag == "folder") {
         $table = "tb_pretty_file_folder_mapping";
         $ID = "folder_ID";
      } else if ($table_flag = "child") {
         $table = "tb_pretty_file_child_mapping";
         $ID = "child_ID";
      }

      foreach ($filelist_ids as $file_ID) {
         $where .= "'$file_ID', ";

         foreach ($target_folder_IDs as $target_folder_ID) {
            $insert .= " ('$file_ID', '$target_folder_ID', now()),";
         }

         //$m_pretty -> moveSelectFolderImageData($file_ID, $source_folder_ID, $target_folder_IDs, $get_cid, $get_mid);
      }
      $where = substr($where, 0, -2);
      $insert = substr($insert, 0, -1);
      $target_folder_IDs = implode(", ", $target_folder_IDs);
      
      $sql = "delete from $table where $ID = '$source_folder_ID' and file_id in ($where)";
      $this -> db -> query($sql);

      $sql = "delete from $table where $ID in ($target_folder_IDs) and file_id in ($where)";
      $this -> db -> query($sql);

      $sql = "insert into $table(file_id, $ID, regist_date) value $insert";
      $this -> db -> query($sql);
   }

   //앱 이미지 이동 S2 / 16.06.22 / kjm
   function prettyAppImgMove_s2($filelist_ids, $source_folder_ID, $target_folder_IDs, $table_flag, $mapping_kind_type) {
      if ($table_flag == "folder") {
         $table = "tb_pretty_file_folder_mapping";
         $ID = "folder_ID";
      } else if ($table_flag = "child") {
         $ID = "child_ID";
         if($mapping_kind_type == 'N') $table = "tb_pretty_file_child_mapping";
         else if($mapping_kind_type == 'G') $table = "tb_pretty_file_child_graduation_mapping";
      }

      foreach ($filelist_ids as $file_ID) {
         $where .= "'$file_ID', ";

         foreach ($target_folder_IDs as $target_folder_ID) {
            $insert .= " ('$file_ID', '$target_folder_ID', now()),";
         }

         //$m_pretty -> moveSelectFolderImageData($file_ID, $source_folder_ID, $target_folder_IDs, $get_cid, $get_mid);
      }
      $where = substr($where, 0, -2);
      $insert = substr($insert, 0, -1);
      $target_folder_IDs = implode(", ", $target_folder_IDs);
      
      $sql = "delete from $table where $ID = '$source_folder_ID' and file_id in ($where)";
      $this -> db -> query($sql);

      $sql = "delete from $table where $ID in ($target_folder_IDs) and file_id in ($where)";
      $this -> db -> query($sql);

      $sql = "insert into $table(file_id, $ID, regist_date) value $insert";
      $this -> db -> query($sql);
   }

   function prettyAppImgCopy($filelist_ids, $source_folder_ID, $target_folder_IDs, $table_flag) {
      if ($table_flag == "folder") {
         $table = "tb_pretty_file_folder_mapping";
         $ID = "folder_ID";
      } else if ($table_flag = "child") {
         $table = "tb_pretty_file_child_mapping";
         $ID = "child_ID";
      }

      foreach ($filelist_ids as $file_ID) {
         $where .= "'$file_ID', ";

         foreach ($target_folder_IDs as $target_folder_ID) {
            $insert .= " ('$file_ID', '$target_folder_ID', now()),";
         }

         //$m_pretty -> moveSelectFolderImageData($file_ID, $source_folder_ID, $target_folder_IDs, $get_cid, $get_mid);
      }
      $where = substr($where, 0, -2);
      $insert = substr($insert, 0, -1);
      $target_folder_IDs = implode(", ", $target_folder_IDs);

      $sql_del = "delete from $table where $ID in ($target_folder_IDs) and file_id in ($where)";
      $this -> db -> query($sql_del);
      
      $sql_insert = "insert into $table(file_id, $ID, regist_date) value $insert";
      $this -> db -> query($sql_insert);
   }
   
   function prettyAppImgCopy_s2($filelist_ids, $source_folder_ID, $target_folder_IDs, $table_flag, $mapping_kind_type) {
      if ($table_flag == "folder") {
         $table = "tb_pretty_file_folder_mapping";
         $ID = "folder_ID";
      } else if ($table_flag = "child") {
         $ID = "child_ID";
         if($mapping_kind_type == 'N') $table = "tb_pretty_file_child_mapping";
         else if($mapping_kind_type == 'G') $table = "tb_pretty_file_child_graduation_mapping";
      }

      foreach ($filelist_ids as $file_ID) {
         $where .= "'$file_ID', ";

         foreach ($target_folder_IDs as $target_folder_ID) {
            $insert .= " ('$file_ID', '$target_folder_ID', now()),";
         }

         //$m_pretty -> moveSelectFolderImageData($file_ID, $source_folder_ID, $target_folder_IDs, $get_cid, $get_mid);
      }
      $where = substr($where, 0, -2);
      $insert = substr($insert, 0, -1);
      $target_folder_IDs = implode(", ", $target_folder_IDs);

      $sql_del = "delete from $table where $ID in ($target_folder_IDs) and file_id in ($where)";
      $this -> db -> query($sql_del);
      
      $sql_insert = "insert into $table(file_id, $ID, regist_date) value $insert";
      $this -> db -> query($sql_insert);
   }

   //원아의 맵핑 이미지를 모두 다른 폴더로 이동시키기   20150402  chunter
   function moveChildImageToFolder($child_ID, $target_folder_ID) {
      //중복 맵핑 정보는 제거한다. 중복 파일중 삭제폴더에 속해 있는 파일들을 지운다.
      $sql = "delete from tb_pretty_file_child_mapping  where ID in (
        select tbl1.ID from
        (select * from tb_pretty_file_child_mapping  where child_ID = '$child_ID') as tbl1,
        (select * from tb_pretty_file_folder_mapping  where folder_ID = '$target_folder_ID') as tbl2
        where tbl1.file_id = tbl2.file_id
        )";
      //$this -> db -> query($sql);

      //속도 개선
      $this -> deleteDuplicateChildImage($child_ID, $target_folder_ID);

      //임시방편
      /*
       $query = "select * from tb_pretty_file_child_mapping where child_ID='$child_ID'";
       $res = $this -> db->query($query);
       while($data = $this -> db->fetch($res)){
       $abc = "insert into tb_pretty_file_folder_mapping set
       file_id = '$data[file_id]',
       folder_ID = '$target_folder_ID',
       regist_date = now()
       ";
       //debug($abc);
       $this -> db->query($abc);
       }
       */

      //폴더 맵핑 정보 이동.
      $sql = "insert into tb_pretty_file_folder_mapping(file_id, folder_ID, regist_date) 
                (select file_id, '$target_folder_ID', now() from tb_pretty_file_child_mapping where child_ID='$child_ID')";
      $this -> db -> query($sql);
   }

   //선택 이동된 원아 파일 맵핑 데이터 삭제 / 15.06.24 / kjm
   function deleteSelectChildImageData($file_ID, $soruce_child_ID) {
      //원아의 맵핑 데이터 삭제
      $sql = "delete from tb_pretty_file_child_mapping where file_id = '$file_ID' and child_ID = '$soruce_child_ID'";
      $this -> db -> query($sql);
      //원아 파일 카운트 업데이트
      $sql = "update tb_pretty_child set file_cnt = (select count(file_id) from tb_pretty_file_child_mapping where child_ID = '$soruce_child_ID') where ID = '$soruce_child_ID'";
      $this -> db -> query($sql);
   }

   //선택 삭제된 원아 파일들을 휴지통으로 이동 / 15.04.15 / kjm
   function moveSelectChildImageData($file_ID, $soruce_child_ID, $target_folder_IDs, $cid, $mid) {
      //$data = $this -> getFolderList($cid, $mid, '', U);
      //$trashID = $data[0][ID];

      //원아의 맵핑 데이터 삭제
      $sql = "delete from tb_pretty_file_child_mapping where file_id = '$file_ID' and child_ID = '$soruce_child_ID'";
      //debug($sql);
      $this -> db -> query($sql);

      //폴더 맵핑에 휴지통으로 파일 데이터 insert
      $sql = "insert into tb_pretty_file_folder_mapping set
               folder_ID = '$target_folder_IDs',
               file_id = '$file_ID',
               regist_date = now()
              on duplicate key update
               regist_date = now()
              ";
      //debug($sql);
      $this -> db -> query($sql);

      $this -> updateChildFileCnt($soruce_child_ID);
   }

   //폴더내 파일 삭제시 파일 맵핑 정보를 다른 폴더로 이동시키기   20150402  chunter
   function moveFolderFileImageToFolder($source_folder_ID, $source_file_ids, $target_folder_ID) {
      //중복 맵핑 정보는 제거한다. 중복 파일중 삭제폴더에 속해 있는 파일들을 지운다.
      $sql = "delete from tb_pretty_file_folder_mapping  where ID in (
        select tbl1.ID from
        (select * from tb_pretty_file_folder_mapping  where folder_ID = '$source_folder_ID' and file_id in ($source_file_ids)) as tbl1,
        (select * from tb_pretty_file_folder_mapping  where folder_ID = '$target_folder_ID') as tbl2
        where tbl1.file_id = tbl2.file_id
        )";
      //속도 개선 쿼리
      $sql = "delete a.* from tb_pretty_file_folder_mapping a 
        inner join (select * from tb_pretty_file_folder_mapping  where folder_ID = '$source_folder_ID' and file_id in ($source_file_ids)) as tbl1 on a.ID=tbl1.ID
        inner join (select * from tb_pretty_file_folder_mapping  where folder_ID = '$target_folder_ID') as tbl2 on tbl1.file_id = tbl2.file_id";
      $this -> db -> query($sql);

      //맵핑 정보 이동.
      if ($target_folder_ID) {
         $sql = "update tb_pretty_file_folder_mapping set
                   folder_ID = '$target_folder_ID' 
                   where ID = (select * from (select ID from tb_pretty_file_folder_mapping where folder_ID='$source_folder_ID' and file_id in ($source_file_ids)) as tbl1)";
         //속도 개선 쿼리
         $sql = "update tb_pretty_file_folder_mapping set
                   folder_ID = '$target_folder_ID' 
                   where folder_ID='$source_folder_ID' and file_id in ($source_file_ids)";
         $this -> db -> query($sql);
      }
   }

   //원아분류내 파일 삭제시 파일 맵핑 정보를 다른 폴더로 이동시키기   20150402  chunter
   function moveChildFileImageToFolder($child_ID, $source_file_ids, $target_folder_ID) {
      //중복 맵핑 정보는 제거한다. 중복 파일중 삭제폴더에 속해 있는 파일들을 지운다.
      $sql = "delete from tb_pretty_file_child_mapping  where ID in (
              select tbl1.ID from
              (select * from tb_pretty_file_child_mapping  where child_ID = '$child_ID' and file_id in ($source_file_ids)) as tbl1,
              (select * from tb_pretty_file_folder_mapping  where folder_ID = '$target_folder_ID') as tbl2
              where tbl1.file_id = tbl2.file_id)
             ";

      //속도 개선 쿼리
      $sql = "delete a.* from tb_pretty_file_child_mapping a 
      inner join (select * from tb_pretty_file_child_mapping  where child_ID = '$child_ID' and file_id in ($source_file_ids)) as tbl1 on a.ID=tbl1.ID";

      //inner join (select * from tb_pretty_file_folder_mapping  where folder_ID = '$target_folder_ID') as tbl2 on tbl1.file_id = tbl2.file_id";

      $this -> db -> query($sql);

      //폴더 맵핑 정보 이동.
      $sql = "insert into tb_pretty_file_folder_mapping(file_id, folder_ID, regist_date) 
              (select file_id, $target_folder_ID, now() from tb_pretty_file_child_mapping where child_ID='$child_ID' and file_id in ($source_file_ids))";

      //폴더 맵핑 정보 이동.
      $sql = "insert into tb_pretty_file_folder_mapping(file_id, folder_ID, regist_date) values(
              (select file_id from tb_pretty_file_child_mapping where child_ID='$child_ID'), '$target_folder_ID', now())";
      $this -> db -> query($sql);
   }

   //장바구니 내역
   function getCartList($cid, $mid, $cart_state = '') {
      if($cart_state)
         $sql_sub = "and cart_state = '$cart_state'";

      $sql = "select * from tb_pretty_cart_mapping
         where cid='$cid' and mid = '$mid' $sql_sub";

      return $this -> db -> listArray($sql);
   }
   
   //주문 요청 내역
   function getOrderRequestList($cid, $mid) {
      $sql = "select * from tb_pretty_cart_mapping 
               where class_ID = '$class_ID' and cid='$cid' and mid = '$mid' and cart_state = 'R'";
      return $this -> db -> listArray($sql);
   }

   //장바구니에서 삭제.      chunter
   function setCartDelete($cid, $mid, $cartno) {
      $sql = "update tb_pretty_cart_mapping set cart_state = 'E'
        where cartno = '$cartno' and cid='$cid' and mid = '$mid'";
      return $this -> db -> fetch($sql);
   }

   //주문 요청내역에서 삭제      chunter
   function setOrderRequestDelete($cid, $mid, $cartno) {
      $sql = "update tb_pretty_cart_mapping set cart_state='C' 
        where cartno = '$cartno' and cid='$cid' and mid = '$mid'";
      return $this -> db -> fetch($sql);
   }

   function getTeacherInfo($cid, $teacher_id) {
      $sql = "select * from tb_pretty_teacher where cid='$cid' and teacher_id = '$teacher_id'";
      return $this -> db -> fetch($sql);
   }

   function getTeacherList($cid, $mid) {
      $sql = "select * from tb_pretty_teacher where cid='$cid' and mid = '$mid' order by teacher_name";
      return $this -> db -> listArray($sql);
   }

   /*
   function checkFileStorageCode($storage_code) {
   $sql = "select storage_code from tb_pretty_file_config where storage_code = '$storage_code' limit 1";
   return $this -> db -> fetch($sql);
   }
   */

   /*
   function getFileConfig($storage_code) {
   $sql = "select * from tb_pretty_file_config where storage_code = '$storage_code'";
   return $this -> db -> fetch($sql);
   }
   */

   function getCartMasterCartno($carykey) {
      $sql = "select cartno from exm_cart where cartkey = '$carykey'";
      //debug($sql) ;
      return $this -> db -> fetch($sql);
   }
   
   function getPodsData($goodsno){
      $sql = "select * from exm_goods where goodsno = '$goodsno'";
      //debug($sql) ;
      return $this -> db -> fetch($sql);
   }

   //file & file config join data
   /*
    function getFileNFileconfig($cid, $mid) {
    $sql = "select * from tb_pretty_file a
    inner join tb_pretty_file_config b on a.storage_code = b.storage_code
    where a.cid = '$cid' and b.mid = '$mid'";
    return $this -> db -> query($query);
    }
    */

   //폴더 리스트      20150306
   //$service_year, $folder_kind를 추가 쿼리로 뺌 / 15.03.09 / kjm
   function getFolderList($cid, $mid, $service_year = '', $folder_kind = '') {
      if ($service_year)
         $sub_sql_service_yaer = "and  service_year='$service_year'";
      if ($folder_kind)
         $sub_sql_folder_kind = "and folder_kind = '$folder_kind'";

      //$sub_sql = "and  service_year='$service_year' and folder_kind = '$folder_kind'";

      $sql = "select * from tb_pretty_folder where cid='$cid' and mid = '$mid' $sub_sql_service_yaer $sub_sql_folder_kind order by orderby asc";
      //debug($sql);
      return $this -> db -> listArray($sql);
   }
   
   //폴더 리스트 s2     20160713
   function getFolderList_s2($cid, $mid, $service_year = '', $mapping_kind_type = '', $season_code = '') {
      if ($service_year)
         $sub_sql_service_yaer = "and  service_year='$service_year'";
      
      if ($mapping_kind_type)
         $sub_sql_mapping_kind = "and mapping_kind_type = '$mapping_kind_type'";
      
      if ($season_code)
         $sub_sql_season_code = "and season_code = '$season_code'";

      //$sub_sql = "and  service_year='$service_year' and folder_kind = '$folder_kind'";

      $sql = "select * from tb_pretty_folder where cid='$cid' and mid = '$mid' and parent_folder_ID is not null $sub_sql_service_yaer $sub_sql_mapping_kind $sub_sql_season_code order by orderby asc";
      //debug($sql);
      return $this -> db -> listArray($sql);
   }
   
   //부모 폴더 리스트 s2     20160713
   function getFolderListParentFolder($cid, $mid, $service_year = '', $mapping_kind_type = '', $parent_folder_id = '', $season_code = '0') {
      if ($service_year)
         $sub_sql_service_yaer = "and  service_year='$service_year'";
      if ($mapping_kind_type == '')
         $sub_sql_mapping_kind = "and mapping_kind_type = 'N'";
      else
         $sub_sql_mapping_kind = "and mapping_kind_type = '$mapping_kind_type'";

      if($parent_folder_id)
         $sub_sql_parent_folder_id = "and parent_folder_id = '$parent_folder_id'";

      if($season_code)
         $sub_season_code = "and season_code = '$season_code'";
      else
         $sub_season_code = "and season_code = '0'";

      $sql = "select * from tb_pretty_folder where cid='$cid' and mid = '$mid' $sub_season_code  $sub_sql_service_yaer $sub_sql_mapping_kind $sub_sql_parent_folder_id order by orderby asc";
      //debug($sql);
      return $this -> db -> listArray($sql);
   }

   function getFolderData($cid, $mid, $service_year, $folder_kind, $folder_ID = '') {
      if ($folder_ID)
         $sub_sql = "and ID = '$folder_ID'";
      $sql = "select * from tb_pretty_folder where cid='$cid' and mid = '$mid' and  service_year='$service_year' and folder_kind = '$folder_kind' $sub_sql order by orderby desc";

      return $this -> db -> fetch($sql);
   }

   //휴지통 폴더 정보 가져오기
   function getTrashFolderData($cid, $mid) {
      $sql = "select * from tb_pretty_folder where cid='$cid' and mid = '$mid' and folder_kind = 'U'";
      return $this -> db -> fetch($sql);
   }

   //폴더내에서 사진 분류 파일 리스트     20150306
   //$list_kind - all, not_group, group (전체, 미분류, 분류완료)
   function getFolderImageList($cid, $mid, $service_year, $folder_ID, $list_kind = 'all', $limit = '') {
      /*
       if($folder_ID == 'D' || $folder_ID == 'F') $folder_sql = "and a.folder_kind = '$folder_ID'";
       else $folder_sql = "and a.ID = $folder_ID";

       if($list_kind == 'total') $list_kind = "all";
       else if ($list_kind == 'not_total') $list_kind = "not_group";

       switch ($list_kind) {
       case 'all' :
       $sub_sql = "select b.file_id from tb_pretty_folder a, tb_pretty_file_folder_mapping b where a.cid='$cid' and a.mid = '$mid' and  a.service_year='$service_year'
       $folder_sql and b.folder_ID = a.ID";
       break;
       case 'group' :
       $sub_sql = "select b.file_id
       from tb_pretty_folder a, tb_pretty_file_folder_mapping b
       where a.cid='$cid' and a.mid = '$mid' and a.service_year='$service_year' $folder_sql and b.folder_ID = a.ID and b.file_id IN (
       select file_id
       from tb_pretty_file_child_mapping)";
       break;
       case 'not_group' :
       $sub_sql = "select b.file_id
       from tb_pretty_folder a, tb_pretty_file_folder_mapping b
       where a.cid='$cid' and a.mid = '$mid' and a.service_year='$service_year' $folder_sql and b.folder_ID = a.ID and b.file_id not IN (
       select file_id
       from tb_pretty_file_child_mapping)";
       break;
       }

       $sql = "select * from tb_pretty_file
       where file_id in ($sub_sql)";
       */

      //속도 개선 쿼리 변경       20150824    chunter
      $sql_select = "select c.* ";
      $sql_from = " from tb_pretty_folder a";
      $sql_select_join = " inner join tb_pretty_file_folder_mapping b on b.folder_ID=a.ID";
      $sql_select_join .= " inner join tb_pretty_file c on c.file_id=b.file_id";
      $sql_where = " where a.cid='$cid' and a.mid = '$mid' and a.service_year='$service_year'";

      if ($folder_ID == 'D' || $folder_ID == 'F')
         $sql_where .= " and a.folder_kind = '$folder_ID'";
      else
         $sql_where .= " and a.ID = $folder_ID";

      switch ($list_kind) {
         case 'all' :
            $sql = $sql_select . $sql_from . $sql_select_join . $sql_where . $limit;
            break;

         case 'not_group' :
            $sql = $sql_select . $sql_from . $sql_select_join . " LEFT JOIN tb_pretty_file_child_mapping d on c.file_id=d.file_id" . $sql_where . " and d.file_id IS NULL" . $limit;
            break;

         case 'group' :
            $sql = $sql_select . $sql_from . $sql_select_join . " inner join tb_pretty_file_child_mapping d on c.file_id=d.file_id" . $sql_where . $limit;
            break;

         case 'trash' :
            $sql = $sql_select . $sql_from . $sql_select_join . $sql_where . $limit;
            break;
      }

      return $this -> db -> listArray($sql);
   }

   //폴더내에서 사진 수량 구하기     20150306
   //$list_kind - all, not_group, group (전체, 미분류, 분류완료)

   //not_group, group 쿼리 변경 / 15.03.09 / kjm
   function getFolderImageCount($cid, $mid, $service_year, $folder_kind, $folder_ID, $list_kind = 'all', $season_code= '') {
      /*
      $sub_sql = "select count(b.file_id) from tb_pretty_folder a, tb_pretty_file_folder_mapping b where a.cid='$cid' and a.mid = '$mid' and  a.service_year='$service_year'
      and b.folder_ID=a.ID";

      if ($folder_kind)
      $sub_sql .= " and a.folder_kind = '$folder_kind'";
      if ($folder_ID)
      $sub_sql .= " and a.ID=$folder_ID";

      switch ($list_kind) {
      case 'all' :
      $sql = $sub_sql;
      break;

      case 'not_group' :
      sql = $sub_sql . " and b.file_id not in (select file_id from tb_pretty_file_child_mapping)";
      break;

      case 'group' :
      $sql = $sub_sql . " and b.file_id in (select file_id from tb_pretty_file_child_mapping)";
      break;

      case 'trash' :
      $sql = $sub_sql;
      break;
      }
      */

      //쿼리 성능 개선으로 변경     20150817    chunter
      //$sql_select = "select count(b.file_id) as count from tb_pretty_folder a";
      //$sql_select .= " inner join tb_pretty_file_folder_mapping b on b.folder_ID=a.ID";
      //$sql_where = " where a.cid='$cid' and a.mid = '$mid' and  a.service_year='$service_year'";

      $sql_select = "select file_cnt as count";
      $sql_select2 = "select sum(not_group_file_cnt) as count";
      $sql_from = " from tb_pretty_folder";
      //$sql_select_join = " inner join tb_pretty_file_folder_mapping b on b.folder_ID=a.ID";
      $sql_where = " where cid='$cid' and mid = '$mid' and service_year = '$service_year'";

      if ($folder_kind)
         $sql_where .= " and folder_kind = '$folder_kind'";
      if ($folder_ID)
         $sql_where .= " and ID = $folder_ID";
         
      if($season_code)
         $sql_where .= " and season_code = '$season_code'";

      switch ($list_kind) {
         case 'all' :
            $sql = $sql_select . $sql_from . $sql_where;
            break;

         case 'not_group' :
            $sql = $sql_select2 . $sql_from . $sql_where;
         break;
         
         /*
         case 'group' :
         $sql = $sql_select2. $sql_from. $sql_select_join . " inner join tb_pretty_file_child_mapping c on b.file_id=c.file_id" .$sql_where;
         break;
         */
         case 'trash' :
            $sql = $sql_select . $sql_from . $sql_where;
         break;
      }

      //debug($sql);
      //$query = $this->db->query($sql);
      //$fileCnt = $this->db->count_($query);

      list($fileCnt) = $this -> db -> fetch($sql, 1);
      return $fileCnt;
   }

   //모든 폴더별 사진수량 가져오기      20150817      chunter
   function getFolderImageCountGroupByFolder($cid, $mid, $service_year, $folder_kind, $folder_ID, $list_kind = 'all', $mapping_kind = '') {

      $sql_select = "select a.*";
      $sql_select2 = " , count(b.file_id) as count";
      $sql_from = " from tb_pretty_folder a";
      $sql_select_join = " inner join tb_pretty_file_folder_mapping b on b.folder_ID=a.ID";
      $sql_where = " where a.cid='$cid' and a.mid = '$mid'";
      $sql_groupby = " group by a.ID";
      $sql_orderby = " order by orderby";

      if ($folder_kind)
         $sql_where .= " and a.folder_kind = '$folder_kind'";
      if ($folder_ID)
         $sql_where .= " and a.ID=$folder_ID";
      if($service_year)
         $sql_where .= " and a.service_year='$service_year'";
      
      //일반사진, 졸업사진 구분
      if($mapping_kind == 'G')
         $join_table = "tb_pretty_file_child_graduation_mapping";
      else if ($mapping_kind == 'N' || $mapping_kind == '')
         $join_table = "tb_pretty_file_child_mapping";

      switch ($list_kind) {
         case 'all' :
            $sql = $sql_select . $sql_from . $sql_where . $sql_groupby . $sql_orderby;
            break;

         case 'not_group' :
            $sql = $sql_select . $sql_select2 . $sql_from . $sql_select_join . " LEFT JOIN $join_table c on b.file_id=c.file_id" . $sql_where . " and c.file_id IS NULL" . $sql_groupby;
            break;

         case 'group' :
            $sql = $sql_select . $sql_select2 . $sql_from . $sql_select_join . " inner join $join_table c on b.file_id=c.file_id" . $sql_where . $sql_groupby;
            break;

         case 'trash' :
            $sql = $sql_select . $sql_from . $sql_where . $sql_groupby;
            break;
      }

      //debug($sql);

      return $this -> db -> listArray($sql);
   }

   //폴더별 폴더 정보와 그 폴더안의 파일수량 구하기
   function getFolderDataNImageCount($cid, $mid, $service_year, $folder_kind, $folder_ID) {
      /*
       $sub_sql = "select a.ID, count(b.file_id) as file_cnt from tb_pretty_folder a, tb_pretty_file_folder_mapping b
       where a.cid='$cid' and a.mid = '$mid' and  a.service_year='$service_year' ";

       if ($folder_kind)
       $sub_sql .= " and a.folder_kind = '$folder_kind'";

       $sub_sql .= " and a.ID = b.folder_ID group by b.folder_ID";

       $sql = "select a.*, tbl1.file_cnt  from tb_pretty_folder a left outer join ($sub_sql) as tbl1
       on a.ID = tbl1.ID where a.cid='$cid' and a.mid = '$mid' and a.service_year='$service_year' ";

       if ($folder_kind)
       $sql .= " and a.folder_kind = '$folder_kind'";
       $sql .= " order by a.orderby asc";
       */

      //속도 개선으로 쿼리 재 생성     20150814    chunter
      $sql = "select * from tb_pretty_folder where cid='$cid' and mid = '$mid' and service_year='$service_year' ";
      if ($folder_kind)
         $sql .= " and folder_kind = '$folder_kind'";
      $sql .= " order by orderby asc";

      return $this -> db -> listArray($sql);
   }

   //, $service_year, $orderby = "orderby"
   function getClassList($cid, $mid, $service_year, $orderby = "orderby", $season_code = "") {
      if($season_code) $addWhere = "and season_code = '$season_code'";
      //$sql = "select * from tb_pretty_class where cid='$cid' and mid = '$mid' and service_year='$service_year'";
      $sql = "select * from tb_pretty_class where cid='$cid' and mid = '$mid' and service_year = '$service_year' $addWhere order by $orderby";
      return $this -> db -> listArray($sql);
   }

   function getChildList($cid, $mid, $class_ID, $orderby = "orderby", $season_code = "") {
      $sql = "select * from tb_pretty_child  
                where cid='$cid' and mid = '$mid' and class_ID = '$class_ID' order by $orderby";
      return $this -> db -> listArray($sql);
   }

   //유치원별 원아수
   function getChildCountGroupByMember($cid, $service_year) {
      $sql = "select mid, count(ID) as cnt from tb_pretty_child where cid='$cid' and service_year = '$service_year' group by mid";
      return $this -> db -> listArray($sql);
   }

   //유치원별 반수
   function getClassCountGroupByMember($cid, $service_year) {
      $sql = "select mid, count(ID) as cnt from tb_pretty_class where cid='$cid' and service_year = '$service_year' group by mid";
      return $this -> db -> listArray($sql);
   }

   //유치원 원아수       20150818    chunter
   function getChildCount($cid, $mid, $service_year, $class_ID = '', $season_code = '') {
      $sql = "select count(cid) from tb_pretty_child where cid='$cid' and mid = '$mid' and service_year = '$service_year'";
      if ($class_ID)
         $sql .= " and class_ID = '$class_ID'";
      list($cnt) = $this -> db -> fetch($sql, 1);
      return $cnt;
   }

   function getClassNChildList($cid, $mid, $service_year = '', $class_ID = '', $orderBy = '') {
      if ($class_ID)
         $sub_sql = "and a.ID = '$class_ID'";
         if (!$orderBy) $orderBy = "b.orderby";
      $sql = "select a.*, b.*, b.ID as child_ID from tb_pretty_class a,  tb_pretty_child b 
                where a.cid='$cid' and a.mid = '$mid' and a.service_year = '$service_year' and a.ID = b.class_ID and b.service_year = '$service_year' $sub_sql 
                order by $orderBy";
      return $this -> db -> listArray($sql);
   }

   function getClassInfo($cid, $mid, $class_ID) {
      $sql = "select * from tb_pretty_class where cid='$cid' and mid = '$mid' and ID=$class_ID";
      return $this -> db -> fetch($sql);
   }

   function getChildInfo($cid, $mid, $child_ID) {
      $sql = "select * from tb_pretty_child where cid='$cid' and mid = '$mid' and ID=$child_ID";
      return $this -> db -> fetch($sql);
   }   
   
   //상품 편집하기 상풀별 편집목록
   function getGroupGoodsEditList($cid, $mid, $bDisplayFlagOption = false, $regdt_where = false) {
      $addWhere = "";
      if ($bDisplayFlagOption)
         $addWhere .= " and display_flag != 'N'";
      if($_COOKIE[season_code])
         $addWhere .= " and season_code = '$_COOKIE[season_code]'";

      $sql = "select tbl1.*, tbl2.goodsno, tbl2.goodsnm, tbl2.podskind, tbl2.pods_use from
        (
        select * from tb_pretty_cart_mapping
        where class_ID = '' and child_ID = '' and cid='$cid' and mid = '$mid' $addWhere $regdt_where
        ) as tbl1
        left join exm_goods tbl2 
        on tbl1.goodsno = tbl2.goodsno 
        order by tbl1.order_by asc, tbl2.goodsnm asc";

      //$sql = "select * from tb_pretty_child where cid='$cid' and mid = '$mid' and service_year='$service_year' and ID=$class_ID";
      //debug($sql);

      return $this -> db -> listArray($sql);
   }
   
   /* 이전 내용
   //상품 편집하기 상풀별 편집목록
   function getGroupGoodsEditList($cid, $mid, $bDisplayFlagOption = false, $regdt_where = false) {
      if ($bDisplayFlagOption)
         $addWhere = " and a.display_flag != 'N'";

      $sql = "select tbl1.*, tbl2.goodsno, tbl2.goodsnm, tbl2.podskind, tbl2.pods_use from
        (
        select a.master_cartno, a.class_ID, a.child_ID, a.display_flag, a.order_by, b.* from tb_pretty_cart_mapping a left join exm_cart b on a.master_cartno = b.cartno 
        where a.class_ID = '' and a.child_ID = '' and b.cid='$cid' and b.mid = '$mid' $addWhere $regdt_where
        ) as tbl1
        left join exm_goods tbl2 
        on tbl1.goodsno = tbl2.goodsno 
        order by tbl1.order_by asc, tbl2.goodsnm asc";

      //$sql = "select * from tb_pretty_child where cid='$cid' and mid = '$mid' and service_year='$service_year' and ID=$class_ID";
      //debug($sql);

      return $this -> db -> listArray($sql);
   }
   */
   
   //상품별 원아별 편집리스트   
   function getGroupGoodsEditChildList($cid, $mid, $class_ID, $master_cartno, $service_year = '') {
      $sql = "select tbl1.*, tbl2.* from
        tb_pretty_child tbl1 left join 
          (
          select child_ID, master_cartno, cart_state, reedit, cartno,
            goodsno,
            optno,
            addoptno,
            ea,
            storageid,
            cartkey,
            updatedt,
            title,
            vdp_edit_data,
            child_flag,
            child_code,
            order_shiptype,
            cart_manager_no,
            pods_use,
            podsno,
            podskind from tb_pretty_cart_mapping
          where class_ID = '$class_ID' and master_cartno='$master_cartno'
          ) as tbl2
        on tbl1.ID = tbl2.child_ID
        where tbl1.class_ID=$class_ID and tbl1.cid='$cid' and tbl1.mid = '$mid'
        order by tbl1.orderby asc";
      //debug($sql);
      return $this -> db -> listArray($sql);
   }
   
   /*
   function getGroupGoodsEditChildList($cid, $mid, $class_ID, $master_cartno, $service_year = '') {
      $sql = "select tbl1.*, tbl2.* from
        tb_pretty_child tbl1 left join 
          (
          select a.child_ID, a.master_cartno, a.cart_state, a.reedit, b.* from tb_pretty_cart_mapping a left join exm_cart b on a.cartno = b.cartno
          where a.class_ID = '$class_ID' and a.master_cartno='$master_cartno'
          ) as tbl2
        on tbl1.ID = tbl2.child_ID
        where tbl1.class_ID=$class_ID and tbl1.cid='$cid' and tbl1.mid = '$mid'
        order by tbl1.orderby asc";
      debug($sql);
      
      $a = $this -> db -> listArray($sql);
      debug($a);
      return $this -> db -> listArray($sql);
   }
   */
   
   //상품별(마스터 템플릿) 편집수량 구하기     20150311
   function getGroupGoodsEditCount($cid, $mid, $goodsno) {
      $sql = "select master_cartno, count(master_cartno) as count
        from tb_pretty_cart_mapping
        where  cid='$cid' and mid = '$mid' and goodsno=$goodsno and class_ID <> '' and cart_state = 'E'
        group by master_cartno";
      //debug($sql);
      return $this -> db -> listArray($sql);
   }

   //상품별(마스터 템플릿) 원아별 편집중 인원 숫자      20150311
   function getGroupGoodsEditChildCount($cid, $mid, $master_cartno) {
      $sql = "select class_ID, count(class_ID) as count
        from tb_pretty_cart_mapping
        where  cid='$cid' and mid = '$mid' and class_ID <> '' and master_cartno='$master_cartno' and cart_state = 'E'
        group by class_ID";
      //debug($sql);
      return $this -> db -> listArray($sql);
   }

   function getChildImgList($cid, $mid, $child_ID, $mapping_kind) {
      $sub_sql = "select file_id from tb_pretty_file_child_mapping where child_ID = $child_ID";

      $sql = "select * from tb_pretty_file
                where file_id in ($sub_sql)";

      if($mapping_kind == '' || $mapping_kind == 'N')
         $join_table = "tb_pretty_file_child_mapping";
      else if($mapping_kind == 'G')
         $join_table = "tb_pretty_file_child_graduation_mapping";

      //속도 성능 개선을 위한 쿼리 재조정        20150814    chunter
      $sql = "select a.* from tb_pretty_file a
                inner join $join_table b on a.file_id = b.file_id
                where b.child_ID = $child_ID";
      //debug($sql);
      return $this -> db -> listArray($sql);
   }

   function loginChk() {
      if (!$GLOBALS[sess][mid])
         go("../main/index.php");
   }

   function getCartStorageCode($master_cartno) {
      $sql = "select storageid from tb_pretty_cart_mapping where master_cartno = $master_cartno";
      return $this -> db -> fetch($sql);
   }

   //원아 편집기 띄울때 필요한 데이터
   function callEditorData($cartno, $storageCode = '') {
      if (!$storageCode) {
         $sql = "select a.cartno, a.goodsno, a.title, b.goodsnm, b.podsno, c.podoptno, c.optno, a.ea, a.addoptno from tb_pretty_cart_mapping a
                     inner join exm_goods b on a.goodsno = b.goodsno
                     left join exm_goods_opt c on a.optno = c.optno
                     where a.cartno = $cartno";
         return $this -> db -> fetch($sql);
      } else {
         $sql = "select a.cartno, a.goodsno, a.storageid, b.pods_use, b.podsno, c.podoptno, c.optno, a.ea, a.addoptno from tb_pretty_cart_mapping a
                     inner join exm_goods b on a.goodsno = b.goodsno
                     left join exm_goods_opt c on a.optno = c.optno
                     where a.cartno = $cartno";
         return $this -> db -> fetch($sql);
      }
   }

   //사용하지 않음 확인      20150824
   function getFolderImageListWithChildGroupInfo($cid, $mid, $service_year, $folder_ID, $list_kind = 'all') {
      switch ($list_kind) {
         case 'all' :
            $sub_sql = "select b.file_id from tb_pretty_folder a, tb_pretty_file_folder_mapping b where a.cid='$cid' and a.mid = '$mid' and  a.service_year='$service_year'
                            and a.ID=$folder_ID and b.folder_ID = a.ID";
            break;
         case 'group' :
            $sub_sql = "select b.file_id
                            from tb_pretty_folder a, tb_pretty_file_folder_mapping b
                            where a.cid='$cid' and a.mid = '$mid' and a.service_year='$service_year' and a.ID=$folder_ID and b.folder_ID = a.ID and b.file_id IN (
                            select file_id
                            from tb_pretty_file_child_mapping)";
            break;
         case 'not_group' :
            $sub_sql = "select b.file_id
                            from tb_pretty_folder a, tb_pretty_file_folder_mapping b
                            where a.cid='$cid' and a.mid = '$mid' and a.service_year='$service_year' and a.ID=$folder_ID and b.folder_ID = a.ID and b.file_id not IN (
                            select file_id
                            from tb_pretty_file_child_mapping)";
            break;
      }

      $sql = "select b.* from tb_pretty_file a, tb_pretty_file_child_mapping b
                where a.file_id in ($sub_sql) and a.file_id = b.file_id";
      //debug($sql);
      //exit;

      return $this -> db -> listArray($sql);
   }

   //장바구니 데이터 가져오기 / 15.03.30 / kjm
   function getCartData($cid, $mid, $state) {

      //if($state == 'R') $subsql = "";

      $sql = "select b.*, c.class_name, d.child_name, e.goodsnm from tb_pretty_cart_mapping b
          inner join tb_pretty_class c on b.class_ID = c.ID
          inner join tb_pretty_child d on b.child_ID = d.ID
          inner join exm_goods e on b.goodsno = e.goodsno
          where b.cid = '$cid' and b.mid = '$mid' and b.cart_state = '$state'
         ";
      return $this -> db -> listArray($sql);
   }

   //주문 데이터 가져오기
   function getOrderData($cid, $mid, $step) {

      if ($_GET[step] == 91)
         $subsql = "and b.itemstep = '$_GET[step]'";
      else if ($_GET[step] == 92)
         $subsql = "and b.itemstep in (92, 3, 4, 5)";
      else
         $subsql = "and b.itemstep = '$_GET[step]'";

      $sql = "select b.payno, a.master_cartno, a.cartno, b.saleprice, b.ea, b.itemstep  from tb_pretty_cart_mapping a
                inner join exm_ord_item b on a.cartno = b.cartno
                where b.cid = '$cid' and b.mid = '$mid' and a.cart_state = 'O' $subsql";
      return $this -> db -> listArray($sql);
   }

   function getOrderDataDetail($cid, $mid, $payno) {
      if ($payno)
         $sub_sql = " where b.payno = '$payno'";

      $sql = "select b.*, a.master_cartno, a.cartno, b.saleprice, b.ea, b.itemstep, b.title, b.goodsnm, b.goodsno, e.class_name, f.child_name, g.pretty_point from
                tb_pretty_cart_mapping a
                inner join exm_ord_item b on a.cartno = b.cartno
                inner join tb_pretty_class e on a.class_ID = e.ID
                inner join tb_pretty_child f on a.child_ID = f.ID
                inner join exm_goods g on a.goodsno = g.goodsno
                $sub_sql
                order by b.payno desc
        ";
      //debug($sql);
      return $this -> db -> listArray($sql);
   }

   function getOrderPrettyPoint($payno, $ordno, $ordseq) {
      $sql = "select if(c.pretty_point,c.pretty_point,b.pretty_point) pretty_point from exm_ord_item a
               inner join exm_goods b on a.goodsno = b.goodsno
               left join tb_goods_cid_pretty_point c on a.goodsno = c.goodsno
               where a.payno = '$payno' and a.ordno = '$ordno' and a.ordseq = '$ordseq' 
              ";
      return $this -> db -> fetch($sql);
   }

   function getPointChargeHistory($cid, $mid, $tno) {
      $sql = "select * from tb_pretty_account_point_history where cid = '$cid' and mid = '$mid' and tno = '$tno'";
      return $this -> db -> fetch($sql);
   }

   function getAdminOrderData($cid, $addWhere = '') {
      $sql = "select * exm_ord_item";
      return $this -> db -> query($sql);
   }

   function getAdminOrderData_2($cid, $addWhere = '') {
      $sql = "select * from tb_pretty_cart_mapping a
            inner join exm_ord_item b on a.cartno = b.cartno
            left join tb_pretty_child c on a.child_ID = c.ID
            left join tb_pretty_class d on a.class_ID = d.ID
            where  a.cid= '$cid' and a.cart_state = 'O'
            $addWhere
            ";
      return $this -> db -> query($sql);
   }

   //반 삭제시 관련 맵핑 정보도 지워준다.   20150325    chunter
   //파일 잘못된 삭제처리를 막기위해 cid, mid 를 변수로 받는다.
   function deleteClass($cid, $mid, $class_ID) {
      //$sql = "delete from tb_pretty_cart_mapping where class_ID in (select ID from tb_pretty_class where cid = '$cid' and mid = '$mid' and ID='$class_ID')";
      //debug($sql);
      //$this->db->fetch($sql);
      $sql = "delete from tb_pretty_file_child_mapping where child_ID in (select ID from tb_pretty_child where cid = '$cid' and mid = '$mid' and class_ID='$class_ID')";
      //속도 성능 개선 쿼리      20150825
      $sql = "delete a.* from tb_pretty_file_child_mapping a
          inner join tb_pretty_child b on b.ID = a.child_ID where b.cid = '$cid' and b.mid = '$mid' and b.class_ID='$class_ID'";
      //debug($sql);
      $this -> db -> fetch($sql);
      $sql = "delete from tb_pretty_class where cid='$cid' and mid = '$mid' and ID='$class_ID'";
      //debug($sql);
      $this -> db -> fetch($sql);
      $sql = "delete from tb_pretty_child where cid='$cid' and mid = '$mid' and class_ID='$class_ID'";
      //debug($sql);
      $this -> db -> fetch($sql);
   }

   //원아 삭제시 관련 맵핑 정보도 삭제     20150325    chunter
   //파일 잘못된 삭제처리를 막기위해 cid, mid 를 변수로 받는다.
   function deleteChild($cid, $mid, $child_ID) {
      //$sql = "delete from tb_pretty_cart_mapping where child_ID in (select ID from tb_pretty_child where cid = '$cid' and mid = '$mid' and ID='$child_ID')";
      //debug($sql);
      //$this->db->fetch($sql);
      $sql = "delete from tb_pretty_file_child_mapping where child_ID in (select ID from tb_pretty_child where cid = '$cid' and mid = '$mid' and ID='$child_ID')";
      //속도 성능 개선 쿼리      20150825
      $sql = "delete a.* from tb_pretty_file_child_mapping a 
          inner join tb_pretty_child b on b.ID = a.child_ID where b.cid = '$cid' and b.mid = '$mid' and b.ID='$child_ID'";
      //debug($sql);
      $this -> db -> fetch($sql);

      $sql = "delete from tb_pretty_child where cid='$cid' and mid = '$mid' and ID='$child_ID'";
      //debug($sql);
      $this -> db -> fetch($sql);
   }

   //폴더 삭제시 관련 맵핑 정보도 삭제       20150325    chunter
   //파일 잘못된 삭제처리를 막기위해 cid, mid 를 변수로 받는다.
   function deleteFolder($cid, $mid, $folder_ID) {
      $sql = "delete from tb_pretty_file_folder_mapping where folder_ID in (select ID from tb_pretty_folder where cid = '$cid' and mid = '$mid' and ID='$folder_ID')";
      //속도 성능 개선 쿼리      20150825
      $sql = "delete a.* from tb_pretty_file_folder_mapping a 
          inner join tb_pretty_folder b on b.ID = a.folder_ID where b.cid = '$cid' and b.mid = '$mid' and b.ID='$folder_ID'";
      //debug($sql);
      $this -> db -> fetch($sql);

      $sql = "delete from tb_pretty_folder where cid='$cid' and mid = '$mid' and ID='$folder_ID'";
      //debug($sql);
      $this -> db -> fetch($sql);
   }

   //반에 속한 원아로 분류된 모든 파일 삭제. 폴더에 속해 있는 파일 제외     20150325    chunter
   function deleteClassFiles($cid, $mid, $class_ID) {
      $sql_tbl = "select a.file_id, 'Y', now(), a.cid from tb_pretty_file a, tb_pretty_file_child_mapping b, tb_pretty_child c
                   where a.file_id = b.file_id and b.child_ID=c.ID and c.cid='$cid' and c.mid = '$mid' and c.class_ID='$class_ID'";
      $sql = "insert into tb_pretty_file_delete(file_id, regist_flag, regist_date, cid) $sql_tbl";
      //debug($sql);
      //exit;
      $this -> db -> fetch($sql);
   }

   //원아에 분류된 파일 삭제. 폴더에 속해있는 파일은 제외한다.     20150325    chunter
   //원아 삭제시 원아에 맵핑된 파일을 파일삭제 테이블에 추가. 추후 삭제 처리 프로그램에서 해당 파일에 맵핑된 정보가 없을경우만 파일경로를 추가 입력한다.       20151007 chunter
   function deleteChildFiles($cid, $mid, $child_ID) {
      $sql_tbl = "select a.file_id, 'Y', now(), a.cid from tb_pretty_file a, tb_pretty_file_child_mapping b
                where a.file_id = b.file_id and b.child_ID='$child_ID'";
      $sql = "insert into tb_pretty_file_delete(file_id, regist_flag, regist_date, cid) $sql_tbl";
      //debug($sql);
      //exit;
      $this -> db -> fetch($sql);
   }

   //폴더삭제시 폴더에 종속된 파일 삭제. 원아에 분류된 파일은 제외       20150325    chunter
   //폴더 삭제시 폴더에 맵핑된 파일을 파일삭제 테이블에 추가. 추후 삭제 처리 프로그램에서 해당 파일에  맵핑된 정보가 없을경우만 파일경로를 추가 입력한다.         20151007 chunter
   function deleteFolderFiles($cid, $mid, $folder_ID) {
      $sql_tbl = "select a.file_id, 'Y', now(), a.cid from tb_pretty_file a, tb_pretty_file_folder_mapping b
                  where a.cid='$cid' and a.mid = '$mid' and b.folder_ID='$folder_ID' and a.file_id = b.file_id";
      $sql = "insert into tb_pretty_file_delete(file_id, regist_flag, regist_date, cid) $sql_tbl";
      //debug($sql);
      //exit;
      $this -> db -> fetch($sql);
   }

   //파일 개별 삭제시 파일삭제 테이블에 추가. 추후 삭제 처리 프로그램에서 해당 파일에 맵핑된 정보가 없을경우만 파일경로를 추가 입력한다.       20151007 chunter
   //파일 잘못된 삭제처리를 막기위해 cid, mid 를 변수로 받는다.
   function deleteFiles($cid, $mid, $file_ids_arr) {
      foreach ($file_ids_arr as $file_ID) {
         $file_ids .= "'$file_ID', ";
      }
      $file_ids = substr($file_ids, 0, -2);

      $sql_tbl = "select file_id, 'Y', now(), cid from tb_pretty_file where cid='$cid' and mid = '$mid' and file_id in ($file_ids)";
      $sql = "insert into tb_pretty_file_delete(file_id, regist_flag, regist_date, cid) $sql_tbl";
      //debug($sql);
      //exit;
      $this -> db -> fetch($sql);
   }

   function getFileInfoWithFilename($cid, $mid, $server_file_name) {
      $sql = "select * from tb_pretty_file where cid = '$cid' and mid = '$mid' and
        (server_name = '$server_file_name' or server_thum1_name='$server_file_name' or server_thum2_name ='$server_file_name')";
      $list = $this -> db -> listArray($sql);
   }

   function getListCount($cid, $addWhere = '') {

      $sql = "select count(a.payno) as cnt from exm_pay a
                    inner join exm_ord_item c on a.payno = c.payno
                    inner join tb_pretty_cart_mapping d on c.cartno = d.cartno
                    inner join exm_goods e on d.goodsno = e.goodsno
                    inner join tb_pretty_class f on d.class_ID = f.ID
                    inner join tb_pretty_child g on d.child_ID = g.ID
                    inner join exm_member h on a.cid = h.cid and a.mid = h.mid
                    where  a.cid = '$cid' $addWhere
                    ";
      $result = $this -> db -> fetch($sql);
      $total = $result[cnt];
      //$result = $this -> db -> query($sql);
      //$total = $this -> db -> count;
      return $total;
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
      //$result = $this -> db -> query($sql);
      //$total = $this -> db -> count;
      return $total;
   }
   
   function getListTotCount($cid, $addWhere = '') {

      $sql = "select count(a.payno) as cnt from exm_ord_item a
                 inner join exm_pay c on a.payno = c.payno
                 inner join tb_pretty_cart_mapping d on a.cartno = d.cartno
                 inner join exm_goods e on d.goodsno = e.goodsno
                 inner join tb_pretty_class f on d.class_ID = f.ID
                 inner join tb_pretty_child g on d.child_ID = g.ID
                 inner join exm_member h on c.cid = h.cid and c.mid = h.mid
                 where  c.cid = '$cid' $addWhere
              ";
      //debug($sql);
      $result = $this -> db -> fetch($sql);
      $total = $result[cnt]; 
      //$result = $this -> db -> query($sql);
      //$total = $this -> db -> count;
      return $total;
   }

   function getBranchFlag($cid, $mid) {//삭제예정
      $sql = "select branch_flag from exm_manager where cid = '$cid' and login_id = '$mid'";
      $data = $this -> db -> fetch($sql);

      return $data[branch_flag];
   }

   function getMemberManager($cid, $manager_no) {
      $sql = "select * from exm_member where cid = '$cid' and manager_no = '$manager_no'";
      return $this -> db -> listArray($sql);
   }

   function getDetailPayData($payno) {
      $sql = "select * from tb_pretty_cart_mapping a
          inner join exm_ord_item b on a.cartno = b.cartno
          inner join exm_goods d on a.goodsno = d.goodsno
          inner join tb_pretty_class e on a.class_ID = e.ID
          inner join tb_pretty_child f on a.child_ID = f.ID
          where b.payno = '$payno'";
      return $this -> db -> listArray($sql);
   }

   function getFileListFolder($cid, $mid, $folder_ID) {
      $sql = "select a.*, b.folder_ID from tb_pretty_file a
                inner join tb_pretty_file_folder_mapping b on a.file_id = b.file_id
                where a.cid = '$cid' and a.mid = '$mid' and b.folder_ID = '$folder_ID'";
      $list = $this -> db -> listArray($sql);
      return $list;
   }

   //
   function getFileListFolderWithChildMapping($cid, $mid, $folder_ID, $limit) {
      $sql = "select * from (select a.*, b.folder_ID, c.ID as child_mapping_ID from tb_pretty_file a
                inner join tb_pretty_file_folder_mapping b on a.file_id = b.file_id
                left outer join tb_pretty_file_child_mapping c on a.file_id = c.file_id
                where a.cid = '$cid' and a.mid = '$mid' and b.folder_ID = '$folder_ID') as tbl group by tbl.file_id $limit";

      $list = $this -> db -> listArray($sql);
      return $list;
   }

   function getFileListChild($cid, $mid, $child_ID) {
      $sql = "select a.*, b.child_ID from tb_pretty_file a
                inner join tb_pretty_file_child_mapping b on a.file_id = b.file_id
                where a.cid = '$cid' and a.mid = '$mid' and b.child_ID = '$child_ID'";
      $list = $this -> db -> listArray($sql);
      return $list;
   }

   function getFileListChild_s2($cid, $mid, $child_ID, $mapping_kind_type, $limit = '') {
      if($mapping_kind_type == 'N') $join_table = "tb_pretty_file_child_mapping";
      else if($mapping_kind_type == 'G') $join_table = "tb_pretty_file_child_graduation_mapping";
      
      //안넘어올때(전체) 도 해야됨
      $sql = "select a.*, b.child_ID from tb_pretty_file a
                inner join $join_table b on a.file_id = b.file_id
                where a.cid = '$cid' and a.mid = '$mid' and b.child_ID = '$child_ID' $limit";
      $list = $this -> db -> listArray($sql);
      return $list;
   }

   function fileUploadMappingData($file_id, $ID, $mode, $child_mode = '', $season_code = '') {
      if($season_code) $flds = ", season_code = '$season_code'";
      if ($mode == 'folder') {
         $query = "insert into tb_pretty_file_folder_mapping set
                        file_id = '$file_id',
                        folder_ID = '$ID',
                        regist_date = now()
                        $flds
                   on duplicate key update
                        regist_date = now()";

         $this -> db -> query($query);
      } else if ($mode == 'child') {
         //일반분류
         if($child_mode == 'N' || $child_mode == ''){
            $query = "insert into tb_pretty_file_child_mapping set
                           file_id = '$file_id',
                           child_ID = '$ID',
                           regist_date = now()
                           $flds
                      on duplicate key update
                           regist_date = now()
                           ";
            $this -> db -> query($query);
         //졸업분류
         } else if ($child_mode == 'G') {
            $query = "insert into tb_pretty_file_child_graduation_mapping set
                        file_id = '$file_id',
                        child_ID = '$ID',
                        regist_date = now()
                        $flds
                      on duplicate key update
                        regist_date = now()
                        ";

            $this -> db -> query($query);
         }
      }
   }

   function getOrderbyFolderKindOrder($cid, $mid, $service_year) {
      $query = "select orderby from tb_pretty_folder_kind_order where cid = '$cid' and mid = '$mid' and service_year='$service_year'";
      list($orderby) = $this -> db -> fetch($query, 1);
      return $orderby;
   }

   //원아에 맵핑된 파일 아이디 가져오기
   function getChildFileMappingData($ID) {
      $query = "select file_id from tb_pretty_file_child_mapping where child_ID = '$ID'";
      $list = $this -> db -> listArray($query);
      return $list;
   }

   function getPrettyOrderDetailData($data, $mode) {
      switch ($mode) {
         case "cnt" :
            list($cnt) = $this -> db -> fetch("select count(payno) as cnt from exm_ord_item where payno = '$data'", 1);
            return $cnt;

            break;

         case "payprice" :
            list($payprice) = $this -> db -> fetch("select payprice from exm_pay where payno = '$data'", 1);
            return $payprice;

            break;

         case "saleprice" :
            list($saleprice) = $this -> db -> fetch("select saleprice from exm_pay where payno = '$data'", 1);
            return $saleprice;

            break;

         case "shipprice" :
            list($shipprice) = $this -> db -> fetch("select shipprice from exm_pay where payno = '$data'", 1);
            return $shipprice;

            break;
            
         case "requestDate" :
            list($requestDate) = $this -> db -> fetch("select request_date from tb_pretty_cart_mapping where cartno = '$data'", 1);
            return $requestDate;

            break;

         case "className" :
            list($className) = $this -> db -> fetch("select b.class_name from tb_pretty_cart_mapping a 
                                            inner join tb_pretty_class b on a.class_ID = b.ID
                                            where a.cartno = '$data'", 1);
            return $className;

            break;

         case "childName" :
            list($childName) = $this -> db -> fetch("select b.child_name from tb_pretty_cart_mapping a 
                                            inner join tb_pretty_child b on a.child_ID = b.ID
                                            where a.cartno = '$data'", 1);
            return $childName;

            break;

         case "goodsnm" :
            list($goodsnm) = $this -> db -> fetch("select b.goodsnm from tb_pretty_cart_mapping a
                                         inner join exm_goods b on a.goodsno = b.goodsno
                                         where a.cartno = '$data'", 1);
            return $goodsnm;

            break;

         case "title" :
            list($title) = $this -> db -> fetch("select title from tb_pretty_cart_mapping where cartno = '$data'", 1);
            return $title;

            break;

         case "orddt" :
            list($orddt) = $this -> db -> fetch("select orddt from exm_pay where payno = '$data'", 1);
            return $orddt;

            break;

         case "returnMsg" :
            list($returnMsg) = $this -> db -> fetch("select return_msg from exm_pay where payno = '$data'", 1);
            return $returnMsg;

            break;
      }
   }

   //보관함 코드로 원아정보 가져오기     20150526    chunter
   function getClassChindInfo($cid, $mid, $storageid) {
      $sql = "select c.class_name, d.child_name from tb_pretty_cart_mapping b
        inner join tb_pretty_class c on b.class_ID = c.ID
        inner join tb_pretty_child d on b.child_ID = d.ID          
        where b.cid = '$cid' and b.mid = '$mid' and b.storageid = '$storageid'";
      return $this -> db -> fetch($sql);
   }

   //PODS 2.0으로 오아시스 옵션 데이터 보낼 데이터 가져오기 / 15.06.24 / kjm
   function getPodsOptionData($cartno, $cid) {
      $sql = "select a.goodsno, b.name, c.class_name, d.macro_name, a.storageid
               from tb_pretty_cart_mapping a, exm_member b, tb_pretty_class c, tb_pretty_child d
               where a.cartno = '$cartno' and a.class_ID = c.ID and a.child_ID = d.ID and b.mid = c.mid and b.cid = '$cid'";

      return $this -> db -> fetch($sql);
   }

   //tb_pretty_account_payno 테이블 insert      20150714    chunter
   function insertPrettyAccountPayno($cid, $mid, $payno, $account_money, $account_point, $storage_size, $storage_month, $storage_account_kind, $account_type, $account_detail_info, $account_pay_method) {
      $sql = "insert into tb_pretty_account_payno set
             payno                = '$payno',
             cid                  = '$cid',
             mid                  = '$mid',
             account_money        = '$account_money',
             account_point        = '$account_point',
             storage_size         = '$storage_size',
             storage_month        = '$storage_month',
             storage_account_kind = '$storage_account_kind',
             account_type         = '$account_type',
             account_detail_info  = '$account_detail_info',
             paymethod            = '$account_pay_method',
             regist_date          = now()";

      $this -> db -> query($sql);
   }

   //계약 수량, 편집 중 + 주문 완료 상품 숫자 가져오기 / 15.09.11 / kjm
   function getContractData($cid, $mid, $goodsno, $season_code = '') {
      if($season_code) $addWhere = "and a.season_code = '$season_code'";
      $sql = "select a.contract_ea, a.edit_goods_ea, a.order_goods_ea, b.goodsnm from tb_member_goods_mapping a
               inner join exm_goods b on a.goodsno = b.goodsno where a.cid = '$cid' and a.mid = '$mid' and a.goodsno = '$goodsno' $addWhere";

      list($contract_ea, $edit_goods_ea, $order_goods_ea, $goodsnm) = $this -> db -> fetch($sql, 1);

      $data[contract_ea] = $contract_ea;
      $data[edit_goods_ea] = $edit_goods_ea;
      $data[order_goods_ea] = $order_goods_ea;
      $data[goodsnm] = $goodsnm;

      return $data;
   }
   
   
   function chkContract($cid, $mid, $_POST_cartno, $_POST_flag = '') {

      $m_goods = new M_goods();
      
      //장바구니 번호 담기
      $cartno = $_POST_cartno;
      
      //재편집, 주문취소건 다시 주문 할때 계약수량 구하기
      //재편집건은 계약수량 변동없고 주문취소건만 계약수량 업데이트
      if($_POST_flag == "reorder"){
         //재편집, 취소건의 주문인 경우 장바구니 번호 초기화
         $cartno = '';

         $_POST_cartno = explode(",", $_POST_cartno);
         foreach($_POST_cartno as $k => $v){
            $cart_mapping_data = $this->getCartMappingData($v);

            //재편집 건이 아닐때
            if($cart_mapping_data[cart_state] != 'MR')
               $cartno[] = $v;
         }
         if($cartno)
            $cartno = implode(",", $cartno);
      }

      if($cartno){
         $list = $this->getCartGoodsCount($cartno);

         foreach($list as $key => $val){
            //계약 수량, 편집 중 + 주문 완료 상품 숫자 가져오기

            $contract_data = $this->getContractData($cid, $mid, $val[goodsno], $_COOKIE[season_code]);
     
            /*
            list($contract_ea, $order_goods_ea, $goodsnm) = $db->fetch("select a.contract_ea, a.order_goods_ea, b.goodsnm from tb_member_goods_mapping a
                                                                        inner join exm_goods b on a.goodsno = b.goodsno
                                                                        where a.cid = '$cid' and a.mid = '$sess[mid]' and a.goodsno = '$val[goodsno]'",1);
            */
            $data[$val[goodsno]][contract_ea] = $contract_data[contract_ea];
            $data[$val[goodsno]][tot_goods_ea] = $contract_data[order_goods_ea] + $val[cnt];
            $data[$val[goodsno]][goodsnm] = $contract_data[goodsnm];
         }

         foreach($data as $k => $v){
            //상품명이 없는 장바구니 상품(유치원, 상품 맵핑이 끊김)
            if(!$v[goodsnm]){
               //상품명 가져오기
               $goods_data = $m_goods -> getInfo($k);
               $ret_order_disable .= "$goods_data[goodsnm]\n";
            } else {
               if($v[contract_ea] < $v[tot_goods_ea]){
                  $up = $v[tot_goods_ea] - $v[contract_ea];
                  $ret_up .= "[$v[goodsnm]] "._('계약수량')." $v[contract_ea]"._('개').$up._('개 초과')."\n";
               } else {
                  $ret_down .= "[$v[goodsnm]] "._('계약수량')." $v[contract_ea]"._('개 중')." $v[tot_goods_ea]"._('개 주문')."\n";
               }
            }
         }
      }

      //유치원 연결이 끊긴 상품이 존재할 때
      if($ret_order_disable){
         $ret = _('다음 상품은 등록된 상품이 아니어서 주문할 수 없습니다.')."\n";
         $ret = "0|".$ret.$ret_order_disable;
      }
      //계약수량을 초과한 상품이 존재할 때
      else if($ret_up){
         $ret = _('다음 상품이 계약수량을 초과해서 주문할 수 없습니다.')."\n";
         $ret = "1|".$ret.$ret_up;
      }
      else if($ret_down) {
         $ret = _('상품의 남은 계약수량 정보입니다.');
         $ret = "2|".$ret.$ret_down;
      } else {
         $ret = "3|3";
      }
      
      return $ret;
   }

   /*
   //아래 getCartMappingData로 통합
   //재편집요청, 주문취소 시 작성한 메모 데이터 가져오기 / 15.10.16 / kjm
   function getReeditData($cartno) {
      $sql = "select reedit from tb_pretty_cart_mapping where cartno = '$cartno'";
      list($reedit) = $this -> db -> fetch($sql, 1);

      return $reedit;
   }
   
   //상품의 장바구니 상태값 가져오기 / 15.11.04 / kjm
   function getCartStateData($cartno) {
      $sql = "select cart_state from tb_pretty_cart_mapping where cartno = '$cartno'";
      list($cart_state) = $this -> db -> fetch($sql, 1);

      return $cart_state;
   }
   */
   
   //장바구니 맵핑 데이터 가져오기
   function getCartMappingData($cartno){
      $sql = "select * from tb_pretty_cart_mapping where cartno = '$cartno'";
      return $this -> db -> fetch($sql);
   }
   
   function setCartStateData($cartno, $cart_state){
      $sql = "update tb_pretty_cart_mapping set cart_state = '$cart_state', request_date = now() where cartno = '$cartno'";
      $this->db->query($sql);
   }

   //맵핑 상품의 편집, 주문수량 업데이트 / 15.10.16 / kjm
   function updateGoodsMappingEa($cid, $mid, $goodsno, $edit_goods_ea, $order_goods_ea, $db_type = "") {
      $sql = "update tb_member_goods_mapping set
               edit_goods_ea = edit_goods_ea + $edit_goods_ea,
               order_goods_ea = order_goods_ea - $order_goods_ea
              where cid = '$cid' and mid = '$mid' and goodsno = $goodsno
             ";
      //debug($sql);
      
      if($db_type){
         //include_once "../lib/class.db.mysqli.php";

         //$Mysqli = new DBMysqli(dirname(__FILE__)."/../conf/conf.db.php");
         
         $db_type -> query($sql);
      } else 
         $this -> db -> query($sql);
   }

   //장바구니 맵핑 데이터 - 재편집 요청, 주문취소 메모 & 장바구니 상품 상태 변경 / 15.10.16 / kjm
   function updateCartMappingData($cartno, $cart_state, $memo = '', $re_chk = false, $only_memo = false, $order_date = '') {
      $addUpdate = '';
      $addUpdate .= ", updatedt = now()";
      
      //메모 변경은 cart_state 값을 변경하지 않는다
      if ($cart_state){
         //주문완료로 변경 시 주문날짜도 업데이트
         if($cart_state == 'O')
            $addUpdate .= ", cart_state = '$cart_state', order_date = now()";
         else
            $addUpdate .= ", cart_state = '$cart_state'";
      }

      if($re_chk)
         $addUpdate .= ", re_cnt = re_cnt+1";

      if($order_date)
         $addUpdate .= ", order_date = now()";
      
      //메모 텍스트 클릭 후 메모 팝업창에서 내용만 수정 시 기존 메모에 수정만 되어야 한다.
      if($only_memo)
         $update = "reedit = '$memo'";
      //재편집, 주문취소 요청 시 여러건의 주문건 선택 후 메모 입력하면
      //각각 기존의 메모 내용 + 추가된 메모가 들어가야한다. / 15.11.23 / kjm
      else {
         if($memo)
            $update = "reedit = CONCAT_WS('\\n', reedit,'$memo')";
         else
            $update = "reedit = reedit";
      }

      $sql = "update tb_pretty_cart_mapping set
               $update
               $addUpdate
              where cartno = '$cartno'";
      $this -> db -> query($sql);
   }

   //상품 연결 및 가격 & 계약수량 수정 시 해당 상품의 편집 데이터, 주문 데이터 수량을 업데이트 한다 / 15.10.16 / kjm
   //마스터는 체크하지 않도록 조건 추가 & 수량 체크 쿼리 전반적으로 변경 /16.11.04 / kjm
   function setEditOrderCountCalc($cid, $mid, $goodsno, $season_code='') {
      $regdt_where = $this->getCartMappingRegdtWhere();
      $regdt_where_a = $this->getCartMappingRegdtWhere('a');
      
      if($season_code) $addWhere = "and season_code = '$season_code'";
      
      $sql = "select count(cartno) from tb_pretty_cart_mapping where (cart_state = 'E' or cart_state = 'C' or cart_state = 'R' or cart_state = 'CR')
               and cid= '$cid' and mid = '$mid' and goodsno = '$goodsno' and class_ID != 0 $addWhere $regdt_where";

      list($edit_num) = $this -> db -> fetch($sql, 1);

      //주문완료
      $sql = "SELECT sum(c.ea) as sum
               FROM tb_pretty_cart_mapping a
               inner join exm_ord_item c on a.cartno = c.cartno
               inner join exm_pay d on c.payno = d.payno
              WHERE a.cid= '$cid' AND a.mid = '$mid' AND a.goodsno = '$goodsno' and d.paystep >= 1 and (a.cart_state = 'O' or a.cart_state = 'M' or a.cart_state = 'MR') $addWhere $regdt_where_a";
      list($ord_num) = $this -> db -> fetch($sql, 1);
      
      if(!$ord_num) $ord_num = 0;
      
      $sql = "update tb_member_goods_mapping set
               edit_goods_ea = '$edit_num',
               order_goods_ea = '$ord_num'
              where cid = '$cid' and mid = '$mid' and goodsno = '$goodsno' $addWhere";
      $this -> db -> query($sql);
   }

   //원아 이름 체크
   function childNameChk($cid, $mid, $child_name, $service_year, $season_code = ''){
      if($season_code) $addWhere = "and season_code = '$season_code'";
      list($chk) = $this -> db -> fetch("select child_name from tb_pretty_child where cid = '$cid' and mid='$mid' and child_name = '$child_name' and service_year = '$service_year' $addWhere", 1);
      return $chk;
   }

   //편집, 장바구니, 주문 숫자 체크
   function chkEditNOrdNum($cid, $mid, $class_ID = '', $child_ID = ''){
      if($class_ID)
         $addWhere_class = "AND class_ID in ($class_ID)";

      if($child_ID)
         $addWhere_child = "AND child_ID in ($child_ID)";

      //편집
      list($edit_num_E) = $this -> db -> fetch("select count(cartno) from tb_pretty_cart_mapping
                                                where cart_state = 'E' and cid= '$cid' AND mid = '$mid' $addWhere_class $addWhere_child",1);

      //장바구니
      list($edit_num_R) = $this -> db -> fetch("select count(cartno) from tb_pretty_cart_mapping
                                                where cart_state = 'R' and cid= '$cid' AND mid = '$mid' $addWhere_class $addWhere_child",1);

      //주문완료
      list($ord_num) = $this -> db -> fetch("SELECT sum(c.ea) as sum
                                              FROM tb_pretty_cart_mapping a
                                              inner join exm_ord_item c on a.cartno = c.cartno
                                             WHERE a.cid= '$cid' AND a.mid = '$mid' $addWhere_class $addWhere_child",1);

      if($edit_num_E || $edit_num_R || $ord_num)
         return true;
      else
         return false;
   }

   //회원이 받은 파일 수, 크기 업데이트
   function updateFileDownData($cid, $mid, $file_cnt, $file_size){
      $sql = "update exm_member set
               down_file_cnt = down_file_cnt + '$file_cnt',
               down_file_size = down_file_size + '$file_size'
              where
               cid = '$cid' and mid = '$mid'";
      $this -> db -> query($sql);
   }

   //블루포토 기타 내역 관리
   function insertEtcHistory($cid, $mid, $msg='', $etc='', $file_count='', $file_size='', $flag=''){
      $sql = "insert into tb_pretty_etc_history set
               cid         = '$cid',
               mid         = '$mid',
               ip          = '$_SERVER[REMOTE_ADDR]',
               msg         = '$msg',
               etc         = '$etc',
               regist_date = now(),
               file_count  = '$file_count',
               file_size   = '$file_size',
               flag        = '$flag'
             ";
      $this -> db -> query($sql);
   }
   
   
   //폴더내에서 사진 분류 파일 리스트     20150306
   //$list_kind - all, not_group, group (전체, 미분류, 분류완료)
   function getClassImageList($cid, $mid, $service_year, $folder_ID, $list_kind = 'all') {
      //속도 개선 쿼리 변경       20150824    chunter
      $sql_select = "select c.* ";
      $sql_from = " from tb_pretty_class a";
      $sql_select_join = " inner join tb_pretty_file_folder_mapping b on b.folder_ID=a.ID";
      $sql_select_join .= " inner join tb_pretty_file c on c.file_id=b.file_id";
      $sql_where = " where a.cid='$cid' and a.mid = '$mid' and a.service_year='$service_year'";

      if ($folder_ID == 'D' || $folder_ID == 'F')
         $sql_where .= " and a.folder_kind = '$folder_ID'";
      else
         $sql_where .= " and a.ID = $folder_ID";

         $sql = $sql_select . $sql_from . $sql_select_join . $sql_where;

      return $this -> db -> listArray($sql);
   }
   
   //장바구니에 속해있는 상품들의 반 데이터 가져오기(cart_state에 따라 장바구니 or 재편집, 취소건)
   function getCartClassData($cid, $mid, $addWhere = '', $season_code = '') {
      if($season_code) $addWhereSeason = "and b.season_code = '$season_code'";

      $sql = "select c.* from tb_pretty_cart_mapping b
               inner join tb_pretty_class c on b.class_ID = c.ID
              where b.cid = '$cid' and b.mid = '$mid' $addWhere $addWhereSeason group by c.ID";
      return $this -> db -> listArray($sql);
   }
   
   //지사 1차 검수 완료 & 취소 처리 / 15.11.18 / kjm
   function setManagerInspectionUpdate($payno, $ordno, $ordseq, $manager_inspection) {
      //검수 완료 처리일 때만 날짜 업데이트
      if($manager_inspection == 'Y')
         $addWhere = ", manager_inspection_date = now()";
      else
         $addWhere = ", manager_inspection_date = ''";

      $sql = "update exm_ord_item set manager_inspection = '$manager_inspection' $addWhere where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'";
      $this->db->query($sql);
   }
   
   function setOrderAllowUpdate($payno, $ordno, $ordseq, $order_allow_chk){
      $sql =  "update exm_ord_item set order_allow_chk = '$order_allow_chk' where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'";
      $this->db->query($sql);
   }
   
   function setOrderAllowUpdate_v2($payno, $ordno, $ordseq, $order_allow_chk){
      $ordseq = substr($ordseq, 0, -1);

      $sql =  "update exm_ord_item set order_allow_chk = '$order_allow_chk' where payno = '$payno' and ordno = '$ordno' and ordseq in ($ordseq)";
      //$this->db->query($sql);
   }
   
   //장바구니에 담긴 상품들의 상품별 갯수 / 15.10.08 / kjm
   function getCartGoodsCount($cartno){
      $query = "select sum(ea) as cnt, goodsno from tb_pretty_cart_mapping where cartno in ($cartno) group by goodsno";
      return $this->db->listArray($query);
   }
   
   //공지사항 글 가져오기
   function getNoticeBoard($cid, $board_id, $addQuery){
      $query = "select * from exm_board where cid = '$cid' and board_id = '$board_id' $addQuery";
      return $this->db->fetch($query);
   }
   
   //공지사항 글 가져오기
   function getNoticeBoardList($cid, $board_id, $addQuery){
      $query = "select * from exm_board where cid = '$cid' and board_id = '$board_id' $addQuery";
      return $this->db->listArray($query);
   }
   
   //공지사항 글 갯수 가져오기
   function getNoticeBoardCount($cid, $board_id){
      $query = "select count(*) as cnt from exm_board where board_id = '$board_id' and cid = '$cid'";
      $totCnt = $this->db->fetch($query);
      return $totCnt[cnt];
   }
   
   //게시판 세팅 데이터 가져오기
   function getBoardSetting($cid, $board_id){
      $query = "select * from exm_board_set where cid = '$cid' and board_id = '$board_id'";
      return $this->db->fetch($query);
   }
   
   //FAQ분류가져오기
   function getFAQCatnm($cid) {
		$query = "select catnm from exm_faq where cid = '$cid' group by catnm";
		//debug($query);
		return $this->db->listArray($query);
   }
   
   //FAQ가져오기
   function getFAQ($cid, $addQuery){
      $query = "select * from exm_faq where cid = '$cid' $addQuery";
      return $this->db->listArray($query);
   }
   
   //FAQ 갯수 가져오기
   function getFAQCount($cid){
      $query = "select count(*) as cnt from exm_faq where cid = '$cid'";
      $totCnt = $this->db->fetch($query);
      return $totCnt[cnt];
   }
   
   //1:1문의 , 상품문의 문의 가져오기
   function getMycs($cid, $id, $addQuery){
      $query = "select * from exm_mycs where id = '$id' and cid = '$cid' $addQuery";
      return $this->db->listArray($query);
   }
   
   //1:1문의, 상품문의 갯수 가져오기
   function getMycsCount($cid, $id, $addQuery){
      $query = "select count(*) as cnt from exm_mycs where id = '$id' and cid = '$cid' $addQuery";
      $totCnt = $this->db->fetch($query);
      return $totCnt[cnt];
   }
   
   //1:1문의 , 상품문의 수정(답변)
   function mycsModify($cid, $no){
      $query = "select a.*, b.name as admin_name from exm_mycs a left join exm_admin b on b.cid = '$cid' and a.reply_mid = b.mid where a.cid = '$cid' and no = '$no'";
      return $this->db->fetch($query,1);
   }
   
   function getFileData($cid, $no, $board_id){
      $r_file = array();
      $query = "select * from exm_board_file where pno = $no";
      $res = $this->db->query($query);
      while ($file_data = $this->db->fetch($res)){
         $file_data[size] = getImageSize("../../data/board/$cid/$board_id/$file_data[filesrc]");
         $r_file[] = $file_data;
      }
      return $r_file;
   }

   function getDivideList($cid, $mid, $service_year, $file_id, $mapping_kind_type){
      if($mapping_kind_type == 'N') $db_table = "tb_pretty_file_child_mapping";
      else if($mapping_kind_type == 'G') $db_table = "tb_pretty_file_child_graduation_mapping";

      $sql = "select b.* from $db_table a
               inner join tb_pretty_child b on a.child_ID = b.ID
               where a.file_id = '$file_id' and b.service_year = '$service_year'
             ";
      return $this->db->listArray($sql);
   }

   //주문취소관련 생성
   function setOrderItemStepUpdate($payno, $ordno, $ordseq, $itemstep){
      $sql =  "update exm_ord_item set itemstep = '$itemstep' where payno = '$payno' and ordno = '$ordno' and ordseq = '$ordseq'";
      $this->db->query($sql);
   }

   //트리구조 아이디 가져오기
   //해당 폴더와 하위 모든 폴더 아이디 가져오기
   function getTreeIds($ID){
      //해동 폴더의 하위 폴더가 있는지 체크
      $chk_child_folder = $this->getChildTreeFolderID($ID);

      //폴더가 있으면 하위 폴더 체크
      if($chk_child_folder){
         $folder_tree_id_one = implode(",", $chk_child_folder);

         //하위 폴더의 하위 폴더가 있는지 체크
         $chk_child_folder_two = $this->getChildTreeFolderID($folder_tree_id_one);
         if($chk_child_folder_two){
            $folder_tree_id_two = implode(",", $chk_child_folder_two);

            $tree_ids = $ID.",".$folder_tree_id_one.",".$folder_tree_id_two;
         } else {
            $tree_ids = $ID.",".$folder_tree_id_one;
         }
         return $tree_ids;
      } else {
         return $ID;
      }
   }

   function getFolderNameTree($ID){
      $data_three = $this->getFolderData_s2($ID);

      if($data_three[parent_folder_ID] == 0) return $data_three[folder_name];
      else{
         $data_two = $this->getFolderData_s2($data_three[parent_folder_ID]);

         if($data_two[parent_folder_ID] == 0) return $data_two[folder_name]." > ".$data_three[folder_name];
         else{
            $data_one = $this->getFolderData_s2($data_two[parent_folder_ID]);
            //debug($data_one);
            if($data_one[parent_folder_ID] == 0) return $data_one[folder_name]." > ".$data_two[folder_name]." > ".$data_three[folder_name];
         }
      }
   }

   //폴더 데이터 가져오기
   function getFolderData_s2($ID){
      $data = $this->db->ListFetch("select * from tb_pretty_folder where ID = $ID",1);
      return $data;
   }

   
   //트리구조 파일 리스트 가져오기
   function getFileFolderMappingParent($ID, $limit){
      return $this->db->listArray("select a.* from tb_pretty_file a 
                                   inner join tb_pretty_file_folder_mapping b on a.file_id = b.file_id
                                   where b.folder_id in ($ID) limit $limit, 20");
   }
   
   
   function getChildTreeFolderID($ID){
      $data = $this->db->listArray("select ID from tb_pretty_folder where parent_folder_id in ($ID)");
      foreach($data as $val){
         $item[] = $val[ID];
      }
      
      return $item;
   }
   
   function getFolderFileCnt($ids, $mode){
      if($mode == "total") $sel = "sum(file_cnt)";
      else if($mode == "not_group") $sel = "sum(not_group_file_cnt)";

      list($sum) = $this->db->fetch("select $sel from tb_pretty_folder where ID in ($ids)",1);
      return $sum;
   }
   
   //삭제할 폴더 ID 찾기
   function getDeleteFolderIDs($ID){
      $low_rank_folder = $this -> getTreeIds($ID);

      $low_rank_folder = explode(",", $low_rank_folder);

      if($low_rank_folder) {
         //for문을 돌면서 폴더에 이미지가 없는 것만 찾는다
         foreach($low_rank_folder as $val){
            $count = $this->getFileCnt($val, 'folder');
            if($count[cnt] == 0 || $count[cnt] == '')
            $folder_ID[] = $val;
         }
         
         //이미지 없는 폴더들 ,로 묶기
         if($folder_ID){
            $folder_ID = implode(",", $folder_ID);
         }
      }

      if($folder_ID) return $folder_ID;
   }
   
   //pm : plus, minus
   //폴더 이동 시 정렬 값 수정
   function updateFolderOrder($pm, $get_orderby, $folder_orderby, $folder_id, $service_year, $cid, $mid){
      $m_pretty = new M_pretty();
      
      if($pm == "plus"){
         $count = "+ 1";
         $add_where = "orderby >= '$get_orderby' and orderby < '$folder_orderby'";
      } else {
         $count = "- 1";
         $add_where = "orderby > '$folder_orderby' and orderby <= '$get_orderby'";
      }
      
      $query = "update tb_pretty_folder set orderby = orderby $count where $add_where and parent_folder_ID = '$folder_id'";
      //debug($query);
      $this->db->query($query);
      /*
      //이동 하는 폴더의 사진 갯수 업데이트
      $data = $m_pretty -> getFileCnt_s2($folder_id, 'folder');
      $m_pretty -> updateFileCnt($data[cnt], $folder_id, 'folder');

      //폴더 데이터(미분류 사진 포함)
      $folder_data = $m_pretty -> getFolderImageCountGroupByFolder($cid, $mid, $service_year, '', $folder_id, not_group);
      //미분류 사진 tb_pretty_folder 테이블에 업데이트
      $m_pretty -> updateFolderNotGroupImageCount($cid, $mid, $folder_id, $folder_data[0][count]);
      */
   }

   //폴더가 몇번째 뎁스인지 가져오기
   function getFolderDepth($ID) {
      $data = $this->getParentFoldeID($ID);
      if(!$data || $data == '0'){
         $depth = "1";
      } else {
         $data_2 = $this->getParentFoldeID($data);
         if(!$data_2 || $data_2 == '0'){
            $depth = '2';
         } else $depth = '3';
      }
      return $depth;
   }

   //폴더의 부모 아이디 찾기
   function getParentFoldeID($ID){
      list($data) = $this->db->fetch("select parent_folder_ID from tb_pretty_folder where ID = '$ID'",1);
      return $data;
   }
   
   function getLowRankFolderTree($ID){
      //폴더가 몇번째 뎁스까지 있는지 찾는다.
      $chk_folder_depth = $this->getChildTreeFolderID($ID);
      
      //폴더에 하위 폴더가 있을 때
      if($chk_folder_depth){
         //하위 폴더가 있으면 최소 2
         $move_folder_depth = 2;
         foreach($chk_folder_depth as $key => $val){
            $second_depth = $this->getChildTreeFolderID($val);
            //하위 폴더의 하위 폴더가 있으면 3
            if($second_depth) $move_folder_depth = 3;
         }
      } else
         //하위 폴더가 없으면 트리는 1
         $move_folder_depth = 1;

      return $move_folder_depth;
   }
   
   //상품별 편집률 가져오기
   function getEditRate($cartno){
      $cart = new cart(array($cartno));
   
      foreach ($cart->item as $key => $value) {
         foreach ($value as $itemkey => $itemvalue) {
            
            if ($itemvalue[edit_progress])
               $edit_rate = $itemvalue[edit_progress];
            else if(!$itemvalue[edit_progress] && !$itemvalue[storageid])
               $edit_rate = "0%";
            else
               $edit_rate = "100%";
         }
      }
   
      //% 제거
      $edit_rate = str_replace("%", "", $edit_rate);

      return $edit_rate;
   }
   
   function getCartMappingRegdtWhere($join_data = ''){
      global $cfg;

      if($join_data)
         $join_data = $join_data.".";

      $now = date("Y-m-d H:i:s");

      if(!$cfg[kids_service_year]) $cfg[kids_service_year] = "04-01";

      if($cfg[kids_service_year] != '12-31' && $now >= '2016-'.$cfg[kids_service_year]){
         $regdt_where = "and ".$join_data."regist_date >= '2016-".$cfg[kids_service_year]."'";
      }
      return $regdt_where;
   }
   
   function getCartMappingOrddtWhere(){
      global $cfg;

      $now = date("Y-m-d H:i:s");

      if(!$cfg[kids_service_year]) $cfg[kids_service_year] = "04-01";

      if($cfg[kids_service_year] != '12-31' && $now >= '2016-'.$cfg[kids_service_year]){
         $order_where = "and orddt >= '2016-".$cfg[kids_service_year]."'";
      }
      
      return $order_where;
   }
   
   function setFolderOrderby($cid, $mid, $parents_ID){
      $i = 0;

      $folder_list = $this->db->listArray("select * from tb_pretty_folder where cid = '$cid' and mid = '$mid' and service_year = '2017' and parent_folder_ID = '$parents_ID' and mapping_kind_type = 'N' order by orderby");

      if($folder_list){
         foreach($folder_list as $key => $val){
            $query_orderby = "update tb_pretty_folder set orderby = '$i' where ID = $val[ID]";

            $this->db->query($query_orderby);

            $i++;
         }
      }
   }

   function getPrettyCartMappingChk($storageid){
      list($data) = $this->db->fetch("select storageid from tb_pretty_cart_mapping where storageid = '$storageid'",1);
      return $data;
   }
   
   function getMemberListA($cid, $addWhere='', $orderby='', $limit='', $bQuery=false){
      $query = "select *,(
          select sum(y.payprice) totpayprice from 
              exm_pay x
              inner join exm_ord_item y on y.payno = x.payno
          where 
              cid = a.cid and mid = a.mid and itemstep in (2, 3, 4, 5, 92)
      ) totpayprice
       from exm_member a where cid='$cid' and sort <= 0 $addWhere $orderby $limit";

      if ($bQuery) return $query;
      else return $this->db->listArray($query);
   }

   function getMemberListA2($cid, $addWhere='', $orderby='', $limit='', $bQuery=false){
      $query = "select * from exm_member a where cid='$cid' and sort <= 0 $addWhere $orderby";
      if ($bQuery) return $query;
      else return $this->db->listArray($query);
   }

   function getMemberListA_cnt($cid, $addWhere='', $orderby='', $limit='', $bQuery=false){
      $query = "select count(mid) as cnt,(
          select sum(y.payprice) totpayprice from 
              exm_pay x
              inner join exm_ord_item y on y.payno = x.payno
          where 
              cid = a.cid and mid = a.mid and itemstep in (2, 3, 4, 5, 92)
      ) totpayprice
       from exm_member a where cid='$cid' and sort <= 0 $addWhere $orderby $limit";

      $cnt = $this->db->fetch($query);
      return $cnt[cnt];
   }
   
   /*function getMemberListA_cnt($cid, $addWhere='', $orderby='', $limit=''){
      $query = "select count(mid) as cnt from exm_member a where cid='$cid' and sort <= 0 $addWhere $orderby $limit";
	  $cnt = $this->db->fetch($query);
      return $cnt[cnt];
   }*/
   
   function getRequestListChart_A($cid, $addWhere = '', $orderby = '', $limit = '', $inner_join_flag = ''){
      if($inner_join_flag == 'Y'){
         $inner_query = "inner join exm_ord b on b.payno = a.payno 
                         inner join exm_ord_item c on c.payno = a.payno and c.ordno = b.ordno";
      }

      $query = "select * from exm_pay a
                $inner_query
                where a.cid = '$cid' $addWhere $orderby $limit
               ";

      return $this->db->listArray($query);
   }

   function getSeasonList($cid, $start = "", $limit = "", $orderby = "", $now = ""){
      if($start && $limit) $addLimit = "limit $start, $limit";
      if($now) $addWhere = " and start_date <= '$now' and end_date >='$now'";
      $query = "select * from tb_pretty_season where cid = '$cid' $addWhere $orderby $addLimit";
      return $this->db->listArray($query);
   }
   
   function getCartSeasonData($cid, $mid){
      $query = "select * from tb_pretty_season where season_code in (select b.season_code from tb_pretty_cart_mapping b
                  inner join tb_pretty_class c on b.class_ID = c.ID
                 where b.cid = '$cid' and b.mid = '$mid' and (b.cart_state = 'MR' or b.cart_state = 'CR')  group by b.season_code
                )";
      return $this->db->listArray($query);
   }

   function getSeasonData($cid, $season_code = ''){
      if($season_code) $addWhere = "and season_code = '$season_code'";
      $query = "select * from tb_pretty_season where cid = '$cid' $addWhere";
      return $this->db->fetch($query);
   }

   function getSeasonTotalCnt($cid){
      $query = "select count(cid) from tb_pretty_season where cid = '$cid'";
      list($m_cnt) = $this->db->fetch($query, 1);
      return $m_cnt;
   }
   
   function getBasicSeasonCode($cid, $now = ""){
      if($now) $addWhere = " and start_date <= '$now' and end_date >='$now'";
      
      //기본시즌이 있는지 체크
      list($basic_code) = $this->db->fetch("select season_code from tb_pretty_season where cid = '$cid' and basic_flag = 'Y' $addWhere",1);

      //기본시즌이 있으면 시즌코드 리턴
      if($basic_code)
         return $basic_code;
      else {
         //기본시즌이 없으면 가장 최근 시즌코드 리턴
         list($basic_code) = $this->db->fetch("select max(season_code) from tb_pretty_season where cid = '$cid' $addWhere order by regist_date desc",1);

         return $basic_code;
      }
   }

   function getLastSeasonCode($cid, $year, $code){
      $table = "tb_pretty_".$code;
      $query = "select max(season_code) from $table where cid = '$cid' and service_year = '$year'";
      list($max_season_code) = $this->db->fetch($query,1);
      return $max_season_code;
   }

   function prettyCopyClass($cid, $year, $season_code) {

      $copy_season_code = $this->getLastSeasonCode($cid, $year, "class");

      $query = "INSERT IGNORE INTO tb_pretty_class (cid,mid,teacher_id,class_name,regist_date,service_year,orderby,season_code,org_ID)
                  SELECT cid,mid,teacher_id,class_name,now(),service_year,orderby,$season_code,ID
                FROM tb_pretty_class where cid = '$cid' and service_year = '$year' and season_code = $copy_season_code";
      //debug($query);
      $this->db->query($query);
   }

   function prettyCopyChild($cid, $year, $season_code) {

      $copy_season_code = $this->getLastSeasonCode($cid,  $year, "child");

      $query = "INSERT IGNORE INTO tb_pretty_child (cid,mid,child_name,macro_name,child_birth,class_ID,regist_date,orderby,file_cnt,file_cnt_graduation,service_year,parents,parents_mobile,parents_email,gradu_code,season_code)
                  SELECT a.cid,a.mid,a.child_name,a.macro_name,a.child_birth,b.ID,now(),a.orderby,0,0,a.service_year,a.parents,a.parents_mobile,a.parents_email,a.gradu_code,$season_code
                FROM tb_pretty_child a inner join tb_pretty_class b on a.class_ID = b.org_ID where a.cid = '$cid' and a.service_year = '$year' and a.season_code = $copy_season_code and a.class_ID != 0";
      
      
      //원아 복사 후 반 데이터도 복사된 반의 ID로 업데이트 해준다.
      $data = $this->db->listArray("select class_ID from tb_pretty_child where cid = '$cid' and service_year = '$year' and season_code = '$season_code' group by class_ID");
      foreach($data as $val){
         $sql = "UPDATE
                   tb_pretty_child a
                 left join(
                   select ID,org_ID
                   from tb_pretty_child
                   where org_ID = '$val[class_ID]'
                  ) b on a.class_ID = b.org_ID 
                 SET
                   a.class_ID = b.ID
                 WHERE
                   a.class_ID = '$val[class_ID]' and season_code = '$season_code'";
         $this->db->query($sql);
      }
      $this->db->query($query);
   }

   function prettyCopyFolder($cid, $year, $season_code) {

      $copy_season_code = $this->getLastSeasonCode($cid,  $year, "folder");

      $query = "INSERT IGNORE INTO tb_pretty_folder (service_year,folder_name,folder_kind,regist_date,update_date,orderby,file_cnt,not_group_file_cnt,mid,cid,mapping_kind_type,parent_folder_ID,season_code,org_ID)
                  SELECT service_year,folder_name,folder_kind,now(),update_date,orderby,0,0,mid,cid,mapping_kind_type,parent_folder_ID,$season_code,ID
                FROM tb_pretty_folder where cid = '4483' and service_year = '$year' and season_code = $copy_season_code";
      $this->db->query($query);

      //기존 데이터 복사 후 부모 아이디도 복사된 값의 아이디로 업데이트 한다.
      $data = $this->db->listArray("select parent_folder_ID from tb_pretty_folder where cid = '$cid' and service_year = '$year' and season_code = '$season_code' and parent_folder_ID != 0 group by parent_folder_ID");
      foreach($data as $val){
         $sql = "UPDATE
                   tb_pretty_folder a
                 left join(
                   select ID,org_ID
                   from tb_pretty_folder
                   where org_ID = '$val[parent_folder_ID]'
                  ) b on a.parent_folder_ID = b.org_ID 
                 SET
                   a.parent_folder_ID = b.ID
                 WHERE
                   a.parent_folder_ID = '$val[parent_folder_ID]' and season_code = '$season_code'";
         $this->db->query($sql);
      }
   }
   
   function getMainCnt($cid, $mid, $regdt_where, $mode, $season_code = '') {
      $addWhere = "";
      if($mode == "edit") $addWhere .= " and class_ID <> '' and cart_state = 'E'";
      else if($mode == "cart") $addWhere .= " and cart_state = 'R'";
      
      if($season_code) $addWhere .= " and season_code = '$season_code'";

      list($cnt) = $this -> db -> fetch("select count(cartno) as cartCnt from tb_pretty_cart_mapping
                                   where cid = '$cid' and mid = '$mid' $addWhere $regdt_where", 1);
      return $cnt;
   }
   
   function getSeasonDeleteOrder($limit){
      $addLimit = "limit $limit";
      $query = "select storageid from tb_pretty_season_order_delete $addLimit";
      //debug($query);
      return $this->db->listFetch($query);
   }
}
?>