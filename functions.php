<?php
/*
*
*Part 2 - WP preparation
*	-Create chiled Theme for “twenty-twenty” 
*	-enqueue parent rtl styles (Hebrew wordpress installation)
*/
function ns_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style-rtl.css' );
}
add_action( 'wp_enqueue_scripts', 'ns_enqueue_styles' );

