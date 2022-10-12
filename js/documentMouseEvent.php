<?
include_once dirname(__FILE__)."/../conf/language_locale.php";				//다국어 파일 처리. 다국어 처리하는곳에만 실제 처리 로직을 넣는다.
?>
document.ondragstart = function (e) {
	var elt = window.event? event.srcElement: e.target;
	if (elt.id == "orderQtyNoOption" || elt.id == "orderQty" || elt.id == "hdivItemTitle")
		return true;
	else
		return false;
};
document.onselectstart = function (e) {
	var elt = window.event? event.srcElement: e.target;
	if (elt.id == "orderQtyNoOption" || elt.id == "orderQty" || elt.id == "sns_url_box" || elt.id == "hdivItemTitle")
		return true;
	else
		return false;
};
document.oncontextmenu = function (e) {
	var elt = window.event? event.srcElement: e.target;
	if (elt.id == "orderQtyNoOption" || elt.id == "orderQty" || elt.id == "sns_url_box" || elt.id == "hdivItemTitle")
		return true;
	else
	{
		alert('<?echo _("상품상세설명 무단도용방지를 위하여")?>' + "\n" + '<?echo _("마우스 오른쪽버튼을 이용할 수 없습니다!")?>');
		return false;
	}
};