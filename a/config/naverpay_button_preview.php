<?
include_once "../_pheader.php";
?>

<script type="text/javascript" src="http://pay.naver.com/customer/js/naverPayButton.js" charset="UTF-8"></script>
<script type="text/javascript" >
	function button_select(btype, bcolor)
	{
		window.opener.parent.document.fm.npay_button_type.value = btype;
		window.opener.parent.document.fm.npay_button_color.value = bcolor;
		window.close();
	}
</script>
<div id="page-container" class="page-without-sidebar page-header-fixed">
   <!-- begin #content -->
   <div id="content" class="content">
      <!-- begin #header -->
      <div id="header" class="header navbar navbar-default navbar-fixed-top">
         <div class="container-fluid">
            <div class="navbar-header">
               <a href="javascript:window.close();" class="navbar-brand"><span class="navbar-logo"></span><?=_("미리보기")?></a>
            </div>
         </div>
      </div>

      <div class="panel panel-inverse">
         <div class="panel-body panel-form">
            <div class="panel-body">
               <div class="table-responsive">
                  <table id="data-table" class="table table-bordered">
                     <tbody>
                     <tr align="center">
                        <td style="padding:1px;" width="13">                       
               			

												<script type="text/javascript" >//<![CDATA[
													naver.NaverPayButton.apply({
														BUTTON_KEY: "BE83F116-DECB-4D8F-86D6-5BFA6FA915A5",
														TYPE: "A",
														COLOR: 1,
														COUNT: 1,
														ENABLE: "Y",
														"":""
													});
												//]]></script>			
<!--{ / }-->         
                        <BR><BR><a href="javascript:button_select('A', '1');">A type (236 * 88)</a>
                        </td>
                        
                        <td style="padding:1px;" width="13">                       
               			

												<script type="text/javascript" >//<![CDATA[
													naver.NaverPayButton.apply({
														BUTTON_KEY: "BE83F116-DECB-4D8F-86D6-5BFA6FA915A5",
														TYPE: "B",
														COLOR: 1,
														COUNT: 1,
														ENABLE: "Y",
														"":""
													});
												//]]></script>			
<!--{ / }-->         
                        <BR><BR><a href="javascript:button_select('B', '1');">B type (214 * 83)</a>
                        </td>
                        
                        <td style="padding:1px;" width="13">                       
               			

												<script type="text/javascript" >//<![CDATA[
													naver.NaverPayButton.apply({
														BUTTON_KEY: "BE83F116-DECB-4D8F-86D6-5BFA6FA915A5",
														TYPE: "C",
														COLOR: 1, 
														COUNT: 1, 
														ENABLE: "Y",
														"":""
													});
												//]]></script>			
<!--{ / }-->         
                        <BR><BR><a href="javascript:button_select('C', '1');">C type (225 * 88)</a>
                        </td>
                        
                     </tr>
                     
                     
                     <tr align="center">
                        <td style="padding:1px;" width="13">                       
               			

												<script type="text/javascript" >//<![CDATA[
													naver.NaverPayButton.apply({
														BUTTON_KEY: "BE83F116-DECB-4D8F-86D6-5BFA6FA915A5",
														TYPE: "C",
														COLOR: 3,
														COUNT: 1,
														ENABLE: "Y",
														"":""
													});
												//]]></script>			
<!--{ / }-->         
                        <BR><BR><a href="javascript:button_select('C', '3');">D type (225 * 92)</a>
                        </td>
                        
                        <td style="padding:1px;" width="13">                       
               			

												<script type="text/javascript" >//<![CDATA[
													naver.NaverPayButton.apply({
														BUTTON_KEY: "BE83F116-DECB-4D8F-86D6-5BFA6FA915A5",
														TYPE: "D",
														COLOR: 1,
														COUNT: 1,
														ENABLE: "Y",
														"":""
													});
												//]]></script>			
<!--{ / }-->         
                        <BR><BR><a href="javascript:button_select('D', '1');">E type (210 * 83)</a>
                        </td>
                        
                        <td style="padding:1px;" width="13">                       
               			

												<script type="text/javascript" >//<![CDATA[
													naver.NaverPayButton.apply({
														BUTTON_KEY: "BE83F116-DECB-4D8F-86D6-5BFA6FA915A5",
														TYPE: "D",
														COLOR: 3,
														COUNT: 1,
														ENABLE: "Y",
														"":""
													});
												//]]></script>			
<!--{ / }-->         
                        <BR><BR><a href="javascript:button_select('D', '3');">F type (210 * 87)</a>
                        </td>
                        
                     </tr>
                     <tr>
                           <td style="text-align: center;" colspan="3">                      
                              <button type="button" class="btn btn-sm btn-warning" onclick="window.close();"><?=_("닫기")?></button>
                           </td>
                    	</tr>
                     
                     
                     </tbody>
                  </table>                  
               </div>
               
            </div>
         </div>
      </div>
   </div>
</div>


<? include_once "../_pfooter.php"; ?>