<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	reliabletradie/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $reliabletradie, $reliableTradie; 
$my_account_id = $reliableTradie->get_page_id( 'myaccount');

$reliableTradie->show_messages();  
 
 if (!isset($_GET['register']) && ! is_user_logged_in()): 
     include_once('userlogin.php');//include_once('userdecide.php');
 else:
 
?>
<div class="col2-set" id="customer_login">
<?php 
   if (!isset($_POST['register']) && isset($_GET['register']) && ! is_user_logged_in()):
       include_once('userdecide.php');
   elseif (isset($_POST['register']) && ! is_user_logged_in()):  
	include_once('userregister.php');
  elseif (!is_user_logged_in()): 
         include_once('userlogin.php');
      else: ?>
    <div class="col-1">
        <h1>You are already logged in</h1><?php echo wp_loginout( );  ?>
         
    </div>
    
    <?php endif; ?>
</div>
 

<?php endif; do_action('reliabletradie_after_customer_login_form'); ?>