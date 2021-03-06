<?php

include_once("pico-functions.php");

/**
 * @TODO customise login page
 * @TODO customise admin area
 * @TODO plug in google maps
 * @TODO customise blogroll for 404 etc pages
 * @TODO write tracker library
*/

/**
 * get parent styles
 */
add_action( 'wp_enqueue_scripts', 'wp_pico_theme_enqueue_styles' );
function wp_pico_theme_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css', '', version_id() );
    wp_enqueue_style('twentysixteen-style',  get_stylesheet_directory_uri() . '/style.css', '', version_id() );
}


function wp_pico_disable_admin_bar() {
   if ( ! current_user_can('create_users') ) {
        add_filter('show_admin_bar', '__return_false');
    }
}
add_action( 'after_setup_theme', 'wp_pico_disable_admin_bar' );

/**
 * Redirect back to homepage and not allow access to
 * WP admin for Subscribers.
 */
function wp_pico_redirect_admin()
{
    if ( ! defined('DOING_AJAX') && ! current_user_can('create_users') ) {
        wp_redirect( site_url() );
        exit;
    }
}
add_action( 'admin_init', 'wp_pico_redirect_admin' );

/**
 * Redirect after login
 */
add_filter( 'login_redirect', 'wp_pico_login_redirect', 10, 3 );
function wp_pico_login_redirect( $redirect_to, $request, $user ) {
    //is there a user to check?
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        //check for admins
        if ( in_array( 'administrator', $user->roles ) ) {
            // redirect them to the default place
            return $redirect_to;
        } else {
            return home_url().'/mission-management';
        }
    } else {
        return $redirect_to;
    }
}

/**
 * Prevent authors from seeing others' posts
 */
add_action( 'load-edit.php', 'wp_pico_only_mine_load_edit' );
function wp_pico_only_mine_load_edit()
{
    add_action( 'request', 'wp_pico_only_mine_posts' );
}
function wp_pico_only_mine_posts( $query_vars )
{
    if ( ! current_user_can( $GLOBALS['post_type_object']->cap->edit_others_posts ) ) {
        $query_vars['author'] = get_current_user_id();
    }
    return $query_vars;
}

/**
 * remove all-mine-published filters on edit posts page
 */
add_filter( 'views_edit-post', 'wp_pico_only_mine_views_edit_post' );
function wp_pico_only_mine_views_edit_post( $views )
{
    if (! current_user_can( $GLOBALS['post_type_object']->cap->edit_others_posts)) {
        return array();
    }
    return $views;
}

/**
 * remove link to tools page
 */
add_action( 'admin_menu', 'wp_pico_remove_tools', 99 );
function wp_pico_remove_tools()
{
    if (array_key_exists('post_type_object', $GLOBALS) ) {
        if (! current_user_can( $GLOBALS['post_type_object']->cap->edit_others_posts)) {
            remove_menu_page( 'tools.php' );
        }
    }

}

/**
 * prettify missions page querystring
 * @param $aVars
 * @return array
 */
add_filter('query_vars', 'wp_pico_add_query_vars');
function wp_pico_add_query_vars($aVars)
{
    $aVars[] = "mission"; // represents the name as shown in the URL
    return $aVars;
}

add_filter('rewrite_rules_array', 'wp_pico_add_rewrite_rules');
function wp_pico_add_rewrite_rules($aRules)
{
    $aNewRules = array(
        'mission-control/([^/]+)/?$' => 'index.php?pagename=mission-control&mission=$matches[1]',
        'mission-preparation/([^/]+)/?$' => 'index.php?pagename=mission-preparation&mission=$matches[1]',
        'mission-flight/([^/]+)/?$' => 'index.php?pagename=mission-flight&mission=$matches[1]',
        'mission-archive/([^/]+)/?$' => 'index.php?pagename=mission-archive&mission=$matches[1]',
    );
    $aRules = $aNewRules + $aRules;
    return $aRules;
}

/**
 * load custom styles for login page
 */
function wp_pico_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/css/style-login.css' );
    wp_enqueue_script( 'custom-login', get_stylesheet_directory_uri() . '/js/ls-login.js' );
}
add_action( 'login_enqueue_scripts', 'wp_pico_login_stylesheet' );

define ('VERSION', '1.1');

function version_id() {
    if ( WP_DEBUG )
        return time();
    return VERSION;
}

function wp_pico_add_map_script()
{
    if (!is_page('mission-control')) {
        return true;
    }
    wp_enqueue_script( 'geometry', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDEYRnigI-5bwe9t4ulawHTGSrVywEMf4Q&libraries=geometry,places&ext=.js', array('jquery'));
    wp_enqueue_script( 'mission-flight-map', get_stylesheet_directory_uri() . '/js/flight-map.js', array('geometry'), version_id() );
}
add_action('wp_enqueue_scripts', 'wp_pico_add_map_script');