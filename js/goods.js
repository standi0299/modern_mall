function refresh_price(){
	var goodsno = $j("#goodsno").val();
	var optno = $j("select[name='optno[]']:last").val();

	//통합형 커버 아이디 만들기 / 18.01.25 / kjm
	var cover_range = $j("select[name='cover_range']").val();
	if(!cover_range) cover_range = '';

	var cover_type = $j("select[name='cover_type']").val();
	if(!cover_type) cover_type = '';

	var cover_paper_code = $j("select[name='cover_paper_code']").val();
	if(!cover_paper_code) cover_paper_code = '';

	var cover_coating_code = $j("select[name='cover_coating_code']").val();
	if(!cover_coating_code) cover_coating_code = '';

	var cover_id = '';
	if(cover_range && cover_type && cover_paper_code && cover_coating_code){
		cover_id = cover_range+"_"+cover_type+"_"+cover_paper_code+"_"+cover_coating_code;
	}

	var addopt = [];
	var addopt_idx = 0;

	$j("select[name='addopt[]']").each(function(){
		if ($j(this).val() && $j(this).val()!=""){
			addopt[addopt_idx] = $j(this).val();
		}
		addopt_idx++;
	});
	
	//좌측 옵션
	var qoptno = $j("select[name='quick-optno[]']");
	$j("select[name='optno[]']").each(function(index){
		$j(qoptno[index]).val($j(this).val());
	});
	var qaddopt = $j("select[name='quick-addopt[]']");
	$j("select[name='addopt[]']").each(function(index){
		$j(qaddopt[index]).val($j(this).val());
	});

	addopt = addopt.join(",");
	
	//common.defer.js
	get_goods_price(goodsno,optno,addopt,"set_price_str",cover_id); //가격조회 ajax
	get_goods_cprice(goodsno,optno,addopt,"set_cprice_str"); //소비자가 ajax
	get_goods_reserve(goodsno,optno,addopt,"set_reserve_str"); //적립금 ajax
}

function refresh_price2(){
	var goodsno = $j("#goodsno").val();
	var qoptno = $j("select[name='quick-optno[]']:last").val();
	var qaddopt = [];
	var qaddopt_idx = 0;
	$j("select[name='quick-addopt[]']").each(function(){
		if ($j(this).val() && $j(this).val()!=""){
			qaddopt[qaddopt_idx] = $j(this).val();
		}
		qaddopt_idx++;
	});

	//우측 옵션 편집기 호출이 안된다는 요청으로 임시 조치 원인 파악이 안됨.
	var optno = $j("select[name='optno[]']");
	$j("select[name='quick-optno[]']").each(function(index){

		$j(optno[index]).find("option").remove();
		$j(optno[index]).append("<option value='' >" + tls("선택") + "</option>");
		
		$j(this).find("option").each(function() {
			if(this.value) {
				if(index>0) {
					$j(optno[index]).append("<option value='" + this.value +"' >" + this.text +"</option>");
				}
				else {
					$j(optno[index]).append("<option title='" + $j(this).attr("title") +"' value='" + this.value +"' productid='" + $j(this).attr("productid") +"' podoptno='" + $j(this).attr("podoptno") +"' stock='" + $j(this).attr("stock") +"' >" + this.text +"</option>");
				}
			}
		});

		$j(optno[index]).val($j(this).val());
		$j(optno[index]).siblings('label').text($j(this).children("option:selected").text());
	});

	var addopt = $j("select[name='addopt[]']");
	$j("select[name='quick-addopt[]']").each(function(index){
		$j(addopt[index]).val($j(this).val());
		$j(addopt[index]).siblings('label').text($j(this).children("option:selected").text());
	});

	qaddopt = qaddopt.join(",");

	//common.defer.js
	get_goods_price(goodsno,qoptno,qaddopt,"set_price_str"); //가격조회 ajax
	get_goods_cprice(goodsno,qoptno,qaddopt,"set_cprice_str"); //소비자가 ajax
	get_goods_reserve(goodsno,qoptno,qaddopt,"set_reserve_str"); //적립금 ajax
}

function refresh_price_M2(){
	var goodsno = $j("#goodsno").val();
   //var optno = $j("select[name='optno[]_sub']").val();
   
   /*
   var optno = $j(":input:radio[name=optno[]_sub]:checked").val();

   if(typeof(optno) == "undefined")
   	var optno = $j(":input:radio[name=optno[]]:checked").val();
	*/
	
	var chk_opt_sub = false; 
	$j(":input:radio[name=optno[]_sub]").each(function(){		
		if($j(this).val() && $j(this).val() == $j(":input:radio[name=optno[]]:checked").val()){
			chk_opt_sub = true;
		}
   });
   
   if(chk_opt_sub == true) {
   	var optno = $j(":input:radio[name=optno[]_sub]:checked").val();
   } else {
   	var optno = $j(":input:radio[name=optno[]]:checked").val();
   }

   var addopt = [];
   var addopt_idx = 0;

   $j("[id^='addopt_']").each(function(){ 
      if ($j(this).val() && $j(this).val()!="" && $j(this).is(':checked') == true){
         addopt[addopt_idx] = $j(this).val();
         addopt_idx++;
      }
   });

   addopt = addopt.join(",");
   /*
   alert(goodsno);
   alert(optno);
   alert(addopt);
   */
   
   //common.defer.js
   get_goods_price(goodsno,optno,addopt,"set_price_str"); //가격조회 ajax
   get_goods_cprice(goodsno,optno,addopt,"set_cprice_str"); //소비자가 ajax
   get_goods_reserve(goodsno,optno,addopt,"set_reserve_str"); //적립금 ajax
}

function set_price_str(ret){
	var ea = $j("input[name=ea]").val();
   ret = ret * ea;
	$j("#goods_price").html(comma(ret)+tls("원"));

	//좌측 옵션보기
	$j("#quick-goods_price").html(comma(ret)+tls("원"));
}

function set_cprice_str(ret){
	$j("#goods_cprice").html(comma(ret)+tls("원"));
	var cprice = 0;
	var price = parseInt($j("#goods_price").html().replace(",",""));
	if ($j("#goods_cprice").html()){
		cprice = parseInt($j("#goods_cprice").html().replace(",",""));
	}
	var gap = cprice - price;
	if (gap > 0){
		$j("#goods_cprice_x").html("(▼ "+comma(gap)+tls("원 할인")+")");
	} else {
		$j("#goods_cprice_x").html("");
	}
	
	//좌측 옵션보기
	$j("#quick-goods_cprice").html($j("#goods_cprice").html());
	$j("#quick-goods_cprice_x").html($j("#goods_cprice_x").html());
}

function set_reserve_str(ret){
	$j("#goods_reserve").html(comma(ret));
	
	//좌측 옵션보기
	$j("#quick-goods_reserve").html(comma(ret));
}

function updateOption(obj){
	var goodsno = $j("#goodsno").val();
	var selected_opt = obj.options[obj.selectedIndex];

	if ($j(selected_opt).attr("productid") && $j(selected_opt).attr("productid")!=0){
		$j("input[name=productid]").val($j(selected_opt).attr("productid"));
	} else {
		$j("input[name=productid]").val("{podsno}");
	}
	if ($j(selected_opt).attr("podoptno")){
		$j("input[name=podoptno]").val($j(selected_opt).attr("podoptno"));
	} else {
		$j("input[name=podoptno]").val("");
	}

	var opt = obj.form["optno[]"][1];
	if (opt.tagName!="SELECT" || obj==opt) return;
	opt.options.length = 1;

	if ($j("select[name='quick-optno[]']").length) {
		var q_opt = obj.form["quick-optno[]"][1];
		if (q_opt.tagName!="SELECT" || obj==q_opt) return;
		q_opt.options.length = 1;
	}

	$j.post("indb.php", {mode:"ajax_updateOption", goodsno:goodsno, opt1:obj.options[obj.selectedIndex].title}, function(data){
		data = evalJSON(data);
		for (var i=0;i<data.length;i++){
			var txt = data[i].opt2;
			if (data[i].aprice!="0") txt += " (+" + comma(data[i].aprice) + tls("원")+")";
			opt[i+1] = new Option(txt, data[i].optno);
			opt[i+1].stock = data[i].stock;
			opt[i+1].productid = data[i].podsno;
			opt[i+1].podoptno = data[i].podoptno;
			
			if ($j("select[name='quick-optno[]']").length) {
				q_opt[i+1] = new Option(txt, data[i].optno);
				q_opt[i+1].stock = data[i].stock;
				q_opt[i+1].productid = data[i].podsno;
				q_opt[i+1].podoptno = data[i].podoptno;
			}
		}
	});	
}

//리스트 이미지 클릭시 확대 이미지 변경
function listImgOnClick(imgSrc) {
	var ez = jQuery1_11_0('#big_img').data('elevateZoom');
	ez.swaptheimage(imgSrc, imgSrc);
}