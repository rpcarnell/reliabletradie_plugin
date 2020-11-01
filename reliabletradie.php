<?php
/*
Plugin Name: ReliableTradie
Plugin URI: http://example.com/wordpress-plugins/my-plugin
Description: still being worked on
Version: 1.0
Author: Roddy P Carbonell
Author URI: http://www.redacron.com
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
register_activation_hook(__FILE__,'reliableTradie_install');
define('PER_RESULT', 2);
function reliableTradie_install()
{
    $reliat = new reliableTradieInstall();
}
if ( ! class_exists( 'reliableTradieInstall' ) ) 
{
    class reliableTradieInstall 
    {
        public function __construct() { $this->install(); }
        public function install()
        {
	    include('admin/reliabletradie-admin-install.php');
            do_install_reliableTradie();
	}
    }
}
if ( ! class_exists( 'reliableTradie' ) ) 
{
    class reliableTradie 
    {
        var $templateDir;
        var $tradies = false;
        var $messages;
        var $plugin_path;
        public function __construct() 
        {
            $this->templateDir = apply_filters('getTemplateDir', $this->getTemplateDir());
            $this->loadFiles();
            $this->createTradies();
            $RT_Shortcodes = new RT_Shortcodes();
            define('RT_UPLOAD_PATH', $this->plugin_path()."/userimages");
        }
        function loadFiles()
        {
            include_once('reliabletradie-hooks.php');
            include_once('reliabletradie-functions.php');
            include_once('reliabletradie-ajax.php');
            include_once('classes/class-rt-shortcodes.php');
            include_once('classes/class-rt-findtradie.php');
            include_once('classes/shortcodes/class-rt-shortcode-lost-password.php');
            include_once('classes/shortcodes/class-rt-shortcode-tradie-setup.php');
        }
        function get_wp_installation()
        {
            $full_path = getcwd();
            $ar = explode("wp-", $full_path);
            return $ar[0];
        }
        public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;

		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
        public function plugin_url() {  
		return untrailingslashit( plugins_url( '/', __FILE__ ));
	}
        public function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class' => 'reliabletradie',
			'before' => null,
			'after' => null
		)
	){
		ob_start();

		$before 	= empty( $wrapper['before'] ) ? '<div class="' . $wrapper['class'] . '">' : $wrapper['before'];
		$after 		= empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		echo $before;
		call_user_func( $function, $atts );
		echo $after;

		return ob_get_clean();
	}
        /*function mailer()
        {
            //sendEmail($_POST['email_2'], "Welcome to Reliable Tradie", "<p>Hello, welcome to Reliable Tradie.</p><p>Your username is $username, and your password is $password</p>");
			
        }*/
        function getTemplateDir()
        {
            return plugin_dir_path(__FILE__)."templates";
        }
        function get_page_id( $page ) {
		$page = apply_filters('reliabletradie_get_' . $page . '_page_id', get_option('reliabletradie_' . $page . '_page_id'));
		return ( $page ) ? $page : -1;
	}
        function createTradies()
        {
            global $wpdb;
            $query = "SELECT * FROM ".$wpdb->prefix."reliabletradie_trades ORDER BY trade";
            $rows = $wpdb->get_results($query);
            $r2 = array();
            foreach($rows as $row)
            {
                $r2[$row->id] = $row->trade;
            }
            $this->tradies = $r2;
            //$this->tradies = array(0 => 'plumber', 1 =>'electrician');
        }
        public function verify_nonce( $action, $method='_POST', $error_message = false ) {

		$name = 'reliable_token';
		$action = 'reliabletradie-' . $action;

		if ( $error_message === false ) $error_message = __( 'Action failed. Please refresh the page and retry.', 'reliabletradie' );

		if ( ! in_array( $method, array( '_GET', '_POST', '_REQUEST' ) ) ) $method = '_POST';

		if ( isset($_REQUEST[$name] ) && wp_verify_nonce( $_REQUEST[$name], $action ) ) return true;

		if ( $error_message ) $this->add_error( $error_message );

		return false;
	}
        public function nonce_field( $action, $referer = true , $echo = true ) {
		return wp_nonce_field('reliabletradie-' . $action, 'reliable_token', $referer, $echo );
	}
        public function add_error( $error ) {
		$this->errors[] = apply_filters( 'reliabletradie_add_error', $error );
	}
        public function add_message( $message ) {
		$this->messages[] = apply_filters( 'reliabletradie_add_message', $message );
	}
        public function error_count() {
		return sizeof( $this->errors );
	}
        public function getLocations()
        {
            global $wpdb;
            $rows = $wpdb->get_Results("SELECT * FROM ".$wpdb->prefix."reliabletradie_locations group by concat(suburb,'_',state) ORDER BY state, suburb");
             
            return $rows;
        }
        public function getLocStates()
        {
            global $wpdb;
            $rows = $wpdb->get_Results("SELECT * FROM ".$wpdb->prefix."reliabletradie_locations GROUP BY state ORDER BY state");
             
            return $rows;
        }
        public function message_count() {
		return sizeof( $this->messages );
	}
        public function get_errors() {
		return (array) $this->errors;
	}


	/**
	 * Get messages.
	 *
	 * @access public
	 * @return array
	 */
	public function get_messages() {
		return (array) $this->messages;
	}
        public function show_messages()
        {
            if ( $this->error_count() > 0  )
			reliabletradie_get_template( 'rt/errors.php', array(
					'errors' => $this->get_errors()
				) );


		if ( $this->message_count() > 0  )
			  reliabletradie_get_template( 'rt/messages.php', array(
					'messages' => $this->get_messages()
				) );

		$this->clear_messages();
        }
        public function clear_messages() {
		$this->errors = $this->messages = array();
		unset( $this->session->errors, $this->session->messages );
	}
        public function get_RT_Upload_Path()
        {
            return RT_UPLOAD_PATH;
        }
    }
    $GLOBALS['reliableTradie'] = new reliableTradie();
    $GLOBALS['register_form'] = false;
}

//add_action( 'user_register', 'myplugin_registration_save', 10, 1 );

function myplugin_registration_save( $user_id ) {

    print_r($user_id);
    print_r($_POST);
    //exit;

}
function reliabletradie_get_page_id( $page ) {
		$page = apply_filters('reliabletradie_get_' . $page . '_page_id', get_option('reliabletradie_' . $page . '_page_id'));
		return ( $page ) ? $page : -1;
	}
function reliabletradierules()
{  
    global $reliableTradie;
    
    include_once($reliableTradie->templateDir."/myaccount/form-login.php");
}
function reliabletradie_my_account()
{ 
    global $reliableTradie;  
    wp_enqueue_style( 'reliabletradie_search_css', $reliableTradie->plugin_url(). '/assets/css/reliabletradie.css'  );
     wp_enqueue_script( 'woocommerce_admin', $reliableTradie->plugin_url() . '/assets/js/rt.js');
    if (isset($_POST['register']) && $_POST['register'] == 1)
    {
        include_once('classes/class-rt-registertradie.php');
        $rrt = new RT_RegisterTradie();
    }
    include_once($reliableTradie->templateDir."/myaccount/form-login.php");
   
} 

function redirect_tradie() {
      global $reliableTradie;
      $page_id = $reliableTradie->get_page_id('tradie_setup');
      $location = 'index.php?page_id='.$page_id;
      $location = set_url_scheme(get_site_url()."/".$location);
      wp_safe_redirect( $location);
      die;
}
function createMainImage($row)
{
    global $wpdb; 
    if ($row->id > 0)
    {
       $q = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'reliabletradie_providerimages WHERE provider_id=%d LIMIT 1', $row->user_id);
       $row2 = $wpdb->get_row($q);
       if ($row2)
       {
           $q = $wpdb->prepare('UPDATE '.$wpdb->prefix.'reliabletradie_providers SET main_image= %d WHERE user_id=%d LIMIT 1', $row2->id, $row->user_id);
           $wpdb->query($q);
           //echo $q;
           return $row2->id;
       }
    }
}


//add_shortcode('reliabletradie_rules', 'reliabletradierules');
add_shortcode('reliabletradie_my_account', 'reliabletradie_my_account');
//add_shortcode('reliabletradie_lost_password', 'reliabletradie_lost_password');

 

?>
