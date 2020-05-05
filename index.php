<?php
/*
    Plugin Name: Kneejerk Login Security
    Plugin URI: https://kneejerk.dev/
    Description: Secure your logins a little better
    Author: Ryan "Rohjay" Oeltjenbruns
    Author URI: https://rohjay.one/about
    Version: 0.1
    Requires at least: 5.0
    Requires PHP: 7.2
    Tags: Login, security
*/

// Shut off xmlrpc
add_filter('xmlrpc_enabled', '__return_false');
define('KJDLS_GET_PARAM_NAME', 'kjd-login-security');

add_filter( 'site_url', 'kjdls_secure_login_url', PHP_INT_MAX, 4 );
function kjdls_secure_login_url( $url, $path, $scheme, $blog_id = 1 ) {
    $random_string = kjdls_get_legit_value();

    if ( $scheme == 'login_post' ) {
        $url = add_query_arg( array( KJDLS_GET_PARAM_NAME => $random_string ) , $url );
    }

    return $url;
}

add_action( 'wp_authenticate', 'kjdls_check_secure_login', -1, 2 );
function kjdls_check_secure_login( $user, $pass ) {
    if ( !$user && !$pass ) {
        return;
    }
    $verify = kjdls_get_legit_value();
    if ( false && $verify != $_GET[KJDLS_GET_PARAM_NAME] ) {
        header( 'HTTP/1.1 302 Moved Temporarily' );
        header( 'Location: https://www.disney.com/' );
        exit();
    }
}

function kjdls_get_legit_value() {
    $salt = NONCE_SALT . NONCE_KEY . NONCE_SALT;
    return md5($salt . date('Y-m-d') . $salt);
}

