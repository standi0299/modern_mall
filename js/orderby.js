function _set_orderby(_get_orderby){
	
	/* 검색폼명은 searchFm 으로 정의
	searchFm 은 orderby 라는 input 값을 가져야함
	각 클릭객체는 _orderby 클래스를 가지며, name attribute 로 orderby field 를 정의하게 된다. */
	if (!document.searchFm) return;

	var _get_orderby_arr = _get_orderby.split(" ");
	$j("._orderby").click(function(){
		if ($j(this).attr("name")==null) return;
		else {
			var orderby = $j(this).attr("name");
			if (orderby && orderby==_get_orderby) orderby = orderby+" desc";
			document.searchFm.orderby.value = orderby;
			document.searchFm.submit();
		}
	});

	$j("._orderby").each(function(){
		$j(this).css("cursor","pointer");
		if ($j(this).attr("name")==_get_orderby_arr[0]){
			var orderbyhead = (_get_orderby_arr[1]=="desc") ? "<img src='../img/icn_triangle_dn_s.png'/>":"<img src='../img/icn_triangle_up_s.png'/>";
			$j(this).html(orderbyhead+" <b>"+$j(this).html()+"</b>");
		}
	});
}