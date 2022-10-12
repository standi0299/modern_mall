<?

//include "../_pheader.php";
include "../_header.php";

$m_member = new M_member();
$m_order = new M_order();


### 아이템정보추출
$data = $m_order->getOrderDataInfo($cid, $sess[mid], $_GET[payno], $_GET[ordno], $_GET[ordseq]);


//if (!$data) msg(_("잘못된 접근경로입니다."), -1);

### 리뷰추출
$review = $m_member->getReviewInfo($cid, $_GET[payno], $_GET[ordno], $_GET[ordseq]);

$checked[degree][$review[degree]] = "checked";
$checked[kind][$review[kind]] = "checked";
$checked[review_deny_user][$review[review_deny_user]+0] = "checked";

if (!$review[degree]) $checked[degree][5] = "checked";
if (!$review[kind]) $checked[kind]['normal'] = "checked";

if($cfg[skin_theme] != "P1"){
   $tpl->assign($data);
}
$tpl->assign('review',$review);
$tpl->print_('tpl');

?>