<?
/*
 * 메인 페이지 템플릿 추천 기능 (템플릿 전시)
 * skin : spring , pod_group
 * 2016.05.12 by kdk
 * */
function f_dp_template($dpcode){

	global $db,$cfg_center,$cfg,$cid,$sess;
	$loop = array();

	$dp_ = $db->fetch("select * from exm_goods_dp where cid = '$cid' and dpno = '$dpcode'");

	$limit = $dp_[rows]*$dp_[cells];

	$query = "select * from tb_template_dp_link where dpno = '$dpcode' and cid = '$cid'	order by seq limit $limit ";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		
		//2013.12.09 / minks / 조건문 추가(상품명이 20byte이상이고 goodsnm_cut_flag가 Y일 경우 상품명을 20byte까지만 보여주고 나머지 경우는 상품명을 그대로 보여줌)
		//2013.12.18 / minks / mb_substr은 문자단위로 변수명을 자름 -> 상품명이 20byte이상이고 goodsnm_cut_flag가 Y일 경우 상품명을 13문자까지만 보여주고 나머지 경우는 상품명을 그대로 보여줌(수정)
		if(strlen($data[templatename]) > 20 && $dp_[goodsnm_cut_flag] == "Y"){			
			$data[templatename] = mb_substr($data[templatename], 0, 13, "UTF-8");
		}
		else{
			$data[templatename] = $data[templatename];
		}
		
		$loop[] = $data;
	}

	$dp_[cells] = ($dp_[cells]) ? $dp_[cells]:$cfg[cells];
	$dp_[listimg_w] = ($dp_[listimg_w]) ? $dp_[listimg_w]:$cfg[listimg_w];

	$GLOBALS[tpl]->assign('dpcode',$dpcode);
	$GLOBALS[tpl]->assign('loopBox',$loop);
	$GLOBALS[tpl]->assign('dp_',$dp_);
	$tpl = "/goods/_list.box.template.htm";
	$GLOBALS[tpl]->define('box',$tpl);
	$GLOBALS[tpl]->print_('box');
}

?>