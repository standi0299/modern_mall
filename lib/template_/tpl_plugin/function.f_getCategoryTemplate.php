<?
function f_getCategoryTemplate($catno){
	
	global $db,$cid,$cfg,$sess_admin;
	$ret = array();

	if (!$catno) return;

	$query = "select * from md_template_category where cid = '$cid' and catno like '$catno%' and hidden=0 order by length(catno),sort";

	$res = $db->query($query);
	$category = array();

	while ($data = $db->fetch($res)){
    
        ////링크 주소 만들기. 1차,2차 카테고리만  -> 전체 카테고리에서 (db 부하를 줄이자)  20140403    chunter
        ////if (strlen($data[catno]) == 3 || strlen($data[catno]) == 6 || strlen($data[catno]) == 9)
        ////{
        //if (strlen($data[catno]) == 3)	
        //	$data[category_link_tag] = get_category_anchor_from_arr($data, "", $categoryLinkAddRootClass);
		//		else
		//			$data[category_link_tag] = get_category_anchor_from_arr($data);    
        ////}
        
        $data[category_link_tag] = "javascript:searchTags('$data[catnm]');";

		switch (strlen($data[catno])){
			case "3":
				$category[$data[catno]] = $data;
				break;
			case "6":
				if ($category[substr($data[catno],0,3)]){
					$category[substr($data[catno],0,3)][sub][$data[catno]]= $data;
				}
				break;
			case "9":
				if ($category[substr($data[catno],0,3)][sub][substr($data[catno],0,6)]){
					$category[substr($data[catno],0,3)][sub][substr($data[catno],0,6)][sub][$data[catno]] = $data;
				}
				break;
			case "12":
				if ($category[substr($data[catno],0,3)][sub][substr($data[catno],0,6)][sub][substr($data[catno],0,9)]){
					$category[substr($data[catno],0,3)][sub][substr($data[catno],0,6)][sub][substr($data[catno],0,9)][sub][$data[catno]] = $data;
				}
			break;
		}
	}
    //debug($category);
	return $category;
}

?>