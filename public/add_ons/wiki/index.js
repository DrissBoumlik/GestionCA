$(function () {
    $('.toggle-summary').on('click', function () {
        $('.wiki-summary').toggleClass('visible');
    });
    $('.close-summary').on('click', function () {
        $('.wiki-summary').removeClass('visible');
    });
});