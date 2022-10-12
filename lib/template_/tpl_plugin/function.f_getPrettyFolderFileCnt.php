<?
function f_getPrettyFolderFileCnt($ID, $mode){
	
	global $db,$cid;
   $m_pretty = new M_pretty();
   switch ($mode) {
      case 'total':
         $ids = $m_pretty -> getTreeIds($ID);
         $count = $m_pretty -> getFolderFileCnt($ids, $mode);
         break;

      case 'not_group':
         $ids = $m_pretty -> getTreeIds($ID);
         $count = $m_pretty -> getFolderFileCnt($ids, $mode);
         break;
   }
   
   return $count;
}

?>