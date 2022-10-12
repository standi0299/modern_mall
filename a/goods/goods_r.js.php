<script type="text/javascript">
var oldshipping = <?=$data[shiptype]?>;

$j(function(){

   /* 옵션사용유무 클릭이벤트 */
   $j("[name=useopt]").click(function(e){
      if ($j(this).val()==1){
         $j("#setopt_div").show();
         $j('#mod_opt_btn').show();
         $j("#easy_setopt").show();
         $j("#opt_tb").show();
      } else {
         $j("#setopt_div").hide();
         $j('#mod_opt_btn').hide();
         $j("#easy_setopt").hide();
         $j("#opt_div").hide();
         $j("#opt_tb").hide();
      }
   });
   /* 옵션사용유무 클릭 트리거 */
   $j("input:radio[name=useopt]:checked").trigger("click");

   /* 쉬운세팅 클릭이벤트 */
   $j("#easy_setopt").click(function(e){
      if ($j("#opt_div").css("display")=="none"){
         $j("#opt_div").css("left",$j(this).offset().left);
         $j("#opt_div").css("top",$j(this).offset().top+18);
         $j("#opt_div").fadeIn();
      } else {
         $j("#opt_div").fadeOut();
      }
   });

   /* 배송비 클릭이벤트 */
   $j("[name=shiptype]:radio").click(function(){
      $j("#shipping_"+oldshipping).css("display","none");
      oldshipping = $j(this).val();
      $j("#shipping_"+oldshipping).css("display","block");
   });
   /* 배송비 클릭 트리거 */
   $j("input:radio[name=shiptype]:checked").trigger("click");

   $j("#bt_link").click(function(){
      setCategory();
   });

});

/* 카테고리 선택 함수 */
var selectCatno = [];
function setCategory(){
   if (!catno.value){
      alert('<?=_("분류를 선택해주세요")?>');
      return;
   }
   var err = false;
   if ($j("#link_div input")){
      $j("#link_div input").each(function(){
         if ($j(this).val()==catno.value){
            err = true;
         }
      });
      if (err){
         alert('<?=_("이미 선택된 분류입니다")?>');
         return;
      }
   }
   var obj = $j("#category select");
   var selectCatnm = [];
   for (var i=0;i<obj.length;i++){
      if (!$j("option:selected",obj[i]).val()) continue;
      selectCatnm[i] = $j("option:selected",obj[i]).text();
      selectCatno[selectCatno.length] = catno.value;
   }
   var innerContent = "<div style='margin:2px 0;height:19px;'><img src='../img/bt_del.png' class='absmiddle hand' onclick='$j(this).parent().remove();'><span class='absmiddle'> "+selectCatnm.join(" > ")+"</span><input type='hidden' name='ncatno[]' value='"+catno.value+"'></div>";
   $j(innerContent).appendTo($j("#link_div"));
}

/* 옵션 쉬운세팅 */
var opt_select = "<select name='opt_view[]'><option value='0'>Y<option value='0'>N</select>";
var opt_modify = "<input type='radio' name='dummy'/>";
function set_opt(){
   var opt1 = $j("input[name=add_opt1str]").val();
   var opt2 = $j("input[name=add_opt2str]").val();
   var opt1 = opt1.split(",");
   var opt2 = opt2.split(",");

   var obj = $j("#opt_tb");
   $j("tr:eq(0)",obj).nextAll('tr').remove();
   for (var i=0;i<opt1.length;i++){

      for (var j=0;j<opt2.length;j++){
         var tr = document.createElement("tr");
         $j(tr).attr("align","center");
         $j(tr).attr("class","form-inline");
         $j(tr).appendTo(obj);
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html(opt_modify);
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html("<input type='text' class='form-control' name='opt1[]' value='"+opt1[i]+"' size='12' pt='_pt_txt'/>")
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html("<input type='text' class='form-control' name='opt2[]' value='"+opt2[j]+"' size='12' pt='_pt_txt'/>");
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html("<input type='text' class='form-control' name='podsno[]' size='12' pt='_pt_numplus'/><img src='../img/bt_check_s.png' onclick='popup(\"http://<?=PODS10_DOMAIN?>/ProductInfo30/code_mapping.aspx?siteid=<?=$mall_info[siteid]?>&userid=<?=$mall_info[podsid]?>\",770,415)'/>");
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html("<input type='text' class='form-control' name='podoptno[]' size='10'/>");
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html("<input type='text' class='form-control' name='aprice[]' size='10' pt='_pt_numplus'/>");
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html("<input type='text' class='form-control' name='asprice[]' size='10' pt='_pt_numplus'/>");
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html("<input type='text' class='form-control' name='aoprice[]' size='10' pt='_pt_numplus'/>");
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html("<input type='text' class='form-control' name='opt_cprice[]' size='10' pt='_pt_numplus'/>");
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html("<input type='text' class='form-control' name='stock[]' size='3' pt='_pt_numplus'/>");
         var td = document.createElement("td");
         $j(td).appendTo($j(tr));
         $j(td).html(opt_select);
      }
   }
   $j("#opt_div").fadeOut("slow");
   _pt_set();
}

/* 옵션 추가 */
function add_more_opt(){
   var obj = $j("#opt_tb");
   var goodsno = "<?=$_GET[goodsno]?>";
   var pods_use = $j("input[name=pods_use]:checked").val();
   
   var tr = document.createElement("tr");
   $j(tr).attr("align","center");
   $j(tr).appendTo(obj);
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html(opt_modify);
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html("<input type='text' class='form-control' name='opt1[]' value='' pt='_pt_txt'/>");
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html("<input type='text' class='form-control' name='opt2[]' value='' pt='_pt_txt'/>");
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html("<input type='text' class='form-control' name='podsno[]' /><a class=\"btn btn-success btn-icon btn-circle btn_get_productid\" onclick='popup(\"http://<?=PODS10_DOMAIN?>/ProductInfo30/code_mapping.aspx?siteid=<?=$mall_info[siteid]?>&userid=<?=$mall_info[podsid]?>\",770,415)'><i class=\"fa fa-check\"></i></a>");   
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html("<input type='text' class='form-control' name='podoptno[]' />");
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html("<input type='text' class='form-control' name='aprice[]' pt='_pt_numplus'/>");
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html("<input type='text' class='form-control' name='asprice[]' pt='_pt_numplus'/>");
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html("<input type='text' class='form-control' name='aoprice[]' pt='_pt_numplus'/>");
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html("<input type='text' class='form-control' name='opt_cprice[]' pt='_pt_numplus'/>");
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html("<input type='text' class='form-control' name='stock[]' pt='_pt_numplus'/>");
   var td = document.createElement("td");
   $j(td).appendTo($j(tr));
   $j(td).html(opt_select);
   
   if (goodsno != "") {
   	   var td = document.createElement("td");
	   $j(td).appendTo($j(tr));
	   $j(td).html("");
   } 
   
   if (pods_use != "1") $j(".btn_get_productid").css("display","none");
   
   _pt_set();
}

/* 옵션 삭제 */
function remove_opt(){
   $j("input:radio[name=dummy]:checked").parent().parent().remove();
}

/* 인화옵션 추가 */
function add_printopt(){
   var obj = $j("#printopt_tb");
   obj.css("display","block");
   var tr = document.createElement("tr");
   $j(tr).attr("align","center");
   $j(tr).appendTo(obj);
   var td = document.createElement("td");
   td.innerHTML = "<input type=\"radio\" name=\"dummy_printopt\"/>";
   $j(td).appendTo($j(tr));
   var td = document.createElement("td");
   td.innerHTML = "<input type=\"text\" class=\"form-control\" name=\"printoptnm[]\" pt=\"_pt_txt\"/>";
   $j(td).appendTo($j(tr));
   var td = document.createElement("td");
   td.innerHTML = "<input type=\"text\" class=\"form-control\" name=\"print_size[]\" pt=\"_pt_txt\"/>";
   $j(td).appendTo($j(tr));
   var td = document.createElement("td");
   td.innerHTML = "<input type=\"text\" class=\"form-control\" name=\"print_price[]\" pt=\"_pt_numplus\"/>";
   $j(td).appendTo($j(tr));
   var td = document.createElement("td");
   td.innerHTML = "<input type=\"text\" class=\"form-control\" name=\"print_sprice[]\" pt=\"_pt_numplus\"/>";
   $j(td).appendTo($j(tr));
   var td = document.createElement("td");
   td.innerHTML = "<input type=\"text\" class=\"form-control\" name=\"print_oprice[]\" pt=\"_pt_numplus\"/>";
   $j(td).appendTo($j(tr));
   _pt_set();
}

/* 인화옵션 삭제 */
function remove_printopt(){
   $j("input:radio[name=dummy_printopt]:checked").parent().parent().remove();
   if ($j("tr","#printopt_tb").length < 2){
      $j("#printopt_tb").css("display","none");
   }
}

// 추가옵션 묶음 추가
function add_addoptbox(){

   var div = document.createElement("div");
   $j(div).attr("class","addoptbox_div");
   $j(div).appendTo("#addoptbox_div");
   var addoptbox_idx = $j(".addoptbox_div").index(div);
   $j(div).attr("addoptbox_idx",addoptbox_idx);

   var tb = document.createElement("table");
   $j(tb).attr("class","table table-striped table-bordered");

   var tr = document.createElement("tr");
   $j(tr).appendTo(tb);

   var th = document.createElement("th");
   $j(th).html('<?=_("추가옵션묶음명")?>');
   $j(th).appendTo(tr);

   var td = document.createElement("td");
   $j(td).html("<input type=\"text\" class=\"form-control\" name=\"addopt_bundle_name["+addoptbox_idx+"]\" class=\"w200\" pt=\"_pt_txt\" required/> <span class=\"absmiddle stxt red\"><?=_('필수')?></span> <input type=\"checkbox\" name=\"addopt_bundle_required["+addoptbox_idx+"]\" class=\"absmiddle\" style=\"width:11px;\" value=\"1\">");
   $j(td).appendTo(tr);

   var td = document.createElement("td");
   $j(td).html("<select class=\"form-control\" name=\"addopt_bundle_view["+addoptbox_idx+"]\"><option value=\"0\"><?=_('노출')?><option value=\"1\"><?=_('숨김')?></select>");
   $j(td).appendTo(tr);
   $j(td).attr("width","105");
   $j(td).attr("align","center");

   var td = document.createElement("td");
   $j(td).html("<a class=\"btn btn-success btn-icon btn-circle\" onclick=\"add_addopt(this)\"><i class=\"fa fa-plus\"></i></a><a class=\"btn btn-success btn-icon btn-circle\" onclick=\"remove_addoptbox(this)\"><i class=\"fa fa-times\"></i></a>");
   $j(td).appendTo(tr);
   $j(td).attr("width","124");
   $j(td).attr("align","center");

   $j(tb).appendTo($j(div));

   var tb = document.createElement("table");
   $j(tb).attr("class","addoptbox");

   var tr = document.createElement("tr");
   $j(tr).appendTo(tb);

   var th = document.createElement("th");
   $j(th).html('<?=_("옵션명")?>');
   $j(th).appendTo(tr);

   var th = document.createElement("th");
   $j(th).html('<?=_("옵션판매가")?>');
   $j(th).appendTo(tr);

   var th = document.createElement("th");
   $j(th).html('<?=_("옵션공급가")?>');
   $j(th).appendTo(tr);

   var th = document.createElement("th");
   $j(th).html('<?=_("옵션원가")?>');
   $j(th).appendTo(tr);

   var th = document.createElement("th");
   $j(th).html('<?=_("권장소비자가")?>');
   $j(th).appendTo(tr);
   
   var th = document.createElement("th");
   $j(th).html('<?=_("매칭옵션번호")?>');
   $j(th).appendTo(tr);

   var th = document.createElement("th");
   $j(th).html('<?=_("노출여부")?>');
   $j(th).appendTo(tr);

   var th = document.createElement("th");
   $j(th).html('<?=_("삭제")?>');
   $j(th).appendTo(tr);

   $j(tb).appendTo($j(div));
   add_addopt(tb);

   _pt_set();

}

// 추가옵션 묶음 삭제
function remove_addoptbox(obj){
   $j(obj).closest(".addoptbox_div").remove();
}

// 추가옵션 추가
function add_addopt(obj){
   var addoptbox_idx = $j(obj).closest(".addoptbox_div").attr("addoptbox_idx");
   var tb = $j(obj).closest(".addoptbox_div").children(".addoptbox:last");
   var tr = document.createElement("tr");
   $j(tr).appendTo(tb);
   $j(tr).attr("align","center");

   var td = document.createElement("td");
   $j(td).html("<input type=\"text\" class=\"form-control\" name=\"addoptnm["+addoptbox_idx+"][]\"/>");
   $j(td).appendTo(tr);

   var td = document.createElement("td");
   $j(td).html("<input type=\"text\" class=\"form-control\" name=\"addopt_aprice["+addoptbox_idx+"][]\"/>");
   $j(td).appendTo(tr);

   var td = document.createElement("td");
   $j(td).html("<input type=\"text\" class=\"form-control\" name=\"addopt_asprice["+addoptbox_idx+"][]\"/>");
   $j(td).appendTo(tr);

   var td = document.createElement("td");
   $j(td).html("<input type=\"text\" class=\"form-control\" name=\"addopt_aoprice["+addoptbox_idx+"][]\"/>");
   $j(td).appendTo(tr);

   var td = document.createElement("td");
   $j(td).html("<input type=\"text\" class=\"form-control\" name=\"addopt_cprice["+addoptbox_idx+"][]\"/>");
   $j(td).appendTo(tr);

   var td = document.createElement("td");
   $j(td).html("<input type=\"text\" class=\"form-control\" name=\"addopt_mapping_no["+addoptbox_idx+"][]\"/>");
   $j(td).appendTo(tr);

   var td = document.createElement("td");
   $j(td).html("<select class=\"form-control\" name=\"addopt_view["+addoptbox_idx+"][]\"><option value=\"0\"><?=_('노출')?><option value=\"1\"><?=_('숨김')?></select>");
   $j(td).appendTo(tr);

   var td = document.createElement("td");
   $j(td).html("<a class=\"btn btn-success btn-icon btn-circle\" onclick=\"remove_addopt(this)\"><i class=\"fa fa-times\"></i></a>");
                
   $j(td).appendTo(tr);

   _pt_set();

}

// 추가옵션 삭제
function remove_addopt(obj){

   $j(obj).closest("tr").remove();

}

</script>