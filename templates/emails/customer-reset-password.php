<?php
/**
 * Customer Reset Password email
 *
 * @author 		WooThemes
 * @package 	reliabletradie/Templates/Emails
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once($tempDir."/emails/email-header.php");
 ?>
<p><?php _e( 'Someone requested that the password be reset for the following account:', 'reliabletradie' ); ?></p>
<p><?php printf( __( 'Username: %s', 'reliabletradie' ), $user_login ); ?></p>
<p><?php _e( 'If this was a mistake, just ignore this email and nothing will happen.', 'reliabletradie' ); ?></p>
<p><?php _e( 'To reset your password, visit the following address:', 'reliabletradie' ); ?></p>
<p>
    <a href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'login' => rawurlencode( $user_login ) ), get_permalink( reliabletradie_get_page_id( 'lost_password' ) ) ) ); ?>">
			<?php _e( 'Click here to reset your password', 'reliabletradie' ); ?></a>
</p>
<p></p>

<?php include_once($tempDir."/emails/email-footer.php"); ?>