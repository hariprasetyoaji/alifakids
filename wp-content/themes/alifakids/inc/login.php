<?php 

function ak_login_stylesheet() {
    wp_enqueue_style( 'poppins-font', 'https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&display=swap' );
    wp_enqueue_style( 'lime-css', get_stylesheet_directory_uri() . '/assets/css/lime.min.css' );
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/assets/css/login.css' );
    //wp_enqueue_script( 'custom-login', get_stylesheet_directory_uri() . '/style-login.js' );
}
add_action( 'login_enqueue_scripts', 'ak_login_stylesheet' );

function ak_login_redirect( $redirect_to, $request, $user ){
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( in_array( 'administrator', $user->roles ) ) {
            $redirect_to = admin_url();
        } else {
            $redirect_to = home_url(); 
        }
    }
    return $redirect_to;
}

add_filter( 'login_redirect', 'ak_login_redirect', 10, 3 );