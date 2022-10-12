<?

function f_getCategorySub($catno){

	global $db,$cid,$cfg;
	$loop = array();
	$depth = strlen($catno)/3 + 1;
	$query = "select * from exm_category where cid = '$cid' and catno like '$catno%' and length(catno)=$depth*3 and hidden=0 order by sort";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		
		if ($data[left_img]){
			if ($data[file_path])
				$img_file_path = "..".$data[file_path]."/";
			else
				$img_file_path = "../data/category/".$cfg[skin]."/";
			
			if (is_file($img_file_path.$data[left_img])){
				//$onmouseevent = ($data[left_oimg] && is_file($img_file_path.$data[left_oimg])) ? "onmouseover=this.src='$img_file_path$data[left_oimg]' onmouseout=this.src='$img_file_path$data[left_img]'" : "";
				
				if ($data[left_oimg] && is_file($img_file_path.$data[left_oimg])) 
				{
					$onmouseevent = "onmouseover=this.src='$img_file_path$data[left_oimg]' onmouseout=this.src='$img_file_path$data[left_img]'";
					$data[left_oimg_tag] = $img_file_path . $data[left_oimg];
				} 
				else
				{
					$onmouseevent = "";
					$data[left_oimg_tag] = "";
				}
				
				$data[left_img] = "<img src='$img_file_path$data[left_img]' $onmouseevent>";
			} else {
				unset($data[left_img]);
			}
		}
		//debug($data);
		//exit;
        //링크 주소 만들기. (db 부하를 줄이자)  20140403    chunter
        $data[category_link_tag] = get_category_anchor_from_arr($data);

		$loop[] = $data;
	}
	return $loop;

}

?>