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

/*
*Part 3 - Users
*	-Create editor user
*/
function add_wptest_user() {
    $username = 'wp-test';
    $email = 'wptest@elementor.com';
    $password = '123456789';
	
	//check if username if exist
    $user_id = username_exists( $username );
	
	//if no username found or email create user
    if ( !$user_id && email_exists($email) == false ) {
        $user_id = wp_create_user( $username, $password, $email );
        if( !is_wp_error($user_id) ) {
            $user = get_user_by( 'id', $user_id );
            $user->set_role( 'editor' );
        }
    }
}
add_action('init', 'add_wptest_user');

//Disable wp admin bar for this user 
function disable_editor_admin_bar() {
	global $current_user;
	wp_get_current_user();
	if ($current_user->user_login == 'wp-test') {
		show_admin_bar(false);
	}
}
add_action('after_setup_theme', 'disable_editor_admin_bar');