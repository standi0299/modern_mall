<?
include "../../lib/library.php";

switch ($_REQUEST[mode]){
	
	case "goods_detail_copy":
		
		$auth_code = $_POST[auth_code];
		$o_goods_code = $_POST[o_goods_code]; 
		$t_catno = $_POST[t_catno];
		$t_catno_sub = $_POST[t_catno_sub];
		$t_goods_code = $_POST[t_goods_code];		
		
		if (! $o_goods_code) msg("필수정보를 입력해주세요", -1); 
		
		//보안코드 검증
		$auth_code = md5_decode($auth_code,1);
		$auth_code = substr($auth_code,3,19);
		$timestamp = date("Y-m-d H:i:s", strtotime("$auth_code +180 seconds"));
		$nowTime = date("Y-m-d H:i:s");
		
		$timeDiff = dateTimeDiff($timestamp, $nowTime, "s", true);
		
		//인증코드 시간 차이가 딱 3분안에 들어야 한다.		
		if ($timeDiff > 0 && $timeDiff < 180)
		{		
			$m_goods = new M_goods();
			//자신의 상품인지 체크
			$gData = $m_goods->getGoodsInfo($cid, $o_goods_code);
			if (!$gData)
			{
				msg("상품 정보가 없습니다.", -1);
				break;
			} 
		
			//현재 카테고리 복사
			if ($_POST[copy_range] == "c_cate")
			{
				$gData = $m_goods->setGoodsDetailUpdateWithGoodsCategory($cid, $o_goods_code);			
			}
			else if ($_POST[copy_range] == "category")
			{
				if (! $t_catno) msg("필수정보를 입력해주세요", -1);
				
				//하위 카테고리 포함해서 전체 적용 여부 확인
				$bChildInclude = false;
				if ($t_catno_sub == "Y")
					$bChildInclude = true;
				$m_goods->setGoodsDetailUpdateWithCategory($cid, $o_goods_code, $t_catno, $bChildInclude); 
			}
			//특정상품 복사
			else if ($_POST[copy_range] == "goodscode")
			{
				if (! $t_goods_code) msg("필수정보를 입력해주세요", -1);
				
				$m_goods->setGoodsDetailUpdateWithGoodsNo($cid, $o_goods_code, $t_goods_code);
			}	
		} else {
			msg("인증코드가 만료되었습니다.", -1);			
		}
	
		break;
}

	//$db->end_transaction();
		
//echo($_SERVER[HTTP_REFERER]);
go($_SERVER[HTTP_REFERER]);

?>