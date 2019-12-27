(function($) { $(document).ready(function() {

    // ADMIN OPERATIONS:

    // toggle between an option and its confirmation
    $("[data-toggle-show]").each(function () {
        var $this = $(this);

        var toggleShowId = $this.data("toggle-show");
        var toggleShowElement = $("#" + toggleShowId);

        if(toggleShowElement.length === 0) {
            return;
        }

        var toggleHideId = $this.data("toggle-hide");
        var toggleHideElement = $this;

        if(toggleHideId) {
            toggleHideElement = $("#" + toggleHideId);

            if(toggleHideElement.length === 0) {
                return;
            }
        }

        $this.on("click", function () {
            toggleShowElement.fadeIn(0);
            toggleHideElement.fadeOut(0);
        });
    });





}) }(window.jQuery));