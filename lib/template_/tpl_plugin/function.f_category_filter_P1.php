<?

function f_category_filter_P1(){
	
	global $db,$cid,$cfg,$sess_admin,$P1_F_category;
	$index = 10;		
	$p1_sub_cate = substr($_GET[catno], 0, 3);
	if ($P1_F_category[$p1_sub_cate])
	{
		foreach ($P1_F_category[$p1_sub_cate] as $key => $value) {
			if ($value == str_replace("|","",$_GET[filter]))
				$p1_sub_catetory[active]	= "checked";
			else
				$p1_sub_catetory[active]	= "";
			$p1_sub_catetory[catnm]	= "$value";
			$p1_sub_catetory[catno]	= "$key";
			$p1_sub_catetory[idx]	= "$index";
			$category[] = $p1_sub_catetory;
			
			$index++;
		}
	} else {
		$p1_sub_cate = substr($_GET[catno], 0, 6);
		if ($P1_F_category[$p1_sub_cate])
		{
			foreach ($P1_F_category[$p1_sub_cate] as $key => $value) {
				if ($value == str_replace("|","",$_GET[filter]))
					$p1_sub_catetory[active]	= "checked";
				else
					$p1_sub_catetory[active]	= "";
				$p1_sub_catetory[catnm]	= "$value";
				$p1_sub_catetory[catno]	= "$key";
				$p1_sub_catetory[idx]	= "$index";
				$category[] = $p1_sub_catetory;
				
				$index++;
			}	
		}
	}	
	
  //debug($category);
	return $category;
}
?>