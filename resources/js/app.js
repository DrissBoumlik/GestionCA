require('./bootstrap');

(($) => {
    // Default Ajax Configuration
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

})(jQuery);
