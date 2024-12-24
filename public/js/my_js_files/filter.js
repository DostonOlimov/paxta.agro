function changeDisplay(name) {
    //organization companies change
    var currentUrl = window.location.href;
    var url = new URL(currentUrl);

    // Set the new query parameter
    url.searchParams.set(name, '');

    // Modify the URL and trigger an AJAX request
    var newUrl = url.toString();
    window.history.pushState({
        path: newUrl
    }, '', newUrl);

    $.ajax({
        url: newUrl,
        method: "GET",
        success: function(response) {
            window.location.reload(true);
        }
    });
}
// get region
$(document).ready(function() {

    $('#region').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('region', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //organization companies change
    $('#organization').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('companyId[eq]', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //prepared companies change
    $('#prepared').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('factoryId[eq]', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //status change
    $('#status').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('status[eq]', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });

    //crop names change
    $('#crops_name').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('nameId[eq]', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //years change
    $('#year').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('year[eq]', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });
    //years change
    $('#stateId').change(function() {
        var selectedRegion = $(this).val();

        var currentUrl = window.location.href;
        var url = new URL(currentUrl);

        // Set the new query parameter
        url.searchParams.set('stateId[eq]', selectedRegion);

        // Modify the URL and trigger an AJAX request
        var newUrl = url.toString();
        window.history.pushState({
            path: newUrl
        }, '', newUrl);

        $.ajax({
            url: newUrl,
            method: "GET",
            success: function(response) {
                window.location.reload(true);
            }
        });
    });

});
