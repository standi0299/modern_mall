

//사용자 필터 링크
function _filter()
{
	var argulenth = arguments.length;
	var filter = "";
	if (argulenth > 0)
	{
		for (var i=0; i < argulenth; i++) {
			filter = filter + arguments[i] + "|";
		}		
	}	
	getGoodsListFilder(filter, "", "");
}

function _filterfilter()
{
	var argulenth = arguments.length;
	var filterfilter = "";
	if (argulenth > 0)
	{
		for (var i=0; i < argulenth; i++) {
			filterfilter = filterfilter + arguments[i] + "|";
		}		
	}
	getGoodsListFilder(_filterfirst,filterfilter, "");
}

function _filterfilterfilter()
{
	var argulenth = arguments.length;
	var filterfilterfilter = "";
	if (argulenth > 0)
	{
		for (var i=0; i < argulenth; i++) {
			filterfilterfilter = filterfilterfilter + arguments[i] + "|";
		}		
	}
	getGoodsListFilder(_filterfirst,_filtersecond,filterfilterfilter);
}

