(function() {
    tinymce.create( 'tinymce.plugins.jm_live_blog', {
        init : function( ed, url ) {
            url = url.slice( 0, -3 );
            ed.addButton( 'jm_live_blog', {
                title : 'JM Live Blog',
                cmd : 'jm_live_blog',
                image : url + '/images/jm-live-blog-logo.png'
            } );
            ed.addCommand( 'jm_live_blog', function() {
                var returnText = '[jm-live-blog title="" description=""]';
                ed.execCommand( 'mceInsertContent', 0, returnText );
            } );
        },
        createControl : function( n, cm ) {
            return null;
        },
        getInfo : function() {
            return {
                longname : 'JM Live Blog',
                author : 'Jacob Martella',
                authorurl : 'http://jacobmartella.com',
                infourl : 'http://www.jacobmartella.com/wordpress/wordpress-plugins/jm-live-blog',
                version : "1.0"
            };
        }
    } );
    tinymce.PluginManager.add( 'jm_live_blog', tinymce.plugins.jm_live_blog );
} )();