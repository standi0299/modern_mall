
//사용자 필터 링크
function _filterincate()
{
	var argulenth = arguments.length;
	var filter = "";
	if (argulenth > 0)
	{
		for (var i=0; i < argulenth; i++) {
			if (i == 0) _filtercatno = arguments[i];		//첫번째 파라미터는 카테고리코드
			else filter = filter + arguments[i] + "|";
		}		
	}	
	getGoodsListFilder(filter, "", "");
}


//상품리스트에서 필터 기능 사용하기.
function getGoodsListFilder(filter, refilter, rerefilter)
{	
	location.href="/goods/list.php?catno=" + _filtercatno + "&filter=" + filter + "&seconfilter=" + refilter + "&thirdfilter=" + rerefilter;
}
