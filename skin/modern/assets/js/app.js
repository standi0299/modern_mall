

$( window ).on( 'load', () => {
	$( '.bxslider' ).each( ( $index, $element )=> {
		$element = $( $element );
		const params = {};
		params.mode = $element.data( 'mode' );
		params.infiniteLoop = $element.data( 'infinite-loop' );
		params.minSlides = $element.data( 'min-slides' );
		params.maxSlides = $element.data( 'max-slides' );
		params.slideWidth = $element.data( 'slide-width' );
		params.slideMargin = $element.data( 'slide-margin' );
		params.pagerCustom = $element.data( 'pager-custom' );
		$element.bxSlider( params );
	} );

	$( '.detail-bxslider' ).bxSlider( {
		buildPager: function( slideIndex ) {
			switch( slideIndex ) {
				case 0:
					return '<img src="https://lorempixel.com/65/65/food/1/65x65">';
				case 1:
					return '<img src="https://lorempixel.com/65/65/food/2/65x65">';
				case 2:
					return '<img src="https://lorempixel.com/65/65/food/3/65x65">';
				case 3:
					return '<img src="https://lorempixel.com/65/65/food/4/65x65">';
				case 4:
					return '<img src="https://lorempixel.com/65/65/food/5/65x65">';
				case 5:
					return '<img src="https://lorempixel.com/65/65/food/6/65x65">';
			}
		}
	} );

	$( '.event-bxslider' ).bxSlider( {
		buildPager: function( slideIndex ) {
			switch( slideIndex ) {
				case 0:
					return '<span>1</span>';
				case 1:
					return '<span>2</span>';
				case 2:
					return '<span>3</span>';
				case 3:
					return '<span>4</span>';
				case 4:
					return '<span>5</span>';
			}
		}
	} );
} );

$( ()=> {
    $( '.tab-content' ).hide();
    $( '.tab-content:first' ).show();

    $( 'ul.tab-menu li' ).click( function() {
        $( 'ul.tab-menu li' ).removeClass( 'active' );
        $( this ).addClass( 'active' );
        $( '.tab-content' ).hide();

        var activeTab = $( this ).attr( 'rel' );
        $( '#' + activeTab ).show();
    } );

    // 상단 네비게이션
    $( '.nav-item-wrap' ).on( 'mouseenter', $event=> {
        const item = $( $event.currentTarget );

        if( item.has( '.snb' ) ) {
            $( '.nav-item-wrap .snb' ).hide();
            $( '.nav-item-wrap .nav-item-bullet' ).hide();
            item.find( '.snb' ).show();
            item.find( '.nav-item-bullet' ).show();
        }

        if( item.data( 'interval-id' ) ) {
            clearTimeout( item.data( 'interval-id' ) );
            item.data( 'interval-id', null );
        }
    } );
    $( '.nav-item-wrap' ).on( 'mouseleave', $event=> {
        const item = $( $event.currentTarget );
        const id = setTimeout( ()=> {
            if( item.has( '.snb' ) ) {
                item.find( '.snb' ).hide();
                item.find( '.nav-item-bullet' ).hide();
            }
            item.data( 'interval-id', null );
        }, 500 );
        item.data( 'interval-id', id );
    } );
    // 상단 네비게이션 끝



    $( '.select-box select' ).on( 'change', $event=> {
        const scope = $( $event.currentTarget );
        const selectAmount = scope.children( 'option:selected' ).text();
        scope.siblings( 'label' ).text( selectAmount );
    } );

    $( '#refund-type input[name=refund]' ).on( 'change', ()=> {
        if( $( '#refund-type #refund-count' ).prop( 'checked' ) ) {
            $( '.refund-count-data' ).show();
        } else {
            $( '.refund-count-data' ).hide();
        }
    } );

    $( '#refund-type input[name=refund]' ).trigger( 'change' );

    $( '#option-data-wrap input[name=option-data]' ).on( 'change', ()=> {
        if( $( '#option-data-wrap #option-data' ).prop( 'checked' ) ) {
            $( '#option-data-input' ).show();
        } else {
            $( '#option-data-input' ).hide();
        }
    } );

    $( '#option-data-wrap input[name=option-data]' ).trigger( 'change' );

    $( '.description-wrap .description-content' ).hide();

    $( '.description-wrap .description-btn' ).on( 'click', ()=> {
        $( '.description-wrap .description-content' ).slideToggle( 'fast' );

        if( $( '.description-wrap' ).hasClass( 'active' ) ) {
            $( '.description-wrap' ).removeClass( 'active' );
        } else {
            $( '.description-wrap' ).addClass( 'active' );
        }
    } );

    // 링크가 빈 해시태그일때 클릭 이벤트 취소
    $( 'a[href="#"]' ).on( 'click', $event=> {
        $event.preventDefault();
    } );
    // 링크가 빈 해시태그일때 클릭 이벤트 취소 끝

    // 스크롤시 헤더 크기 조정
    $( document ).on( 'scroll', ()=> {
        const bannerHeight = $( '#banner-area' ).outerHeight();
        const scrollTop = $( document ).scrollTop();

        if( scrollTop > bannerHeight ) {
        		$('#header-top').hide();
            $( '#site-header' ).css( { position: 'fixed', top: 0, height: '60px' } );
            $( '#content-wrapper #content' ).css( { paddingTop: ( 217 + 60 ) + 'px' } );
            $( '#header-area .header-content' ).css( { paddingTop: '17px' } );
            $( '#header-area #logo' ).css( { paddingTop: 0 } );
            $( '.nav-item-wrap .snb' ).css( { top: '64px' } );
        }
        else {
        		$('#header-top').show();
            $( '#site-header' ).css( { position: 'relative', height: '138px' } );
            $( '#content-wrapper #content' ).css( { paddingTop: 217 + 'px' } );
            $( '#header-area .header-content' ).css( { paddingTop: '57px' } );
            $( '#header-area #logo' ).css( { paddingTop: '40px' } );
            $( '.nav-item-wrap .snb' ).css( { top: 104 + 'px' } );
        }
    } );
    // 스크롤시 헤더 크기 조정 끝

    // 아코디언 메뉴
    $( '.accordion-list .question-title' ).on( 'click', $event=> {
        const item = $( $event.currentTarget );
        const answer = item.next();
        if( answer.css( 'display' ) === 'none' ) {
            item.next().show();
        }
        else {
            item.next().hide();
        }

    } );
    // 아코디언 메뉴 끝

    // 퀵메뉴
    $( '#quick-menu-wrap #quick-toggle-btn' ).on( 'click', ()=> {
        const quickMenu = $( '#quick-menu-wrap' );
        if( quickMenu.data( 'is-open' ) === false ) {
            quickMenu.data( 'is-open', true );
            quickMenu.addClass( 'active' );
            if( $( 'html' ).hasClass( 'is-ie' ) ) {
                quickMenu.css( { right: 0 } );
            }
            else {
                quickMenu.css( { transform: 'translate( -183px, 0 )' } );
            }
        } else {
            quickMenu.data( 'is-open', false );
            quickMenu.removeClass( 'active' );
            if( $( 'html' ).hasClass( 'is-ie' ) ) {
                quickMenu.css( { right: '-183px' } );
            }
            else {
                quickMenu.css( { transform: 'translate( 0, 0 )' } );
            }
        }
    } );
    // 퀵메뉴 끝

    // 포토북 상세에 퀵옵션
    $( '.quick-option-wrap #quick-toggle-btn' ).on( 'click', ()=> {
        $( '.quick-option-wrap' ).toggleClass( 'active' );
    } );
    // 포토북 상세에 퀵옵션 끝

    // 회원가입 모두 동의 체크
    $( '#checkbox-essential, #checkbox-essential2, #checkbox-essential3, #checkbox-choice' ).on( 'change', ()=> {
        const agree1 = $( '#checkbox-essential' ).prop( 'checked' );
        const agree2 = $( '#checkbox-choice' ).prop( 'checked' );
        const agree3 = $( '#checkbox-essential2' ).prop( 'checked' );
        const agree4 = $( '#checkbox-essential3' ).prop( 'checked' );

        if( agree1 && agree2 && agree3 && agree4 ) {
            $( '#checkbox-all' ).prop( 'checked', true );
        }
        else {
            $( '#checkbox-all' ).prop( 'checked', false );
        }
    } );

    $( 'label[for="checkbox-all"]' ).on( 'click', ()=> {
        const checked = $( "#checkbox-all" ).prop( 'checked' );
        if( checked === false ) {
            $( '#checkbox-essential, #checkbox-choice, #checkbox-essential2, #checkbox-essential3' ).prop( 'checked', true );
        }
        else {
            $( '#checkbox-essential, #checkbox-choice, #checkbox-essential2, #checkbox-essential3' ).prop( 'checked', false );
        }
    } );
    // 회원가입 모두 동의 체크 끝

    // 결제 모달
    $( '#list-destination-modal' ).hide();
    $( '#edit-destination-point' ).hide();
    $( '#add-destination-point' ).hide();

    $( '.modal-close' ).on( 'click', ()=> {
        $( '#list-destination-modal' ).hide();
        $( '#edit-destination-point' ).hide();
        $( '#add-destination-point' ).hide();
        $( '.modal-bg' ).hide();
    } );

    $( '.select-destination-point-btn' ).on( 'click', ()=> {
        $( '#list-destination-modal' ).hide();
        $( '#edit-destination-point' ).hide();
        $( '#add-destination-point' ).hide();
        $( '.modal-bg' ).hide();
    } );

    $( '#delivery-list-btn' ).on( 'click', ()=> {
        $( '#list-destination-modal' ).show();
        $( '#edit-destination-point' ).hide();
        $( '#add-destination-point' ).hide();
        $( '.modal-bg' ).show();
    } );

    $( '.edit-destination-point-btn' ).on( 'click', ()=> {
        $( '#edit-destination-point' ).show();
        $( '#list-destination-modal' ).hide();
        $( '#add-destination-point' ).hide();
        $( '.modal-bg' ).show();
    } );

    $( '#add-destination-point-btn' ).on( 'click', ()=> {
        $( '#add-destination-point' ).show();
        $( '#list-destination-modal' ).hide();
        $( '#edit-destination-point' ).hide();
        $( '.modal-bg' ).show();
    } );

    // 결제 모달 끝

    // 퀵메뉴 상 하단 이동
    (jQuery1_11_0)( '#go-top-btn' ).on( 'click', ()=> {
        window.scrollTo( 0, 0 );
    } );

    $( '#go-bottom-btn' ).on( 'click', ()=> {
        window.scrollTo( 0, document.body.scrollHeight );
    } );

    // 퀵메뉴 상 하단 이동 끝

    // 마이페이지>주문/배송 조회 주문목록 모달
    $( '#old-history-btn' ).on( 'click', ()=> {
        $( '#order-history-modal' ).show();
        $( '.modal-bg' ).show();
    } );

    $( '#order-history-modal .close-btn' ).on( 'click', ()=> {
        $( '#order-history-modal' ).hide();
        $( '.modal-bg' ).hide();
    } );
    // 마이페이지>주문/배송 조회 주문목록 모달 끝
} );