(function($) { $(document).ready(function() {
    $(window).scroll(function(){
        var scroll = $(window).scrollTop();
        if (scroll > 10) {
            $(".navbar").addClass("navbar-scrolled");
            // $(".nav-link").css("color" , "#ffffff");
        }

        else{
            $(".navbar").removeClass("navbar-scrolled");
            // $(".nav-link").css("color" , "#ffffff");

        }
    });


}) }(window.jQuery));