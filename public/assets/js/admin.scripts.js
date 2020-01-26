(function($) { $(document).ready(function() {

    // New Tree Form:

    // dynamically create more tree-information inputs to the new-tree-form
    var $newTreeForm = $(".admin-new-tree-form");
    var $newTreeAddInformationButton = $newTreeForm.find(".admin-new-tree-add-information-button");
    var $newTreeNewInformationBlueprint = $newTreeForm.find("#admin-new-tree-information-blueprint").children(".admin-new-tree-information");
    $newTreeNewInformationBlueprint.detach();
    var $newTreeInformationArea = $newTreeForm.find(".admin-new-tree-informations");
    var newTreeInformationCounter = 1;

    $newTreeAddInformationButton.on("click", function () {
        var $newInformation = $newTreeNewInformationBlueprint.clone();

        var $nameInput = $newInformation.find(".admin-new-tree-information-name");
        $nameInput.attr("name", $nameInput.attr("name") + newTreeInformationCounter);

        var $valueInput = $newInformation.find(".admin-new-tree-information-value");
        $valueInput.attr("name", $valueInput.attr("name") + newTreeInformationCounter);

        newTreeInformationCounter++;

        $newInformation.find(".admin-new-tree-delete-information-button").on("click", function () {
            $newInformation.remove();
        });
        
        $newInformation.appendTo($newTreeInformationArea);
    });


    // delete all created tree-information inputs on form-reset of the new-tree-form
    $newTreeForm.on("reset", function () {
        $newTreeInformationArea.children().remove();
    });


    // stop submission of form if new tree-name is not unique
    var $treeNameTds = $(".admin-tree-name-display");

    var treeNameIsUnique = function (treeName, ignoreTreeName) {
        var result = true;

        $treeNameTds.each(function () {
            var thisTreeName = $(this).text();

            if(thisTreeName === treeName && thisTreeName !== ignoreTreeName) {
                result = false;
            }
        });

        return result;
    };

    var $newTreeNameInput = $newTreeForm.find(".admin-new-tree-name");

    $newTreeForm.on("submit", function (e) {
        var newTreeName = $newTreeNameInput.val();

        if( !treeNameIsUnique(newTreeName) ) {
            e.preventDefault();
            alert("Baumname '" + newTreeName + "' existiert bereits. Baumnamen dürfen nur einem einzigen Baum zugewiesen werden.");
        }
    });


    // Edit Tree Form:

    // dynamically create more tree-information inputs to the edit-tree-form
    var $editTreeNewInformationBlueprint = $(".admin-edit-tree-information-blueprint").children(".admin-edit-tree-information");
    var $editTreeAddInformationButton = $(".admin-edit-tree-add-information-button");
    
    $editTreeAddInformationButton.on("click", function () {
        var $this = $(this);

        var $editTreeForm = $this.parents(".admin-edit-tree-form");
        var $editTreeAddedInformationArea = $editTreeForm.find(".admin-edit-tree-informations-added");

        var $newInformationDiv = $editTreeNewInformationBlueprint.clone();
        var $newInformation = $("<li></li>").append($newInformationDiv);

        $newInformation.find(".admin-edit-tree-information-delete-old-information-button").on("click", function () {
            $newInformation.remove();
        });

        $newInformation.appendTo($editTreeAddedInformationArea);
    });

    // on submission of edit-form, add a number to every Information to make them distinguishable
    // and check if the new tree-name is unique
    var $editTreeForms = $(".admin-edit-tree-form");

    $editTreeForms.on("submit", function (e) {
        var $this = $(this);

        var newTreeName = $this.find(".admin-edit-tree-name-input input").val();
        var oldTreeName = $this.prev(".admin-tree-row-display").find(".admin-tree-name-display").text();

        if( !treeNameIsUnique(newTreeName, oldTreeName) ) {
            e.preventDefault();
            alert("Baumname '" + newTreeName + "' existiert bereits. Baumnamen dürfen nur einem einzigen Baum zugewiesen werden.");
        }

        var editTreeInformationCounter = 1;

        var $informations = $this.find(".admin-edit-tree-information");

        $informations.each(function () {
            var $information = $(this);

            var $nameInput = $information.find(".admin-edit-tree-information-name");
            $nameInput.attr("name", $nameInput.attr("name") + editTreeInformationCounter);

            var $valueInput = $information.find(".admin-edit-tree-information-value");
            $valueInput.attr("name", $valueInput.attr("name") + editTreeInformationCounter);

            editTreeInformationCounter++;
        });
    });

    // function to delete newly added informations in edit-tree-form
    var deleteAddedInformations = function ($informations) {
        $informations.each(function () {
            var $informationLi = $(this).parent("li");
            $informationLi.detach();
        });
    };

    // function to delete old informations in edit-tree-form
    var deleteOldInformations = function ($informations) {
        $informations.each(function () {
            var $informationLi = $(this).parent("li");
            $informationLi.fadeOut(0);

            $informationLi.find("select, textarea").prop("disabled", true);
        });
    };

    // function to recover deleted old informations in edit-tree-form
    var resetOldInformations = function ($informations) {
        $informations.each(function () {
            var $informationLi = $(this).parent("li");
            $informationLi.fadeIn(0);

            $informationLi.find("select, textarea").prop("disabled", false);
        });
    };

    // delete added information on delete-button click
    $(document).on("click", ".admin-edit-tree-information-delete-added-information-button", function () {
        var $information = $(this).parents(".admin-edit-tree-information-added");

        deleteAddedInformations($information);
    });

    // delete old information on delete-button click
    $(".admin-edit-tree-information-delete-old-information-button").on("click", function () {
        var $information = $(this).parents(".admin-edit-tree-information-old");

        deleteOldInformations($information);
    });

    // delete added informations and recover old informations on reset of edit-form
    $editTreeForms.on("reset", function () {
        var $form = $(this);

        var $addedInformations = $form.find(".admin-edit-tree-information-added");
        deleteAddedInformations($addedInformations);

        var $oldInformations = $form.find(".admin-edit-tree-information-old");
        resetOldInformations($oldInformations);
    });





}) }(window.jQuery));