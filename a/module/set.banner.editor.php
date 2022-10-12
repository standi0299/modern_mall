<?
include "../_pheader.php";

?>

<div id="page-container" class="page-without-sidebar page-header-fixed">
	<div id="content" class="content">
		<div id="header" class="header navbar navbar-default navbar-fixed-top">
        	<div class="container-fluid">
         	<div class="navbar-header">
            		<a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("배너 에디터")?></a>
            </div>
         </div>
      </div>
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs nav-tabs-inverse nav-justified">
					<li class="active"><a href="#tab-1" data-toggle="tab"><?=_("편집기 실행하기")?></a></li>
					<li class=""><a href="#tab-2" data-toggle="tab"><?=_("상품 링크 만들기")?></a></li>
					<li class=""><a href="#tab-3" data-toggle="tab"><?=_("상품 이름 출력하기")?></a></li>
					<li class=""><a href="#tab-4" data-toggle="tab"><?=_("상품 이미지 출력하기")?></a></li>
					<li class=""><a href="#tab-5" data-toggle="tab"><?=_("상품판매가격 출력하기")?></a></li>
					<li class=""><a href="#tab-6" data-toggle="tab"><?=_("상품할인가격 출력하기")?></a></li>
					<li class=""><a href="#tab-7" data-toggle="tab"><?=_("상품상세설명 출력하기")?></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade active in" id="tab-1">
						<!--tab-1-편집기 실행하기-->
						   <form name="tab_1" id="tab_1" class="form-horizontal form-bordered">
						      
                        <div class="panel panel-inverse">
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("상품번호")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="goodsno" class="form-control">
                                 </div>
                               </div>
                           </div>
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("옵션 - 편집코드")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="productid" class="form-control">
                                 </div>
                              </div>
                           </div>
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("옵션 - 편집옵션코드")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="optionid" class="form-control">
                                 </div>
                              </div>
                           </div>
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("템플릿 ID")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="templateIdx" class="form-control">
                                 </div>
                              </div>
                           </div>
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("템플릿 SetID")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="templateSetIdx" class="form-control">
                                 </div>
                              </div>
                           </div>
                        </div>
                        <p class="text-left m-b-0">
                           <button type="button" onclick="set_editor('tab_1', 'exec_editor');" class="btn btn-primary"><?=_("저장")?></button>
                        </p>
                     </form>
						<!--tab-1-편집기 실행하기-->
					</div>

					<div class="tab-pane fade" id="tab-2">
						<!--tab-2-상품 링크 만들기-->
                     <form name="tab_2" id="tab_2" class="form-horizontal form-bordered">

                        <div class="panel panel-inverse">
                           <div class="panel-body panel-form">
                               <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("상품번호")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="goodsno" class="form-control">
                                 </div>
                               </div>
                            </div>
                            
                            <div class="panel-body panel-form">
                               <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("상품 카테고리")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="catno" class="form-control">
                                 </div>
                               </div>
                            </div>

                            <div class="panel-body panel-form">
                               <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("출력 데이터 선택")?></label>
                                 <div class="col-md-2">
                                    <select name="type_link" id="type_link" class="form-control" onchange="show(this.value);">
                                       <option value="1"><?=_("선택")?></option>
                                       <option value="goods_name"><?=_("상품명")?></option>
                                       <option value="goods_list_img"><?=_("상품 리스트 이미지")?></option>
                                       <option value="goods_detail_img_link"><?=_("상품 상세 이미지")?></option>
                                    </select>
                                 </div>
                               </div>
                            </div>

                            <div id="link_img_view" class="notView">
                               <div class="panel-body panel-form">
                                  <div class="form-group">
                                    <label class="col-md-2 control-label"><?=_("상품 상세 이미지")?></label>
                                    <div id="link_detail_img" class="col-md-10">
                                       
                                    </div>
                                  </div>
                               </div>

                               <div class="panel-body panel-form">
                                  <div class="form-group">
                                    <label class="col-md-2 control-label"><?=_("이미지 크기 설정")?></label>
                                    <div class="col-md-5 form-inline">
                                       <?=_("가로")?> : <input type="text" name="width" class="form-control"><br>
                                       <?=_("세로")?> : <input type="text" name="height" class="form-control">
                                    </div>
                                  </div>
                               </div>
                            </div>
                        </div>
                        <p class="text-left m-b-0">
                           <button type="button" onclick="set_editor('tab_2', 'goods_link');" class="btn btn-primary"><?=_("저장")?></button>
                        </p>
                     </form>
						<!--tab-2-상품 링크 만들기-->
					</div>
					<div class="tab-pane fade" id="tab-3">
                  <!--tab-3-상품 이름 출력하기-->
                     <form name="tab_3" id="tab_3" class="form-horizontal form-bordered">
                        <div class="panel panel-inverse">
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("상품번호")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="goodsno" class="form-control">
                                 </div>
                              </div>
                           </div>
                        </div>
                        <p class="text-left m-b-0">
                           <button type="button" onclick="set_editor('tab_3', 'goods_name');" class="btn btn-primary"><?=_("저장")?></button>
                        </p>
                     </form>
                  <!--tab-3-상품 이름 출력하기-->
               </div>

               <div class="tab-pane fade" id="tab-4">
                  <!--tab-4-상품 이미지 출력하기-->
                     <form name="tab_4" id="tab_4" class="form-horizontal form-bordered">

                        <div class="panel panel-inverse">
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("상품번호")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="goodsno" class="form-control">
                                 </div>
                              </div>
                           </div>

                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("출력 데이터 선택")?></label>
                                 <div class="col-md-2">
                                    <select name="type_print" id="type_print" class="form-control" onchange="show(this.value);">
                                       <option value="1"><?=_("선택")?></option>
                                       <option value="goods_list_img"><?=_("상품 리스트 이미지")?></option>
                                       <option value="goods_detail_img_print"><?=_("상품 상세 이미지")?></option>
                                    </select>
                                 </div>
                              </div>
                           </div>

                           <div id="print_img_view" class="notView">
                              <div class="panel-body panel-form">
                                 <div class="form-group">
                                    <label class="col-md-2 control-label"><?=_("상품 상세 이미지")?></label>
                                    <div id="print_detail_img" class="col-md-10">
                                       
                                    </div>
                                 </div>
                              </div>

                              <div class="panel-body panel-form">
                                 <div class="form-group">
                                    <label class="col-md-2 control-label"><?=_("이미지 크기 설정")?></label>
                                    <div class="col-md-5 form-inline">
                                       <?=_("가로")?> : <input type="text" name="width" class="form-control"><br>
                                       <?=_("세로")?> : <input type="text" name="height" class="form-control">
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <p class="text-left m-b-0">
                           <button type="button" onclick="set_editor('tab_4', 'goods_img');" class="btn btn-primary"><?=_("저장")?></button>
                        </p>
                     </form>
                  <!--tab-4-상품 이미지 출력하기-->
               </div>
               <div class="tab-pane fade" id="tab-5">
                  <!--tab-5-상품판매가격 출력하기-->
                     <form name="tab_5" id="tab_5" class="form-horizontal form-bordered">

                        <div class="panel panel-inverse">
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("상품번호")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="goodsno" class="form-control">
                                 </div>
                               </div>
                           </div>

                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("형식")?></label>
                                 <div class="col-md-4">
                                    <input type="radio" name="type" value="origin" checked/><?=_("기본형식")?>(15000)<br>
                                    <input type="radio" name="type" value="number"/><?=_("숫자형식")?>(15,000)
                                 </div>
                              </div>
                           </div>
                        </div>

                        <p class="text-left m-b-0">
                           <button type="button" onclick="set_editor('tab_5', 'goods_price');" class="btn btn-primary"><?=_("저장")?></button>
                        </p>
                     </form>
                  <!--tab-5-상품판매가격 출력하기-->
               </div>
               <div class="tab-pane fade" id="tab-6">
                  <!--tab-6-상품할인가격 출력하기-->
                     <form name="tab_6" id="tab_6" class="form-horizontal form-bordered">
                        <input type="hidden" name="mode" value="goods_dc_price"/>

                        <div class="panel panel-inverse">
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("상품번호")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="goodsno" class="form-control">
                                 </div>
                              </div>
                           </div>

                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("형식")?></label>
                                 <div class="col-md-4">
                                    <input type="radio" name="type" value="origin" checked/><?=_("기본형식")?>(15000)<br>
                                    <input type="radio" name="type" value="number"/><?=_("숫자형식")?>(15,000)
                                 </div>
                              </div>
                           </div>
                        </div>
                        <p class="text-left m-b-0">
                           <button type="button" onclick="set_editor('tab_6', 'goods_dc_price');" class="btn btn-primary"><?=_("저장")?></button>
                        </p>
                     </form>
                  <!--tab-6-상품할인가격 출력하기-->
               </div>
               <div class="tab-pane fade" id="tab-7">
                  <!--tab-7-상품상세설명 출력하기-->
                     <form name="tab_7" id="tab_7" class="form-horizontal form-bordered">
                        <div class="panel panel-inverse">
                           <div class="panel-body panel-form">
                              <div class="form-group">
                                 <label class="col-md-2 control-label"><?=_("상품번호")?></label>
                                 <div class="col-md-2">
                                    <input type="text" name="goodsno" class="form-control">
                                 </div>
                              </div>
                           </div>
                        </div>
                        <p class="text-left m-b-0">
                           <button type="button" onclick="set_editor('tab_7', 'goods_detail');" class="btn btn-primary"><?=_("저장")?></button>
                        </p>
                     </form>
                  <!--tab-7-상품상세설명 출력하기-->
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   function show(obj){
      if(obj == "goods_detail_img_link"){
         var goodsno = $j("#tab_2 input[name=goodsno]").attr("value");
         
         if(goodsno){
            var params = $j("#tab_2").serialize(); // serialize() : 입력된 모든Element(을)를 문자열의 데이터에 serialize 한다.
            
            $j("#link_img_view").show();
            
            $.ajax({
               type : "POST",
               url : "indb.php",
               data : "mode=get_detail_img&" + params,
               success : function(html) {
                  if (html) {
                     $j("#link_detail_img").html(html);
                  }
               }
            });
         } else {
            alert('<?=_("상품번호를 입력해주세요")?>');
            $j("#type_link").val("1").prop("selected", true);
         }

         //link_detail_img
         
      } else if(obj == "goods_detail_img_print"){

         var goodsno = $j("#tab_4 input[name=goodsno]").attr("value");

         if(goodsno){
            var params = $j("#tab_4").serialize(); // serialize() : 입력된 모든Element(을)를 문자열의 데이터에 serialize 한다.
            
            $j("#print_img_view").show();

            $.ajax({
               type : "POST",
               url : "indb.php",
               data : "mode=get_detail_img&" + params,
               success : function(html) {
                  if (html) {
                     $j("#print_detail_img").html(html);
                  }
               }
            });

         } else {
            alert('<?=_("상품번호를 입력해주세요")?>');
            $j("#type_print").val("1").prop("selected", true);
         }
      } else {
         $j("#link_img_view").hide();
         $j("#print_img_view").hide();
      }
   }

   function set_editor(form_name, mode){
      
      var goodsno = $j("#"+form_name+" input[name=goodsno]").attr("value");
      
      if(goodsno){
         var params = $j("#"+form_name).serialize(); // serialize() : 입력된 모든Element(을)를 문자열의 데이터에 serialize 한다.
   
         $.ajax({
            type : "POST",
            url : "indb.php",
            data : "mode=" + mode + "&" + params,
            success : function(html) {
               if (html) {
                  var sHTML = html;
                  var tagIDName = "edit_spc_desc";
                  opener.parent.pasteHTML(tagIDName, sHTML);
               }
            }
         });
      } else {
         alert('<?=_("상품번호를 입력해주세요.")?>');
      }

      //opener.parent.oEditors.getById["edit_spc_desc"].exec("PASTE_HTML", [sHTML]);
      //this.oApp.exec("PASTE_HTML",  [sContents]); // 위즐 첨부 파일 부분 확인
   }
</script>

<? include "../_pfooter.php"; ?>