function setAddress(_this) {
    // 获得相应的内容
    var name = $(_this).parents('.consignee-item').find('.e-name').html();
    var cityParticular = $(_this).parents('.consignee-item').find('.city-particular').html();
    var codeNumber = $(_this).parents('.consignee-item').find('.codeNumber').html();
    var city = $(_this).parents('.consignee-item').find('.city').html();
    var aid = $(_this).parents('.consignee-item').find('.copyreader').attr('aid');
    $('.mailTo .m-city').text(city);
    $('.mailTo .m-particular').text(cityParticular);
    $('.mailTo .m-name').text(name);
    $('.mailTo .m-number').text(codeNumber);
    $('#order_aid').val(aid);
}

$(function () {


    //	切换地址
    $(document).on('click', '.content-address .consignee-item .radio', function () {
        //      取消label的默认事件
        event.preventDefault();
        // 判断当前地址是否已经被选中，如果是则不询问
        var aid = $(this).siblings('.compile').find('a').attr('aid');
        if (!$(this).parent().find('.radio-img').is('.pitchOn')) {
            var _this= this;
            layer.confirm('是否将该地址设置为默认地址？', function (index) {
                $.post(ajaxUpdateSelectedAd, {aid: aid}, function (res) {
                    if (res) {
                        $(_this).parent().find('.radio-img').addClass('pitchOn');
                        $(_this).parent().siblings('.consignee-item').find('.radio-img').removeClass('pitchOn');
                        layer.msg('设置成功！');
                    } else {
                        layer.msg('设置失败！');
                    }
                }, 'json');
            });
        }

        // 隐藏添加和编辑表单
        $('.addAdress_span,.addAddress_form').hide();
        $('.new-address').show();
        $('.edit').hide();
        // 将当前元素中的数据抓取到下面
        setAddress(this);

    })
    //	添加新地址
    $('.new-address').click(function () {
        $('.edit').hide();
        //		让当前元素隐藏
        $(this).hide();
        //		取消掉其他单选框的背景
        // 		$(this).siblings('.consignee-item').find('.radio-img').removeClass('pitchOn');
        //	使添加新地址的单选框和地址框显示
        $('.addAdress_span,.addAddress_form').show();
        //		使添加新地址的单选框添加背景
        // 		$('.new-ress .radio-img').addClass('pitchOn');
    })
    // 点击取消添加或编辑
    $('.btn-qu').click(function () {
        // 取消添加
        //	使添加新地址的单选框和地址框隐藏
        $('.addAdress_span,.addAddress_form').hide();
        //	让添加新地址的按钮显示
        $('.new-address').show();
        // 取消编辑
        $('.edit').hide();
        //	让第一个第值默认选中
        // 	$('.consignee-item').eq(0).find('.radio-img').addClass('pitchOn');

    })
    //点击编辑
    $(document).on('click', '.copyreader', function () {
        //	使添加新地址的单选框和地址框隐藏
        $('.addAdress_span,.addAddress_form').hide();
        //	让添加新地址的按钮显示
        $('.new-address').show();
        //	让第一个第值默认选中
        //     $('.consignee-item').eq(0).find('.radio-img').addClass('pitchOn');

        //	选中背景图
        //     $(this).parents('.consignee-item').find('.radio-img').addClass('pitchOn');
        //	让其他兄弟元素的背景图消失
        //     $(this).parents('.consignee-item').siblings('.consignee-item').find('.radio-img').removeClass('pitchOn');
        //	让编辑页面显示
        $('.edit').show();

        //	获得相应的内容
        var name = $(this).parents('.consignee-item').find('.e-name').html();
        var cityParticular = $(this).parents('.consignee-item').find('.city-particular').html();
        var codeNumber = $(this).parents('.consignee-item').find('.codeNumber').html();
        var postcode = $(this).parents('.consignee-item').find('.postCode').val();
        var city = $(this).parents('.consignee-item').find('.city').html();
        var city_arr = city.split(' ');
        //	将获得的内容写入样式中
        $('.edit .s-name #name').val(name);
        $('.edit .shouji #shouji').val(codeNumber);
        $('.edit .address-s #particular').val(cityParticular);
        $('.edit .postcode #postcode').val(postcode);
        // 将要编辑的地址的aid传入编辑form表单的隐藏域中
        $('.edit form').find('input[name=aid]').val($(this).attr('aid'));
        // 重新初始化城市联动
        area.init('area_');
        //默认选中效果
        area.selected(city_arr[0], city_arr[1], city_arr[2]);
    });
    $(document).on('click', '.del_address', function () {
        var aid = $(this).attr('aid');
        var _this = $(this);
        layer.confirm('确认删除？', function () {
            $.post(ajaxDelAddress, {aid: aid}, function (res) {
                if (res) {
                    layer.msg('删除成功！');
                    _this.parents('.consignee-item').remove();
                } else {
                    layer.msg('删除失败！');
                }
            }, 'json')
        })
    });

    $('.invoice .radio').click(function () {
        $(this).parent().find('.radio-img').addClass('pitchOn');
        $(this).parent().siblings('.consignee-item').find('.radio-img').removeClass('pitchOn');
        if ($(this).hasClass('yes')) {
            $('.con').show();
        } else {
            $('.con').hide();
        }


    })


    $('.tongyong').click(function () {
        $(this).addClass('active');
        $(this).siblings().removeClass('active');
        if ($(this).hasClass('danwei')) {
            $('.danweiname').show();
        } else {
            $('.danweiname').hide();
        }
        // 将选择内容抓到input隐藏域
        $(this).siblings('input[name=p_or_c]').val($(this).html());
    })
    $('#sendOrder').submit(function(){
        if($('#order_aid').val() == ''){
            layer.msg('请添加地址信息！');
            return false;
        }
    })

})