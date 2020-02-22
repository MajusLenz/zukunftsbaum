(function($) { $(document).ready(function() {

    var updateHeaderAnimation = function () {
        var scroll = $(window).scrollTop();
        if (scroll > 10) {
            $(".navbar").addClass("navbar-scrolled");
            // $(".nav-link").css("color" , "#ffffff");
        }

        else{
            $(".navbar").removeClass("navbar-scrolled");
            // $(".nav-link").css("color" , "#ffffff");

        }
    };

    $(window).scroll(function(){
        updateHeaderAnimation();
    });

    // call one time on page load
    updateHeaderAnimation();

}) }(window.jQuery));