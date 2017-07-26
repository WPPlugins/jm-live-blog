jQuery( document ).ready( function() {
    jQuery( '#live-blog-add-row' ).on('click', function() {
        var row = jQuery( '.live-blog-empty-row.screen-reader-text' );
        row.addClass( 'new-row jm-live-blog-fields' );
        row.removeClass( 'live-blog-empty-row screen-reader-text' );
        row.insertBefore( '.jm-live-blog-fields:first' );
        jQuery( '.new-row .new-field' ).attr( "disabled",false );
        return false;
    } );

    jQuery( '.live-blog-remove-row' ).on( 'click', function() {
        jQuery( this ).parents( 'table' ).remove();
        return false;
    } );
    jQuery( '#live_blog_alert_color' ).wpColorPicker();
} );

