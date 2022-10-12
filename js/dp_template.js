/*
 * 메인 페이지 템플릿 추천 기능 (템플릿 전시)
 * skin : classic, spring , pod_group
 * 2016.05.12 by kdk
 * */
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
	$j(".dp_template").mouseover(function(){
		dpcode = $j(this).attr("dpcode");
		$j(edit_dp_btn).css("top",$j(this).offset().top);
		$j(edit_dp_btn).css("left",$j(this).offset().left);
		$j(edit_dp_btn).show();
	});
	$j(".dp_template").mouseout(function(){
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
		popup('../admin/module/set.dp.template.php?dpcode='+dpcode,800,650);
	});
	
	//추가
	var add_dp_btn = document.createElement('div');
	add_dp_btn.innerHTML = "ADD";
	$j(add_dp_btn).css("z-index","99999");
	$j(add_dp_btn).css("position","absolute");
	$j(add_dp_btn).css("background","orange");
	$j(add_dp_btn).css("color","#FFFFFF");
	$j(add_dp_btn).css("padding","1px 2px");
	$j(add_dp_btn).css("cursor","pointer");
	$j(add_dp_btn).appendTo("body");
	$j(add_dp_btn).hide();
	var dp_code;
	var goodsno; 
	var templateSetIdx; 
	var templateIdx; 
	var templateName; 
	var templateURL; 
	var url;
	
	$j(".dp_template_add").mouseover(function(){
		dp_code = $j(this).attr("dpcode");
		goodsno = $j(this).attr("goodsno");
		templateSetIdx = $j(this).attr("templateSetIdx");
		templateIdx = $j(this).attr("templateIdx");
		templateName = $j(this).attr("templateName");
		templateURL = $j(this).attr("templateURL");
		url = $j(this).attr("url");
		
		$j(add_dp_btn).css("top",$j(this).offset().top);
		$j(add_dp_btn).css("left",$j(this).offset().left);
		$j(add_dp_btn).show();
	});
	$j(".dp_template_add").mouseout(function(){
		$j(add_dp_btn).hide();
	});
	$j(add_dp_btn).mouseover(function(){
		$j(add_dp_btn).show();
	});
	$j(add_dp_btn).mouseout(function(){
		$j(add_dp_btn).hide();
	});
	$j(add_dp_btn).click(function(){
		$j(add_dp_btn).hide();
		
		//alert(dp_code + "," + goodsno + "," + templateSetIdx + "," + templateIdx + "," + templateName + "," + templateURL + "," + url);
		
		//메인페이지 출력 위치 선택
		var param = {};
		
		var tn = Base64.encode(templateName);
		tn = tn.replace("+","@");
		param["templateName"] = tn;
		
		//param["templateName"] = Base64.encode(templateName);
		param["img"] = Base64.encode(templateURL);
		param["url"] = Base64.encode(url);

		popup('../admin/module/set.dp.template_code_select.php?dpcode='+dp_code+'&goodsno='+goodsno+'&param='+JSON.stringify(param),500,300);
		
		//ajax 처리
		/*
		$j.post("/admin/module/indb.php", {
				mode:"insert_dp_template_ajax",
				dpcode:dp_code,
				goodsno:goodsno,
				//templateSetIdx:templateSetIdx,
				//templateIdx:templateIdx,
				templateName:templateName,
				img:templateURL,
				url:url
			}, function(data){
			if (data){
				alert('메인페이지에 진열상품(템플릿)으로 등록 되었습니다.');
			} else {
				//var tit = "BluePod";
			}
		});
		*/
	});
	
	
});