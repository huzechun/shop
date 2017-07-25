$(function() {
	//底部返回
	$(window).scroll(function() {
		h = $(window).scrollTop();
		if(h > 400) {
			$('.topback').show();
			$('.floor').addClass('floorshow');
		}
		if(h < 400) {
			$('.topback').hide();
			$('.floorshow').addClass('floor').removeClass('floorshow');
		}
	});

	$('.topback').click(function() {
		$('html,body').animate({
			scrollTop: 0
		}, 400);
	});
	$('.topback').hover(function() {
		$(this).find('a').text('返回顶部');
	}, function() {
		$(this).find('a').text('');
		$(this).find('a').css({
			'background': '#8C8C8C',
			'color': '#fff'
		})
	});

	//左侧楼层

	var arr = [];
	var str = '';
	str = $('#hot').offset().top-100;
	arr.push(str);
	str = $('#floor1').offset().top-100;
	arr.push(str);
	str = $('#floor2').offset().top-100;
	arr.push(str);
	str = $('#floor3').offset().top-100;
	arr.push(str);
	str = $('#floor4').offset().top-100;
	arr.push(str);
	str = $('#newproduct').offset().top-100;
	arr.push(str);

	console.log(arr);
	$('.floor ul li').click(function(){
		var i=$(this).index();
		var target=arr[i]+1;
		$('html,body').stop().animate({
			scrollTop:target
		},300);
	});
	
	
	// 滚动时赋值判断
	$(window).scroll(function() {
		v = $(window).scrollTop();
		getdis(v);
		
	});

	function getdis(v) {
		if(v < arr[0]) {

			$('.floor ul li').eq(0).addClass('spanon').siblings().removeClass('spanon');
			$('.floor ul li').eq(0).find('span').show().closest('li').siblings().find('span').hide();

		}
		if(v >= arr[0] && v < arr[1]) {

			$('.floor ul li').eq(0).addClass('spanon').siblings().removeClass('spanon');
			$('.floor ul li').eq(0).find('span').show().closest('li').siblings().find('span').hide();

		}
		if(v >= arr[1] && v < arr[2]) {

			$('.floor ul li').eq(1).addClass('spanon').siblings().removeClass('spanon');
			$('.floor ul li').eq(1).find('span').show().closest('li').siblings().find('span').hide();

		}
		if(v >= arr[2] && v < arr[3]) {

			$('.floor ul li').eq(2).addClass('spanon').siblings().removeClass('spanon');
			$('.floor ul li').eq(2).find('span').show().closest('li').siblings().find('span').hide();

		}
		if(v >= arr[3] && v < arr[4]) {

			$('.floor ul li').eq(3).addClass('spanon').siblings().removeClass('spanon');
			$('.floor ul li').eq(3).find('span').show().closest('li').siblings().find('span').hide();

		}
		if(v >= arr[4] && v < arr[5]) {

			$('.floor ul li').eq(4).addClass('spanon').siblings().removeClass('spanon');
			$('.floor ul li').eq(4).find('span').show().closest('li').siblings().find('span').hide();

		}
		if(v >= arr[5] - 10) {

			$('.floor ul li').eq(5).addClass('spanon').siblings().removeClass('spanon');
			$('.floor ul li').eq(5).find('span').show().closest('li').siblings().find('span').hide();

		}

	}

	//	轮播图开始
	var index = 0;
	var time = null;
	var l = $('#top .banner .pic li').length;
	$('#top .banner .pic li').eq(0).show();
	$('#top .banner .dot li').eq(0).addClass('cur');
	time = setInterval(run, 5000);

	function run() {
		index++;
		if(index > l - 1) {
			index = 0;
		}
		fous(index);
	}
	$('#top .banner').hover(function() {
		clearInterval(time);
	}, function() {
		time = setInterval(run, 5000);
	});
	$('#top .banner .dot li').click(function() {
		index = $(this).index();
		fous(index);
	});
	$('.prev').click(function() {
		index--;
		if(index < 0) {
			index = l - 1;
		}
		fous(index);
	});
	$('.next').click(function() {
		run();
	});

	function fous(index) {
		$('#top .banner .pic li').eq(index).fadeIn().siblings().fadeOut();
		$('#top .banner .dot li').eq(index).addClass('cur').siblings().removeClass('cur');
	}

	//搜索
	$('.seachtxt').blur(function() {
		fous = $(this).val();
		if(fous === "") {
			$(this).val('请输入关键字');
		}
		if(fous != "") {
			$(this).val();
		}
	});
	$('.seachtxt').focus(function() {
		$(this).val('');
	});

	//	导航开始
	$('#top .navbox .main .navHidden .list2 li:even').addClass('on');
	$('#top .navbox .main .navHidden .list2 li').eq(1).find('i').addClass('c1');
	$('#top .navbox .main .navHidden .list2 li').eq(2).find('i').addClass('c2');
	$('#top .navbox .main .navHidden .list2 li').find('i').eq(3).addClass('c3');
	$('#top .navbox .main .navHidden .list2 li').find('i').eq(4).addClass('c4');
	$('#top .navbox .main .navHidden .list2 li').find('i').eq(5).addClass('c5');
	$('#top .navbox .main .navHidden .list2 li').find('i').eq(6).addClass('c6');
	$('#top .navbox .main .navHidden .list2 li').find('i').eq(7).addClass('c7');

	slidemenu(".menu li");

	function slidemenu(_this) {
		$(_this).each(function() {
			var $this = $(this);
			var theMenu = $this.find(".menuHiden");
			var tarHeight = theMenu.height();
			theMenu.css({
				height: 0
			});
			$this.hover(
				function() {

					theMenu.stop().show().animate({
						height: tarHeight
					}, 400);
				},
				function() {

					theMenu.stop().animate({
						height: 0
					}, 10, function() {
						$(this).css({
							display: "none"
						});
					});
				}
			);
		});
	}
	//	tab标签页切换
	$('.slide-point a').click(function() {
		var point = $(this).index();
		$(this).addClass('curr-point').siblings().removeClass('curr-point');
		$(this).closest('.slide-point').prev().find('.slider-film').eq(point).fadeIn().siblings('.slider-film').fadeOut();
	});

	

})