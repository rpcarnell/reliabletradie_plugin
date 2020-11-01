<?php
add_action( 'init', 'reliabletradie_process_registration' );
add_action('init', 'reliable_process_login');
add_action( 'reliabletradie_reset_password_notification', 'reset_password_notification', 10, 3);
//add_action('getTradies', 'findTradie', 10, 1);
add_action( 'reliabletradie_getTradies', array( 'RT_Shortcode_Find_Tradie', 'findTradie' ), 10, 1);
add_action('wp_login', 'redirect_tradie');

add_filter( 'post_found_actions', 'showtradieimage',10,1 );
add_filter( 'post_fnd_extimages', 'extraTRImages',10,1 );
add_filter('post_fnd_getTrades', 'post_fnd_getTrades', 10,1);

function register_my_menus() {
  register_nav_menus( array(
	'pluginbuddy_mobile' => 'PluginBuddy Mobile Navigation Menu',
	'footer_menu' => 'My Custom Footer Menu'
) );
}

add_action( 'init', 'register_my_menus' );
//wp_nav_menu( array( 'theme_location' => 'extra-menu', 'container_class' => 'my_extra_menu_class' ) );
