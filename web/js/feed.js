$(document).ready(function() {
    $('.feed-selector-box span').on('click', function() {
        if ($(this).attr('id') != 'feed-all') {
            $('.post').not('.'+$(this).attr('id')).slideUp(500);
            $('.'+$(this).attr('id')).slideDown(500);
        } else {
            $('.post').slideDown(500);
        }
    });
});
