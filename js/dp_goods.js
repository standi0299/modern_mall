$j(function(){
	var edit_dp_btn = document.createElement('div');
	edit_dp_btn.innerHTML = "EDIT";
	$j(edit_dp_btn).css("z-index","99999");
	$j(edit_dp_btn).css("position","absolute");
	$j(edit_dp_btn).css("background","red");
	$j(edit_dp_btn).css("color","#FFFFFF");
	$j(edit_dp_btn).css("padding","1px 2px");
	$j(edit_dp_btn).css("cursor","pointer");
	$j(edit_dp_btn).appendTo("body");
	$j(edit_dp_btn).hide();
	var dpcode;
	$j(".dp_goods").mouseover(function(){
		dpcode = $j(this).attr("dpcode");
		$j(edit_dp_btn).css("top",$j(this).offset().top);
		$j(edit_dp_btn).css("left",$j(this).offset().left);
		$j(edit_dp_btn).show();
	});
	$j(".dp_goods").mouseout(function(){
		$j(edit_dp_btn).hide();
	});
	$j(edit_dp_btn).mouseover(function(){
		$j(edit_dp_btn).show();
	});
	$j(edit_dp_btn).mouseout(function(){
		$j(edit_dp_btn).hide();
	});
	$j(edit_dp_btn).click(function(){
		$j(edit_dp_btn).hide();
		popup('../admin/module/set.dp.php?dpcode='+dpcode,800,650);
	});
});