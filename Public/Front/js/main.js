/**
 * Created by hylanda69874 on 2018/8/20.
 */
$(function(){
    $('.dropdown-toggle').click(function () {
        var p = $(this).parent();
        if(p.hasClass('open')){
            p.removeClass('open');
            p.find('.dropdown-menu').hide();
        }else{
            p.addClass('open');
            p.find('.dropdown-menu').show();
        }

        return false;
    });
    $(document).click(function () {
        $('.dropdown-toggle').parent().removeClass('open');
        $('.dropdown-toggle').parent().find('.dropdown-menu').hide();
    });

    $('.navbar-left li').click(function(){
        var self = $(this);
        var link = self.attr('link');
        var ul = $('#'+link);
        if(!self.hasClass('active')){
            self.siblings().removeClass('active');
            self.addClass('active');

            $('#left-chart-nav > ul').hide();
            ul.show();
        }

        return false;
    });

    $('.left-chart-menu a').click(function(){
        var self = $(this);
        var a = self.parents('#left-chart-nav').find('a');
        var url = self.attr('href');
        if(!self.hasClass('active')){
            a.removeClass('active');
            self.addClass('active');
            if(url) {
                $.post(url, function (data) {
                    $('#main-container').html(data);
                }, 'html');
            }
        }
        return false;
    });

    $('#left-chart-nav>ul>li').click(function(){
        var self = $(this);
        var b = self.find('b');
        var bb = self.parent().find('b');
        var p_menu = self.parent().find('.left-chart-menu');
        var menu = self.find('.left-chart-menu');
        if(!self.hasClass('active')){
            self.siblings().removeClass('active');
            self.addClass('active');
        }
        if(b.hasClass('caret-r')){
            bb.removeClass('caret').addClass('caret-r');
            p_menu.hide();
            b.removeClass('caret-r').addClass('caret');
            menu.show();
        }else{
            b.removeClass('caret').addClass('caret-r');
            menu.hide();
        }

        return false;
    });

    // 置顶
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100){
            $('#back-to-top').fadeIn();
        }else{
            $('#back-to-top').fadeOut();
        }
    });
    $('#back-to-top').on('click', function (e) {
        e.preventDefault();
        $('html,body').animate({scrollTop: 0}, 1000);
        return false;
    });
});