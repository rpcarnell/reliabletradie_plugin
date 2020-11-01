<?php
// Register the script first.
wp_register_script( 'rt_handle', 'path/to/myscript.js' );
// Now we can localize the script with our data.
$translation_array = array( 'rt_url' => get_site_url(), 'rt_urlajax' =>  (get_site_url() . '/wp-admin/admin-ajax.php') , 'a_value' => '10' );
wp_localize_script( 'rt_handle', 'rt_vars', $translation_array );
// The script can be enqueued now or later.
wp_enqueue_script( 'rt_handle' );
//let's add a few actions:
add_action('wp_ajax_rt_deletingiImage', 'rt_deletingiImage');
add_action('wp_ajax_rt_getTrade', 'rt_getTrade');
add_action( 'wp_ajax_nopriv_rt_getTrade', 'rt_getTrade');//not-logged-in
add_action('wp_ajax_rt_getSuburb', 'rt_getSuburb');
add_action( 'wp_ajax_nopriv_rt_getSuburb', 'rt_getSuburb');//not-logged-in
add_action('wp_ajax_rt_getSubSec', 'rt_getSubSec');
add_action('wp_ajax_nopriv_rt_getSubSec', 'rt_getSubSec');//not-logged-in
add_action('wp_ajax_rt_delTrade', 'rt_delTrade');
add_action('wp_ajax_rt_delUserLoc', 'rt_delUserLoc');
add_action('wp_ajax_rt_fndtrad_page', 'rt_fndtrad_page');
add_action('wp_ajax_nopriv_rt_fndtrad_page', 'rt_fndtrad_page');//not-logged-in
//let's add the Ajax functions:
function rt_fndtrad_page()
{
     //print_r($_POST);
    include_once('classes/class-rt-findtradie.php');
    $rt = new RT_Shortcode_Find_Tradie();
    $rt->findTradie();
}
function rt_delUserLoc()
{
     $user_id = get_current_user_id();
     if(!is_numeric($user_id)) return;
     global $wpdb;
     $query = $wpdb->prepare("DELETE FROM ".$wpdb->prefix."reliabletradie_provilocations WHERE location_id = %d AND provider_id = %d LIMIT 1", $_POST['location'], $user_id);
     $wpdb->query($query);
     die();
}
function rt_deletingiImage()
{
    global $wpdb, $reliableTradie;
    $userid = $_POST['userid'];
    $imageid = $_POST['imageid'];  
    $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."reliabletradie_providerimages WHERE id = %s AND provider_id = %d LIMIT 1", $imageid, $userid ) );
    $path = ABSPATH.$row->image_url; 
    @unlink($path);
    $wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->prefix."reliabletradie_providerimages WHERE id = %s AND provider_id = %d LIMIT 1", $imageid, $userid ) );
}
function rt_delTrade()
{
    global $wpdb;
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}reliabletradie_providers WHERE user_id= %d LIMIT 1", $_POST['userid']);
    $row = $wpdb->get_row($query);
    $filteroptions = explode('-',$row->filteroptions);
    if (!is_array($filteroptions)) {$filteroptions = array();  }
    $fw = array();
    foreach($filteroptions as $fff) { if (is_numeric($fff)) $fw[] = $fff; }
    if (in_array($_POST['trade_id'], $fw)) { $filteroptions = array_diff($fw, array($_POST['trade_id'])); }
    else {};
    $filteroptions = implode('-',$filteroptions);
    $userrq =  "-$filteroptions-";
    $query = $wpdb->prepare("UPDATE {$wpdb->prefix}reliabletradie_providers set filteroptions = %s WHERE user_id= %d LIMIT 1", $userrq, $_POST['userid']);
    $wpdb->query($query);
    die();
}
function rt_getTrade($getTrade)
{
    global $wpdb;  
    $q = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."reliabletradie_trades WHERE trade like %s LIMIT 20", '%'.like_escape($_POST['trade']).'%');
    $rows = $wpdb->get_results($q);
    echo json_encode($rows);
    die();
}
function rt_getSubSec()
{
    global $wpdb;
    $q = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."reliabletradie_locations WHERE state = %s group by concat(suburb,'_',state) ORDER BY suburb", $_POST['state']);
    
    $rows = $wpdb->get_results($q);
    echo json_encode($rows);
    die();
}
function rt_getSuburb()
{
    global $wpdb;
    $q = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."reliabletradie_locations WHERE suburb like %s OR postcode like %s group by concat(suburb,'_',state) LIMIT 20", like_escape($_POST['suburb']).'%', like_escape($_POST['suburb']).'%');
    
    $rows = $wpdb->get_results($q);
    if (! $rows)
    {
        $rows = array();
        $rows[0] = new stdClass();
        $rows[0]->suburb = 'Unable to find that location';
        $rows[0]->postcode = '';
        //die();
    }
    echo json_encode($rows);
    die();
}

?>