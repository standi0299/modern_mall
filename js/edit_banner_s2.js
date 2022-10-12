$j(window).load(function(){
	var edit_banner_btn = document.createElement('div');
	edit_banner_btn.innerHTML = " E D I T ";
	//$j(edit_banner_btn).css("z-index","99999");
	$j(edit_banner_btn).css("z-index","1000002");
	$j(edit_banner_btn).css("position","absolute");
	$j(edit_banner_btn).css("background","orange");
	$j(edit_banner_btn).css("color","#FFFFFF");
	$j(edit_banner_btn).css("padding","2px 2px");
	$j(edit_banner_btn).css("cursor","pointer");
	$j(edit_banner_btn).css("font-size","8pt");
	$j(edit_banner_btn).appendTo("body");
	$j(edit_banner_btn).hide();
	
	
	var edit_textbanner_btn = document.createElement('div');
	edit_textbanner_btn.innerHTML = "TEXT EDIT";
	$j(edit_textbanner_btn).css("z-index","99999");
	$j(edit_textbanner_btn).css("position","absolute");
	$j(edit_textbanner_btn).css("background","purple");
	$j(edit_textbanner_btn).css("color","#FFFFFF");
	$j(edit_textbanner_btn).css("padding","1px 2px");
	$j(edit_textbanner_btn).css("cursor","pointer");
	$j(edit_textbanner_btn).css("font-size","8pt");
	$j(edit_textbanner_btn).appendTo("body");
	$j(edit_textbanner_btn).hide();
	
	
	var admin_edit_banner_btn = document.createElement('div');
	admin_edit_banner_btn.innerHTML = " A D M I N - C O N F I G ";
	//$j(admin_edit_banner_btn).css("z-index","99999");
	$j(admin_edit_banner_btn).css("z-index","1000002");
	$j(admin_edit_banner_btn).css("position","absolute");
	$j(admin_edit_banner_btn).css("background","green");
	$j(admin_edit_banner_btn).css("color","#FFFFFF");
	$j(admin_edit_banner_btn).css("padding","2px 2px");
	$j(admin_edit_banner_btn).css("cursor","pointer");
	$j(admin_edit_banner_btn).css("font-size","8pt");
	$j(admin_edit_banner_btn).appendTo("body");
	$j(admin_edit_banner_btn).hide();
	
	var code, add_type, banner_type, add_css, click_action;
	$j("._banner").mouseover(function(){
		code = $j(this).attr("code");
		banner_type = $j(this).attr("banner_type");
		add_type = $j(this).attr("add_type");
		add_css = $j(this).attr("add_css");
		//click_action = $j(this).attr("click_action");
		
		if (banner_type == undefined || banner_type == "undefined") banner_type = "";
		if (add_type == undefined || add_type == "undefined") add_type = "Y";
		if (add_css == undefined || add_css == "undefined") add_css = "";
		 		
		$j(edit_banner_btn).css("top",$j(this).offset().top);
		$j(edit_banner_btn).css("left",$j(this).offset().left);
		$j(edit_banner_btn).show();
	});
	$j("._banner").mouseout(function(){
		$j(edit_banner_btn).hide();
	});
	$j(edit_banner_btn).mouseover(function(){
		$j(edit_banner_btn).show();
	});
	$j(edit_banner_btn).mouseout(function(){
		$j(edit_banner_btn).hide();
	});
	$j(edit_banner_btn).click(function(){
		$j(edit_banner_btn).hide();				
		popup('/a/module/set.banner.s2.php?code='+code+'&banner_type='+banner_type+'&add_type='+add_type+'&add_css='+add_css,1024,800);		
	});
	
	
	
	//text 배너가 추가 되었다			20180622		chunter	
	$j("._banner_text").mouseover(function(){
		code = $j(this).attr("code");		
		$j(edit_textbanner_btn).css("top",$j(this).offset().top);
		$j(edit_textbanner_btn).css("left",$j(this).offset().left);
		$j(edit_textbanner_btn).show();
	});
	$j("._banner_text").mouseout(function(){
		$j(edit_textbanner_btn).hide();
	});
	$j(edit_textbanner_btn).mouseover(function(){
		$j(edit_textbanner_btn).show();
	});
	$j(edit_textbanner_btn).mouseout(function(){
		$j(edit_textbanner_btn).hide();
	});
	$j(edit_textbanner_btn).click(function(){
		$j(edit_textbanner_btn).hide();		
		popup('/a/module/set.banner.s2.php?code='+code+'&banner_type=text',1024,800);					
	});
	
		
	var config_type = "";
	$j("._admin_config").mouseover(function(){
		code = $j(this).attr("code");
		config_type = $j(this).attr("config_type");
		//click_action = $j(this).attr("click_action");
		
		if (config_type == undefined || config_type == "undefined") config_type = "";		 		
		$j(admin_edit_banner_btn).css("top",$j(this).offset().top);
		$j(admin_edit_banner_btn).css("left",$j(this).offset().left);
		$j(admin_edit_banner_btn).show();
	});
	$j("._admin_config").mouseout(function(){
		$j(admin_edit_banner_btn).hide();
	});
	$j(admin_edit_banner_btn).mouseover(function(){
		$j(admin_edit_banner_btn).show();
	});
	$j(admin_edit_banner_btn).mouseout(function(){
		$j(admin_edit_banner_btn).hide();
	});
	$j(admin_edit_banner_btn).click(function(){
		$j(admin_edit_banner_btn).hide();				
		popup('/a/module/admin_popup_module.php?not_header=Y&code='+code+'&config_type='+config_type,1280,1024);		
	});

		
});