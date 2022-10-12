//현수막 낱장 PlacardRealprint

function getList(src)
{
    var f = document.fm_print;
	var url = "/print/get_option_list_pr.php";
	
	if (typeof (src) == undefined || src == null || src == "") {}
	else {url = src;}
	
    $.ajax({
    	url : url,
      	type : "POST",
      	data : $("#fm_print").serialize(),
      	async : false,
      	cache : false,
      	dataType : "json",
      	success : function(data) {
	      	if(data.error) {
	        	alert(data.error);
	          	return false;
	      	}

			//console.log(data);
			$.each(data , function(key, value) {
				//console.log(key + ": " + data[key]);
				
				if($("#"+key)) {
					if(key == "opt_size_cut_x" || key == "opt_size_cut_y" || key == "opt_size_work_x" || key == "opt_size_work_y") {
						$("#"+key).val(data[key]);
					}
					else {
						$("#"+key).html(data[key]);
					}	
				}
				
				//건수 사용여부 처리.
				if(key == "opt_cnt_use") {
					if (data[key] == "N") $(".cnt_use").hide();
				}				
				
				//다이렉트 파일업로드 사용여부 처리.
				if(key == "opt_directupload_use") {
					if (data[key] == "N") $("#directupload_use").hide();
				}
			});
  		}
  	});
}

function calcuPrice()
{
	if (isValidOptionPr()) {
		calcuPriceProc("fm_print", "/print/option_calcu_pr.php");
	}
}