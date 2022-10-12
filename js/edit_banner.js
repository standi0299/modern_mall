$j(window).load(function(){
	var edit_banner_btn = document.createElement('div');
	edit_banner_btn.innerHTML = "EDIT";
	$j(edit_banner_btn).css("z-index","99999");
	$j(edit_banner_btn).css("position","absolute");
	$j(edit_banner_btn).css("background","blue");
	$j(edit_banner_btn).css("color","#FFFFFF");
	$j(edit_banner_btn).css("padding","1px 2px");
	$j(edit_banner_btn).css("cursor","pointer");
	$j(edit_banner_btn).css("font-size","8pt");
	$j(edit_banner_btn).appendTo("body");
	$j(edit_banner_btn).hide();
	
	
	var edit_textbanner_btn = document.createElement('div');
	edit_textbanner_btn.innerHTML = "EDIT";
	$j(edit_textbanner_btn).css("z-index","99999");
	$j(edit_textbanner_btn).css("position","absolute");
	$j(edit_textbanner_btn).css("background","green");
	$j(edit_textbanner_btn).css("color","#FFFFFF");
	$j(edit_textbanner_btn).css("padding","1px 2px");
	$j(edit_textbanner_btn).css("cursor","pointer");
	$j(edit_textbanner_btn).css("font-size","8pt");
	$j(edit_textbanner_btn).appendTo("body");
	$j(edit_textbanner_btn).hide();

	var edit_textmsgbanner_btn = document.createElement('span');
	edit_textmsgbanner_btn.innerHTML = "EDIT";
	$j(edit_textmsgbanner_btn).css("z-index","99999");
	$j(edit_textmsgbanner_btn).css("position","absolute");
	$j(edit_textmsgbanner_btn).css("background","orange");
	$j(edit_textmsgbanner_btn).css("color","#FFFFFF");
	$j(edit_textmsgbanner_btn).css("padding","1px 2px");
	$j(edit_textmsgbanner_btn).css("cursor","pointer");
	$j(edit_textmsgbanner_btn).css("font-size","8pt");
	$j(edit_textmsgbanner_btn).appendTo("body");
	$j(edit_textmsgbanner_btn).hide();

	var code;
	$j("._banner").mouseover(function(){
		code = $j(this).attr("code");
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
		
		if (code == 'main_top_backgroud')
			popup('../a/module/set.banner.backgroud.php?code='+code,800,800);
		//else if(code == 'top_menu_banner')		//bizcard 도 같이 사용.(프린트홈 전용 배너 코드가 아니다.)
			//popup('../admin/module/set.banner.menu.php?code='+code,800,650);
		else
			popup('../a/module/set.banner.php?code='+code,800,800);
	});

	$j(".banner_width_span","._banner").each(function(){
		//$j(this).parent().slideUp();
		$j(this).html(($j(this).parent().width()+2)+"px");
	});
	
	
	//text 배너가 추가 되었다			20140708		chunter
	var add_type, image_type, text_cnt, banner_type;
	$j("._banner_text").mouseover(function(){
		code = $j(this).attr("code");
		add_type = $j(this).attr("add_type");
		image_type = $j(this).attr("image_type");
		text_cnt = $j(this).attr("text_cnt");
		banner_type = $j(this).attr("banner_type");
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
		if (add_type == null || add_type == 'undefined')		
			popup('../a/module/set.banner.text.php?code='+code,800,800);
		else if (banner_type == 's2')
			popup('../a/module/set.banner.s2.php?code='+code+'&add_type='+add_type+'&image_type='+image_type+'&text_cnt='+text_cnt+'&banner_type='+banner_type,1024,450);
		else		
			popup('../a/module/set.banner.ph.php?code='+code+'&add_type='+add_type+'&image_type='+image_type+'&text_cnt='+text_cnt,800,450);
	});

	$j(".banner_width_span","._banner_text").each(function(){
		//$j(this).parent().slideUp();
		$j(this).html(($j(this).parent().width()+2)+"px");
	});


	//About/Contact Us/Deposit Account 하단 정보 클릭시 관리자에서 변경하라는 메세지 (이메일, 예금주 등등) 2015.02.24 by kdk
	$j("._banner_text_msg").mouseover(function(){
		code = $j(this).attr("code");
		$j(edit_textmsgbanner_btn).css("top",$j(this).offset().top);
		$j(edit_textmsgbanner_btn).css("left",$j(this).offset().left);
		$j(edit_textmsgbanner_btn).show();
	});
	$j("._banner_text_msg").mouseout(function(){
		$j(edit_textmsgbanner_btn).hide();
	});
	
	$j(edit_textmsgbanner_btn).mouseover(function(){
		$j(edit_textmsgbanner_btn).show();
	});
	$j(edit_textmsgbanner_btn).mouseout(function(){
		$j(edit_textmsgbanner_btn).hide();
	});
	$j(edit_textmsgbanner_btn).click(function(){
		$j(edit_textmsgbanner_btn).hide();
		
		alert(tls("프린트홈 관리자 화면 \'관리메뉴->기본설정\'에서 수정하기 바랍니다."));
	});

	$j(".banner_width_span","._banner_text_msg").each(function(){
		//$j(this).parent().slideUp();
		$j(this).html(($j(this).parent().width()+2)+"px");
	});	
	
	
	$j('._banner_img_map').mouseover(function(e){
		$j(edit_banner_btn).css("top",$j(this).offset().top);
		$j(edit_banner_btn).css("left",$j(this).offset().left);
		$j(edit_banner_btn).show();
  });
    
	$j('._banner_img_map').mouseout(function(){
		$j(edit_banner_btn).hide();
	});
	
});