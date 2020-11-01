<?php
function showtradieimage($row)
{
    global $wpdb;
    $imageDefault = get_site_url()."/wp-content/plugins/reliatabletradie/assets/images/HandyTradie.jpg";
    if (!isset ($row->main_image) || !is_numeric($row->main_image) || $row->main_image == 0) 
    {
        echo "<div style=\"float: left; margin-right: 10px; width: 180px;\"><img title='".$imageDefault."' src=\"$imageDefault\" style='width: 200px;' alt=\"\" /></div>";
        return; 
    }
    $q = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'reliabletradie_providerimages WHERE id=%d LIMIT 1', $row->main_image);
     
    $row = $wpdb->get_row($q);
    $url = get_site_url();
    if ($row->provider_image)
      echo "<div style=\"float: left; margin-right: 10px; \"><img title='".$row->provider_image."' src=\"".$url."/".$row->image_url."\" style='width: 200px;' alt=\"\" /></div>";
    else { echo "<div style=\"float: left; margin-right: 10px; \"><img title='".$imageDefault."' src=\"$imageDefault\" style='width: 200px;' alt=\"\" /></div>"; }
}
function extraTRImages($row)
{
    global $wpdb;
    $q = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'reliabletradie_providerimages WHERE provider_id=%d AND id != %d LIMIT 8', $row->user_id, $row->main_image);
    $rows = $wpdb->get_results($q);
    $url = get_site_url();
    $a = 1;
    //print_r($rows);
    echo "<div style='width: 600px;'>";
    foreach ($rows as $row) {
      echo "<img class='proviextraimg' title='".$row->provider_image."' src=\"".$url."/".$row->image_url."\" alt=\"\" />";
      if ($a % 4 == 0 && $a > 0) { echo "<div style='clear: both;'></div><br />"; }
       $a++;
    }
     echo "</div>";
}
function post_fnd_getTrades($row)
{
    global $wpdb;
    $trades = explode('-', $row->filteroptions);
     
    $trade = array();
    foreach($trades as $trd)
    {
        if (!is_numeric($trd)) continue;
        $trade[] = $trd;
    }
    $query = "SELECT trade FROM ".$wpdb->prefix."reliabletradie_trades WHERE id IN (".implode($trade, ',').")";
    $rows = $wpdb->get_results($query);
    if ($rows)
    {
        echo "<ul>";
        foreach ($rows as $r)
        { echo "<li>$r->trade</li>"; }
        echo "</ul>";
    }
}
function set_rp_content_type( $content_type ){return 'text/html';}
function reliabletradie_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	global $reliableTradie;

	if ( $args && is_array($args) )
		extract( $args );

	$located = $reliableTradie->getTemplateDir()."/$template_name";//reliable_locate_template( $template_name, $template_path, $default_path );

	//do_action( 'reliable_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	//do_action( 'reliable_after_template_part', $template_name, $template_path, $located, $args );
}
function reset_password_notification($user_login, $reset_key, $email)
{
    //echo "userlogin is $user_login and key is $key and email is $email";
     //$this->id 				= 'customer_reset_password';
		$title 			=  'Reset password for '.$user_login;
		$description		= __( 'Customer reset password emails are sent when a customer resets their password.', 'reliabletradie' );

		$template_html 	= 'emails/customer-reset-password.php';
		//$template_plain 	= 'emails/plain/customer-reset-password.php';

		$subject 			= __( 'Password Reset for {blogname}', 'reliabletradie');
		$heading      	= __( 'Password Reset Instructions', 'reliabletradie');
    global $reliableTradie;
    $tempDir = $reliableTradie->getTemplateDir();
    ob_start();
    include_once($tempDir."/emails/customer-reset-password.php");
    $out2 = ob_get_contents();
    ob_end_clean();
    add_filter( 'wp_mail_content_type', 'set_rp_content_type' ); 
    
    wp_mail($email,  $title, $out2, $heading);
    exit;
}
function reliable_process_login() {   
	global $reliableTradie;

	if ( ! empty( $_POST['reliable_login'] ) ) {

		$reliableTradie->verify_nonce( 'login' );

		try {
			$creds = array();

			if ( empty( $_POST['username'] ) )
				throw new Exception( '<strong>' . __( 'Error', 'reliable' ) . ':</strong> ' . __( 'Username is required.', 'reliable' ) );
			if ( empty( $_POST['password'] ) )
				throw new Exception( '<strong>' . __( 'Error', 'reliable' ) . ':</strong> ' . __( 'Password is required.', 'reliable' ) );

			if ( is_email( $_POST['username'] ) ) {
				$user = get_user_by( 'email', $_POST['username'] );

				if ( isset( $user->user_login ) )
					$creds['user_login'] 	= $user->user_login;
				else
					throw new Exception( '<strong>' . __( 'Error', 'reliable' ) . ':</strong> ' . __( 'A user could not be found with this email address.', 'reliable' ) );
			} else {
				$creds['user_login'] 	= $_POST['username'];
			}

			$creds['user_password'] = $_POST['password'];
			$creds['remember']      = true;
			$secure_cookie          = is_ssl() ? true : false;
			$user                   = wp_signon( $creds, $secure_cookie );

			if ( is_wp_error( $user ) ) {
				throw new Exception( $user->get_error_message() );
			} else {

				if ( ! empty( $_POST['redirect'] ) ) {
					$redirect = esc_url( $_POST['redirect'] );
				} elseif ( wp_get_referer() ) {
					$redirect = esc_url( wp_get_referer() );
				} else {
					$redirect = esc_url( get_permalink( reliable_get_page_id( 'myaccount' ) ) );
				}

				wp_redirect( apply_filters( 'reliable_login_redirect', $redirect, $user ) );
				exit;
			}
		} catch (Exception $e) {  
			$reliableTradie->add_error( $e->getMessage() );
		}
	}
}
function reliabletradie_process_registration() 
{
	global $reliableTradie, $current_user;
        
	if ( ! empty( $_POST['reliable_register'] ) ) {
           // print_r($_POST); exit;
        include_once('classes/class-rt-registertradie.php');
        $rrt = new RT_RegisterTradie();  
       // do_action( 'reliabletradie_beforecreating_tradie');
		$reliableTradie->verify_nonce( 'register' );

		// Get fields
		$user_email = isset( $_POST['email'] ) ? trim( $_POST['email'] ) : '';
		$password   = isset( $_POST['password'] ) ? trim( $_POST['password'] ) : '';
		$password2  = isset( $_POST['password2'] ) ? trim( $_POST['password2'] ) : '';
		$user_email = apply_filters( 'user_registration_email', $user_email );

		if ( 1 ==1 || get_option( 'reliabletradie_registration_email_for_username' ) == 'no' ) {

			$username 				= isset( $_POST['username'] ) ? trim( $_POST['username'] ) : '';
			$sanitized_user_login 	= sanitize_user( $username );

			// Check the username
			if ( $sanitized_user_login == '' ) {
				$reliableTradie->add_error( '<strong>' . __( 'ERROR', 'reliabletradie' ) . '</strong>: ' . __( 'Please enter a username.', 'reliabletradie' ) );
			} elseif ( ! validate_username( $username ) ) {
				$reliableTradie->add_error( '<strong>' . __( 'ERROR', 'reliabletradie' ) . '</strong>: ' . __( 'This username is invalid because it uses illegal characters. Please enter a valid username.', 'reliabletradie' ) );
				$sanitized_user_login = '';
			} elseif ( username_exists( $sanitized_user_login ) ) {
				$reliableTradie->add_error( '<strong>' . __( 'ERROR', 'reliabletradie' ) . '</strong>: ' . __( 'This username is already registered, please choose another one.', 'reliabletradie' ) );
			}
//****************WARNING: Look above, we are not using this **********************
		} else {

			$username 				= $user_email;
                        //echo $username; exit;
			$sanitized_user_login 	= sanitize_user( $username );

		}

		// Check the e-mail address
		if ( $user_email == '' ) {
			$reliableTradie->add_error( '<strong>' . __( 'ERROR', 'reliabletradie' ) . '</strong>: ' . __( 'Please type your e-mail address.', 'reliabletradie' ) );
		} elseif ( ! is_email( $user_email ) ) {
			$reliableTradie->add_error( '<strong>' . __( 'ERROR', 'reliabletradie' ) . '</strong>: ' . __( 'The email address isn&#8217;t correct.', 'reliabletradie' ) );
			$user_email = '';
		} elseif ( email_exists( $user_email ) ) {
			$reliableTradie->add_error( '<strong>' . __( 'ERROR', 'reliabletradie' ) . '</strong>: ' . __( 'This email is already registered, please choose another one.', 'reliabletradie' ) );
		}

		// Password
		if ( ! $password ) $reliableTradie->add_error( __( 'Password is required.', 'reliabletradie' ) );
		if ( ! $password2 ) $reliableTradie->add_error( __( 'Re-enter your password.', 'reliabletradie' ) );
		if ( $password != $password2 ) $reliableTradie->add_error( __( 'Passwords do not match.', 'reliabletradie' ) );

		// Spam trap
		if ( ! empty( $_POST['email_2'] ) )
			$reliableTradie->add_error( __( 'Anti-spam field was filled in.', 'reliabletradie' ) );

		// More error checking
		$reg_errors = new WP_Error();
		do_action( 'register_post', $sanitized_user_login, $user_email, $reg_errors );
		$reg_errors = apply_filters( 'registration_errors', $reg_errors, $sanitized_user_login, $user_email );

		if ( $reg_errors->get_error_code() ) {
			$reliableTradie->add_error( $reg_errors->get_error_message() );
			return;
		}

		if ( $reliableTradie->error_count() == 0 ) {

            $new_customer_data = array(
            	'user_login' => $sanitized_user_login,
            	'user_pass'  => $password,
            	'user_email' => $user_email,
            	'role'       => 'customer'
            );
 
            $user_id = wp_insert_user( apply_filters( 'reliabletradie_new_customer_data', $new_customer_data ) );

            if ( is_wp_error($user_id) ) {
            	$reliableTradie->add_error( '<strong>' . __( 'ERROR', 'reliabletradie' ) . '</strong>: ' . __( 'Couldn&#8217;t register you&hellip; please contact us if you continue to have problems.', 'reliabletradie' ) );
                return;
            }

            // Get user
            $current_user = get_user_by( 'id', $user_id );
            
            // Action
            do_action( 'reliabletradie_created_tradie', $user_id );
            do_action('reliabletradie_beforecreating_tradie', $user_id);
            do_action('reliabletradie_created_usertype', $user_id);
			// send the user a confirmation and their login details
			//$mailer = $reliableTradie->mailer();
            sendEmail($_POST['email_2'], "Welcome to Reliable Tradie", "<p>Hello, welcome to Reliable Tradie.</p><p>Your username is $username, and your password is $password</p>");
			//$mailer->customer_new_account( $user_id, $password );

            // set the WP login cookie
            $secure_cookie = is_ssl() ? true : false;
            wp_set_auth_cookie($user_id, true, $secure_cookie);

            // Redirect
			if ( wp_get_referer() ) {
				$redirect = esc_url( wp_get_referer() );
			} else {
				$redirect = esc_url( get_permalink( $reliableTradie->get_page_id( 'myaccount' ) ) );
			}

			wp_redirect( apply_filters( 'reliabletradie_registration_redirect', $redirect ) );
			exit;
		}

	}
}
function sendEmail($to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "", $content_type = 'text/html')
{
      wp_mail( $to, $subject, $message, $headers, $attachments );
      
}



?>