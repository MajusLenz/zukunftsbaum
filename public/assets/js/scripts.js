(function($) { $(document).ready(function() {

    // generic functionality to implement easy html buttons that can fadeIn/fadeOut other elements
    $("[data-toggle-show]").on("click", function () {
        var $this = $(this);

        var idsString = $this.data("toggle-show");
        var idsArray = idsString.split(" ");

        for(var i = 0; i < idsArray.length; i++) {
            var id = idsArray[i];
            var $toggleShowElement = $("#" + id);

            if($toggleShowElement.length !== 0) {
                $toggleShowElement.fadeIn(0);
            }
        }
    });
    $("[data-toggle-hide]").on("click", function () {
        var $this = $(this);

        var idsString = $this.data("toggle-hide");
        var idsArray = idsString.split(" ");

        for(var i = 0; i < idsArray.length; i++) {
            var id = idsArray[i];
            var $toggleHideElement = $("#" + id);

            if($toggleHideElement.length !== 0) {
                $toggleHideElement.fadeOut(0);
            }
            else{
                if(id === "this") {
                    $this.fadeOut(0);
                }
            }
        }
    });


}) }(window.jQuery));