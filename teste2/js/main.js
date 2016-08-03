$(function(){

	$("#menu-mobile").click(function (e) {
		e.preventDefault();

		if($("#menu-mobile" ).hasClass( "responsive-menu-ativo" )) {
			$( "#menu-mobile" ).removeClass( "responsive-menu-ativo" );
			$( "#menu-mobile" ).addClass( "responsive-menu" );
		} else {
			$( "#menu-mobile" ).removeClass( "responsive-menu" );
			$( "#menu-mobile" ).addClass( "responsive-menu-ativo" );
		}

		$(".menu-principal").stop().slideToggle(150);
	});

	$("#btn-search-mobile").click(function (e) {
		e.preventDefault();

		if($("#btn-search-mobile" ).hasClass( "ativo" )) {
			$( "#btn-search-mobile" ).removeClass( "ativo" );
		} else {
			$( "#btn-search-mobile" ).addClass( "ativo" );
		}

		$("#formSearch").stop().slideToggle(150);
	});

	$(window).resize(function () {
		if ($(window).width() >= 1320 && $(".menu-principal").is(':hidden')) {
			$(".menu-principal").slideDown(10)
		}
	});

	$(window).scroll(function() {

		if ($(window).width() >= 1320) {

			$(".testes, .hover-teste").mouseenter(function (e) {
				e.preventDefault();
				$( ".testes a" ).addClass( "ativo" );
				$(".hover-teste").slideToggle(150);
			});

			$(".testes, .hover-teste").mouseleave(function (e) {
				e.preventDefault();
				 $( ".testes a" ).removeClass( "ativo" );
				$(".hover-teste").slideToggle(150);
			});

			$(".carros, .hover-carros").mouseenter(function (e) {
				e.preventDefault();
				$( ".carros a" ).addClass( "ativo" );
				$(".hover-carros").slideToggle(150);
			});

			$(".carros, .hover-carros").mouseleave(function (e) {
				e.preventDefault();
				$( ".carros a" ).removeClass( "ativo" );
				$(".hover-carros").slideToggle(150);
			});

			if ($(window).scrollTop() > 232) {
				$('header').css({'position': 'fixed', 'top': '0', 'left': '50%', 'transform': 'translateX(-50%)', 'width': '1320px', 'z-index': '99'});
				$('.hover-teste, .hover-carros').css({'position': 'fixed', 'top': '80px', 'left': '50%', 'transform': 'translateX(-50%)', 'width': '1320px', 'z-index': '98'});
				$('.sub-menu').stop().fadeOut(300);
			} else {
				$('header').css({'position': 'relative'});
				$('.hover-teste, .hover-carros').css({'position': 'absolute'});
				$('.sub-menu').stop().fadeIn(300);
			}
		}

	});
});