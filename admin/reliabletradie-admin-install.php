<?php
function do_install_reliableTradie() 
{ 
    reliableTradie_tables_install(); 
    reliableTradie_shortCodes_install();
}
function reliableTradie_shortCodes_install()
{
    reliableTradie_create_page( 'rt-my-account', 'reliabletradie_myaccount_page_id', __( 'Login / Register', 'reliabletradie' ), '[reliabletradie_my_account]' );
    reliableTradie_create_page( 'rt-lost-password', 'reliabletradie_lost_password_page_id', __( 'Lost Password', 'reliabletradie' ), '[reliabletradie_lost_password]' );
    reliableTradie_create_page( 'rt-tr-search', 'reliabletradie_find_tradie_page_id', __( 'Find Tradie', 'reliabletradie' ), '[reliabletradie_find_tradie]' );
    reliableTradie_create_page( 'rt-tr-found', 'reliabletradie_tradies_found_page_id', __( 'Tradies Found', 'reliabletradie' ), '[reliabletradie_tradie_found]' );
    reliableTradie_create_page( 'rt-tr-tradie', 'reliabletradie_tradie_setup_page_id', __( 'Tradie Setup', 'reliabletradie' ), '[reliabletradie_tradie_setup]' );
    reliableTradie_create_page( 'rt-tr-showtr', 'reliabletradie_tradie_show_page_id', __( 'Tradie Show', 'reliabletradie' ), '[reliabletradie_tradie_showtra]' );
    //reliableTradie_create_page( 'rt-tr-search', 'reliabletradie_find_tradie_page_id', __( 'Find Tradie', 'reliabletradie' ), '[reliabletradie_find_tradie]' );
    //reliableTradie_create_page( esc_sql( _x( 'view-order', 'page_slug', 'woocommerce' ) ), 'reliabletradie_view_order_page_id', __( 'View Order', 'woocommerce' ), '[reliabletradie_view_order]', woocommerce_get_page_id( 'myaccount' ) );

}
function reliableTradie_create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;

	$option_value = get_option( $option );

	if ( $option_value > 0 && get_post( $option_value ) )
		return;

	$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug ) );
	if ( $page_found ) {
		if ( ! $option_value )
			update_option( $option, $page_found );
		return;
	}

	$page_data = array(
        'post_status' 		=> 'publish',
        'post_type' 		=> 'page',
        'post_author' 		=> 1,
        'post_name' 		=> $slug,
        'post_title' 		=> $page_title,
        'post_content' 		=> $page_content,
        'post_parent' 		=> $post_parent,
        'comment_status' 	=> 'closed'
    );
    $page_id = wp_insert_post( $page_data );

    update_option( $option, $page_id );
}
function reliableTradie_tables_install() {
    global $wpdb;

	$wpdb->hide_errors();
        $collate = '';

    if ( $wpdb->has_cap( 'collation' ) ) {
		if( ! empty($wpdb->charset ) )
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		if( ! empty($wpdb->collate ) )
			$collate .= " COLLATE $wpdb->collate";
    }
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $reliableTradie_tables = "
CREATE TABLE {$wpdb->prefix}reliabletradie_providers (
  id int(13) NOT NULL AUTO_INCREMENT,
  company_name varchar(250) NOT NULL,
  provider_name varchar(250) NOT NULL,
  provider_email varchar(250) NOT NULL,
  phone varchar(20) NOT NULL,
  user_id int(13) NOT NULL,
  filteroptions varchar(100) NOT NULL,
  catering_persons_min int(4) DEFAULT NULL,
  catering_persons_max int(4) DEFAULT NULL,
  regions varchar(40) DEFAULT NULL,
  cities text,
  avcheck tinyint(1) NOT NULL DEFAULT '0',
  couponcode varchar(250) DEFAULT NULL,
  howdidyoufindus varchar(300) DEFAULT NULL,
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  published tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)$collate;
";    
    dbDelta( $reliableTradie_tables );
}
/*
 * CREATE TABLE IF NOT EXISTS `wp_reliabletradie_usertype` (
  `user_id` int(13) NOT NULL,
  `usertype` varchar(10) NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 */