(function($){
	$(document).ready(function(){	
		
		$('.toggle-gnb').on('click',function(){
			$('.snb-area').fadeToggle(100);
		});
		$(window).scroll(function() {
			
			//var bannerHeight = $( '#banner-area' ).outerHeight();
			var headerHeight = $('#site-header').outerHeight();
			var scrollTop = $( document ).scrollTop();

			if( scrollTop > headerHeight ) {
				$('body').css({ paddingTop:headerHeight+'px' });
				$('#site-header').addClass('fixed');
				//$('#header-top').hide();
				//$('#header-wrap' ).css( { position: 'relative', height: '60px' } );
				//$('#site-header' ).css( { position: 'fixed', top: 0, height: '60px' } );            
				//$('#content-wrapper #content' ).css( { paddingTop: ( 217 + 60 ) + 'px' } );
				//$('#header-area .header-content' ).css( { paddingTop: '17px' } );
				//$('#header-area #logo' ).css( { paddingTop: 0 } );
				//$('.nav-item-wrap .snb' ).css( { top: '64px' } );
			} else {
				$('body').css({ paddingTop:'0px' });
				$('#site-header').removeClass('fixed');
				//$('#header-top').show();
				//$('#header-wrap' ).css( { position: 'relative', height: '114px' } );
				//$('#site-header' ).css( { position: 'relative', height: '114px' } );
				//$('#content-wrapper #content' ).css( { paddingTop: '217px' } );
				//$('#header-area .header-content' ).css( { paddingTop: '57px' } );
				//$('#header-area #logo' ).css( { paddingTop: '40px' } );
				//$('.nav-item-wrap .snb' ).css( { top: '104px' } );
			}			
		}).scroll();
	});
})(jQuery1_11_0);