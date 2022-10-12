jQuery(document).ready(function(){
    $(".proc input").change(function(){
        /*var valueNo = $(this).val();
        if($(this).is(":checked")){
            $(".proc_div").eq(valueNo).fadeIn('slow');
        }else{
            $(".proc_div").eq(valueNo).fadeOut('slow');
        };*/
        
        //아이락추가.
        var valueId = $(this).attr('id');        
        if($(this).is(":checked")){
            $("#div_"+valueId).fadeIn('slow');
        }else{
            $("#div_"+valueId).fadeOut('slow');
            
            //값 초기화.
        	var name = $(this).attr('name');
        	name = name.replace("_check", "");
        	name = name.replace("opt_outside_gloss", "outside_gloss"); //코딩(표지)
        	$("input[name='"+name+"']").prop('checked', false);
        	
        	//가격 조회.
        	calcuPrice();
        };
        //아이락추가.
        
        $('.remove_proc').click(function(){
            var valueNo1 = $('.remove_proc').index(this);
            $(".proc input").eq(valueNo1).removeAttr('checked');
            $(".proc_div").eq(valueNo1).fadeOut('slow');
        });
    });
    
    /*$('#m_color01, #m_color02').click(function(){
        if($(this).is(":checked")){
            jQuery('.order_left .side_sec.sec00').fadeIn();
        }else{
            jQuery('.order_left .side_sec.sec00').fadeOut();
        };
    });
    
     $('#m_color_02_01, #m_color_02_02, #m_color_02_03').click(function(){
        if($(this).is(":checked")){
            jQuery('.order_left .side_sec.sec01').fadeIn();
        }else{
            jQuery('.order_left .side_sec.sec01').fadeOut();
        };
    });
    
     $('#m_color_03_01, #m_color_03_02, #m_color_03_03').click(function(){
        if($(this).is(":checked")){
            jQuery('.order_left .side_sec.sec02').fadeIn();
        }else{
            jQuery('.order_left .side_sec.sec02').fadeOut();
        };
    });*/
    
    $('#sign').change(function(){
        if($(this).val() == "2"){
            jQuery('.side #side02').removeAttr('disabled');
            jQuery('.side #side01').removeAttr('checked');
            jQuery('.side #side02').prop('checked', true);
            jQuery('.side #side01').prop('disabled', true);
        }else if($(this).val() == "4"){
            jQuery('.side #side02').removeAttr('checked');
            jQuery('.side #side01').removeAttr('disabled');
            jQuery('.side #side01').prop('checked', true);
            jQuery('.side #side02').prop('disabled', true);
        }
    });

	/*
	//아이락추가.
    $('#opt_print3_check').click(function(){
        if($(this).is(":checked")){
            jQuery('.order_left .spot_color_sec').fadeIn();
        }else{
            jQuery('.order_left .spot_color_sec').fadeOut();
        };
    });
    //아이락추가.    

	//아이락추가.
    $("input[type=checkbox][id^='s_color01']").click(function(){
    	var ul = $(this).parents("ul");
    	var div = $(ul).find('.spot_color_sec');
    	
        if($(this).is(":checked")){
            $(div).fadeIn();
        }else{
            $(div).fadeOut();
        };
    });
    //아이락추가.
    */
    
    /*
    $('#s_color01').click(function(){
        if($(this).is(":checked")){
            jQuery('.order_left .spot_color_sec').fadeIn();
        }else{
            jQuery('.order_left .spot_color_sec').fadeOut();
        };
    });
    
    $('#s_color02').click(function(){
        if($(this).is(":checked")){
            jQuery('.order_left .spot_color_sec1').fadeIn();
        }else{
            jQuery('.order_left .spot_color_sec1').fadeOut();            
        };
    });
    
    $('#s_color03').click(function(){
        if($(this).is(":checked")){
            jQuery('.order_left .spot_color_sec2').fadeIn();
        }else{
            jQuery('.order_left .spot_color_sec2').fadeOut();
        };
    });
    
    
    $('#printColor').change(function(){
        var state = jQuery('#printColor').val();
        if(state == '옵션2') {
            jQuery('.order_left .spot_color_sec').fadeIn();
        } else {
            jQuery('.order_left .spot_color_sec').fadeOut();
        }
    });
    
    $('#printColor1').change(function(){
        var state = jQuery('#printColor1').val();
        if(state == '옵션2') {
            jQuery('.order_left .spot_color_sec1').fadeIn();
        } else {
            jQuery('.order_left .spot_color_sec1').fadeOut();
        }
    });
    
     $('#printColor2').change(function(){
        var state = jQuery('#printColor2').val();
        if(state == '옵션2') {
            jQuery('.order_left .spot_color_sec2').fadeIn();
        } else {
            jQuery('.order_left .spot_color_sec2').fadeOut();
        }
    });
    */
    
    $('.btn-example').click(function(){
        var $href = $(this).attr('href');
        layer_popup($href);
    });
    function layer_popup(el){

        var $el = $(el);        //레이어의 id를 $el 변수에 저장
        var isDim = $el.prev().hasClass('dimBg');   //dimmed 레이어를 감지하기 위한 boolean 변수

        isDim ? $el.parent("div").fadeIn() : $el.fadeIn();

        var $elWidth = ~~($el.outerWidth()),
            $elHeight = ~~($el.outerHeight()),
            docWidth = $(document).width(),
            docHeight = $(document).height();

        // 화면의 중앙에 레이어를 띄운다.
        if ($elHeight < docHeight || $elWidth < docWidth) {
            $el.css({
                marginTop: -$elHeight /2,
                marginLeft: -$elWidth/2
            })
        } else {
            $el.css({top: 0, left: 0});
        }

        $el.find('a.btn-layerClose').click(function(){
            isDim ? $('.dim-layer').fadeOut() : $el.fadeOut(); // 닫기 버튼을 클릭하면 레이어가 닫힌다.
            return false;
        });

        $('.layer .dimBg').click(function(){
            $('.dim-layer').fadeOut();
            return false;
        });

    }
    
});

function dynamic_change(){
    //var image_source = document.getElementById('opt_size').value;

    var image_source = "";
    var opt_key = document.getElementById('opt_size').value;
    for (i = 0; i < print_img.length; i++) {
        if(opt_key == print_img[i].key) {
        	image_source = print_img[i].url;
        }
    }    
    
	document.all("dynamic_img").innerHTML = "<img width='421' height='480' src='"+image_source+"'>";
};