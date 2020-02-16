(function($) { $(document).ready(function() {

    // FILTER LOGIC
    var $form = $(".search-output-form");
    var $searchInputs = $form.find(".search-output-form-input");
    var $allTrees = $(".tree-search-result-item");
    var $allTreesParent = $allTrees.parent();
    var $noTreesFoundMessage = $(".no-trees-found-message");

    var updateSearchResult = function () {
        $allTrees.detach();
        var $searchResultTrees = $allTrees;

        // for each search-input: filter set of matching trees
        $searchInputs.each(function () {
            var $searchInput = $(this);
            var isValueInput = $searchInput.hasClass("value-input");
            var isRangeInput = $searchInput.hasClass("range-input");
            var searchInputName = $searchInput.attr("name");
            var searchInputValue = $searchInput.children("option:selected").val();

            if(searchInputValue === "all" || searchInputValue === "") {
                return; // ignore searchInput. (like "continue;" in loop)
            }
            
            $searchResultTrees = $searchResultTrees.filter(function () {
                var $tree = $(this);
                var $searchableInformations = $tree.find(".searchable-information");
                var treeMatches = false;

                $searchableInformations.each(function () {
                    var $searchableInformation = $(this);
                    var searchableInformationName = $searchableInformation.attr("name");
                    var searchableInformationValue = $searchableInformation.val();

                    if(searchableInformationName === searchInputName) {

                        if(isValueInput) {

                            if (searchableInformationValue === searchInputValue) {
                                treeMatches = true;
                                return false; // ignore rest of searchableInformations. (like "break;" in loop)
                            }
                        }

                        else if(isRangeInput) {
                            // Example: 30+
                            if(searchInputValue.indexOf("+") !== -1) {
                                var greaterThan = parseInt( searchInputValue.split("+")[0] );

                                if (searchableInformationValue > greaterThan) {
                                    treeMatches = true;
                                    return false; // ignore rest of searchableInformations. (like "break;" in loop)
                                }
                            }
                            // Example: 10-20
                            else if(searchInputValue.indexOf("-") !== -1) {
                                var splitted = searchInputValue.split("-");
                                var min = parseInt( splitted[0] );
                                var max = parseInt( splitted[1] );

                                if (searchableInformationValue >= min && searchableInformationValue <= max) {
                                    treeMatches = true;
                                    return false; // ignore rest of searchableInformations. (like "break;" in loop)
                                }
                            }
                        }
                    }
                });

                return treeMatches;
            });
        });

        $allTreesParent.prepend($searchResultTrees);

        if($searchResultTrees.length === 0) {
            $noTreesFoundMessage.fadeIn(0);
        }
        else{
            $noTreesFoundMessage.fadeOut(0);
        }
    };


    // EXECUTE FILTER LOGIC ON PAGE-LOAD WITH URL-PARAMS
    var urlParamsInterface = new URLSearchParams(window.location.search);
    var urlParams = urlParamsInterface.entries();

    var iteration;
    do {
        iteration = urlParams.next();
        if(iteration.done) {
            break;
        }

        var paramPair = iteration.value;
        var paramName = paramPair[0];
        var paramValue = paramPair[1];

        var $paramSearchInput = $searchInputs.filter( $('[name="' + paramName + '"]') );
        $paramSearchInput.find('option[value="' + paramValue + '"]').prop("selected", true);
    } while (true);

    updateSearchResult();


    // BIND FILTER LOGIC TO EVENTS
    $searchInputs.on("change", function (e) {
        updateSearchResult();
    });

    // $form.on("submit", function (e) {
    //     updateSearchResult();
    //     e.preventDefault();
    //     return false;
    // });


}) }(window.jQuery));