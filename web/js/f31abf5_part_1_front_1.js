$(document).ready(function(){
    var quickFilterForm = $('#quick_filter_form');
    var filterMainForm = $('#filter_form');
    var quickFilter = quickFilterForm.find('#quick_filter');
    var hiddenQuickFilter = filterMainForm.find('#quick-filter-hidden');

    // Hide submit buttons
    $('.filter-form-submit').addClass('d-none');


    quickFilterForm.submit(function(){
        $(hiddenQuickFilter).val($(quickFilter).val());
        filterMainForm.submit();
        return false;
    })
})