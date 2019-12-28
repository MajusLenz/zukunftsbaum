(function($) { $(document).ready(function() {

    // ADMIN OPERATIONS:

    // toggle between an option and its confirmation in the tree-overview-table
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


    // dynamically create more tree-information inputs to the new-tree-form
    var $newTreeForm = $(".admin-new-tree-form");
    var $addInformationButton = $newTreeForm.find(".admin-new-tree-add-information-button");
    var $newInformationBlueprint = $newTreeForm.find("#admin-new-tree-information-blueprint").children(".admin-new-tree-information");
    $newInformationBlueprint.detach();
    var $informationArea = $newTreeForm.find(".admin-new-tree-informations");
    var informationCounter = 1;

    $addInformationButton.on("click", function () {
        var $newInformation = $newInformationBlueprint.clone();

        var $nameInput = $newInformation.find(".admin-new-tree-information-name");
        $nameInput.attr("name", $nameInput.attr("name") + informationCounter);

        var $valueInput = $newInformation.find(".admin-new-tree-information-value");
        $valueInput.attr("name", $valueInput.attr("name") + informationCounter);

        informationCounter++;

        $newInformation.find(".admin-new-tree-delete-information-button").on("click", function () {
            $newInformation.remove();
        });
        
        $newInformation.appendTo($informationArea);
    });


    // delete all created tree-information inputs on form-reset of the new-tree-form
    $newTreeForm.on("reset", function () {
        $informationArea.children().remove();
    });


    // stop submission of form if new tree-name is not unique
    var $treeNameTds = $(".admin-index-table-tree-name");
    var $newTreeNameInput = $newTreeForm.find(".admin-new-tree-name");

    $newTreeForm.on("submit", function (e) {
        var newTreeName = $newTreeNameInput.val();

        $treeNameTds.each(function () {
            var thisTreeName = $(this).text();

            if(thisTreeName === newTreeName) {
                e.preventDefault();
                alert("Baum-Name '" + newTreeName + "' existiert bereits. Baumnamen dürfen nur einem einzigen Baum zugewiesen werden.");
                return false;
            }
        });
    });






}) }(window.jQuery));