<?

function f_category($categoryLinkAddRootClass = ''){
	
	global $db,$cid,$cfg,$sess_admin;
	$ret = array();
	
	//20160125 / minks / 스킨별로 모바일전용 카테고리 여부 체크(m_default : mobile / 그 외 스킨 : pc)
	$is_mobile = ($cfg[skin] == "m_default") ? 1 : 0;
	
	/*if ($sess_admin[admin]) $query = "select * from exm_category where cid = '$cid' and hidden=0 order by length(catno),sort";
	else $query = "select * from exm_category where cid = '$cid' and is_mobile = '$is_mobile' and hidden=0 order by length(catno),sort";*/
	$query = "select * from exm_category where cid = '$cid' and is_mobile = '$is_mobile' and hidden=0 order by length(catno),sort";
	$res = $db->query($query);
	$category = array();

    $dir = "../data/category/$cid/$cfg[skin]";

	while ($data = $db->fetch($res)){
        if ($data[file_path])
        $dir = ".." .$data[file_path];

		if ($data[img] && is_file("$dir/$data[img]")){
			$onmouseevent = ($data[oimg] && is_file("$dir/$data[oimg]")) ? "onmouseover=this.src='$dir/$data[oimg]' onmouseout=this.src='$dir/$data[img]'" : "";
			$data[img] = "<img src='$dir/$data[img]' $onmouseevent>";
		} else {
			unset($data[img]);
		}
    
        //링크 주소 만들기. 1차,2차 카테고리만  -> 전체 카테고리에서 (db 부하를 줄이자)  20140403    chunter
        //if (strlen($data[catno]) == 3 || strlen($data[catno]) == 6 || strlen($data[catno]) == 9)
        //{
        if (strlen($data[catno]) == 3)	
        	$data[category_link_tag] = get_category_anchor_from_arr($data, "", $categoryLinkAddRootClass);
				else
					$data[category_link_tag] = get_category_anchor_from_arr($data);    
        //}
          
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