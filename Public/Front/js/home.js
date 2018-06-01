/**
 * Created by hylanda69874 on 2018/5/31.
 */
$(function(){
    // 菜单切换
    $('#nav-menu').on('click','li',function(){
        var that = $(this);
        if(!that.hasClass('active')) {
            that.siblings().removeClass('active');
            that.addClass('active');
        }
    });
    // 置顶
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    $('#back-to-top').on('click', function(e){
        e.preventDefault();
        $('html, body').animate({scrollTop : 0},1000);
        return false;
    });
});
