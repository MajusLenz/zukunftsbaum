(function($) { $(document).ready(function() {

    var $form = $(".search-output-form");
    var $inputs = $form.find("select.search-output-form-input");
    var $allTrees = $(".tree-search-result-item");
    var $allTreesParent = $allTrees.parent();



    console.log($allTreesParent);




    $form.on("submit", function (e) {

        $allTrees.detach();
        var $filteredTrees = $allTrees;

        // TODO get values of all 4 inputs

        // for each tree: compare to all for input-values. If one does not fit, pop from $filteredTrees and continue;
                                                                                            // research: how to continue in jquery->each ??!
                                                            // ....or empty set and put in only if fits



        e.preventDefault();
        return false;
    });

}) }(window.jQuery));