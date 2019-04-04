$(function () {
    //CONTROLE DO MENU MOBILE
    $('.mobile_action').click(function () {
        if (!$(this).hasClass('active')) {
            $(this).addClass('active');
            $('.menu_nav').animate({'left': '18px'}, 400);
        } else {
            $(this).removeClass('active');
            $('.menu_nav').animate({'left': '-100%'}, 300);
        }
    });

});