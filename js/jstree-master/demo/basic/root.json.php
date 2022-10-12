<?
include_once "../../../_header.php";
include_once "../../../lib/class.page.php";
include_once "../../../pretty_s2/_module_util.php";
header('Content-Type: application/json');
$m_pretty = new M_pretty();

$folderListParent = $m_pretty -> getFolderListParentFolder($cid, $sess[mid], 2016, $_GET[mapping_kind], 0);

//해당 트리구조는 루트폴더생성이 안되어서 최상위 루트폴더를 임의로 생성해놓는다. / 16.07.29 / kjm
$folderDepthList[id] = "0";
$folderDepthList[text] = "전체폴더";

foreach($folderListParent as $key=>$val){
   $tree_one[id] =  $val[ID];
   $tree_one[text] =  $val[folder_name];

   $folderDepthList[children][] = $tree_one;

   $tree_two_data = $m_pretty -> getFolderListParentFolder($cid, $sess[mid], 2016, $_GET[mapping_kind], $val[ID]);
   foreach($tree_two_data as $k => $v){
      $tree_two[id] = $v[ID];
      $tree_two[text] = $v[folder_name];
      $folderDepthList[children][$key][children][] = $tree_two;

      $tree_three_data = $m_pretty -> getFolderListParentFolder($cid, $sess[mid], 2016, $_GET[mapping_kind], $v[ID]);

      foreach($tree_three_data as $kk => $vv){
         $tree_three[id] = $vv[ID];
         $tree_three[text] = $vv[folder_name];
         $folderDepthList[children][$key][children][$k][children][] = $tree_three;
      }
   }
}
//debug($folderDepthList);
$json = json_encode($folderDepthList);
echo"$json";
?>