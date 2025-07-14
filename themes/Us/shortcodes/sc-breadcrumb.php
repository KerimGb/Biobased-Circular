<?php
add_shortcode( 'breadcrumb', 'breadcrumb' );
function breadcrumb( $atts, $content = '' )
{
    if ( function_exists('yoast_breadcrumb') ) {


    	yoast_breadcrumb( '</p><p id=“breadcrumbs”>','</p><p>' );


    }
}

?>
