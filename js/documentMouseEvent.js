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
		alert(tls("상품상세설명 무단도용방지를 위하여") + "\n" + tls("마우스 오른쪽버튼을 이용할 수 없습니다!"));
		return false;
	}
};