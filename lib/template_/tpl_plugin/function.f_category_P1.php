<?

function f_category_P1($categoryLinkAddRootClass = ''){

	global $db,$cid,$cfg,$sess_admin,$P1_T_category;
	$ret = array();

	foreach ($P1_T_category as $key => $value) {
		$data[category_link_tag] = "/goods/list.php?catno=$key&intro_flag=Y";
		$data[catnm] = $value;
		$data[catno] = $key;

		$category[$key] = $data;
	}

  //debug($category);
	return $category;
}
?>